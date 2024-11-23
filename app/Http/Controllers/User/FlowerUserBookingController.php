<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionPauseResumeLog;

use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Bankdetail;
use App\Models\Childrendetail;
use App\Models\Addressdetail;
use App\Models\IdcardDetail;
use App\Models\Poojalist;
use App\Models\UserAddress;
use App\Models\Profile;
use App\Models\Poojadetails;
use App\Models\Locality;
use App\Models\PoojaUnit;
use App\Models\FlowerRequest;
use App\Models\FlowerRequestItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FlowerUserBookingController extends Controller
{

    public function flower() {
        // Fetch banners from the external API
        $responseBanners = Http::get('https://pandit.33crores.com/api/app-banners');

        // Check if the response is successful and filter based on the 'flower' category
        $banners = $responseBanners->successful() && isset($responseBanners->json()['data'])
            ? collect($responseBanners->json()['data'])->filter(fn($banner) => isset($banner['category']) && strtolower($banner['category']) === 'flower')
            : collect();
           
            
        // Fetch other data for the view
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->take(3)
                        ->get();
        $otherpoojas = Poojalist::where('status', 'active')
                        ->whereNull('pooja_date')
                        ->take(9)
                        ->get();
        $products = FlowerProduct::where('status', 'active')
                        ->where('category', 'Subscription')
                        ->get();
        $customizedpps = FlowerProduct::where('status', 'active')
                        ->where('category', 'Immediateproduct')
                        ->get();
                        // $password = 'jagannath@1911@2024';
                        // $hashedPassword = Hash::make($password);
                        // dd($hashedPassword);
        return view("user/flower", compact('upcomingPoojas', 'otherpoojas', 'products', 'banners','customizedpps'));
    }
    
    public function show($product_id)
    {
        // dd($product_id);
        // Retrieve the product details by product_id
        // $product = FlowerProduct::findOrFail($product_id);
        
        $product = FlowerProduct::where('product_id', $product_id)->firstOrFail();
        $localities = Locality::where('status', 'active')->get();
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();
        // $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();

        // Pass the product and subscription details to the view
        return view('user.flower-subscription-checkout', compact('localities','product','addresses','user'));
    }

    public function cutsomizedcheckout($product_id)
    {
        // dd($product_id);
        // Retrieve the product details by product_id
        // $product = FlowerProduct::findOrFail($product_id);
        $singleflowers = FlowerProduct::where('status', 'active')
                        ->where('category', 'Flower')
                        ->get();
        $Poojaunits = PoojaUnit::where('status', 'active')
                       
                        ->get();
        $localities = Locality::where('status', 'active')->get();
        $product = FlowerProduct::where('product_id', $product_id)->firstOrFail();
       
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();
        // $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();

        // Pass the product and subscription details to the view
        return view('user.flower-customized-checkout', compact('Poojaunits','singleflowers','product','addresses','user','localities'));
    }
    public function processBooking(Request $request)
    {

        \Log::info('processBooking method called');

        // Log received payment ID
        \Log::info('Received payment ID:', ['payment_id' => $request->payment_id]);
    
        $user = Auth::guard('users')->user();
        \Log::info('Authenticated user ID:', ['user_id' => $user->userid]);
    
        // Log the input data for verification
        \Log::info('Input data:', $request->all());
        // Ensure the user is authenticated
        // $user = Auth::guard('users')->user();
        $productId = $request->product_id; // Assuming you pass product_id in the form
        
        $orderId = 'ORD-' . strtoupper(Str::random(12));
        $addressId = $request->address_id;
        $suggestion = $request->suggestion;

        // Log the order creation attempt
        \Log::info('Creating order', ['order_id' => $orderId, 'product_id' => $productId, 'user_id' => $user->userid, 'address_id' => $addressId]);

        // Create the order
        try {
            $order = Order::create([
                'order_id' => $orderId,
                'product_id' => $productId,
                'user_id' => $user->userid,
                'quantity' => 1,
                'total_price' => $request->price,
                'address_id' => $addressId,
                'suggestion' => $suggestion,
            ]);
            \Log::info('Order created successfully', ['order' => $order]);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create order');
        }

        // Calculate subscription start and end dates
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now(); // Default to now if no start date is provided
        $duration = $request->duration; // Duration is 1 for 30 days, 3 for 60 days, 6 for 90 days

        // Calculate end date based on subscription duration
        if ($duration == 1) {
            $endDate = $startDate->copy()->addDays(29); // For 1, add 30 days
        } else if ($duration == 3) {
            $endDate = $startDate->copy()->addDays(89); // For 3, add 90 days
        } else if ($duration == 6) {
            $endDate = $startDate->copy()->addDays(179); // For 6, add 180 days
        } else {
            // Handle unexpected duration value
            \Log::error('Invalid subscription duration', ['duration' => $duration]);
            return back()->with('error', 'Invalid subscription duration');
        }

        // Log subscription creation
        \Log::info('Creating subscription', ['user_id' => $user->userid, 'product_id' => $productId, 'start_date' => $startDate, 'end_date' => $endDate]);

        // Create the subscription
        $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
        try {
            Subscription::create([
                'subscription_id' => $subscriptionId,
                'user_id' => $user->userid,
                'order_id' => $orderId,
                'product_id' => $productId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'status' => 'active'
            ]);
            \Log::info('Subscription created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create subscription');
        }

        // Process payment details and create payment record
        try {
            FlowerPayment::create([
                'order_id' => $orderId,
                'payment_id' => $request->payment_id,
                'user_id' => $user->userid,
                'payment_method' => "Razorpay",
                'paid_amount' => $request->price,
                'payment_status' => "paid",
            ]);
            \Log::info('Payment recorded successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to record payment', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to record payment');
        }

        // Redirect or respond as needed
        // return redirect()->route('flower-booking.success')->with('success', 'Booking successful');

        return redirect()->back()->with('success', 'Booking successful');
    
    }

    // public function showSuccessPage($order_id)
    // {
    //     $booking = Order::with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address'])->findOrFail($order_id);
    //     // dd($booking);
    //     return view('user.flower-booking-success', compact('booking'));
    // }



    public function subscriptionhistory() {
        // Get the authenticated user ID using the 'api' guard
        $userId = Auth::guard('users')->user()->userid;
    
        // Fetch standalone orders for the authenticated user (orders without request_id)
        $subscriptionsOrder = Order::whereNull('request_id')
            ->where('user_id', $userId)
            ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails', 'pauseResumeLogs'])
            ->orderBy('id', 'desc')
            ->get();
    
        // Map to add the product_image_url to each order's flowerProduct
        $subscriptionsOrder = $subscriptionsOrder->map(function ($order) {
            if ($order->flowerProduct) {
                // Ensure flowerProduct exists before accessing product_image
                $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image); // Generate full URL for the photo
            }
            return $order;
        });
    
        // Pass the orders to the view
        return view('user.subscription-history', compact('subscriptionsOrder'));
    }
    public function viewSubscriptionOrderDetails($order_id)
    {
        // Fetch the order details using the order_id
        $order = Order::where('order_id', $order_id)
            ->with(['subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails', 'pauseResumeLogs'])
            ->firstOrFail(); // Ensure the order exists or fail
        
        // Add the product image URL
        if ($order->flowerProduct) {
            $order->flowerProduct->product_image_url = asset('storage/' . $order->flowerProduct->product_image);
        }

        // Pass the order to the view
        return view('user.view-subscription-details', compact('order'));
    }

    public function requestedorderhistory(){
        $userId = Auth::guard('users')->user()->userid;
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
        ->get();
        
        return view('user.requested-order-history', compact('requestedOrders'));

    }
    public function requestedOrderDetails($id)
{
    $userId = Auth::guard('users')->user()->userid;
    
    // Fetch the requested order by ID and include its relationships
    $requestedOrder = FlowerRequest::where('id', $id)
        ->where('user_id', $userId)
        ->with([
            'order.flowerPayments',
            'flowerProduct',
            'user',
            'address.localityDetails',
            'flowerRequestItems'
        ])
        ->firstOrFail();

    return view('user.view-requested-order-details', compact('requestedOrder'));
}

    public function userflowerdashboard(){
        return view('user.user-flower-dashboard');
    }



    // customized order
    public function customizedstore(Request $request)
    {
        $user = Auth::guard('users')->user();

        // Generate the request ID
        $requestId = 'REQ-' . strtoupper(Str::random(12));

        // Create the flower request and store the request ID
        $flowerRequest = FlowerRequest::create([
            'request_id' => $requestId,
            'product_id' => $request->product_id,
            'user_id' => $user->userid,
            'address_id' => $request->address_id,
            'description' => $request->description,
            'suggestion' => $request->suggestion,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending'
        ]);

        // Loop through flower names, units, and quantities to create FlowerRequestItem entries
        foreach ($request->item as $index => $flowerName) {
            FlowerRequestItem::create([
                'flower_request_id' => $requestId,
                'flower_name' => $flowerName,
                'flower_unit' => $request->unit[$index],
                'flower_quantity' => $request->quantity[$index],
            ]);
        }

        // Return success message using SweetAlert and redirect
        // return redirect()->route('flower.history')->with('success', 'Flower request created successfully!');
        return redirect()->back()->with('message', 'Flower request created successfully!');

    }

    public function RequestpaymentCallback(Request $request)
    {
        try {
            $request->validate([
                'razorpay_payment_id' => 'required',
                // 'razorpay_order_id' => 'required',
                'request_id' => 'required',
            ]);
    
            $order = Order::where('request_id', $request->request_id)->firstOrFail();
    
            // Save payment details
            FlowerPayment::create([
                'order_id' => $order->order_id,
                'payment_id' => $request->razorpay_payment_id,
                'user_id' => $order->user_id,
                'payment_method' => 'Razorpay',
                'paid_amount' => $order->total_price,
                'payment_status' => 'paid',
            ]);
    
            // Update FlowerRequest status
            $flowerRequest = FlowerRequest::where('request_id', $request->request_id)->firstOrFail();
            if ($flowerRequest->status === 'approved') {
                $flowerRequest->status = 'paid';
                $flowerRequest->save();
            }
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Payment error: ' . $e->getMessage());
    
            return response()->json(['success' => false, 'message' => 'Payment processing failed.']);
        }
    }


    public function pause(Request $request, $order_id)
    {
        try {
            // Find the subscription by order_id
            $subscription = Subscription::where('order_id', $order_id)->firstOrFail();
            
            // Validate input dates
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

            // Update the subscription status and new date field
            $subscription->status = 'paused';
            $subscription->pause_start_date = $pauseStartDate;
            $subscription->pause_end_date = $pauseEndDate;
            $subscription->new_date = $newEndDate; // Update with recalculated end date
            $subscription->is_active = true;

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
                'message' => 'Subscription paused successfully.',
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
                'message' => 'An error occurred while pausing the subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
    
            return response()->json([
                'success' => 200,
                'message' => 'Subscription resumed successfully.',
                'subscription' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'An error occurred while resuming the subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    

    
}