<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentImport implements ToModel, WithHeadingRow, WithStartRow
{
    public function startRow(): int
    {
        return 3; // column header in row 3 (conditional, waiting for excel template form)
    }
    
    public function model(array $row)
    {
        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'],
            'password' => bcrypt($row['password']),
            'nip_nisn' => $row['nip_nisn'],
            'role' => 'Student',
            'school_id' => $row['school_id'],
            'major_id' => $row['major_id'],
            'class_id' => $row['class_id'],
            'partner_id' => $row['partner_id'],
        ]);
    }
}
