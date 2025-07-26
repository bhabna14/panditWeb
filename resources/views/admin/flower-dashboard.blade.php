@extends('admin.layouts.app')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/flower-dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    {{-- <div class="breadcrumb-header justify-content-between" id="welcomeSection">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">FLOWER DASHBOARD</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sales</li>
            </ol>
        </div>
    </div> --}}

    <!-- row -->
    {{-- <div class="row" id="welcomeSections">
        <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-9 col-lg-7 col-md-12 col-sm-12">
                            <div class="text-justified align-items-center">
                                <h3 class="text-dark font-weight-semibold mb-2 mt-0">
                                    Hi, Welcome Back
                                    <span class="text-primary">{{ Auth::guard('admins')->user()->name }}!</span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
            <h6 class="card-title-custom mb-4" style="font-size: 14px">Todays Transaction</h6>
            <div class="row">

                {{-- <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <div class="card sales-card">
                        <div class="row">
                            <div class="col-8">
                                <div class="ps-4 pt-4 pe-3 pb-4">
                                    <h4 class="tx-20 font-weight-semibold mb-2" id="todayDate"></h4>
                                    <h5 class="tx-16 font-weight-semibold mb-0" id="liveTime"></h5>
                                </div>
                            </div>
                            <div class="col-4 d-flex justify-content-center align-items-center">
                                <div
                                    class="circle-icon bg-gradient-to-r from-teal-500 to-blue-600 text-center align-self-center overflow-hidden">
                                    <i class="fa fa-clock tx-16 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}


                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Today Active Subscription/Total Delivery</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeSubscriptions }}/{{ $totalDeliveriesTodayCount }}</h4>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="circle-icon bg-gradient-to-r from-teal-500 to-blue-600 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-white"></i> <!-- Active Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- New Subscription -->
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card " style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Total Income</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            ₹{{ number_format($totalIncomeToday, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-blue-500 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fas fa-rupee-sign tx-16 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                        <div class="row">
                            <div class="col-8">
                                <div class="ps-4 pt-4 pe-3 pb-4">
                                    <h6 class="mb-2 tx-12">Today Total Expenditure</h6>
                                    <h4 class="tx-20 font-weight-semibold mb-2">
                                        ₹{{ number_format($todayTotalExpenditure, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="circle-icon bg-gradient-to-r from-pink-500 to-purple-600 text-center align-self-center overflow-hidden">
                                    <i class="fas fa-money-bill-wave tx-16 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row card sales-card mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom mb-4" style="font-size: 14px">Individual Rider Details</h4>
            <div class="row">
                @foreach ($ridersData as $data)
                    <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12 mb-4">
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}" target="_blank"
                            class="text-decoration-none">
                            <div class="sales-card" style="border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);border: 1px solid rgb(186, 185, 185);">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2 text-dark">{{ $data['rider']->rider_name }}</h6>
                                            <div class="d-flex flex-column">
                                                <!-- Total Delivery with icon -->
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-2">
                                                    <i class="fas fa-truck-loading me-2 text-dark"></i>
                                                    Total Delivery: {{ $data['totalAssignedOrders'] }}
                                                </h4>

                                                <!-- Total Delivered with icon -->
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-0">
                                                    <i class="fas fa-check-circle me-2 text-dark"></i>
                                                    Total Delivered: {{ $data['totalDeliveredToday'] }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-4 d-flex justify-content-center align-items-center">
                                        <div class="circle-icon bg-white text-primary text-center"
                                            style="border-radius: 50%; width: 60px; height: 60px;">
                                            <i class="fas fa-truck-loading fa-2x"></i>
                                        </div>
                                    </div> --}}
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
                        <div class="card sales-card bg-gradient-primary text-white" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Riders</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalRiders }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-white text-primary text-center">
                                        <i class="fa fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery Today -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card bg-gradient-info text-white" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery Today</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalDeliveriesToday }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-white text-info text-center">
                                        <i class="fa fa-calendar-check fa-2x"></i>
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
                        <div class="card sales-card bg-gradient-success text-white" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Delivery in Month</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalDeliveriesThisMonth }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-white text-success text-center">
                                        <i class="fa fa-calendar-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.managedeliveryhistory') }}" target="_blank">
                        <div class="card sales-card bg-gradient-secondary text-white" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Sub Total Delivery</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalDeliveries }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-white text-secondary text-center">
                                        <i class="fa fa-box fa-2x"></i>
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
            <h4 class="card-title-custom" style="font-size: 14px">Todays Order Block</h4>
            <div class="row">
                <!-- New Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Subscription</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $newUserSubscription }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-blue-500 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-gift tx-16 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Renewed Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Renewed Subscription</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $renewSubscription }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-pink-500 to-purple-600 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-recycle tx-16 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('flower-request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $ordersRequestedToday }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-green-400 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-cogs tx-16 text-white"></i> <!-- Customize Order Icon -->
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
                                        <h6 class="mb-2 tx-12">Subscription Ends Today</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $todayEndSubscription }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-teal-500 to-blue-600 text-center align-self-center overflow-hidden">
                                        <i
                                            class="fa fa-exclamation-triangle tx-16 text-white"></i><!-- Active Subscription Icon -->
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
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $subscriptionEndFiveDays }}</h4>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="circle-icon bg-gradient-to-r from-orange-400 to-red-500 text-center align-self-center overflow-hidden">
                                        <i class="fas fa-calendar-check tx-16 text-white"></i>
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
                                <div>
                                    <div
                                        class="circle-icon bg-gradient-to-r from-red-600 to-orange-500 text-center align-self-center overflow-hidden">
                                        <img src="{{ asset('assets/img/s.png') }}" alt="Image">

                                        <!-- Expired Subscription Icon -->
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
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-red-400 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-users text-white"></i>
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
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-orange-400 to-red-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-pause tx-16 text-white"></i> <!-- Paused Subscription Icon -->
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
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-orange-400 to-red-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-pause tx-16 text-white"></i> <!-- Paused Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tommorow']) }}" target="_blank">
                        <div class="card sales-card" style="border: 1px solid rgb(186, 185, 185);">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Pause</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $nextDayPaused }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-green-400 to-red-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-pause tx-16 text-white"></i> <!-- Paused Subscription Icon -->
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
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-gradient-to-r from-green-400 to-red-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-pause tx-16 text-white"></i> <!-- Paused Subscription Icon -->
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
    <!-- Include the notification sound script in your script.blade.php -->

    <script>
        // Hide the section after 5 seconds (5000 milliseconds)
        setTimeout(() => {
            const welcomeSection = document.getElementById('welcomeSection');
            if (welcomeSection) {
                welcomeSection.style.display = 'none';
            }
        }, 5000);
    </script>

    <script>
        // Hide the section after 5 seconds (5000 milliseconds)
        setTimeout(() => {
            const welcomeSection = document.getElementById('welcomeSections');
            if (welcomeSection) {
                welcomeSection.style.display = 'none';
            }
        }, 5000);
    </script>

    <script>
        function updateDateTime() {
            const now = new Date();
            const dateOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('todayDate').textContent = now.toLocaleDateString(undefined, dateOptions);
            document.getElementById('liveTime').textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <script>
        function updateDateTime() {
            const dateElem = document.getElementById('current-date');
            const timeElem = document.getElementById('current-time');
            const now = new Date();

            // Format date: e.g., Monday, 10 June 2024
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            dateElem.textContent = now.toLocaleDateString(undefined, options);

            // Format time: e.g., 10:15:30 AM
            timeElem.textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
@endsection
