@extends('admin.layouts.apps')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <!-- Feather Icons -->
    <link href="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.css" rel="stylesheet">
    <!-- INTERNAL DataTable css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <!-- Dashboard custom css -->
    <link href="{{ asset('assets/css/flower-dashboard.css') }}" rel="stylesheet" />

    <style>
        :root {
            --nu-primary: #2563eb;
            --nu-primary-soft: #eff6ff;
            --nu-success: #10b981;
            --nu-warning: #f59e0b;
            --nu-danger: #ef4444;
            --nu-slate: #0f172a;
            --nu-muted: #64748b;

            --nu-emerald-soft: #dcfce7;
            --nu-cyan-soft: #e0f2fe;
            --nu-amber-soft: #fef3c7;
            --nu-fuchsia-soft: #fae8ff;
            --nu-indigo-soft: #e0e7ff;
            --nu-pink-soft: #ffe4f1;
            --nu-slate-soft: #f1f5f9;

            --nu-card-radius: 18px;
            --nu-shell-radius: 20px;
        }

        /* ====== SECTION SHELL (outer row card) – like big white tile ====== */
        .dashboard-shell-card {
            border-radius: var(--nu-shell-radius);
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
            padding: 1.25rem 1.5rem 1.4rem;
            margin-top: 1rem;
        }

        .dashboard-shell-card .card-title-custom {
            font-weight: 800;
            color: var(--nu-slate);
            letter-spacing: 0.02em;
        }

        /* Slightly soften the page background if not already */
        body {
            background-color: #f8fafc;
        }

        /* ====== METRIC CARD – matches notification template look ====== */
        .dash-metric-card {
            position: relative;
            border-radius: var(--nu-card-radius);
            border: 1px solid rgba(148, 163, 184, 0.45);
            background: linear-gradient(135deg, #eef2ff, #e0f2fe);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            transition: transform .14s ease, box-shadow .14s ease, border-color .14s ease;
        }

        .dash-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 48px rgba(15, 23, 42, 0.14);
            border-color: rgba(129, 140, 248, 0.8);
        }

        /* Colored corner blob like notification templates */
        .dash-metric-card::after {
            content: "";
            position: absolute;
            right: -40px;
            bottom: -40px;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            opacity: .22;
            background: radial-gradient(circle at 30% 30%, #4f46e5, #0ea5e9);
        }

        /* Text hierarchy inside cards */
        .dash-metric-card h6,
        .dash-metric-card h5 {
            font-size: .8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #0f172a;
            opacity: .9;
        }

        .dash-metric-card h4 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
        }

        .dash-metric-card h5.card-subtitle,
        .dash-metric-card h6.card-subtitle {
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
        }

        /* ====== COLOR VARIANTS (match the pastel chips feel) ====== */
        .dash-metric-card--amber {
            background: linear-gradient(135deg, var(--nu-amber-soft), #fffbeb);
        }

        .dash-metric-card--amber::after {
            background: radial-gradient(circle at 30% 30%, #fbbf24, #f97316);
        }

        .dash-metric-card--fuchsia {
            background: linear-gradient(135deg, var(--nu-fuchsia-soft), #fdf4ff);
        }

        .dash-metric-card--fuchsia::after {
            background: radial-gradient(circle at 30% 30%, #e879f9, #c026d3);
        }

        .dash-metric-card--cyan {
            background: linear-gradient(135deg, var(--nu-cyan-soft), #f0f9ff);
        }

        .dash-metric-card--cyan::after {
            background: radial-gradient(circle at 30% 30%, #22d3ee, #0ea5e9);
        }

        .dash-metric-card--indigo {
            background: linear-gradient(135deg, var(--nu-indigo-soft), #eef2ff);
        }

        .dash-metric-card--indigo::after {
            background: radial-gradient(circle at 30% 30%, #6366f1, #4f46e5);
        }

        .dash-metric-card--emerald {
            background: linear-gradient(135deg, var(--nu-emerald-soft), #f0fdf4);
        }

        .dash-metric-card--emerald::after {
            background: radial-gradient(circle at 30% 30%, #22c55e, #16a34a);
        }

        .dash-metric-card--pink {
            background: linear-gradient(135deg, var(--nu-pink-soft), #fff1f2);
        }

        .dash-metric-card--pink::after {
            background: radial-gradient(circle at 30% 30%, #ec4899, #db2777);
        }

        .dash-metric-card--slate {
            background: linear-gradient(135deg, var(--nu-slate-soft), #e5e7eb);
        }

        .dash-metric-card--slate::after {
            background: radial-gradient(circle at 30% 30%, #64748b, #475569);
        }

        /* Make all inner cards use our metric style */
        .sales-card,
        .card.sales-card {
            position: relative;
            border-radius: var(--nu-card-radius);
            background-clip: padding-box;
            overflow: hidden;
        }

        /* ========= Colorful pulse glows (border halo) ========= */
        .pulse-glow--cyan {
            animation: pulseGlowCyan 1.2s ease-in-out 0s 6;
            border-color: rgba(6, 182, 212, .45) !important;
        }

        .pulse-glow--emerald {
            animation: pulseGlowEmerald 1.2s ease-in-out 0s 6;
            border-color: rgba(16, 185, 129, .45) !important;
        }

        .pulse-glow--fuchsia {
            animation: pulseGlowFuchsia 1.2s ease-in-out 0s 6;
            border-color: rgba(217, 70, 239, .45) !important;
        }

        .pulse-glow--amber {
            animation: pulseGlowAmber 1.2s ease-in-out 0s 6;
            border-color: rgba(245, 158, 11, .45) !important;
        }

        @keyframes pulseGlowCyan {
            0% {
                box-shadow: 0 0 0 rgba(6, 182, 212, 0)
            }

            50% {
                box-shadow: 0 0 34px rgba(6, 182, 212, .75)
            }

            100% {
                box-shadow: 0 0 0 rgba(6, 182, 212, 0)
            }
        }

        @keyframes pulseGlowEmerald {
            0% {
                box-shadow: 0 0 0 rgba(16, 185, 129, 0)
            }

            50% {
                box-shadow: 0 0 34px rgba(16, 185, 129, .75)
            }

            100% {
                box-shadow: 0 0 0 rgba(16, 185, 129, 0)
            }
        }

        @keyframes pulseGlowFuchsia {
            0% {
                box-shadow: 0 0 0 rgba(217, 70, 239, 0)
            }

            50% {
                box-shadow: 0 0 34px rgba(217, 70, 239, .75)
            }

            100% {
                box-shadow: 0 0 0 rgba(217, 70, 239, 0)
            }
        }

        @keyframes pulseGlowAmber {
            0% {
                box-shadow: 0 0 0 rgba(245, 158, 11, 0)
            }

            50% {
                box-shadow: 0 0 34px rgba(245, 158, 11, .75)
            }

            100% {
                box-shadow: 0 0 0 rgba(245, 158, 11, 0)
            }
        }

        /* ========= Background blink via ::after (works with gradients) ========= */
        .pulse-bg--cyan::after {
            --tint: rgba(6, 182, 212, .16);
            animation: pulseBg 1.2s ease-in-out 0s 6;
        }

        .pulse-bg--emerald::after {
            --tint: rgba(16, 185, 129, .16);
            animation: pulseBg 1.2s ease-in-out 0s 6;
        }

        .pulse-bg--fuchsia::after {
            --tint: rgba(217, 70, 239, .16);
            animation: pulseBg 1.2s ease-in-out 0s 6;
        }

        .pulse-bg--amber::after {
            --tint: rgba(245, 158, 11, .16);
            animation: pulseBg 1.2s ease-in-out 0s 6;
        }

        .pulse-bg--cyan::after,
        .pulse-bg--emerald::after,
        .pulse-bg--fuchsia::after,
        .pulse-bg--amber::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            z-index: 0;
            background: transparent;
        }

        @keyframes pulseBg {

            0%,
            100% {
                background: transparent;
            }

            50% {
                background: var(--tint);
            }
        }

        /* children above ::after layer */
        .sales-card>*,
        .card.sales-card>* {
            position: relative;
            z-index: 1;
        }

        /* --- Sound unlock pill --- */
        #sound-unlock {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 9999;
            background: #111827;
            color: #fff;
            padding: 8px 12px;
            border-radius: 999px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .22);
            font-size: 12px;
            cursor: pointer;
            opacity: .9;
        }

        #sound-unlock.hidden {
            display: none;
        }

        /* run animations infinitely while active-for-a-day */
        .pulse-day {
            animation-iteration-count: infinite !important;
        }

        .pulse-day::after {
            animation-iteration-count: infinite !important;
        }
    </style>
@endsection

@section('content')
    {{-- Todays Order --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Todays Order</h4>
            <div class="row">
                <!-- New Subscription (WATCH) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--amber watch-card" data-color="amber">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Subscription</h6>
                                        <h4 id="newUserSubscriptionCount" data-initial="{{ $newUserSubscription }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $newUserSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Renewed Subscription (WATCH) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--fuchsia watch-card"
                            data-color="fuchsia">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Renewed Subscription</h6>
                                        <h4 id="renewSubscriptionCount" data-initial="{{ $renewSubscription }}"
                                            class="tx-20 font-weight-semibold mb-2">
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
                    <a href="{{ route('flower.customize.request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--cyan watch-card" data-color="cyan">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order</h6>
                                        <h4 id="ordersRequestedTodayCount" data-initial="{{ $ordersRequestedToday }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $ordersRequestedToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order (Upcoming 3 Days) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('flower.customize.request', ['filter' => 'upcoming']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--indigo">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order (Next 3 Days)</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $upcomingCustomizeOrders }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Subscription Status --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Subscription Status</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--pink">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Ends Today</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $todayEndSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'fivedays']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--indigo">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription ends in 5 days</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $subscriptionEndFiveDays }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'expired']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--amber">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription Renew Pending</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $expiredSubscriptions }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'rider']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--cyan">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Order Assign Rider</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $nonAssignedRidersCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Paused Subscription --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Paused Subscription</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'todayrequest']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--amber">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Paused Request</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayPausedRequest }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--slate">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Paused Subscription</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $pausedSubscriptions }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tomorrow']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--indigo">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Pause</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $nextDayPaused }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'nextdayresumed']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--emerald">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Resumed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $nextDayResumed }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Todays Transaction --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h6 class="card-title-custom mb-4" style="font-size: 14px">Todays Transaction</h6>
            <div class="row">
                <!-- Active Subscription / Total Delivery -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--cyan">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Active Subscription / Total Delivery</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeSubscriptions }}/{{ $totalDeliveriesTodayCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Today Total Income -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.payments.index') }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--emerald">
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

                <!-- Today Total Expenditure -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('flower.expenditure.today') }}" class="text-decoration-none d-block">
                        <div class="card sales-card dash-metric-card dash-metric-card--pink position-relative">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Total Expenditure</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            ₹{{ number_format($todayTotalExpenditure, 2) }}
                                        </h4>
                                        <span class="stretched-link"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Tomorrow Active Order -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.tomorrowSubscriptions') }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--indigo">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Tomorrow Active Order</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeTomorrowCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Todays Rider Details --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom mb-4" style="font-size: 14px">Todays Rider Details</h4>
            <div class="row">
                @foreach ($ridersData as $data)
                    <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12 mb-4">
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}"
                            target="_blank" class="text-decoration-none">
                            <div class="card sales-card dash-metric-card dash-metric-card--slate">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2">{{ $data['rider']->rider_name }}</h6>
                                            <div class="d-flex flex-column">
                                                <h4 class="tx-12 font-weight-semibold mb-2">
                                                    Delivery Assigned: {{ $data['totalAssignedOrders'] }}
                                                </h4>
                                                <h4 class="tx-12 font-weight-semibold mb-0">
                                                    Delivered: {{ $data['totalDeliveredToday'] }}
                                                </h4>
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

    {{-- Rider Details --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Rider Details</h4>
            <div class="row">
                <!-- Total Riders -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--indigo">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Riders</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalRiders }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery Today (WATCH) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--emerald watch-card"
                            data-color="emerald">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery Today</h6>
                                        <h4 id="totalDeliveriesTodayCount" data-initial="{{ $totalDeliveriesToday }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveriesToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Delivery in Month -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}"
                        target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--cyan">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Delivery in Month</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveriesThisMonth }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory') }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--slate">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveries }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Marketing --}}
    <div class="row card sales-card dashboard-shell-card">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Marketing</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.visitPlace', ['filter' => 'todayVisitPlace']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--pink">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Marketing Visit Place Today</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $visitPlaceCountToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Referal Details --}}
    <div class="row card sales-card dashboard-shell-card mb-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px">Referal Details</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'claimed', 'date' => 'today']) }}"
                        target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--emerald">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Claimed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayClaimed ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'approved', 'date' => 'today']) }}"
                        target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--cyan">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Approved</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayApproved ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.referrals.index', ['date' => 'today']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--amber">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayRefer ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.referrals.index', ['date' => 'all']) }}" target="_blank">
                        <div class="card sales-card dash-metric-card dash-metric-card--slate">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $totalRefer ?? 0 }}
                                        </h4>
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
    <script>
        feather.replace();
    </script>

    <script>
        // Hide welcome sections if present
        setTimeout(() => {
            const el = document.getElementById('welcomeSection');
            if (el) el.style.display = 'none';
        }, 5000);
        setTimeout(() => {
            const el = document.getElementById('welcomeSections');
            if (el) el.style.display = 'none';
        }, 5000);

        // Single DateTime updater
        function updateDateTime() {
            const now = new Date();
            const date1 = document.getElementById('todayDate');
            const time1 = document.getElementById('liveTime');
            const date2 = document.getElementById('current-date');
            const time2 = document.getElementById('current-time');

            if (date1) date1.textContent = now.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            if (time1) time1.textContent = now.toLocaleTimeString();
            if (date2) date2.textContent = now.toLocaleDateString(undefined, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            if (time2) time2.textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <!-- Live metrics poll + colorful glow + sound unlock (unchanged) -->
    <script>
        (function() {
            const watchers = [{
                    key: 'ordersRequestedToday',
                    elId: 'ordersRequestedTodayCount',
                    color: 'cyan'
                },
                {
                    key: 'newUserSubscription',
                    elId: 'newUserSubscriptionCount',
                    color: 'amber'
                },
                {
                    key: 'renewSubscription',
                    elId: 'renewSubscriptionCount',
                    color: 'fuchsia'
                },
                {
                    key: 'totalDeliveriesToday',
                    elId: 'totalDeliveriesTodayCount',
                    color: 'emerald'
                },
            ];

            const els = {},
                prev = {};
            watchers.forEach(w => {
                els[w.key] = document.getElementById(w.elId);
                if (els[w.key]) {
                    const initAttr = parseInt(els[w.key].getAttribute('data-initial'), 10);
                    const parsed = Number.isFinite(initAttr) ? initAttr : (parseInt(els[w.key].textContent,
                        10) || 0);
                    prev[w.key] = parsed;
                }
            });

            function findCard(el) {
                return el ? (el.closest('.watch-card') || el.closest('.card')) : null;
            }

            const DAY_MS = 24 * 60 * 60 * 1000;

            function keyFor(wkey) {
                return `pf_blink_until_${wkey}`;
            }

            function setBlinkUntil(wkey, ts) {
                try {
                    localStorage.setItem(keyFor(wkey), String(ts));
                } catch (e) {}
            }

            function getBlinkUntil(wkey) {
                try {
                    const v = localStorage.getItem(keyFor(wkey));
                    return v ? parseInt(v, 10) : 0;
                } catch (e) {
                    return 0;
                }
            }

            function clearBlinkUntil(wkey) {
                try {
                    localStorage.removeItem(keyFor(wkey));
                } catch (e) {}
            }

            function glow(el, color, {
                persistMs = 0
            } = {}) {
                const card = findCard(el);
                if (!card) return;
                const borderCls = `pulse-glow--${color}`;
                const bgCls = `pulse-bg--${color}`;

                card.classList.add(borderCls, bgCls);

                if (persistMs > 0) {
                    card.classList.add('pulse-day');
                    setTimeout(() => {
                        stopGlow(card, borderCls, bgCls, true);
                    }, persistMs);
                } else {
                    setTimeout(() => {
                        stopGlow(card, borderCls, bgCls, false);
                    }, 6000);
                }
            }

            function stopGlow(card, borderCls, bgCls, removePulseDay) {
                if (!card.classList.contains('pulse-day')) {
                    card.classList.remove(borderCls, bgCls);
                }
                if (removePulseDay) {
                    card.classList.remove('pulse-day', borderCls, bgCls);
                }
            }

            let audioEnabled = false,
                audioCtx = null;
            const beepQueue = [];
            let lastBeepAt = 0;
            const BEEP_COOLDOWN_MS = 3500;

            function ensureAudio() {
                try {
                    if (!audioCtx) audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                    if (audioCtx && audioCtx.state === 'suspended') {
                        audioCtx.resume().then(() => {
                            audioEnabled = true;
                            flushBeepQueue();
                        });
                    } else {
                        audioEnabled = true;
                        flushBeepQueue();
                    }
                } catch (e) {}
            }

            function flushBeepQueue() {
                while (audioEnabled && beepQueue.length) {
                    const [ms, f] = beepQueue.shift();
                    _beep(ms, f);
                }
            }

            function _beep(ms = 230, freq = 880) {
                try {
                    const now = Date.now();
                    if (now - lastBeepAt < BEEP_COOLDOWN_MS) return;
                    lastBeepAt = now;

                    const osc = audioCtx.createOscillator(),
                        gain = audioCtx.createGain();

                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    gain.gain.value = 0.0001;

                    osc.connect(gain).connect(audioCtx.destination);
                    osc.start();

                    gain.gain.exponentialRampToValueAtTime(0.07, audioCtx.currentTime + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + (ms / 1000));

                    setTimeout(() => {
                        try {
                            osc.stop();
                        } catch (e) {}
                    }, ms + 60);
                } catch (e) {}
            }

            function beep(ms = 230, f = 880) {
                if (!audioEnabled) {
                    beepQueue.push([ms, f]);
                    return;
                }
                _beep(ms, f);
            }

            function setupUnlockUI() {
                const pill = document.getElementById('sound-unlock');
                const hide = () => pill && pill.classList.add('hidden');

                if (audioEnabled) {
                    hide();
                    return;
                }

                if (pill) pill.classList.remove('hidden');

                const unlock = () => {
                    ensureAudio();
                    hide();
                    window.removeEventListener('click', unlock, true);
                    window.removeEventListener('keydown', unlock, true);
                    pill && pill.removeEventListener('click', unlock, true);
                };

                window.addEventListener('click', unlock, true);
                window.addEventListener('keydown', unlock, true);
                pill && pill.addEventListener('click', unlock, true);
            }

            function initialKick() {
                watchers.forEach(w => {
                    const el = els[w.key];
                    if (!el) return;
                    const val = prev[w.key] ?? 0;

                    const until = getBlinkUntil(w.key);
                    const dayActive = until && Date.now() < until;

                    if (dayActive) {
                        glow(el, w.color, {
                            persistMs: until - Date.now()
                        });
                    }

                    if (!dayActive && val > 0) {
                        glow(el, w.color);
                        if (w.key === 'ordersRequestedToday') {
                            beep(260, 1200);
                            setTimeout(() => beep(220, 900), 160);
                        } else {
                            beep(200, 880);
                        }
                    }

                    if (!dayActive && until && Date.now() >= until) clearBlinkUntil(w.key);
                });
            }

            async function poll() {
                try {
                    const url = `{{ route('admin.flowerDashboard.liveMetrics') }}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    });
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
                                if (w.key === 'ordersRequestedToday') {
                                    const untilTs = Date.now() + DAY_MS;
                                    setBlinkUntil(w.key, untilTs);
                                    glow(el, w.color, {
                                        persistMs: DAY_MS
                                    });
                                    beep(300, 1250);
                                    setTimeout(() => beep(240, 920), 170);
                                } else {
                                    glow(el, w.color);
                                    beep(200, 880);
                                }
                            }
                            prev[w.key] = newVal;
                        }
                    });
                } catch (e) {}
            }

            function updateDateTime() {
                const now = new Date();
                const date1 = document.getElementById('todayDate');
                const time1 = document.getElementById('liveTime');
                const date2 = document.getElementById('current-date');
                const time2 = document.getElementById('current-time');

                if (date1) date1.textContent = now.toLocaleDateString(undefined, {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                if (time1) time1.textContent = now.toLocaleTimeString();
                if (date2) date2.textContent = now.toLocaleDateString(undefined, {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                if (time2) time2.textContent = now.toLocaleTimeString();
            }

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') poll();
            });

            document.addEventListener('DOMContentLoaded', () => {
                setupUnlockUI();
                updateDateTime();
                setInterval(updateDateTime, 1000);
                initialKick();
                poll();
                setInterval(poll, 5000);
            });
        })();
    </script>
@endsection
