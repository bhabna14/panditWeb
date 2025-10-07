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
public function flowerDashboard()
{
    $activeSubscriptions = Subscription::where('status', 'active')->count();

    $tomorrow = Carbon::tomorrow()->toDateString();
    $tomorrowActiveOrder = Subscription::where('status', 'pending')
        ->whereDate('start_date', $tomorrow)
        ->count();

     $totalDeliveriesTodayCount = DeliveryHistory::whereDate('created_at', now()->toDateString())->where('delivery_status', 'delivered')
        ->count();


    $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())
        ->where('delivery_status', 'delivered')
        ->get();

    $totalIncomeToday = 0;

    foreach ($totalDeliveriesToday as $delivery) {
        $order = $delivery->order;
        if (!$order) continue;

        $subscription = Subscription::where('order_id', $order->order_id)->first();
        if (!$subscription || !$subscription->start_date || !$subscription->end_date) continue;

        $start = Carbon::parse($subscription->start_date);
        $end = Carbon::parse($subscription->end_date);
        $days = $start->diffInDays($end) + 1;

        if ($days > 0 && $order->total_price > 0) {
            $totalIncomeToday += $order->total_price / $days;
        }
    }

      $todayTotalExpenditure = FlowerPickupDetails::whereDate('pickup_date', Carbon::today())
        ->sum('total_price');

          $riders = RiderDetails::where('status','active')->get();

          $ridersData = $riders->map(function ($rider) {
            // Total assigned orders to this rider
            $totalAssignedOrders = Order::where('rider_id', $rider->rider_id) // Filter by rider_id
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active'); // Check subscription status
            })
            ->count();

            // Total delivered orders today by this rider
            $totalDeliveredToday = DeliveryHistory::whereDate('created_at', Carbon::today())
                ->where('rider_id', $rider->rider_id)
                ->where('delivery_status', 'delivered')
                ->count();

            return [
                'rider' => $rider,
                'totalAssignedOrders' => $totalAssignedOrders,
                'totalDeliveredToday' => $totalDeliveredToday,
            ];
        });

        $totalRiders = RiderDetails::where('status','active')->count();

        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())->where('delivery_status', 'delivered')
            ->count();

        $totalDeliveriesThisMonth = DeliveryHistory::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('delivery_status', 'delivered')
            ->count();

        $totalDeliveries = DeliveryHistory::where('delivery_status', 'delivered')->count();

        $newUserSubscription = Subscription::where('status', 'pending')
            ->whereDate('created_at',  Carbon::today())
            ->groupBy('user_id')
            ->selectRaw('MIN(order_id) as order_id, user_id')
            ->get()
            ->filter(function ($subscription) {
                return Subscription::where('user_id', $subscription->user_id)->count() === 1;
            })
            ->count();

        $renewSubscription = Subscription::whereDate('created_at', Carbon::today()) // Check rows created today
            ->whereIn('order_id', function ($query) {
            $query->select('order_id')
            ->from('subscriptions')
            ->groupBy('order_id')
            ->havingRaw('COUNT(order_id) > 1'); // Find duplicate order IDs
        })
        ->count();

         $todayEndSubscription = Subscription::where(function ($query) {
            $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('new_date') // Check if new_date is available
                            ->whereDate('new_date', Carbon::today()); // Count using new_date if available
                })
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNull('new_date') // Check if new_date is not available
                            ->whereDate('end_date', Carbon::today()); // Count using end_date
                });
            })
            ->where('status', 'active') // Status must be active
            ->count();

            $tz         = config('app.timezone');
            $winStart   = Carbon::today($tz)->startOfDay();
            $winEnd     = (clone $winStart)->addDays(4)->endOfDay();

            // END date logic: use new_date if present else end_date
            $subscriptionEndFiveDays = Subscription::where('status', 'active')
                ->whereRaw('COALESCE(new_date, end_date) BETWEEN ? AND ?', [$winStart, $winEnd])
                ->count();


            $tz         = config('app.timezone');
            $monthStart = \Carbon\Carbon::now($tz)->startOfMonth();
            $monthEnd   = \Carbon\Carbon::now($tz)->endOfMonth();

            $latestPerUserIds = \DB::table('subscriptions as s1')
                ->selectRaw('MAX(s1.id) as id')
                ->groupBy('s1.user_id');

            $expiredSubscriptions = \App\Models\Subscription::query()
                ->whereIn('id', $latestPerUserIds)   // one latest row per user
                ->where('status', 'expired')         // latest is expired
                ->whereNotNull('end_date')           // safety
                ->whereBetween('end_date', [$monthStart, $monthEnd]) // ended this month
                ->count();

            $nonAssignedRidersCount = Subscription::where('status', 'active')
            ->whereHas('order', function ($q) {
                $q->whereNull('rider_id')->orWhere('rider_id', '');
            })
            ->count();

            $ordersRequestedToday = FlowerRequest::whereDate('created_at', Carbon::today())->count();

            $pausedSubscriptions = Subscription::where('status', 'paused')->count();

            $tomorrow = Carbon::tomorrow()->toDateString();

            $nextDayPaused = Subscription::where('status', 'active')
            ->whereDate('pause_start_date', $tomorrow)
            ->count();

            $nextDayResumed = Subscription::where('status', 'active')
            ->whereDate('pause_end_date', $tomorrow)
            ->count();

            $todayPausedRequest = SubscriptionPauseResumeLog::whereDate('created_at', Carbon::today())
            ->where('action', 'paused') // Optional: only count pause actions
            ->count();

            $today = Carbon::today();
            $threeDaysLater = Carbon::today()->addDays(3);

            $upcomingCustomizeOrders = FlowerRequest::whereBetween('date', [$today, $threeDaysLater])
            ->count();

            $visitPlaceCountToday = MarketingVisitPlace::whereDate('created_at', Carbon::today())->count();

            $tz       = config('app.timezone');
            $todayStr = Carbon::today($tz)->toDateString();

            // Today Claimed = claims made today (by their claim timestamp if present, else created_at)
            $todayClaimed = ReferOfferClaim::where('status', 'claimed')
                ->whereDate('date_time', $todayStr)
                ->count();

            // Today Approved = claims approved today (status=approved and updated today)
            $todayApproved = ReferOfferClaim::where('status', 'approved')
                ->whereDate('updated_at', $todayStr)
                ->count();

            // Today Refer = referral rows created today
            $todayRefer = FLowerReferal::whereDate('created_at', $todayStr)->count();

            // Total Refer = all referral rows
            $totalRefer = FLowerReferal::count();


            return view('admin/flower-dashboard', compact(
                'activeSubscriptions',
                'tomorrowActiveOrder',
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
        $today = Carbon::today();

        $activeSubscriptions = Subscription::with([
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
                $start  = $sub->start_date ? Carbon::parse($sub->start_date) : null;
                $end    = $sub->end_date ? Carbon::parse($sub->end_date) : null;

                $days_total = ($start && $end) ? $start->diffInDays($end) + 1 : null;
                $days_left  = $end ? max(0, $today->diffInDays($end, false)) : null;

                $per_day = null;
                if ($sub->order && $sub->order->total_price && $days_total && $days_total > 0) {
                    $per_day = round($sub->order->total_price / $days_total, 2);
                } elseif ($sub->flowerProducts && $sub->flowerProducts->per_day_price) {
                    $per_day = (float) $sub->flowerProducts->per_day_price;
                }

                $sub->computed = (object) [
                    'days_total' => $days_total,
                    'days_left'  => $days_left,
                    'per_day'    => $per_day,
                ];

                $addr = $sub->order?->address;
                $sub->computed->address_line = $addr
                    ? trim(implode(', ', array_filter([
                        $addr->apartment_name,
                        $addr->apartment_flat_plot,
                        $addr->area,
                        $addr->city,
                        $addr->state,
                        $addr->pincode
                    ])))
                    : null;

                if ($sub->flowerProducts) {
                    $sub->flowerProducts->product_image_url = $sub->flowerProducts->product_image;
                }

                $sub->computed->todays_delivery = $sub->order?->deliveryHistories?->first();
                return $sub;
            });

        $riders = RiderDetails::select('rider_id','rider_name')
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
}
