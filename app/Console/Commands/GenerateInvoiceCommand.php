<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoicesJob;
use Illuminate\Console\Command;

class GenerateInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:invoices';

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
        GenerateInvoicesJob::dispatch();
    }
}
