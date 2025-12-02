<?php

namespace App\Services;

use App\Models\Denda;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
use App\Models\PengaturanSistem;
use App\Exceptions\ServiceException;

class DendaService
{
    public static function generateDenda(Pelaporan $pelaporan)
    {
        if ($pelaporan->is_paid) {
            throw new ServiceException('Pelaporan sudah dibayar');
        }

        // Check if the pelaporan is expired
        if ($pelaporan->is_expired) {
            throw new ServiceException('Pelaporan sudah kadaluarsa');
        }


        $batas_pelaporan = $pelaporan->batas_pelaporan;
        $now = now();
        if ($now->isAfter($batas_pelaporan)) {
            // Get existing denda records for this pelaporan
            $existingDendas = Denda::where('pelaporan_id', $pelaporan->id)
                ->first();

            if(!$existingDendas) {
                // cancel the invoice
                InvoiceService::cancelAllInvoices($pelaporan);
                Denda::create([
                    'pelaporan_id' => $pelaporan->id,
                    'waktu_denda' => $batas_pelaporan->copy()->addDay(),
                    'persentase_denda' => 0,
                    'denda' => 1000000,
                    'keterangan' => 'Denda keterlambatan pelaporan'
                ]);
            } else {
                throw new ServiceException('Denda sudah dikenakan untuk pelaporan ini');
            }
        }
    }

    /**
     * Get system settings value
     *
     * @param string $key
     * @return int
     */
    protected static function getSystemSetting(string $key): int
    {
        return (int) PengaturanSistem::where('key', $key)->first()->value;
    }
}
