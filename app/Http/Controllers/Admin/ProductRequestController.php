<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRequest;
use App\Models\ProductSucription;
use App\Models\RiderDetails;
use Illuminate\Support\Str;
use App\Models\ProductOrder;
use App\Models\FlowerPayment;
use App\Models\UserDevice;
use App\Services\NotificationService;


use Carbon\Carbon;

class ProductRequestController extends Controller
{
    public function showRequests(Request $request)
    {
        // Get the filter value from the query string, defaulting to 'today'
        // $filter = $request->input('filter', 'today');
    
        $query = ProductRequest::with([
            'order' => function ($query) {
                $query->with('flowerPayments');
            },
            'flowerProduct',
            'user',
            'address.localityDetails',
            'flowerRequestItems'
        ])->orderBy('id', 'desc');
    
        $pendingRequests = $query->get();

        $activeSubscriptions = ProductSucription::where('status', 'active')->count();
        $pausedSubscriptions = ProductSucription::where('status', 'paused')->count();
        $ordersRequestedToday = ProductRequest::whereDate('date', Carbon::today())->count();
        $riders = RiderDetails::where('status', 'active')->get();
    
        return view('admin.product-request.manage-product-request', compact(
            'riders',
            'pendingRequests',
            'activeSubscriptions',
            'pausedSubscriptions',
            'ordersRequestedToday'
        ));
    }

    public function saveProductOrder(Request $request, $id)
    {
        try {
            \Log::info('Processing order for request ID: ' . $id);
    
            // Find the product request
            $flowerRequest = ProductRequest::findOrFail($id);
            \Log::info('Product request found: ', $flowerRequest->toArray());
    
            // Generate a unique order ID
            $orderId = 'ORD-' . strtoupper(Str::random(12));
            \Log::info('Generated order ID: ' . $orderId);
    
            // Check requested flower price
            if (!$request->has('requested_flower_price')) {
                \Log::error('Requested flower price is missing.');
                return redirect()->back()->with('error', 'Requested flower price is missing.');
            }
    
            if (!$request->has('delivery_charge')) {
                \Log::error('Delivery charge is missing.');
                return redirect()->back()->with('error', 'Delivery charge is missing.');
            }
    
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
    
            \Log::info('Order created successfully: ', $order->toArray());
    
            // Update the flower request status
            $flowerRequest->status = 'approved';
            $flowerRequest->save();
    
            \Log::info('Flower request status updated to approved.');
    
            return redirect()->back()->with('success', 'Order saved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Product request not found: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Product request not found.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ', $e->errors());
            return redirect()->back()->with('error', 'Validation failed. Please check your inputs.');
        } catch (\Exception $e) {
            \Log::error('Failed to save order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save order. Check the logs for more details.');
        }
    }
    
        
    
public function markPayment(Request $request, $id)
{
    try {
        $order = ProductOrder::where('request_id', $id)->firstOrFail();

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
        $flowerRequest = ProductRequest::where('request_id', $id)->firstOrFail();

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
