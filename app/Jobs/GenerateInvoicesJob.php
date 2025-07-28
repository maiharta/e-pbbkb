<?php

namespace App\Jobs;

use Exception;
use App\Models\Pelaporan;
use Illuminate\Bus\Queueable;
use App\Services\SipayService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateInvoicesJob implements ShouldQueue
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
            ->get();

        $pelaporans->each(function ($pelaporan) {
            try {
                InvoiceService::generateInvoice($pelaporan);
            } catch (Exception $e) {
                Log::error('Failed to generate invoice for Pelaporan', [
                    'pelaporan_id' => $pelaporan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });

    }
}
