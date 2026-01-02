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

    // Base query (SSR list)
    $query = FlowerRequest::with([
        'order' => function ($q) {
            $q->with('flowerPayments', 'delivery', 'rider');
        },
        'flowerProduct',
        'user',
        'address.localityDetails',
        'flowerRequestItems',
    ])->orderByDesc('id');

    $this->applyRequestFilter($query, $filter, $today, $startDate, $endDate);

    $pendingRequests = $query->get();

    // ---------------------------
    // Card Counts (Global)
    // ---------------------------
    $totalCustomizeOrders    = FlowerRequest::count();
    $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
    $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

    $paidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyPaidFilter($q);
        })
        ->count();

    $rejectCustomizeOrders = FlowerRequest::whereIn('status', $this->rejectedStatuses())->count();

    $unpaidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyUnpaidFilter($q);
        })
        ->count();

    // NEW: unpaid amount to collect (must match unpaid definition)
    $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();

    $riders = RiderDetails::where('status', 'active')->get();

    return view('admin.flower-request.manage-flower-request', compact(
        'riders',
        'pendingRequests',
        'totalCustomizeOrders',
        'todayCustomizeOrders',
        'paidCustomizeOrders',
        'unpaidCustomizeOrders',
        'rejectCustomizeOrders',
        'upcomingCustomizeOrders',
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

    $this->applyRequestFilter($query, $filter, $today, $startDate, $endDate);

    $pendingRequests = $query->get();

    // Counts for cards
    $totalCustomizeOrders    = FlowerRequest::count();
    $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
    $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

    $paidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyPaidFilter($q);
        })
        ->count();

    $rejectCustomizeOrders = FlowerRequest::whereIn('status', $this->rejectedStatuses())->count();

    $unpaidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyUnpaidFilter($q);
        })
        ->count();

    $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();

    $riders   = RiderDetails::where('status', 'active')->get();
    $rowsHtml = view('admin.flower-request.partials._rows', compact('pendingRequests', 'riders'))->render();

    return response()->json([
        'rows_html' => $rowsHtml,
        'counts' => [
            'total'               => $totalCustomizeOrders,
            'today'               => $todayCustomizeOrders,
            'paid'                => $paidCustomizeOrders,
            'unpaid'              => $unpaidCustomizeOrders,
            'unpaid_amount'       => (float) $unpaidAmountToCollect,
            'unpaid_amount_fmt'   => '₹' . number_format((float) $unpaidAmountToCollect, 2),
            'rejected'            => $rejectCustomizeOrders,
            'upcoming'            => $upcomingCustomizeOrders,
        ],
        'active' => $filter,
    ]);
}


/* =========================================================
 |  FILTER HELPERS (single source of truth)
 ========================================================= */

private function applyRequestFilter($query, string $filter, string $today, string $startDate, string $endDate): void
{
    switch ($filter) {
        case 'today':
            $query->whereDate('date', $today);
            break;

        case 'upcoming':
            $query->whereBetween('date', [$startDate, $endDate]);
            break;

        case 'paid':
            $this->applyPaidFilter($query);
            break;

        case 'unpaid':
            $this->applyUnpaidFilter($query);
            break;

        case 'rejected':
            $query->whereIn('status', $this->rejectedStatuses());
            break;

        case 'all':
        default:
            // no extra where
            break;
    }
}

private function applyPaidFilter($query): void
{
    $paidStatuses = $this->paidStatuses();
    $paidPayStatuses = $this->paidPaymentStatuses();

    $query->where(function ($q) use ($paidStatuses, $paidPayStatuses) {
        // (A) FlowerRequest status says paid
        $q->whereIn('status', $paidStatuses)

          // OR (B) there is a successful payment against the order
          ->orWhereHas('order.flowerPayments', function ($p) use ($paidPayStatuses) {
              $p->whereIn('payment_status', $paidPayStatuses);
          });
    });
}

private function applyUnpaidFilter($query): void
{
    $rejectStatuses  = $this->rejectedStatuses();
    $paidStatuses    = $this->paidStatuses();
    $paidPayStatuses = $this->paidPaymentStatuses();

    // Unpaid = status is NULL or not in (paid+rejected)
    $query->where(function ($q) use ($paidStatuses, $rejectStatuses) {
        $q->whereNull('status')
          ->orWhereNotIn('status', array_merge($paidStatuses, $rejectStatuses));
    });

    // AND no successful payment exists
    $query->whereDoesntHave('order.flowerPayments', function ($p) use ($paidPayStatuses) {
        $p->whereIn('payment_status', $paidPayStatuses);
    });
}

private function paidStatuses(): array
{
    return ['paid', 'Paid', 'PAID'];
}

private function rejectedStatuses(): array
{
    return ['cancelled', 'Cancelled', 'rejected', 'Rejected'];
}

/**
 * Adjust if your gateway uses different success keywords.
 * Keep it broad so it never mis-classifies paid as unpaid.
 */
private function paidPaymentStatuses(): array
{
    return ['paid', 'Paid', 'PAID', 'success', 'Success', 'captured', 'CAPTURED'];
}


/* =========================================================
 |  UNPAID AMOUNT (must match unpaid definition)
 ========================================================= */

private function computeUnpaidAmountToCollect(): float
{
    $paidPayStatuses = $this->paidPaymentStatuses();
    $rejectStatuses  = $this->rejectedStatuses();
    $paidStatuses    = $this->paidStatuses();

    $unpaidRequests = FlowerRequest::with([
            'order.flowerPayments',
            'flowerRequestItems',
        ])
        ->where(function ($q) use ($paidStatuses, $rejectStatuses) {
            $q->whereNull('status')
              ->orWhereNotIn('status', array_merge($paidStatuses, $rejectStatuses));
        })
        ->whereDoesntHave('order.flowerPayments', function ($p) use ($paidPayStatuses) {
            $p->whereIn('payment_status', $paidPayStatuses);
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
    $order = $req->order ?? null;

    // 1) Try common ORDER total columns (safe: no select => no SQL error)
    if ($order) {
        foreach ([
            'total_price',
            'total_amount',
            'amount',
            'payable_amount',
            'order_total',
            'grand_total',
        ] as $col) {
            if (isset($order->{$col}) && is_numeric($order->{$col})) {
                return (float) $order->{$col};
            }
        }

        // 2) Try latest payment paid_amount (if exists)
        $payments = $order->flowerPayments ?? collect();
        if ($payments->count() > 0) {
            $latest = $payments->sortByDesc('id')->first();
            if ($latest && isset($latest->paid_amount) && is_numeric($latest->paid_amount)) {
                return (float) $latest->paid_amount;
            }
        }
    }

    // 3) Fallback: compute from request items
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
