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

class PaymentExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
        $search = request()->search;

        $cards = Payment::where('type', 'card')
        ->where(function($qq) use ($search) {
            // Check if search term is provided
            if (!empty($search)) {
                $qq->where('tx_id', 'like', "%{$search}%")
                ->orWhereHas('user', function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }
        })
        ->orderBy('id', 'desc')
        ->with('user');

        if($request->status){
            $cards = $cards->where('status',$request->status);
        }
      return $cards = $cards->get();

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
            'Tax Id',
            'Status',
            'Created At',
        ];
    }
}
