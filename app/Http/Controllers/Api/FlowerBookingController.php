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
use Illuminate\Support\Facades\Validator;

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
         
            $rawItems = $request->input('items');

            // If items is a JSON string (common with form-data), try decoding
            if (is_string($rawItems)) {
                $decoded = json_decode($rawItems, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $rawItems = $decoded;
                }
            }

            $normalizedItems = [];

            if (is_array($rawItems)) {
                // New format already (items[])
                $normalizedItems = $rawItems;
            } else {
                // Try OLD format -> convert to new
                $flowerNames     = $request->input('flower_name', []);
                $flowerUnits     = $request->input('flower_unit', []);
                $flowerQuantites = $request->input('flower_quantity', []);

                // Only build if we actually have arrays with some elements
                if (is_array($flowerNames) && count($flowerNames) > 0) {
                    foreach ($flowerNames as $i => $name) {
                        if ($name === null || $name === '') continue;

                        $normalizedItems[] = [
                            'type'             => 'flower',
                            'flower_name'      => $name,
                            'flower_unit'      => $flowerUnits[$i]     ?? null,
                            'flower_quantity'  => $flowerQuantites[$i] ?? null,
                        ];
                    }
                }
            }

            // Merge back so validator sees a proper array
            $request->merge(['items' => $normalizedItems]);

            // --------------------------------
            // 2) Validate AFTER normalization
            // --------------------------------
            $validator = Validator::make($request->all(), [
                // If your product_id can be a code like "FLOW1977637", do not force integer
                'product_id'   => ['required'], // change to ['required','integer'] if it must be numeric
                'address_id'   => ['required', 'integer'],
                'description'  => ['nullable', 'string'],
                'suggestion'   => ['nullable', 'string'],
                'date'         => ['required', 'date'],
                'time'         => ['required', 'string'],

                'items'        => ['required', 'array', 'min:1'],
                'items.*.type' => ['required', 'in:flower,garland'],

                // Flower fields
                'items.*.flower_name'     => ['required_if:items.*.type,flower', 'string'],
                'items.*.flower_unit'     => ['required_if:items.*.type,flower', 'string'],
                'items.*.flower_quantity' => ['required_if:items.*.type,flower', 'numeric', 'min:1'],

                // Garland fields
                'items.*.garland_name'     => ['required_if:items.*.type,garland', 'string'],
                'items.*.garland_quantity' => ['nullable', 'numeric', 'min:1'],
                'items.*.flower_count'     => ['nullable', 'integer', 'min:1'],
                'items.*.garland_size'     => ['nullable', 'string'],
            ], [
                'items.required' => 'Please add at least one item (flower or garland).',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 422,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // ------------------------------------------------
            // 3) Create request + items inside a transaction
            // ------------------------------------------------
            DB::beginTransaction();

            $user = Auth::guard('sanctum')->user();

            $publicRequestId = 'REQ-' . strtoupper(Str::random(12));

            $flowerRequest = FlowerRequest::create([
                'request_id'  => $publicRequestId,
                'product_id'  => $request->product_id,
                'user_id'     => $user->userid,
                'address_id'  => $request->address_id,
                'description' => $request->description,
                'suggestion'  => $request->suggestion,
                'date'        => $request->date,
                'time'        => $request->time,
                'status'      => 'pending',
            ]);

            foreach ($request->input('items', []) as $item) {
                $type = $item['type'];

                $payload = [
                    'flower_request_id' => $flowerRequest->request_id,
                    'type'              => $type,
                ];

                if ($type === 'flower') {
                    $payload['flower_name']     = $item['flower_name'];
                    $payload['flower_unit']     = $item['flower_unit'];
                    // cast to number if it came as string
                    $payload['flower_quantity'] = is_numeric($item['flower_quantity']) ? (float)$item['flower_quantity'] : $item['flower_quantity'];
                } else {
                    $payload['garland_name']     = $item['garland_name'];
                    $payload['garland_quantity'] = is_numeric($item['garland_quantity']) ? (int)$item['garland_quantity'] : $item['garland_quantity'];
                    $payload['flower_count']     = is_numeric($item['flower_count']) ? (int)$item['flower_count'] : $item['flower_count'];
                    $payload['garland_size']     = $item['garland_size'];
                    // If your DB column is named 'size' instead of 'garland_size', also set:
                    // $payload['size'] = $item['garland_size'];
                }

                FlowerRequestItem::create($payload);
            }

            // -----------------------------------
            // 4) Notifications (same as before)
            // -----------------------------------
            $deviceTokens = UserDevice::where('user_id', $user->userid)
                ->whereNotNull('device_id')
                ->pluck('device_id')
                ->filter()
                ->toArray();

            if (empty($deviceTokens)) {
                \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
            } else {
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
            }

            // -----------------------------------
            // 5) Email
            // -----------------------------------
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

            // -----------------------------------
            // 6) WhatsApp (Twilio)
            // -----------------------------------
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
                "*Items:*\n";

            foreach ($flowerRequest->flowerRequestItems as $it) {
                if ($it->type === 'flower') {
                    $messageBody .= "- (Flower) {$it->flower_name}: {$it->flower_quantity} {$it->flower_unit}\n";
                } else {
                    $messageBody .= "- (Garland) {$it->garland_name}: {$it->garland_quantity} pcs, {$it->flower_count} flowers, size {$it->garland_size}\n";
                }
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

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Flower request created successfully',
                'data'    => $flowerRequest,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create flower request.', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 500,
                'message' => 'Failed to create flower request',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function ordersList(Request $request)
    {
        try {
            $authUser = Auth::guard('sanctum')->user();
            if (!$authUser) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $userId = $authUser->userid;

            // SUBSCRIPTIONS
            $subscriptionsOrder = Subscription::where('user_id', $userId)
                ->with([
                    'order.flowerPayments',
                    'order.address.localityDetails',
                    'flowerProducts',   // make sure this relation exists on Subscription
                    'pauseResumeLog',
                    'users',
                ])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($sub) {
                    if ($sub->flowerProduct) {
                        $sub->flowerProduct->product_image_url = $sub->flowerProduct->product_image;
                    }
                    if ($sub->order) {
                        $payments = $sub->order->flowerPayments ?? collect();
                        $sub->order->flower_payments = $payments->isEmpty() ? (object)[] : $payments;
                        unset($sub->order->flowerPayments);
                    }
                    return $sub;
                });

            // ONE-OFF REQUESTS
            $requestedOrders = FlowerRequest::where('user_id', $userId)
                ->with([
                    'order.flowerPayments',
                    'flowerProduct',
                    'user',
                    'address.localityDetails',
                    // NOTE: no custom select here — avoids selecting a non-existent 'size' column
                    'flowerRequestItems',
                ])
                ->orderByDesc('id')
                ->get()
                ->map(function ($requestRow) {
                    // normalize order -> flower_payments
                    if ($requestRow->order) {
                        $payments = $requestRow->order->flowerPayments ?? collect();
                        $requestRow->order->flower_payments = $payments->isEmpty() ? (object)[] : $payments;
                        unset($requestRow->order->flowerPayments);
                    }

                    // product image url
                    if ($requestRow->flowerProduct) {
                        $requestRow->flowerProduct->product_image_url = $requestRow->flowerProduct->product_image;
                    }

                    // GARLAND DETAILS
                    // $garlandItems = $requestRow->flowerRequestItems
                    //     ->where('type', 'garland')
                    //     ->values();

                    // $requestRow->garland_items = $garlandItems->map(function ($item) {
                    //     return [
                    //         'id'               => $item->id,
                    //         'garland_name'     => $item->garland_name,
                    //         'garland_quantity' => (int) $item->garland_quantity,
                    //         'garland_size'     => $item->garland_size,
                    //         'flower_count'     => (int) $item->flower_count,
                    //         'created_at'       => $item->created_at,
                    //         'updated_at'       => $item->updated_at,
                    //     ];
                    // });

                    // $requestRow->garland_summary = [
                    //     'items'              => $garlandItems->count(),
                    //     'total_quantity'     => (int) $garlandItems->sum('garland_quantity'),
                    //     'total_flower_count' => (int) $garlandItems->sum('flower_count'),
                    // ];

                    return $requestRow;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'subscriptions_order' => $subscriptionsOrder,
                    'requested_orders'    => $requestedOrders,
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Failed to fetch orders list', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders list.',
                'error'   => app()->environment('local') ? $e->getMessage() : null,
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
    
    public function pause(Request $request, $order_id)
    {
        // 1) Validate input (inclusive window)
        $request->validate([
            'pause_start_date' => ['required','date'],
            'pause_end_date'   => ['required','date'],
        ]);

        $pauseStartDate = Carbon::parse($request->pause_start_date)->startOfDay();
        $pauseEndDate   = Carbon::parse($request->pause_end_date)->startOfDay();

        if ($pauseEndDate->lt($pauseStartDate)) {
            return response()->json([
                'success' => 422,
                'message' => 'Pause end date must be on/after the start date.',
            ], 422);
        }

        // Inclusive day count
        $plannedPausedDays = $pauseStartDate->diffInDays($pauseEndDate) + 1;

        try {
            return DB::transaction(function () use ($order_id, $pauseStartDate, $pauseEndDate, $plannedPausedDays) {

                // 2) Lock subscription row
                $subscription = Subscription::where('order_id', $order_id)
                    ->whereIn('status', ['active', 'paused'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // 3) The "current" paused cycle (if we are already paused)
                $existingPauseLog = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                    ->where('order_id', $order_id)
                    ->where('action', 'paused')
                    ->latest('id')
                    ->first();
                
                $overlapQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                    ->where('order_id', $order_id)
                    ->where('action', 'paused')
                    ->when($subscription->status === 'paused' && $existingPauseLog, function ($q) use ($existingPauseLog) {
                        $q->where('id', '!=', $existingPauseLog->id);
                    })
                    ->where(function ($q) use ($pauseStartDate, $pauseEndDate) {
                        $q->whereBetween('pause_start_date', [$pauseStartDate, $pauseEndDate])
                        ->orWhereBetween('pause_end_date',   [$pauseStartDate, $pauseEndDate])
                        ->orWhere(function ($q2) use ($pauseStartDate, $pauseEndDate) {
                            $q2->where('pause_start_date', '<=', $pauseStartDate)
                                ->where('pause_end_date',   '>=', $pauseEndDate);
                        });
                    });

                if ($overlapQuery->exists()) {
                    return response()->json([
                        'success' => 422,
                        'message' => 'This pause window overlaps with another pause request for the same subscription.',
                    ], 422);
                }

                // 5) Determine base end date BEFORE applying this pause
                // Use effective end (COALESCE(new_date, end_date))
                $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();

                // If we are currently paused and editing that same paused cycle,
                // undo the previous extension first to avoid double counting.
                $baseEnd = clone $effectiveEnd;
                if ($subscription->status === 'paused' && $existingPauseLog) {
                    $prevPausedDays = (int) ($existingPauseLog->paused_days ?? 0);
                    if ($prevPausedDays > 0) {
                        $baseEnd = (clone $effectiveEnd)->subDays($prevPausedDays);
                    }
                }

                // 6) Compute new end date = base + plannedPausedDays
                $newEndDate = (clone $baseEnd)->addDays($plannedPausedDays);

                Log::info('Pausing subscription', [
                    'order_id'            => $order_id,
                    'user_id'             => $subscription->user_id,
                    'pause_start'         => $pauseStartDate->toDateString(),
                    'pause_end'           => $pauseEndDate->toDateString(),
                    'planned_paused_days' => $plannedPausedDays,
                    'base_end'            => $baseEnd->toDateString(),
                    'new_end'             => $newEndDate->toDateString(),
                    'had_new_date'        => (bool) $subscription->new_date,
                    'status_before'       => $subscription->status,
                ]);

                // 7) Upsert the pause log (create if new, update if editing)
                if ($subscription->status === 'paused' && $existingPauseLog) {
                    $existingPauseLog->update([
                        'pause_start_date' => $pauseStartDate->toDateString(),
                        'pause_end_date'   => $pauseEndDate->toDateString(),
                        'paused_days'      => $plannedPausedDays,
                        'new_end_date'     => $newEndDate->toDateString(),
                    ]);
                } else {
                    SubscriptionPauseResumeLog::create([
                        'subscription_id'  => $subscription->subscription_id,
                        'order_id'         => $order_id,
                        'action'           => 'paused',
                        'pause_start_date' => $pauseStartDate->toDateString(),
                        'pause_end_date'   => $pauseEndDate->toDateString(),
                        'paused_days'      => $plannedPausedDays,
                        'new_end_date'     => $newEndDate->toDateString(),
                    ]);
                }

                // 8) Update subscription -> status paused + set window + new_date
                $subscription->status           = 'paused';
                $subscription->pause_start_date = $pauseStartDate->toDateString();
                $subscription->pause_end_date   = $pauseEndDate->toDateString();
                $subscription->new_date         = $newEndDate->toDateString();
                $subscription->save();

                return response()->json([
                    'success' => 200,
                    'message' => 'Subscription pause details updated successfully.',
                    'data' => [
                        'subscription_id'  => $subscription->subscription_id,
                        'order_id'         => $order_id,
                        'pause_start_date' => $pauseStartDate->toDateString(),
                        'pause_end_date'   => $pauseEndDate->toDateString(),
                        'paused_days'      => $plannedPausedDays,
                        'new_end_date'     => $newEndDate->toDateString(),
                    ]
                ], 200);
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => 404,
                'message' => 'Subscription not found or inactive.',
                'error'   => $e->getMessage()
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error pausing subscription', [
                'order_id' => $order_id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => 500,
                'message' => 'An error occurred while updating the pause details.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function resume(Request $request, $order_id)
    {
        // 1) Validate input
        $request->validate([
            'resume_date' => ['required','date'],
        ]);

        try {
            return DB::transaction(function () use ($request, $order_id) {
                // 2) Lock the subscription row
                $subscription = Subscription::where('order_id', $order_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($subscription->status !== 'paused') {
                    return response()->json([
                        'success' => 409,
                        'message' => 'Subscription is not in a paused state.',
                    ], 409);
                }

                // Ensure pause dates exist
                if (empty($subscription->pause_start_date) || empty($subscription->pause_end_date)) {
                    return response()->json([
                        'success' => 422,
                        'message' => 'Pause window is not defined for this subscription.',
                    ], 422);
                }

                // 3) Parse dates (treat as whole days)
                $resumeDate      = Carbon::parse($request->resume_date)->startOfDay();
                $pauseStartDate  = Carbon::parse($subscription->pause_start_date)->startOfDay();
                $pauseEndDate    = Carbon::parse($subscription->pause_end_date)->startOfDay();
                $currentEndDate  = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();

                Log::info('Resuming subscription (incoming)', [
                    'order_id'        => $order_id,
                    'user_id'         => $subscription->user_id,
                    'resume_date'     => $resumeDate->toDateString(),
                    'pause_start'     => $pauseStartDate->toDateString(),
                    'pause_end'       => $pauseEndDate->toDateString(),
                    'current_end'     => $currentEndDate->toDateString(),
                    'had_new_date'    => (bool) $subscription->new_date,
                ]);

                // 4) Resume must be within pause period (inclusive)
                if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
                    return response()->json([
                        'success' => 422,
                        'message' => 'Resume date must be within the pause period.',
                    ], 422);
                }

                // 5) Planned vs actual paused days
                $plannedPausedDays   = $pauseStartDate->diffInDays($pauseEndDate) + 1; // inclusive
                $actualPausedDays    = $pauseStartDate->diffInDays($resumeDate);      // resume on start ⇒ 0
                $remainingPausedDays = max(0, $plannedPausedDays - $actualPausedDays);

                // 6) Correct new end date adjustment
                // If pause() already extended new_date by planned days, now roll back the unused remainder.
                // If not (legacy data), extend only by the actually paused days.
                if (!empty($subscription->new_date)) {
                    $newEndDate = (clone $currentEndDate)->subDays($remainingPausedDays);
                } else {
                    $newEndDate = (clone $currentEndDate)->addDays($actualPausedDays);
                }

                // 7) Persist: activate + clear pause window + set new_date
                $subscription->status            = 'active';
                $subscription->new_date          = $newEndDate->toDateString();
                $subscription->pause_start_date  = null;
                $subscription->pause_end_date    = null;
                $subscription->save();

                // 8) Log resume
                SubscriptionPauseResumeLog::create([
                    'subscription_id'  => $subscription->subscription_id,
                    'order_id'         => $order_id,
                    'action'           => 'resumed',
                    'resume_date'      => $resumeDate->toDateString(),
                    'pause_start_date' => $pauseStartDate->toDateString(),
                    'pause_end_date'   => $pauseEndDate->toDateString(),
                    'new_end_date'     => $newEndDate->toDateString(),
                    'paused_days'      => $actualPausedDays,
                    'meta'             => json_encode([
                        'planned_paused_days'   => $plannedPausedDays,
                        'remaining_paused_days' => $remainingPausedDays,
                        'had_new_date_at_pause' => true, // with the new pause() logic this is always true
                    ]),
                ]);

                Log::info('Subscription resumed successfully', [
                    'order_id'    => $order_id,
                    'new_end_date'=> $newEndDate->toDateString(),
                ]);

                // 9) Fresh instance for response
                $subscription->refresh();

                return response()->json([
                    'success'      => 200,
                    'message'      => 'Subscription resumed successfully.',
                    'subscription' => $subscription,
                ], 200);
            });
        } catch (\Throwable $e) {
            Log::error('Error resuming subscription', [
                'order_id' => $order_id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => 500,
                'message' => 'An error occurred while resuming the subscription.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getApplication()
    {
        try {
        
            $photoBaseUrl = rtrim(config('app.photo_url'), '/') . '/';

            // Get applications
            $applications = PratihariApplication::where('status', 'active')->get();

            // Append full photo URL
            $applications->transform(function ($app) use ($photoBaseUrl) {
                $app->photo_url = $app->photo
                    ? (str_starts_with($app->photo, 'http') ? $app->photo : $photoBaseUrl . ltrim($app->photo, '/'))
                    : null;
                return $app;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Applications fetched successfully.',
                'data' => $applications
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching applications: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch applications.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
