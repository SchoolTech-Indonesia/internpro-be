<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Major;
use App\Models\Partner;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::where('uuid', SchoolSeeder::$schoolUuid)->first();
        $major = Major::where('major_code', '0987654321')->first();
        $class = Kelas::where('class_code', 'TIF2024')->first();
        $partner = Partner::where('partner_name', 'PT mencari cinta sejati')->first();

        $admin = User::firstOrCreate([
            'nip_nisn' => "987",
            'name' => 'Super Administrator',
            'email' => 'user@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711987654",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
            'partner_id' => $partner->uuid
        ]);

        $guru = User::firstOrCreate([
            'nip_nisn' => "789",
            'name' => 'John Doe',
            'email' => 'guru@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711986543",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
            'partner_id' => $partner->uuid
        ]);

        $admin->assignRole(['Super Administrator']);
        $guru->assignRole(['Teacher']);
    }
}