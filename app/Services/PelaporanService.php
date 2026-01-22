<?php

namespace App\Services;

use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PelaporanService
{
    public static function generatePbbkbSistem(Pelaporan $pelaporan): Collection
    {
        $updatedItems = collect();

        // Process in chunks to avoid memory issues with large datasets
        $pelaporan->penjualan()->chunk(500, function ($penjualans) use ($updatedItems) {
            $cases = [];
            $ids = [];

            foreach ($penjualans as $item) {
                $pbbkb_sistem = PenjualanService::generatePbbkbSistem($item);

                // Pembulatan ke atas jika 2 angka desimal setelah koma lebih dari 0
                if (round($pbbkb_sistem, 2) > floor($pbbkb_sistem)) {
                    $pbbkb_sistem = ceil($pbbkb_sistem);
                }

                $ids[] = $item->id;
                $cases[] = "WHEN {$item->id} THEN {$pbbkb_sistem}";

                // Update the model instance for return collection
                $item->pbbkb_sistem = $pbbkb_sistem;
                $updatedItems->push($item);
            }

            // Bulk update using CASE statement for better performance
            if (!empty($ids)) {
                $idsString = implode(',', $ids);
                $casesString = implode(' ', $cases);

                DB::table('penjualans')
                    ->whereIn('id', $ids)
                    ->update([
                        'pbbkb_sistem' => DB::raw("CASE id {$casesString} END"),
                        'updated_at' => now()
                    ]);
            }
        });

        return $updatedItems;
    }

    public static function generateNote(Pelaporan $pelaporan): void
    {
        $pelaporan_id = $pelaporan->id;

        if ($pelaporan->pelaporanNote->count() == 0) {
            $step = 1;
        } else {
            $step = $pelaporan->pelaporanNote->last()->step + 1;
            $pelaporan->pelaporanNote()->delete();
        }

        // generate note ppbkb is match
        $pelaporan->penjualan->each(function ($penjualan) use ($pelaporan_id, $step) {
            if (!self::pbbkbIsMatch($penjualan)) {
                $penjualan->pelaporanNote()->create([
                    'penjualan_id' => $penjualan->id,
                    'pelaporan_id' => $pelaporan_id,
                    'deskripsi' => 'PBBKB user berbeda dengan hasil generate sistem',
                    'status' => 'danger',
                    'step' => $step
                ]);
            }
        });

        // generate note kuitansi duplicate
        $penjualan_duplicate = self::kuitansiDuplicate($pelaporan);
        $penjualan_duplicate->each(function ($penjualan, $nomor_kuitansi) use ($pelaporan, $step) {
            $pelaporan->pelaporanNote()->create([
                'deskripsi' => $nomor_kuitansi . ' - Terdapat ' . $penjualan->count() . ' penjualan dengan nomor kuitansi yang sama ',
                'status' => 'info',
                'step' => $step
            ]);
        });
    }

    private static function kuitansiDuplicate(Pelaporan $pelaporan): Collection
    {
        $penjualans = $pelaporan->penjualan;
        $penjualan_duplicate = $penjualans->groupBy('nomor_kuitansi')->filter(function ($penjualan) {
            return $penjualan->count() > 1;
        });

        return $penjualan_duplicate;
    }

    private static function pbbkbIsMatch(Penjualan $penjualan): bool
    {
        return (float) round($penjualan->pbbkb) == (float) round($penjualan->pbbkb_sistem);
    }

    private static function penjualanMissmatchWithSisa(Pelaporan $pelaporan)
    {
        // $pembelians = $pelaporan->pembelian;
        // $penjualans = $pelaporan->penjualan;

        // // Group pembelian by tanggal
        // $pembelianByTanggal = $pembelians->groupBy('tanggal');

        // $pembelianByTanggal->each(function($pembelians,$tanggal){
        //     $pembeliansByBbm = $pembelians->groupBy('jenis_bbm_id');
        //     $pembeliansByBbm->each(function($pembelians, $jenis_bbm_id){

        //     })
        // })
    }
}
