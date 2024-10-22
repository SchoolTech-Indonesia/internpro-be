<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $major = Major::firstOrCreate([
            "major_code" => "0987654321",
            "major_name" => "Rekaya Perangkat Lunak",
            "school_id" => School::where("school_name", "SMK Negeri 1 Twitter")->first()->uuid,
        ]);

        Major::firstOrCreate([
            "major_code" => "AK",
            "major_name" => "Akuntansi"
        ]);

        Major::firstOrCreate([
            "major_code" => "OTKP",
            "major_name" => "Otomaasi Tata Kelola Perkantoran"
        ]);

        Major::firstOrCreate([
            "major_code" => "TKJ",
            "major_name" => "Teknik Komputer dan Jaringan"
        ]);

        Major::firstOrCreate([
            "major_code" => "MM",
            "major_name" => "Multimedia"
        ]);

        Major::firstOrCreate([
            "major_code" => "BDP",
            "major_name" => "Bisnis Daring dan Pemasaran"
        ]);

    }
}
