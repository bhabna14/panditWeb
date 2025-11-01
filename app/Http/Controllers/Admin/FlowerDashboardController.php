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
            $q->from('subscriptions')
            ->select('order_id')
            ->groupBy('order_id')
            ->where('status',  'pending')
            ->havingRaw('COUNT(*) > 1');
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

    public function flowerDashboard()
    {
        $tz = config('app.timezone');

        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $tomorrowDate = Carbon::tomorrow($tz)->toDateString();

        $tmr          = Carbon::tomorrow($tz)->startOfDay();
        $excludeStats = ['expired', 'dead'];

        // ✅ Count of subscriptions that will be ACTIVE tomorrow
        $today = Carbon::today($tz);

        // same "pending, starts today, unpaid" hide rule as index()
        $shouldHide = function ($sub) use ($today) {
            if (strtolower($sub->status ?? '') !== 'pending') return false;

            $startsToday = $sub->start_date ? Carbon::parse($sub->start_date)->isSameDay($today) : false;
            if (!$startsToday) return false;

            // paid?
            $hasPaid = !empty($sub->latestPaidPayment);
            if (!$hasPaid && $sub->relationLoaded('flowerPayments')) {
                $hasPaid = $sub->flowerPayments->contains(function ($p) {
                    $ps = strtolower((string)($p->payment_status ?? ''));
                    $s  = strtolower((string)($p->status ?? ''));
                    return $ps === 'paid' || $s === 'paid';
                });
            }
            return !$hasPaid;
        };

        // === FIXED COUNT: build the same set then count ===
        $activeTomorrowCount = Subscription::with([
                'latestPaidPayment',
                'flowerPayments',
            ])
            ->whereNotIn('status', $excludeStats)
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'paused', 'pending'])
                ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $tmr->toDateString())
            ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $tmr->toDateString())
            ->get()
            ->filter(function ($s) use ($tmr) {
                // exclude if paused on that day (same as index())
                if ($s->pause_start_date && $s->pause_end_date) {
                    $ps = \Carbon\Carbon::parse($s->pause_start_date)->startOfDay();
                    $pe = \Carbon\Carbon::parse($s->pause_end_date)->endOfDay();
                    if ($ps->lte($tmr) && $pe->gte($tmr)) return false;
                }
                return true;
            })
            ->reject($shouldHide)
            ->count();

        // (Optional) keep this if you also want the “starts tomorrow” metric elsewhere:
        $startingTomorrow = Subscription::query()
        ->whereIn('status', ['active', 'paused', 'pending'])
        ->whereDate('start_date', $tmr->toDateString())
        ->count();

        // --- the rest of your existing metrics (unchanged) ---
        $totalDeliveriesTodayCount = DeliveryHistory::whereDate('created_at', Carbon::today($tz)->toDateString())
            ->where('delivery_status', 'delivered')->count();

        $todayDeliveredRows = DeliveryHistory::with(['order'])
            ->whereDate('created_at', Carbon::today($tz)->toDateString())
            ->where('delivery_status', 'delivered')
            ->get();

        $totalIncomeToday = 0;

        $totalIncomeToday = FlowerPayment::query()
        ->whereDate('created_at', Carbon::today())
        ->where('payment_status', 'paid')
        ->sum('paid_amount');

        $todayTotalExpenditure = FlowerPickupDetails::whereDate('pickup_date', Carbon::today($tz))->sum('total_price');

        $riders = RiderDetails::where('status', 'active')->get();
        $assignedRiderIds = Order::query()
        ->whereNotNull('rider_id')
        ->whereHas('subscription', function ($q) {
            // keep your current intent: count only orders tied to an ACTIVE subscription
            $q->where('status', 'active');
        })
        ->distinct()
        ->pluck('rider_id');

        $riders = RiderDetails::where('status', 'active')
            ->whereIn('rider_id', $assignedRiderIds)
            ->get();

        $ridersData = $riders->map(function ($rider) use ($tz) {
            // total assigned orders (only those tied to ACTIVE subscription)
            $totalAssignedOrders = Order::where('rider_id', $rider->rider_id)
                ->whereHas('subscription', fn ($q) => $q->where('status', 'active'))
                ->count();

            // delivered today by this rider
            $totalDeliveredToday = DeliveryHistory::whereDate('created_at', Carbon::today($tz))
                ->where('rider_id', $rider->rider_id)
                ->where('delivery_status', 'delivered')
                ->count();

            return [
                'rider' => $rider,
                'totalAssignedOrders' => $totalAssignedOrders,
                'totalDeliveredToday' => $totalDeliveredToday,
            ];
        })->values();

        $totalRiders = RiderDetails::where('status', 'active')->count();
        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', Carbon::today($tz)->toDateString())
            ->where('delivery_status', 'delivered')->count();
        $totalDeliveriesThisMonth = DeliveryHistory::whereYear('created_at', now($tz)->year)
            ->whereMonth('created_at', now($tz)->month)
            ->where('delivery_status', 'delivered')->count();
        $totalDeliveries = DeliveryHistory::where('delivery_status', 'delivered')->count();

        $todayStrs = Carbon::today($tz)->toDateString();


        $newUserSubscription = DB::table('subscriptions as s')
        ->join(DB::raw('(SELECT user_id, MIN(created_at) AS first_created_at
                        FROM subscriptions
                        GROUP BY user_id) firsts'),
            function ($join) {
                $join->on('s.user_id', '=', 'firsts.user_id')
                    ->on('s.created_at', '=', 'firsts.first_created_at');
            })
        ->whereDate('s.created_at', $todayStrs)
        ->distinct()
        ->count('s.user_id');


        $renewSubscription = DB::table('subscriptions as s')
        ->whereDate('s.created_at', $todayStrs)
        ->whereExists(function ($q) use ($todayStrs) {
            $q->select(DB::raw(1))
            ->from('subscriptions as prev')
            ->whereColumn('prev.user_id', 's.user_id')
            ->whereDate('prev.created_at', '<', $todayStrs);
        })
        ->count();

        $todayEndSubscription = Subscription::where(function ($query) use ($tz) {
                $query->where(function ($subQuery) use ($tz) {
                        $subQuery->whereNotNull('new_date')
                                ->whereDate('new_date', Carbon::today($tz));
                    })
                    ->orWhere(function ($subQuery) use ($tz) {
                        $subQuery->whereNull('new_date')
                                ->whereDate('end_date', Carbon::today($tz));
                    });
            })
            ->where('status', 'active')->count();

        $winStart = Carbon::today($tz)->startOfDay();
        $winEnd   = (clone $winStart)->addDays(4)->endOfDay();

        $subscriptionEndFiveDays = Subscription::where('status', 'active')
            ->whereRaw('COALESCE(new_date, end_date) BETWEEN ? AND ?', [$winStart, $winEnd])
            ->count();

        $monthStart = Carbon::now($tz)->startOfMonth();
        $monthEnd   = Carbon::now($tz)->endOfMonth();

        $latestPerUserIds = \DB::table('subscriptions as s1')
            ->selectRaw('MAX(s1.id) as id')
            ->groupBy('s1.user_id');

        $expiredSubscriptions = Subscription::query()
            ->whereIn('id', $latestPerUserIds)
            ->where('status', 'expired')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$monthStart, $monthEnd])
            ->count();

        $nonAssignedRidersCount = Subscription::where('status', 'active')
            ->whereHas('order', fn($q) => $q->whereNull('rider_id')->orWhere('rider_id', ''))
            ->count();

        $ordersRequestedToday = FlowerRequest::whereDate('date', Carbon::today($tz))->count();

        $pausedSubscriptions = Subscription::where('status', 'paused')->count();

        $nextDayPaused = Subscription::where('status', 'active')
            ->whereDate('pause_start_date', $tomorrowDate)->count();

        $nextDayResumed = Subscription::where('status', 'active')
            ->whereDate('pause_end_date', $tomorrowDate)->count();

        $todayPausedRequest = SubscriptionPauseResumeLog::whereDate('created_at', Carbon::today($tz))
            ->where('action', 'paused')->count();

        $today = Carbon::today($tz);
        $threeDaysLater = Carbon::today($tz)->addDays(3);
        $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$today, $threeDaysLater])->count();

        $visitPlaceCountToday = MarketingVisitPlace::whereDate('created_at', Carbon::today($tz))->count();

        $todayStr = Carbon::today($tz)->toDateString();
        $todayClaimed = ReferOfferClaim::where('status', 'claimed')->whereDate('date_time', $todayStr)->count();
        $todayApproved = ReferOfferClaim::where('status', 'approved')->whereDate('updated_at', $todayStr)->count();
        $todayRefer = FLowerReferal::whereDate('created_at', $todayStr)->count();
        $totalRefer = FLowerReferal::count();

        return view('admin/flower-dashboard', compact(
            'activeSubscriptions',
            'startingTomorrow',
            'activeTomorrowCount',   // <<— pass to view
            'totalDeliveriesTodayCount',
            'totalIncomeToday',
            'todayTotalExpenditure',
            'ridersData',
            'totalRiders',
            'totalDeliveriesToday',
            'totalDeliveriesThisMonth',
            'totalDeliveries',
            'newUserSubscription',
            'renewSubscription',
            'ordersRequestedToday',
            'todayEndSubscription',
            'subscriptionEndFiveDays',
            'expiredSubscriptions',
            'nonAssignedRidersCount',
            'todayPausedRequest',
            'pausedSubscriptions',
            'nextDayPaused',
            'nextDayResumed',
            'upcomingCustomizeOrders',
            'visitPlaceCountToday',
            'todayClaimed',
            'todayApproved',
            'todayRefer',
            'totalRefer'
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
