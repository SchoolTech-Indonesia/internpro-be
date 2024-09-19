<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * Return a collection of users.
    *
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all(['id', 'name', 'email', 'nip', 'nisn', 'role_id']);
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
            'NIP',
            'NISN',
            'Role ID',
            // 'Created At',
            // 'Updated At',
        ];
    }
}
