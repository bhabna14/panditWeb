<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionStatusActivetoPause extends Command
{
    protected $signature = 'subscription:update-status-active-to-pause';
    protected $description = 'Pause active subscriptions where pause_start_date is today';

    public function handle()
    {
        $today = Carbon::today();

        $subscriptions = Subscription::whereDate('pause_start_date', $today)
            ->where('status', 'active')
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions found to pause today.');
            Log::info('No active subscriptions to pause on ' . $today);
        } else {
            foreach ($subscriptions as $subscription) {
                $subscription->status = 'paused';
                $subscription->save();

                Log::info('Subscription paused', [
                    'subscription_id' => $subscription->subscription_id,
                    'order_id' => $subscription->order_id,
                ]);
            }

            $this->info('Subscription statuses updated successfully to paused.');
            Log::info('subscription:update-status-active-to-pause completed.');
        }
    }
}
