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
        $endDateThreshold = $now->addDays(5); // Subscriptions ending in the next 5 days

        // Query subscriptions where either new_date or end_date is within the next 5 days
        $subscriptions = Subscription::where(function ($query) use ($now, $endDateThreshold) {
            // If new_date is available, use it. Otherwise, fall back to end_date.
            $query->whereNotNull('new_date')
                  ->whereBetween('new_date', [$now, $endDateThreshold]);
        })->orWhere(function ($query) use ($now, $endDateThreshold) {
            // If new_date is NULL, use end_date
            $query->whereNull('new_date')
                  ->whereBetween('end_date', [$now, $endDateThreshold]);
        })->get();

        // Loop through subscriptions and send notifications
        foreach ($subscriptions as $subscription) {
            // Determine the correct end date to use (either new_date or end_date)
            $subscriptionEndDate = $subscription->new_date ?? $subscription->end_date;
            
            // If the subscription is ending in the next 5 days, send a notification
            if (Carbon::parse($subscriptionEndDate)->between($now, $endDateThreshold)) {
                $user_id = $subscription->user_id;

                // Fetch device tokens for the user
                $deviceTokens = UserDevice::where('user_id', $user_id)->whereNotNull('device_id')->pluck('device_id')->toArray();

                if (!empty($deviceTokens)) {
                    // Send the notification
                    $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                    $notificationService->sendBulkNotifications(
                        $deviceTokens,
                        'Subscription Ending Soon',
                        'Your subscription is ending soon. Please renew it to avoid any inconvenience.',
                        ['order_id' => $subscription->order_id]
                    );

                    \Log::info('Notification sent successfully to user.', [
                        'user_id' => $user_id,
                        'device_tokens' => $deviceTokens,
                    ]);
                } else {
                    \Log::warning('No device tokens found for user.', ['user_id' => $user_id]);
                }
            }
        }
    }
}
