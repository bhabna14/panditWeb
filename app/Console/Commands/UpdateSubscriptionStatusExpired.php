<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionStatusExpired extends Command
{
    protected $signature = 'subscription:update-status-expired';
    protected $description = 'Automatically update subscriptions to expired status based on new_date or end_date';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();

        // Only process subscriptions that are active or paused
        $subscriptions = Subscription::whereIn('status', ['active', 'paused'])->get();

        foreach ($subscriptions as $subscription) {
            $expiryDate = $subscription->new_date ?? $subscription->end_date;

            if (!$expiryDate) {
                $this->info("Subscription ID {$subscription->id} has no expiry date. Skipping...");
                Log::info("Subscription ID {$subscription->id} skipped - no expiry date.");
                continue;
            }

            $adjustedExpiryDate = Carbon::parse($expiryDate)->addDay()->startOfDay();

            if ($today >= $adjustedExpiryDate) {
                $subscription->status = 'expired';
                $subscription->save();

                $this->info("Subscription ID {$subscription->id} status updated to expired.");
                Log::info("Subscription ID {$subscription->id} marked as expired.");
            } else {
                $this->info("Subscription ID {$subscription->id} is not yet expired.");
            }
        }

        $this->info('All matching subscriptions have been checked for expiration.');
        Log::info('subscription:update-status-expired completed.');
    }
}
