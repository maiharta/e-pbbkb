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
            $bulan = now()->month - 1;
            $tahun = now()->year;
            $batas_pelaporan = CutiService::getBatasPelaporan($bulan, $tahun);
            $batas_pembayaran = CutiService::getBatasPembayaran($bulan, $tahun);
            $pelaporan = Pelaporan::firstOrCreate([
                'user_id' => $user->id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ], [
                'is_sent_to_admin' => false,
                'is_verified' => false,
                'batas_pelaporan' => $batas_pelaporan,
                'batas_pembayaran' => $batas_pembayaran
            ]);
        }
    }
}
