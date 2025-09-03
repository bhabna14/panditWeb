<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PaymentCollectionController extends Controller
{
    public function index(Request $request)
    {
        // ----- PENDING PAYMENTS -----
        // Flower payments with status 'pending' + useful joins for display.
        $pendingPayments = DB::table('flower_payments as fp')
            ->join('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->join('users as u', 'u.userid', '=', 'fp.user_id')
            ->leftJoin('flower_products as p', 'p.product_id', '=', 's.product_id')
            ->where('fp.payment_status', 'pending')
            ->select([
                'fp.id as payment_row_id',
                'fp.payment_id',
                'fp.order_id',
                'fp.user_id',
                'fp.paid_amount as amount', // treated as due amount for pending
                'fp.payment_status',
                's.subscription_id',
                's.start_date',
                's.end_date',
                's.status as subscription_status',
                'p.name as product_name',
                'p.category as product_category',
                'u.name as user_name',
                'u.mobile_number',
            ])
            ->orderByDesc('fp.id')
            ->get();

        // ----- EXPIRED SUBSCRIPTIONS -----
        // Show each user’s latest expired sub ONLY if they have no live (active/paused/resume) sub.
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

        $expiredSubs = DB::table('subscriptions')
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
            ])
            ->orderByDesc('subscriptions.end_date')
            ->get();

        return view('admin.payment-collection.index', [
            'pendingPayments' => $pendingPayments,
            'expiredSubs'     => $expiredSubs,
        ]);
    }

    public function collect(Request $request)
    {
        // We’ll update a single flower_payments row from the modal.
        $data = $request->validate([
            'payment_row_id' => ['required', 'integer', 'exists:flower_payments,id'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::in(['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'])],
            'received_by'    => ['required', 'string', 'max:100'],
        ]);

        // Only switch from pending -> paid (or received)
        $updated = DB::table('flower_payments')
            ->where('id', $data['payment_row_id'])
            ->where('payment_status', 'pending')
            ->update([
                'paid_amount'    => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
                'received_by'    => $data['received_by'], // requires column (see migration note)
                'updated_at'     => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Payment was not in pending state or not found.');
        }

        return redirect()
            ->route('payment.collection.index', [], 303)
            ->with('success', 'Payment marked as paid successfully.');
    }
}
