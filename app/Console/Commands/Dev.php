<?php

namespace App\Console\Commands;

use App\Models\Pelaporan;
use App\Services\PdfService;
use Illuminate\Console\Command;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';

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
        PdfService::generateSspd(Pelaporan::first());
    }
}
