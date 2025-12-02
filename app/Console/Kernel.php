<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 00.01 at gmt+8
        $schedule->command('generate:denda-bunga')->timezone('GMT+8')->dailyAt('00:01');
        $schedule->command('generate:data-pelaporan-operator')->timezone('GMT+8')->dailyAt('00:02');
        // 00.03 at gmt+8
        $schedule->command('generate:invoices')->timezone('GMT+8')->dailyAt('00:03');
        // Add other scheduled commands here as needed
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
