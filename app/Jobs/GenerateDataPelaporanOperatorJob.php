<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Pelaporan;
use App\Services\CutiService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateDataPelaporanOperatorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate data pelaporan operator pada bulan dan tahun ini
        $users = User::role('operator')->whereHas('userDetail', function ($query) {
            $query->where('is_verified', true);
        })->get();

        foreach ($users as $user) {
            $user_verified_at = $user->userDetail->verified_at;
            $now = now();

            // foreach verfied at until now
            if ($user_verified_at && $user_verified_at->isBefore($now)) {
                foreach (range(0, $now->diffInMonths($user_verified_at)+1) as $i) {
                    $now = $user_verified_at->copy()->addMonths($i);
                    $bulan = $now->month;
                    $tahun = $now->year;
                    $pelaporan = Pelaporan::firstOrCreate([
                        'user_id' => $user->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ], [
                        'is_sent_to_admin' => false,
                        'is_verified' => false,
                    ]);
                    $batas_pelaporan = CutiService::getBatasPelaporan($pelaporan);
                    $batas_pembayaran = CutiService::getBatasPembayaran($pelaporan);
                    $pelaporan->update([
                        'batas_pelaporan' => $batas_pelaporan,
                        'batas_pembayaran' => $batas_pembayaran,
                    ]);
                }
            }
        }
    }
}
