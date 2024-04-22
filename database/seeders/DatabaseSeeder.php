<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // role and permission
        Role::firstOrCreate(['name' => 'administrator']);
        Role::firstOrCreate(['name' => 'operator']);
        User::firstOrCreate([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('administrator');
    }
}
