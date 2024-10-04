<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::where('school_name', 'SMK Negeri 1 Twitter')->first();

        Partner::firstOrCreate([
            "name" => "PT Dufan Mega Sejahtera",
            "address" => "Jalan Ancol Barat, Jakarta Utara",
            "logo" => fake()->filePath(),
            "number_sk" => "3001",
            "file_sk" => fake()->filePath(),
            "end_date_sk" => "2024-09-09 10:00:00",
            "school_id" => $school->uuid
        ]);
    }
}
