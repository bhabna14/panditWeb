<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        \App\Console\Commands\ResumePausedSubscriptions::class,
        \App\Console\Commands\UpdateSubscriptionStatus::class,
        \App\Console\Commands\UpdateSubscriptionStatusActivetoPause::class,
        \App\Console\Commands\UpdateSubscriptionStatusExpired::class,
        \App\Console\Commands\UpdatePausedSubscriptions::class, // ✅ REGISTERED HERE
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // ✅ Subscription status update schedules
        $schedule->command('subscription:update-status-active')
                 ->dailyAt('00:00');

        $schedule->command('subscription:update-status-expired')
                 ->daily(); // ⏰ Will run every day

        $schedule->command('subscription:update-paused-to-active')
                 ->daily();

        $schedule->command('subscription:update-status-active-to-pause')
                 ->daily();

        // ✅ Send subscription ending notifications
        $schedule->command('subscriptions:sendEndingNotifications')
                 ->at('18:55')
                 ->runInBackground();

        $schedule->command('subscriptions:sendEndingNotifications')
                 ->at('18:57')
                 ->runInBackground();

        // ✅ Resume paused subscriptions daily at midnight
        $schedule->command('subscription:resume-paused')
                 ->dailyAt('00:00')
                 ->onSuccess(function () {
                     Log::info('subscription:resume-paused executed successfully');
                 })
                 ->onFailure(function () {
                     Log::error('subscription:resume-paused failed to execute');
                 });

        // ✅ Log that scheduler is running
        Log::info('Scheduler running at: ' . now());
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
