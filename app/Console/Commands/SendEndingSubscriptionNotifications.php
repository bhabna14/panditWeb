<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\UserDevice;
use App\Services\NotificationService;
class SendEndingSubscriptionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:sendEndingNotifications';
    protected $description = 'Send notifications to users whose subscription is ending soon';


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
        $now = Carbon::now(); // Get current date and time
        $endDateThreshold = $now->addDays(5); // Next 5 days

        // Query for subscriptions that are ending in the next 5 days
        $subscriptionQuery = Subscription::where(function ($query) use ($now, $endDateThreshold) {
            $query->whereNotNull('new_date')
                ->whereBetween('new_date', [$now, $endDateThreshold]);
        })->orWhere(function ($query) use ($now, $endDateThreshold) {
            $query->whereNull('new_date')
                ->whereBetween('end_date', [$now, $endDateThreshold]);
        })->get();

        // Loop through subscriptions and send notifications
        foreach ($subscriptionQuery as $subscription) {
            $user_id = $subscription->user_id;
            $deviceTokens = UserDevice::where('user_id', $user_id)->pluck('device_id')->toArray();

            if (!empty($deviceTokens)) {
                $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                $notificationService->sendBulkNotifications(
                    $deviceTokens,
                    'Subscription Ending Soon',
                    'Please start your Subscription Now, To avoid any inconvenience.',
                    ['order_id' => $subscription->order_id]
                );

                \Log::info('Notification sent successfully to all devices.', [
                    'user_id' => $user_id,
                    'device_tokens' => $deviceTokens,
                ]);
            }
        }
    }
}
