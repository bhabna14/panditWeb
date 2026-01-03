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
     private array $successPaymentStatuses = ['paid', 'success', 'captured'];

    private function normalizeFilter(?string $filter): string
    {
        $filter = strtolower(trim((string) $filter));

        return match ($filter) {
            'paid'      => 'paid',
            'rejected'  => 'rejected',
            'pending'   => 'pending',
            'approved'  => 'approved',
            'unpaid'    => 'unpaid',
            'all'       => 'all',
            default     => 'pending',
        };
    }

    private function applyFilter($query, string $filter)
    {
        $success = $this->successPaymentStatuses;

        if ($filter === 'paid') {
            return $query->whereRaw('LOWER(status) = ?', ['paid']);
        }

        if ($filter === 'rejected') {
            return $query->whereRaw('LOWER(status) = ?', ['rejected']);
        }

        if ($filter === 'pending') {
            return $query->whereRaw('LOWER(status) = ?', ['pending']);
        }

        if ($filter === 'approved') {
            return $query->whereRaw('LOWER(status) = ?', ['approved']);
        }

        /**
         * UNPAID definition:
         * flower_requests.status = approved
         * AND (order missing OR order exists but has NO successful payment)
         */
        if ($filter === 'unpaid') {
            return $query
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->where(function ($q) use ($success) {
                    // approved but order not created yet => unpaid
                    $q->whereDoesntHave('order')

                      // OR order exists but no successful payment exists for that order_id
                      ->orWhereHas('order', function ($oq) use ($success) {
                          $oq->whereDoesntHave('flowerPayments', function ($pq) use ($success) {
                              $pq->whereIn(DB::raw('LOWER(payment_status)'), $success);
                          });
                      });
                });
        }

        // all
        return $query;
    }

    private function getCounts(): array
    {
        $success = $this->successPaymentStatuses;

        $paidCount = FlowerRequest::whereRaw('LOWER(status) = ?', ['paid'])->count();
        $rejectedCount = FlowerRequest::whereRaw('LOWER(status) = ?', ['rejected'])->count();
        $pendingCount = FlowerRequest::whereRaw('LOWER(status) = ?', ['pending'])->count();
        $approvedCount = FlowerRequest::whereRaw('LOWER(status) = ?', ['approved'])->count();

        $unpaidCount = FlowerRequest::query()
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->where(function ($q) use ($success) {
                $q->whereDoesntHave('order')
                  ->orWhereHas('order', function ($oq) use ($success) {
                      $oq->whereDoesntHave('flowerPayments', function ($pq) use ($success) {
                          $pq->whereIn(DB::raw('LOWER(payment_status)'), $success);
                      });
                  });
            })
            ->count();

        // Collected money only for "paid" requests (by successful payments)
        $paidCollectedAmount = (float) FlowerPayment::query()
            ->whereIn(DB::raw('LOWER(payment_status)'), $success)
            ->whereHas('order.flowerRequest', function ($q) {
                $q->whereRaw('LOWER(status) = ?', ['paid']);
            })
            ->sum('paid_amount');

        return [
            'paid' => $paidCount,
            'rejected' => $rejectedCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'unpaid' => $unpaidCount,
            'paid_collected' => $paidCollectedAmount,
        ];
    }

    private function collectedAmountForFilter(string $filter): float
    {
        $success = $this->successPaymentStatuses;

        if ($filter !== 'paid' && $filter !== 'all') {
            return 0.0;
        }

        $q = FlowerPayment::query()
            ->whereIn(DB::raw('LOWER(payment_status)'), $success);

        if ($filter === 'paid') {
            $q->whereHas('order.flowerRequest', function ($qr) {
                $qr->whereRaw('LOWER(status) = ?', ['paid']);
            });
        }

        return (float) $q->sum('paid_amount');
    }

    /**
     * Page load
     */
    public function showRequests(Request $request)
    {
        $filter = $this->normalizeFilter($request->query('filter', 'pending'));

        $counts = $this->getCounts();

        $query = FlowerRequest::query()
            ->with([
                'user',
                'address',
                'flowerProduct',
                'order.latestPayment',
                'order.latestSuccessfulPayment',
            ])
            ->orderByDesc('id');

        $query = $this->applyFilter($query, $filter);

        $requests = $query->paginate(15)->withQueryString();
        $collected = $this->collectedAmountForFilter($filter);

        return view('admin.flower-request.manage-flower-request', compact('filter', 'counts', 'requests', 'collected'));
    }

    /**
     * AJAX load (cards + pagination)
     */
    public function ajaxData(Request $request)
    {
        $filter = $this->normalizeFilter($request->query('filter', 'pending'));

        $query = FlowerRequest::query()
            ->with([
                'user',
                'address',
                'flowerProduct',
                'order.latestPayment',
                'order.latestSuccessfulPayment',
            ])
            ->orderByDesc('id');

        $query = $this->applyFilter($query, $filter);

        $requests = $query->paginate(15)->withQueryString();
        $collected = $this->collectedAmountForFilter($filter);

        return response()->json([
            'filter' => $filter,
            'label' => ucfirst($filter),
            'total' => $requests->total(),
            'collected_amount' => number_format($collected, 2),
            'html' => view('admin.flower-request._rows', compact('requests'))->render(),
            'pagination' => $requests->links('pagination::bootstrap-5')->render(),
        ]);
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
