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
        $filters = [
            'q'      => trim($request->get('q', '')),
            'from'   => $request->get('from'),
            'to'     => $request->get('to'),
            'method' => $request->get('method', ''),
            'min'    => $request->get('min'),
            'max'    => $request->get('max'),
        ];

        // Case-insensitive column helper
        $ci = fn($col) => DB::raw("LOWER($col)");

        // Accept common “pending” labels, case-insensitively
        $PENDING_VALUES = ['pending', 'unpaid', 'due'];

        // ====== PENDING (grouped per user) ======
        $pendingBase = DB::table('flower_payments as fp')
            ->join('users as u', 'u.userid', '=', 'fp.user_id')
            ->leftJoin('subscriptions as s', 's.order_id', '=', 'fp.order_id')
            ->leftJoin('flower_products as p', 'p.product_id', '=', 's.product_id')
            ->whereIn($ci('fp.payment_status'), $PENDING_VALUES)
            ->select([
                'fp.user_id',
                'u.name as user_name',
                'u.mobile_number',

                // NOTE: in your schema, the due amount is stored in fp.paid_amount for pending rows.
                DB::raw('COALESCE(SUM(fp.paid_amount),0) as due_amount'),

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
        if (is_numeric($filters['min'])) $pendingBase->havingRaw('COALESCE(SUM(fp.paid_amount),0) >= ?', [(float) $filters['min']]);
        if (is_numeric($filters['max'])) $pendingBase->havingRaw('COALESCE(SUM(fp.paid_amount),0) <= ?', [(float) $filters['max']]);

        // IMPORTANT: isolate pagination param to avoid empty page issue
        $pendingPayments     = (clone $pendingBase)
            ->orderByDesc('latest_pending_since')
            ->paginate(25, ['*'], 'pending_page')
            ->withQueryString();

        $pendingCount        = (clone $pendingBase)->count();
        $pendingTotalAmount  = (clone $pendingBase)->get()->sum('due_amount');

        return view('admin.payment-pending.index', [
            'pendingPayments'     => $pendingPayments,
            'pendingTotalAmount'  => $pendingTotalAmount,
            'pendingCount'        => $pendingCount,
            'filters'             => $filters,
            'methods'             => ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'],
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
