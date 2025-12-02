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

    <!-- Dashboard custom css (optional, keep if used elsewhere) -->
    <link href="{{ asset('assets/css/flower-dashboard.css') }}" rel="stylesheet" />

    <!-- Poppins font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f5f6fa;
        }

        .dashboard-section {
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            padding: 16px 18px;
            margin-left: 0;
            margin-right: 0;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
        }

        .card-title-custom {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
        }

        .sales-card,
        .card.sales-card {
            position: relative;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            background: #ffffff; /* default: white (for 0 values) */
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
        }

        .sales-card:hover,
        .card.sales-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.10);
            border-color: #cbd5e1;
        }

        .sales-card h6,
        .sales-card h5 {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #374151;
            margin-bottom: 4px;
        }

        .sales-card h4,
        .sales-card .tx-20,
        .sales-card .tx-22 {
            font-weight: 700;
            font-size: 18px;
            color: #111827;
            margin-bottom: 0;
        }

        .sales-card .tx-12 {
            font-size: 12px;
        }

        /* ========= GRADIENT THEMES (APPLY ONLY WHEN VALUE > 0) ========= */

        .card-theme-sky {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 45%, #a5f3fc 100%) !important;
        }

        .card-theme-emerald {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 45%, #6ee7b7 100%) !important;
        }

        .card-theme-amber {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 45%, #fed7aa 100%) !important;
        }

        .card-theme-indigo {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 45%, #a5b4fc 100%) !important;
        }

        .card-theme-rose {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 45%, #ffe4e6 100%) !important;
        }

        .card-theme-violet {
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 45%, #f5d0fe 100%) !important;
        }

        .card-theme-cyan {
            background: linear-gradient(135deg, #ecfeff 0%, #e0f2fe 45%, #a5f3fc 100%) !important;
        }

        .card-theme-lime {
            background: linear-gradient(135deg, #f7fee7 0%, #ecfccb 45%, #bef264 100%) !important;
        }

        .card-theme-slate {
            background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 45%, #e0f2fe 100%) !important;
        }

        .card-theme-peach {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 45%, #fed7aa 100%) !important;
        }

        .card-theme-mint {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 45%, #a7f3d0 100%) !important;
        }
    </style>
@endsection

@section('content')
    {{-- TODAY'S ORDER --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom">Todays Order</h4>
            <div class="row">
                <!-- New Subscription -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card @if($newUserSubscription > 0) card-theme-sky @endif">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Subscription</h6>
                                        <h4 id="newUserSubscriptionCount"
                                            data-initial="{{ $newUserSubscription }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $newUserSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Renewed Subscription -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card @if($renewSubscription > 0) card-theme-emerald @endif">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Renewed Subscription</h6>
                                        <h4 id="renewSubscriptionCount"
                                            data-initial="{{ $renewSubscription }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $renewSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order (Today) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.customize.request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card @if($ordersRequestedToday > 0) card-theme-amber @endif">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order</h6>
                                        <h4 id="ordersRequestedTodayCount"
                                            data-initial="{{ $ordersRequestedToday }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $ordersRequestedToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order (Next 3 Days) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.customize.request', ['filter' => 'upcoming']) }}" target="_blank">
                        <div class="card sales-card @if($upcomingCustomizeOrders > 0) card-theme-indigo @endif">
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

    {{-- SUBSCRIPTION STATUS --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom">Subscription Status</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card @if($todayEndSubscription > 0) card-theme-rose @endif">
                            <div class="row">
                                <div class="col-12">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'fivedays']) }}" target="_blank">
                        <div class="card sales-card @if($subscriptionEndFiveDays > 0) card-theme-violet @endif">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'expired']) }}" target="_blank">
                        <div class="card sales-card @if($expiredSubscriptions > 0) card-theme-lime @endif">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'rider']) }}" target="_blank">
                        <div class="card sales-card @if($nonAssignedRidersCount > 0) card-theme-cyan @endif">
                            <div class="row">
                                <div class="col-12">
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

    {{-- PAUSED SUBSCRIPTION --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom">Paused Subscription</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'todayrequest']) }}" target="_blank">
                        <div class="card sales-card @if($todayPausedRequest > 0) card-theme-slate @endif">
                            <div class="row">
                                <div class="col-12">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank">
                        <div class="card sales-card @if($pausedSubscriptions > 0) card-theme-peach @endif">
                            <div class="row">
                                <div class="col-12">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tomorrow']) }}" target="_blank">
                        <div class="card sales-card @if($nextDayPaused > 0) card-theme-mint @endif">
                            <div class="row">
                                <div class="col-12">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'nextdayresumed']) }}" target="_blank">
                        <div class="card sales-card @if($nextDayResumed > 0) card-theme-emerald @endif">
                            <div class="row">
                                <div class="col-12">
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

    {{-- TODAY'S TRANSACTION --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h6 class="card-title-custom mb-4">Todays Transaction</h6>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card @if(($activeSubscriptions + $totalDeliveriesTodayCount) > 0) card-theme-sky @endif">
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

                <!-- Today Total Income -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.payments.index') }}" target="_blank">
                        <div class="card sales-card @if($totalIncomeToday > 0) card-theme-emerald @endif">
                            <div class="row">
                                <div class="col-12">
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
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.expenditure.today') }}" class="text-decoration-none d-block">
                        <div class="card sales-card position-relative @if($todayTotalExpenditure > 0) card-theme-rose @endif">
                            <div class="row">
                                <div class="col-12">
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
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.tomorrowSubscriptions') }}" target="_blank">
                        <div class="card sales-card @if($activeTomorrowCount > 0) card-theme-indigo @endif">
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

    {{-- TODAY'S RIDER DETAILS --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12">
            <h4 class="card-title-custom mb-4">Todays Rider Details</h4>
            <div class="row">
                @foreach ($ridersData as $data)
                    @php
                        $assigned = $data['totalAssignedOrders'] ?? 0;
                        $delivered = $data['totalDeliveredToday'] ?? 0;
                        $riderHasData = ($assigned + $delivered) > 0;
                    @endphp
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}"
                           target="_blank" class="text-decoration-none">
                            <div class="card sales-card @if($riderHasData) card-theme-mint @endif">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2 text-dark tx-12">
                                                {{ $data['rider']->rider_name }}
                                            </h6>
                                            <div class="d-flex flex-column">
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-2">
                                                    Delivery Assigned: {{ $assigned }}
                                                </h4>
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-0">
                                                    Delivered: {{ $delivered }}
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

    {{-- RIDER DETAILS SUMMARY --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12">
            <h4 class="card-title-custom">Rider Details</h4>
            <div class="row">
                <!-- Total Riders -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card @if($totalRiders > 0) card-theme-sky @endif">
                            <div class="row">
                                <div class="col-12">
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

                <!-- Total Delivery Today -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card @if($totalDeliveriesToday > 0) card-theme-emerald @endif">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery Today</h6>
                                        <h4 id="totalDeliveriesTodayCount"
                                            data-initial="{{ $totalDeliveriesToday }}"
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
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}"
                       target="_blank">
                        <div class="card sales-card @if($totalDeliveriesThisMonth > 0) card-theme-indigo @endif">
                            <div class="row">
                                <div class="col-12">
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
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.managedeliveryhistory') }}" target="_blank">
                        <div class="card sales-card @if($totalDeliveries > 0) card-theme-amber @endif">
                            <div class="row">
                                <div class="col-12">
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

    {{-- MARKETING --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom">Marketing</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.visitPlace', ['filter' => 'todayVisitPlace']) }}" target="_blank">
                        <div class="card sales-card @if($visitPlaceCountToday > 0) card-theme-rose @endif">
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

    {{-- REFERRAL DETAILS --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom">Referal Details</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'claimed', 'date' => 'today']) }}"
                       target="_blank">
                        <div class="card sales-card @if(($todayClaimed ?? 0) > 0) card-theme-emerald @endif">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'approved', 'date' => 'today']) }}"
                       target="_blank">
                        <div class="card sales-card @if(($todayApproved ?? 0) > 0) card-theme-lime @endif">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.referrals.index', ['date' => 'today']) }}" target="_blank">
                        <div class="card sales-card @if(($todayRefer ?? 0) > 0) card-theme-cyan @endif">
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

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.referrals.index', ['date' => 'all']) }}" target="_blank">
                        <div class="card sales-card @if(($totalRefer ?? 0) > 0) card-theme-violet @endif">
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
    </script>

    <!-- Live metrics poll (simple, no sounds) -->
    <script>
        (function () {
            const watchers = [
                { key: 'ordersRequestedToday', elId: 'ordersRequestedTodayCount' },
                { key: 'newUserSubscription', elId: 'newUserSubscriptionCount' },
                { key: 'renewSubscription',    elId: 'renewSubscriptionCount' },
                { key: 'totalDeliveriesToday', elId: 'totalDeliveriesTodayCount' },
            ];

            const els = {}, prev = {};

            watchers.forEach(w => {
                const el = document.getElementById(w.elId);
                els[w.key] = el;
                if (el) {
                    const initAttr = parseInt(el.getAttribute('data-initial'), 10);
                    const parsed = Number.isFinite(initAttr)
                        ? initAttr
                        : (parseInt(el.textContent, 10) || 0);
                    prev[w.key] = parsed;
                }
            });

            async function poll() {
                try {
                    const url = `{{ route('admin.flowerDashboard.liveMetrics') }}`;
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                    if (!res.ok) return;
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
                            prev[w.key] = newVal;
                        }
                    });
                } catch (e) {
                    // silent fail
                }
            }

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') poll();
            });

            document.addEventListener('DOMContentLoaded', () => {
                poll();
                setInterval(poll, 5000);
            });
        })();
    </script>
@endsection
