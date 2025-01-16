<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateSubscriptionStatusExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:update-status-expired';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Automatically update subscriptions to expired status based on new_date or end_date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get today's date
        $today = Carbon::now()->startOfDay();

        // Get subscriptions where status is not 'dead' (we want to skip 'dead' subscriptions)
        $subscriptions = Subscription::whereIn('status', ['active', 'paused'])->get();

        foreach ($subscriptions as $subscription) {
            // Skip if the subscription status is 'dead'
            if ($subscription->status === 'dead') {
                $this->info("Subscription ID {$subscription->id} is marked as 'dead' and will not be updated.");
                continue;  // Skip this subscription
            }

            // Determine the expiry date (new_date takes priority over end_date)
            $expiryDate = $subscription->new_date ?? $subscription->end_date;

            if ($expiryDate) {
                // Add one day to the expiry date
                $adjustedExpiryDate = Carbon::parse($expiryDate)->addDay()->startOfDay();

                // Check if today's date is on or after the adjusted expiry date
                if ($today >= $adjustedExpiryDate) {
                    // Update status to expired
                    $subscription->status = 'expired';
                    $subscription->save();

                    $this->info("Subscription ID {$subscription->id} status updated to expired.");
                } else {
                    $this->info("Subscription ID {$subscription->id} is still active/paused. No update needed.");
                }
            } else {
                $this->info("Subscription ID {$subscription->id} has no expiry date. Skipping...");
            }
        }

        $this->info('All matching subscriptions have been checked for expiration.');
    }
}
