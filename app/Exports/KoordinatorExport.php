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
        return User::where('role_id', 2)->get(['id', 'name', 'email', 'nip', 'nisn']); // 2 as Koordinator role_id
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
            // 'Role ID',
            // 'Created At',
            // 'Updated At',
        ];
    }
}
