<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\FlowerPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentCollectionController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q'      => trim($request->get('q', '')),
            'from'   => $request->get('from'),
            'to'     => $request->get('to'),
            'method' => $request->get('method', ''),
            'min'    => $request->get('min'),
            'max'    => $request->get('max'),
        ];

        $pendingBase = DB::table('flower_payments as fp')
            ->join('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->join('users as u', 'u.userid', '=', 'fp.user_id')
            ->leftJoin('flower_products as p', 'p.product_id', '=', 's.product_id')
            ->where('fp.payment_status', 'pending')
            ->select([
                'fp.id as payment_row_id',
                'fp.payment_id',
                'fp.order_id',
                'fp.user_id',
                'fp.paid_amount as amount',
                'fp.payment_status',
                'fp.payment_method',
                'fp.created_at as pending_since',
                's.subscription_id',
                's.start_date',
                's.end_date',
                's.status as subscription_status',
                'p.name as product_name',
                'p.category as product_category',
                'u.name as user_name',
                'u.mobile_number',
            ]);

        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $pendingBase->where(function ($qq) use ($q) {
                $qq->where('u.name', 'like', "%{$q}%")
                   ->orWhere('u.mobile_number', 'like', "%{$q}%")
                   ->orWhere('fp.order_id', 'like', "%{$q}%")
                   ->orWhere('s.subscription_id', 'like', "%{$q}%")
                   ->orWhere('p.name', 'like', "%{$q}%")
                   ->orWhere('p.category', 'like', "%{$q}%");
            });
        }
        if ($filters['from']) $pendingBase->whereDate('fp.created_at', '>=', $filters['from']);
        if ($filters['to'])   $pendingBase->whereDate('fp.created_at', '<=', $filters['to']);
        if ($filters['method'] !== '') $pendingBase->where('fp.payment_method', $filters['method']);
        if (is_numeric($filters['min'])) $pendingBase->where('fp.paid_amount', '>=', (float)$filters['min']);
        if (is_numeric($filters['max'])) $pendingBase->where('fp.paid_amount', '<=', (float)$filters['max']);

        $pendingPayments     = (clone $pendingBase)->orderByDesc('fp.id')->get();
        $pendingTotalAmount  = (clone $pendingBase)->sum('fp.paid_amount');
        $pendingCount        = (clone $pendingBase)->count();

        $liveStatuses = ['active', 'paused', 'resume'];

        $subQuery = DB::table('subscriptions as s')
            ->select('s.user_id', DB::raw('MAX(s.end_date) as latest_end_date'))
            ->where('s.status', 'expired')
            ->whereNotExists(function ($q) use ($liveStatuses) {
                $q->select(DB::raw(1))
                  ->from('subscriptions as sa')
                  ->whereColumn('sa.user_id', 's.user_id')
                  ->whereIn('sa.status', $liveStatuses);
            })
            ->groupBy('s.user_id');

        $expiredBase = DB::table('subscriptions')
            ->joinSub($subQuery, 'latest_expired', function ($join) {
                $join->on('subscriptions.user_id', '=', 'latest_expired.user_id')
                     ->on('subscriptions.end_date', '=', 'latest_expired.latest_end_date');
            })
            ->join('users as u', 'u.userid', '=', 'subscriptions.user_id')
            ->leftJoin('flower_products as p', 'p.product_id', '=', 'subscriptions.product_id')
            ->where('subscriptions.status', 'expired')
            ->whereNotExists(function ($q) use ($liveStatuses) {
                $q->select(DB::raw(1))
                  ->from('subscriptions as so')
                  ->whereColumn('so.order_id', 'subscriptions.order_id')
                  ->whereIn('so.status', $liveStatuses);
            })
            ->select([
                'subscriptions.subscription_id',
                'subscriptions.order_id',
                'subscriptions.user_id',
                'subscriptions.start_date',
                'subscriptions.end_date',
                'subscriptions.status',
                'u.name as user_name',
                'u.mobile_number',
                'p.name as product_name',
                'p.category as product_category',
            ]);

        $expiredSubs  = (clone $expiredBase)->orderByDesc('subscriptions.end_date')->get();
        $expiredCount = (clone $expiredBase)->count();

        return view('admin.payment-collection.index', [
            'pendingPayments'     => $pendingPayments,
            'pendingTotalAmount'  => $pendingTotalAmount,
            'pendingCount'        => $pendingCount,
            'expiredSubs'         => $expiredSubs,
            'expiredCount'        => $expiredCount,
            'filters'             => $filters,
            'methods'             => ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'],
        ]);
    }

    // public function collect(Request $request)
    // {
    //     // Validate fields from modal
    //     $data = $request->validate([
    //         'payment_row_id' => ['required', 'integer', 'exists:flower_payments,id'],
    //         'amount'         => ['required', 'numeric', 'min:0'],
    //         'payment_method' => ['required', Rule::in(['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'])],
    //         'received_by'    => ['required', 'string', 'max:100'],
    //     ]);

    //     // Confirm row exists and is pending
    //     $payment = DB::table('flower_payments')
    //         ->where('id', $data['payment_row_id'])
    //         ->first();

    //     if (!$payment) {
    //         return back()->with('error', 'Payment row not found.');
    //     }
    //     if (strtolower((string)$payment->payment_status) !== 'pending') {
    //         return back()->with('error', 'Payment is not in pending state.');
    //     }

    //     // Update to PAID
    //     $updated = DB::table('flower_payments')
    //         ->where('id', $data['payment_row_id'])
    //         ->update([
    //             'paid_amount'    => $data['amount'],
    //             'payment_method' => $data['payment_method'],
    //             'payment_status' => 'paid',
    //             'received_by'    => $data['received_by'],
    //             'updated_at'     => now(),
    //         ]);

    //     if (!$updated) {
    //         return back()->with('error', 'Update failed.');
    //     }

    //     return redirect()
    //         ->route('payment.collection.index', [], 303)
    //         ->with('success', 'Payment marked as paid successfully.');
    // }
  public function collect(Request $request, $id)
    {
        // keep methods consistent with your index() view
        $allowedMethods = ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'];

        // force JSON behavior on validation errors for AJAX callers
        if (! $request->wantsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        $validated = $request->validate([
            'amount'          => ['required', 'numeric', 'min:0'],
            'payment_method'  => ['required', 'string', 'in:' . implode(',', $allowedMethods)],
            'received_by'     => ['required', 'string', 'max:100'],
        ]);

        $payment = FlowerPayment::query()
            ->where('id', $id)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($payment, $validated) {
            $payment->paid_amount    = $validated['amount'];
            $payment->payment_method = $validated['payment_method'];
            $payment->received_by    = $validated['received_by'];
            $payment->payment_status = 'paid';
            $payment->save();
        });

        return response()->json([
            'ok'      => true,
            'message' => 'Payment marked as paid.',
            'data'    => [
                'id'              => $payment->id,
                'order_id'        => $payment->order_id,
                'payment_id'      => $payment->payment_id,
                'paid_amount'     => (float) $payment->paid_amount,
                'payment_method'  => $payment->payment_method,
                'received_by'     => $payment->received_by,
                'payment_status'  => $payment->payment_status,
                'updated_at'      => optional($payment->updated_at)->toDateTimeString(),
            ],
        ], 200);
    }
}
