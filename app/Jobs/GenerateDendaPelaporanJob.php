<?php

namespace App\Jobs;

use App\Models\Pelaporan;
use Illuminate\Bus\Queueable;
use App\Services\DendaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateDendaPelaporanJob implements ShouldQueue
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
        $pelaporans = Pelaporan::where('is_paid', false)
            ->where('is_expired', false)
            ->whereDoesntHave('denda')
            ->get();

        $pelaporans->each(function ($pelaporan) {
            try {
                DendaService::generateDenda($pelaporan);
            } catch (\Exception $e) {
                Log::error('Failed to generate bunga for Pelaporan ID: ' . $pelaporan->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
