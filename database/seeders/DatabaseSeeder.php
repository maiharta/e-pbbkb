<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Kabupaten;
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
        }
    }
}
