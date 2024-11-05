<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update paused subscriptions to active if the pause end date has passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();

        // Find paused subscriptions where the pause end date has passed
        $subscriptions = Subscription::where('status', 'paused')
            ->where('pause_end_date', '<=', $today)
            ->get();

        foreach ($subscriptions as $subscription) {
            // Update the status to active
            $subscription->status = 'active';
            $subscription->is_active = true;
            $subscription->pause_start_date = null;
            $subscription->pause_end_date = null;
            $subscription->save();

            // Log the status update
            Log::info('Subscription status updated to active', [
                'order_id' => $subscription->order_id,
                'user_id' => $subscription->user_id,
            ]);
        }

        $this->info('Subscription statuses updated successfully.');

        return Command::SUCCESS;
    }
}
