<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class UpdateSubscriptionStatus extends Command
{
    protected $signature = 'subscription:update-status-active';
    protected $description = 'Update subscription status to active if the start date is today';

    public function handle()
    {
        $today = Carbon::today();

        $subscriptions = Subscription::whereDate('start_date', $today)
                                      ->where('status', 'pending')
                                      ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions found for today.');
        } else {
            foreach ($subscriptions as $subscription) {
                $subscription->status = 'active';
                $subscription->save();
                $this->info("Subscription ID {$subscription->id} status updated to active.");
            }
        }

        $this->info('Subscription status update completed.');
    }
}
