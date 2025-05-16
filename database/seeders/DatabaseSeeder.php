<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\UserApi;
use App\Models\Kabupaten;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Database\Seeders\SektorSeeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\JenisBbmSeeder;
use Database\Seeders\OperatorSeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PengaturanSistemSeeder;

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
        ], ['password' => bcrypt('password')])->assignRole('administrator');

        $kabupaten = [
            'Kabupaten Badung', 'Kabupaten Bangli', 'Kabupaten Buleleng', 'Kabupaten Gianyar', 'Kabupaten Jembrana', 'Kabupaten Karangasem', 'Kabupaten Klungkung', 'Kabupaten Tabanan',
            'Kota Denpasar'
        ];

        foreach ($kabupaten as $nama) {
            Kabupaten::firstOrCreate([
                'nama' => $nama,
            ]);
        }

        $this->call([
            PengaturanSistemSeeder::class,
        ]);

        if (config('app.env') != 'production') {
            $this->call([
                SektorSeeder::class,
                JenisBbmSeeder::class,
                OperatorSeeder::class
            ]);

            UserApi::create([
                'name' => 'API User',
                'email' => 'api@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Set a secure password
                'api_key' => Str::random(64),
                'status' => true,
            ]);
        }
    }
}
