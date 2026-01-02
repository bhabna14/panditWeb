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

    $this->applyRequestFilter($query, $filter, $today, $startDate, $endDate);

    $pendingRequests = $query->get();

    // ---------------------------
    // Card Counts (Global)
    // ---------------------------
    $totalCustomizeOrders    = FlowerRequest::count();
    $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
    $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

    // APPROVED count (status=approved)
    $approvedCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyApprovedFilter($q);
        })
        ->count();

    // PAID count (status=paid OR successful payment exists)
    $paidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyPaidFilter($q);
        })
        ->count();

    // REJECTED count (status=Rejected)
    $rejectCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyRejectedFilter($q);
        })
        ->count();

    // UNPAID count = approved but not paid (and no successful payment)
    $unpaidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyUnpaidFilter($q);
        })
        ->count();

    // Amounts
    $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();   // approved but not paid
    $paidCollectedAmount   = $this->computePaidCollectedAmount();     // paid/collected

    $riders = RiderDetails::where('status', 'active')->get();

    return view('admin.flower-request.manage-flower-request', compact(
        'riders',
        'pendingRequests',
        'totalCustomizeOrders',
        'todayCustomizeOrders',
        'upcomingCustomizeOrders',
        'approvedCustomizeOrders',
        'paidCustomizeOrders',
        'rejectCustomizeOrders',
        'unpaidCustomizeOrders',
        'unpaidAmountToCollect',
        'paidCollectedAmount',
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

    // Counts
    $totalCustomizeOrders    = FlowerRequest::count();
    $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
    $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

    $approvedCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyApprovedFilter($q);
        })
        ->count();

    $paidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyPaidFilter($q);
        })
        ->count();

    $rejectCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyRejectedFilter($q);
        })
        ->count();

    $unpaidCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyUnpaidFilter($q);
        })
        ->count();

    $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();
    $paidCollectedAmount   = $this->computePaidCollectedAmount();

    $riders   = RiderDetails::where('status', 'active')->get();
    $rowsHtml = view('admin.flower-request.partials._rows', compact('pendingRequests', 'riders'))->render();

    return response()->json([
        'rows_html' => $rowsHtml,
        'counts' => [
            'total'                => $totalCustomizeOrders,
            'today'                => $todayCustomizeOrders,
            'upcoming'             => $upcomingCustomizeOrders,

            'approved'             => $approvedCustomizeOrders,
            'paid'                 => $paidCustomizeOrders,
            'rejected'             => $rejectCustomizeOrders,
            'unpaid'               => $unpaidCustomizeOrders,

            'unpaid_amount'        => (float) $unpaidAmountToCollect,
            'unpaid_amount_fmt'    => '₹' . number_format((float) $unpaidAmountToCollect, 2),

            'paid_amount'          => (float) $paidCollectedAmount,
            'paid_amount_fmt'      => '₹' . number_format((float) $paidCollectedAmount, 2),
        ],
        'active' => $filter,
    ]);
}


/* =========================================================
 |  FILTERS (STATUS: approved, paid, rejected)
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

        case 'approved':
            $this->applyApprovedFilter($query);
            break;

        case 'paid':
            $this->applyPaidFilter($query);
            break;

        case 'unpaid':
            $this->applyUnpaidFilter($query);
            break;

        case 'rejected':
            $this->applyRejectedFilter($query);
            break;

        case 'all':
        default:
            break;
    }
}

private function applyApprovedFilter($query): void
{
    // Only status=approved
    $this->whereStatusEqualsLowerTrim($query, 'approved');
}

private function applyRejectedFilter($query): void
{
    // Only status=Rejected (case-insensitive)
    $this->whereStatusEqualsLowerTrim($query, 'rejected');
}

private function applyPaidFilter($query): void
{
    // Paid = status=paid OR any successful payment exists
    $paidPaymentStatusesLower = $this->paidPaymentStatusesLower();

    $query->where(function ($q) use ($paidPaymentStatusesLower) {
        $this->whereStatusEqualsLowerTrim($q, 'paid')
            ->orWhereHas('order.flowerPayments', function ($p) use ($paidPaymentStatusesLower) {
                $this->wherePaymentStatusInLowerTrim($p, $paidPaymentStatusesLower);
            });
    });
}

private function applyUnpaidFilter($query): void
{
    // IMPORTANT:
    // Unpaid = APPROVED but NOT PAID (and no successful payment exists)
    $paidPaymentStatusesLower = $this->paidPaymentStatusesLower();

    $this->whereStatusEqualsLowerTrim($query, 'approved');

    $query->whereDoesntHave('order.flowerPayments', function ($p) use ($paidPaymentStatusesLower) {
        $this->wherePaymentStatusInLowerTrim($p, $paidPaymentStatusesLower);
    });
}

/* ---------- normalize helpers ---------- */

private function whereStatusEqualsLowerTrim($query, string $valueLower)
{
    return $query->whereRaw("LOWER(TRIM(status)) = ?", [$valueLower]);
}

private function wherePaymentStatusInLowerTrim($query, array $valuesLower)
{
    $valuesLower = array_values(array_unique(array_map('strtolower', $valuesLower)));
    $ph = implode(',', array_fill(0, count($valuesLower), '?'));
    return $query->whereRaw("LOWER(TRIM(payment_status)) IN ($ph)", $valuesLower);
}

private function paidPaymentStatusesLower(): array
{
    // Adjust if your gateway uses other success values
    return ['paid', 'success', 'captured'];
}


/* =========================================================
 |  AMOUNTS
 ========================================================= */

private function computeUnpaidAmountToCollect(): float
{
    // Unpaid = approved but not paid
    $q = FlowerRequest::with(['order.flowerPayments', 'flowerRequestItems']);
    $this->applyUnpaidFilter($q);

    $rows = $q->get();

    $sum = 0.0;
    foreach ($rows as $req) {
        $sum += $this->resolveRequestAmount($req);
    }
    return (float) $sum;
}

private function computePaidCollectedAmount(): float
{
    // Paid = status=paid OR successful payment exists
    $q = FlowerRequest::with(['order.flowerPayments', 'flowerRequestItems']);
    $this->applyPaidFilter($q);

    $rows = $q->get();

    $sum = 0.0;
    foreach ($rows as $req) {
        $sum += $this->resolvePaidCollectedAmount($req);
    }
    return (float) $sum;
}

private function resolvePaidCollectedAmount($req): float
{
    // Prefer latest successful payment.paid_amount, otherwise fallback to request amount
    $order = $req->order ?? null;
    if ($order && $order->flowerPayments) {
        $success = $order->flowerPayments->filter(function ($p) {
            $st = strtolower(trim((string)($p->payment_status ?? '')));
            return in_array($st, $this->paidPaymentStatusesLower(), true);
        });

        if ($success->count() > 0) {
            $latest = $success->sortByDesc('id')->first();
            if ($latest && is_numeric($latest->paid_amount)) {
                return (float) $latest->paid_amount;
            }
        }
    }

    return (float) $this->resolveRequestAmount($req);
}

private function resolveRequestAmount($req): float
{
    // 1) If order has a known amount field, use it (safe check)
    $order = $req->order ?? null;
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
    }

    // 2) Fallback: compute from request items
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
