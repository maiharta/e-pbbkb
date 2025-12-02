<?php

namespace App\Console\Commands;

use App\Jobs\GenerateBungaPembayaranJob;
use Illuminate\Console\Command;
use App\Jobs\GenerateDendaPelaporanJob;

class GenerateDendaBungaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:denda-bunga';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        GenerateDendaPelaporanJob::dispatch();
        GenerateBungaPembayaranJob::dispatch();
    }
}
