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

    // Command description
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
    
        // Get subscriptions where status is not already expired
        $subscriptions = Subscription::where('status', '!=', 'expired')->get();
    
        foreach ($subscriptions as $subscription) {
            // Determine the expiry date
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
                }
            }
        }
    
        $this->info('All matching subscriptions have been checked for expiration.');
    }
    
}
