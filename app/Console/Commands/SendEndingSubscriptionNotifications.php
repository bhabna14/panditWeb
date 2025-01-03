<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\UserDevice;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

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
        \Log::info('Current date and time:', ['now' => $now]);
    
        $endDateThreshold = $now->addDays(5); // Subscriptions ending in the next 5 days
        \Log::info('End date threshold:', ['endDateThreshold' => $endDateThreshold]);
    
        // Log the generated SQL query for debugging
        $query = Subscription::where(function ($query) use ($now, $endDateThreshold) {
            // If new_date is available, use it. Otherwise, fall back to end_date.
            $query->whereNotNull('new_date')
                  ->whereBetween('new_date', [$now, $endDateThreshold]);
        })->orWhere(function ($query) use ($now, $endDateThreshold) {
            // If new_date is NULL, use end_date
            $query->whereNull('new_date')
                  ->whereBetween('end_date', [$now, $endDateThreshold]);
        });
        
        \Log::info('SQL Query:', ['query' => $query->toSql()]);
    
        // Fetch subscriptions
        $subscriptions = $query->get();
    
        \Log::info('Fetched subscriptions:', ['subscriptions_count' => $subscriptions->count()]);
    
        // Loop through subscriptions and send notifications
        foreach ($subscriptions as $subscription) {
            \Log::info('Processing subscription:', ['subscription_id' => $subscription->id]);
    
            // Determine the correct end date to use (either new_date or end_date)
            $subscriptionEndDate = $subscription->new_date ?? $subscription->end_date;
            \Log::info('Subscription end date:', ['subscriptionEndDate' => $subscriptionEndDate]);
    
            // If the subscription is ending in the next 5 days, send a notification
            if (Carbon::parse($subscriptionEndDate)->between($now, $endDateThreshold)) {
                $user_id = $subscription->user_id;
                \Log::info('Subscription is ending soon. User ID:', ['user_id' => $user_id]);
    
                // Fetch device tokens for the user
                $deviceTokens = UserDevice::where('user_id', $user_id)
                                          ->whereNotNull('device_id')
                                          ->pluck('device_id')
                                          ->toArray();
    
                \Log::info('Fetched device tokens:', ['device_tokens_count' => count($deviceTokens), 'user_id' => $user_id]);
    
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
    
        \Log::info('Subscription processing completed.');
    }
    
    
}
