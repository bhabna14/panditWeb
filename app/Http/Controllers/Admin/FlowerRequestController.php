<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Carbon\Carbon;
class FlowerRequestController extends Controller
{
    //
    public function showRequests()
    {
        // Eager load the necessary relationships, including flowerRequestItems
        $pendingRequests = FlowerRequest::with([
            'order' => function ($query) {
                $query->with('flowerPayments');
            },
            'flowerProduct',
            'user',
            'address.localityDetails',
            'flowerRequestItems'  // Eager load flowerRequestItems
        ])
        ->orderBy('id', 'desc')
        ->get();

        $activeSubscriptions = Subscription::where('status', 'active')->count();

        // Paused subscriptions count
        $pausedSubscriptions = Subscription::where('status', 'paused')->count();

        // Orders requested today
        $ordersRequestedToday = FlowerRequest::whereDate('date', Carbon::today())->count();
        
        return view('admin.flower-request.manage-flower-request', compact('pendingRequests', 'activeSubscriptions', 'pausedSubscriptions', 'ordersRequestedToday'));
    }
    
public function saveOrder(Request $request, $id)
{
    try {
        $flowerRequest = FlowerRequest::findOrFail($id);

        // Generate a unique order ID
        $orderId = 'ORD-' . strtoupper(Str::random(12));

        // Create the order
        $order = Order::create([
            'order_id' => $orderId,
            'request_id' => $flowerRequest->request_id,
            'product_id' => $flowerRequest->product_id,
            'user_id' => $flowerRequest->user_id,
            'address_id' => $flowerRequest->address_id,
            'quantity' => 1,
            'requested_flower_price' => $request->requested_flower_price,
            'delivery_charge' => $request->delivery_charge,
            'total_price' => ($request->requested_flower_price ?: 0) + ($request->delivery_charge ?: 0),

            'suggestion' => $flowerRequest->suggestion,
        ]);

        $flowerRequest->status = 'approved';
        $flowerRequest->save();
        
        return redirect()->back()->with('success', 'Order saved successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to save order');
    }
}

public function markPayment(Request $request, $id)
{
    try {
        $order = Order::where('request_id', $id)->firstOrFail();

        // Create a new flower payment entry
        FlowerPayment::create([
            'order_id' => $order->order_id,
            'payment_id' => NULL, // Can be set later if available
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

        return redirect()->back()->with('success', 'Payment marked as paid');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to mark payment as paid');
    }
}
}
