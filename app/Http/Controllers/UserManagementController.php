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
        $user_details = User::get();

        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->get();
    
        return view('demo-order-details', compact('flowers','user_details'));
    }
    
    public function getUserAddresses($userId)
    {
        $addresses = UserAddress::where('user_id', $userId)
            ->where('status', 'active')
            ->get(['id', 'address_type', 'apartment_flat_plot', 'locality', 'landmark', 'city', 'state', 'country', 'pincode', 'default']);
    
        foreach ($addresses as $address) {
            $address->locality_name = $address->localityDetails->locality_name ?? 'N/A';
        }
    
        return response()->json(['addresses' => $addresses]);
    }


public function handleUserData(Request $request)
{
    try {
        // Start a database transaction
        \DB::beginTransaction();

        // Validate user details
        $validatedUserData = $request->validate([
            'userid' => 'required|string',
            'address_id' => 'required|string',
            'product_id' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        // Generate unique order ID
        $orderId = 'ORD-' . strtoupper(Str::random(12));

        // Create order
        $order = Order::create([
            'user_id' => $validatedUserData['userid'],
            'order_id' => $orderId,
            'product_id' => $validatedUserData['product_id'],
            'quantity' => '1',
            'start_date' => $validatedUserData['start_date'],
            'address_id' => $validatedUserData['address_id'],
            'total_price' => $validatedUserData['paid_amount'],
            'created_at' => $validatedUserData['start_date'],
            
        ]);

        // Generate unique subscription ID
        $subscriptionId = 'SUB-' . strtoupper(Str::random(12));

        // // Calculate subscription start and end dates
        // $startDate = $validatedUserData['start_date'] 
        //     ? Carbon::parse($validatedUserData['start_date']) 
        //     : Carbon::now(); // Convert to Carbon instance or default to now

        // $duration = $validatedUserData['duration'];

        // // Determine the end date based on duration
        // if ($duration == 1) {
        //     $endDate = $startDate->addDays(29); // For 1 month, 30 days
        // } elseif ($duration == 3) {
        //     $endDate = $startDate->addDays(89); // For 3 months, 90 days
        // } elseif ($duration == 6) {
        //     $endDate = $startDate->addDays(179); // For 6 months, 180 days
        // } else {
        //     $endDate = $startDate; // Default case (no duration provided)
        // }

        // Create subscription
        Subscription::create([
            'subscription_id' => $subscriptionId,
            'user_id' => $validatedUserData['userid'],
            'order_id' => $order->order_id,
            'product_id' => $validatedUserData['product_id'],
            'start_date' => $validatedUserData['start_date'],
            'end_date' => $validatedUserData['end_date'],
            'status' => $validatedUserData['status'],
        ]);

        // Add flower payment
        FlowerPayment::create([
            'order_id' => $order->order_id,
            'payment_id' => 'NULL',
            'user_id' => $validatedUserData['userid'],
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
