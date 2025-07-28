<?php

namespace App\Services;

use App\Models\Bunga;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
use App\Models\PengaturanSistem;
use App\Services\InvoiceService;
use App\Exceptions\ServiceException;

class BungaService
{
    public static function generateBunga(Pelaporan $pelaporan)
    {
        if ($pelaporan->is_paid) {
            throw new ServiceException('Pelaporan sudah dibayar');
        }

        // Check if the pelaporan is expired
        if ($pelaporan->is_expired) {
            throw new ServiceException('Pelaporan sudah kadaluarsa');
        }

        // if($pelaporan->is_verified) {
        //     throw new ServiceException('Pelaporan sudah diverifikasi, tidak bisa dikenakan bunga');
        // }

        $now = now();
        $batas_pembayaran = $pelaporan->batas_pembayaran;

        if($now->isAfter($batas_pembayaran)) {
            // Get existing bunga records for this pelaporan
            $existingBungas = Bunga::where('pelaporan_id', $pelaporan->id)
                ->orderBy('bunga_ke', 'desc')
                ->get();

            $persentaseBunga = self::getSystemSetting('bunga') / 100;

            // If no bunga exists yet, create the first one
            if($existingBungas->isEmpty()) {
                // cancel the invoice
                InvoiceService::cancelAllInvoices($pelaporan);
                // Create the first bunga record
                Bunga::create([
                    'pelaporan_id' => $pelaporan->id,
                    'waktu_bunga' => $batas_pembayaran->copy()->addDay(),
                    'bunga_ke' => 1,
                    'persentase_bunga' => $persentaseBunga * 100,
                    'bunga' => $persentaseBunga,
                    'keterangan' => 'Bunga telat pembayaran ke-1'
                ]);

                // Re-fetch after creating the first one
                $existingBungas = Bunga::where('pelaporan_id', $pelaporan->id)
                    ->orderBy('bunga_ke', 'desc')
                    ->get();
            }

            // Get the last bunga record
            $lastBunga = $existingBungas->first();
            $lastBungaDate = $lastBunga ? Carbon::parse($lastBunga->waktu_bunga) : $batas_pembayaran;
            $lastBungaKe = $lastBunga ? $lastBunga->bunga_ke : 0;

            // Loop until we reach the current month
            $currentMonthYear = now()->format('Y-m');
            $processedMonthYear = $lastBungaDate->format('Y-m');

            while ($processedMonthYear < $currentMonthYear) {
                // cancel the invoice
                InvoiceService::cancelAllInvoices($pelaporan);

                // Move to the next month
                $nextMonthDate = $lastBungaDate->copy()->addMonth()->startOfMonth();

                // Calculate the bunga date using CutiService to get 10 working days from the start of month
                $nextBungaDate = CutiService::getDateAfterCuti($nextMonthDate, 10);

                // If the calculated bunga date is in the future, break the loop
                if ($nextBungaDate->isAfter($now)) {
                    break;
                }

                $bungaKe = $lastBungaKe + 1;

                // Create new bunga record
                Bunga::create([
                    'pelaporan_id' => $pelaporan->id,
                    'waktu_bunga' => $nextBungaDate,
                    'bunga_ke' => $bungaKe,
                    'persentase_bunga' => $persentaseBunga * 100,
                    'bunga' => $persentaseBunga,
                    'keterangan' => 'Bunga telat pembayaran ke-' . $bungaKe
                ]);

                // Update for next iteration
                $lastBungaDate = $nextBungaDate;
                $lastBungaKe = $bungaKe;
                $processedMonthYear = $lastBungaDate->format('Y-m');
            }

            return true;
        } else {
            throw new ServiceException('Tidak ada bunga yang dikenakan karena belum melewati batas pembayaran');
        }
    }

    protected static function getSystemSetting(string $key): int
    {
        return (int) PengaturanSistem::where('key', $key)->first()->value;
    }
}
