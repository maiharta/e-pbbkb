<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'PT Mencari Cinta Sejati',
                'email' => 'operator@example.com',
                'password' => bcrypt('password')
            ]
        ];

        $user_details = [
            [
                'kabupaten_id' => 2,
                'npwpd' => 123123123,
                'nomor_telepon' => '081234567890',
                'alamat' => 'Dsn. Krajan RT 04/02 Yosomulyo, Gambiran',
                'filepath_berkas_persyaratan' => 'berkas/mQLqnPLqqcYqowX04JPM1mXDdD5S57tWGky6jNIR.xlsx',
                'is_user_readonly' => 1,
                'catatan_revisi' => null,
                'is_verified' =>         1,
                'verified_at' =>     '2024-06-13 09:12:00'
            ]
        ];

        foreach ($users as $key => $value) {
            if (!User::where('email', $value['email'])->exists()) {
                $user = User::create($value);
                $user->userDetail()->create($user_details[$key]);
                $user->assignRole('operator');
            }
        }
    }
}
