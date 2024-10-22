<?php

namespace Database\Seeders;

use App\Models\Internship;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::where('school_name', 'SMK Negeri 1 SchoolTech')->first();
        Internship::create([
            'name' => 'Data Science Internship',
            'description' => 'Internship for data science and machine learning students.',
            'start_date' => '2024-03-01',
            'end_date' => '2024-07-01',
            'school_id' => $school->uuid,
        ]);
    }
}
