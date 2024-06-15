<?php

namespace Database\Seeders;

use App\Models\Sektor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SektorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setors = [
            [
                'nama' => 'Perusahaan',
                'kode' => '001',
                'persentase_pengenaan' => 10
            ],
            [
                'nama' => 'Industri',
                'kode' => '002',
                'persentase_pengenaan' => 10
            ],
        ];

        foreach ($setors as $sektor) {
            Sektor::firstOrCreate(['kode' => $sektor['kode']], $sektor);
        }
    }
}
