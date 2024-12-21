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
        // $schedule->command('inspire')->hourly();
        $schedule->command('subscriptions:update-status')->daily();
        $schedule->command('subscription:update-status-expired')->daily();
        $schedule->command('subscription:update-paused-to-active')->daily();
        // Scheduler for Subscription ending soon
        // $schedule->command('subscriptions:sendEndingNotifications')
        //      ->twiceDaily(9, 17); // Runs at 9 AM and 5 PM

             $schedule->command('subscriptions:sendEndingNotifications')
             ->at('15:28')
             ->runInBackground(); // Runs at 15:20 (3:20 PM)
             
    $schedule->command('subscriptions:sendEndingNotifications')
             ->at('15:29')
             ->runInBackground(); // Runs at 15:21 (3:21 PM)
        
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
    
}
