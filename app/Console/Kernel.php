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
        $schedule->command('app:birthday-sms')
                 ->dailyAt('8')
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/birthdays.log'));

        $schedule->command('app:wallet-expired')
                 ->dailyAt('0')
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/wallets.log'));
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
