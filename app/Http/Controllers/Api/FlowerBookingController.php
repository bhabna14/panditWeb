<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerProduct;
use App\Models\FlowerRequest;
use App\Models\SubscriptionPauseResumeLog;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade
use App\Mail\FlowerRequestMail;
use App\Mail\SubscriptionConfirmationMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPayment;
use App\Models\FlowerRequestItem;
use App\Services\NotificationService;
use App\Models\UserDevice;
use Razorpay\Api\Api;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\DB;

class FlowerBookingController extends Controller
{
   
    public function purchaseSubscription(Request $request)
    {
        $user = Auth::guard('sanctum')->user(); // Get the authenticated user
    
        try {
    
            $orderId = $request->order_id;
            $productId = $request->product_id;
            $addressId = $request->address_id;
            $suggestion = $request->suggestion;
            $paymentId = $request->payment_id;
    
            // Order handling
            if ($orderId) {
                $order = Order::where('order_id', $orderId)->first();
                if ($order) {
                    $order->update([
                        'product_id' => $productId,
                        'user_id' => $user->userid,
                        'quantity' => 1,
                        'total_price' => $request->paid_amount,
                        'address_id' => $addressId,
                        'suggestion' => $suggestion,
                    ]);
                } else {
                    return response()->json(['message' => 'Order not found for update'], 404);
                }
            } else {
                $orderId = 'ORD-' . strtoupper(Str::random(12));
                Order::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'user_id' => $user->userid,
                    'quantity' => 1,
                    'total_price' => $request->paid_amount,
                    'address_id' => $addressId,
                    'suggestion' => $suggestion,
                ]);
            }
    
            // Razorpay payment handling
            $razorpayApi = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $payment = $razorpayApi->payment->fetch($paymentId);
    
            if ($payment->status === 'authorized') {
                $payment->capture(['amount' => $payment->amount]);
            } elseif ($payment->status !== 'captured') {
                return response()->json(['message' => 'Payment failed or not authorized.'], 400);
            }
    
            // Subscription logic
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
            $endDate = match ($request->duration) {
                1 => $startDate->copy()->addDays(29),
                3 => $startDate->copy()->addDays(89),
                6 => $startDate->copy()->addDays(179),
                default => throw new \Exception('Invalid subscription duration'),
            };
    
            $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
            Subscription::create([
                'subscription_id' => $subscriptionId,
                'user_id' => $user->userid,
                'order_id' => $orderId,
                'product_id' => $productId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'status' => $startDate->isToday() ? 'active' : 'pending',
            ]);
    
            // Record payment
            FlowerPayment::create([
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'user_id' => $user->userid,
                'payment_method' => 'Razorpay',
                'paid_amount' => $request->paid_amount,
                'payment_status' => 'paid',
            ]);
    
            // Notification
            $deviceTokens = UserDevice::where('user_id', $user->userid)->whereNotNull('device_id')->pluck('device_id')->toArray();
            if ($deviceTokens) {
                $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                $notificationService->sendBulkNotifications($deviceTokens, 'Order Received', 'Your subscription has been placed successfully.', [
                    'subscription_id' => $subscriptionId,
                ]);
            }
    
            // Send email
            $emails = [
                'soumyaranjan.puhan@33crores.com',
                'pankaj.sial@33crores.com',
                'basudha@33crores.com',
                'priya@33crores.com',
                'starleen@33crores.com'
            ];
                        Mail::to($emails)->send(new SubscriptionConfirmationMail(Order::where('order_id', $orderId)->first()));
    
            return response()->json([
                'message' => 'Subscription activated successfully',
                'end_date' => $endDate,
                'order_id' => $orderId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error processing subscription', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to process subscription', 'error' => $e->getMessage()], 500);
        }
    }
    
public function storerequest(Request $request)
{
    try {
        // Start a transaction
        DB::beginTransaction();

        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();

        // Generate the request_id for the new flower request
        $requestId = 'REQ-' . strtoupper(Str::random(12));

        // Create the flower request and store the generated request_id
        $flowerRequest = FlowerRequest::create([
            'request_id' => $requestId,
            'product_id' => $request->product_id,
            'user_id' => $user->userid,
            'address_id' => $request->address_id,
            'description' => $request->description,
            'suggestion' => $request->suggestion,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        // Process flower items and create corresponding entries
        foreach ($request->flower_name as $index => $flowerName) {
            FlowerRequestItem::create([
                'flower_request_id' => $requestId,
                'flower_name' => $flowerName,
                'flower_unit' => $request->flower_unit[$index],
                'flower_quantity' => $request->flower_quantity[$index],
            ]);
        }

        $deviceTokens = UserDevice::where('user_id', $user->userid)
    ->whereNotNull('device_id')
    ->pluck('device_id')
    ->filter()
    ->toArray();

if (empty($deviceTokens)) {
    \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
}


        if (!empty($deviceTokens)) {
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $notificationService->sendBulkNotifications(
                $deviceTokens,
                'Order Created',
                'Your order has been placed. Price will be notified in few minutes.',
                ['order_id' => $flowerRequest->id]
            );
            \Log::info('Notifications sent successfully to all devices.', [
                'user_id' => $user->userid,
                'device_tokens' => $deviceTokens,
            ]);
        } else {
            \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
        }

        // Email Notification
        $flowerRequest = $flowerRequest->load([
            'address.localityDetails',
            'user',
            'flowerRequestItems',
        ]);

        $emails = [
            'soumyaranjan.puhan@33crores.com',
            'pankaj.sial@33crores.com',
            'basudha@33crores.com',
            'priya@33crores.com',
            'starleen@33crores.com',
        ];

        \Log::info('Attempting to send email to multiple recipients.', ['emails' => $emails]);
try {
    Mail::to($emails)->send(new FlowerRequestMail($flowerRequest));
    \Log::info('Email sent successfully to all recipients.');
} catch (\Exception $e) {
    \Log::error('Failed to send email.', ['error' => $e->getMessage()]);
}
        \Log::info('Email sent successfully to all recipients.');

        // Twilio WhatsApp Notification Logic
        $adminNumber = '+919776888887';
        $twilioSid = env('TWILIO_ACCOUNT_SID');
        $twilioToken = env('TWILIO_AUTH_TOKEN');
        $twilioWhatsAppNumber = env('TWILIO_WHATSAPP_NUMBER');

        $messageBody = "*New Flower Request Received*\n\n" .
            "*Request ID:* {$flowerRequest->request_id}\n" .
            "*User:* {$flowerRequest->user->mobile_number}\n" .
            "*Address:* {$flowerRequest->address->apartment_flat_plot}, " .
            "{$flowerRequest->address->localityDetails->locality_name}, " .
            "{$flowerRequest->address->city}, {$flowerRequest->address->state}, " .
            "{$flowerRequest->address->pincode}\n" .
            "*Landmark:* {$flowerRequest->address->landmark}\n" .
            "*Description:* {$flowerRequest->description}\n" .
            "*Suggestion:* {$flowerRequest->suggestion}\n" .
            "*Date:* {$flowerRequest->date}\n" .
            "*Time:* {$flowerRequest->time}\n\n" .
            "*Flower Items:*\n";

        foreach ($flowerRequest->flowerRequestItems as $item) {
            $messageBody .= "- {$item->flower_name}: {$item->flower_quantity} {$item->flower_unit}\n";
        }

        try {
            $twilioClient = new \Twilio\Rest\Client($twilioSid, $twilioToken);
            $twilioClient->messages->create(
                "whatsapp:{$adminNumber}",
                [
                    'from' => $twilioWhatsAppNumber,
                    'body' => $messageBody,
                ]
            );
            \Log::info('WhatsApp notification sent successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notification.', ['error' => $e->getMessage()]);
        }
        

        \Log::info('WhatsApp notification sent successfully.', ['admin_number' => $adminNumber]);

        // Commit the transaction
        DB::commit();

        // Return a successful response with flower request details
        return response()->json([
            'status' => 200,
            'message' => 'Flower request created successfully',
            'data' => $flowerRequest,
        ], 200);

    } catch (\Exception $e) {
        // Rollback the transaction on failure
        DB::rollBack();

        // Log the error for debugging
        \Log::error('Failed to create flower request.', ['error' => $e->getMessage()]);

        // Return an error response
        return response()->json([
            'status' => 500,
            'message' => 'Failed to create flower request',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function ordersList()
    {
        try {
            // Get the authenticated user's ID
            $userId = Auth::guard('sanctum')->user()->userid;


            $subscriptionsOrder = Subscription::where('user_id', $userId)
            ->with([
                'order', // Associated orders
                'flowerProducts', // Product info related to subscriptions
                'pauseResumeLog',
                'flowerPayments',
                'users',
                'order.address', // Include associated address through the order

            ])
            ->orderBy('created_at', 'desc') // Order by latest subscription
            ->get();

        // Add image URL to flower products if they exist
        $subscriptionsOrder = $subscriptionsOrder->map(function ($order) {
            if ($order->flowerProducts) {
                $order->flowerProducts->product_image_url = $order->flowerProducts->product_image;
            }
            return $order;
        });


            // Fetch related orders for the authenticated user (orders with request_id)
            $requestedOrders = FlowerRequest::where('user_id', $userId)
            ->with([
                'order' => function ($query) {
                    $query->with('flowerPayments');
                },
                'flowerProduct',
                'user',
                'address.localityDetails',
                'flowerRequestItems' 
            ])
            ->orderBy('id', 'desc')
            ->get()
            // ->orderBy('id', 'desc')
            ->map(function ($request) {
                // Check if 'order' relationship exists and has 'flower_payments'
                if ($request->order) {
                    // If 'flower_payments' is empty, set it to an empty object
                    if ($request->order->flowerPayments->isEmpty()) {
                        $request->order->flower_payments = (object)[];
                    } else {
                        // Otherwise, assign the 'flowerPayments' collection to 'flower_payments'
                        $request->order->flower_payments = $request->order->flowerPayments;
                    }
                    // Remove the 'flowerPayments' property to avoid duplication
                    unset($request->order->flowerPayments);
                }
        
                // Map product image URL
                if ($request->flowerProduct) {
                    // Generate full URL for the product image
                    $request->flowerProduct->product_image_url =  $request->flowerProduct->product_image;
                }
        
                return $request;
            });
        
            // Combine both into a single response
            return response()->json([
                'success' => 200,
                'data' => [
                    'subscriptions_order' => $subscriptionsOrder,
                    'requested_orders' => $requestedOrders,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to fetch orders list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders list.',
            ], 500);
        }
    }

    public function pause(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->where('status','active')->firstOrFail();

        // Calculate pause start and end dates
        $pauseStartDate = Carbon::parse($request->pause_start_date);
        $pauseEndDate = Carbon::parse($request->pause_end_date);
        $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates

        // Get the most recent new_end_date or default to the original end_date
        $lastNewEndDate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
            ->orderBy('id', 'desc')
            ->value('new_end_date');

        // Use the most recent new_end_date for recalculating the new end date
        $currentEndDate = $lastNewEndDate ? Carbon::parse($lastNewEndDate) : Carbon::parse($subscription->end_date);

        // Calculate the new end date by adding paused days
        $newEndDate = $currentEndDate->addDays($pausedDays);

        // Check if today matches the pause start date
        $today = Carbon::today();
        if ($today->eq($pauseStartDate)) {
            // Update the subscription status and new date field if today is the pause start date
            $subscription->status = 'paused';
            $subscription->is_active = true;
        }

        // Always update pause dates and new end date
        $subscription->pause_start_date = $pauseStartDate;
        $subscription->pause_end_date = $pauseEndDate;
        $subscription->new_date = $newEndDate; // Update with recalculated end date

        // Save the changes
        $subscription->save();

        // Log the pause action
        SubscriptionPauseResumeLog::create([
            'subscription_id' => $subscription->subscription_id,
            'order_id' => $order_id,
            'action' => 'paused',
            'pause_start_date' => $pauseStartDate,
            'pause_end_date' => $pauseEndDate,
            'paused_days' => $pausedDays,
            'new_end_date' => $newEndDate,
        ]);

        // Log the creation of the pause resume log
        Log::info('Pause resume log created successfully');

        return response()->json([
            'success' => 200,
            'message' => 'Subscription pause scheduled successfully.',
            'subscription' => $subscription
        ], 200);
    } catch (\Exception $e) {
        // Log any errors that occur during the process
        Log::error('Error pausing subscription', [
            'order_id' => $order_id,
            'error_message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => 500,
            'message' => 'An error occurred while scheduling the subscription pause.',
            'error' => $e->getMessage()
        ], 500);
    }
}

 
    public function markPaymentApi(Request $request, $id)
    {
        try {
            // Initialize Razorpay API
            $razorpayApi = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $paymentId = $request->payment_id;
    
            try {
                // Fetch the payment details from Razorpay
                $payment = $razorpayApi->payment->fetch($paymentId);
                \Log::info('Fetched payment details', ['payment_id' => $paymentId, 'payment_status' => $payment->status]);
    
                // Check if the payment is captured
                if ($payment->status !== 'captured') {
                    // Attempt to capture the payment if it is authorized
                    if ($payment->status === 'authorized') {
                        $capture = $razorpayApi->payment->fetch($paymentId)->capture(['amount' => $payment->amount]);
                        \Log::info('Payment captured manually', ['payment_id' => $paymentId, 'captured_status' => $capture->status]);
                    } else {
                        \Log::error('Payment not captured', ['payment_id' => $paymentId]);
                        return response()->json(['message' => 'Payment was not successful, Your payment will be refunded within 7 days.'], 400);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch payment status', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to fetch payment status'], 500);
            }
    
            // Find the order by flower request ID
            $order = Order::where('request_id', $id)->firstOrFail();
    
            // Create a new flower payment entry
            FlowerPayment::create([
                'order_id' => $order->order_id,
                'payment_id' => $paymentId, // Set payment ID from Razorpay
                'user_id' => $order->user_id,
                'payment_method' => 'Razorpay',
                'paid_amount' => $order->total_price,
                'payment_status' => 'paid',
            ]);
    
            // Update the status of the FlowerRequest to "paid"
            $flowerRequest = FlowerRequest::where('request_id', $id)->firstOrFail();
    
            if ($flowerRequest->status === 'approved') {
                $flowerRequest->status = 'paid';
                $flowerRequest->save();
            }
    
            // Send notification to the user
            $deviceTokens = UserDevice::where('user_id', $order->user_id)->whereNotNull('device_id')->pluck('device_id')->toArray();
    
            if (!empty($deviceTokens)) {
                $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                $notificationService->sendBulkNotifications(
                    $deviceTokens,
                    'Payment Successful',
                    'Payment is successfully done. Your order will be delivered on time.',
                    ['order_id' => $order->order_id]
                );
    
                \Log::info('Notification sent successfully to all devices.', [
                    'user_id' => $order->user_id,
                    'device_tokens' => $deviceTokens,
                ]);
            } else {
                \Log::warning('No device tokens found for user.', ['user_id' => $order->user_id]);
            }
    
            return response()->json([
                'status' => 200,
                'message' => 'Payment marked as paid'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to mark payment as paid.', ['error' => $e->getMessage()]);
    
            return response()->json([
                'status' => 500,
                'message' => 'Failed to mark payment as paid'
            ], 500);
        }
    }
    
    
public function resume(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->where('status','active')->firstOrFail();

        // Validate that the subscription is currently paused
        if ($subscription->status !== 'paused') {
            return response()->json([
                'success' => 400,
                'message' => 'Subscription is not in a paused state.'
            ], 400);
        }

        // Log the resume attempt
        Log::info('Resuming subscription', [
            'order_id' => $order_id,
            'user_id' => $subscription->user_id,
            'pause_start_date' => $subscription->pause_start_date,
            'pause_end_date' => $subscription->pause_end_date,
        ]);

        // Parse the dates
        $resumeDate = Carbon::parse($request->resume_date);
        $pauseStartDate = Carbon::parse($subscription->pause_start_date);
        $pauseEndDate = Carbon::parse($subscription->pause_end_date);
        $currentEndDate = $subscription->new_date ? Carbon::parse($subscription->new_date) : Carbon::parse($subscription->end_date);

        // Ensure the resume date is within the pause period
        if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
            return response()->json([
                'success' => 400,
                'message' => 'Resume date must be within the pause period.'
            ], 400);
        }

        // Calculate the days actually paused until the resume date
        $actualPausedDays = $resumeDate->diffInDays($pauseStartDate); // Include start date

        // Calculate total planned paused days
        $totalPausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1;

        // Calculate the remaining paused days to adjust if resuming early
        $remainingPausedDays = $totalPausedDays - $actualPausedDays;

        // Adjust the new end date by subtracting the remaining paused days if necessary
        if ($remainingPausedDays > 0) {
            $newEndDate = $currentEndDate->subDays($actualPausedDays);
        } else {
            $newEndDate = $currentEndDate;
        }

        // Update the subscription status and clear pause dates
        $subscription->status = 'active';
        $subscription->pause_start_date = null;
        $subscription->pause_end_date = null;
        $subscription->new_date = $newEndDate;
        $subscription->save();

        // Log the resume action 
        SubscriptionPauseResumeLog::create([
            'subscription_id' => $subscription->subscription_id,
            'order_id' => $order_id,
            'action' => 'resumed',
            'resume_date' => $resumeDate,
            'pause_start_date' => $pauseStartDate,
            'new_end_date' => $newEndDate,
            'paused_days' => $actualPausedDays,
        ]);

        // Log the successful resume
        Log::info('Subscription resumed successfully', [
            'order_id' => $order_id,
            'new_end_date' => $newEndDate,
        ]);

        return response()->json([
            'success' => 200,
            'message' => 'Subscription resumed successfully.',
            'subscription' => $subscription
        ], 200);
    } catch (\Exception $e) {
        // Log any errors that occur during the process
        Log::error('Error resuming subscription', [
            'order_id' => $order_id,
            'error_message' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => 500,
            'message' => 'An error occurred while resuming the subscription.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
