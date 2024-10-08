<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Major;
use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $school = School::where('school_name', 'School Tech Indonesia')->first();
        $major = Major::where('major_name', 'Rekaya Perangkat Lunak')->first();
        $class = Kelas::where('class_name', 'Rekaya Perangkat Lunak 2024 - RPL 004')->first();

        $admin = User::firstOrCreate([
            'nip_nisn' => "321",
            'name' => 'Administrator',
            'email' => 'admin@dev-internpro.schooltech.biz.id',
            'phone_number' => "085711987659",
            'password' => bcrypt('password'),
            'school_id' => $school->uuid,
            'major_id' => $major->uuid,
            'class_id' => $class->uuid,
        ]);

        $admin->assignRole(['Super Administrator']);

        $adminRole = Role::where("name", "Super Administrator")->first();

        $adminRole->givePermissionTo(["create-users", "edit-users", "delete-users", "view-reports", "manage-roles"]);
    }
}
