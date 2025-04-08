<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Pelaporan;
use App\Models\PengaturanSistem;
use Carbon\Traits\Date;

class CutiService
{
    public static function getBatasPelaporan($month, $year)
    {
        $batas_pelaporan = PengaturanSistem::where('key', 'batas_pelaporan')->first()->value;
        $start_date = Carbon::now()->setMonth($month)->setYear($year)->startOfMonth();
        $cutis = Cuti::query()
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->pluck('tanggal');

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


    public static function getBatasPembayaran($month, $year)
    {
        $batas_pembayaran = PengaturanSistem::where('key', 'batas_pembayaran')->first()->value;
        $start_date = self::getBatasPelaporan($month, $year);
        $cutis = Cuti::query()
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->pluck('tanggal');

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

    public static function updateBatasPelaporan($month, $year)
    {
        $pelaporans = Pelaporan::query()
            ->where('bulan', $month)
            ->where('tahun', $year)
            // TODO : EXCLUDE FINISHED Pelaporan
            ->get();

        $batas_pelaporan = self::getBatasPelaporan($month, $year);
        $batas_pembayaran = self::getBatasPembayaran($month, $year);

        $pelaporans->each(
            function ($item) use ($batas_pelaporan, $batas_pembayaran) {
                $item->update([
                    'batas_pelaporan' => $batas_pelaporan->format('Y-m-d'),
                    'batas_pembayaran' => $batas_pembayaran->format('Y-m-d'),
                ]);
            }
        );
    }

    public static function updateAllPelaporan()
    {
        $pelaporans = Pelaporan::query()
            // TODO : EXCLUDE FINISHED Pelaporan
            ->get();

        $pelaporan_by_year = $pelaporans->groupBy('tahun');

        $pelaporan_by_year->each(function ($item, $year) {
            $item->groupBy('bulan')
                ->each(function ($pelaporan, $month) use ($year) {
                    $batas_pelaporan = self::getBatasPelaporan($month, $year);
                    $batas_pembayaran = self::getBatasPembayaran($month, $year);

                    $pelaporan->each(
                        function ($item) use ($batas_pelaporan, $batas_pembayaran) {
                            $item->update([
                                'batas_pelaporan' => $batas_pelaporan->format('Y-m-d'),
                                'batas_pembayaran' => $batas_pembayaran->format('Y-m-d'),
                            ]);
                        }
                    );
                });
        });
    }
}
