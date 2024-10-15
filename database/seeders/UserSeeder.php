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
        $major = Major::where('major_name', 'Rekaya Perangkat Lunak')->first();
        $class = Kelas::where('class_name', 'Rekaya Perangkat Lunak 2024 - RPL 004')->first();
        $partner = Partner::where('name', 'PT Dufan Mega Sejahtera')->first();

        // $partner = Partner::firstOrCreate([
        //     "uuid" => "7dcda20c-dc76-4dd6-b427-87dc86d6e0c7",
        //     "name" => "PT Partner Alam Sejati",
        //     "address" => "Jalan kebun raya bogor",
        //     "logo" => "00xx000x00x",
        //     "school" => $school->uuid,
        //     "number_sk" => "35124",
        //     "file_sk" => fake()->filePath(),
        //     "end_date_sk" => "2024-09-09 10:00:00",
        // ]);

        // $major = Major::firstOrCreate([
        //     "major_code" => "0987654321",
        //     "major_name" => "Rekaya Perangkat Lunak"
        // ]);

        // $class = Kelas::firstOrCreate([
        //     "class_code" => "TIF2024",
        //     "class_name" => "Rekaya Perangkat Lunak 2024 - RPL 004",
        //     "major" => $major->uuid
        // ]);

        $admin = User::firstOrCreate([
            'nip_nisn' => "987",
            'name' => 'Super Administrator',
            'email' => 'user@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711987654",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
            // 'partner_id' => $partner->uuid
        ]);

        $guru = User::firstOrCreate([
            'nip_nisn' => "789",
            'name' => 'John Doe',
            'email' => 'guru@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711986543",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            // 'major_id' => $major->uuid,
            // 'class_id' => $class->uuid,
            //            'partner_id' => $partner->uuid
        ]);

        $admin->assignRole(['Super Administrator']);
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
            'password' => bcrypt('koordinator'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
        ]);
        $koordinator->assignRole(['Koordinator']);

    }

}