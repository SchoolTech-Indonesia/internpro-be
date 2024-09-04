<?php

namespace Database\Seeders;

use App\Models\Role;
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
        Role::create([
            'name' => 'Super Administrator',
            'description' => 'Super Administrator'
        ]);

        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@gmail.com',
            'nip' => '1234567890',
            'nisn' => '9876543210',
            'password' => bcrypt('password'),
            'id_role' => 1
        ]);

        User::factory(10)->create();
    }
}
