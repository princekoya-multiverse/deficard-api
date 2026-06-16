<?php

namespace App\Exports;

use App\Models\KycVerification;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KycVerificationExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    /**
     * @param Data $data
     */
    public function map($data): array
    {
        return [
            $data->first_name,
            $data->last_name,
            $data->birthday,
            $data->email,
            $data->phone,
            $data->city,
            $data->street_address,
            $data->street_address_2,
            $data->region_state_province,
            $data->zipcode,
            $data->country,
            $data->status,
            $data->created_at,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $search = request()->get('search');

        $kyc = KycVerification::orderBy('id', 'DESC');
        if ($search) {
           $kyc= $kyc->where(function($query) use ($search) {
                $query->
                      orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('birthday', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('city', 'LIKE', "%{$search}%")
                      ->orWhere('street_address', 'LIKE', "%{$search}%")
                      ->orWhere('street_address_2', 'LIKE', "%{$search}%")
                      ->orWhere('region_state_province', 'LIKE', "%{$search}%")
                      ->orWhere('zipcode', 'LIKE', "%{$search}%")
                      ->orWhere('country', 'LIKE', "%{$search}%")
                     // ->orWhere('file1', 'LIKE', "%{$search}%")
                     // ->orWhere('file2', 'LIKE', "%{$search}%")
                      ->orWhere('status', 'LIKE', "%{$search}%");
                    //  ->orWhere('user_id', 'LIKE', "%{$search}%")
                   //   ->orWhere('created_at', 'LIKE', "%{$search}%")
                   //   ->orWhere('updated_at', 'LIKE', "%{$search}%");
            });
        }

        if(request()->status){
            $kyc = $kyc->where('status',request()->status);
            }
        return $kyc->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Birthday',
            'Email',
            'Phone',
            'City',
            'Street Address',
            'Street Address2',
            'Region State Province',
            'Postcode / Zipcode:',
            'Country',
            'Status',
            'Created At',
        ];
    }
}
