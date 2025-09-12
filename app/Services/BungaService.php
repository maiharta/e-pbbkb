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

            }else{
                // Generate recurring bunga for subsequent months
                self::generateRecurringBunga($pelaporan, $persentaseBunga);
            }

            return true;
        } else {
            throw new ServiceException('Tidak ada bunga yang dikenakan karena belum melewati batas pembayaran');
        }
    }

    /**
     * Generate recurring bunga every month with same day, considering leap years and month-end variations
     */
    protected static function generateRecurringBunga(Pelaporan $pelaporan, float $persentaseBunga)
    {
        $now = now();
        
        // Get the first bunga record to determine the original day
        $firstBunga = Bunga::where('pelaporan_id', $pelaporan->id)
            ->orderBy('bunga_ke', 'asc')
            ->first();
            
        if (!$firstBunga) {
            return;
        }

        $originalDate = Carbon::parse($firstBunga->waktu_bunga);
        $originalDay = $originalDate->day;
        
        // Get the last bunga record to determine next sequence
        $lastBunga = Bunga::where('pelaporan_id', $pelaporan->id)
            ->orderBy('bunga_ke', 'desc')
            ->first();
            
        $nextBungaKe = $lastBunga->bunga_ke + 1;
        $lastBungaDate = Carbon::parse($lastBunga->waktu_bunga);
        
        // Generate bunga for all missing months up to current month
        $currentDate = $lastBungaDate->copy()->addMonth();
        
        while ($currentDate->lte($now)) {
            // Check if bunga already exists for this month
            $existingBunga = Bunga::where('pelaporan_id', $pelaporan->id)
                ->whereYear('waktu_bunga', $currentDate->year)
                ->whereMonth('waktu_bunga', $currentDate->month)
                ->first();
                
            if (!$existingBunga) {
                // Calculate the appropriate day for this month
                $targetDay = self::calculateRecurringDay($originalDay, $currentDate->year, $currentDate->month);
                $bungaDate = Carbon::create($currentDate->year, $currentDate->month, $targetDay);
                
                // Create bunga record
                Bunga::create([
                    'pelaporan_id' => $pelaporan->id,
                    'waktu_bunga' => $bungaDate,
                    'bunga_ke' => $nextBungaKe,
                    'persentase_bunga' => $persentaseBunga * 100,
                    'bunga' => $persentaseBunga,
                    'keterangan' => "Bunga telat pembayaran ke-{$nextBungaKe}"
                ]);
                
                $nextBungaKe++;
            }
            
            $currentDate->addMonth();
        }
    }

    /**
     * Calculate the appropriate day for recurring bunga considering leap years and month-end variations
     */
    protected static function calculateRecurringDay(int $originalDay, int $year, int $month): int
    {
        // Get the last day of the target month
        $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->day;
        
        // Special handling for different scenarios
        if ($originalDay <= 28) {
            // Safe days that exist in all months
            return $originalDay;
        } else if ($originalDay == 29) {
            // Handle Feb 29 case
            if ($month == 2) {
                // February - check if leap year
                return Carbon::create($year, 2, 1)->isLeapYear() ? 29 : 28;
            } else {
                // Other months - use original day or last day of month
                return min($originalDay, $lastDayOfMonth);
            }
        } else if ($originalDay >= 30) {
            // Handle 30th and 31st cases
            if ($month == 2) {
                // February - always use 28 or 29 (leap year)
                return Carbon::create($year, 2, 1)->isLeapYear() ? 29 : 28;
            } else {
                // Other months - use original day or last day of month (whichever is smaller)
                return min($originalDay, $lastDayOfMonth);
            }
        }
        
        return $originalDay;
    }

    /**
     * Calculate the next recurring bunga date for a given pelaporan
     * This will be used to set the invoice expiration date
     */
    public static function getNextRecurringBungaDate(Pelaporan $pelaporan): Carbon
    {
        $now = now();
        $batas_pembayaran = $pelaporan->batas_pembayaran;
        
        // If we haven't passed the batas_pembayaran yet, the next bunga date is the day after batas_pembayaran
        if ($now->lessThan($batas_pembayaran)) {
            return $batas_pembayaran->copy()->addDay();
        }
        
        // Get the last bunga record to determine the pattern
        $lastBunga = Bunga::where('pelaporan_id', $pelaporan->id)
            ->orderBy('bunga_ke', 'desc')
            ->first();
            
        if (!$lastBunga) {
            // No bunga exists yet, so next bunga date is the day after batas_pembayaran
            return $batas_pembayaran->copy()->addDay();
        }
        
        // Get the first bunga to determine the original day pattern
        $firstBunga = Bunga::where('pelaporan_id', $pelaporan->id)
            ->orderBy('bunga_ke', 'asc')
            ->first();
            
        $originalDay = Carbon::parse($firstBunga->waktu_bunga)->day;
        $lastBungaDate = Carbon::parse($lastBunga->waktu_bunga);
        
        // Calculate the next month's bunga date
        $nextMonth = $lastBungaDate->copy()->addMonth();
        $targetDay = self::calculateRecurringDay($originalDay, $nextMonth->year, $nextMonth->month);
        
        return Carbon::create($nextMonth->year, $nextMonth->month, $targetDay);
    }

    protected static function getSystemSetting(string $key): int
    {
        return (int) PengaturanSistem::where('key', $key)->first()->value;
    }
}
