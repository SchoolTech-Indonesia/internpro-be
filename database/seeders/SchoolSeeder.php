<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('school')->insert([
            'uuid' => Str::uuid()->toString(),
            'school_name' => 'School Tech Indonesia',
            'school_address' => 'Malang, Indonesia',
            'phone_number' => '08xxxxx',
            'start_member' => now(), 
            'end_member' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
