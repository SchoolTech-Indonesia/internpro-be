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
        $school = School::where('uuid', SchoolSeeder::$schoolUuid)->first();

        Partner::firstOrCreate([
            "name" => "PT mencari cinta sejati",
            "address" => "Jalan kebun raya bogor",
            "logo" => "00xx000x00x",
            "number_sk" => "3001",
            "end_date_sk" => "2024-09-09 10:00:00",
            "school_id" => $school->uuid
        ]);
    }
}
