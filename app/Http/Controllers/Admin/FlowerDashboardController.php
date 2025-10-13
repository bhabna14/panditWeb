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

   public function flowerDashboard()
{
    $tz = config('app.timezone');

    $activeSubscriptions = Subscription::where('status', 'active')->count();

    $tomorrowDate = Carbon::tomorrow($tz)->toDateString();
    $tmr = Carbon::tomorrow($tz)->startOfDay();

    // Optional eager loads you already had (kept, but not needed for counts)
    $with = [
        'users',
        'users.addressDetails',
        'order',
        'flowerProducts:product_id,name',
    ];

    // ✅ Subscriptions that START tomorrow (keep this if you show it elsewhere)
    $startingTomorrow = Subscription::whereDate('start_date', '=', $tmr->toDateString())->count();

    // ✅ Subscriptions ACTIVE on tomorrow (this is what you want to display)
    $activeTomorrowCount = Subscription::where(function ($q) {
            // treat these as “live-ish” states
            $q->whereIn('status', ['active', 'paused', 'pending'])
              ->orWhere('is_active', 1);
        })
        ->whereDate('start_date', '<=', $tmr->toDateString())
        ->whereDate(DB::raw('COALESCE(new_date, end_date)'), '>=', $tmr->toDateString())
        // Exclude if tomorrow is inside a pause range
        ->where(function ($q) use ($tmr) {
            $d = $tmr->toDateString();
            $q->whereNull('pause_start_date')
              ->orWhereNull('pause_end_date')
              ->orWhereDate('pause_end_date', '<', $d)
              ->orWhereDate('pause_start_date', '>', $d);
        })
        ->count();

    // --- the rest of your existing metrics (unchanged) ---
    $totalDeliveriesTodayCount = DeliveryHistory::whereDate('created_at', Carbon::today($tz)->toDateString())
        ->where('delivery_status', 'delivered')->count();

    $todayDeliveredRows = DeliveryHistory::with(['order'])
        ->whereDate('created_at', Carbon::today($tz)->toDateString())
        ->where('delivery_status', 'delivered')
        ->get();

    $totalIncomeToday = 0;
    foreach ($todayDeliveredRows as $delivery) {
        $order = $delivery->order;
        if (!$order) continue;

        $subscription = Subscription::where('order_id', $order->order_id)->first();
        if (!$subscription || !$subscription->start_date || !$subscription->end_date) continue;

        $start = Carbon::parse($subscription->start_date);
        $end   = Carbon::parse($subscription->end_date);
        $days  = $start->diffInDays($end) + 1;

        if ($days > 0 && $order->total_price > 0) {
            $totalIncomeToday += $order->total_price / $days;
        }
    }

    $todayTotalExpenditure = FlowerPickupDetails::whereDate('pickup_date', Carbon::today($tz))->sum('total_price');

    $riders = RiderDetails::where('status', 'active')->get();
    $ridersData = $riders->map(function ($rider) use ($tz) {
        $totalAssignedOrders = Order::where('rider_id', $rider->rider_id)
            ->whereHas('subscription', fn($q) => $q->where('status', 'active'))
            ->count();

        $totalDeliveredToday = DeliveryHistory::whereDate('created_at', Carbon::today($tz))
            ->where('rider_id', $rider->rider_id)
            ->where('delivery_status', 'delivered')
            ->count();

        return [
            'rider' => $rider,
            'totalAssignedOrders' => $totalAssignedOrders,
            'totalDeliveredToday' => $totalDeliveredToday,
        ];
    });

    $totalRiders = RiderDetails::where('status', 'active')->count();
    $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', Carbon::today($tz)->toDateString())
        ->where('delivery_status', 'delivered')->count();
    $totalDeliveriesThisMonth = DeliveryHistory::whereYear('created_at', now($tz)->year)
        ->whereMonth('created_at', now($tz)->month)
        ->where('delivery_status', 'delivered')->count();
    $totalDeliveries = DeliveryHistory::where('delivery_status', 'delivered')->count();

    $newUserSubscription = Subscription::where('status', 'pending')
        ->whereDate('created_at', Carbon::today($tz))
        ->groupBy('user_id')
        ->selectRaw('MIN(order_id) as order_id, user_id')
        ->get()
        ->filter(fn ($subscription) => Subscription::where('user_id', $subscription->user_id)->count() === 1)
        ->count();

    $renewSubscription = Subscription::whereDate('created_at', Carbon::today($tz))
        ->whereIn('order_id', function ($query) {
            $query->select('order_id')->from('subscriptions')
                  ->groupBy('order_id')->havingRaw('COUNT(order_id) > 1');
        })->count();

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
}
