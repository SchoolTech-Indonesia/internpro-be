<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KoordinatorExport implements FromCollection, WithHeadings
{
    /**
    * Return a collection of Koordinator.
    *
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::where('role', 'Coordinator')->get();
    }

    /**
    * Return headings for the excel sheet.
    *
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'NIP/NISN',
            'Phone Number',
            // 'Role',
        ];
    }

    public function map($users): array
    {
        return [
            $users->uuid,
            $users->name,
            $users->email,
            $users->nip_nisn,
            $users->phone_number,
            // $users->getRoleNames()->implode(', '), 
        ];
    }
}
