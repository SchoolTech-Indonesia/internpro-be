<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SchoolSeeder;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SchoolSeeder::class,
            MajorSeeder::class,
            ClassSeeder::class,
            PartnerSeeder::class,
            UserSeeder::class,
            AssignPermissionsSeeder::class,
            InternshipSeeder::class
        ]);
    }
}
