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
        $filter = strtolower(trim($request->query('filter', 'all')));

        // Card counts + amounts
        $counts = $this->buildCounts();

        // Table data (first load)
        $requests = $this->buildListQuery($filter)->paginate(10)->withQueryString();

        return view('admin.manage-flower-request', [
            'filter' => $filter,
            'requests' => $requests,

            'totalCount'    => $counts['total'],
            'pendingCount'  => $counts['pending'],
            'approvedCount' => $counts['approved'],
            'paidCount'     => $counts['paid'],
            'rejectedCount' => $counts['rejected'],
            'unpaidCount'   => $counts['unpaid'],

            'paidCollectedAmount'   => $counts['paidCollectedAmount'],
            'unpaidAmountToCollect' => $counts['unpaidAmountToCollect'],
        ]);
    }

    public function ajaxData(Request $request)
    {
        $filter = strtolower(trim($request->query('filter', 'all')));

        $requests = $this->buildListQuery($filter)->paginate(10)->withQueryString();

        $rowsHtml = '';
        foreach ($requests as $row) {
            $rowsHtml .= view('admin._row', compact('row'))->render();
        }

        return response()->json([
            'rows_html'       => $rowsHtml,
            'pagination_html' => $requests->links('pagination::bootstrap-4')->render(),
            'total'           => $requests->total(),
        ]);
    }

    private function successPaymentStatuses(): array
    {
        return ['paid', 'success', 'captured'];
        // If you also treat "approved" as paid in payment table, then use:
        // return ['paid', 'success', 'captured', 'approved'];
    }

    private function buildListQuery(string $filter)
    {
        $success = $this->successPaymentStatuses();
        $placeholders = implode(',', array_fill(0, count($success), '?'));

        $q = FlowerRequest::query()
            ->with([
                'user',
                'address',
                'flowerProduct',
                'order',               // request_id -> orders.request_id
                'order.flowerPayments' // orders.order_id -> flower_payments.order_id
            ])
            ->orderByDesc('id');

        if ($filter === 'pending') {
            $q->whereRaw('LOWER(status) = ?', ['pending']);
        } elseif ($filter === 'approved') {
            $q->whereRaw('LOWER(status) = ?', ['approved']);
        } elseif ($filter === 'paid') {
            $q->whereRaw('LOWER(status) = ?', ['paid']);
        } elseif ($filter === 'rejected') {
            // handles "Rejected" or "rejected"
            $q->whereRaw('LOWER(status) = ?', ['rejected']);
        } elseif ($filter === 'unpaid') {
            // Unpaid = approved AND (no order OR order exists but no successful payment)
            $q->whereRaw('LOWER(status) = ?', ['approved'])
              ->where(function ($x) use ($placeholders, $success) {
                  $x->whereDoesntHave('order')
                    ->orWhereHas('order', function ($oq) use ($placeholders, $success) {
                        $oq->whereDoesntHave('flowerPayments', function ($pq) use ($placeholders, $success) {
                            $pq->whereRaw("LOWER(payment_status) IN ($placeholders)", $success);
                        });
                    });
              });
        } else {
            // all
        }

        return $q;
    }

    private function buildCounts(): array
    {
        $success = $this->successPaymentStatuses();
        $placeholders = implode(',', array_fill(0, count($success), '?'));

        $base = FlowerRequest::query();

        $total    = (clone $base)->count();
        $pending  = (clone $base)->whereRaw('LOWER(status)=?', ['pending'])->count();
        $approved = (clone $base)->whereRaw('LOWER(status)=?', ['approved'])->count();
        $paid     = (clone $base)->whereRaw('LOWER(status)=?', ['paid'])->count();
        $rejected = (clone $base)->whereRaw('LOWER(status)=?', ['rejected'])->count();

        // Unpaid count: approved AND (no order OR no successful payment)
        $unpaid = (clone $base)
            ->whereRaw('LOWER(status)=?', ['approved'])
            ->where(function ($x) use ($placeholders, $success) {
                $x->whereDoesntHave('order')
                  ->orWhereHas('order', function ($oq) use ($placeholders, $success) {
                      $oq->whereDoesntHave('flowerPayments', function ($pq) use ($placeholders, $success) {
                          $pq->whereRaw("LOWER(payment_status) IN ($placeholders)", $success);
                      });
                  });
            })
            ->count();

        // Paid Collected Amount: sum successful payments for requests with status = paid
        $paidCollectedAmount = FlowerPayment::query()
            ->whereHas('order.flowerRequest', function ($rq) {
                $rq->whereRaw('LOWER(status)=?', ['paid']);
            })
            ->whereRaw("LOWER(payment_status) IN ($placeholders)", $success)
            ->sum('paid_amount');

        // Unpaid Amount to Collect: sum (orders.total_price + orders.delivery_charge) for approved+unpaid
        // NOTE: This avoids grand_total_price column issue.
        $unpaidAmountToCollect = DB::table('flower_requests as fr')
            ->leftJoin('orders as o', 'o.request_id', '=', 'fr.request_id')
            ->whereRaw('LOWER(fr.status)=?', ['approved'])
            ->where(function ($x) {
                $x->whereNull('o.order_id')
                  ->orWhereRaw('o.order_id IS NOT NULL AND NOT EXISTS (
                        SELECT 1
                        FROM flower_payments p2
                        WHERE p2.order_id = o.order_id
                          AND LOWER(p2.payment_status) IN ("paid","success","captured")
                    )');
            })
            ->selectRaw('SUM(COALESCE(o.total_price,0) + COALESCE(o.delivery_charge,0)) as total_due')
            ->value('total_due');

        return [
            'total' => (int) $total,
            'pending' => (int) $pending,
            'approved' => (int) $approved,
            'paid' => (int) $paid,
            'rejected' => (int) $rejected,
            'unpaid' => (int) $unpaid,

            'paidCollectedAmount' => (float) ($paidCollectedAmount ?? 0),
            'unpaidAmountToCollect' => (float) ($unpaidAmountToCollect ?? 0),
        ];
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
