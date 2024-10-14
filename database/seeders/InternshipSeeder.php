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
//        DB::table('internships')->insert([
//            [
//                'uuid' => Str::uuid(),
//                'code' => Str::uuid(),
//                'name' => 'Data Science Internship',
//                'description' => 'Internship for data science and machine learning students.',
//                'start_date' => '2024-03-01',
//                'end_date' => '2024-07-01',
//                'school_id' => '1cd02e53-1233-47a5-94b4-768a8c5e2c4f', // Sesuaikan dengan data yang ada
//                'created_by' => '9d15e098-258d-4c8a-822c-331211f7b183', // Sesuaikan dengan user UUID yang valid
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//        ]);
        $school = School::where('school_name', 'SMK Negeri 1 SchoolTech')->first();
        Internship::create([
            'name' => 'Data Science Internship',
            'description' => 'Internship for data science and machine learning students.',
            'start_date' => '2024-03-01',
            'end_date' => '2024-07-01',
            'school_id' => $school->uuid,
        ]);
        DB::table('internships')->insert([
            [
                'uuid' => Str::uuid(),
                'code' => Str::uuid(),
                'name' => 'Data Science Internship',
                'description' => 'Internship for data science and machine learning students.',
                'start_date' => '2024-03-01',
                'end_date' => '2024-07-01',
                'school_id' => '1cd02e53-1233-47a5-94b4-768a8c5e2c4f', // Sesuaikan dengan data yang ada
                'created_by' => '9d15e098-258d-4c8a-822c-331211f7b183', // Sesuaikan dengan user UUID yang valid
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
