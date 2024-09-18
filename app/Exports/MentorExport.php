<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MentorExport implements FromCollection,  WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::role('Mentor')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'NIP/NISN',
            'Phone Number',
            'School Name',
            'Partner Name',
            'Role',
        ];
    }

    public function map($mentor): array
    {
        return [
            $mentor->uuid,
            $mentor->name,
            $mentor->email,
            $mentor->nip_nisn,
            $mentor->phone_number,
            $mentor->school->school_name,
            $mentor->partners->pluck('name')->implode(', '),
            $mentor->getRoleNames()->implode(', '), 
        ];
    }
}