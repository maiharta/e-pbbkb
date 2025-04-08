<?php

namespace Database\Seeders;

use App\Models\JenisBbm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisBbmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis_bbms = [
            [
                'kode' => '001',
                'nama' => 'Pertamax',
                'is_subsidi' => false,
                'persentase_tarif' => 10.00
            ],
            [
                'kode' => '002',
                'nama' => 'Pertalite',
                'is_subsidi' => true,
                'persentase_tarif' => 10.00
            ],
            [
                'kode' => '003',
                'nama' => 'Solar',
                'is_subsidi' => true,
                'persentase_tarif' => 10.00
            ]
        ];

        foreach ($jenis_bbms as $jenis_bbm) {
            JenisBbm::firstOrCreate(['kode' => $jenis_bbm['kode']], $jenis_bbm);
        }
    }
}
