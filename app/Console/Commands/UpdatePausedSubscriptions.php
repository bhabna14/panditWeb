<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePausedSubscriptions extends Command
{
    protected $signature = 'subscription:update-paused-to-active';
    protected $description = 'Automatically update paused subscriptions to active when the pause period ends';

    public function handle()
    {
        $today = Carbon::today();

        // Step 1: Set subscriptions to paused if pause_start_date is today
        $pausedTodayCount = Subscription::whereDate('pause_start_date', $today)
            ->update(['status' => 'paused']);

        if ($pausedTodayCount > 0) {
            Log::info("Set $pausedTodayCount subscriptions to paused based on pause_start_date = today.");
        }

        // Step 2: Resume subscriptions if pause_end_date was yesterday
        $yesterday = Carbon::yesterday();

        $subscriptionsToResume = Subscription::where('status', 'paused')
            ->whereDate('pause_end_date', $yesterday)
            ->get();

        if ($subscriptionsToResume->isEmpty()) {
            Log::info('No paused subscriptions to resume today based on pause_end_date.');
        }

        foreach ($subscriptionsToResume as $subscription) {
            $subscription->status = 'active';
            $subscription->is_active = true;
            $subscription->pause_start_date = null;
            $subscription->pause_end_date = null;
            $subscription->save();

            Log::info('Subscription resumed to active (pause ended)', [
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $subscription->order_id,
                'user_id' => $subscription->user_id,
            ]);
        }

        $this->info('Paused subscriptions updated successfully.');
        return Command::SUCCESS;
    }
}
