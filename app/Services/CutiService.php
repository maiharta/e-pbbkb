<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Invoice;
use App\Models\Pelaporan;
use App\Models\PengaturanSistem;

class CutiService
{
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

    /**
     * Get holidays (cuti) for a specific month and year
     *
     * @param Carbon $date
     * @return \Illuminate\Support\Collection
     */
    protected static function getHolidaysForMonth(Carbon $date)
    {
        return Cuti::query()
            ->whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->pluck('tanggal');
    }

    /**
     * Calculate a deadline date by adding working days to a start date
     *
     * @param Carbon $startDate
     * @param int $workingDays
     * @param \Illuminate\Support\Collection|null $holidays
     * @return Carbon
     */
    protected static function calculateDeadlineDate(Carbon $startDate, int $workingDays, $holidays = null): Carbon
    {
        // Clone the start date to avoid modifying the original
        $deadlineDate = $startDate->copy();
        $workingDaysAdded = 0;

        // Get holidays if not provided
        if ($holidays === null) {
            $holidays = self::getHolidaysForMonth($startDate);
        }

        while ($workingDaysAdded < $workingDays) {
            $deadlineDate->addDay();

            // Count only weekdays that are not holidays
            if (!$deadlineDate->isWeekend() && !$holidays->contains($deadlineDate->format('Y-m-d'))) {
                $workingDaysAdded++;
            }
        }

        return $deadlineDate;
    }

    /**
     * Get the reporting deadline for a pelaporan
     *
     * @param Pelaporan $pelaporan
     * @return Carbon
     */
    public static function getBatasPelaporan(Pelaporan $pelaporan): Carbon
    {
        // Get the configured working days limit
        $batasPelaporan = self::getSystemSetting('batas_pelaporan');

        // Create start date (last day of the reporting month)
        $startDate = Carbon::create($pelaporan->year, $pelaporan->month)->endOfMonth();

        // Get holidays for the following month
        $nextMonth = $startDate->copy()->addDay();
        $holidays = self::getHolidaysForMonth($nextMonth);

        // Calculate deadline date
        return self::calculateDeadlineDate($startDate, $batasPelaporan, $holidays);
    }

    /**
     * Get the payment deadline for a pelaporan
     *
     * @param Pelaporan $pelaporan
     * @return Carbon
     */
    public static function getBatasPembayaran(Pelaporan $pelaporan): Carbon
    {
        // Get the configured working days limit
        $batasPembayaran = self::getSystemSetting('batas_pembayaran');

        // Create start date (last day of the reporting month)
        $startDate = Carbon::create($pelaporan->year, $pelaporan->month)->endOfMonth();

        // Get holidays for the following month
        $nextMonth = $startDate->copy()->addDay();
        $holidays = self::getHolidaysForMonth($nextMonth);

        // Calculate deadline date
        return self::calculateDeadlineDate($startDate, $batasPembayaran, $holidays);
    }

    /**
     * Update deadline dates for a pelaporan
     *
     * @param Pelaporan $pelaporan
     * @return bool
     */
    public static function updateBatasPelaporan(Pelaporan $pelaporan): bool
    {
        // Skip updates for paid or expired reports
        if ($pelaporan->is_paid || $pelaporan->is_expired) {
            return false;
        }

        $batasPelaporan = self::getBatasPelaporan($pelaporan);
        $batasPembayaran = self::getBatasPembayaran($pelaporan);

        return $pelaporan->update([
            'batas_pelaporan' => $batasPelaporan->format('Y-m-d'),
            'batas_pembayaran' => $batasPembayaran->format('Y-m-d'),
        ]);
    }

    /**
     * Update all active pelaporan deadlines
     *
     * @return int Number of updated records
     */
    public static function updateAllPelaporan(): int
    {
        $pelaporans = Pelaporan::query()
            ->where('is_paid', false)
            ->where('is_expired', false)
            ->get();

        $updatedCount = 0;

        $pelaporans->each(function (Pelaporan $pelaporan) use (&$updatedCount) {
            if (self::updateBatasPelaporan($pelaporan)) {
                $updatedCount++;
            }
        });

        return $updatedCount;
    }

    public static function updateAllInvoices()
    {
        $invoices = Invoice::query()
            ->where('is_paid', false)
            ->where('payment_status', 'pending')
            ->get();

        $invoices->each(function (Invoice $invoice) {
            // Update invoice based on pelaporan
            $expires_at = CutiService::getDateAfterCuti(
                $invoice->created_at->startOfMonth()->addMonth(),
                10,
            );

            $invoice->expires_at = $expires_at;
            $invoice->save();
        });
    }

    /**
     * Update pelaporan deadlines for a specific month and year
     *
     * @param int $month
     * @param int $year
     * @return int Number of updated records
     */
    public static function updateBatasPelaporanByMonthYear(int $month, int $year): int
    {
        $pelaporans = Pelaporan::query()
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->where('is_paid', false)
            ->where('is_expired', false)
            ->get();

        $updatedCount = 0;

        $pelaporans->each(function (Pelaporan $pelaporan) use (&$updatedCount) {
            if (self::updateBatasPelaporan($pelaporan)) {
                $updatedCount++;
            }
        });

        return $updatedCount;
    }

    /**
     * Calculate a date by adding working days to a start date
     *
     * @param Carbon $startDate
     * @param int $days
     * @return Carbon
     */
    public static function getDateAfterCuti(Carbon $startDate, int $days): Carbon
    {
        $holidays = self::getHolidaysForMonth($startDate);
        return self::calculateDeadlineDate($startDate, $days, $holidays);
    }
}
