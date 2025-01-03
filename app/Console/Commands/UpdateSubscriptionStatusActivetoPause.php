<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class UpdateSubscriptionStatusActivetoPause extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:update-status-active-to-pause';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update subscription status to paused if the pause start date is today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
    
        // Find subscriptions scheduled to be paused today
        $subscriptions = Subscription::where('pause_start_date', $today)
            ->where('status', '!=', 'paused')
            ->get();
    
        foreach ($subscriptions as $subscription) {
            $subscription->status = 'paused';
            // $subscription->is_active = false; // Assuming paused subscriptions are inactive
            $subscription->save();
    
            // Log the status update
            Log::info('Subscription status updated to paused', [
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $subscription->order_id,
            ]);
        }
    
        $this->info('Subscription statuses updated successfully for paused subscriptions.');
    }
    
}
