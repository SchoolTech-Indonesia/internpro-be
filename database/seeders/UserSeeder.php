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
        $school = School::where('school_name', 'SMK Negeri 1 Twitter')->first();
        $school_exp = School::where('school_name', 'SMK Negeri 1 Waze')->first();
        $major = Major::where('major_name', 'Rekaya Perangkat Lunak')->first();
        $class = Kelas::where('class_name', 'Rekaya Perangkat Lunak 2024 - RPL 004')->first();
        $partner = Partner::where('name', 'PT Dufan Mega Sejahtera')->first();

        $admin = User::firstOrCreate([
            'nip_nisn' => "987",
            'name' => 'Super Administrator',
            'email' => 'user@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711987654",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
        ]);
        $admin->assignRole(['Super Administrator']);

        $admin_exp = User::firstOrCreate([
            'nip_nisn' => "565",
            'name' => 'Super Administrator Expired',
            'email' => 'expired@dev-internpro.schooltech.biz.id',
            'phone_number' => "085746587654",
            'password' => bcrypt('password'),
            'school_id' => $school_exp->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
        ]);

        $school = School::where('school_name', 'SMK Negeri 1 SchoolTech')->first();
        $admin = User::firstOrCreate([
            'nip_nisn' => "001",
            'name' => 'Johantius Armando',
            'email' => 'johan@schooltechindonesia.com',
            'phone_number' => "081444666332",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
        ]);
        $coorSTI = User::firstOrCreate([
            'nip_nisn' => "002",
            'name' => 'Johantius Coordinator',
            'email' => 'coord@schooltechindonesia.com',
            'phone_number' => "081444666132",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
        ]);
        @$coorSTI->assignRole(['Coordinator']);

        $guru = User::firstOrCreate([
            'nip_nisn' => "789",
            'name' => 'John Doe',
            'email' => 'guru@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711986543",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
        ]);

        $admin->assignRole(['Super Administrator']);
        $admin_exp->assignRole(['Super Administrator']);
        $guru->assignRole(['Teacher']);


        $mentor = User::firstOrCreate([
            'nip_nisn' => "111",
            'name' => 'Test Mentor',
            'email' => 'mentor@dev-internpro.schooltech.biz.id',
            'phone_number' => "085511112222",
            'password' => bcrypt('mentor'),
            'school_id' => $school->uuid,
        ]);
        $mentor->partners()->attach($partner->uuid);
        $mentor->assignRole(['Mentor']);

        $teacher = User::firstOrCreate([
            'nip_nisn' => "1111",
            'name' => 'Test Teacher',
            'email' => 'teacher@dev-internpro.schooltech.biz.id',
            'phone_number' => "085511122222",
            'password' => bcrypt('teacher'),
            'school_id' => $school->uuid,
        ]);
        $teacher->assignRole(['Teacher']);

        $student = User::firstOrCreate([
            'nip_nisn' => "1234",
            'name' => 'Test Student',
            'email' => 'student@dev-internpro.schooltech.biz.id',
            'phone_number' => "0855111123332",
            'password' => bcrypt('student'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
        ]);
        $student->assignRole(['Student']);

        $koordinator = User::firstOrCreate([
            'nip_nisn' => "123456",
            'name' => 'Test Koordinator',
            'email' => 'koordinator@dev-internpro.schooltech.biz.id',
            'phone_number' => "08551111232",
            'password' => bcrypt('Coordinator'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
        ]);
        $koordinator->assignRole(['Coordinator']);

    }

}