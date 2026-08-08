<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * Requires the cron job `php artisan schedule:run` once a minute
     * (see deploy notes) - country lookups happen here, never in the
     * request path.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('pageviews:resolve-countries --limit=100')->everyFiveMinutes();

        $schedule->command('reminders:notify-due')->everyFiveMinutes();

        $schedule->command('projects:expire-stale-claims')->hourly();
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
