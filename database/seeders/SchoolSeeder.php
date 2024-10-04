<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    public static $schoolUuid;
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('school')->insert([
            'uuid' => Str::uuid()->toString(),
            'school_name' => 'SMK Negeri 1 Twitter',
            'school_address' => 'Jalan Danau Tamblingan, Sanur, Denpasar Selatan, Denpasar, Bali',
            'phone_number' => '081222333444',
            'start_member' => now(),
            'end_member' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('school')->insert([
            'uuid' => Str::uuid()->toString(),
            'school_name' => 'SMK Negeri 1 Waze',
            'school_address' => 'Jalan Merdeka Barat, Waze, Denpasar Selatan, Denpasar, Bali',
            'phone_number' => '081222444333',
            'start_member' => '2021-01-01',
            'end_member' => '2022-01-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
