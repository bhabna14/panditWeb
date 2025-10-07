@extends('admin.layouts.apps')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <!-- Feather Icons -->
    <link href="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.css" rel="stylesheet">

    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/flower-dashboard.css') }}" rel="stylesheet">

    <style>
        /* ========= Colorful pulse glows ========= */
        .pulse-glow--cyan {
            animation: pulseGlowCyan 1.2s ease-in-out 0s 6;
        }

        .pulse-glow--emerald {
            animation: pulseGlowEmerald 1.2s ease-in-out 0s 6;
        }

        .pulse-glow--fuchsia {
            animation: pulseGlowFuchsia 1.2s ease-in-out 0s 6;
        }

        .pulse-glow--amber {
            animation: pulseGlowAmber 1.2s ease-in-out 0s 6;
        }

        @keyframes pulseGlowCyan {
            0% {
                box-shadow: 0 0 0 rgba(6, 182, 212, 0);
            }

            50% {
                box-shadow: 0 0 32px rgba(6, 182, 212, 0.7);
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
                box-shadow: 0 0 32px rgba(16, 185, 129, 0.7);
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
                box-shadow: 0 0 32px rgba(217, 70, 239, 0.7);
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
                box-shadow: 0 0 32px rgba(245, 158, 11, 0.7);
            }

            100% {
                box-shadow: 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        /* optional: subtle border color shift during glow */
        .pulse-glow--cyan {
            border-color: rgba(6, 182, 212, 0.4) !important;
        }

        .pulse-glow--emerald {
            border-color: rgba(16, 185, 129, 0.4) !important;
        }

        .pulse-glow--fuchsia {
            border-color: rgba(217, 70, 239, 0.4) !important;
        }

        .pulse-glow--amber {
            border-color: rgba(245, 158, 11, 0.4) !important;
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
@endsection

@section('scripts')
    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    <script>
        // Hide the section(s) after 5 seconds if they exist
        setTimeout(() => (document.getElementById('welcomeSection') || {}).style && (document.getElementById(
            'welcomeSection').style.display = 'none'), 5000);
        setTimeout(() => (document.getElementById('welcomeSections') || {}).style && (document.getElementById(
            'welcomeSections').style.display = 'none'), 5000);

        // Single DateTime updater (merged duplicates)
        function updateDateTime() {
            const now = new Date();
            const dateFmt = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
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
            if (date2) date2.textContent = now.toLocaleDateString(undefined, dateFmt);
            if (time2) time2.textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script>
        feather.replace();
    </script>

    <!-- Live metrics poll + colorful glow + beep -->
    <script>
        (function() {
            // Map element IDs -> server key + color class
            const watchers = [{
                    key: 'ordersRequestedToday',
                    elId: 'ordersRequestedTodayCount',
                    color: 'cyan'
                }, // main
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

            const els = {};
            const prev = {};
            watchers.forEach(w => {
                els[w.key] = document.getElementById(w.elId);
                if (els[w.key]) {
                    const init = parseInt(els[w.key].getAttribute('data-initial'), 10);
                    prev[w.key] = Number.isFinite(init) ? init : (parseInt(els[w.key].textContent, 10) || 0);
                }
            });

            function findCard(el) {
                return el ? (el.closest('.watch-card') || el.closest('.card')) : null;
            }

            // Colorful glow
            function glow(el, color) {
                const card = findCard(el);
                if (!card) return;
                const cls = `pulse-glow--${color}`;
                card.classList.add(cls);
                setTimeout(() => card.classList.remove(cls), 6000);
            }

            // Soft pleasant beep (no asset)
            function beep(ms = 230, freq = 880) {
                try {
                    const ctx = new(window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    gain.gain.value = 0.0001;
                    osc.connect(gain).connect(ctx.destination);
                    osc.start();
                    gain.gain.exponentialRampToValueAtTime(0.06, ctx.currentTime + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + (ms / 1000));
                    setTimeout(() => {
                        osc.stop();
                        ctx.close();
                    }, ms + 50);
                } catch (e) {
                    /* ignore */ }
            }

            async function poll() {
                try {
                    const url = `{{ route('flowerDashboard.liveMetrics') }}`;
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

                            // Only ring/flash on increases
                            if (newVal > oldVal) {
                                glow(el, w.color);

                                if (w.key === 'ordersRequestedToday') {
                                    // Make Customize Orders more noticeable
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
                // Optional: when user returns to the tab, refresh once
                if (document.visibilityState === 'visible') poll();
            });

            // Start
            document.addEventListener('DOMContentLoaded', () => {
                poll(); // sync immediately
                setInterval(poll, 15000); // every 15s; adjust if you want
            });
        })();
    </script>
@endsection
