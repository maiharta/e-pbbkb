<?php

namespace App\Services;

use App\Models\Denda;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
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
        $first_send_at = $pelaporan->first_send_at;

        if (
            ($first_send_at && $first_send_at->isAfter($batas_pelaporan)) ||
            (!$first_send_at && $now->isAfter($batas_pelaporan))
        ) {
            $existingDenda = Denda::where('pelaporan_id', $pelaporan->id)
                ->orderBy('denda_ke', 'desc')
                ->get();

            if($existingDenda->where('denda_ke', 1)->isEmpty()) {
                Denda::create([
                    'pelaporan_id' => $pelaporan->id,
                    'waktu_denda' => $batas_pelaporan->addDays(1),
                    'denda_ke' => 1,
                    'denda' => 1000000,
                    'keterangan' => null,
                ]);
            } else {
                // check if denda ke 1 already exists
                $firstDenda = $existingDenda->first();
                if ($firstDenda && $firstDenda->denda_ke == 1) {
                    $batas_pelaporan = $firstDenda->waktu_denda;
                }
            }

            // loop every month between batas_pelaporan and now, check if denda already exists dont create
            $month_diff = $batas_pelaporan->setDay(11)->diffInMonths($now);
            echo "month_diff: $month_diff\n";
            $existingDenda = Denda::where('pelaporan_id', $pelaporan->id)
                ->orderBy('denda_ke', 'desc')
                ->get();
            foreach (range(1, $month_diff) as $i) {
                if ($existingDenda->where('denda_ke', $i+1)->isEmpty()) {
                    Denda::create([
                        'pelaporan_id' => $pelaporan->id,
                        'waktu_denda' => $batas_pelaporan->addMonths($i),
                        'denda_ke' => $i+1,
                        'denda' => 100000,
                        'keterangan' => null,
                    ]);
                }
            }
        }
    }
}
