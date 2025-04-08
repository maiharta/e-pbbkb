<?php

namespace App\Services;

use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Support\Collection;

class PelaporanService
{
    public static function generatePbbkbSistem(Pelaporan $pelaporan): Collection
    {
        return $pelaporan->penjualan->map(function ($item) {
            $item->pbbkb_sistem = PenjualanService::generatePbbkbSistem($item);
            $item->save();
            return $item;
        });
    }

    public static function generateNote(Pelaporan $pelaporan): void
    {
        $pelaporan_id = $pelaporan->id;

        if ($pelaporan->pelaporanNote->count() == 0) {
            $step = 1;
        } else {
            $step = $pelaporan->pelaporanNote->last()->step + 1;
            $pelaporan->pelaporanNote()->update(['is_active' => false]);
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
