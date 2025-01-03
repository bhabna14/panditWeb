<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class UpdateSubscriptionStatusActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:update-status-active';
    protected $description = 'Update subscription status to active if the start date is today';


    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Get all subscriptions whose start_date matches today
        $subscriptions = Subscription::where('start_date', $today)
                                      ->where('status', 'pending') // Optional: Only update 'pending' ones
                                      ->get();

        // Update status to active for all matching subscriptions
        foreach ($subscriptions as $subscription) {
            $subscription->status = 'active';
            $subscription->save();
            $this->info("Subscription ID {$subscription->id} status updated to active.");
        }

        $this->info('All matching subscriptions have been updated.');
    }
}
