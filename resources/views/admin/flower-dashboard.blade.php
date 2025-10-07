@extends('admin.layouts.apps')
@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/flower-dashboard.css') }}" rel="stylesheet" />

    <style>
        /* =============================================
           Metric cards with overlay background blink
           (border stays untouched)
           ============================================= */
        .metric-card,
        .card.sales-card {
            transition: transform .2s ease, box-shadow .35s ease;
            will-change: transform, box-shadow;
            background-clip: padding-box;
            border-radius: .75rem; /* feel free to match your theme */
            position: relative; /* needed for overlay */
            overflow: hidden;   /* keep overlay rounded */
        }

        /* Non-metric cards keep their default white background */
        .card.sales-card:not(.metric-card) { background-color: #fff; }

        /* ---- Color presets via CSS variables (overlay tints) ---- */
        .metric-card {
            --metric-base: rgba(0,0,0,0.03);
            --metric-peak: rgba(0,0,0,0.12);
        }
        .metric--cyan    { --metric-base: rgba(6,182,212,0.14);  --metric-peak: rgba(6,182,212,0.36); }
        .metric--amber   { --metric-base: rgba(245,158,11,0.16); --metric-peak: rgba(245,158,11,0.38); }
        .metric--fuchsia { --metric-base: rgba(217,70,239,0.14); --metric-peak: rgba(217,70,239,0.34); }
        .metric--emerald { --metric-base: rgba(16,185,129,0.14); --metric-peak: rgba(16,185,129,0.34); }

        /* ---- Overlay that paints background (never affects border) ---- */
        .metric-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: var(--metric-base) !important; /* ensure it wins */
            transition: background-color .35s ease;
            z-index: 0;
        }
        /* Keep content above overlay */
        .metric-card > * { position: relative; z-index: 1; }

        /* ---- Blink by animating the overlay only ---- */
        .metric-card.is-blinking::before {
            animation: metricBlink 1.2s ease-in-out 0s 6;
        }
        @keyframes metricBlink {
            0%   { background-color: var(--metric-base); }
            50%  { background-color: var(--metric-peak); box-shadow: 0 10px 24px rgba(0,0,0,0.12); }
            100% { background-color: var(--metric-base); }
        }

        /* Reduced motion: no animation, just stronger tint briefly */
        @media (prefers-reduced-motion: reduce) {
            .metric-card.is-blinking::before { animation: none; background-color: var(--metric-peak) !important; }
        }

        /* Tiny floating pill to unlock audio (optional) */
        #sound-unlock {
            position: fixed; right: 16px; bottom: 16px; z-index: 9999;
            background: #111827; color: #fff; padding: 8px 12px; border-radius: 999px;
            box-shadow: 0 6px 20px rgba(0,0,0,.22); font-size: 12px; cursor: pointer; opacity: .9;
        }
        #sound-unlock.hidden { display: none; }
    </style>
@endsection

@section('content')
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h6 class="card-title-custom mb-4" style="font-size: 14px">Todays Transaction</h6>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Active Subscription/Total Delivery</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeSubscriptions }}/{{ $totalDeliveriesTodayCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Total Income</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            ₹{{ number_format($totalIncomeToday, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                        <div class="row">
                            <div class="col-8">
                                <div class="ps-4 pt-4 pe-3 pb-4">
                                    <h6 class="mb-2 tx-12">Today Total Expenditure</h6>
                                    <h4 class="tx-20 font-weight-semibold mb-2">
                                        ₹{{ number_format($todayTotalExpenditure, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tomorrowOrder']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Tomorrow Active Order</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $tomorrowActiveOrder }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom mb-4" style="font-size: 14px">Todays Rider Details</h4>
            <div class="row">
                @foreach ($ridersData as $data)
                    <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12 mb-4">
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}" target="_blank" class="text-decoration-none">
                            <div class="sales-card" style="border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,.1); border: 1px solid rgb(186,185,185);">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2 text-dark">{{ $data['rider']->rider_name }}</h6>
                                            <div class="d-flex flex-column">
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-2">Delivery Assigned: {{ $data['totalAssignedOrders'] }}</h4>
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-0">Delivered: {{ $data['totalDeliveredToday'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Rider Details -->
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Rider Details</h4>

            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card bg-gradient-primary text-white" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Riders</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalRiders }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card text-white metric-card metric--emerald" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery Today</h6>
                                        <h4 id="totalDeliveriesTodayCount" data-initial="{{ $totalDeliveriesToday }}" class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveriesToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}" target="_blank">
                        <div class="card sales-card bg-gradient-success text-white" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Delivery in Month</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalDeliveriesThisMonth }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory') }}" target="_blank">
                        <div class="card sales-card bg-gradient-secondary text-white" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalDeliveries }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Order -->
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Todays Order</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card metric-card metric--amber" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Subscription</h6>
                                        <h4 id="newUserSubscriptionCount" data-initial="{{ $newUserSubscription }}" class="tx-20 font-weight-semibold mb-2">
                                            {{ $newUserSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card metric-card metric--fuchsia" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Renewed Subscription</h6>
                                        <h4 id="renewSubscriptionCount" data-initial="{{ $renewSubscription }}" class="tx-20 font-weight-semibold mb-2">
                                            {{ $renewSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order (WATCH, main one) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('flower-request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card metric-card metric--cyan" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order</h6>
                                        <h4 id="ordersRequestedTodayCount" data-initial="{{ $ordersRequestedToday }}" class="tx-20 font-weight-semibold mb-2">
                                            {{ $ordersRequestedToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('flower-request', ['filter' => 'upcoming']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order (Next 3 Days)</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $upcomingCustomizeOrders }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Subscription Status -->
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Subscription Status</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Ends Today</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $todayEndSubscription }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'fivedays']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription ends in 5 days</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $subscriptionEndFiveDays }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'expired']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription Renew Pending</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $expiredSubscriptions }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'rider']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Order Assign Rider</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $nonAssignedRidersCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Marketing -->
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Marketing</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.visitPlace', ['filter' => 'todayVisitPlace']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Marketing Visit Place Today</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $visitPlaceCountToday }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral -->
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Referal Details</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'claimed', 'date' => 'today']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Claimed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $todayClaimed ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'approved', 'date' => 'today']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Approved</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $todayApproved ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.referrals.index', ['date' => 'today']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $todayRefer ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.referrals.index', ['date' => 'all']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186,185,185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $totalRefer ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Add the unlock pill --}}
    <div id="sound-unlock" class="hidden">🔔 Enable sound</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script>feather.replace();</script>

    <script>
        // Hide welcome sections if present
        setTimeout(() => { const el = document.getElementById('welcomeSection'); if (el) el.style.display = 'none'; }, 5000);
        setTimeout(() => { const el = document.getElementById('welcomeSections'); if (el) el.style.display = 'none'; }, 5000);

        // Live time
        function updateDateTime() {
            const now  = new Date();
            const date1 = document.getElementById('todayDate');
            const time1 = document.getElementById('liveTime');
            const date2 = document.getElementById('current-date');
            const time2 = document.getElementById('current-time');
            if (date1) date1.textContent = now.toLocaleDateString(undefined, {year:'numeric',month:'long',day:'numeric'});
            if (time1) time1.textContent = now.toLocaleTimeString();
            if (date2) date2.textContent = now.toLocaleDateString(undefined, {weekday:'long',year:'numeric',month:'long',day:'numeric'});
            if (time2) time2.textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <!-- Live metrics poll + overlay blink -->
    <script>
        (function() {
            const watchers = [
                { key: 'ordersRequestedToday', elId: 'ordersRequestedTodayCount' }, // metric--cyan
                { key: 'newUserSubscription',  elId: 'newUserSubscriptionCount'  }, // metric--amber
                { key: 'renewSubscription',    elId: 'renewSubscriptionCount'    }, // metric--fuchsia
                { key: 'totalDeliveriesToday', elId: 'totalDeliveriesTodayCount' }  // metric--emerald
            ];

            const els = {};
            const prev = {};
            watchers.forEach(w => {
                els[w.key] = document.getElementById(w.elId);
                if (els[w.key]) {
                    const initAttr = parseInt(els[w.key].getAttribute('data-initial'), 10);
                    const parsed   = Number.isFinite(initAttr) ? initAttr : (parseInt(els[w.key].textContent,10) || 0);
                    prev[w.key]    = parsed;
                }
            });

            function findMetricCard(el) {
                return el ? (el.closest('.metric-card') || el.closest('.card')) : null;
            }

            function blink(el) {
                const card = findMetricCard(el);
                if (!card) return;

                // restart animation cleanly on the overlay
                card.classList.remove('is-blinking');
                // force reflow
                void card.offsetWidth;
                card.classList.add('is-blinking');

                const endOnce = () => {
                    card.classList.remove('is-blinking');
                    card.removeEventListener('animationend', endOnce);
                };
                card.addEventListener('animationend', endOnce);
                setTimeout(() => card.classList.remove('is-blinking'), 8000);
            }

            // (Optional) minimal beep queue if you still want sound
            let audioEnabled = false, audioCtx = null, beepQueue = [];
            function ensureAudio(){try{if(!audioCtx)audioCtx=new(window.AudioContext||window.webkitAudioContext)();if(audioCtx&&audioCtx.state==='suspended'){audioCtx.resume().then(()=>{audioEnabled=true;flush();});}else{audioEnabled=true;flush();}}catch(e){}}
            function flush(){while(audioEnabled&&beepQueue.length){const [ms,f]=beepQueue.shift();_beep(ms,f);}}
            function _beep(ms=230,f=880){try{const o=audioCtx.createOscillator(),g=audioCtx.createGain();o.type='sine';o.frequency.value=f;g.gain.value=.0001;o.connect(g).connect(audioCtx.destination);o.start();g.gain.exponentialRampToValueAtTime(.06,audioCtx.currentTime+.02);g.gain.exponentialRampToValueAtTime(.0001,audioCtx.currentTime+(ms/1000));setTimeout(()=>o.stop(),ms+60);}catch(e){}}
            function beep(ms=230,f=880){if(!audioEnabled){beepQueue.push([ms,f]);return;} _beep(ms,f);}
            (function setupUnlock(){
                const pill=document.getElementById('sound-unlock'); if(!pill) return;
                const hide=()=>pill.classList.add('hidden');
                pill.classList.remove('hidden');
                const unlock=()=>{ensureAudio(); hide(); window.removeEventListener('click',unlock,true); window.removeEventListener('keydown',unlock,true); pill.removeEventListener('click',unlock,true);};
                window.addEventListener('click',unlock,true); window.addEventListener('keydown',unlock,true); pill.addEventListener('click',unlock,true);
            })();

            // Initial blink if values > 0
            function initialKick() {
                watchers.forEach(w => {
                    const el = els[w.key]; if (!el) return;
                    const val = prev[w.key] ?? 0;
                    if (val > 0) {
                        blink(el);
                        if (w.key === 'ordersRequestedToday') { beep(260,1200); setTimeout(()=>beep(220,900),160); }
                        else { beep(200,880); }
                    }
                });
            }

            async function poll() {
                try {
                    const res = await fetch(`{{ route('admin.flowerDashboard.liveMetrics') }}`, { headers: { 'Accept':'application/json' }, cache: 'no-store' });
                    if (!res.ok) throw new Error('Bad response');
                    const json = await res.json();
                    if (!json || !json.ok || !json.data) return;

                    const data = json.data;
                    watchers.forEach(w => {
                        const el = els[w.key];
                        if (!el) return;
                        const newVal = parseInt(data[w.key], 10) || 0;
                        const oldVal = prev[w.key] ?? 0;
                        if (newVal !== oldVal) {
                            el.textContent = newVal;
                            if (newVal > oldVal) {
                                blink(el); // overlay blink (background only)
                                if (w.key === 'ordersRequestedToday') { beep(260,1200); setTimeout(()=>beep(220,900),160); }
                                else { beep(200,880); }
                            }
                            prev[w.key] = newVal;
                        }
                    });
                } catch(e) { /* console.warn(e); */ }
            }

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') poll();
            });

            document.addEventListener('DOMContentLoaded', () => {
                initialKick();
                poll();
                setInterval(poll, 5000);
            });
        })();
    </script>
@endsection
