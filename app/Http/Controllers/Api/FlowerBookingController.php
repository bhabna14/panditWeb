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
use Illuminate\Support\Facades\Cache;     // ✅ add this
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

    // public function purchaseSubscription(Request $request)
    // {
    //     $user = Auth::guard('sanctum')->user(); // Get the authenticated user

    //     try {

    //         $orderId = $request->order_id;
    //         $productId = $request->product_id;
    //         $addressId = $request->address_id;
    //         $suggestion = $request->suggestion;
    //         $paymentId = $request->payment_id;

    //         // Order handling
    //         if ($orderId) {
    //             $order = Order::where('order_id', $orderId)->first();
    //             if ($order) {
    //                 $order->update([
    //                     'product_id' => $productId,
    //                     'user_id' => $user->userid,
    //                     'quantity' => 1,
    //                     'total_price' => $request->paid_amount,
    //                     'address_id' => $addressId,
    //                     'suggestion' => $suggestion,
    //                 ]);
    //             } else {
    //                 return response()->json(['message' => 'Order not found for update'], 404);
    //             }
    //         } else {
    //             $orderId = 'ORD-' . strtoupper(Str::random(12));
    //             Order::create([
    //                 'order_id' => $orderId,
    //                 'product_id' => $productId,
    //                 'user_id' => $user->userid,
    //                 'quantity' => 1,
    //                 'total_price' => $request->paid_amount,
    //                 'address_id' => $addressId,
    //                 'suggestion' => $suggestion,
    //             ]);
    //         }

    //         // Razorpay payment handling
    //         $razorpayApi = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    //         $payment = $razorpayApi->payment->fetch($paymentId);

    //         if ($payment->status === 'authorized') {
    //             $payment->capture(['amount' => $payment->amount]);
    //         } elseif ($payment->status !== 'captured') {
    //             return response()->json(['message' => 'Payment failed or not authorized.'], 400);
    //         }

    //         // Subscription logic
    //         $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
    //         $endDate = match ($request->duration) {
    //             1 => $startDate->copy()->addDays(29),
    //             3 => $startDate->copy()->addDays(89),
    //             6 => $startDate->copy()->addDays(179),
    //             default => throw new \Exception('Invalid subscription duration'),
    //         };

    //         $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
    //         Subscription::create([
    //             'subscription_id' => $subscriptionId,
    //             'user_id' => $user->userid,
    //             'order_id' => $orderId,
    //             'product_id' => $productId,
    //             'start_date' => $startDate,
    //             'end_date' => $endDate,
    //             'is_active' => true,
    //             'status' => $startDate->isToday() ? 'active' : 'pending',
    //         ]);

    //         // Record payment
    //         FlowerPayment::create([
    //             'order_id' => $orderId,
    //             'payment_id' => $paymentId,
    //             'user_id' => $user->userid,
    //             'payment_method' => 'Razorpay',
    //             'paid_amount' => $request->paid_amount,
    //             'payment_status' => 'paid',
    //         ]);

    //         // Notification
    //         $deviceTokens = UserDevice::where('user_id', $user->userid)->whereNotNull('device_id')->pluck('device_id')->toArray();
    //         if ($deviceTokens) {
    //             $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
    //             $notificationService->sendBulkNotifications($deviceTokens, 'Order Received', 'Your subscription has been placed successfully.', [
    //                 'subscription_id' => $subscriptionId,
    //             ]);
    //         }

    //         // Send email
    //         $emails = [
    //             'soumyaranjan.puhan@33crores.com',
    //             'pankaj.sial@33crores.com',
    //             'basudha@33crores.com',
    //             'priya@33crores.com',
    //             'starleen@33crores.com'
    //         ];
    //                     Mail::to($emails)->send(new SubscriptionConfirmationMail(Order::where('order_id', $orderId)->first()));

    //         return response()->json([
    //             'message' => 'Subscription activated successfully',
    //             'end_date' => $endDate,
    //             'order_id' => $orderId,
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         Log::error('Error processing subscription', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to process subscription', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function createOrUpdateOrderWithSubscription(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'order_id'   => 'nullable|string',
            'product_id' => 'required|string',
            'address_id' => 'required|integer',
            'suggestion' => 'nullable|string',
            'paid_amount'=> 'required|numeric',
            'duration'   => 'required|integer|in:1,3,6',
            'start_date' => 'nullable|date',
        ]);

        try {
            // 1) Order handling
            $orderId = $validated['order_id'];

            if ($orderId) {
                $order = Order::where('order_id', $orderId)->first();
                if (!$order) {
                    // not found → create new
                    $orderId = 'ORD-' . strtoupper(Str::random(12));
                    $order = Order::create([
                        'order_id'    => $orderId,
                        'product_id'  => $validated['product_id'],
                        'user_id'     => $user->userid,
                        'quantity'    => 1,
                        'total_price' => $validated['paid_amount'],
                        'address_id'  => $validated['address_id'],
                        'suggestion'  => $validated['suggestion'],
                    ]);
                } else {
                    // update existing order
                    $order->update([
                        'product_id'  => $validated['product_id'],
                        'user_id'     => $user->userid,
                        'quantity'    => 1,
                        'total_price' => $validated['paid_amount'],
                        'address_id'  => $validated['address_id'],
                        'suggestion'  => $validated['suggestion'],
                    ]);
                }
            } else {
                // no order_id given → new order
                $orderId = 'ORD-' . strtoupper(Str::random(12));
                $order = Order::create([
                    'order_id'    => $orderId,
                    'product_id'  => $validated['product_id'],
                    'user_id'     => $user->userid,
                    'quantity'    => 1,
                    'total_price' => $validated['paid_amount'],
                    'address_id'  => $validated['address_id'],
                    'suggestion'  => $validated['suggestion'],
                ]);
            }

            // 2) Subscription create (always new row)
            $startDate = $validated['start_date']
                ? Carbon::parse($validated['start_date'])
                : now();

            $endDate = match ($validated['duration']) {
                1 => $startDate->copy()->addDays(29),
                3 => $startDate->copy()->addDays(89),
                6 => $startDate->copy()->addDays(179),
            };

            $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
            $subscription = Subscription::create([
                'subscription_id' => $subscriptionId,
                'user_id'         => $user->userid,
                'order_id'        => $orderId,
                'product_id'      => $validated['product_id'],
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'is_active'       => true,
                'status'          => $startDate->isToday() ? 'active' : 'pending',
            ]);

            // 📧 Send Emails (still keep email confirmation here)
            $emails = [
                'soumyaranjan.puhan@33crores.com',
                'pankaj.sial@33crores.com',
                'basudha@33crores.com',
                'priya@33crores.com',
                'starleen@33crores.com'
            ];
            Mail::to($emails)->send(new SubscriptionConfirmationMail($order));

            return response()->json([
                'message'      => 'Order & Subscription processed successfully',
                'order_id'     => $orderId,
                'subscription' => $subscription,
                'end_date'     => $endDate,
            ]);
        } catch (\Exception $e) {
            Log::error('Order/Subscription creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function processPayment(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'order_id'   => 'required|string',
            'payment_id' => 'required|string',
            'paid_amount'=> 'required|numeric',
        ]);

        try {
            $razorpayApi = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $payment = $razorpayApi->payment->fetch($validated['payment_id']);

            if ($payment->status === 'authorized') {
                $payment->capture(['amount' => $payment->amount]);
            } elseif ($payment->status !== 'captured') {
                return response()->json(['message' => 'Payment failed or not authorized.'], 400);
            }

            // Save payment
            $flowerPayment = FlowerPayment::create([
                'order_id'       => $validated['order_id'],
                'payment_id'     => $validated['payment_id'],
                'user_id'        => $user->userid,
                'payment_method' => 'Razorpay',
                'paid_amount'    => $validated['paid_amount'],
                'payment_status' => 'paid',
            ]);

            // ✅ Send Notifications here after payment success
            $deviceTokens = UserDevice::where('user_id', $user->userid)
                ->whereNotNull('device_id')
                ->pluck('device_id')
                ->toArray();

            if ($deviceTokens) {
                $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                $notificationService->sendBulkNotifications(
                    $deviceTokens,
                    'Payment Successful',
                    'Your order & subscription have been confirmed successfully.',
                    ['order_id' => $validated['order_id']]
                );
            }

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment' => $flowerPayment,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment processing failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Payment failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function storerequest(Request $request)
    {
        try {
            // 1) Normalize items (handles both new/old formats)
            $rawItems = $request->input('items');

            if (is_string($rawItems)) {
                $decoded = json_decode($rawItems, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $rawItems = $decoded;
                }
            }

            $normalizedItems = [];

            if (is_array($rawItems)) {
                $normalizedItems = $rawItems;
            } else {
                $flowerNames     = $request->input('flower_name', []);
                $flowerUnits     = $request->input('flower_unit', []);
                $flowerQuantites = $request->input('flower_quantity', []);

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

            // ---- hard de-dup items (same payload repeated) ----
            // Use a signature as array key to collapse duplicates
            $dedup = [];
            foreach ($normalizedItems as $it) {
                // normalize scalar types to avoid "2" vs 2 being different
                if (($it['type'] ?? null) === 'flower') {
                    $sig = implode('|', [
                        'flower',
                        trim((string)($it['flower_name'] ?? '')),
                        trim((string)($it['flower_unit'] ?? '')),
                        (string)(is_numeric($it['flower_quantity'] ?? null) ? (float)$it['flower_quantity'] : ($it['flower_quantity'] ?? ''))
                    ]);
                } else {
                    $sig = implode('|', [
                        'garland',
                        trim((string)($it['garland_name'] ?? '')),
                        (string)(is_numeric($it['garland_quantity'] ?? null) ? (int)$it['garland_quantity'] : ($it['garland_quantity'] ?? '')),
                        (string)(is_numeric($it['flower_count'] ?? null) ? (int)$it['flower_count'] : ($it['flower_count'] ?? '')),
                        trim((string)($it['garland_size'] ?? '')),
                    ]);
                }
                $dedup[$sig] = $it;
            }
            $normalizedItems = array_values($dedup);

            // Merge back so validator sees proper array
            $request->merge(['items' => $normalizedItems]);

            // 2) Validate AFTER normalization/dedup
            $validator = Validator::make($request->all(), [
                'product_id'   => ['required'], // keep non-integer if it can be a code
                'address_id'   => ['required', 'integer'],
                'description'  => ['nullable', 'string'],
                'suggestion'   => ['nullable', 'string'],
                'date'         => ['required', 'date'],
                'time'         => ['required', 'string'],

                'items'        => ['required', 'array', 'min:1'],
                'items.*.type' => ['required', 'in:flower,garland'],

                // Flower
                'items.*.flower_name'     => ['required_if:items.*.type,flower', 'string'],
                'items.*.flower_unit'     => ['required_if:items.*.type,flower', 'string'],
                'items.*.flower_quantity' => ['required_if:items.*.type,flower', 'numeric', 'min:1'],

                // Garland
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

            // 3) Build an idempotency key (user+product+date+time+items hash)
            $user = Auth::guard('sanctum')->user();
            $itemsForHash = collect($normalizedItems)
                ->map(function ($it) {
                    // normalize numeric fields so 2 and "2" hash the same
                    if (($it['type'] ?? null) === 'flower') {
                        return [
                            'type' => 'flower',
                            'flower_name' => trim((string)$it['flower_name']),
                            'flower_unit' => trim((string)$it['flower_unit']),
                            'flower_quantity' => (float)$it['flower_quantity'],
                        ];
                    }
                    return [
                        'type' => 'garland',
                        'garland_name' => trim((string)$it['garland_name']),
                        'garland_quantity' => isset($it['garland_quantity']) ? (int)$it['garland_quantity'] : null,
                        'flower_count' => isset($it['flower_count']) ? (int)$it['flower_count'] : null,
                        'garland_size' => trim((string)($it['garland_size'] ?? '')),
                    ];
                })
                ->sortBy(fn ($v) => json_encode($v)) // order-independent
                ->values()
                ->all();

            $requestHash = hash('sha256', implode('|', [
                $user->userid,
                (string)$request->product_id,
                (string)$request->date,
                (string)$request->time,
                json_encode($itemsForHash),
            ]));

            // Optional header-based idempotency key from client (mobile SDKs)
            $clientKey = $request->header('Idempotency-Key');
            $idempotencyKey = $clientKey ? ('flower:idem:'.$clientKey) : ('flower:req:'.$requestHash);

            // 4) Acquire a short lock to prevent double-submits creating duplicates
            $lock = Cache::lock($idempotencyKey, 10); // 10 seconds
            // if (!$lock->get()) {
            //     // Another identical request is in-flight or just completed
            //     // Try to return the existing order if it exists
            //     $existing = \App\Models\FlowerRequest::query()
            //         ->where('user_id', $user->userid)
            //         ->where('request_hash', $requestHash)
            //         ->with(['address.localityDetails','user','flowerRequestItems'])
            //         ->latest('id')
            //         ->first();

            //     if ($existing) {
            //         return response()->json([
            //             'status'  => 200,
            //             'message' => 'Flower request already created',
            //             'data'    => $existing,
            //         ], 200);
            //     }

            //     return response()->json([
            //         'status'  => 409,
            //         'message' => 'A similar request is being processed. Please try again in a moment.',
            //     ], 409);
            // }

            // 5) Create inside a single DB transaction
            try {
                $flowerRequest = DB::transaction(function () use ($request, $user, $normalizedItems, $requestHash) {

                    // If a matching request was created milliseconds ago (retry), reuse it
                    $already = \App\Models\FlowerRequest::query()
                        ->where('user_id', $user->userid)
                        ->where('request_hash', $requestHash)
                        ->latest('id')
                        ->first();

                    if ($already) {
                        return $already->load(['address.localityDetails','user','flowerRequestItems']);
                    }

                    $publicRequestId = 'REQ-' . strtoupper(Str::random(12));

                    /** @var \App\Models\FlowerRequest $flowerRequest */
                    $flowerRequest = \App\Models\FlowerRequest::create([
                        'request_id'   => $publicRequestId,
                        'request_hash' => $requestHash,      // <<< store hash (add nullable column in DB)
                        'product_id'   => $request->product_id,
                        'user_id'      => $user->userid,
                        'address_id'   => $request->address_id,
                        'description'  => $request->description,
                        'suggestion'   => $request->suggestion,
                        'date'         => $request->date,
                        'time'         => $request->time,
                        'status'       => 'pending',
                    ]);

                    // Insert items
                    foreach ($normalizedItems as $item) {
                        $type = $item['type'];

                        $payload = [
                            'flower_request_id' => $flowerRequest->request_id, // FK is the public request_id (string)
                            'type'              => $type,
                        ];

                        if ($type === 'flower') {
                            $payload['flower_name']     = trim((string)$item['flower_name']);
                            $payload['flower_unit']     = trim((string)$item['flower_unit']);
                            $payload['flower_quantity'] = is_numeric($item['flower_quantity']) ? (float)$item['flower_quantity'] : null;
                        } else {
                            $payload['garland_name']     = trim((string)$item['garland_name']);
                            $payload['garland_quantity'] = isset($item['garland_quantity']) && is_numeric($item['garland_quantity']) ? (int)$item['garland_quantity'] : null;
                            $payload['flower_count']     = isset($item['flower_count']) && is_numeric($item['flower_count']) ? (int)$item['flower_count'] : null;
                            $payload['garland_size']     = trim((string)($item['garland_size'] ?? ''));
                        }

                        \App\Models\FlowerRequestItem::create($payload);
                    }

                    return $flowerRequest->load(['address.localityDetails','user','flowerRequestItems']);
                });
            } finally {
                // Always release the lock
                optional($lock)->release();
            }

            // 6) Notifications (same as yours, but use public request_id consistently in data)
            $deviceTokens = \App\Models\UserDevice::where('user_id', $user->userid)
                ->whereNotNull('device_id')
                ->pluck('device_id')
                ->filter()
                ->toArray();

            if (!empty($deviceTokens)) {
                try {
                    $notificationService = new \App\Services\NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                    $notificationService->sendBulkNotifications(
                        $deviceTokens,
                        'Order Created',
                        'Your order has been placed. Price will be notified in few minutes.',
                        ['request_id' => $flowerRequest->request_id] // <<< use public id; avoids confusion
                    );
                } catch (\Throwable $e) {
                    \Log::error('Failed to send device notifications', ['e' => $e->getMessage()]);
                }
            } else {
                \Log::warning('No device tokens found for user.', ['user_id' => $user->userid]);
            }

            // 7) Email
            try {
                $emails = [
                    'soumyaranjan.puhan@33crores.com',
                    'pankaj.sial@33crores.com',
                    'basudha@33crores.com',
                    'priya@33crores.com',
                    'starleen@33crores.com',
                ];
                \Mail::to($emails)->send(new \App\Mail\FlowerRequestMail($flowerRequest));
            } catch (\Throwable $e) {
                \Log::error('Failed to send email.', ['error' => $e->getMessage()]);
            }

            // 8) WhatsApp (Twilio)
            try {
                $adminNumber = '+919776888887';
                $twilioSid = env('TWILIO_ACCOUNT_SID');
                $twilioToken = env('TWILIO_AUTH_TOKEN');
                $twilioWhatsAppNumber = env('TWILIO_WHATSAPP_NUMBER');

                $messageBody = "*New Flower Request Received*\n\n" .
                    "*Request ID:* {$flowerRequest->request_id}\n" .
                    "*User:* {$flowerRequest->user->mobile_number}\n" .
                    "*Address:* {$flowerRequest->address->apartment_flat_plot}, " .
                    "{$flowerRequest->address->localityDetails->locality_name}, " .
                    "{$flowerRequest->address->city}, {$flowerRequest->state}, " .
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

                $twilioClient = new \Twilio\Rest\Client($twilioSid, $twilioToken);
                $twilioClient->messages->create(
                    "whatsapp:{$adminNumber}",
                    [
                        'from' => $twilioWhatsAppNumber,
                        'body' => $messageBody,
                    ]
                );
            } catch (\Throwable $e) {
                \Log::error('Failed to send WhatsApp notification.', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Flower request created successfully',
                'data'    => $flowerRequest,
            ], 200);

        } catch (\Throwable $e) {
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

        // ===== SUBSCRIPTIONS =====
        $subscriptionsOrder = Subscription::where('user_id', $userId)
            ->with([
                'order.flowerPayments',
                'order.address.localityDetails',
                'flowerProducts',
                'pauseResumeLog',
                'users',
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($sub) {
                // Product image URL
                if ($sub->flowerProduct) {
                    $sub->flowerProduct->product_image_url = $sub->flowerProduct->product_image;
                }

                // Normalize order -> flower_payments (select only one latest payment)
                if ($sub->order) {
                    $payments = $sub->order->flowerPayments ?? collect();

                    // Find the latest "paid" record first
                    $latestPaid = $payments->where('payment_status', 'paid')->sortByDesc('id')->first();

                    // If no paid record, find latest "pending"
                    $latestPending = $payments->where('payment_status', 'pending')->sortByDesc('id')->first();

                    // Prefer latestPaid if exists, else latestPending
                    $selectedPayment = $latestPaid ?? $latestPending ?? null;

                    $sub->order->flower_payments = $selectedPayment ? (object)$selectedPayment : (object)[];
                    unset($sub->order->flowerPayments);
                }

                // Pending renewals
                if ($sub->status === 'active') {
                    $pendingRenewal = Subscription::where('user_id', $sub->user_id)
                        ->where('status', 'pending')
                        ->orderBy('start_date', 'asc')
                        ->first();
                    $sub->pending_renewals = $pendingRenewal ?: (object)[];
                } else {
                    $sub->pending_renewals = (object)[];
                }

                return $sub;
            });

        // ===== ONE-OFF REQUESTS =====
        $requestedOrders = FlowerRequest::where('user_id', $userId)
            ->with([
                'order.flowerPayments',
                'flowerProduct',
                'user',
                'address.localityDetails',
                'flowerRequestItems',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(function ($requestRow) {
                // Normalize order -> flower_payments (same logic)
                if ($requestRow->order) {
                    $payments = $requestRow->order->flowerPayments ?? collect();

                    $latestPaid = $payments->where('payment_status', 'paid')->sortByDesc('id')->first();
                    $latestPending = $payments->where('payment_status', 'pending')->sortByDesc('id')->first();

                    $selectedPayment = $latestPaid ?? $latestPending ?? null;

                    $requestRow->order->flower_payments = $selectedPayment ? (object)$selectedPayment : (object)[];
                    unset($requestRow->order->flowerPayments);
                }

                // Product image URL
                if ($requestRow->flowerProduct) {
                    $requestRow->flowerProduct->product_image_url = $requestRow->flowerProduct->product_image;
                }

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

    // public function pause(Request $request, $order_id)
    // {
    //     // 1) Validate input (inclusive window)
    //     $request->validate([
    //         'pause_start_date' => ['required', 'date'],
    //         'pause_end_date'   => ['required', 'date'],
    //     ]);

    //     $pauseStartDate = Carbon::parse($request->pause_start_date)->startOfDay();
    //     $pauseEndDate   = Carbon::parse($request->pause_end_date)->startOfDay();

    //     if ($pauseEndDate->lt($pauseStartDate)) {
    //         return response()->json([
    //             'success' => 422,
    //             'message' => 'Pause end date must be on/after the start date.',
    //         ], 422);
    //     }

    //     // Inclusive day count
    //     $plannedPausedDays = $pauseStartDate->diffInDays($pauseEndDate) + 1;
    //     $today             = Carbon::today();

    //     try {
    //         return DB::transaction(function () use ($order_id, $pauseStartDate, $pauseEndDate, $plannedPausedDays, $today) {

    //             // 2) Lock subscription row
    //             $subscription = Subscription::where('order_id', $order_id)
    //                 ->whereIn('status', ['active', 'paused']) // only adjustable states
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             // 3) Identify the current paused cycle (only if we're actually paused now)
    //             $existingPauseLog = null;
    //             if ($subscription->status === 'paused') {
    //                 $existingPauseLog = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->latest('id')
    //                     ->first();
    //             }

    //             // 4) Overlap guard ONLY when subscription is currently paused
    //             // If active, we accept the new window even if it overlaps historical windows.
    //             if ($subscription->status === 'paused') {
    //                 $overlapQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->when($existingPauseLog, function ($q) use ($existingPauseLog) {
    //                         // ignore the currently-open paused log if editing it
    //                         $q->where('id', '!=', $existingPauseLog->id);
    //                     })
    //                     ->where(function ($q) use ($pauseStartDate, $pauseEndDate) {
    //                         // Treat endpoints as overlapping (inclusive)
    //                         $start = $pauseStartDate->toDateString();
    //                         $end   = $pauseEndDate->toDateString();

    //                         $q->whereBetween(DB::raw('DATE(pause_start_date)'), [$start, $end])
    //                           ->orWhereBetween(DB::raw('DATE(pause_end_date)'),   [$start, $end])
    //                           ->orWhere(function ($q2) use ($start, $end) {
    //                               $q2->whereDate('pause_start_date', '<=', $start)
    //                                  ->whereDate('pause_end_date',   '>=', $end);
    //                           });
    //                     });

    //                 if ($overlapQuery->exists()) {
    //                     return response()->json([
    //                         'success' => 422,
    //                         'message' => 'This pause window overlaps with another pause request for the same subscription.',
    //                     ], 422);
    //                 }
    //             }

    //             // 5) Determine base end date BEFORE applying this pause
    //             // Use effective end (COALESCE(new_date, end_date))
    //             $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();

    //             // If currently paused and we're editing that same cycle, undo previous extension first
    //             $baseEnd = clone $effectiveEnd;
    //             if ($subscription->status === 'paused' && $existingPauseLog) {
    //                 $prevPausedDays = (int) ($existingPauseLog->paused_days ?? 0);
    //                 if ($prevPausedDays > 0) {
    //                     $baseEnd = (clone $effectiveEnd)->subDays($prevPausedDays);
    //                 }
    //             }

    //             // 6) Compute new end date = base + plannedPausedDays
    //             $newEndDate = (clone $baseEnd)->addDays($plannedPausedDays);

    //             // 7) Upsert the pause log (create if new, update if editing)
    //             if ($subscription->status === 'paused' && $existingPauseLog) {
    //                 $existingPauseLog->update([
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             } else {
    //                 SubscriptionPauseResumeLog::create([
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'action'           => 'paused',
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             }

    //             // 8) Update subscription window + new_date
    //             $subscription->pause_start_date = $pauseStartDate->toDateString();
    //             $subscription->pause_end_date   = $pauseEndDate->toDateString();
    //             $subscription->new_date         = $newEndDate->toDateString();

    //             // ✨ Status logic:
    //             // - If the pause window includes TODAY, set status to 'paused'
    //             // - If the pause starts in the FUTURE, keep status as-is (usually 'active') so today shows active
    //             $isInPauseToday = $today->betweenIncluded($pauseStartDate, $pauseEndDate);
    //             if ($isInPauseToday) {
    //                 $subscription->status = 'paused';
    //             } else {
    //                 // keep current status; if it was paused but the new window is future-only,
    //                 // bring it back to 'active'
    //                 if ($subscription->status === 'paused') {
    //                     $subscription->status = 'active';
    //                 }
    //             }

    //             $subscription->save();

    //             Log::info('Pausing subscription', [
    //                 'order_id'            => $order_id,
    //                 'user_id'             => $subscription->user_id,
    //                 'pause_start'         => $pauseStartDate->toDateString(),
    //                 'pause_end'           => $pauseEndDate->toDateString(),
    //                 'planned_paused_days' => $plannedPausedDays,
    //                 'base_end'            => $baseEnd->toDateString(),
    //                 'new_end'             => $newEndDate->toDateString(),
    //                 'had_new_date'        => (bool) $subscription->new_date,
    //                 'status_saved'        => $subscription->status,
    //             ]);

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Subscription pause details updated successfully.',
    //                 'data' => [
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                     'status'           => $subscription->status,
    //                 ]
    //             ], 200);
    //         });
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => 404,
    //             'message' => 'Subscription not found or inactive.',
    //             'error'   => $e->getMessage()
    //         ], 404);
    //     } catch (\Throwable $e) {
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error'    => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while updating the pause details.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function resume(Request $request, $order_id)
    // {
    //     // 1) Validate input
    //     $request->validate([
    //         'resume_date' => ['required', 'date'],
    //     ]);

    //     try {
    //         return DB::transaction(function () use ($request, $order_id) {
    //             // 2) Lock the subscription row
    //             $subscription = Subscription::where('order_id', $order_id)
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             if ($subscription->status !== 'paused') {
    //                 return response()->json([
    //                     'success' => 409,
    //                     'message' => 'Subscription is not in a paused state.',
    //                 ], 409);
    //             }

    //             // Ensure pause dates exist
    //             if (empty($subscription->pause_start_date) || empty($subscription->pause_end_date)) {
    //                 return response()->json([
    //                     'success' => 422,
    //                     'message' => 'Pause window is not defined for this subscription.',
    //                 ], 422);
    //             }

    //             // 3) Parse dates (treat as whole days)
    //             $resumeDate      = Carbon::parse($request->resume_date)->startOfDay();
    //             $pauseStartDate  = Carbon::parse($subscription->pause_start_date)->startOfDay();
    //             $pauseEndDate    = Carbon::parse($subscription->pause_end_date)->startOfDay();
    //             $currentEndDate  = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();

    //             Log::info('Resuming subscription (incoming)', [
    //                 'order_id'     => $order_id,
    //                 'user_id'      => $subscription->user_id,
    //                 'resume_date'  => $resumeDate->toDateString(),
    //                 'pause_start'  => $pauseStartDate->toDateString(),
    //                 'pause_end'    => $pauseEndDate->toDateString(),
    //                 'current_end'  => $currentEndDate->toDateString(),
    //                 'had_new_date' => (bool) $subscription->new_date,
    //             ]);

    //             // 4) Resume must be within pause period (inclusive)
    //             if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
    //                 return response()->json([
    //                     'success' => 422,
    //                     'message' => 'Resume date must be within the pause period.',
    //                 ], 422);
    //             }

    //             // 5) Planned vs actual paused days
    //             $plannedPausedDays   = $pauseStartDate->diffInDays($pauseEndDate) + 1; // inclusive
    //             $actualPausedDays    = $pauseStartDate->diffInDays($resumeDate);      // resume on start ⇒ 0
    //             $remainingPausedDays = max(0, $plannedPausedDays - $actualPausedDays);

    //             // 6) Correct new end date adjustment
    //             // If pause() already extended new_date by planned days, now roll back the unused remainder.
    //             // If not (legacy), extend only by actual paused days.
    //             if (!empty($subscription->new_date)) {
    //                 $newEndDate = (clone $currentEndDate)->subDays($remainingPausedDays);
    //             } else {
    //                 $newEndDate = (clone $currentEndDate)->addDays($actualPausedDays);
    //             }

    //             // 7) Persist: activate + clear pause window + set new_date
    //             $subscription->status            = 'active';
    //             $subscription->new_date          = $newEndDate->toDateString();
    //             $subscription->pause_start_date  = null;
    //             $subscription->pause_end_date    = null;
    //             $subscription->save();

    //             // 8) Log resume
    //             SubscriptionPauseResumeLog::create([
    //                 'subscription_id'  => $subscription->subscription_id,
    //                 'order_id'         => $order_id,
    //                 'action'           => 'resumed',
    //                 'resume_date'      => $resumeDate->toDateString(),
    //                 'pause_start_date' => $pauseStartDate->toDateString(),
    //                 'pause_end_date'   => $pauseEndDate->toDateString(),
    //                 'new_end_date'     => $newEndDate->toDateString(),
    //                 'paused_days'      => $actualPausedDays,
    //                 'meta'             => json_encode([
    //                     'planned_paused_days'   => $plannedPausedDays,
    //                     'remaining_paused_days' => $remainingPausedDays,
    //                     'had_new_date_at_pause' => true,
    //                 ]),
    //             ]);

    //             Log::info('Subscription resumed successfully', [
    //                 'order_id'     => $order_id,
    //                 'new_end_date' => $newEndDate->toDateString(),
    //             ]);

    //             // 9) Fresh instance for response
    //             $subscription->refresh();

    //             return response()->json([
    //                 'success'      => 200,
    //                 'message'      => 'Subscription resumed successfully.',
    //                 'subscription' => $subscription,
    //             ], 200);
    //         });
    //     } catch (\Throwable $e) {
    //         Log::error('Error resuming subscription', [
    //             'order_id' => $order_id,
    //             'error'    => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while resuming the subscription.',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function pause(Request $request, $order_id)
    // {
    //     // 1) Validate input (inclusive window)
    //     $request->validate([
    //         'pause_start_date' => ['required', 'date'],
    //         'pause_end_date'   => ['required', 'date'],
    //     ]);

    //     $pauseStartDate = Carbon::parse($request->pause_start_date)->startOfDay();
    //     $pauseEndDate   = Carbon::parse($request->pause_end_date)->startOfDay();

    //     if ($pauseEndDate->lt($pauseStartDate)) {
    //         return response()->json([
    //             'success' => 422,
    //             'message' => 'Pause end date must be on/after the start date.',
    //         ], 422);
    //     }

    //     // Inclusive day count
    //     $plannedPausedDays = $pauseStartDate->diffInDays($pauseEndDate) + 1;
    //     $today             = Carbon::today();

    //     try {
    //         return DB::transaction(function () use ($order_id, $pauseStartDate, $pauseEndDate, $plannedPausedDays, $today) {

    //             // 2) Lock subscription row
    //             $subscription = Subscription::where('order_id', $order_id)
    //                 ->whereIn('status', ['active', 'paused'])
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             // 3) Get the latest log for this order/subscription (we may edit it)
    //             $latestLog = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                 ->where('order_id', $order_id)
    //                 ->latest('id')
    //                 ->first();

    //             // If the latest entry is a 'paused' log, treat it as the editable/open pause
    //             $editPausedLog = ($latestLog && $latestLog->action === 'paused') ? $latestLog : null;

    //             // 4) Overlap guard ONLY when subscription is currently paused
    //             // If active, we accept the new window even if it overlaps historical windows.
    //             if ($subscription->status === 'paused') {
    //                 $overlapQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->when($editPausedLog, function ($q) use ($editPausedLog) {
    //                         // ignore the paused row we are editing (if any)
    //                         $q->where('id', '!=', $editPausedLog->id);
    //                     })
    //                     ->where(function ($q) use ($pauseStartDate, $pauseEndDate) {
    //                         // Inclusive overlap
    //                         $start = $pauseStartDate->toDateString();
    //                         $end   = $pauseEndDate->toDateString();

    //                         $q->whereBetween(DB::raw('DATE(pause_start_date)'), [$start, $end])
    //                         ->orWhereBetween(DB::raw('DATE(pause_end_date)'),   [$start, $end])
    //                         ->orWhere(function ($q2) use ($start, $end) {
    //                             $q2->whereDate('pause_start_date', '<=', $start)
    //                                 ->whereDate('pause_end_date',   '>=', $end);
    //                         });
    //                     });

    //                 if ($overlapQuery->exists()) {
    //                     return response()->json([
    //                         'success' => 422,
    //                         'message' => 'This pause window overlaps with another pause request for the same subscription.',
    //                     ], 422);
    //                 }
    //             }

    //             // 5) Determine base end date BEFORE applying this pause
    //             // Use effective end (COALESCE(new_date, end_date))
    //             $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();

    //             // If we are editing an existing open/future paused log, undo its previous extension
    //             $baseEnd = clone $effectiveEnd;
    //             if ($editPausedLog) {
    //                 $prevPausedDays = (int) ($editPausedLog->paused_days ?? 0);
    //                 if ($prevPausedDays > 0) {
    //                     $baseEnd = (clone $effectiveEnd)->subDays($prevPausedDays);
    //                 }
    //             }

    //             // 6) Compute new end date = base + plannedPausedDays
    //             $newEndDate = (clone $baseEnd)->addDays($plannedPausedDays);

    //             // 7) Upsert the pause log
    //             if ($editPausedLog) {
    //                 // Update the same row (your new rule)
    //                 $editPausedLog->update([
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             } else {
    //                 // Create a new paused row (no existing open/future paused entry)
    //                 SubscriptionPauseResumeLog::create([
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'action'           => 'paused',
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             }

    //             // 8) Update subscription window + new_date
    //             $subscription->pause_start_date = $pauseStartDate->toDateString();
    //             $subscription->pause_end_date   = $pauseEndDate->toDateString();
    //             $subscription->new_date         = $newEndDate->toDateString();

    //             // Status logic (respect "future-dated" scheduling)
    //             $isInPauseToday = $today->between($pauseStartDate, $pauseEndDate, true); // inclusive
    //             if ($isInPauseToday) {
    //                 $subscription->status = 'paused';
    //             } else {
    //                 // If the new window is future-only, ensure today remains active
    //                 if ($subscription->status === 'paused') {
    //                     $subscription->status = 'active';
    //                 }
    //             }

    //             $subscription->save();

    //             Log::info('Pausing subscription', [
    //                 'order_id'            => $order_id,
    //                 'user_id'             => $subscription->user_id,
    //                 'pause_start'         => $pauseStartDate->toDateString(),
    //                 'pause_end'           => $pauseEndDate->toDateString(),
    //                 'planned_paused_days' => $plannedPausedDays,
    //                 'base_end'            => $baseEnd->toDateString(),
    //                 'new_end'             => $newEndDate->toDateString(),
    //                 'had_new_date'        => (bool) $subscription->new_date,
    //                 'status_saved'        => $subscription->status,
    //                 'edit_log_id'         => $editPausedLog?->id,
    //             ]);

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Subscription pause details updated successfully.',
    //                 'data' => [
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                     'status'           => $subscription->status,
    //                 ]
    //             ], 200);
    //         });
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => 404,
    //             'message' => 'Subscription not found or inactive.',
    //             'error'   => $e->getMessage()
    //         ], 404);
    //     } catch (\Throwable $e) {
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error'    => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while updating the pause details.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function pause(Request $request, $order_id)
    // {
    //     // 1) Validate input (inclusive window)
    //     $request->validate([
    //         'pause_start_date' => ['required', 'date'],
    //         'pause_end_date'   => ['required', 'date'],
    //     ]);

    //     $pauseStartDate = Carbon::parse($request->pause_start_date)->startOfDay();
    //     $pauseEndDate   = Carbon::parse($request->pause_end_date)->startOfDay();

    //     if ($pauseEndDate->lt($pauseStartDate)) {
    //         return response()->json([
    //             'success' => 422,
    //             'message' => 'Pause end date must be on/after the start date.',
    //         ], 422);
    //     }

    //     // Inclusive day count
    //     $plannedPausedDays = $pauseStartDate->diffInDays($pauseEndDate) + 1;
    //     $today             = Carbon::today();

    //     try {
    //         return DB::transaction(function () use ($order_id, $pauseStartDate, $pauseEndDate, $plannedPausedDays, $today) {

    //             // 2) Lock subscription row
    //             $subscription = Subscription::where('order_id', $order_id)
    //                 ->whereIn('status', ['active', 'paused'])
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             // ---------- Find which paused-log row to EDIT (if any) ----------
    //             // Prefer a paused log matching the subscription's current window (if set)
    //             $editPausedLog = null;

    //             $subPauseStart = $subscription->pause_start_date
    //                 ? Carbon::parse($subscription->pause_start_date)->toDateString()
    //                 : null;
    //             $subPauseEnd   = $subscription->pause_end_date
    //                 ? Carbon::parse($subscription->pause_end_date)->toDateString()
    //                 : null;

    //             if ($subPauseStart && $subPauseEnd) {
    //                 $candidate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->whereDate('pause_start_date', $subPauseStart)
    //                     ->whereDate('pause_end_date',   $subPauseEnd)
    //                     ->latest('id')
    //                     ->first();

    //                 if ($candidate) {
    //                     // Make sure it's still "open" (i.e., not followed by a resumed log)
    //                     $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                         ->where('order_id', $order_id)
    //                         ->where('action', 'resumed')
    //                         ->where('id', '>', $candidate->id)
    //                         ->exists();
    //                     if (!$hasResumedAfter) {
    //                         $editPausedLog = $candidate;
    //                     }
    //                 }
    //             }

    //             // If not found by matching window, fall back to latest OPEN paused log (no resumed after it)
    //             if (!$editPausedLog) {
    //                 $latestPaused = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->latest('id')
    //                     ->first();

    //                 if ($latestPaused) {
    //                     $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                         ->where('order_id', $order_id)
    //                         ->where('action', 'resumed')
    //                         ->where('id', '>', $latestPaused->id)
    //                         ->exists();
    //                     if (!$hasResumedAfter) {
    //                         $editPausedLog = $latestPaused;
    //                     }
    //                 }
    //             }

    //             // 4) Overlap guard ONLY when subscription is currently paused.
    //             // If active, allow overlap with historical windows.
    //             if ($subscription->status === 'paused') {
    //                 $overlapQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->when($editPausedLog, function ($q) use ($editPausedLog) {
    //                         $q->where('id', '!=', $editPausedLog->id); // ignore the row we're editing
    //                     })
    //                     ->where(function ($q) use ($pauseStartDate, $pauseEndDate) {
    //                         $start = $pauseStartDate->toDateString();
    //                         $end   = $pauseEndDate->toDateString();

    //                         $q->whereBetween(DB::raw('DATE(pause_start_date)'), [$start, $end])
    //                           ->orWhereBetween(DB::raw('DATE(pause_end_date)'),   [$start, $end])
    //                           ->orWhere(function ($q2) use ($start, $end) {
    //                               $q2->whereDate('pause_start_date', '<=', $start)
    //                                  ->whereDate('pause_end_date',   '>=', $end);
    //                           });
    //                     });

    //                 if ($overlapQuery->exists()) {
    //                     return response()->json([
    //                         'success' => 422,
    //                         'message' => 'This pause window overlaps with another pause request for the same subscription.',
    //                     ], 422);
    //                 }
    //             }

    //             // 5) Determine base end date BEFORE applying this pause
    //             $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();
    //             $baseEnd = clone $effectiveEnd;

    //             // If editing an existing open paused log, undo its previous extension first
    //             if ($editPausedLog) {
    //                 $prevPausedDays = (int) ($editPausedLog->paused_days ?? 0);
    //                 if ($prevPausedDays > 0) {
    //                     $baseEnd = (clone $effectiveEnd)->subDays($prevPausedDays);
    //                 }
    //             }

    //             // 6) Compute new end date = base + plannedPausedDays
    //             $newEndDate = (clone $baseEnd)->addDays($plannedPausedDays);

    //             // 7) Upsert the pause log (ALWAYS update the open row if it exists; else create)
    //             if ($editPausedLog) {
    //                 $editPausedLog->update([
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             } else {
    //                 SubscriptionPauseResumeLog::create([
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'action'           => 'paused',
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             }

    //             // 8) Update subscription window + new_date
    //             $subscription->pause_start_date = $pauseStartDate->toDateString();
    //             $subscription->pause_end_date   = $pauseEndDate->toDateString();
    //             $subscription->new_date         = $newEndDate->toDateString();

    //             // Future-dated logic: paused only if today falls in the window
    //             $isInPauseToday = $today->between($pauseStartDate, $pauseEndDate, true); // inclusive
    //             if ($isInPauseToday) {
    //                 $subscription->status = 'paused';
    //             } else {
    //                 // Ensure today remains active if window is purely future
    //                 if ($subscription->status === 'paused') {
    //                     $subscription->status = 'active';
    //                 }
    //             }

    //             $subscription->save();

    //             Log::info('Pausing subscription (upserted one row only)', [
    //                 'order_id'            => $order_id,
    //                 'user_id'             => $subscription->user_id,
    //                 'pause_start'         => $pauseStartDate->toDateString(),
    //                 'pause_end'           => $pauseEndDate->toDateString(),
    //                 'planned_paused_days' => $plannedPausedDays,
    //                 'base_end'            => $baseEnd->toDateString(),
    //                 'new_end'             => $newEndDate->toDateString(),
    //                 'edit_log_id'         => $editPausedLog?->id,
    //                 'status_saved'        => $subscription->status,
    //             ]);

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Subscription pause details saved.',
    //                 'data' => [
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                     'status'           => $subscription->status,
    //                 ]
    //             ], 200);
    //         });
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => 404,
    //             'message' => 'Subscription not found or inactive.',
    //             'error'   => $e->getMessage()
    //         ], 404);
    //     } catch (\Throwable $e) {
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error'    => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while saving the pause.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function pause(Request $request, $order_id)
    // {
    //     // 1) Validate input (inclusive window)
    //     $request->validate([
    //         'pause_start_date' => ['required', 'date'],
    //         'pause_end_date'   => ['required', 'date'],
    //         'edit'             => ['nullable', 'string'], // "yes" or "no"
    //         'pause_log_id'     => ['nullable', 'integer'],
    //     ]);

    //     $pauseStartDate = Carbon::parse($request->pause_start_date)->startOfDay();
    //     $pauseEndDate   = Carbon::parse($request->pause_end_date)->startOfDay();

    //     if ($pauseEndDate->lt($pauseStartDate)) {
    //         return response()->json([
    //             'success' => 422,
    //             'message' => 'Pause end date must be on/after the start date.',
    //         ], 422);
    //     }

    //     // Parse edit flag
    //     $editRaw  = strtolower((string) $request->input('edit', 'no'));
    //     $isEdit   = in_array($editRaw, ['yes','y','true','1'], true);
    //     $targetId = $request->input('pause_log_id'); // optional when edit=yes

    //     // Inclusive day count
    //     $plannedPausedDays = $pauseStartDate->diffInDays($pauseEndDate) + 1;
    //     $today             = Carbon::today();

    //     try {
    //         return DB::transaction(function () use (
    //             $order_id, $pauseStartDate, $pauseEndDate, $plannedPausedDays, $today, $isEdit, $targetId
    //         ) {
    //             // 2) Lock subscription row
    //             $subscription = Subscription::where('order_id', $order_id)
    //                 ->whereIn('status', ['active', 'paused'])
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             // Save prior window to decide today's status if we add a future pause
    //             $priorStart = $subscription->pause_start_date ? Carbon::parse($subscription->pause_start_date)->startOfDay() : null;
    //             $priorEnd   = $subscription->pause_end_date   ? Carbon::parse($subscription->pause_end_date)->startOfDay()   : null;
    //             $wasPausedToday = $priorStart && $priorEnd ? $today->between($priorStart, $priorEnd, true) : false;

    //             // ---------- Decide which paused-log row to EDIT (only if edit=yes) ----------
    //             $editPausedLog = null;
    //             if ($isEdit) {
    //                 // Prefer explicit id if supplied and open (no later resumed)
    //                 if ($targetId) {
    //                     $candidate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                         ->where('order_id', $order_id)
    //                         ->where('id', $targetId)
    //                         ->where('action', 'paused')
    //                         ->lockForUpdate()
    //                         ->first();

    //                     if ($candidate) {
    //                         $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                             ->where('order_id', $order_id)
    //                             ->where('action', 'resumed')
    //                             ->where('id', '>', $candidate->id)
    //                             ->exists();
    //                         if (!$hasResumedAfter) $editPausedLog = $candidate;
    //                     }
    //                 }

    //                 // If no explicit, try matching the subscription's current window
    //                 if (!$editPausedLog && $priorStart && $priorEnd) {
    //                     $candidate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                         ->where('order_id', $order_id)
    //                         ->where('action', 'paused')
    //                         ->whereDate('pause_start_date', $priorStart->toDateString())
    //                         ->whereDate('pause_end_date',   $priorEnd->toDateString())
    //                         ->latest('id')
    //                         ->lockForUpdate()
    //                         ->first();

    //                     if ($candidate) {
    //                         $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                             ->where('order_id', $order_id)
    //                             ->where('action', 'resumed')
    //                             ->where('id', '>', $candidate->id)
    //                             ->exists();
    //                         if (!$hasResumedAfter) $editPausedLog = $candidate;
    //                     }
    //                 }

    //                 // If still not found, fall back to the latest OPEN paused row
    //                 if (!$editPausedLog) {
    //                     $latestPaused = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                         ->where('order_id', $order_id)
    //                         ->where('action', 'paused')
    //                         ->latest('id')
    //                         ->lockForUpdate()
    //                         ->first();

    //                     if ($latestPaused) {
    //                         $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                             ->where('order_id', $order_id)
    //                             ->where('action', 'resumed')
    //                             ->where('id', '>', $latestPaused->id)
    //                             ->exists();
    //                         if (!$hasResumedAfter) $editPausedLog = $latestPaused;
    //                     }
    //                 }
    //             }

    //             // 4) Overlap guard ONLY when subscription is currently paused (same as before)
    //             if ($subscription->status === 'paused') {
    //                 $overlapQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
    //                     ->where('order_id', $order_id)
    //                     ->where('action', 'paused')
    //                     ->when($editPausedLog, function ($q) use ($editPausedLog) {
    //                         // ignore the row we're editing
    //                         $q->where('id', '!=', $editPausedLog->id);
    //                     })
    //                     ->where(function ($q) use ($pauseStartDate, $pauseEndDate) {
    //                         $start = $pauseStartDate->toDateString();
    //                         $end   = $pauseEndDate->toDateString();

    //                         $q->whereBetween(DB::raw('DATE(pause_start_date)'), [$start, $end])
    //                         ->orWhereBetween(DB::raw('DATE(pause_end_date)'),   [$start, $end])
    //                         ->orWhere(function ($q2) use ($start, $end) {
    //                             $q2->whereDate('pause_start_date', '<=', $start)
    //                                 ->whereDate('pause_end_date',   '>=', $end);
    //                         });
    //                     });

    //                 if ($overlapQuery->exists()) {
    //                     return response()->json([
    //                         'success' => 422,
    //                         'message' => 'This pause window overlaps with another pause request for the same subscription.',
    //                     ], 422);
    //                 }
    //             }

    //             // 5) Determine base end date BEFORE applying this pause
    //             $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();
    //             $baseEnd = clone $effectiveEnd;

    //             // If editing an existing open paused log, undo its previous extension first
    //             if ($isEdit && $editPausedLog) {
    //                 $prevPausedDays = (int) ($editPausedLog->paused_days ?? 0);
    //                 if ($prevPausedDays > 0) {
    //                     $baseEnd = (clone $effectiveEnd)->subDays($prevPausedDays);
    //                 }
    //             }
    //             // If not editing, we do NOT reverse — we’re stacking a new pause onto the current effective end.

    //             // 6) Compute new end date = base + plannedPausedDays
    //             $newEndDate = (clone $baseEnd)->addDays($plannedPausedDays);

    //             // 7) Upsert the pause log
    //             if ($isEdit && $editPausedLog) {
    //                 $editPausedLog->update([
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             } else {
    //                 SubscriptionPauseResumeLog::create([
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'action'           => 'paused',
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                 ]);
    //             }

    //             // 8) Update subscription window + new_date
    //             $subscription->pause_start_date = $pauseStartDate->toDateString();
    //             $subscription->pause_end_date   = $pauseEndDate->toDateString();
    //             $subscription->new_date         = $newEndDate->toDateString();

    //             // Status logic:
    //             $isPausedTodayByNew = $today->between($pauseStartDate, $pauseEndDate, true); // inclusive

    //             if ($isEdit) {
    //                 // Editing: reflect only the NEW window
    //                 $subscription->status = $isPausedTodayByNew ? 'paused' : 'active';
    //             } else {
    //                 // New row: keep paused if either prior window or new window covers today
    //                 $subscription->status = ($isPausedTodayByNew || $wasPausedToday) ? 'paused' : 'active';
    //             }

    //             $subscription->save();

    //             Log::info('Pausing subscription (edit flag)', [
    //                 'order_id'            => $order_id,
    //                 'user_id'             => $subscription->user_id,
    //                 'pause_start'         => $pauseStartDate->toDateString(),
    //                 'pause_end'           => $pauseEndDate->toDateString(),
    //                 'planned_paused_days' => $plannedPausedDays,
    //                 'base_end'            => $baseEnd->toDateString(),
    //                 'new_end'             => $newEndDate->toDateString(),
    //                 'had_new_date'        => (bool) $subscription->new_date,
    //                 'status_saved'        => $subscription->status,
    //                 'is_edit'             => $isEdit,
    //                 'edit_log_id'         => $isEdit ? ($editPausedLog->id ?? null) : null,
    //             ]);

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => $isEdit ? 'Pause updated successfully.' : 'Pause created successfully.',
    //                 'data' => [
    //                     'subscription_id'  => $subscription->subscription_id,
    //                     'order_id'         => $order_id,
    //                     'pause_start_date' => $pauseStartDate->toDateString(),
    //                     'pause_end_date'   => $pauseEndDate->toDateString(),
    //                     'paused_days'      => $plannedPausedDays,
    //                     'new_end_date'     => $newEndDate->toDateString(),
    //                     'status'           => $subscription->status,
    //                     'is_edit'          => $isEdit,
    //                 ]
    //             ], 200);
    //         });
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => 404,
    //             'message' => 'Subscription not found or inactive.',
    //             'error'   => $e->getMessage()
    //         ], 404);
    //     } catch (\Throwable $e) {
    //         Log::error('Error pausing subscription', [
    //             'order_id' => $order_id,
    //             'error'    => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'An error occurred while saving the pause.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function pause(Request $request, $order_id)
    {
        // 1) Validate input (inclusive window)
        $request->validate([
            'pause_start_date' => ['required', 'date'],
            'pause_end_date'   => ['required', 'date'],
            'edit'             => ['nullable', 'string'], // "yes" or "no"
            'pause_log_id'     => ['nullable', 'integer'],
        ]);

        $pauseStartDate = Carbon::parse($request->pause_start_date)->startOfDay();
        $pauseEndDate   = Carbon::parse($request->pause_end_date)->startOfDay();

        if ($pauseEndDate->lt($pauseStartDate)) {
            return response()->json([
                'success' => 422,
                'message' => 'Pause end date must be on/after the start date.',
            ], 422);
        }

        // Parse edit flag
        $editRaw  = strtolower((string) $request->input('edit', 'no'));
        $isEdit   = in_array($editRaw, ['yes','y','true','1'], true);
        $targetId = $request->input('pause_log_id'); // optional when edit=yes

        // Inclusive day count
        $plannedPausedDays = $pauseStartDate->diffInDays($pauseEndDate) + 1;
        $today             = Carbon::today();

        try {
            return DB::transaction(function () use (
                $order_id, $pauseStartDate, $pauseEndDate, $plannedPausedDays, $today, $isEdit, $targetId
            ) {
                // 2) Lock subscription row
                $subscription = Subscription::where('order_id', $order_id)
                    ->whereIn('status', ['active', 'paused'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Save prior window
                $priorStart = $subscription->pause_start_date ? Carbon::parse($subscription->pause_start_date)->startOfDay() : null;
                $priorEnd   = $subscription->pause_end_date   ? Carbon::parse($subscription->pause_end_date)->startOfDay()   : null;
                $wasPausedToday = $priorStart && $priorEnd ? $today->between($priorStart, $priorEnd, true) : false;

                // ---------- Decide which paused-log row to EDIT ----------
                $editPausedLog = null;
                if ($isEdit) {
                    if ($targetId) {
                        $candidate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                            ->where('order_id', $order_id)
                            ->where('id', $targetId)
                            ->where('action', 'paused')
                            ->lockForUpdate()
                            ->first();

                        if ($candidate) {
                            $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                                ->where('order_id', $order_id)
                                ->where('action', 'resumed')
                                ->where('id', '>', $candidate->id)
                                ->exists();
                            if (!$hasResumedAfter) $editPausedLog = $candidate;
                        }
                    }

                    if (!$editPausedLog && $priorStart && $priorEnd) {
                        $candidate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                            ->where('order_id', $order_id)
                            ->where('action', 'paused')
                            ->whereDate('pause_start_date', $priorStart->toDateString())
                            ->whereDate('pause_end_date',   $priorEnd->toDateString())
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();

                        if ($candidate) {
                            $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                                ->where('order_id', $order_id)
                                ->where('action', 'resumed')
                                ->where('id', '>', $candidate->id)
                                ->exists();
                            if (!$hasResumedAfter) $editPausedLog = $candidate;
                        }
                    }

                    if (!$editPausedLog) {
                        $latestPaused = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                            ->where('order_id', $order_id)
                            ->where('action', 'paused')
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();

                        if ($latestPaused) {
                            $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                                ->where('order_id', $order_id)
                                ->where('action', 'resumed')
                                ->where('id', '>', $latestPaused->id)
                                ->exists();
                            if (!$hasResumedAfter) $editPausedLog = $latestPaused;
                        }
                    }
                }

                // 4) Overlap guard ONLY when subscription is currently paused
                if ($subscription->status === 'paused') {
                    $overlapQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                        ->where('order_id', $order_id)
                        ->where('action', 'paused')
                        ->when($editPausedLog, function ($q) use ($editPausedLog) {
                            $q->where('id', '!=', $editPausedLog->id);
                        })
                        ->where(function ($q) use ($pauseStartDate, $pauseEndDate) {
                            $start = $pauseStartDate->toDateString();
                            $end   = $pauseEndDate->toDateString();

                            $q->whereBetween(DB::raw('DATE(pause_start_date)'), [$start, $end])
                            ->orWhereBetween(DB::raw('DATE(pause_end_date)'),   [$start, $end])
                            ->orWhere(function ($q2) use ($start, $end) {
                                $q2->whereDate('pause_start_date', '<=', $start)
                                    ->whereDate('pause_end_date',   '>=', $end);
                            });
                        });

                    if ($overlapQuery->exists()) {
                        return response()->json([
                            'success' => 422,
                            'message' => 'This pause window overlaps with another pause request for the same subscription.',
                        ], 422);
                    }
                }

                // 5) Determine base end date
                $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();
                $baseEnd = clone $effectiveEnd;

                $prevPausedDays = 0;
                if ($isEdit && $editPausedLog) {
                    $prevPausedDays = (int) ($editPausedLog->paused_days ?? 0);
                    if ($prevPausedDays > 0) {
                        $baseEnd = (clone $effectiveEnd)->subDays($prevPausedDays);
                    }
                }

                // 6) Compute new end date
                $newEndDate = (clone $baseEnd)->addDays($plannedPausedDays);

                // 7) Upsert the pause log
                if ($isEdit && $editPausedLog) {
                    $editPausedLog->update([
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

                /**
                 * 7.5) ✅ Pending/Renew shift by EDIT DELTA
                 * deltaDays:
                 *   - new pause:  = plannedPausedDays
                 *   - edit pause: = plannedPausedDays - prevPausedDays  (can be negative)
                 */
                $deltaDays = $isEdit && $editPausedLog ? ($plannedPausedDays - $prevPausedDays) : $plannedPausedDays;

                $pendingUpdated = false;
                $pendingBefore  = null;
                $pendingAfter   = null;

                if ($deltaDays !== 0) {
                    // If you want to shift ALL future rows, swap first() -> get() and loop.
                    $pendingRenew = Subscription::where('user_id', $subscription->user_id)
                        ->whereIn('status', ['pending', 'renew'])
                        ->lockForUpdate()
                        ->orderBy('start_date')
                        ->first();

                    if ($pendingRenew) {
                        $pendingBefore = [
                            'start_date' => $pendingRenew->start_date,
                            'end_date'   => $pendingRenew->end_date,
                            'new_date'   => $pendingRenew->new_date,
                            'status'     => $pendingRenew->status,
                        ];

                        $pendingRenew->start_date = Carbon::parse($pendingRenew->start_date)
                            ->startOfDay()->addDays($deltaDays)->toDateString();

                        $pendingRenew->end_date = Carbon::parse($pendingRenew->end_date)
                            ->startOfDay()->addDays($deltaDays)->toDateString();

                        if (!empty($pendingRenew->new_date)) {
                            $pendingRenew->new_date = Carbon::parse($pendingRenew->new_date)
                                ->startOfDay()->addDays($deltaDays)->toDateString();
                        }

                        $pendingRenew->save();
                        $pendingUpdated = true;

                        $pendingAfter = [
                            'start_date' => $pendingRenew->start_date,
                            'end_date'   => $pendingRenew->end_date,
                            'new_date'   => $pendingRenew->new_date,
                            'status'     => $pendingRenew->status,
                            'delta_days' => $deltaDays,
                        ];
                    }
                }

                // 8) Update subscription itself
                $subscription->pause_start_date = $pauseStartDate->toDateString();
                $subscription->pause_end_date   = $pauseEndDate->toDateString();
                $subscription->new_date         = $newEndDate->toDateString();

                $isPausedTodayByNew = $today->between($pauseStartDate, $pauseEndDate, true);
                if ($isEdit) {
                    $subscription->status = $isPausedTodayByNew ? 'paused' : 'active';
                } else {
                    $subscription->status = ($isPausedTodayByNew || $wasPausedToday) ? 'paused' : 'active';
                }

                $subscription->save();

                return response()->json([
                    'success' => 200,
                    'message' => $isEdit ? 'Pause updated successfully.' : 'Pause created successfully.',
                    'data' => [
                        'subscription_id'  => $subscription->subscription_id,
                        'order_id'         => $order_id,
                        'pause_start_date' => $pauseStartDate->toDateString(),
                        'pause_end_date'   => $pauseEndDate->toDateString(),
                        'paused_days'      => $plannedPausedDays,
                        'new_end_date'     => $newEndDate->toDateString(),
                        'status'           => $subscription->status,
                        'is_edit'          => $isEdit,
                        'pending_updated'  => $pendingUpdated,
                        'pending_snapshot' => [
                            'before' => $pendingBefore,
                            'after'  => $pendingAfter,
                        ],
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
                'message' => 'An error occurred while saving the pause.',
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
                    'order_id'     => $order_id,
                    'user_id'      => $subscription->user_id,
                    'resume_date'  => $resumeDate->toDateString(),
                    'pause_start'  => $pauseStartDate->toDateString(),
                    'pause_end'    => $pauseEndDate->toDateString(),
                    'current_end'  => $currentEndDate->toDateString(),
                    'had_new_date' => (bool) $subscription->new_date,
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
                if (!empty($subscription->new_date)) {
                    // pause() already extended by planned days ⇒ roll back unused remainder
                    $newEndDate = (clone $currentEndDate)->subDays($remainingPausedDays);
                } else {
                    // legacy case: extend only by actually paused days
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
                        'had_new_date_at_pause' => true,
                    ]),
                ]);

                Log::info('Subscription resumed successfully', [
                    'order_id'     => $order_id,
                    'new_end_date' => $newEndDate->toDateString(),
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

    public function deletePause(Request $request, $order_id)
    {
        $pauseLogId = $request->input('pause_log_id'); // optional

        try {
            return DB::transaction(function () use ($order_id, $pauseLogId) {
                // 1) Lock the current subscription (expected states)
                $subscription = Subscription::where('order_id', $order_id)
                    ->whereIn('status', ['active', 'paused'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2) Identify which paused log to delete (specific or latest)
                $pausedLogQuery = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                    ->where('order_id', $order_id)
                    ->where('action', 'paused');

                if ($pauseLogId) {
                    $pausedLogQuery->where('id', $pauseLogId);
                } else {
                    $pausedLogQuery->latest('id');
                }

                /** @var \App\Models\SubscriptionPauseResumeLog $pausedLog */
                $pausedLog = $pausedLogQuery->lockForUpdate()->first();

                if (!$pausedLog) {
                    return response()->json([
                        'success' => 404,
                        'message' => 'Pause request not found for this order/subscription.',
                    ], 404);
                }

                // 3) Block deletion if this pause has a subsequent 'resumed'
                $hasResumedAfter = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                    ->where('order_id', $order_id)
                    ->where('action', 'resumed')
                    ->where('id', '>', $pausedLog->id)
                    ->exists();

                if ($hasResumedAfter) {
                    return response()->json([
                        'success' => 422,
                        'message' => 'This pause has already been resumed and cannot be deleted.',
                    ], 422);
                }

                // 4) Roll back the extension on the CURRENT subscription
                $pausedDays   = (int) ($pausedLog->paused_days ?? 0);
                $effectiveEnd = Carbon::parse($subscription->new_date ?: $subscription->end_date)->startOfDay();
                $revertedEnd  = (clone $effectiveEnd)->subDays($pausedDays);
                $origEnd      = Carbon::parse($subscription->end_date)->startOfDay();
                $newDateToStore = $revertedEnd->equalTo($origEnd) ? null : $revertedEnd->toDateString();

                // 5) Roll back future PENDING subscriptions for the SAME USER
                //    (If you also want 'renew', change to ->whereIn('status', ['pending','renew']))
                $rolledBackRows = [];
                if ($pausedDays !== 0) {
                    $futureSubs = Subscription::where('user_id', $subscription->user_id)
                        ->where('status', 'pending') // <-- fixed (was whereIn with string)
                        ->lockForUpdate()
                        ->get();

                    foreach ($futureSubs as $fs) {
                        $before = [
                            'subscription_id' => $fs->subscription_id,
                            'order_id'        => $fs->order_id,
                            'start_date'      => $fs->start_date,
                            'end_date'        => $fs->end_date,
                            'new_date'        => $fs->new_date,
                            'status'          => $fs->status,
                        ];

                        $fs->start_date = Carbon::parse($fs->start_date)->startOfDay()->subDays($pausedDays)->toDateString();
                        $fs->end_date   = Carbon::parse($fs->end_date)->startOfDay()->subDays($pausedDays)->toDateString();
                        if (!empty($fs->new_date)) {
                            $fs->new_date = Carbon::parse($fs->new_date)->startOfDay()->subDays($pausedDays)->toDateString();
                        }
                        $fs->save();

                        $rolledBackRows[] = [
                            'before' => $before,
                            'after'  => [
                                'start_date' => $fs->start_date,
                                'end_date'   => $fs->end_date,
                                'new_date'   => $fs->new_date,
                                'status'     => $fs->status,
                            ],
                            'shift_reversed_days' => $pausedDays,
                        ];
                    }
                }

                // 6) If the subscription's current window matches this paused log, clear it
                $subPauseStart = $subscription->pause_start_date ? Carbon::parse($subscription->pause_start_date)->toDateString() : null;
                $subPauseEnd   = $subscription->pause_end_date ? Carbon::parse($subscription->pause_end_date)->toDateString() : null;

                $matchesCurrentWindow = (
                    $subPauseStart === ($pausedLog->pause_start_date ? Carbon::parse($pausedLog->pause_start_date)->toDateString() : null) &&
                    $subPauseEnd   === ($pausedLog->pause_end_date   ? Carbon::parse($pausedLog->pause_end_date)->toDateString()   : null)
                );

                if ($matchesCurrentWindow) {
                    $subscription->pause_start_date = null;
                    $subscription->pause_end_date   = null;
                }

                // 7) If we are currently inside this deleted window, flip back to active
                $today = Carbon::today();
                $inThisWindowToday = false;
                if ($pausedLog->pause_start_date && $pausedLog->pause_end_date) {
                    $start = Carbon::parse($pausedLog->pause_start_date)->startOfDay();
                    $end   = Carbon::parse($pausedLog->pause_end_date)->startOfDay();
                    $inThisWindowToday = $today->between($start, $end, true);
                }

                $subscription->new_date = $newDateToStore;
                if ($inThisWindowToday || $subscription->status === 'paused') {
                    $subscription->status = 'active';
                }
                $subscription->save();

                // 8) Delete the pause log itself
                $deletedId = $pausedLog->id;
                $pausedLog->delete();

                Log::info('Deleted pause request with future pending rollback', [
                    'order_id'         => $order_id,
                    'subscription_id'  => $subscription->subscription_id,
                    'deleted_log_id'   => $deletedId,
                    'new_date_after'   => $subscription->new_date,
                    'status_after'     => $subscription->status,
                    'rolled_back_rows' => $rolledBackRows,
                ]);

                return response()->json([
                    'success' => 200,
                    'message' => 'Pause request deleted; subscription and pending orders rolled back.',
                    'data' => [
                        'subscription_id'   => $subscription->subscription_id,
                        'order_id'          => $order_id,
                        'new_effective_end' => $subscription->new_date ?: $subscription->end_date,
                        'status'            => $subscription->status,
                        'rolled_back'       => $rolledBackRows,
                    ],
                ], 200);
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => 404,
                'message' => 'Subscription not found or not deletable.',
                'error'   => $e->getMessage(),
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error deleting pause request', [
                'order_id' => $order_id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json([
                'success' => 500,
                'message' => 'An error occurred while deleting the pause request.',
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

    public function cancel(Request $request, $request_id)
    {
        $validated = $request->validate([
            'cancel_by'     => 'nullable|string',
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        try {
            $result = DB::transaction(function () use ($request_id, $validated) {
                // Lock the row to avoid race conditions
                $flowerRequest = FlowerRequest::where('request_id', $request_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Update fields
                $flowerRequest->status        = 'Rejected';
                $flowerRequest->cancel_by     = $validated['cancel_by'];
                $flowerRequest->cancel_reason = $validated['cancel_reason'];
                $flowerRequest->save();

                return $flowerRequest;
            });

            return response()->json([
                'success' => true,
                'message' => 'Customize order cancelled successfully.',
                'data'    => [
                    'request_id'     => $result->request_id,
                    'status'         => $result->status,
                    'cancel_by'      => $result->cancel_by,
                    'cancel_reason'  => $result->cancel_reason,
                    'updated_at'     => $result->updated_at,
                ],
            ], 200);

        } catch (ModelNotFoundException $e) {
            // If you strictly want only 200 / 500, you can convert this to 500.
            // Returning 404 is often better, but following your ask:
            Log::warning('FlowerRequest not found for cancel', ['request_id' => $request_id]);
            return response()->json([
                'success' => false,
                'message' => 'Customize order not found.',
            ], 500);

        } catch (\Throwable $e) {
            Log::error('Cancel customize order failed', [
                'request_id' => $request_id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }

    
    public function cancelSubscription(Request $request, $id)
    {
        try {
            // Find subscription by primary auto-increment ID
            $subscription = Subscription::findOrFail($id);

            // Update status
            $subscription->status = 'cancelled_by_user';
            $subscription->is_active = false; // Optional flag
            $subscription->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
                'data'    => $subscription
            ], 200);

        } catch (Exception $e) {
            Log::error('Cancel subscription failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription'
            ], 500);
        }
    }


}
