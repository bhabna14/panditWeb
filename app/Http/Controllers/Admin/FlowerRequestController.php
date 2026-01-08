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

        // ---------------------------
        // Card Counts (Global)
        // ---------------------------
        $totalCustomizeOrders    = FlowerRequest::count();
        $todayCustomizeOrders    = FlowerRequest::whereDate('date', $today)->count();
        $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$startDate, $endDate])->count();

        $pendingCustomizeOrders = FlowerRequest::query()
            ->where(function ($q) {
                $this->applyPendingFilter($q);
            })
            ->count();

        $approvedCustomizeOrders = FlowerRequest::query()
            ->where(function ($q) {
                $this->applyApprovedFilter($q);
            })
            ->count();

        // ✅ PAID COUNT (UPDATED to your required logic)
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

        // ✅ UNPAID = approved but NOT paid (no successful payment)
        $unpaidCustomizeOrders = FlowerRequest::query()
            ->where(function ($q) {
                $this->applyUnpaidFilter($q);
            })
            ->count();

        $unpaidAmountToCollect = $this->computeUnpaidAmountToCollect();
        $paidCollectedAmount   = $this->computePaidCollectedAmount();

        $riders = RiderDetails::where('status', 'active')->get();

        return view('admin.flower-request.manage-flower-request', compact(
            'riders',
            'pendingRequests',
            'totalCustomizeOrders',
            'todayCustomizeOrders',
            'upcomingCustomizeOrders',
            'pendingCustomizeOrders',
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

    $pendingCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyPendingFilter($q);
        })
        ->count();

    $approvedCustomizeOrders = FlowerRequest::query()
        ->where(function ($q) {
            $this->applyApprovedFilter($q);
        })
        ->count();

    // ✅ PAID COUNT (UPDATED)
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

    // ✅ UNPAID COUNT (approved but not paid)
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
            'total'               => $totalCustomizeOrders,
            'today'               => $todayCustomizeOrders,
            'upcoming'            => $upcomingCustomizeOrders,

            'pending'             => $pendingCustomizeOrders,
            'approved'            => $approvedCustomizeOrders,
            'paid'                => $paidCustomizeOrders,
            'rejected'            => $rejectCustomizeOrders,
            'unpaid'              => $unpaidCustomizeOrders,

            'unpaid_amount'       => (float) $unpaidAmountToCollect,
            'unpaid_amount_fmt'   => '₹' . number_format((float) $unpaidAmountToCollect, 2),

            'paid_amount'         => (float) $paidCollectedAmount,
            'paid_amount_fmt'     => '₹' . number_format((float) $paidCollectedAmount, 2),
        ],
        'active' => $filter,
    ]);
}


/* =========================================================
 | FILTER APPLY (SSR + AJAX)
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

        case 'pending':
            $this->applyPendingFilter($query);
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


/* =========================================================
 | YOUR STATUS SET: approved, paid, Rejected
 | (We also keep "cancelled" in exclusions for old data safety)
 ========================================================= */

private function applyPendingFilter($query): void
{
    // Pending = NULL/empty (or not set)
    $query->where(function ($q) {
        $q->whereNull('status')
          ->orWhereRaw('TRIM(COALESCE(status,"")) = ""');
    });
}

private function applyApprovedFilter($query): void
{
    $query->whereRaw('LOWER(COALESCE(status,"")) = "approved"');
}

private function applyRejectedFilter($query): void
{
    // Handles "Rejected" stored with capital R
    $query->whereRaw('LOWER(COALESCE(status,"")) = "rejected"');
}

/**
 * ✅ PAID FILTER (UPDATED exactly to your required logic)
 * Paid = status paid OR successful payment exists
 * Exclude rejected/cancelled from being counted as paid.
 */
private function applyPaidFilter($query): void
{
    $paidPaymentExists = $this->paidPaymentExistsSubquery();

    $query->whereRaw('LOWER(COALESCE(status,"")) NOT IN ("rejected","cancelled")')
          ->where(function ($q) use ($paidPaymentExists) {
              $q->whereRaw('LOWER(COALESCE(status,"")) = "paid"')
                ->orWhereExists($paidPaymentExists);
          });
}

/**
 * ✅ UNPAID = approved but NOT paid (no successful payment exists)
 */
private function applyUnpaidFilter($query): void
{
    $paidPaymentExists = $this->paidPaymentExistsSubquery();

    $query->whereRaw('LOWER(COALESCE(status,"")) = "approved"')
          ->whereNotExists($paidPaymentExists);
}


/* =========================================================
 | PAYMENT EXISTS SUBQUERY
 | Checks if any successful payment exists for the request's order
 | Works even if flower_payments.order_id stores orders.id OR orders.order_id
 ========================================================= */

private function paidPaymentExistsSubquery(): \Closure
{
    // IMPORTANT: adjust success statuses if you use different values
    $successStatuses = ['paid', 'success', 'captured'];

    return function ($sub) use ($successStatuses) {
        $sub->select(DB::raw(1))
            ->from('orders as o')
            ->join('flower_payments as fp', function ($join) {
                // Support BOTH patterns:
                // fp.order_id = o.id  OR fp.order_id = o.order_id
                $join->on('fp.order_id', '=', 'o.id')
                     ->orOn('fp.order_id', '=', 'o.order_id');
            })
            // link orders to flower_requests by request_id
            ->whereColumn('o.request_id', 'flower_requests.request_id')
            ->whereRaw('LOWER(COALESCE(fp.payment_status,"")) IN ("' . implode('","', $successStatuses) . '")');
    };
}


/* =========================================================
 | AMOUNTS (optional but consistent with filters)
 ========================================================= */

private function computeUnpaidAmountToCollect(): float
{
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
    // Prefer latest successful payment.paid_amount, else fallback to request amount
    $order = $req->order ?? null;

    if ($order && $order->flowerPayments) {
        $success = $order->flowerPayments->filter(function ($p) {
            $st = strtolower(trim((string)($p->payment_status ?? '')));
            return in_array($st, ['paid', 'success', 'captured'], true);
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
    $order = $req->order ?? null;

    // Try common order total fields (safe; no SQL select on unknown columns)
    if ($order) {
        foreach (['total_price', 'total_amount', 'amount', 'payable_amount', 'order_total', 'grand_total'] as $col) {
            if (isset($order->{$col}) && is_numeric($order->{$col})) {
                return (float) $order->{$col};
            }
        }
    }

    // Fallback from request items
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

            return (is_numeric($qty) && is_numeric($price)) ? ((float)$qty * (float)$price) : 0.0;
        });
    }

    return 0.0;
}

        
    public function saveOrder(Request $request, $id)
    {
        $request->validate([
            'requested_flower_price' => ['required', 'numeric', 'min:0'],
            'delivery_charge'        => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $flowerRequest = FlowerRequest::findOrFail($id);

            // Block invalid transitions
            $st = strtolower(trim((string)($flowerRequest->status ?? '')));
            if ($st === 'paid' || $st === 'rejected') {
                return redirect()->back()->with('error', 'This request is already finalized. You cannot update price.');
            }

            DB::transaction(function () use ($request, $flowerRequest) {

                // If order already exists for this request_id, update it (avoid duplicate orders)
                $existingOrder = Order::where('request_id', $flowerRequest->request_id)->first();

                if ($existingOrder) {
                    $existingOrder->requested_flower_price = $request->requested_flower_price;
                    $existingOrder->delivery_charge        = $request->delivery_charge;
                    $existingOrder->total_price            = ((float)$request->requested_flower_price) + ((float)$request->delivery_charge);
                    $existingOrder->suggestion             = $flowerRequest->suggestion;
                    $existingOrder->save();

                    $order = $existingOrder;
                } else {
                    $orderId = 'ORD-' . strtoupper(Str::random(12));

                    $order = Order::create([
                        'order_id'               => $orderId,
                        'request_id'             => $flowerRequest->request_id,
                        'product_id'             => $flowerRequest->product_id,
                        'user_id'                => $flowerRequest->user_id,
                        'address_id'             => $flowerRequest->address_id,
                        'quantity'               => 1,
                        'requested_flower_price' => $request->requested_flower_price,
                        'delivery_charge'        => $request->delivery_charge,
                        'total_price'            => ((float)$request->requested_flower_price) + ((float)$request->delivery_charge),
                        'suggestion'             => $flowerRequest->suggestion,
                    ]);
                }

                // Update request status => approved
                $flowerRequest->status = 'approved';
                $flowerRequest->save();

                // Attach order_id into local scope for notification below
                $GLOBALS['__last_order_id'] = $order->order_id;
            });

            // Send notification to user's devices
            $deviceTokens = UserDevice::where('user_id', $flowerRequest->user_id)
                ->whereNotNull('device_id')
                ->pluck('device_id')
                ->toArray();

            if (!empty($deviceTokens)) {
                $notificationService = new \NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
                $notificationService->sendBulkNotifications(
                    $deviceTokens,
                    'Order Approved',
                    'Price is updated, please pay the amount',
                    ['order_id' => $GLOBALS['__last_order_id'] ?? null]
                );

                Log::info('Notification sent successfully to all devices.', [
                    'user_id'        => $flowerRequest->user_id,
                    'device_tokens'  => $deviceTokens,
                ]);
            } else {
                Log::warning('No device tokens found for user.', ['user_id' => $flowerRequest->user_id]);
            }

            return redirect()->back()->with('success', 'Order saved successfully');
        } catch (\Throwable $e) {
            Log::error('Failed to save order.', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save order');
        }
    }

    public function markPayment(Request $request, $request_id)
    {
        $request->validate([
            'payment_method' => ['required', 'in:upi,razorpay,cash'],
        ]);

        try {
            $order         = Order::where('request_id', $request_id)->firstOrFail();
            $flowerRequest = FlowerRequest::where('request_id', $request_id)->firstOrFail();

            DB::transaction(function () use ($request, $order, $flowerRequest) {

                // Prevent duplicate paid rows (optional but recommended)
                $alreadyPaid = FlowerPayment::where('order_id', $order->order_id)
                    ->whereRaw("LOWER(TRIM(COALESCE(payment_status,''))) IN ('paid','approved','success','captured')")
                    ->exists();

                if (!$alreadyPaid) {
                    FlowerPayment::create([
                        'order_id'       => $order->order_id,
                        'payment_id'     => null,
                        'user_id'        => $order->user_id,
                        'payment_method' => $request->payment_method,
                        'paid_amount'    => $order->total_price,
                        'payment_status' => 'paid',
                    ]);
                }

                // Update request status
                $st = strtolower(trim((string)($flowerRequest->status ?? '')));
                if ($st === 'approved') {
                    $flowerRequest->status = 'paid';
                    $flowerRequest->save();
                }
            });

            return redirect()->back()->with(
                'success',
                'Payment marked as paid via ' . strtoupper($request->payment_method) . '.'
            );
        } catch (\Throwable $e) {
            Log::error('Failed to mark payment as paid', [
                'request_id' => $request_id,
                'error'      => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to mark payment as paid');
        }
    }

public function updateDeliveryStatus(\Illuminate\Http\Request $request, \App\Models\FlowerRequest $flowerRequest)
{
    $validated = $request->validate([
        'delivery_status' => ['required', 'string', \Illuminate\Validation\Rule::in([
            'pending', 'assigned', 'out_for_delivery', 'delivered', 'failed', 'returned'
        ])],
    ]);

    $flowerRequest->delivery_status = $validated['delivery_status'];
    $flowerRequest->save();

    return back()->with('success', 'Delivery status updated successfully.');
}


}
