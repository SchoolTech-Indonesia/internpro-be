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
            'name' => 'Super Administrator',
            'description' => 'Super Administrator'
        ]);

        $school = School::create([
            "uuid" => "15808e5b-cec3-4df5-a0c5-f1324bce7357",
            "school_name" => "smk tadika mesra",
            "school_address" => "kampung durian runtuh",
            "phone_number" => "082198765",
            "start_member" => "2024-09-01 10:00:00",
            "end_member" => "2024-09-05 10:00:00"
        ]);

        $major = Major::create([
            "major_code" => "0987654321",
            "major_name" => "Rekaya Perangkat Lunak"
        ]);

        $class = Kelas::create([
            "class_code" => "TIF2024",
            "class_name" => "Rekaya Perangkat Lunak 2024 - RPL 004",
            "major" => $major->uuid
        ]);

        $partner = Partner::create([
            "partner_name" => "PT mencari cinta sejati",
            "partner_address" => "Jalan kebun raya bogor",
            "partner_logo" => "00xx000x00x",
            "number_sk" => "3001",
            "end_date_sk" => "2024-09-09 10:00:00",
            "school" => $school->uuid
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
            "partner_name" => "PT mencari cinta sejati",
            "partner_address" => "Jalan kebun raya bogor",
            "partner_logo" => "00xx000x00x",
            "number_sk" => "3001",
            "end_date_sk" => "2024-09-09 10:00:00",
            "school" => $school->uuid
        ]);

        $user = User::firstOrCreate([
            'nip_nisn' => "987",
            'name' => 'Super Administrator',
            'email' => 'user@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711987654",
            'password' => bcrypt('password'),
            'school_id' => "15808e5b-cec3-4df5-a0c5-f1324bce7357",
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
            'partner_id' => $partner->uuid
        ]);
        $user->assignRole(['Super Administrator']);
    }
}
