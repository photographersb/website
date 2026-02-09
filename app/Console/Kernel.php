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
        // Deactivate expired featured photographers daily at 2 AM
        $schedule->command('featured:deactivate-expired')
            ->dailyAt('02:00')
            ->onOneServer()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Successfully deactivated expired featured photographers');
            })
            ->onFailure(function () {
                \Log::error('Failed to deactivate expired featured photographers');
            });

        // Send renewal reminders 3 days before expiration (daily at 1 AM)
        $schedule->command('featured:send-renewal-reminders')
            ->dailyAt('01:00')
            ->onOneServer()
            ->withoutOverlapping();

        // Calculate and update featured photographer statistics (hourly)
        $schedule->command('featured:update-statistics')
            ->hourly()
            ->withoutOverlapping();

        // Send booking reminders (daily at 9 AM)
        $schedule->command('bookings:send-reminders')
            ->dailyAt('09:00')
            ->onOneServer()
            ->withoutOverlapping();

        // Request reviews for completed bookings (daily at 10 AM)
        $schedule->command('bookings:request-reviews')
            ->dailyAt('10:00')
            ->onOneServer()
            ->withoutOverlapping();
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
