<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Pelaporan;
use App\Models\PengaturanSistem;
use Carbon\Traits\Date;

class CutiService
{
    public static function getBatasPelaporan(Pelaporan $pelaporan)
    {
        $batas_pelaporan = PengaturanSistem::where('key', 'batas_pelaporan')->first()->value;
        $start_date = Carbon::now()->setMonth($pelaporan->month)->setYear($pelaporan->year)->startOfMonth()->addMonth();
        $cutis = Cuti::query()
            ->whereMonth('tanggal', $start_date->month)
            ->whereYear('tanggal', $start_date->year)
            ->pluck('tanggal');

        $start_date = $start_date->subDay();

        // 3. Calculate the deadline date
        $deadline_date = $start_date; // Start with the initial date
        $working_days_added = 0; // Track working days added

        while ($working_days_added < $batas_pelaporan) {
            $deadline_date->addDay(); // Move to the next day

            // Check if it's a weekend or a holiday
            if (!$deadline_date->isWeekend() && !$cutis->contains($deadline_date->format('Y-m-d'))) {
                $working_days_added++; // Increment working days if it's a weekday and not a holiday
            }
        }

        return $deadline_date;
    }


    public static function getBatasPembayaran(Pelaporan $pelaporan)
    {
        $batas_pembayaran = PengaturanSistem::where('key', 'batas_pembayaran')->first()->value;
        $start_date = Carbon::now()->setMonth($pelaporan->month)->setYear($pelaporan->year)->startOfMonth()->addMonth();
        $cutis = Cuti::query()
            ->whereMonth('tanggal', $start_date->month)
            ->whereYear('tanggal', $start_date->year)
            ->pluck('tanggal');

        $start_date = $start_date->subDay(); // Adjust start date to the last day of the month before the payment deadline

        // 3. Calculate the deadline date
        $deadline_date = $start_date; // Start with the initial date
        $working_days_added = 0; // Track working days added

        while ($working_days_added < $batas_pembayaran) {
            $deadline_date->addDay(); // Move to the next day

            // Check if it's a weekend or a holiday
            if (!$deadline_date->isWeekend() && !$cutis->contains($deadline_date->format('Y-m-d'))) {
                $working_days_added++; // Increment working days if it's a weekday and not a holiday
            }
        }

        return $deadline_date;
    }

    public static function updateBatasPelaporan(Pelaporan $pelaporan)
    {
        if ($pelaporan->is_paid || $pelaporan->is_expired) {
            return;
        }

        $batas_pelaporan = self::getBatasPelaporan($pelaporan);
        $batas_pembayaran = self::getBatasPembayaran($pelaporan);

        // Update the pelaporan with the new batas dates
        $pelaporan->update([
            'batas_pelaporan' => $batas_pelaporan->format('Y-m-d'),
            'batas_pembayaran' => $batas_pembayaran->format('Y-m-d'),
        ]);
    }

    public static function updateAllPelaporan()
    {
        $pelaporans = Pelaporan::query()
            ->where('is_paid', false)
            ->where('is_expired', false)
            ->get();

        $pelaporans->each(function (Pelaporan $pelaporan) {
            self::updateBatasPelaporan($pelaporan);
        });
    }

    public static function updateBatasPelaporanByMonthYear(int $month, int $year)
    {
        $pelaporans = Pelaporan::query()
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->where('is_paid', false)
            ->where('is_expired', false)
            ->get();

        $pelaporans->each(function (Pelaporan $pelaporan) {
            self::updateBatasPelaporan($pelaporan);
        });
    }
}
