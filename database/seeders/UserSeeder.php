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
        $role = Role::firstOrCreate([
            'name' => 'Super Administrator'
        ]);

        $school = School::firstOrCreate([
            "uuid" => "15808e5b-cec3-4df5-a0c5-f1324bce7357",
            "school_name" => "smk tadika mesra",
            "school_address" => "kampung durian runtuh",
            "phone_number" => "082198765",
            "start_member" => "2024-09-01 10:00:00",
            "end_member" => "2024-09-05 10:00:00"
        ]);

        $major = Major::firstOrCreate([
            "major_code" => "0987654321",
            "major_name" => "Rekaya Perangkat Lunak"
        ]);

        $class = Kelas::firstOrCreate([
            "class_code" => "TIF2024",
            "class_name" => "Rekaya Perangkat Lunak 2024 - RPL 004",
            "major" => $major->uuid
        ]);

        $partner = Partner::firstOrCreate([
            "uuid" => "7dcda20c-dc76-4dd6-b427-87dc86d6e0c7",
            "name" => "PT mencari cinta sejati",
            "address" => "Jalan kebun raya bogor",
            "logo" => "00xx000x00x",
            "number_sk" => "3001",
            "file_sk" => fake()->filePath(),
            "end_date_sk" => "2024-09-09 10:00:00",
        ]);

        $major = Major::firstOrCreate([
            "major_code" => "0987654321",
            "major_name" => "Rekaya Perangkat Lunak"
        ]);

        $class = Kelas::firstOrCreate([
            "class_code" => "TIF2024",
            "class_name" => "Rekaya Perangkat Lunak 2024 - RPL 004",
            "major" => $major->uuid
        ]);

        $user = User::firstOrCreate([
            'nip_nisn' => "987",
            'name' => 'Super Administrator',
            'email' => 'user@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711987654",
            'password' => bcrypt('password'),
//             'role_id' => 1
            'school_id' => "15808e5b-cec3-4df5-a0c5-f1324bce7357",
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
//            'partner_id' => $partner->uuid
        ]);
        $user->assignRole(['Super Administrator']);

        $role = Role::firstOrCreate([
            'name' => 'Mentor'
        ]);

        $user = User::firstOrCreate([
            'nip_nisn' => "111",
            'name' => 'Test Mentor',
            'email' => 'mentor@dev-internpro.schooltech.biz.id',
            'phone_number' => "085511112222",
            'password' => bcrypt('mentor'),
            'school_id' => '15808e5b-cec3-4df5-a0c5-f1324bce7357',
        ]);
        $user->assignRole(['Mentor']);
    }
}