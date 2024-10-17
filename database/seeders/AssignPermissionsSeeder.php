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
        $superAdmins = Role::where("name", "Super Administrator")->get();

        foreach ($superAdmins as $superAdmin) {
            $superAdmin->givePermissionTo([
                "create-users",
                "edit-users",
                "delete-users",
                "view-reports",
                "manage-roles"
            ]);
        }
    }
}
