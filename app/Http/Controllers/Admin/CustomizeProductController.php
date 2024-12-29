<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRequest;
use App\Models\ProductSucription;
use App\Models\RiderDetails;
use App\Models\ProductOrder;
use Illuminate\Support\Str;
use Carbon\Carbon;


class CustomizeProductController extends Controller
{
    public function showCustomizeRequest()
    {
        // Eager load the necessary relationships, including flowerRequestItems
        $pendingRequests = ProductRequest::with([
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

        $activeSubscriptions = ProductSucription::where('status', 'active')->count();


        // Orders requested today
        $ordersRequestedToday = ProductRequest::whereDate('date', Carbon::today())->count();
        $riders = RiderDetails::where('status', 'active')->get();
        // dd($riders);
        return view('admin.product-request.manage-product-request', compact('riders','pendingRequests', 'activeSubscriptions', 'ordersRequestedToday'));
    }

    public function saveCustomizePrice(Request $request, $id)
{
    try {
        $flowerRequest = ProductRequest::findOrFail($id);

        // Generate a unique order ID
        $orderId = 'ORD-' . strtoupper(Str::random(12));

        // Create the order
        $order = ProductOrder::create([
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
    
}
