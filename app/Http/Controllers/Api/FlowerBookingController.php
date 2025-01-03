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
// use Illuminate\Support\Facades\Log;
use App\Models\FlowerPayment;
use App\Models\FlowerRequestItem;
use App\Services\NotificationService;
use App\Models\UserDevice;
use Razorpay\Api\Api;
use Twilio\Rest\Client;
class FlowerBookingController extends Controller
{
    //
   
// old
    // public function purchaseSubscription(Request $request)
    // {
    //     \Log::info('Purchase subscription called', ['request' => $request->all()]);
    
    //     $productId = $request->product_id; 
    //     $user = Auth::guard('sanctum')->user();
    
    //     if (!$user) {
    //         \Log::error('User not authenticated');
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }
    
    //     $orderId = 'ORD-' . strtoupper(Str::random(12));
    //     $addressId = $request->address_id;
    //     $suggestion = $request->suggestion;
    
    //     \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $productId, 'user_id' => $user->userid, 'address_id' => $addressId]);
    
    //     try {
    //         $order = Order::create([
    //             'order_id' => $orderId,
    //             'product_id' => $productId, 
    //             'user_id' => $user->userid,
    //             'quantity' => 1,
    //             'total_price' => $request->paid_amount,
    //             'address_id' => $addressId,
    //             'suggestion' => $suggestion,
    //         ]);
    //         \Log::info('Order created successfully', ['order' => $order]);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to create order', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to create order'], 500);
    //     }
    
    //     $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
    //     $duration = $request->duration;
    
    //     if ($duration == 1) {
    //         $endDate = $startDate->copy()->addDays(29);
    //     } else if ($duration == 3) {
    //         $endDate = $startDate->copy()->addDays(89);
    //     } else if ($duration == 6) {
    //         $endDate = $startDate->copy()->addDays(179);
    //     } else {
    //         \Log::error('Invalid subscription duration', ['duration' => $duration]);
    //         return response()->json(['message' => 'Invalid subscription duration'], 400);
    //     }
    
    //     \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $productId, 'start_date' => $startDate, 'end_date' => $endDate]);
    
    //     $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
    //     $today = now()->format('Y-m-d');
    //     $status = ($startDate->format('Y-m-d') === $today) ? 'active' : 'pending';
    
    //     try {
    //         Subscription::create([
    //             'subscription_id' => $subscriptionId,
    //             'user_id' => $user->userid,
    //             'order_id' => $orderId,
    //             'product_id' => $productId,
    //             'start_date' => $startDate,
    //             'end_date' => $endDate,
    //             'is_active' => true,
    //             'status' => $status,
    //         ]);
    //         \Log::info('Subscription created successfully');
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to create subscription'], 500);
    //     }
    
    //     try {
    //         FlowerPayment::create([
    //             'order_id' => $orderId,
    //             'payment_id' => $request->payment_id,
    //             'user_id' => $user->userid,
    //             'payment_method' => "Razorpay",
    //             'paid_amount' => $request->paid_amount,
    //             'payment_status' => "paid",
    //         ]);
    //         \Log::info('Payment recorded successfully');
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to record payment', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to record payment'], 500);
    //     }
    
    //     $deviceTokens = UserDevice::where('user_id', $user->userid)->whereNotNull('device_id')->pluck('device_id')->toArray();
    
    //     if (!empty($deviceTokens)) {
    //         $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
    //         $notificationService->sendBulkNotifications(
    //             $deviceTokens,
    //             'Order Received',
    //             'Your subscription has been placed successfully.',
    //             ['subscription_id' => $subscriptionId]
    //         );
    
    //         \Log::info('Notification sent successfully to all devices.', [
    //             'user_id' => $user->userid,
    //             'device_tokens' => $deviceTokens,
    //         ]);
    //     } else {
    //         \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
    //     }
    
    //     $emails = [
    //         'bhabana.samantara@33crores.com',
    //         'pankaj.sial@33crores.com',
    //         'basudha@33crores.com',
    //         'priya@33crores.com',
    //         'starleen@33crores.com'
    //     ];
    
    //     try {
    //         Mail::to($emails)->send(new SubscriptionConfirmationMail($order));
    //         \Log::info('Order details email sent successfully', ['emails' => $emails]);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send order details email', ['error' => $e->getMessage()]);
    //     }
    
    //     return response()->json([
    //         'message' => 'Subscription activated successfully',
    //         'end_date' => $endDate,
    //         'order_id' => $orderId,
    //     ]);
    // }
    
    // razoy pay captured
  

// public function purchaseSubscription(Request $request)
// {
//     \Log::info('Purchase subscription called', ['request' => $request->all()]);

//     $productId = $request->product_id;
//     $user = Auth::guard('sanctum')->user();

//     if (!$user) {
//         \Log::error('User not authenticated');
//         return response()->json(['message' => 'Unauthorized'], 401);
//     }

//     $orderId = $request->order_id;
//     $addressId = $request->address_id;
//     $suggestion = $request->suggestion;

//     \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $productId, 'user_id' => $user->userid, 'address_id' => $addressId]);

//     try {
//         $order = Order::create([
//             'order_id' => $orderId,
//             'product_id' => $productId,
//             'user_id' => $user->userid,
//             'quantity' => 1,
//             'total_price' => $request->paid_amount,
//             'address_id' => $addressId,
//             'suggestion' => $suggestion,
//         ]);
//         \Log::info('Order created successfully', ['order' => $order]);
//     } catch (\Exception $e) {
//         \Log::error('Failed to create order', ['error' => $e->getMessage()]);
//         return response()->json(['message' => 'Failed to create order'], 500);
//     }

//     // Initialize Razorpay API
//     $razorpayApi = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
//     $paymentId = $request->payment_id;

//     try {
//         // Fetch the payment details from Razorpay
//         $payment = $razorpayApi->payment->fetch($paymentId);
//         \Log::info('Fetched payment details', ['payment_id' => $paymentId, 'payment_status' => $payment->status]);

//         // Check if the payment is captured
//         if ($payment->status !== 'captured') {
//             \Log::error('Payment not captured', ['payment_id' => $paymentId]);
//             return response()->json(['message' => 'Payment was not successfull, Your payment will be refunded with in 7 days.'], 400);
//         }
//     } catch (\Exception $e) {
//         \Log::error('Failed to fetch payment status', ['error' => $e->getMessage()]);
//         return response()->json(['message' => 'Failed to fetch payment status'], 500);
//     }

//     // Process subscription logic
//     $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
//     $duration = $request->duration;

//     if ($duration == 1) {
//         $endDate = $startDate->copy()->addDays(29);
//     } else if ($duration == 3) {
//         $endDate = $startDate->copy()->addDays(89);
//     } else if ($duration == 6) {
//         $endDate = $startDate->copy()->addDays(179);
//     } else {
//         \Log::error('Invalid subscription duration', ['duration' => $duration]);
//         return response()->json(['message' => 'Invalid subscription duration'], 400);
//     }

//     \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $productId, 'start_date' => $startDate, 'end_date' => $endDate]);

//     $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
//     $today = now()->format('Y-m-d');
//     $status = ($startDate->format('Y-m-d') === $today) ? 'active' : 'pending';

//     try {
//         Subscription::create([
//             'subscription_id' => $subscriptionId,
//             'user_id' => $user->userid,
//             'order_id' => $orderId,
//             'product_id' => $productId,
//             'start_date' => $startDate,
//             'end_date' => $endDate,
//             'is_active' => true,
//             'status' => $status,
//         ]);
//         \Log::info('Subscription created successfully');
//     } catch (\Exception $e) {
//         \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
//         return response()->json(['message' => 'Failed to create subscription'], 500);
//     }

//     try {
//         FlowerPayment::create([
//             'order_id' => $orderId,
//             'payment_id' => $paymentId,
//             'user_id' => $user->userid,
//             'payment_method' => "Razorpay",
//             'paid_amount' => $request->paid_amount,
//             'payment_status' => "paid",
//         ]);
//         \Log::info('Payment recorded successfully');
//     } catch (\Exception $e) {
//         \Log::error('Failed to record payment', ['error' => $e->getMessage()]);
//         return response()->json(['message' => 'Failed to record payment'], 500);
//     }

//     $deviceTokens = UserDevice::where('user_id', $user->userid)->whereNotNull('device_id')->pluck('device_id')->toArray();

//     if (!empty($deviceTokens)) {
//         $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
//         $notificationService->sendBulkNotifications(
//             $deviceTokens,
//             'Order Received',
//             'Your subscription has been placed successfully.',
//             ['subscription_id' => $subscriptionId]
//         );

//         \Log::info('Notification sent successfully to all devices.', [
//             'user_id' => $user->userid,
//             'device_tokens' => $deviceTokens,
//         ]);
//     } else {
//         \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
//     }

//     $emails = [
//         'bhabana.samantara@33crores.com',
//         'pankaj.sial@33crores.com',
//         'basudha@33crores.com',
//         'priya@33crores.com',
//         'starleen@33crores.com'
//     ];

//     try {
//         Mail::to($emails)->send(new SubscriptionConfirmationMail($order));
//         \Log::info('Order details email sent successfully', ['emails' => $emails]);
//     } catch (\Exception $e) {
//         \Log::error('Failed to send order details email', ['error' => $e->getMessage()]);
//     }

//     return response()->json([
//         'message' => 'Subscription activated successfully',
//         'end_date' => $endDate,
//         'order_id' => $orderId,
//     ]);
// }

public function purchaseSubscription(Request $request)
{
    \Log::info('Purchase subscription called', ['request' => $request->all()]);

    $productId = $request->product_id;
    $user = Auth::guard('sanctum')->user();

    if (!$user) {
        \Log::error('User not authenticated');
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $orderId = 'ORD-' . strtoupper(Str::random(12));
    $addressId = $request->address_id;
    $suggestion = $request->suggestion;

    \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $productId, 'user_id' => $user->userid, 'address_id' => $addressId]);

    try {
        $order = Order::create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'user_id' => $user->userid,
            'quantity' => 1,
            'total_price' => $request->paid_amount,
            'address_id' => $addressId,
            'suggestion' => $suggestion,
        ]);
        \Log::info('Order created successfully', ['order' => $order]);
    } catch (\Exception $e) {
        \Log::error('Failed to create order', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to create order'], 500);
    }

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

    // Process subscription logic
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
    $duration = $request->duration;

    if ($duration == 1) {
        $endDate = $startDate->copy()->addDays(29);
    } else if ($duration == 3) {
        $endDate = $startDate->copy()->addDays(89);
    } else if ($duration == 6) {
        $endDate = $startDate->copy()->addDays(179);
    } else {
        \Log::error('Invalid subscription duration', ['duration' => $duration]);
        return response()->json(['message' => 'Invalid subscription duration'], 400);
    }

    \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $productId, 'start_date' => $startDate, 'end_date' => $endDate]);

    $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
    $today = now()->format('Y-m-d');
    $status = ($startDate->format('Y-m-d') === $today) ? 'active' : 'pending';

    try {
        Subscription::create([
            'subscription_id' => $subscriptionId,
            'user_id' => $user->userid,
            'order_id' => $orderId,
            'product_id' => $productId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
            'status' => $status,
        ]);
        \Log::info('Subscription created successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to create subscription'], 500);
    }

    try {
        FlowerPayment::create([
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'user_id' => $user->userid,
            'payment_method' => "Razorpay",
            'paid_amount' => $request->paid_amount,
            'payment_status' => "paid",
        ]);
        \Log::info('Payment recorded successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to record payment', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to record payment'], 500);
    }

    $deviceTokens = UserDevice::where('user_id', $user->userid)->whereNotNull('device_id')->pluck('device_id')->toArray();

    if (!empty($deviceTokens)) {
        $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
        $notificationService->sendBulkNotifications(
            $deviceTokens,
            'Order Received',
            'Your subscription has been placed successfully.',
            ['subscription_id' => $subscriptionId]
        );

        \Log::info('Notification sent successfully to all devices.', [
            'user_id' => $user->userid,
            'device_tokens' => $deviceTokens,
        ]);
    } else {
        \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
    }

    $emails = [
        'bhabana.samantara@33crores.com',
        'pankaj.sial@33crores.com',
        'basudha@33crores.com',
        'priya@33crores.com',
        'starleen@33crores.com'
    ];

    try {
        Mail::to($emails)->send(new SubscriptionConfirmationMail($order));
        \Log::info('Order details email sent successfully', ['emails' => $emails]);
    } catch (\Exception $e) {
        \Log::error('Failed to send order details email', ['error' => $e->getMessage()]);
    }

    return response()->json([
        'message' => 'Subscription activated successfully',
        'end_date' => $endDate,
        'order_id' => $orderId,
    ]);
}

    // public function storerequest(Request $request)
    // {
    //     try {
    //         // Get the authenticated user
    //         $user = Auth::guard('sanctum')->user();
            
    //         // Generate the request_id
    //         $requestId = 'REQ-' . strtoupper(Str::random(12));
    
    //         // Create the flower request and store the request_id
    //         $flowerRequest = FlowerRequest::create([
    //             'request_id' => $requestId,  // Store request_id in FlowerRequest
    //             'product_id' => $request->product_id,
    //             'user_id' => $user->userid,
    //             'address_id' => $request->address_id,
    //             'description' => $request->description,
    //             'suggestion' => $request->suggestion,
    //             'date' => $request->date,
    //             'time' => $request->time,
    //             'status' => 'pending'
    //         ]);
    
    //         // Loop through flower names, units, and quantities to create FlowerRequestItem entries
    //         foreach ($request->flower_name as $index => $flowerName) {
    //             // Create a FlowerRequestItem with flower_request_id set to the generated request_id
    //             FlowerRequestItem::create([
    //                 'flower_request_id' => $requestId,  // Use the generated request_id
    //                 'flower_name' => $flowerName,
    //                 'flower_unit' => $request->flower_unit[$index],
    //                 'flower_quantity' => $request->flower_quantity[$index],
    //             ]);
    //         }
    
    //         // Eager load the flower_request_items relationship
    //         // $flowerRequest = $flowerRequest->load('flowerRequestItems');
    //         $flowerRequest = $flowerRequest->load([
    //             'order',
    //             'address.localityDetails', // Load localityDetails as a nested relationship
    //             'user',
    //             'flowerProduct',
    //             'flowerRequestItems',
    //         ]);
    
    //         Notification::create([
    //             'type' => 'order',
    //             'data' => [
    //                 'message' => 'A new order has been placed!',
    //                 'order_id' => $flowerRequest->id,
    //                 'user_name' => $flowerRequest->user->name, // Assuming the order has a user relation
    //             ],
    //             'is_read' => false, // Mark as unread
    //         ]);
    
    //         try {
    //             // Log the alert for a new order
    //             Log::info('New order created successfully.', ['request_id' => $requestId]);
            
    //             // Array of email addresses to send the email
    //             $emails = [
    //                 'bhabana.samantara@33crores.com',
    //                 'pankaj.sial@33crores.com',
    //                 'basudha@33crores.com',
    //                 'priya@33crores.com',
    //                 'starleen@33crores.com',
    //             ];
            
    //             // Log before attempting to send the email
    //             Log::info('Attempting to send email to multiple recipients.', ['emails' => $emails]);
            
    //             // Send the email to all recipients
    //             Mail::to($emails)->send(new FlowerRequestMail($flowerRequest));
            
    //             // Log success
    //             Log::info('Email sent successfully to multiple recipients.', [
    //                 'request_id' => $requestId,
    //                 'user_id' => $user->userid,
    //             ]);
            
    //         } catch (\Exception $e) {
    //             // Log the error with details
    //             Log::error('Failed to send email.', [
    //                 'request_id' => $requestId,
    //                 'user_id' => $user->userid ?? 'N/A',
    //                 'error_message' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString(),
    //             ]);
    //         }
            
            
    //         // Prepare response data including flower details in FlowerRequest
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Flower request created successfully',
    //             'data' => $flowerRequest,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // Log the error and return a response
    //         Log::error('Failed to create flower request.', ['error' => $e->getMessage()]);
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Failed to create flower request',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // fcm integrate notification
    
    
    public function storerequest(Request $request)
{
    try {
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();

        // Generate the request_id for the new flower request
        $requestId = 'REQ-' . strtoupper(Str::random(12));

        // Create the flower request and store the generated request_id
        $flowerRequest = FlowerRequest::create([
            'request_id' => $requestId, // Store request_id in FlowerRequest
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
                'flower_request_id' => $requestId, // Use the generated request_id
                'flower_name' => $flowerName,
                'flower_unit' => $request->flower_unit[$index],
                'flower_quantity' => $request->flower_quantity[$index],
            ]);
        }

        // Firebase Notification: Notify the user via registered device tokens
        $deviceTokens = UserDevice::where('user_id', $user->userid)->whereNotNull('device_id')->pluck('device_id')->toArray();

        if (!empty($deviceTokens)) {
            // Initialize the notification service with Firebase credentials
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));

            // Send a bulk notification to all registered device tokens
            $notificationService->sendBulkNotifications(
                $deviceTokens,
                'Order Created',
                'Your order has been placed. Price will be notified in few minutes.',
                ['order_id' => $flowerRequest->id] // Additional data payload
            );

            // Log successful notifications
            \Log::info('Notifications sent successfully to all devices.', [
                'user_id' => $user->userid,
                'device_tokens' => $deviceTokens,
            ]);
        } else {
            // Log a warning if no device tokens are found
            \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
        }

        // Email Notification: Send details to multiple recipients
        $flowerRequest = $flowerRequest->load([
            'address.localityDetails',
            'user',
            'flowerRequestItems',
        ]);

        $emails = [
            'bhabana.samantara@33crores.com',
            'pankaj.sial@33crores.com',
            'basudha@33crores.com',
            'priya@33crores.com',
            'starleen@33crores.com',
        ];

        // Log before sending the email
        \Log::info('Attempting to send email to multiple recipients.', ['emails' => $emails]);

        // Send the email
        Mail::to($emails)->send(new FlowerRequestMail($flowerRequest));

        \Log::info('Email sent successfully to all recipients.');

         // Twilio WhatsApp Notification: Notify admin with request details
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
     
             $twilioClient = new \Twilio\Rest\Client($twilioSid, $twilioToken);
             $twilioClient->messages->create(
                 "whatsapp:{$adminNumber}",
                 [
                     'from' => $twilioWhatsAppNumber,
                     'body' => $messageBody,
                 ]
             );
     
 
         \Log::info('WhatsApp notification sent successfully.', ['admin_number' => $adminNumber]);

        // Return a successful response with flower request details
        return response()->json([
            'status' => 200,
            'message' => 'Flower request created successfully',
            'data' => $flowerRequest,
        ], 200);

    } catch (\Exception $e) {
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

            // Fetch standalone orders for the authenticated user (orders without request_id)
            $subscriptionsOrder = Order::whereNull('request_id')
            ->where('user_id', $userId)
            ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails','pauseResumeLogs'])
            ->orderBy('id', 'desc')
            ->get();
        
        // Map to add the product_image_url to each order's flowerProduct
        $subscriptionsOrder = $subscriptionsOrder->map(function ($order) {
            if ($order->flowerProduct) {
                // Ensure flowerProduct exists before accessing product_image
                $order->flowerProduct->product_image_url = $order->flowerProduct->product_image; // Generate full URL for the photo
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
                    $request->flowerProduct->product_image_url = asset('storage/' . $request->flowerProduct->product_image);
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

    
//     public function ordersList()
// {
//     try {
//         // Fetch the user ID
//         $userId = Auth::guard('sanctum')->user()->userid;

//         // Get both subscription and request-based orders in a single query
//         $mergedOrdersObject = $this->getUserOrders($userId);

//         // Return response with the merged orders under the 'date' key
//         return response()->json([
//             'success' => 200,
//             'data' => $mergedOrdersObject
//         ]);
//     } catch (\Exception $e) {
//         // Handle errors gracefully
//         return response()->json([
//             'error' => 'Something went wrong',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }
// public function getUserOrders($userId)
// {
//     // Fetch subscription orders with relations
//     $subscriptionsOrder = Order::whereNull('request_id')
//         ->where('user_id', $userId)
//         ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])
//         ->orderBy('id', 'desc')
//         ->get();
//     $subscriptionsOrder->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });
//     // Fetch request-based orders with relations
//     $requestedOrders = FlowerRequest::where('user_id', $userId)
//         ->with(['order', 'flowerProduct', 'user', 'address'])
//         ->orderBy('id', 'desc')
//         ->get();
//         $requestedOrders->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });

//     // Merge both sets of orders and reset the keys to ensure the response is an array
//     return  $requestedOrders->merge($subscriptionsOrder)->sortByDesc('id')->values()->toArray();
    
// }

// private function getUserOrders($userId)
// {
//     // Fetch subscription orders with relations
//     $subscriptionsOrder = Order::whereNull('request_id')
//         ->where('user_id', $userId)
//         ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])
//         ->orderBy('id', 'desc')
//         ->get();

//     // Loop through the subscription orders and generate the full URL for flowerProduct->product_image
//     $subscriptionsOrder->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });

//     // Fetch request-based orders with relations
//     $requestedOrders = FlowerRequest::where('user_id', $userId)
//         ->with(['order', 'flowerProduct', 'user', 'address'])
//         ->orderBy('id', 'desc')
//         ->get();

//     // Loop through the request-based orders and generate the full URL for flowerProduct->product_image
//     $requestedOrders->transform(function ($order) {
//         if ($order->flowerProduct && $order->flowerProduct->product_image) {
//             // Generate the full URL for product_image
//             $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
//         }
//         return $order;
//     });

//     // Merge both sets of orders
//     return $subscriptionsOrder->merge($requestedOrders)->sortByDesc('id');
// }


  
     // old code
    // public function pause(Request $request, $order_id)
    // {
    //     try {
    //         // Find the subscription by order_id
    //         $subscription = Subscription::where('order_id', $order_id)->firstOrFail();
            
    //         // Log the subscription being paused
    //         Log::info('Pausing subscription', [
    //             'order_id' => $order_id,
    //             'user_id' => $subscription->user_id,
    //             'pause_start_date' => $request->pause_start_date,
    //             'pause_end_date' => $request->pause_end_date,
    //         ]);
        
    //         // Calculate the number of days to extend (include both start and end date)
    //         $pauseStartDate = Carbon::parse($request->pause_start_date);
    //         $pauseEndDate = Carbon::parse($request->pause_end_date);
    //         $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates
        
    //         // Store the new paused end date in the new_date field
    //         $newEndDate = Carbon::parse($subscription->end_date)->addDays($pausedDays);
    
    //         // Update the subscription status and dates
    //         $subscription->status = 'paused';
    //         $subscription->pause_start_date = $pauseStartDate;
    //         $subscription->pause_end_date = $pauseEndDate;
    //         $subscription->new_date = $newEndDate; // Store the new end date after pausing
    //         $subscription->is_active = true;
        
    //         // Save the changes
    //         $subscription->save();
        
    //         // Log the successful pause
    //         Log::info('Subscription paused successfully', [
    //             'order_id' => $order_id,
    //             'new_end_date' => $newEndDate,
    //         ]);
        
    //         // Log the pause action
    //         SubscriptionPauseResumeLog::create([
    //             'subscription_id' => $subscription->subscription_id,
    //             'order_id' => $order_id,
    //             'action' => 'paused',
    //             'pause_start_date' => $pauseStartDate,
    //             'pause_end_date' => $pauseEndDate,
    //             'paused_days' => $pausedDays,
    //             'new_end_date' => $subscription->new_date,

    //         ]);
        
    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Subscription paused successfully.',
    //             'subscription' => $subscription
    //         ], 200);    
    //     } catch (\Exception $e) {
    //         // Log any errors that occur during the process
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error_message' => $e->getMessage(),
    //         ]);
        
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while pausing the subscription.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    //old code
    // public function pause(Request $request, $order_id)
    // {
    //     try {
           
    //         // Find the subscription by order_id
    //         $subscription = Subscription::where('order_id', $order_id)->firstOrFail();
          
    //         // Calculate pause start and end dates
    //         $pauseStartDate = Carbon::parse($request->pause_start_date);
    //         $pauseEndDate = Carbon::parse($request->pause_end_date);
    //         $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates
    
           
    //         // Get the most recent new_end_date or default to the original end_date
    //         $lastNewEndDate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //             ->orderBy('id', 'desc')
    //             ->value('new_end_date');
    
    //         // Use the most recent new_end_date for recalculating the new end date
    //         $currentEndDate = $lastNewEndDate ? Carbon::parse($lastNewEndDate) : Carbon::parse($subscription->end_date);
    
           
    //         // Calculate the new end date by adding paused days
    //         $newEndDate = $currentEndDate->addDays($pausedDays);
    
         
    //         // Update the subscription status and new date field
    //         $subscription->status = 'paused';
    //         $subscription->pause_start_date = $pauseStartDate;
    //         $subscription->pause_end_date = $pauseEndDate;
    //         $subscription->new_date = $newEndDate; // Update with recalculated end date
    //         $subscription->is_active = true;
    
    //         // Save the changes
    //         $subscription->save();
    
    //         // Log the pause action
    //         SubscriptionPauseResumeLog::create([
    //             'subscription_id' => $subscription->subscription_id,
    //             'order_id' => $order_id,
    //             'action' => 'paused',
    //             'pause_start_date' => $pauseStartDate,
    //             'pause_end_date' => $pauseEndDate,
    //             'paused_days' => $pausedDays,
    //             'new_end_date' => $newEndDate,
    //         ]);
    
    //         // Log the creation of the pause resume log
    //         Log::info('Pause resume log created successfully');
    
    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Subscription paused successfully.',
    //             'subscription' => $subscription
    //         ], 200);    
    //     } catch (\Exception $e) {
    //         // Log any errors that occur during the process
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error_message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while pausing the subscription.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function pause(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

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
    
    
// public function resume(Request $request, $order_id)
// {
//     try {
//         // Find the subscription by order_id
//         $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

//         // Validate that the subscription is currently paused
//         if ($subscription->status !== 'paused') {
//             return response()->json([
//                 'success' => 400,
//                 'message' => 'Subscription is not in a paused state.'
//             ], 400);
//         }

//         // Log the resume attempt
//         Log::info('Resuming subscription', [
//             'order_id' => $order_id,
//             'user_id' => $subscription->user_id,
//             'pause_start_date' => $subscription->pause_start_date,
//             'pause_end_date' => $subscription->pause_end_date,
//         ]);

//         // Parse the dates
//         $resumeDate = Carbon::parse($request->resume_date);
//         $pauseStartDate = Carbon::parse($subscription->pause_start_date);
//         $startDate = Carbon::parse($subscription->end_date);

//         // Ensure the resume date is within the pause period
//         if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt(Carbon::parse($subscription->pause_end_date))) {
//             return response()->json([
//                 'success' => 400,
//                 'message' => 'Resume date must be within the pause period.'
//             ], 400);
//         }

//         // Calculate the days paused up to the resume date
//         $pausedDays = $resumeDate->diffInDays($pauseStartDate) + 1; // Include the start date

//         // Calculate the new end date
//         $newEndDate = $startDate->addDays($pausedDays);

//         // Update the subscription status and add resume_date
//         $subscription->status = 'active';
//         $subscription->pause_start_date = null;
//         $subscription->pause_end_date = null;
//         // $subscription->resume_date = $resumeDate; // Add the resume date
//         $subscription->new_date = $newEndDate;
//         $subscription->save();

//         // Log the resume action
//         SubscriptionPauseResumeLog::create([
//             'subscription_id' => $subscription->subscription_id,
//             'order_id' => $order_id,
//             'action' => 'resumed',
//             'pause_start_date' => $pauseStartDate,
//             'resume_date' => $resumeDate, // Log the resume date
//             'new_end_date' => $newEndDate,
//             'paused_days' => $pausedDays,
//         ]);

//         // Log the successful resume
//         Log::info('Subscription resumed successfully', [
//             'order_id' => $order_id,
//             'new_end_date' => $newEndDate,
//         ]);

//         return response()->json([
//             'success' => 200,
//             'message' => 'Subscription resumed successfully.',
//             'subscription' => $subscription
//         ], 200);
//     } catch (\Exception $e) {
//         // Log any errors that occur during the process
//         Log::error('Error resuming subscription', [
//             'order_id' => $order_id,
//             'error_message' => $e->getMessage(),
//         ]);

//         return response()->json([
//             'success' => 500,
//             'message' => 'An error occurred while resuming the subscription.',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }
public function resume(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

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
