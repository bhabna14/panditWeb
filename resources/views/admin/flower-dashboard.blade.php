{{-- resources/views/admin/flower-dashboard.blade.php --}}
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
            background: linear-gradient(180deg, #e0f2fe, #f9fafb);
        }

        .dashboard-section {
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e5e7eb;
            padding: 16px 18px;
            margin-left: 0;
            margin-right: 0;
            margin-bottom: 16px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(10px);
        }

        .card-title-custom {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            letter-spacing: 0.04em;
        }

        .sales-card,
        .card.sales-card {
            position: relative;
            border-radius: 14px;
            border: none;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
            overflow: hidden;
        }

        .sales-card:hover,
        .card.sales-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.28);
        }

        .sales-card h6,
        .sales-card h5 {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.10em;
            margin-bottom: 4px;
            color: #6b7280;
        }

        .sales-card h4,
        .sales-card .tx-20,
        .sales-card .tx-22 {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 0;
            color: #111827;
        }

        .sales-card .tx-12 {
            font-size: 12px;
        }

        /* ---------- Gradient card styling (only when gradient-* class is present) ---------- */
        .sales-card[class*="gradient-"] {
            color: #f9fafb;
            border: none;
        }

        .sales-card[class*="gradient-"] h4,
        .sales-card[class*="gradient-"] h5,
        .sales-card[class*="gradient-"] h6,
        .sales-card[class*="gradient-"] .tx-20,
        .sales-card[class*="gradient-"] .tx-22,
        .sales-card[class*="gradient-"] .tx-12,
        .sales-card[class*="gradient-"] .text-dark {
            color: #f9fafb !important;
        }

        .sales-card[class*="gradient-"]::before {
            content: "";
            position: absolute;
            right: -40px;
            top: -40px;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.20);
            pointer-events: none;
        }

        .sales-card[class*="gradient-"]::after {
            content: "";
            position: absolute;
            left: -60px;
            bottom: -60px;
            width: 160px;
            height: 160px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            pointer-events: none;
        }

        /* ---------- Different gradient flavors ---------- */
        .sales-card.gradient-1 {
            background: linear-gradient(135deg, #6366f1, #22d3ee) !important;
        }

        .sales-card.gradient-2 {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        }

        .sales-card.gradient-3 {
            background: linear-gradient(135deg, #f97316, #facc15) !important;
        }

        .sales-card.gradient-4 {
            background: linear-gradient(135deg, #ec4899, #f97316) !important;
        }

        .sales-card.gradient-5 {
            background: linear-gradient(135deg, #0f172a, #1e293b) !important;
        }

        .sales-card.gradient-6 {
            background: linear-gradient(135deg, #a855f7, #6366f1) !important;
        }

        .sales-card.gradient-7 {
            background: linear-gradient(135deg, #14b8a6, #22c55e) !important;
        }

        .sales-card.gradient-8 {
            background: linear-gradient(135deg, #f97373, #fb7185) !important;
        }

        .sales-card.gradient-9 {
            background: linear-gradient(135deg, #22d3ee, #0ea5e9) !important;
        }

        .sales-card.gradient-10 {
            background: linear-gradient(135deg, #4ade80, #a3e635) !important;
        }

        .sales-card.gradient-11 {
            background: linear-gradient(135deg, #facc15, #f97316) !important;
        }

        .sales-card.gradient-12 {
            background: linear-gradient(135deg, #6366f1, #a855f7) !important;
        }
    </style>
@endsection

@section('content')
    {{-- TODAY'S ORDER --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom">TODAYS ORDER</h4>
            <div class="row">
                <!-- New Subscription -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card {{ $newUserSubscription > 0 ? 'gradient-1' : '' }}">
                            <div class="row">
                                <div class="col-12">
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

                <!-- Renewed Subscription -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card {{ $renewSubscription > 0 ? 'gradient-2' : '' }}">
                            <div class="row">
                                <div class="col-12">
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

                <!-- Customize Order (Today) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.customize.request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card {{ $ordersRequestedToday > 0 ? 'gradient-3' : '' }}">
                            <div class="row">
                                <div class="col-12">
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

                <!-- Customize Order (Next 3 Days) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.customize.request', ['filter' => 'upcoming']) }}" target="_blank">
                        <div class="card sales-card {{ $upcomingCustomizeOrders > 0 ? 'gradient-4' : '' }}">
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

   <div class="row mt-2 dashboard-section">
    <div class="col-12 mt-2">
        <h4 class="card-title-custom">Customize Order Details</h4>

        <div class="row">
            <!-- Unpaid Order -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                <a href="{{ route('flower.customize.request', ['filter' => 'unpaid']) }}" target="_blank">
                    <div class="card sales-card {{ ($unpaidCustomizeOrders ?? 0) > 0 ? 'gradient-2' : '' }}">
                        <div class="row">
                            <div class="col-12">
                                <div class="ps-4 pt-4 pe-3 pb-4">
                                    <h6 class="mb-2 tx-12">Unpaid Order</h6>
                                    <h4 class="tx-22 font-weight-semibold mb-2">
                                        {{ $unpaidCustomizeOrders ?? 0 }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Paid Order -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                <a href="{{ route('flower.customize.request', ['filter' => 'paid']) }}" target="_blank">
                    <div class="card sales-card {{ ($paidCustomizeOrders ?? 0) > 0 ? 'gradient-4' : '' }}">
                        <div class="row">
                            <div class="col-12">
                                <div class="ps-4 pt-4 pe-3 pb-4">
                                    <h6 class="mb-2 tx-12">Paid Order</h6>
                                    <h4 class="tx-22 font-weight-semibold mb-2">
                                        {{ $paidCustomizeOrders ?? 0 }}
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
            <h4 class="card-title-custom">SUBSCRIPTION STATUS</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card {{ $todayEndSubscription > 0 ? 'gradient-5' : '' }}">
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
                        <div class="card sales-card {{ $subscriptionEndFiveDays > 0 ? 'gradient-6' : '' }}">
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
                        <div class="card sales-card {{ $expiredSubscriptions > 0 ? 'gradient-7' : '' }}">
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
                        <div class="card sales-card {{ $nonAssignedRidersCount > 0 ? 'gradient-8' : '' }}">
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
            <h4 class="card-title-custom">PAUSED SUBSCRIPTION</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'todayrequest']) }}" target="_blank">
                        <div class="card sales-card {{ $todayPausedRequest > 0 ? 'gradient-9' : '' }}">
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
                        <div class="card sales-card {{ $pausedSubscriptions > 0 ? 'gradient-10' : '' }}">
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
                        <div class="card sales-card {{ $nextDayPaused > 0 ? 'gradient-11' : '' }}">
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
                        <div class="card sales-card {{ $nextDayResumed > 0 ? 'gradient-12' : '' }}">
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
            <h6 class="card-title-custom mb-4">TODAYS TRANSACTION</h6>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card {{ $totalDeliveriesTodayCount > 0 ? 'gradient-2' : '' }}">
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
                        <div class="card sales-card {{ $totalIncomeToday > 0 ? 'gradient-3' : '' }}">
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
                        <div
                            class="card sales-card position-relative {{ $todayTotalExpenditure > 0 ? 'gradient-4' : '' }}">
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
                        <div class="card sales-card {{ $activeTomorrowCount > 0 ? 'gradient-1' : '' }}">
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
            <h4 class="card-title-custom mb-4">TODAYS RIDER DETAILS</h4>
            <div class="row">
                @foreach ($ridersData as $index => $data)
                    @php
                        $hasData = $data['totalAssignedOrders'] > 0 || $data['totalDeliveredToday'] > 0;
                        $gradientClass = $hasData ? 'gradient-' . (($index % 6) + 1) : '';
                    @endphp
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}"
                            target="_blank" class="text-decoration-none">
                            <div class="card sales-card {{ $gradientClass }}">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2 tx-12">
                                                {{ $data['rider']->rider_name }}
                                            </h6>
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

    {{-- RIDER DETAILS SUMMARY --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12">
            <h4 class="card-title-custom">RIDER DETAILS</h4>
            <div class="row">
                <!-- Total Riders -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card {{ $totalRiders > 0 ? 'gradient-5' : '' }}">
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
                        <div class="card sales-card {{ $totalDeliveriesToday > 0 ? 'gradient-6' : '' }}">
                            <div class="row">
                                <div class="col-12">
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
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}"
                        target="_blank">
                        <div class="card sales-card {{ $totalDeliveriesThisMonth > 0 ? 'gradient-7' : '' }}">
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
                        <div class="card sales-card {{ $totalDeliveries > 0 ? 'gradient-8' : '' }}">
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
            <h4 class="card-title-custom">MARKETING</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.visitPlace', ['filter' => 'todayVisitPlace']) }}" target="_blank">
                        <div class="card sales-card {{ $visitPlaceCountToday > 0 ? 'gradient-9' : '' }}">
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
            <h4 class="card-title-custom">REFERAL DETAILS</h4>
            <div class="row">
                @php
                    $tc = $todayClaimed ?? 0;
                    $ta = $todayApproved ?? 0;
                    $tr = $todayRefer ?? 0;
                    $tt = $totalRefer ?? 0;
                @endphp

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'claimed', 'date' => 'today']) }}"
                        target="_blank">
                        <div class="card sales-card {{ $tc > 0 ? 'gradient-10' : '' }}">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Claimed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $tc }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'approved', 'date' => 'today']) }}"
                        target##_blank">
                        <div class="card sales-card {{ $ta > 0 ? 'gradient-11' : '' }}">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Approved</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $ta }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.referrals.index', ['date' => 'today']) }}" target="_blank">
                        <div class="card sales-card {{ $tr > 0 ? 'gradient-12' : '' }}">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $tr }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.referrals.index', ['date' => 'all']) }}" target="_blank">
                        <div class="card sales-card {{ $tt > 0 ? 'gradient-8' : '' }}">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $tt }}
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

    <!-- Live metrics poll (simple, no colors / sounds) -->
    <script>
        (function() {
            const watchers = [{
                    key: 'ordersRequestedToday',
                    elId: 'ordersRequestedTodayCount'
                },
                {
                    key: 'newUserSubscription',
                    elId: 'newUserSubscriptionCount'
                },
                {
                    key: 'renewSubscription',
                    elId: 'renewSubscriptionCount'
                },
                {
                    key: 'totalDeliveriesToday',
                    elId: 'totalDeliveriesTodayCount'
                },
            ];

            const els = {},
                prev = {};

            watchers.forEach(w => {
                const el = document.getElementById(w.elId);
                els[w.key] = el;
                if (el) {
                    const initAttr = parseInt(el.getAttribute('data-initial'), 10);
                    const parsed = Number.isFinite(initAttr) ?
                        initAttr :
                        (parseInt(el.textContent, 10) || 0);
                    prev[w.key] = parsed;
                }
            });

            async function poll() {
                try {
                    const url = `{{ route('admin.flowerDashboard.liveMetrics') }}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        },
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
