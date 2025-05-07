<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanSistem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PengaturanSistemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengaturans = [
            [
                'key' => 'batas_pelaporan',
                'value' => '15',
                'deskripsi' => 'Batas hari untuk pelaporan wapu',
            ],
            [
                'key' => 'batas_pembayaran',
                'value' => '10',
                'deskripsi' => 'Batas hari untuk pembayaran wapu',
            ],
            [
                'key' => 'bunga',
                'value' => '1',
                'deskripsi' => 'Besar bunga per tahun',
            ],
            [
                'key' => 'denda',
                'value' => '1000000',
                'deskripsi' => 'Besar denda per hari',
            ],
        ];

        foreach ($pengaturans as $pengaturan) {
            PengaturanSistem::firstOrCreate(
                [
                    'key' => $pengaturan['key']
                ],
                [
                    'value' => $pengaturan['value'],
                    'deskripsi' => $pengaturan['deskripsi']
                ]
            );
        }
    }
}
