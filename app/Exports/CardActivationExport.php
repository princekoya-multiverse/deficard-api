<?php

namespace App\Exports;

use App\Models\CardActivation;
use App\Models\KycVerification;
use App\Models\Payment;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CardActivationExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    /**
     * @param Data $data
     */
    public function map($data): array
    {
        return [
            isset($data->user->first_name)?$data->user->first_name:'',
            isset($data->user->last_name)?$data->user->last_name:'',
            isset($data->user->email)?$data->user->email:'',
            isset($data->user->phone)?$data->user->phone:'',
            $data->number,
            $data->kit_number,
            $data->status,
            $data->created_at,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $request = request();

        $card_activations = CardActivation::orderBy('id', 'desc')->with('user')->whereHas('user',function($q) use($request){
            $search = $request->search;
            if ($request->search) {
                $q->where('number', 'like', "%{$search}%");
                $q->orWhere('kit_number', 'like', "%{$search}%");
                $q->orWhere(function($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%")
                         // ->orWhere('title', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        });

        if($request->status){
            $card_activations = $card_activations->where('status',$request->status);
        }

        return   $card_activations =$card_activations->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Card Number',
            'Kit Number',
            'Status',
            'Created At',
        ];
    }
}
