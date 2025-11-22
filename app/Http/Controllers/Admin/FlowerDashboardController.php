<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Subscription;
use App\Models\FlowerPickupDetails;
use App\Models\RiderDetails;
use App\Models\FlowerRequest;
use App\Models\ReferOfferClaim;
use App\Models\FLowerReferal;
use App\Models\User;
use App\Models\ReferOffer;
use App\Models\MarketingVisitPlace;
use App\Models\SubscriptionPauseResumeLog;
use App\Models\Order;
use Carbon\CarbonPeriod;
use App\Models\FlowerPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;

class FlowerDashboardController extends Controller
{
    public function liveTodayMetrics(): JsonResponse
    {
        $tz       = config('app.timezone');
        $today    = Carbon::today($tz);
        $todayStr = $today->toDateString();

        // Use 'date' for customize orders scheduled today
        $ordersRequestedToday = FlowerRequest::whereDate('date', $todayStr)->count();

        $newUserSubscription = Subscription::where('status', 'pending')
            ->whereDate('created_at', $today)
            ->groupBy('user_id')
            ->selectRaw('MIN(order_id) as order_id, user_id')
            ->get()
            ->filter(fn ($sub) => Subscription::where('user_id', $sub->user_id)->count() === 1)
            ->count();

        $renewSubscription = Subscription::whereDate('created_at', $today)
            ->whereIn('order_id', function ($q) {
                $q->select('order_id')->from('subscriptions')
                ->groupBy('order_id')->havingRaw('COUNT(order_id) > 1');
            })
            ->count();

        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', $todayStr)
            ->where('delivery_status', 'delivered')->count();

        return response()->json([
            'ok' => true,
            'data' => [
                'ordersRequestedToday' => (int)$ordersRequestedToday,
                'newUserSubscription'  => (int)$newUserSubscription,
                'renewSubscription'    => (int)$renewSubscription,
                'totalDeliveriesToday' => (int)$totalDeliveriesToday,
            ],
            'ts' => now($tz)->toIso8601String(),
        ]);
    }
    
public function showOrders(Request $request)
{
    // TZ-safe "today"
    $tz         = config('app.timezone');
    $todayStart = Carbon::today($tz)->startOfDay();
    $todayEnd   = (clone $todayStart)->endOfDay();

    // Base query + eager loads (use singular 'user' relation)
    $query = Subscription::with([
        'order.address.localityDetails',
        'flowerPayments',
        'user',
        'flowerProducts',
        'pauseResumeLog',
        'order.rider',
    ])->orderByDesc('id');

    $filter = $request->query('filter');

    if ($filter === 'rider') {
        $query->where('status', 'active')
            ->whereHas('order', function ($q) {
                $q->whereNull('rider_id')->orWhere('rider_id', '');
            });
    }

    if ($filter === 'end') {
        $query->where(function ($dateQuery) use ($todayStart, $todayEnd) {
            $dateQuery->where(function ($sq) use ($todayStart, $todayEnd) {
                    $sq->whereNotNull('new_date')
                        ->whereBetween('new_date', [$todayStart, $todayEnd]);
                })
                ->orWhere(function ($sq) use ($todayStart, $todayEnd) {
                    $sq->whereNull('new_date')
                        ->whereBetween('end_date', [$todayStart, $todayEnd]);
                });
        })
        ->where('status', 'active')
        ->withoutOtherActiveOrPending();
    }

    if ($filter === 'fivedays') {
        $today = Carbon::today($tz);

        $winStart = $today->copy()->addDay()->startOfDay();   // tomorrow
        $winEnd   = $today->copy()->addDays(5)->endOfDay();   // today + 5 days

        $query->where('status', 'active')
            ->whereRaw('COALESCE(new_date, end_date) BETWEEN ? AND ?', [$winStart, $winEnd]);
    }

    if ($filter === 'tomorrowOrder') {
        $tomorrow = Carbon::tomorrow($tz)->toDateString();
        $query->where('status', 'pending')
            ->whereDate('start_date', $tomorrow);
    }

    if ($filter === 'todayrequest') {
        $query->whereIn('subscription_id', function ($sub) use ($todayStart, $todayEnd) {
            $sub->select('subscription_id')
                ->from('subscription_pause_resume_logs')
                ->where('action', 'paused')
                ->whereBetween('created_at', [$todayStart, $todayEnd]);
        });
    }

    // 🔹 NEW: all subscriptions placed today
    if ($filter === 'today') {
        $query->whereBetween('created_at', [$todayStart, $todayEnd]);
    }

    if ($filter === 'new') {
        $firstRowsSub = DB::table('subscriptions as s1')
            ->join(
                DB::raw('(SELECT user_id, MIN(created_at) AS first_created_at
                        FROM subscriptions
                        GROUP BY user_id) f'),
                function ($join) {
                    $join->on('s1.user_id', '=', 'f.user_id')
                        ->on('s1.created_at', '=', 'f.first_created_at');
                }
            )
            ->whereBetween('s1.created_at', [$todayStart, $todayEnd])
            ->select('s1.id');

        $query->whereIn('id', $firstRowsSub);
    }

    if ($filter === 'renewed') {
        $query->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 'pending')
            ->whereExists(function ($q) use ($todayStart) {
                $q->select(DB::raw(1))
                    ->from('subscriptions as prev')
                    ->whereColumn('prev.user_id', 'subscriptions.user_id')
                    ->where('prev.created_at', '<', $todayStart);
            });
    }

    if ($filter === 'active') {
        $query->where('status', 'active');
    }

    if ($filter === 'expired') {
        $monthStart = Carbon::now($tz)->startOfMonth();
        $monthEnd   = Carbon::now($tz)->endOfMonth();

        // subquery returning latest subscription id per user
        $latestPerUserIds = DB::table('subscriptions as s1')
            ->selectRaw('MAX(s1.id) as id')
            ->groupBy('s1.user_id');

        $query->whereIn('id', $latestPerUserIds)
            ->where('status', 'expired')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$monthStart, $monthEnd])
            ->whereRaw('DATE_ADD(end_date, INTERVAL 30 DAY) <= ?', [$monthEnd->toDateString()])
            ->whereHas('user', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('status', '!=', 'active')
                       ->orWhere('is_active', false);
                });
            });
    }

    if ($filter === 'discontinued') {
        $twoMonthsAgo = Carbon::now($tz)->subMonths(2);
        $liveStatuses = ['active', 'paused', 'resume'];

        $query->where('status', 'expired')
            ->whereNotExists(function ($q) use ($liveStatuses) {
                $q->select(DB::raw(1))
                    ->from('subscriptions as s2')
                    ->whereColumn('s2.user_id', 'subscriptions.user_id')
                    ->whereIn('s2.status', $liveStatuses);
            })
            ->whereNotExists(function ($q) use ($liveStatuses) {
                $q->select(DB::raw(1))
                    ->from('subscriptions as s3')
                    ->whereColumn('s3.order_id', 'subscriptions.order_id')
                    ->whereIn('s3.status', $liveStatuses);
            })
            ->where(function ($q) use ($twoMonthsAgo) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '<', $twoMonthsAgo);
            })
            ->whereIn('id', function ($sub) {
                $sub->select(DB::raw('MAX(id)'))
                    ->from('subscriptions')
                    ->groupBy('user_id');
            });
    }

    if ($filter === 'paused') {
        $query->where('status', 'paused');
    }

    $tomorrowStart = (clone $todayStart)->addDay();
    $tomorrowEnd   = (clone $tomorrowStart)->endOfDay();

    if ($filter === 'tomorrow') { // corrected spelling
        $query->where('status', 'active')
            ->whereBetween('pause_start_date', [$tomorrowStart, $tomorrowEnd]);
    }

    if ($filter === 'nextdayresumed') {
        $query->where('status', 'active')
            ->whereBetween('pause_end_date', [$tomorrowStart, $tomorrowEnd]);
    }

    // ---- Search fields ----
    if ($request->filled('customer_name')) {
        $name = $request->customer_name;
        $query->whereHas('user', fn($q) => $q->where('name', $name));
    }

    if ($request->filled('mobile_number')) {
        $mobile = $request->mobile_number;
        $query->whereHas('user', fn($q) => $q->where('mobile_number', $mobile));
    }

    if ($request->filled('apartment_name')) {
        $apt = $request->apartment_name;
        $query->whereHas('order.address', fn($q) => $q->where('apartment_name', $apt));
    }

    if ($request->filled('apartment_flat_plot')) {
        $flat = $request->apartment_flat_plot;
        $query->whereHas('order.address', fn($q) => $q->where('apartment_flat_plot', $flat));
    }

    if ($request->ajax()) {
        return datatables()->eloquent($query)->toJson();
    }

    // Card counts
    $activeSubscriptions  = Subscription::where('status', 'active')->count();
    $pausedSubscriptions  = Subscription::where('status', 'paused')->count();
    $ordersRequestedToday = Subscription::whereBetween('created_at', [$todayStart, $todayEnd])->count();
    $riders               = RiderDetails::where('status', 'active')->get();

    $users = User::select('name', 'mobile_number')->distinct()->orderBy('name')->get();

    $apartmentNames = UserAddress::query()
        ->whereNotNull('apartment_name')
        ->where('apartment_name', '!=', '')
        ->selectRaw('DISTINCT TRIM(apartment_name) AS apartment_name')
        ->orderBy('apartment_name')
        ->pluck('apartment_name');

    $apartmentNumbers = UserAddress::query()
        ->whereNotNull('apartment_flat_plot')
        ->where('apartment_flat_plot', '!=', '')
        ->selectRaw('DISTINCT TRIM(apartment_flat_plot) AS apartment_flat_plot')
        ->orderBy('apartment_flat_plot')
        ->pluck('apartment_flat_plot');

    return view('admin.flower-order.manage-flower-orders', compact(
        'riders',
        'activeSubscriptions',
        'pausedSubscriptions',
        'ordersRequestedToday',
        'users',
        'apartmentNames',
        'apartmentNumbers'
    ));
}



    public function showTodayDeliveries()
    {
        $today = Carbon::today()->startOfDay();

        $activeSubscriptions = \App\Models\Subscription::with([
                'users:id,userid,name,mobile_number',
                'order:id,order_id,user_id,address_id,total_price,created_at,rider_id',
                'order.address:id,user_id,country,state,city,pincode,area,locality,apartment_name,apartment_flat_plot,landmark,address_type',
                'order.address.localityDetails:id,locality_name,unique_code,pincode',
                'order.rider:id,rider_id,rider_name',
                'flowerProducts:id,product_id,name,product_image,price,per_day_price,duration',
                'order.deliveryHistories' => function ($q) use ($today) {
                    $q->whereDate('created_at', $today)
                      ->where('delivery_status', 'delivered')
                      ->latest('created_at');
                },
                'order.deliveryHistories.rider:id,rider_name'
            ])
            ->where('status', 'active')
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($sub) use ($today) {
                // -------- Normalize dates --------
                $start = $sub->start_date ? Carbon::parse($sub->start_date)->startOfDay() : null;
                $end   = $sub->end_date   ? Carbon::parse($sub->end_date)->endOfDay()   : null;
                $new   = $sub->new_date   ? Carbon::parse($sub->new_date)->startOfDay() : null;

                // -------- Effective window (NO extension of end) --------
                // If new_date exists and is after start_date, use new_date as effective start.
                // Otherwise keep start_date. If only new_date exists, use it.
                $effectiveStart = $start;
                if ($new && $start) {
                    $effectiveStart = $new->gt($start) ? $new : $start;
                } elseif ($new && !$start) {
                    $effectiveStart = $new;
                }

                $effectiveEnd = $end;

                // Guard: unusable/inverted -> zero days left
                if (!$effectiveStart || !$effectiveEnd || $effectiveEnd->lt($effectiveStart)) {
                    $days_total = null;
                    $days_left  = 0;
                } else {
                    // -------- Totals (inclusive) --------
                    $days_total = $effectiveStart->diffInDays($effectiveEnd) + 1;

                    // -------- Days Left (inclusive) --------
                    if ($today->lt($effectiveStart)) {
                        // Not started yet → from effectiveStart to effectiveEnd
                        $days_left = $effectiveStart->diffInDays($effectiveEnd) + 1;
                    } elseif ($today->betweenIncluded($effectiveStart, $effectiveEnd)) {
                        // Running → from today to effectiveEnd
                        $days_left = $today->diffInDays($effectiveEnd) + 1;
                    } else {
                        // Finished
                        $days_left = 0;
                    }
                }

                // -------- ₹/Day = total_price / 30 (fallback to product per_day_price) --------
                $per_day = null;
                $total = $sub->order?->total_price;
                if (is_numeric($total) && (float)$total > 0) {
                    $per_day = round(((float)$total) / 30, 2);
                } elseif ($sub->flowerProducts && $sub->flowerProducts->per_day_price !== null) {
                    $per_day = (float) $sub->flowerProducts->per_day_price;
                }

                // -------- Address line (unchanged) --------
                $addr = $sub->order?->address;
                $address_line = $addr
                    ? trim(implode(', ', array_filter([
                        $addr->apartment_name,
                        $addr->apartment_flat_plot,
                        $addr->area,
                        $addr->city,
                        $addr->state,
                        $addr->pincode
                    ])))
                    : null;

                // -------- Attach computed --------
                $sub->computed = (object) [
                    'effective_start' => $effectiveStart,
                    'effective_end'   => $effectiveEnd,
                    'days_total'      => $days_total,
                    'days_left'       => $days_left,
                    'per_day'         => $per_day,
                    'address_line'    => $address_line,
                    'todays_delivery' => $sub->order?->deliveryHistories?->first(),
                ];

                if ($sub->flowerProducts) {
                    $sub->flowerProducts->product_image_url = $sub->flowerProducts->product_image;
                }

                return $sub;
            });

        $riders = \App\Models\RiderDetails::select('rider_id','rider_name')
            ->orderBy('rider_name','asc')
            ->get();

        return view('admin.today-delivery-data', compact('activeSubscriptions', 'today', 'riders'));
    }

    public function assignRider(Request $request, $order) // {order} from route
    {
        try {
            $validated = $request->validate([
                'rider_id' => 'required|exists:flower__rider_details,rider_id',
            ]);

            // business key "order_id"
            $orderModel = Order::where('order_id', $order)->first();

            if (!$orderModel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            $orderModel->rider_id = $validated['rider_id'];
            $orderModel->save();

            $orderModel->load('rider:rider_id,rider_name');

            return response()->json([
                'status'     => 'ok',
                'message'    => 'Rider assigned successfully.',
                'rider_name' => optional($orderModel->rider)->rider_name,
                'rider_id'   => $orderModel->rider_id,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation failed.',
                'errors'  => $ve->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('assignRider failed', [
                'order_id' => $order,
                'err'      => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong while assigning the rider.',
            ], 500);
        }
    }
    public function todayExpenditure(Request $request)
    {
        $tz   = config('app.timezone');
        $date = $request->input('date', Carbon::today($tz)->toDateString());

        // Optional filters
        $vendorId       = $request->input('vendor_id');
        $riderId        = $request->input('rider_id');
        $paymentMethod  = $request->input('payment_method');
        $paymentStatus  = $request->input('payment_status');

        $base = FlowerPickupDetails::with([
                'vendor:vendor_id,vendor_name,phone_no',
                'rider:rider_id,rider_name',
                // Eager-load item relations so item & unit names are available in the view
                'flowerPickupItems' => function ($q) {
                    $q->with([
                        'flower:product_id,name',      // item name from FlowerProduct
                        'unit:id,unit_name',           // unit name from PoojaUnit
                    ])->select([
                        'id','pick_up_id','flower_id','unit_id','quantity','price'
                    ]);
                },
            ])
            ->whereDate('pickup_date', $date);

        if ($vendorId)      $base->where('vendor_id', $vendorId);
        if ($riderId)       $base->where('rider_id', $riderId);
        if ($paymentMethod) $base->where('payment_method', $paymentMethod);
        if ($paymentStatus) $base->where('payment_status', $paymentStatus);

        $totalForDay = (clone $base)->sum('total_price');

        $byVendor = (clone $base)
            ->select('vendor_id', DB::raw('SUM(total_price) AS total'))
            ->groupBy('vendor_id')
            ->with('vendor:vendor_id,vendor_name')
            ->get();

        $pickups = $base->orderByDesc('pickup_date')
                        ->orderByDesc('pick_up_id')
                        ->paginate(25)
                        ->withQueryString();

        // Friendly counts for header chips
        $totalPickupsCount = (clone $base)->count();
        $totalItemsCount   = (clone $base)->withCount('flowerPickupItems')->get()->sum('flower_pickup_items_count');

        return view('admin.reports.today-expenditure', [
            'date'                => $date,
            'pickups'             => $pickups,
            'totalForDay'         => $totalForDay,
            'byVendor'            => $byVendor,
            'vendorId'            => $vendorId,
            'riderId'             => $riderId,
            'paymentMethod'       => $paymentMethod,
            'paymentStatus'       => $paymentStatus,
            'totalPickupsCount'   => $totalPickupsCount,
            'totalItemsCount'     => $totalItemsCount,
        ]);
    }
    public function paymentHistory(Request $request)
{
    // -------- Parse filters ----------
    $preset        = $request->string('preset')->toString(); // today|yesterday|tomorrow|this_week|this_month
    $userId        = $request->string('user_id')->toString();
    $statusFilter  = $request->string('status')->toString(); // pending|paid
    $methodFilter  = $request->string('payment_method')->toString(); // UPI|Cash|Card|...
    $search        = $request->string('q')->toString(); // search by order/payment id or user

    // Resolve [start, end] (inclusive) — defaults to TODAY if nothing provided
    [$start, $end, $effectivePreset] = $this->resolveRange($request, $preset);

    // -------- Base query (JOIN subscriptions + flower_products) ----------
    $q = FlowerPayment::query()
        ->leftJoin('users', 'users.userid', '=', 'flower_payments.user_id')
        ->leftJoin('subscriptions as s', 's.order_id', '=', 'flower_payments.order_id')
        ->leftJoin('flower_products as p', 'p.product_id', '=', 's.product_id')
        ->select([
            'flower_payments.*',
            'users.name as user_name',
            'users.mobile_number as user_mobile',

            // subscription + product fields to show in table
            's.subscription_id',
            's.start_date',
            's.end_date',
            's.status as subscription_status',
            'p.name as product_name',
            'p.category as product_category',
            'p.duration as product_duration', // optional plan length if you want to display it too
        ])
        ->when($start, fn($qq) => $qq->whereDate('flower_payments.created_at', '>=', $start->toDateString()))
        ->when($end,   fn($qq) => $qq->whereDate('flower_payments.created_at', '<=', $end->toDateString()))
        ->when($userId, fn($qq) => $qq->where('flower_payments.user_id', $userId))
        ->when($statusFilter, fn($qq) => $qq->where('flower_payments.payment_status', $statusFilter))
        ->when($methodFilter, fn($qq) => $qq->where('flower_payments.payment_method', $methodFilter))
        ->when($search, function ($qq) use ($search) {
            $needle = '%' . trim($search) . '%';
            $qq->where(function ($w) use ($needle) {
                $w->where('flower_payments.order_id', 'like', $needle)
                  ->orWhere('flower_payments.payment_id', 'like', $needle)
                  ->orWhere('users.name', 'like', $needle)
                  ->orWhere('users.mobile_number', 'like', $needle)
                  ->orWhere('p.name', 'like', $needle)
                  ->orWhere('p.category', 'like', $needle)
                  ->orWhere('s.subscription_id', 'like', $needle);
            });
        })
        ->orderByDesc('flower_payments.created_at');

    // -------- Pagination ----------
    $payments = $q->paginate(25)->withQueryString();

    // -------- Totals / Stats (GROUP BY safe) ----------
    $statsQ = (clone $q);
    // Remove ORDER BY and previous select to safely aggregate
    $statsQ->getQuery()->orders  = null;
    $statsQ->getQuery()->columns = null;

    $stats = $statsQ
        ->selectRaw('
            COUNT(*) as cnt,
            SUM(CASE WHEN flower_payments.payment_status = "paid" THEN flower_payments.paid_amount ELSE 0 END)    as sum_paid,
            SUM(CASE WHEN flower_payments.payment_status = "pending" THEN flower_payments.paid_amount ELSE 0 END) as sum_pending,
            SUM(flower_payments.paid_amount) as sum_all
        ')
        ->first();

    // -------- Lookups ----------
    $users = User::query()
        ->orderBy('name')
        ->get(['userid','name','mobile_number']);

    $methods = FlowerPayment::query()
        ->distinct()
        ->orderBy('payment_method')
        ->pluck('payment_method')
        ->filter()
        ->values();

    return view('admin.reports.payment-history', [
        'payments'  => $payments,
        'users'     => $users,
        'methods'   => $methods,

        // send the effective preset so "Today" lights up by default
        'preset'    => $effectivePreset,
        'userId'    => $userId,
        'status'    => $statusFilter,
        'method'    => $methodFilter,
        'search'    => $search,

        'start'     => $start?->toDateString(),
        'end'       => $end?->toDateString(),
        'stats'     => $stats,
    ]);
}

private function resolveRange(Request $request, ?string $preset): array
{
    $start = null;
    $end   = null;
    $effectivePreset = $preset;

    if ($request->filled('start_date') || $request->filled('end_date')) {
        $start = $request->filled('start_date') ? \Carbon\Carbon::parse($request->get('start_date'))->startOfDay() : null;
        $end   = $request->filled('end_date')   ? \Carbon\Carbon::parse($request->get('end_date'))->endOfDay()     : null;
        if (!$effectivePreset) {
            $effectivePreset = 'custom';
        }
    } else {
        switch ($preset) {
            case 'today':
                $start = \Carbon\Carbon::today()->startOfDay();
                $end   = \Carbon\Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $start = \Carbon\Carbon::yesterday()->startOfDay();
                $end   = \Carbon\Carbon::yesterday()->endOfDay();
                break;
            case 'tomorrow':
                $start = \Carbon\Carbon::tomorrow()->startOfDay();
                $end   = \Carbon\Carbon::tomorrow()->endOfDay();
                break;
            case 'this_week':
            case 'week':
                $start = \Carbon\Carbon::now()->startOfWeek();
                $end   = \Carbon\Carbon::now()->endOfWeek();
                break;
            case 'this_month':
            case 'month':
                $start = \Carbon\Carbon::now()->startOfMonth();
                $end   = \Carbon\Carbon::now()->endOfMonth();
                break;
            default:
                // DEFAULT → TODAY
                $start = \Carbon\Carbon::today()->startOfDay();
                $end   = \Carbon\Carbon::today()->endOfDay();
                $effectivePreset = 'today';
                break;
        }
    }

    if ($start && $end && $end->lt($start)) {
        [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
    }

    return [$start, $end, $effectivePreset];
}


}
