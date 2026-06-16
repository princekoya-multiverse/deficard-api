<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsersExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            $data->email,
            $data->phone,
            $data->kyc_status,
            $data->created_at,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $search = request()->get('search');
        $status = request()->get('status');
        $users = User::where('users.is_admin', false)
                    ->select('users.*', 'kyc_verifications.status as kyc_status')
                    ->leftJoin('kyc_verifications', 'users.id', '=', 'kyc_verifications.user_id')
                    ->orderBy('users.id', 'DESC');
        if ($search) {
            $users->where(function($query) use ($search) {
                $query->where('users.first_name', 'like', "%{$search}%")
                      ->orWhere('users.last_name', 'like', "%{$search}%")
                      //->orWhere('name', 'like', "%{$search}%")
                      //->orWhere('phone', 'like', "%{$search}%");
                      //->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
            });
        }
        if ($status) {
            if($status == 'None') {
                $users->whereNull('kyc_verifications.status');
            } else {
                $users->where('kyc_verifications.status', $status);
            }
        }

        return $users->get();
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
            'KYC Status',
            'Register At',
        ];
    }
}
