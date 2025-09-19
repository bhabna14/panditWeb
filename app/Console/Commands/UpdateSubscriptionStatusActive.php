<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionStatus extends Command
{
    protected $signature   = 'subscription:update-status-active';
    protected $description = 'Activate pending subscriptions that start today AND have a paid flower payment';

    public function handle(): int
    {
        $tz    = config('app.timezone', 'Asia/Kolkata');
        $today = Carbon::today($tz)->toDateString();

        Log::info('Running subscription:update-status-active', ['date' => $today]);

        $count = 0;

        // Only subscriptions that:
        // 1) start today
        // 2) currently status = pending
        // 3) have at least one paid flower payment
        Subscription::query()
            ->whereDate('start_date', $today)
            ->where('status', 'pending')
            ->whereHas('flowerPayments', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->orderBy('id')                  // for stable chunking
            ->chunkById(500, function ($subs) use (&$count) {
                foreach ($subs as $subscription) {
                    $subscription->status = 'active';
                    $subscription->save();

                    $count++;

                    Log::info('Subscription activated', [
                        'subscription_id' => $subscription->subscription_id,
                        'id'              => $subscription->id,
                        'order_id'        => $subscription->order_id,
                        'user_id'         => $subscription->user_id,
                    ]);
                }
            });

        if ($count === 0) {
            $this->info('No subscriptions to activate today (either none start today, are not pending, or no paid payment found).');
            Log::info('No subscriptions activated.');
        } else {
            $this->info("Activated {$count} subscription(s).");
            Log::info('subscription:update-status-active completed', ['activated' => $count]);
        }

        return self::SUCCESS;
    }
}
