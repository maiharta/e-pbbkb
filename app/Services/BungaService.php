<?php

namespace App\Services;

use App\Models\Bunga;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
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


        $batas_pembayaran = $pelaporan->batas_pembayaran;
        $now = now();
        $first_send_at = $pelaporan->first_send_at;

        if (
            ($first_send_at && $first_send_at->isAfter($batas_pembayaran)) ||
            (!$first_send_at && $now->isAfter($batas_pembayaran))
        ) {
            $existingBunga = Bunga::where('pelaporan_id', $pelaporan->id)
                ->orderBy('bunga_ke', 'desc')
                ->get();

            if ($existingBunga->where('bunga_ke', 1)->isEmpty()) {
                Bunga::create([
                    'pelaporan_id' => $pelaporan->id,
                    'waktu_bunga' => $batas_pembayaran->addDays(1),
                    'bunga_ke' => 1,
                    'persentase_bunga' => 1,
                    'bunga' => 0.01 * $pelaporan->sptpd->total_pbbkb,
                    'keterangan' => 'Bunga telat pembayaran ke-1',
                ]);
            } else {
                // check if bunga ke 1 already exists
                $firstBunga = $existingBunga->first();
                if ($firstBunga && $firstBunga->bunga_ke == 1) {
                    $batas_pembayaran = $firstBunga->waktu_bunga;
                }
            }

            // loop every month between batas_pembayaran and now, check if bunga already exists dont create
            $month_diff = $batas_pembayaran->setDay(16)->diffInMonths($now);
            $existingBunga = Bunga::where('pelaporan_id', $pelaporan->id)
                ->orderBy('bunga_ke', 'desc')
                ->get();
            if($month_diff){
                foreach (range(1, $month_diff) as $i) {
                    if ($existingBunga->where('bunga_ke', $i + 1)->isEmpty()) {
                        Bunga::create([
                            'pelaporan_id' => $pelaporan->id,
                            'waktu_bunga' => $batas_pembayaran->addMonths($i),
                            'bunga_ke' => $i + 1,
                            'persentase_bunga' => 1,
                            'bunga' => 0.01 * $pelaporan->sptpd->total_pbbkb,
                            'keterangan' => 'Bunga telat pembayaran ke-' . ($i + 1),
                        ]);
                    }
                }
            }
        }
    }
}
