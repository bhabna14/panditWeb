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
use App\Models\RiderDetails;
use App\Models\UserDevice;
use App\Services\NotificationService;
use Carbon\Carbon;

class FlowerRequestController extends Controller
{

    public function showRequests(Request $request)
    {
        // Filters we support via cards / querystring
        $filter = $request->query('filter', 'all');

        // Use app timezone for "today"
        $tz    = config('app.timezone');
        $today = Carbon::today($tz)->toDateString();

        // Base query + eager loads
        $query = FlowerRequest::with([
            'order' => function ($q) {
                $q->with('flowerPayments', 'delivery', 'rider');
            },
            'flowerProduct',
            'user',
            'address.localityDetails',
            'flowerRequestItems',
        ])->orderByDesc('id');

        // Apply filter to the table dataset
        switch ($filter) {
            case 'today':
                // Orders scheduled for delivery today
                $query->whereDate('date', $today);
                break;

            case 'upcoming':
                // Next 3 days (including today)
                $query->whereBetween('date', [$today, Carbon::parse($today)->addDays(3)->toDateString()]);
                break;

            case 'paid':
                $query->where('status', 'paid');
                break;

            case 'rejected':
                // Some projects use "cancelled", some "rejected" — support both.
                $query->whereIn('status', 'Rejected');
                break;

            case 'all':
            default:
                // no extra where
                break;
        }

        $pendingRequests = $query->get();

        // Dashboard counts (global, not tied to the current filter)
        $totalCustomizeOrders   = FlowerRequest::count();
        $todayCustomizeOrders   = FlowerRequest::whereDate('date', $today)->count();
        $paidCustomizeOrders    = FlowerRequest::where('status', 'paid')->count();
        $rejectCustomizeOrders  = FlowerRequest::whereIn('status', ['cancelled', 'rejected'])->count();

        // Riders for assignment (active only)
        $riders = RiderDetails::where('status', 'active')->get();

        return view('admin.flower-request.manage-flower-request', compact(
            'riders',
            'pendingRequests',
            'totalCustomizeOrders',
            'todayCustomizeOrders',
            'paidCustomizeOrders',
            'rejectCustomizeOrders',
            'filter'
        ));
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

        // Update the flower request status
        $flowerRequest->status = 'approved';
        $flowerRequest->save();

        // Send notification to user's devices
        $deviceTokens = UserDevice::where('user_id', $flowerRequest->user_id)->whereNotNull('device_id')->pluck('device_id')->toArray();

        if (!empty($deviceTokens)) {
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $notificationService->sendBulkNotifications(
                $deviceTokens,
                'Order Approved',
                'Price is updated, please pay the amount',
                ['order_id' => $order->order_id]
            );

            \Log::info('Notification sent successfully to all devices.', [
                'user_id' => $flowerRequest->user_id,
                'device_tokens' => $deviceTokens,
            ]);
        } else {
            \Log::warning('No device tokens found for user.', ['user_id' => $flowerRequest->user_id]);
        }

        return redirect()->back()->with('success', 'Order saved successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to save order.', ['error' => $e->getMessage()]);
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
