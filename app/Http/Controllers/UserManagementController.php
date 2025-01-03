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

    public function existingUser()
    {
        $user_details = User::get();

        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->get();
    
        return view('existing-user-details', compact('flowers','user_details'));
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
            'address_id' => 'required|numeric',
            'product_id' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
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
            'payment_method' => $validatedUserData['payment_method'],
            'paid_amount' => $validatedUserData['paid_amount'],
            'payment_status' => $validatedUserData['payment_status'],
        ]);

        // Commit the transaction
        \DB::commit();

               return redirect()->back()->with('success', 'existing user add succesful!');

    } catch (\Exception $e) {
        // Rollback the transaction on error
        \DB::rollBack();

        return redirect()->back()->with('error', 'An error occurred while adding the existing user. Please try again.');

    }
}


}
