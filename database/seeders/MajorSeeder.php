<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $major = Major::firstOrCreate([
            "major_code" => "0987654321",
            "major_name" => "Rekaya Perangkat Lunak"
        ]);

    }
}
