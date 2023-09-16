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
        // @todo - feature for admins to set specific time for syncing StandTemplate
        $schedule->command('stand:sync-stand-template-weeks')->weeklyOn(1, '00:00'); // every Monday at 00:00. Cron - 0 0 * * 1
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
