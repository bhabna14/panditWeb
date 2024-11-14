<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Subscription;
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
    
        return view("user/flower", compact('upcomingPoojas', 'otherpoojas', 'products', 'banners'));
    }
    
    public function show($product_id)
    {
        // dd($product_id);
        // Retrieve the product details by product_id
        // $product = FlowerProduct::findOrFail($product_id);
        
        $product = FlowerProduct::where('product_id', $product_id)->firstOrFail();
       
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();
        // $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();

        // Pass the product and subscription details to the view
        return view('user.flower-subscription-checkout', compact('product','addresses','user'));
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
    
    
}