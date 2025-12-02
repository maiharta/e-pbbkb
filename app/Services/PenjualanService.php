<?php

namespace App\Services;

use App\Models\Penjualan;

class PenjualanService
{
    public static function generatePbbkbSistem(Penjualan $penjualan): float
    {
        if (!$penjualan->is_wajib_pajak) {
            return 0;
        }

        return (($penjualan->persentase_pengenaan_sektor / 100) * ($penjualan->persentase_tarif_jenis_bbm / 100)) * $penjualan->dpp * $penjualan->volume;
    }
}
