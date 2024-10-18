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
        $major = Major::where('major_name', 'Rekaya Perangkat Lunak')->first();
        Kelas::firstOrCreate([
            "class_code" => "TIF2024",
            "class_name" => "Rekaya Perangkat Lunak 2024 - RPL 004",
            "major" => $major->uuid
        ]);

        $major = Major::where('major_name', 'Akuntansi')->first();
        Kelas::firstOrCreate([
            "class_code" => "AK2024",
            "class_name" => "Akuntansi 2024 - AK 004",
            "major" => $major->uuid
        ]);

        $major = Major::where('major_name', 'Otomaasi Tata Kelola Perkantoran')->first();
        Kelas::firstOrCreate([
            "class_code" => "OTKP2024",
            "class_name" => "Otomaasi Tata Kelola Perkantoran 2024 - OTKP 004",
            "major" => $major->uuid
        ]);

        $major = Major::where('major_name', 'Teknik Komputer dan Jaringan')->first();
        Kelas::firstOrCreate([
            "class_code" => "TKJ2024",
            "class_name" => "Teknik Komputer dan Jaringan 2024 - TKJ 004",
            "major" => $major->uuid
        ]);

        $major = Major::where('major_name', 'Multimedia')->first();
        Kelas::firstOrCreate([
            "class_code" => "MM2024",
            "class_name" => "Multimedia 2024 - MM 004",
            "major" => $major->uuid
        ]);

        $major = Major::where('major_name', 'Bisnis Daring dan Pemasaran')->first();
        Kelas::firstOrCreate([
            "class_code" => "BDP2024",
            "class_name" => "Bisnis Daring dan Pemasaran 2024 - BDP 004",
            "major" => $major->uuid
        ]);
    }
}
