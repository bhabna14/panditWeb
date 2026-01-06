<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Notification;
use App\Models\RiderDetails;
use App\Models\DeliveryHistory;
use App\Models\FlowerPickupDetails;
use App\Models\ReferOfferClaim;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SubscriptionPauseResumeLog;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables; // ✅ Correct import
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\UserDevice; // ✅ add this
use App\Models\DeliveryCustomizeHistory;

class FlowerOrderController extends Controller
{
        
    public function showOrders(Request $request)
    {
        // TZ-safe "today"
        $tz         = config('app.timezone');
        $todayStart = Carbon::today($tz)->startOfDay();
        $todayEnd   = (clone $todayStart)->endOfDay();

        // Base query + eager loads
        $query = Subscription::with([
            'order.address.localityDetails',
            'flowerPayments',
            'users',
            'flowerProducts',
            'pauseResumeLog',
            'order.rider',
        ])->orderByDesc('id');

        $filter = $request->query('filter');

        // 🔹 Filter-wise header text
        $filterHeader = 'All Subscriptions';

        switch ($filter) {
            case 'today':
                $filterHeader = 'Today Subscriptions';
                break;
            case 'end':
                $filterHeader = 'Subscriptions Ending Today';
                break;
            case 'fivedays':
                $filterHeader = 'Subscriptions Ending in Next 5 Days';
                break;
            case 'tomorrowOrder':
                $filterHeader = 'Subscriptions Starting Tomorrow';
                break;
            case 'tomorrow':
                $filterHeader = 'Next Day Pause Subscriptions';
                break;
            case 'todayrequest':
                $filterHeader = 'Pause Requests Created Today';
                break;
            case 'new':
                $filterHeader = 'New Customers - First Subscription Today';
                break;
            case 'renewed':
                $filterHeader = 'Renewed Subscriptions Created Today';
                break;
            case 'active':
                $filterHeader = 'Active Subscriptions';
                break;
            case 'expired':
                $filterHeader = 'Expired Subscriptions (This Month)';
                break;
            case 'discontinued':
                $filterHeader = 'Discontinued Customers';
                break;
            case 'paused':
                $filterHeader = 'Paused Subscriptions';
                break;
            case 'rider':
                $filterHeader = 'Active Subscriptions Without Rider';
                break;
            case 'tommorow':
                $filterHeader = 'Subscriptions Pausing Tomorrow';
                break;
            case 'nextdayresumed':
                $filterHeader = 'Subscriptions Resuming Tomorrow';
                break;
            case 'pending':
                $filterHeader = 'Pending Subscriptions';
                break;
        }

        if ($filter === 'rider') {
            $query->where('status', 'active')
                ->whereHas('order', function ($q) {
                    $q->whereNull('rider_id')->orWhere('rider_id', '');
                });
        }

        if ($filter === 'end') {
        // assuming $todayStart is Carbon for today 00:00:00
        $todayDate = $todayStart->toDateString();

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
        // skip users who have another future sub with paid payment
        ->whereNotExists(function ($sq) use ($todayDate) {
            $sq->select(DB::raw(1))
                ->from('subscriptions as s2')
                ->whereColumn('s2.user_id', 'subscriptions.user_id')   // same user
                ->whereColumn('s2.id', '!=', 'subscriptions.id')       // different subscription
                // future window
                ->whereDate(DB::raw('COALESCE(s2.new_date, s2.end_date)'), '>', $todayDate)
                // and that future sub has a PAID payment
                ->whereExists(function ($qp) {
                    $qp->select(DB::raw(1))
                       ->from('flower_payments as fp')
                       ->whereColumn('fp.order_id', 's2.order_id')
                       ->where('fp.payment_status', 'paid');
                });
        })
        ->withoutOtherActiveOrPending();
        }

        if ($filter === 'fivedays') {
            $tz    = config('app.timezone');
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

        if ($filter === 'tomorrow') {
            $tomorrow = Carbon::tomorrow($tz)->toDateString();
            $query->where('status', 'active')
                ->whereDate('pause_start_date', $tomorrow);
        }

        if ($filter === 'todayrequest') {
            $query->whereIn('subscription_id', function ($sub) use ($todayStart, $todayEnd) {
                $sub->select('subscription_id')
                    ->from('subscription_pause_resume_logs')
                    ->where('action', 'paused')
                    ->whereBetween('created_at', [$todayStart, $todayEnd]);
            })->distinct('subscription_id');
        }

        // 🔹 all subscriptions placed today
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
                // ensure end_date + 30 days does NOT cross past this month's end
                ->whereRaw('DATE_ADD(end_date, INTERVAL 30 DAY) <= ?', [$monthEnd->toDateString()])
                // only include subscriptions whose user is NOT active
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

        // 🔹 NEW: filter pending
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        }

        $tomorrowStart = (clone $todayStart)->addDay();
        $tomorrowEnd   = (clone $tomorrowStart)->endOfDay();

        if ($filter === 'tommorow') {
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
            $query->whereHas('users', fn($q) => $q->where('name', $name));
        }

        if ($request->filled('mobile_number')) {
            $mobile = $request->mobile_number;
            $query->whereHas('users', fn($q) => $q->where('mobile_number', $mobile));
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

        // ---- Card counts ----
        $activeSubscriptions   = Subscription::where('status', 'active')->count();
        $pausedSubscriptions   = Subscription::where('status', 'paused')->count();
        $ordersRequestedToday  = Subscription::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $pendingSubscriptions  = Subscription::where('status', 'pending')->count(); // 🔹 NEW

        // New subscriptions (first subscription per user created today)
        $firstRowsSubCard = DB::table('subscriptions as s1')
            ->join(
                DB::raw('(SELECT user_id, MIN(created_at) AS first_created_at
                        FROM subscriptions
                        GROUP BY user_id) f'),
                function ($join) {
                    $join->on('s1.user_id', '=', 'f.user_id')
                        ->on('s1.created_at', '=', 'f.first_created_at');
                }
            )
            ->whereBetween('s1.created_at', [$todayStart, $todayEnd]);

        $newSubscriptions = $firstRowsSubCard->count();

        // Expired subscriptions this month (same logic as filter=expired)
        $monthStart = Carbon::now($tz)->startOfMonth();
        $monthEnd   = Carbon::now($tz)->endOfMonth();

        $latestPerUserIdsForExpired = DB::table('subscriptions as s1')
            ->selectRaw('MAX(s1.id) as id')
            ->groupBy('s1.user_id');

        $expiredSubscriptions = Subscription::whereIn('id', $latestPerUserIdsForExpired)
            ->where('status', 'expired')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$monthStart, $monthEnd])
            ->whereRaw('DATE_ADD(end_date, INTERVAL 30 DAY) <= ?', [$monthEnd->toDateString()])
            ->whereHas('user', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('status', '!=', 'active')
                        ->orWhere('is_active', false);
                });
            })
            ->count();

        $riders = RiderDetails::where('status', 'active')->get();

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
            'pendingSubscriptions',
            'newSubscriptions',
            'expiredSubscriptions',
            'users',
            'apartmentNames',
            'apartmentNumbers',
            'filterHeader'
        ));
    }

    public function updateDates(Request $request, $id)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
            ]);

            $subscription = Subscription::findOrFail($id);
            $subscription->start_date = Carbon::parse($request->start_date)->toDateString();

            $submittedEndDate = Carbon::parse($request->end_date)->toDateString();
            $originalEndDate = Carbon::parse($subscription->end_date)->toDateString();

            if ($submittedEndDate !== $originalEndDate) {
                $subscription->new_date = $submittedEndDate;
            }

            $subscription->save();

            return response()->json(['status' => 'success', 'message' => 'Subscription dates updated successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->validator->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,paused,pending,expired'
            ]);

            $subscription = Subscription::findOrFail($id);
            $subscription->status = $request->status;
            $subscription->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription status updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePauseDates(Request $request, $id)
    {
        try {
            // Validate input
            $request->validate([
                'pause_start_date' => 'required|date',
                'pause_end_date'   => 'required|date|after_or_equal:pause_start_date',
                'resume_date'      => 'nullable|date',
            ]);

            $subscription = Subscription::findOrFail($id);

            $pauseStart = Carbon::parse($request->pause_start_date);
            $pauseEnd   = Carbon::parse($request->pause_end_date);
            $resumeDate = $request->resume_date ? Carbon::parse($request->resume_date) : null;
            $currentEnd = $subscription->new_date
                ? Carbon::parse($subscription->new_date)
                : Carbon::parse($subscription->end_date);

            $newEndDate = null;
            $pausedDays = 0;

            // Handle resume date logic
            if ($resumeDate) {
                if ($resumeDate->lt($pauseStart) || $resumeDate->gt($pauseEnd)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Resume date must be within the pause period.'
                    ], 422);
                }

                $pausedDays = $resumeDate->diffInDays($pauseStart) + 1;
                $newEndDate = $currentEnd->copy()->addDays($pausedDays);
                $subscription->new_date = $newEndDate->toDateString();
            }

            // Update subscription pause dates
            $subscription->pause_start_date = $pauseStart->toDateString();
            $subscription->pause_end_date = $pauseEnd->toDateString();
            $subscription->save();

            // Update or create pause log
            $existingLog = SubscriptionPauseResumeLog::where('subscription_id', $subscription->id)
                ->where('order_id', $subscription->order_id)
                ->where('action', 'pause-update')
                ->latest()
                ->first();

            $logData = [
                'pause_start_date' => $pauseStart->toDateString(),
                'pause_end_date'   => $pauseEnd->toDateString(),
                'resume_date'      => $resumeDate?->toDateString(),
                'new_end_date'     => $newEndDate?->toDateString(),
                'paused_days'      => $pausedDays,
            ];

            if ($existingLog) {
                $existingLog->update($logData);
            } else {
                SubscriptionPauseResumeLog::create(array_merge([
                    'subscription_id' => $subscription->id,
                    'order_id'        => $subscription->order_id,
                    'action'          => 'pause-update',
                ], $logData));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pause dates updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update pause dates.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsViewed()
    {
        Order::where('is_viewed', false)->update(['is_viewed' => true]);
        return response()->json(['message' => 'Orders marked as viewed.'], 200);
    }

    public function showNotifications()
    {
        $notifications = Notification::where('is_read', false)->latest()->get();
        return view('admin.layouts.components.app-header', compact('notifications'));
    }
        
    public function showCustomerDetails($userid)
    {
        // User by business key `userid`
        $user = User::where('userid', $userid)->firstOrFail();

        // Active addresses with locality relation
        $addressdata = UserAddress::where('user_id', $userid)
            ->where('status', 'active')
            ->with('localityDetails')
            ->get();

        // Subscriptions + related data
        $orders = Subscription::where('user_id', $userid)
            ->with([
                'flowerProducts',
                'order.address',
                'flowerPayments',
            ])
            ->orderBy('id', 'desc')
            ->get();

        // Flower requests + items + user + address (attach created order)
        $pendingRequests = FlowerRequest::where('user_id', $userid)
            ->with([
                'flowerProduct',
                'user',
                'address',
                'flowerRequestItems',
            ])
            ->orderBy('id', 'desc')
            ->get();

        foreach ($pendingRequests as $request) {
            $request->setRelation(
                'order',
                Order::where('request_id', $request->request_id)->with('flowerPayments')->first()
            );
        }

        // Metrics
        $totalOrders = Subscription::where('user_id', $userid)
            ->whereNotIn('status', ['dead', 'cancelled'])
            ->count();
        $ongoingOrders = Subscription::where('user_id', $userid)->where('status', 'active')->count();
        $totalSpend    = FlowerPayment::where('user_id', $userid)->sum('paid_amount');

        // Total Refer (distinct users who claimed this user's offer)
        $totalRefer = ReferOfferClaim::whereHas('offer', function ($q) use ($userid) {
                $q->where('user_id', $userid);
            })
            ->distinct('user_id')
            ->count('user_id');

        // ✅ Last Login Time (authorized devices only), using business key `userid`
        $lastLoginRaw = UserDevice::authorized()
            ->where('user_id', $userid)
            ->max('last_login_time'); // could be null

        // Normalize to app timezone string (for easy formatting in Blade)
        $tz = config('app.timezone', 'Asia/Kolkata');
        $lastLogin = $lastLoginRaw
            ? Carbon::parse($lastLoginRaw)->timezone($tz)
            : null;

        return view(
            'admin.flower-order.show-customer-details',
            compact(
                'user',
                'addressdata',
                'pendingRequests',
                'orders',
                'totalOrders',
                'ongoingOrders',
                'totalSpend',
                'totalRefer',
                'lastLogin' // ✅ pass to view
            )
        );
    }

    public function showorderdetails($id)
    {
        $order = Subscription::with([
            'order',
            'flowerPayments',
            'users',
            'flowerProducts',
            'pauseResumeLogs',
        ])->findOrFail($id);

        // Compute subscription window: start_date → (new_date if present else end_date)
        $periodStart = $order->start_date ? Carbon::parse($order->start_date)->startOfDay() : null;
        $periodEndRaw = $order->new_date ?: $order->end_date;
        $periodEnd = $periodEndRaw ? Carbon::parse($periodEndRaw)->endOfDay() : null;

        // Filter toggle: ?range=period (default) or ?range=all
        $range = request()->string('range')->lower()->value();
        if (!in_array($range, ['period', 'all'], true)) {
            $range = 'period';
        }

        $deliveriesQuery = DeliveryHistory::with('rider')
            ->where('order_id', $order->order_id)
            ->orderByDesc('created_at');

        if ($range === 'period') {
            if ($periodStart && $periodEnd) {
                $deliveriesQuery->whereBetween('created_at', [$periodStart, $periodEnd]);
            } elseif ($periodStart) {
                $deliveriesQuery->where('created_at', '>=', $periodStart);
            } elseif ($periodEnd) {
                $deliveriesQuery->where('created_at', '<=', $periodEnd);
            }
        }

        $deliveries = $deliveriesQuery->get();

        // Aggregates for UI
        $totalDeliveries = $deliveries->count();
        $lastStatus = optional($deliveries->first())->delivery_status;

        // Group deliveries by date (Y-m-d) for date headers in the timeline
        $groupedDeliveries = $deliveries->groupBy(function ($d) {
            return Carbon::parse($d->created_at)->format('Y-m-d');
        });

        // Status counts
        $statusCounts = $deliveries->groupBy('delivery_status')->map->count();

        return view('admin.flower-order.show-order-details', compact(
            'order',
            'periodStart',
            'periodEnd',
            'deliveries',
            'groupedDeliveries',
            'totalDeliveries',
            'lastStatus',
            'statusCounts',
            'range'
        ));
    }

    public function showActiveSubscriptions()
    {
        $activeSubscriptions = Order::whereNull('request_id')
        ->whereHas('subscription', function ($query) {
            $query->where('status', 'active');
        })
        ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin.flower-order.manage-active-subscriptions', compact('activeSubscriptions'));
    }

    public function showPausedSubscriptions()
    {
    
            $pausedSubscriptions = Order::whereNull('request_id')
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'paused');
            })
            ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
            ->orderBy('created_at', 'desc')
            ->get();
        

        return view('admin.flower-order.manage-paused-subscriptions', compact('pausedSubscriptions'));
    }

    public function showexpiredSubscriptions()
    {
        // Fetch the orders with unique user_id where request_id is null and subscription status is expired
        $expiredSubscriptions = Order::whereNull('request_id')
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'expired');
            })
            ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address.localityDetails'])
            ->distinct('user_id') // Use distinct to fetch unique user_id
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.flower-order.manage-expired-subscriptions', compact('expiredSubscriptions'));
    }

    public function showOrdersToday()
    {
        $today = \Carbon\Carbon::today();
        $ordersRequestedToday = Subscription::whereDate('start_date', $today)
            ->with(['order.flowerPayments', 'order.user', 'order.flowerProduct', 'order.address'])
            ->get();

        return view('admin.flower-order.manage-today-requestorder', compact('ordersRequestedToday'));
    }

    public function assignRider(Request $request, $orderId)
    {
        $request->validate([
            'rider_id' => 'required|exists:flower__rider_details,rider_id',
        ]);
        $order = Order::findOrFail($orderId);
        $order->rider_id = $request->rider_id;
        $order->save();

        return redirect()->back()->with('success', 'Rider assigned successfully.');
    }

    public function refferRider(Request $request, $orderId)
    {
        $request->validate([
            'referral_id' => 'required|exists:flower__rider_details,rider_id', // Ensure the referral_id exists in the riders table
        ]);

        $order = Order::findOrFail($orderId);
        $order->referral_id = $request->referral_id;
        $order->save();

        return redirect()->back()->with('success', 'Rider referred successfully.');
    }

    public function updateRider(Request $request, $orderId)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'rider_id' => 'required|exists:flower__rider_details,rider_id', // Ensure rider_id exists
        ]);

        // Find the order
        $order = Order::findOrFail($orderId);

        // Update the rider
        $order->rider_id = $request->rider_id;
        $order->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Rider updated successfully.');
    }

public function mngdeliveryhistory(Request $request)
{
    try {
        $filter = $request->input('filter', 'all');
        $tz     = config('app.timezone', 'Asia/Kolkata');

        // ----- Resolve date range -----
        $from = null;
        $to   = null;

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->input('from_date'), $tz)->startOfDay();
            $to   = Carbon::parse($request->input('to_date'),   $tz)->endOfDay();
        } else {
            switch ($filter) {
                case 'todaydelivery':
                    $from = Carbon::now($tz)->startOfDay();
                    $to   = Carbon::now($tz)->endOfDay();
                    break;

                case 'monthlydelivery':
                    $from = Carbon::now($tz)->startOfMonth();
                    $to   = Carbon::now($tz)->endOfMonth();
                    break;

                default:
                    // DEFAULT: last 7 days including today
                    $from = Carbon::now($tz)->subDays(6)->startOfDay();
                    $to   = Carbon::now($tz)->endOfDay();
                    break;
            }
        }

        // Riders for dropdown
        $riders = RiderDetails::where('status', 'active')->orderBy('rider_name')->get();

        // =========================
        // 1) NORMAL ORDER DELIVERY HISTORY
        // =========================
        $orderQuery = DeliveryHistory::with([
            'order.user',
            'order.flowerProduct',
            'order.flowerPayments',
            'order.address.localityDetails',
            'rider',
        ])->whereBetween('created_at', [$from, $to])
          ->orderBy('created_at', 'desc');

        if ($request->filled('rider_id')) {
            $orderQuery->where('rider_id', $request->input('rider_id'));
        }

        $deliveryHistory = $orderQuery->get();

        $totalDeliveriesToday = DeliveryHistory::whereDate(
            'created_at',
            Carbon::now($tz)->toDateString()
        )->count();

        $metricsOrder = [
            'total'         => $deliveryHistory->count(),
            'delivered'     => $deliveryHistory->filter(fn($h) => in_array(strtolower($h->delivery_status ?? ''), ['delivered', 'completed']))->count(),
            'unique_riders' => $deliveryHistory->pluck('rider.rider_name')->filter()->unique()->count(),
        ];

        // =========================
        // 2) CUSTOMIZE DELIVERY HISTORY
        // =========================
        $customQuery = DeliveryCustomizeHistory::with([
            'flowerRequest.user',
            'flowerRequest.flowerProduct',
            'flowerRequest.address.localityDetails',
            'rider',
        ])
        // Filter by delivery_time if available else created_at
        ->where(function ($q) use ($from, $to) {
            $q->whereBetween('delivery_time', [$from, $to])
              ->orWhere(function ($q2) use ($from, $to) {
                  $q2->whereNull('delivery_time')->whereBetween('created_at', [$from, $to]);
              });
        })
        ->orderByDesc('delivery_time')
        ->orderByDesc('created_at');

        if ($request->filled('rider_id')) {
            $customQuery->where('rider_id', $request->input('rider_id'));
        }

        $customizeDeliveryHistory = $customQuery->get();

        $totalCustomizeDeliveriesToday = DeliveryCustomizeHistory::whereDate(
            \DB::raw('DATE(COALESCE(delivery_time, created_at))'),
            Carbon::now($tz)->toDateString()
        )->count();

        $metricsCustomize = [
            'total'         => $customizeDeliveryHistory->count(),
            'delivered'     => $customizeDeliveryHistory->filter(fn($h) => in_array(strtolower($h->delivery_status ?? ''), ['delivered', 'completed']))->count(),
            'unique_riders' => $customizeDeliveryHistory->pluck('rider.rider_name')->filter()->unique()->count(),
        ];

        $from_date = $from->toDateString();
        $to_date   = $to->toDateString();

        return view('admin.flower-order.manage-delivery-history', compact(
            'deliveryHistory',
            'totalDeliveriesToday',
            'metricsOrder',

            'customizeDeliveryHistory',
            'totalCustomizeDeliveriesToday',
            'metricsCustomize',

            'riders',
            'from_date',
            'to_date'
        ));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Failed to fetch delivery history: ' . $e->getMessage()]);
    }
}

    public function showRiderDetails($id)
    {
        // Fetch rider details
        $rider = RiderDetails::findOrFail($id);

        // Fetch delivery history for the rider
        $deliveryHistory = DeliveryHistory::with([
            'order.user',
            'order.flowerProduct',
            'order.flowerPayments',
            'order.address.localityDetails',
            'rider'
        ])
        ->where('rider_id', $rider->rider_id)
        ->orderBy('created_at', 'desc')
        ->get();

        // Add pickup history
        $pickupHistory = FlowerPickupDetails::with([
            'vendor',
            'rider',
            'flowerPickupItems',
        ])
        ->where('rider_id', $rider->rider_id)
        ->orderBy('created_at', 'desc')
        ->get();

        // Calculate total pickup price
        $total_price = FlowerPickupDetails::where('rider_id', $rider->rider_id)->sum('total_price');

        // Calculate total paid pickup
        $total_paid = FlowerPickupDetails::where('rider_id', $rider->rider_id)
            ->where('payment_status', 'Paid')
            ->sum('total_price');

        // Calculate total unpaid pickup
        $total_unpaid = FlowerPickupDetails::where('rider_id', $rider->rider_id)
            ->where('payment_status', 'pending')
            ->sum('total_price');

        // Calculate total orders
        $totalOrders = $deliveryHistory->count();

        // Calculate ongoing orders
        $ongoingOrders = $deliveryHistory->where('delivery_status', 'ongoing')->count();

        // Calculate monthly orders
        $monthlyOrders = $deliveryHistory->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->count();

        // Calculate total spend (fixed to avoid null error)
        $totalSpend = $deliveryHistory->sum(function ($history) {
            if ($history->order && $history->order->flowerPayments) {
                return $history->order->flowerPayments->sum('paid_amount');
            }
            return 0;
        });

        // Return to the Blade view
        return view('admin.rider-all-details', compact(
            'total_price',
            'total_paid',
            'total_unpaid',
            'rider',
            'pickupHistory',
            'deliveryHistory',
            'totalOrders',
            'ongoingOrders',
            'monthlyOrders',
            'totalSpend'
        ));
    }

    public function updateAddress(Request $request, $id)
    {
        // Find the address by ID
        $address = UserAddress::findOrFail($id);

        // Update the address fields
        $address->apartment_flat_plot = $request->input('apartment_flat_plot');
        $address->apartment_name = $request->input('apartment_name');
        $address->locality = $request->input('locality_name');
        $address->landmark = $request->input('landmark');
        $address->pincode = $request->input('pincode');
        $address->city = $request->input('city');
        $address->state = $request->input('state');

        // Save the updated address
        $address->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Address updated successfully.');
    }

    public function updatePrice(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'total_price' => 'required|numeric|min:0',
        ]);

        // Find the order by ID
        $order = Order::findOrFail($id);

        // Update the total price
        $order->total_price = $request->input('total_price');
        $order->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Order price updated successfully!');
    }

    public function updatePaymentStatus(Request $request, $order_id)
    {
        $request->validate([
            'payment_status' => 'required|string|in:pending,paid',
        ]);

        $payment = FlowerPayment::where('order_id', $order_id)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found.');
        }

        $payment->update([
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    public function pausePage($id)
    {

        $order = Subscription::where('id', $id)->firstOrFail();

        return view('admin.pause-resume' , [
            'order' => $order,
            'action' => 'pause',
        ]);

    }

    public function resumePage($id)
    {
        $order = Subscription::where('id', $id)->firstOrFail();

        return view('admin.pause-resume', [
            'order' => $order,
            'action' => 'resume',
        ]);
    }

    public function pause(Request $request, $id)
    {
        try {
            // Find the subscription by order_id
            $subscription = Subscription::where('id', $id)->where('status','active')->firstOrFail();

            // Validate input dates
            $pauseStartDate = Carbon::parse($request->pause_start_date);
            $pauseEndDate = Carbon::parse($request->pause_end_date);
            $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates

            // Get the most recent new_end_date or default to the original end_date
            $lastNewEndDate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                ->orderBy('id', 'desc')
                ->value('new_end_date');

            // Use the most recent new_end_date for recalculating the new end date
            $currentEndDate = $lastNewEndDate ? Carbon::parse($lastNewEndDate) : Carbon::parse($subscription->end_date);

            // Calculate the new end date by adding paused days
            $newEndDate = $currentEndDate->addDays($pausedDays);

            // Update the subscription status and new date field
            $subscription->update([
                'pause_start_date' => $pauseStartDate,
                'pause_end_date' => $pauseEndDate,
                'new_date' => $newEndDate,
            ]);

            // Log the pause action
            SubscriptionPauseResumeLog::create([
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $subscription->order_id,
                'action' => 'paused',
                'pause_start_date' => $pauseStartDate,
                'pause_end_date' => $pauseEndDate,
                'paused_days' => $pausedDays,
                'new_end_date' => $newEndDate,
            ]);
            
            return redirect()->back()->with('success', 'Successfully paused subscription');

        } catch (\Exception $e) {
            // Log any errors that occur during the process
            Log::error('Error pausing subscription', [
                'order_id' => $subscription->order_id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('success', 'Failed to pause subscription.');

        }
    }

    public function resume(Request $request, $id)
    {
        try {
            // Find the subscription by order_id
            $subscription = Subscription::where('id', $id)->where('status','paused')->firstOrFail();

            // Validate that the subscription is currently paused
            if ($subscription->status !== 'paused') {
                return redirect()->back()->with('error', 'Subscription is not in a paused state.');
            }

            // Parse the dates
            $resumeDate = Carbon::parse($request->resume_date);
            $pauseStartDate = Carbon::parse($subscription->pause_start_date);
            $pauseEndDate = Carbon::parse($subscription->pause_end_date);
            $currentEndDate = $subscription->new_date ? Carbon::parse($subscription->new_date) : Carbon::parse($subscription->end_date);

            // Ensure the resume date is within the pause period
            if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
                return redirect()->back()->with('error', 'Resume date must be within the pause period.');
            }

            // Calculate the days actually paused until the resume date
            $actualPausedDays = $resumeDate->diffInDays($pauseStartDate) + 1;

            // Adjust the new end date by subtracting the paused days
            $newEndDate = $currentEndDate->subDays($actualPausedDays);

            // Update the subscription
            $subscription->update([
                'status' => 'active',
                'pause_start_date' => null,
                'pause_end_date' => null,
                'new_date' => $newEndDate,
            ]);

            // Log the resume action
            SubscriptionPauseResumeLog::create([
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $subscription->order_id,
                'action' => 'resumed',
                'resume_date' => $resumeDate,
                'pause_start_date' => $pauseStartDate,
                'new_end_date' => $newEndDate,
                'paused_days' => $actualPausedDays,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Successfully resumed subscription.');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Failed to resume subscription.');
        }
    }

    public function discontinue($userId)
    {
        // Find all subscriptions related to the order
        $subscriptions = Subscription::where('user_id', $userId)->get();

        // Update their status to 'dead'
        foreach ($subscriptions as $subscription) {
            $subscription->status = 'dead';
            $subscription->save();
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'All related subscriptions have been discontinued.');
    }

}
