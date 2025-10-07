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
        /* ========= Colorful pulse glows (single, consolidated) ========= */
        .pulse-glow--cyan {
            animation: pulseGlowCyan 1.2s ease-in-out 0s 6;
            border-color: rgba(6, 182, 212, .4) !important;
        }

        .pulse-glow--emerald {
            animation: pulseGlowEmerald 1.2s ease-in-out 0s 6;
            border-color: rgba(16, 185, 129, .4) !important;
        }

        .pulse-glow--fuchsia {
            animation: pulseGlowFuchsia 1.2s ease-in-out 0s 6;
            border-color: rgba(217, 70, 239, .4) !important;
        }

        .pulse-glow--amber {
            animation: pulseGlowAmber 1.2s ease-in-out 0s 6;
            border-color: rgba(245, 158, 11, .4) !important;
        }

        @keyframes pulseGlowCyan {
            0% {
                box-shadow: 0 0 0 rgba(6, 182, 212, 0);
            }

            50% {
                box-shadow: 0 0 32px rgba(6, 182, 212, .7);
            }

            100% {
                box-shadow: 0 0 0 rgba(6, 182, 212, 0);
            }
        }

        @keyframes pulseGlowEmerald {
            0% {
                box-shadow: 0 0 0 rgba(16, 185, 129, 0);
            }

            50% {
                box-shadow: 0 0 32px rgba(16, 185, 129, .7);
            }

            100% {
                box-shadow: 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        @keyframes pulseGlowFuchsia {
            0% {
                box-shadow: 0 0 0 rgba(217, 70, 239, 0);
            }

            50% {
                box-shadow: 0 0 32px rgba(217, 70, 239, .7);
            }

            100% {
                box-shadow: 0 0 0 rgba(217, 70, 239, 0);
            }
        }

        @keyframes pulseGlowAmber {
            0% {
                box-shadow: 0 0 0 rgba(245, 158, 11, 0);
            }

            50% {
                box-shadow: 0 0 32px rgba(245, 158, 11, .7);
            }

            100% {
                box-shadow: 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        /* Tiny floating pill to unlock audio (auto-hides on first click/keypress) */
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
    </style>
@endsection

@section('content')
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h6 class="card-title-custom mb-4" style="font-size: 14px">Todays Transaction</h6>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Active Subscription/Total Delivery</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeSubscriptions }}/{{ $totalDeliveriesTodayCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Today Total Income -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Total Income</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            ₹{{ number_format($totalIncomeToday, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Today Total Expenditure -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                        <div class="row">
                            <div class="col-8">
                                <div class="ps-4 pt-4 pe-3 pb-4">
                                    <h6 class="mb-2 tx-12">Today Total Expenditure</h6>
                                    <h4 class="tx-20 font-weight-semibold mb-2">
                                        ₹{{ number_format($todayTotalExpenditure, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tomorrow Active Order -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tomorrowOrder']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}" target="_blank"
                            class="text-decoration-none">
                            <div class="sales-card"
                                style="border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid rgb(186,185,185);">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2 text-dark">{{ $data['rider']->rider_name }}</h6>
                                            <div class="d-flex flex-column">
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-2">
                                                    Delivery Assigned: {{ $data['totalAssignedOrders'] }}
                                                </h4>
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-0">
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

    <!-- row closed -->
    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom" style="font-size: 14px"> Rider Details</h4>

            <div class="row">
                <!-- Total Riders -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card bg-gradient-primary text-white"
                            style="border: 1px solid rgb(186, 185, 185);">
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

                <!-- Total Delivery Today (WATCH) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card bg-gradient-info text-white watch-card" data-color="emerald"
                            style="border: 1px solid rgb(186, 185, 185);">
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

                <!-- Total Delivery in Month -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}"
                        target="_blank">
                        <div class="card sales-card bg-gradient-success text-white"
                            style="border: 1px solid rgb(186, 185, 185);">
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

                <!-- Total Delivery -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory') }}" target="_blank">
                        <div class="card sales-card bg-gradient-secondary text-white"
                            style="border: 1px solid rgb(186, 185, 185);">
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

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Todays Order</h4>
            <div class="row">
                <!-- New Subscription (WATCH) -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card watch-card" data-color="amber"
                            style="border: 1px solid rgb(186, 185, 185);">
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
                        <div class="card sales-card watch-card" data-color="fuchsia"
                            style="border: 1px solid rgb(186, 185, 185);">
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
                    <a href="{{ route('flower-request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card watch-card" data-color="cyan"
                            style="border: 1px solid rgb(186, 185, 185);">
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
                    <a href="{{ route('flower-request', ['filter' => 'upcoming']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Subscription Status</h4>
            <div class="row">

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12"> New Order Assign Rider</h6>
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

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Paused Subscription</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'todayrequest']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Paused Request</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $todayPausedRequest }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Paused Subscription</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $pausedSubscriptions }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tomorrow']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Pause</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $nextDayPaused }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'nextdayresumed']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Resumed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $nextDayResumed }}</h4>
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
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Marketing</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.visitPlace', ['filter' => 'todayVisitPlace']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Referal Details</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'claimed', 'date' => 'today']) }}"
                        target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'approved', 'date' => 'today']) }}"
                        target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
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
    <script> feather.replace(); </script>

    <script>
        // Hide welcome sections if present
        setTimeout(() => { const el = document.getElementById('welcomeSection');  if (el) el.style.display = 'none'; }, 5000);
        setTimeout(() => { const el = document.getElementById('welcomeSections'); if (el) el.style.display = 'none'; }, 5000);

        // Single DateTime updater
        function updateDateTime() {
            const now   = new Date();
            const date1 = document.getElementById('todayDate');
            const time1 = document.getElementById('liveTime');
            const date2 = document.getElementById('current-date');
            const time2 = document.getElementById('current-time');

            if (date1) date1.textContent = now.toLocaleDateString(undefined, {year:'numeric', month:'long', day:'numeric'});
            if (time1) time1.textContent = now.toLocaleTimeString();
            if (date2) date2.textContent = now.toLocaleDateString(undefined, {weekday:'long', year:'numeric', month:'long', day:'numeric'});
            if (time2) time2.textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <!-- Live metrics poll + colorful glow + initial fire + robust sound unlock -->
    <script>
    (function() {
        // Map element IDs -> server key + glow color
        const watchers = [
            { key: 'ordersRequestedToday', elId: 'ordersRequestedTodayCount', color: 'cyan'    }, // main
            { key: 'newUserSubscription',  elId: 'newUserSubscriptionCount',  color: 'amber'   },
            { key: 'renewSubscription',    elId: 'renewSubscriptionCount',    color: 'fuchsia' },
            { key: 'totalDeliveriesToday', elId: 'totalDeliveriesTodayCount', color: 'emerald' },
        ];

        const els  = {};
        const prev = {};
        watchers.forEach(w => {
            els[w.key] = document.getElementById(w.elId);
            if (els[w.key]) {
                const initAttr = parseInt(els[w.key].getAttribute('data-initial'), 10);
                const parsed   = Number.isFinite(initAttr) ? initAttr : (parseInt(els[w.key].textContent, 10) || 0);
                prev[w.key]    = parsed;
            }
        });

        function findCard(el) {
            return el ? (el.closest('.watch-card') || el.closest('.card')) : null;
        }
        function glow(el, color) {
            const card = findCard(el);
            if (!card) return;
            const cls = `pulse-glow--${color}`;
            card.classList.add(cls);
            setTimeout(() => card.classList.remove(cls), 6000);
        }

        // ---- Sound unlock (one global AudioContext reused) + QUEUE ----
        let audioEnabled = false;
        let audioCtx = null;
        const beepQueue = []; // store [ms, freq] until unlocked

        function ensureAudio() {
            try {
                if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                if (audioCtx && audioCtx.state === 'suspended') {
                    audioCtx.resume().then(() => {
                        audioEnabled = true;
                        flushBeepQueue();
                    });
                } else {
                    audioEnabled = true;
                    flushBeepQueue();
                }
            } catch (e) { /* ignore */ }
        }
        function flushBeepQueue() {
            while (audioEnabled && beepQueue.length) {
                const [ms, freq] = beepQueue.shift();
                _beep(ms, freq);
            }
        }
        function _beep(ms=230, freq=880) {
            try {
                const osc  = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.value = 0.0001;
                osc.connect(gain).connect(audioCtx.destination);
                osc.start();
                gain.gain.exponentialRampToValueAtTime(0.06, audioCtx.currentTime + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + (ms/1000));
                setTimeout(() => { osc.stop(); }, ms + 60);
            } catch(e) { /* ignore */ }
        }
        function beep(ms=230, freq=880) {
            if (!audioEnabled) { beepQueue.push([ms, freq]); return; }
            _beep(ms, freq);
        }

        function setupUnlockUI() {
            const pill = document.getElementById('sound-unlock');
            const maybeHide = () => pill && pill.classList.add('hidden');

            // if audio enabled already, hide pill
            if (audioEnabled) { maybeHide(); return; }

            if (pill) pill.classList.remove('hidden');

            const unlock = () => {
                ensureAudio();
                maybeHide();
                window.removeEventListener('click', unlock, true);
                window.removeEventListener('keydown', unlock, true);
                pill && pill.removeEventListener('click', unlock, true);
            };
            window.addEventListener('click', unlock, true);
            window.addEventListener('keydown', unlock, true);
            pill && pill.addEventListener('click', unlock, true);
        }

        // ---- Initial glow/sound if values already > 0 on page load ----
        function initialKick() {
            watchers.forEach(w => {
                const el = els[w.key];
                if (!el) return;
                const val = prev[w.key] ?? 0;
                if (val > 0) {
                    glow(el, w.color);
                    // More attention for Customize Orders
                    if (w.key === 'ordersRequestedToday') {
                        beep(260, 1200);
                        setTimeout(() => beep(220, 900), 160);
                    } else {
                        beep(200, 880);
                    }
                }
            });
        }

        async function poll() {
            try {
                const url = `{{ route('admin.flowerDashboard.liveMetrics') }}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
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

                        // Highlight + sound on increases
                        if (newVal > oldVal) {
                            glow(el, w.color);
                            if (w.key === 'ordersRequestedToday') {
                                beep(260, 1200);
                                setTimeout(() => beep(220, 900), 160);
                            } else {
                                beep(200, 880);
                            }
                        }
                        prev[w.key] = newVal;
                    }
                });
            } catch (e) {
                // optional console.warn(e);
            }
        }

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') poll();
        });

        document.addEventListener('DOMContentLoaded', () => {
            setupUnlockUI(); // show 🔔 pill until user interacts
            initialKick();   // <<< glow + (queued) sound if counts already > 0
            poll();          // initial sync
            setInterval(poll, 5000); // poll every 5s
        });
    })();
    </script>
@endsection
