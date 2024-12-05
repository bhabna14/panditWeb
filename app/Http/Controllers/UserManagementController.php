<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\Locality;
use Illuminate\Support\Str;

use App\Models\Apartment;

use Carbon\Carbon; // Add this at the top of the controller
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade

use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{

    public function demoOrderDetails()
    {
        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->get();
    
        // Get all localities and apartments
        $localities = Locality::where('status', 'active')
            ->select('locality_name', 'unique_code', 'pincode')
            ->get();
    
        $apartments = Apartment::where('status', 'active')->get();
    
        // Group apartments by locality_id
        $apartmentsByLocality = $apartments->groupBy('locality_id');
    
        return view('demo-order-details', compact('localities', 'flowers', 'apartmentsByLocality'));
    }
    
    


public function handleUserData(Request $request)
{
    try {
        // Start a database transaction
        \DB::beginTransaction();

        // Validate user details
        $validatedUserData = $request->validate([
            'name' => 'nullable',
            'mobile_number' => 'required',
            'state' => 'required|string',
            'city' => 'required|string',
            'pincode' => 'required|digits:6',
            'apartment_name' => 'nullable|string',
            'address_type' => 'nullable|string',
            'place_category' => 'nullable|string',
            'duration' => 'nullable|numeric|in:1,3,6',
            'locality' => 'required|string',
            'apartment_flat_plot' => 'required|string',
            'landmark' => 'nullable|string',
            'product_id' => 'nullable',
            'start_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        // Generate unique user ID
        $user_id = 'USER' . rand(10000, 99999);

        // Create the user
        $user = User::create([
            'userid' => $user_id,
            'name' => $validatedUserData['name'],
            'mobile_number' => '+91' . $validatedUserData['mobile_number'],
        ]);

        // Create user address
        $address = UserAddress::create([
            'user_id' => $user->userid,
            'state' => $validatedUserData['state'],
            'city' => $validatedUserData['city'],
            'pincode' => $validatedUserData['pincode'],
            'locality' => $validatedUserData['locality'],
            'apartment_name' => $validatedUserData['apartment_name'],
            'place_category' => $validatedUserData['place_category'],
            'apartment_flat_plot' => $validatedUserData['apartment_flat_plot'],
            'landmark' => $validatedUserData['landmark'] ?? null,
            'address_type' => $validatedUserData['address_type'],
            'country' => 'India',
            'status' => 'active',
        ]);

        // Generate unique order ID
        $orderId = 'ORD-' . strtoupper(Str::random(12));

        // Create order
        $order = Order::create([
            'user_id' => $user->userid,
            'order_id' => $orderId,
            'product_id' => $validatedUserData['product_id'],
            'quantity' => '1',
            'start_date' => $validatedUserData['start_date'],
            'address_id' => $address->id,
            'total_price' => $validatedUserData['paid_amount'],
            
        ]);

        // Generate unique subscription ID
        $subscriptionId = 'SUB-' . strtoupper(Str::random(12));

        // Calculate subscription start and end dates
        $startDate = $validatedUserData['start_date'] 
            ? Carbon::parse($validatedUserData['start_date']) 
            : Carbon::now(); // Convert to Carbon instance or default to now

        $duration = $validatedUserData['duration'];

        // Determine the end date based on duration
        if ($duration == 1) {
            $endDate = $startDate->addDays(29); // For 1 month, 30 days
        } elseif ($duration == 3) {
            $endDate = $startDate->addDays(89); // For 3 months, 90 days
        } elseif ($duration == 6) {
            $endDate = $startDate->addDays(179); // For 6 months, 180 days
        } else {
            $endDate = $startDate; // Default case (no duration provided)
        }

        // Create subscription
        Subscription::create([
            'subscription_id' => $subscriptionId,
            'user_id' => $user->userid,
            'order_id' => $order->order_id,
            'product_id' => $validatedUserData['product_id'],
            'start_date' => $validatedUserData['start_date'],
            'end_date' => $endDate,
            // 'is_active' => true,
            'status' => $validatedUserData['status'],
        ]);

        // Add flower payment
        FlowerPayment::create([
            'order_id' => $order->id,
            'payment_id' => 'NULL',
            'user_id' => $user->userid,
            'payment_method' => 'rozarpay',
            'paid_amount' => $validatedUserData['paid_amount'],
            'payment_status' => 'paid',
        ]);

        // Commit the transaction
        \DB::commit();

        return response()->json([
            'message' => 'User, address, order, subscription, and payment added successfully.',
        ], 200);
    } catch (\Exception $e) {
        // Rollback the transaction on error
        \DB::rollBack();

        return response()->json([
            'message' => 'An error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
