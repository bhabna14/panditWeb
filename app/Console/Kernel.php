<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule subscription status updates
        $schedule->command('subscription:update-status-active')->daily();
        $schedule->command('subscription:update-status-expired')->daily();
        $schedule->command('subscription:update-paused-to-active')->daily();
        $schedule->command('subscription:update-status-active-to-pause')->daily();

        // Schedule for sending subscription ending notifications
        $schedule->command('subscriptions:sendEndingNotifications')
                 ->at('18:55')
                 ->runInBackground();

        $schedule->command('subscriptions:sendEndingNotifications')
                 ->at('18:57')
                 ->runInBackground();

                 Log::info('Scheduler running at: ' . now());

    $schedule->command('subscription:resume-paused')
             ->dailyAt('00:00')
             ->onSuccess(fn () => Log::info('subscription:resume-paused executed successfully'))
             ->onFailure(fn () => Log::error('subscription:resume-paused failed to execute'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        \App\Console\Commands\ResumePausedSubscriptions::class,
    ];

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */

}
