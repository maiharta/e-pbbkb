<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDataPelaporanOperatorJob;
use Illuminate\Console\Command;

class GenerateDataPelaporanOperatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:data-pelaporan-operator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data pelaporan operator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        GenerateDataPelaporanOperatorJob::dispatch();
    }
}
