<?php

namespace App\Exports;

use App\Models\KycVerification;
use App\Models\Payment;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CardLoadExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            $data->tx_id,
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

        $card_loads = Payment::where('type', 'load')->orderBy('id', 'desc')->with('user')->whereHas('user',function($q) use($request){
            $search = $request->search;

            if ($request->search) {
                $q->where('tx_id', 'like', "%{$search}%");
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
            $card_loads = $card_loads->where('status',$request->status);
        }

        return    $card_loads =$card_loads->get();
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
            'Tax Id',
            'Status',
            'Created At',
        ];
    }
}
