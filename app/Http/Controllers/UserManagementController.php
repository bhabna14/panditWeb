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
        DB::beginTransaction();

        // Validate request data
        $validated = $request->validate([
            'userid' => 'required|string',
            'address_id' => 'required|numeric',
            'start_date' => 'nullable|date',
            'duration' => 'nullable|integer|in:1,3,6',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
        ]);

        $existingSubscription = Subscription::where('user_id', $validated['userid'])->first();

        if ($existingSubscription) {
            $order = Order::where('order_id', $existingSubscription->order_id)->first();
            if ($order) {
                $order->update([
                    'user_id' => $validated['userid'],
                    'quantity' => 1,
                    'total_price' => $validated['paid_amount'] ?? 0,
                    'address_id' => $validated['address_id'],
                ]);
                Log::info('Order updated successfully', ['order_id' => $order->order_id]);
            } else {
                return back()->with('error', 'Order ID not found for update.');
            }
        } else {
            $orderId = 'ORD-' . strtoupper(Str::random(12));
            $order = Order::create([
                'user_id' => $validated['userid'],
                'order_id' => $orderId,
                'quantity' => 1,
                'start_date' => $validated['start_date'] ?? now(),
                'address_id' => $validated['address_id'],
                'total_price' => $validated['paid_amount'] ?? 0,
            ]);
        }

        // Calculate end date
        $startDate = $validated['start_date'] ? Carbon::parse($validated['start_date']) : now();
        $endDate = match ((int) $validated['duration']) {
            1 => $startDate->copy()->addDays(29),
            3 => $startDate->copy()->addDays(89),
            6 => $startDate->copy()->addDays(179),
            default => now(), // Provide a fallback value instead of throwing an exception
        };
        
        // Create subscription
        Subscription::create([
            'subscription_id' => 'SUB-' . strtoupper(Str::random(12)),
            'user_id' => $validated['userid'],
            'product_id' => $order->product_id ?? null,
            'order_id' => $order->order_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
            'status' => $validated['status'] ?? 'active',
        ]);

     
        // Create payment record
        FlowerPayment::create([
            'order_id' => $order->order_id,
            'payment_id' => null,
            'user_id' => $validated['userid'],
            'payment_method' => $validated['payment_method'],
            'paid_amount' => $validated['paid_amount'] ?? 0,
            'payment_status' => $validated['payment_status'] ?? 'pending',
        ]);

        DB::commit();
        return back()->with('success', 'User data processed successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error processing user data', ['error' => $e->getMessage()]);
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

    
    
}
