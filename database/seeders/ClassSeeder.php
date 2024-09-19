<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $major = Major::where('major_code', '0987654321')->first();
        Kelas::firstOrCreate([
            "class_code" => "TIF2024",
            "class_name" => "Rekaya Perangkat Lunak 2024 - RPL 004",
            "major" => $major->uuid
        ]);
    }
}
