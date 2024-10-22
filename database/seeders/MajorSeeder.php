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

    }
}
