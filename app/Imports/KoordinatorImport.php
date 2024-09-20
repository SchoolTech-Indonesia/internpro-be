<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KoordinatorImport implements ToModel, WithHeadingRow, WithStartRow
{
    public function startRow(): int
    {
        return 3; // column header in row 3 (conditional, waiting for excel template form)
    }
    
    public function model(array $row)
    {
        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => bcrypt($row['password']),
            'nip'      => $row['nip'],
            'nisn'     => $row['nisn'],
            'role_id'  => 2, // 2 as Koordinator role_id
        ]);
    }
}
