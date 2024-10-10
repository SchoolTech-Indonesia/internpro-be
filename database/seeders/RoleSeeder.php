<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Administrator', 'guard_name' => 'api'],
            ['name' => 'Teacher', 'guard_name' => 'api'],
            ['name' => 'Administrator', 'guard_name' => 'api'],
            ['name' => 'Koordinator', 'guard_name' => 'api'],
            ['name' => 'Student', 'guard_name' => 'api'],
            ['name' => 'Mentor', 'guard_name' => 'api']
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}