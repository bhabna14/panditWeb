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

        // UNPAID: Approved but NOT paid (payment not received OR no successful payment)
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
                'total'             => $totalCustomizeOrders,
                'today'             => $todayCustomizeOrders,
                'upcoming'          => $upcomingCustomizeOrders,
                'pending'           => $pendingCustomizeOrders,
                'approved'          => $approvedCustomizeOrders,
                'paid'              => $paidCustomizeOrders,
                'rejected'          => $rejectCustomizeOrders,
                'unpaid'            => $unpaidCustomizeOrders,
                'unpaid_amount'     => (float) $unpaidAmountToCollect,
                'unpaid_amount_fmt' => '₹' . number_format((float) $unpaidAmountToCollect, 2),
                'paid_amount'       => (float) $paidCollectedAmount,
                'paid_amount_fmt'   => '₹' . number_format((float) $paidCollectedAmount, 2),
            ],
            'active' => $filter,
        ]);
    }

    public function rejectRequest(Request $request, FlowerRequest $flowerRequest)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        // store as "Rejected" (matches your DB value too), but filter is case-insensitive
        $flowerRequest->status        = 'Rejected';
        $flowerRequest->cancel_by     = 'admin';
        $flowerRequest->cancel_reason = $validated['reason'];
        $flowerRequest->save();

        return redirect()->back()->with('success', 'Order rejected successfully.');
    }

    // ---------------------------
    // FILTER SWITCH (includes PENDING)
    // ---------------------------
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

    private function applyPendingFilter($query): void
    {
        // pending = NULL OR '' OR 'pending' (case-insensitive)
        $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhereRaw("TRIM(COALESCE(status,'')) = ''")
              ->orWhereRaw("LOWER(TRIM(status)) = 'pending'");
        });
    }

    private function applyApprovedFilter($query): void
    {
        $this->whereStatusEqualsLowerTrim($query, 'approved');
    }

    private function applyRejectedFilter($query): void
    {
        // handles "Rejected" also because we compare lower(trim())
        $this->whereStatusEqualsLowerTrim($query, 'rejected');
    }

    private function applyPaidFilter($query): void
    {
        // Paid = request.status = paid OR payment_status is successful
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
        // Unpaid = status approved AND NO successful payment exists
        $paidPaymentStatusesLower = $this->paidPaymentStatusesLower();

        $this->whereStatusEqualsLowerTrim($query, 'approved');

        $query->whereDoesntHave('order.flowerPayments', function ($p) use ($paidPaymentStatusesLower) {
            $this->wherePaymentStatusInLowerTrim($p, $paidPaymentStatusesLower);
        });
    }

    private function whereStatusEqualsLowerTrim($query, string $valueLower)
    {
        return $query->whereRaw("LOWER(TRIM(COALESCE(status,''))) = ?", [$valueLower]);
    }

    private function wherePaymentStatusInLowerTrim($query, array $valuesLower)
    {
        $valuesLower = array_values(array_unique(array_map('strtolower', $valuesLower)));
        if (count($valuesLower) === 0) {
            return $query->whereRaw('1=0');
        }

        $ph = implode(',', array_fill(0, count($valuesLower), '?'));
        return $query->whereRaw("LOWER(TRIM(COALESCE(payment_status,''))) IN ($ph)", $valuesLower);
    }

    private function paidPaymentStatusesLower(): array
    {
        // keep "approved" if your payment gateway stores it as success
        return ['approved', 'paid', 'success', 'captured'];
    }

    // ---------------------------
    // Amount Helpers
    // ---------------------------
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
        $order = $req->order ?? null;

        if ($order) {
            foreach (['total_price', 'total_amount', 'amount', 'payable_amount', 'order_total', 'grand_total'] as $col) {
                if (isset($order->{$col}) && is_numeric($order->{$col})) {
                    return (float) $order->{$col};
                }
            }
        }

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

}
