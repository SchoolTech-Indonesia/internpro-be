<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    private function createPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create-users',
            'edit-users',
            'delete-users',
            'view-reports',
        ];

        $this->createPermissions($permissions);

        $rolesPermissions = [
            'view-roles',
            'create-roles',
            'update-roles',
            'delete-roles'
        ];

        $this->createPermissions($rolesPermissions);

        $permissionsPermissions = [
            'view-permissions',
            'create-permissions',
            'update-permissions',
            'delete-permissions'
        ];

        $this->createPermissions($permissionsPermissions);

        $role = Role::where('name', 'Super Administrator')->first();
        $role->givePermissionTo($permissions, $rolesPermissions, $permissionsPermissions);
//        $role->revokePermissionTo($rolesPermissions);
    }
}
