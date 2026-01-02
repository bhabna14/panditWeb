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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlowerRequestController extends Controller
{

public function showRequests(Request $request)
{
    $filter = $request->query('filter', 'all');

    $tz          = config('app.timezone', 'Asia/Kolkata');
    $todayCarbon = Carbon::today($tz);
    $today       = $todayCarbon->toDateString();

    // Next 3 days (excluding today)
    $startDateCarbon = $todayCarbon->copy()->addDay();   // tomorrow
    $endDateCarbon   = $todayCarbon->copy()->addDays(3); // 3 days from today
    $startDate       = $startDateCarbon->toDateString();
    $endDate         = $endDateCarbon->toDateString();

    $query = FlowerRequest::with([
        'order' => function ($q) {
            $q->with('flowerPayments', 'delivery', 'rider');
        },
        'flowerProduct',
        'user',
        'address.localityDetails',
        'flowerRequestItems',
    ])->orderByDesc('id');

    switch ($filter) {
        case 'today':
            $query->whereDate('date', $today);
            break;

        case 'upcoming':
            $query->whereBetween('date', [$startDate, $endDate]);
            break;

        case 'paid':
            $query->whereIn('status', ['paid', 'Paid']);
            break;

        case 'unpaid':
            $query->where(function ($q) {
                $q->whereNull('status')
                  ->orWhereNotIn('status', [
                      'paid', 'Paid',
                      'cancelled', 'Cancelled',
                      'rejected', 'Rejected',
                  ]);
            });
            break;

        case 'rejected':
            $query->whereIn('status', ['cancelled', 'Cancelled', 'rejected', 'Rejected']);
            break;

        case 'all':
        default:
            break;
    }

    $pendingRequests = $query->get();

    // ---------------------------
    // Card Counts (Global)
    // ---------------------------
    $totalCustomizeOrders    = FlowerRequest::count();
    $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
    $paidCustomizeOrders     = FlowerRequest::whereIn('status', ['paid', 'Paid'])->count();
    $rejectCustomizeOrders   = FlowerRequest::whereIn('status', ['cancelled', 'Cancelled', 'rejected', 'Rejected'])->count();
    $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

    // NEW: unpaid count
    $unpaidCustomizeOrders = FlowerRequest::where(function ($q) {
        $q->whereNull('status')
          ->orWhereNotIn('status', [
              'paid', 'Paid',
              'cancelled', 'Cancelled',
              'rejected', 'Rejected',
          ]);
    })->count();

    // NEW: unpaid amount to collect (robust; NO hardcoded order columns)
    $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();

    $riders = RiderDetails::where('status', 'active')->get();

    return view('admin.flower-request.manage-flower-request', compact(
        'riders',
        'pendingRequests',
        'totalCustomizeOrders',
        'todayCustomizeOrders',
        'paidCustomizeOrders',
        'rejectCustomizeOrders',
        'upcomingCustomizeOrders',
        'unpaidCustomizeOrders',
        'unpaidAmountToCollect',
        'filter'
    ));
}


public function ajaxData(Request $request)
{
    $filter = $request->query('filter', 'all');

    $tz          = config('app.timezone', 'Asia/Kolkata');
    $todayCarbon = Carbon::today($tz);
    $today       = $todayCarbon->toDateString();

    // Next 3 days (excluding today)
    $startDateCarbon = $todayCarbon->copy()->addDay();
    $endDateCarbon   = $todayCarbon->copy()->addDays(3);
    $startDate       = $startDateCarbon->toDateString();
    $endDate         = $endDateCarbon->toDateString();

    $query = FlowerRequest::with([
        'order' => function ($q) {
            $q->with('flowerPayments', 'delivery', 'rider');
        },
        'flowerProduct',
        'user',
        'address.localityDetails',
        'flowerRequestItems',
    ])->orderByDesc('id');

    switch ($filter) {
        case 'today':
            $query->whereDate('date', $today);
            break;

        case 'upcoming':
            $query->whereBetween('date', [$startDate, $endDate]);
            break;

        case 'paid':
            $query->whereIn('status', ['paid', 'Paid']);
            break;

        case 'unpaid':
            $query->where(function ($q) {
                $q->whereNull('status')
                  ->orWhereNotIn('status', [
                      'paid', 'Paid',
                      'cancelled', 'Cancelled',
                      'rejected', 'Rejected',
                  ]);
            });
            break;

        case 'rejected':
            $query->whereIn('status', ['cancelled', 'Cancelled', 'rejected', 'Rejected']);
            break;

        case 'all':
        default:
            break;
    }

    $pendingRequests = $query->get();

    // Global counts for cards
    $totalCustomizeOrders    = FlowerRequest::count();
    $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
    $paidCustomizeOrders     = FlowerRequest::whereIn('status', ['paid', 'Paid'])->count();
    $rejectCustomizeOrders   = FlowerRequest::whereIn('status', ['cancelled', 'Cancelled', 'rejected', 'Rejected'])->count();
    $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

    $unpaidCustomizeOrders = FlowerRequest::where(function ($q) {
        $q->whereNull('status')
          ->orWhereNotIn('status', [
              'paid', 'Paid',
              'cancelled', 'Cancelled',
              'rejected', 'Rejected',
          ]);
    })->count();

    $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();

    $riders  = RiderDetails::where('status', 'active')->get();
    $rowsHtml = view('admin.flower-request.partials._rows', compact('pendingRequests', 'riders'))->render();

    return response()->json([
        'rows_html' => $rowsHtml,
        'counts' => [
            'total'             => $totalCustomizeOrders,
            'today'             => $todayCustomizeOrders,
            'paid'              => $paidCustomizeOrders,
            'unpaid'            => $unpaidCustomizeOrders,
            'unpaid_amount'     => (float) $unpaidAmountToCollect,
            'unpaid_amount_fmt' => '₹' . number_format((float) $unpaidAmountToCollect, 2),
            'rejected'          => $rejectCustomizeOrders,
            'upcoming'          => $upcomingCustomizeOrders,
        ],
        'active' => $filter,
    ]);
}


/**
 * Compute total "amount to collect" for unpaid requests.
 * Priority:
 * 1) Order total fields (if they exist)
 * 2) Latest FlowerPayment.paid_amount (since you use it as collectable/collected amount)
 * 3) Sum from items (item_total or qty*price)
 */
private function computeUnpaidAmountToCollect(): float
{
    $unpaidRequests = FlowerRequest::with([
            'order.flowerPayments',
            'flowerRequestItems',
        ])
        ->where(function ($q) {
            $q->whereNull('status')
              ->orWhereNotIn('status', [
                  'paid', 'Paid',
                  'cancelled', 'Cancelled',
                  'rejected', 'Rejected',
              ]);
        })
        ->get();

    $sum = 0.0;

    foreach ($unpaidRequests as $req) {
        $sum += $this->resolveRequestCollectableAmount($req);
    }

    return (float) $sum;
}

private function resolveRequestCollectableAmount($req): float
{
    // ---------------------------
    // 1) Try ORDER total columns
    // ---------------------------
    $order = $req->order ?? null;

    if ($order) {
        // Try common total column names safely (no SELECT, no SQL error)
        foreach ([
            'grand_total_price',
            'grand_total',
            'total_price',
            'total_amount',
            'amount',
            'payable_amount',
            'order_total',
        ] as $col) {
            if (isset($order->{$col}) && is_numeric($order->{$col})) {
                return (float) $order->{$col};
            }
        }

        // ---------------------------
        // 2) Try latest PAYMENT paid_amount
        // ---------------------------
        $payments = $order->flowerPayments ?? collect();
        if ($payments->count() > 0) {
            // Take latest payment record's paid_amount (avoid overcount if multiple attempts)
            $latest = $payments->sortByDesc('id')->first();
            if ($latest && isset($latest->paid_amount) && is_numeric($latest->paid_amount)) {
                return (float) $latest->paid_amount;
            }
        }
    }

    // ---------------------------
    // 3) Fallback: compute from items
    // ---------------------------
    $items = $req->flowerRequestItems ?? collect();
    if ($items->count() > 0) {
        return (float) $items->sum(function ($it) {
            foreach (['item_total', 'total', 'total_price', 'amount'] as $col) {
                if (isset($it->{$col}) && is_numeric($it->{$col})) {
                    return (float) $it->{$col};
                }
            }

            $qty   = $it->quantity ?? ($it->qty ?? 0);
            $price = $it->price ?? ($it->unit_price ?? 0);

            if (is_numeric($qty) && is_numeric($price)) {
                return (float) $qty * (float) $price;
            }

            return 0.0;
        });
    }

    return 0.0;
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
    // Validate payment method coming from SweetAlert
    $request->validate([
        'payment_method' => ['required', 'in:upi,razorpay,cash'],
    ]);

    try {
        $order         = Order::where('request_id', $id)->firstOrFail();
        $flowerRequest = FlowerRequest::where('request_id', $id)->firstOrFail();

        DB::transaction(function () use ($request, $order, $flowerRequest) {
            // Create flower payment row
            FlowerPayment::create([
                'order_id'       => $order->order_id,
                'payment_id'     => null, // set later if you want
                'user_id'        => $order->user_id,
                'payment_method' => $request->payment_method, // upi | razorpay | cash
                'paid_amount'    => $order->total_price,
                'payment_status' => 'paid',
            ]);

            // Update request status
            if ($flowerRequest->status === 'approved') {
                $flowerRequest->status = 'paid';
                $flowerRequest->save();
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Payment marked as paid via ' . strtoupper($request->payment_method) . '.');
    } catch (\Throwable $e) {
        Log::error('Failed to mark payment as paid', [
            'request_id' => $id,
            'error'      => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->with('error', 'Failed to mark payment as paid');
    }
}
}
