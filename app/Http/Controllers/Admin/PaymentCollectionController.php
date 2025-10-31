<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowerPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentCollectionController extends Controller
{
    public function index(Request $request)
    {
        // ===== Per-page: default 10; accept "all"
        $perPageRaw = $request->get('per_page', 10);
        $perPage    = (is_numeric($perPageRaw) ? (int)$perPageRaw : $perPageRaw);
        $isAll      = (is_string($perPage) && strtolower($perPage) === 'all');
        // Safe "all" upper bound to avoid memory blowups; adjust if needed
        $ALL_LIMIT  = 100000;

        $filters = [
            'q'      => trim($request->get('q', '')),
            'from'   => $request->get('from'),
            'to'     => $request->get('to'),
            'method' => $request->get('method', ''),
            'min'    => $request->get('min'),
            'max'    => $request->get('max'),
        ];

        // =========================
        // PENDING (same logic)
        // =========================
        $pendingBase = DB::table('flower_payments as fp')
            ->join('users as u', 'u.userid', '=', 'fp.user_id')
            ->leftJoin('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('flower_products as p', 'p.product_id', '=', 's.product_id')
            ->where('fp.payment_status', 'pending')
            ->select([
                'fp.user_id',
                'u.name as user_name',
                'u.mobile_number',
                DB::raw('MAX(fp.paid_amount) as due_amount'),
                DB::raw('MAX(fp.id) as latest_payment_row_id'),
                DB::raw('MAX(fp.order_id) as latest_order_id'),
                DB::raw('MAX(fp.created_at) as latest_pending_since'),
                DB::raw('MAX(s.subscription_id) as subscription_id'),
                DB::raw('MAX(s.start_date) as start_date'),
                DB::raw('MAX(s.end_date) as end_date'),
                DB::raw('MAX(s.status) as subscription_status'),
                DB::raw('MAX(p.name) as product_name'),
                DB::raw('MAX(p.category) as product_category'),
                DB::raw('MAX(fp.payment_method) as payment_method'),
            ])
            ->groupBy('fp.user_id', 'u.name', 'u.mobile_number');

        // Filters for pending
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
        if ($filters['from'])            $pendingBase->whereDate('fp.created_at', '>=', $filters['from']);
        if ($filters['to'])              $pendingBase->whereDate('fp.created_at', '<=', $filters['to']);
        if ($filters['method'] !== '')   $pendingBase->where('fp.payment_method', $filters['method']);
        if (is_numeric($filters['min'])) $pendingBase->havingRaw('SUM(fp.paid_amount) >= ?', [(float) $filters['min']]);
        if (is_numeric($filters['max'])) $pendingBase->havingRaw('SUM(fp.paid_amount) <= ?', [(float) $filters['max']]);

        // Wrap in subquery for page-wise results AND accurate totals
        $pendingPaginateQuery = DB::query()
            ->fromSub($pendingBase, 'pb')
            ->orderByDesc('latest_payment_row_id');

        $pendingPayments = $isAll
            ? $pendingPaginateQuery->limit($ALL_LIMIT)->get()
            : $pendingPaginateQuery->paginate((int)$perPage)->withQueryString();

        $pendingCount       = DB::query()->fromSub($pendingBase, 'pb')->count();
        $pendingTotalAmount = DB::query()->fromSub($pendingBase, 'pb')->sum('due_amount');

        // =========================
        // PAID
        // =========================
        $paidBase = DB::table('flower_payments as fp')
            ->join('users as u', 'u.userid', '=', 'fp.user_id')
            ->leftJoin('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('flower_products as p', 'p.product_id', '=', 's.product_id')
            ->where('fp.payment_status', 'paid')
            ->where('s.status', 'active')
            ->select([
                'fp.id',
                'fp.order_id',
                'fp.user_id',
                'fp.paid_amount',
                'fp.payment_method',
                'fp.created_at as paid_at',
                'u.name as user_name',
                'u.mobile_number',
                's.subscription_id',
                's.start_date',
                's.end_date',
                'p.name as product_name',
                'p.category as product_category',
            ]);

        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $paidBase->where(function ($qq) use ($q) {
                $qq->where('u.name', 'like', "%{$q}%")
                    ->orWhere('u.mobile_number', 'like', "%{$q}%")
                    ->orWhere('fp.order_id', 'like', "%{$q}%")
                    ->orWhere('s.subscription_id', 'like', "%{$q}%")
                    ->orWhere('p.name', 'like', "%{$q}%")
                    ->orWhere('p.category', 'like', "%{$q}%");
            });
        }
        if ($filters['from'])            $paidBase->whereDate('fp.created_at', '>=', $filters['from']);
        if ($filters['to'])              $paidBase->whereDate('fp.created_at', '<=', $filters['to']);
        if ($filters['method'] !== '')   $paidBase->where('fp.payment_method', $filters['method']);
        if (is_numeric($filters['min'])) $paidBase->where('fp.paid_amount', '>=', (float) $filters['min']);
        if (is_numeric($filters['max'])) $paidBase->where('fp.paid_amount', '<=', (float) $filters['max']);

        $paidBaseOrdered = (clone $paidBase)->orderByDesc('fp.id');

        $paidPayments = $isAll
            ? $paidBaseOrdered->limit($ALL_LIMIT)->get()
            : $paidBaseOrdered->paginate((int)$perPage)->withQueryString();

        $paidCount       = (clone $paidBase)->count();
        $paidTotalAmount = (clone $paidBase)->sum('fp.paid_amount');

        // =========================
        // EXPIRED (page-wise)
        // =========================
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

        $expiredOrdered = (clone $expiredBase)->orderByDesc('subscriptions.end_date');

        $expiredSubs = $isAll
            ? $expiredOrdered->limit($ALL_LIMIT)->get()
            : $expiredOrdered->paginate((int)$perPage)->withQueryString();

        $expiredCount = (clone $expiredBase)->count();

        // Build method list (or keep your own)
        $methods = ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'];

        return view('admin.payment-collection.index', [
            'filters'            => $filters,
            'methods'            => $methods,
            'perPage'            => $isAll ? 'all' : (int)$perPage,

            // Pending
            'pendingPayments'    => $pendingPayments,
            'pendingCount'       => $pendingCount,
            'pendingTotalAmount' => $pendingTotalAmount,

            // Paid
            'paidPayments'       => $paidPayments,
            'paidCount'          => $paidCount,
            'paidTotalAmount'    => $paidTotalAmount,

            // Expired
            'expiredSubs'        => $expiredSubs,
            'expiredCount'       => $expiredCount,
        ]);
    }

    public function collect(Request $request, $id)
    {
        $allowedMethods = ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'];

        // Make sure validation errors return JSON for the AJAX caller
        if (!$request->wantsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', Rule::in($allowedMethods)],
            'received_by'    => ['required', 'string', 'max:100'],
        ]);

        // Do everything atomically. Apply FOR UPDATE lock on the query (NOT on the model instance).
        $payment = null;
        DB::transaction(function () use ($id, $validated, &$payment) {
            $payment = FlowerPayment::query()
                ->whereKey($id)
                ->where('payment_status', 'pending')
                ->lockForUpdate()
                ->firstOrFail();

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
                'id'             => $payment->id,
                'order_id'       => $payment->order_id,
                'payment_id'     => $payment->payment_id,
                'paid_amount'    => (float) $payment->paid_amount,
                'payment_method' => $payment->payment_method,
                'received_by'    => $payment->received_by,
                'payment_status' => $payment->payment_status,
                'updated_at'     => optional($payment->updated_at)->toDateTimeString(),
            ],
        ], 200);
    }
}
