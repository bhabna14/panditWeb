<?php

namespace App\Console\Commands;
use App\Models\Subscription;
use App\Models\SubscriptionPauseResumeLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePausedSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   // Command signature
   protected $signature = 'subscription:update-paused-to-active';

   // Command description
   protected $description = 'Automatically update paused subscriptions to active when the pause period ends';

    /**
     * The console command description.
     *
     * @var string
     */
    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Fetch logs where pause_end_date has passed and resume_date is null
        $logs = SubscriptionPauseResumeLog::where('action', 'paused')
            ->whereNull('resume_date')
            ->where('pause_end_date', '<=', $today)
            ->get();

        foreach ($logs as $log) {
            $subscription = Subscription::where('id', $log->subscription_id)
                ->where('status', 'paused')
                ->first();

            if ($subscription) {
                // Update subscription status to active
                $subscription->status = 'active';
                $subscription->save();

                // Update resume_date in log
                $log->resume_date = $today;
                $log->save();

                $this->info("Subscription ID {$subscription->id} status updated to active.");
            }
        }

        $this->info('All matching paused subscriptions have been updated.');
    }
}
