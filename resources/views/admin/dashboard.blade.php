@extends('admin.layouts.app')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <style>
      /* General Card Styling */
.card.sales-card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
    background-color: #ffffff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card.sales-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(47, 41, 47, 0.2);
}

/* Title Styling */
.card .card-title-custom {
    color: #444;
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Circle Icon Styling */
.circle-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(45deg, #6a11cb, #2575fc);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

.circle-icon:hover {
    background: linear-gradient(45deg, #6a11cb, #fc4a1a);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

.circle-icon i {
    font-size: 24px;
    color: #ffffff;
}

/* Subscription Stats */
.tx-20, .tx-22 {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.tx-12 {
    font-size: 12px;
    color: #777;
}

/* Background Gradient for Each Card */
.bg-gradient-to-r.from-blue-500.to-teal-500 {
    background: linear-gradient(45deg, #2196f3, #64b5f6);
}

.bg-gradient-to-r.from-pink-500.to-purple-600 {
    background: linear-gradient(45deg, #f06292, #9c27b0);
}

.bg-gradient-to-r.from-green-400.to-teal-500 {
    background: linear-gradient(45deg, #66bb6a, #26a69a);
}

.bg-gradient-to-r.from-orange-400.to-red-500 {
    background: linear-gradient(45deg, #ff7043, #e57373);
}

.bg-gradient-to-r.from-teal-500.to-blue-600 {
    background: linear-gradient(45deg, #00897b, #039be5);
}

.bg-gradient-to-r.from-red-600.to-orange-500 {
    background: linear-gradient(45deg, #e53935, #ff7043);
}

/* Hover Effect on Card */
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
    .card.sales-card {
        margin-bottom: 20px;
    }
    .circle-icon {
        width: 40px;
        height: 40px;
    }
    .tx-20, .tx-22 {
        font-size: 18px;
    }
    .tx-12 {
        font-size: 10px;
    }
}

        .card-title-custom {
            margin-bottom: 0.75rem;
        }

        .card-title-custom {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            margin-left: 8px;
        }

        .card-title-custom:before {
            content: "";
            width: 3px;
            height: 16px;
            background: var(--primary-bg-color);
            position: absolute;
            left: 13px;
            display: block;
            top: 1px;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between"  id="welcomeSection">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sales</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- row -->
    <div class="row" id="welcomeSections">
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
    </div>
    
    
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom">Flower Subscription</h4>
            <div class="row">
                <!-- New Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Subscription</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $newUserSubscription }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-blue-500 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-gift tx-16 text-white"></i> <!-- New Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Renewed Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Renewed Subscription</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $renewSubscription }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-pink-500 to-purple-600 text-center align-self-center overflow-hidden">
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
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $ordersRequestedToday }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-green-400 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-cogs tx-16 text-white"></i> <!-- Customize Order Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'rider']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12"> Non Assign Rider</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $nonAssignedRidersCount }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-red-400 to-teal-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-users text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Paused Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Paused Subscription</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $pausedSubscriptions }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-orange-400 to-red-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-pause tx-16 text-white"></i> <!-- Paused Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Active Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'active']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Active Subscription</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $activeSubscriptions }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-teal-500 to-blue-600 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-play tx-16 text-white"></i> <!-- Active Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription Ends Today</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $todayEndSubscription }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-teal-500 to-blue-600 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-exclamation-triangle tx-16 text-white"></i><!-- Active Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Expired Subscription -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.orders.index', ['filter' => 'expired']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Ended Subscription</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">{{ $expiredSubscriptions }}</h4>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="circle-icon bg-gradient-to-r from-red-600 to-orange-500 text-center align-self-center overflow-hidden">
                                        <i class="fa fa-skull-crossbones tx-16 text-white"></i>
                                        <!-- Expired Subscription Icon -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- row closed -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom mb-4">Individual Rider Details</h4>
    
            <div class="row">
                @foreach ($ridersData as $data)
                    <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12 mb-4">
                        <a href="{{ route('admin.manageRiderDetails') }}" target="_blank" class="text-decoration-none">
                            <div class="sales-card" style="border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
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
                                    <div class="col-4 d-flex justify-content-center align-items-center">
                                        <div class="circle-icon bg-white text-primary text-center" style="border-radius: 50%; width: 60px; height: 60px;">
                                            <i class="fas fa-truck-loading fa-2x"></i>
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
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom"> Rider Details</h4>

            <div class="row">
                <!-- Total Riders -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card bg-gradient-primary text-white">
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
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'todaydelivery']) }}" target="_blank">
                        <div class="card sales-card bg-gradient-info text-white">
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
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}" target="_blank">
                        <div class="card sales-card bg-gradient-success text-white">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Delivery in Month</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalDeliveriesThisMonth }}</h4>
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
                        <div class="card sales-card bg-gradient-secondary text-white">
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
  
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom">Expenses Details in a Day</h4>
    
            <div class="row">
    
                <!-- Total Expense Today -->
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageflowerpickupdetails', ['filter' => 'todayexpenses']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Vendor Expense Today</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹ {{ $totalExpensesday }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="circle-icon bg-gradient-primary text-white text-center align-self-center">
                                        <i class="fas fa-wallet fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Total Paid Today -->
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageflowerpickupdetails', ['filter' => 'todaypaidpickup']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Paid To Rider Today</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹ {{ $totalPaidExpensesday }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="circle-icon bg-gradient-success text-white text-center align-self-center">
                                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Total Unpaid Today -->
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageflowerpickupdetails', ['filter' => 'todaypendingpickup']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Unpaid In Today</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹ {{ $totalUnpaidExpensesday }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="circle-icon bg-gradient-warning text-white text-center align-self-center">
                                        <i class="fas fa-credit-card fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
            </div>
        </div>
    </div>
    
    <!-- row closed -->
    <!---Expenses in month-->
    <!-- row closed -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom"> Expenses Details in this Month</h4>

            <div class="row">
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">

                    <a href="{{ route('admin.manageflowerpickupdetails', ['filter' => 'monthlyexpenses']) }}"
                        target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Vendor Expenses In Month </h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2"> ₹ {{ $totalAmountThisMonth }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-follow tx-16 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageflowerpickupdetails', ['filter' => 'monthlypaidpickup']) }}"
                        target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Paid To Rider In Month</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹ {{ $totalPaidThisMonth }}
                                                </h4>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.manageflowerpickupdetails', ['filter' => 'monthlypendingpickup']) }}"
                        target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12"> Unpaid in this Month </h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹ {{ $totalUnpaidThisMonth }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-follow tx-16 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


            </div>
        </div>
    </div>
    <!-- row closed -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom">Other Details</h4>

            <div class="row">
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Expenses</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹
                                                    {{ number_format($totalFlowerPickupPrice, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-follow tx-16 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Income (Subscription)</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹
                                                    {{ number_format($totalPriceWithoutRequestId ?? 0, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-secondary-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-following tx-16 text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Income (Customized Order)</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">₹
                                                    {{ number_format($totalPriceWithRequestId ?? 0, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-warning-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


            </div>
        </div>
    </div>
    {{-- product details --}}

    {{-- <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

            <h4 class="card-title-custom">Product Subscription</h4>

            <div class="row">

                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.product.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">New Subscription</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $newUserSubscriptionProduct }}
                                                </h4>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.product.index', ['filter' => 'renewed']) }}" target="_blank">

                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Renewed Subscription</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $renewSubscriptionProduct }}</h4>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">

                    <a href="{{ route('product-request') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Customize Order</h6>
                                        </div>
                                        
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $productRequestedToday }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-follow tx-16 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                
                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.product.index', ['filter' => 'active']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">

                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Active Subscription</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $activeSubscriptionsProduct }}
                                                </h4>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-secondary-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-following tx-16 text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-4 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('admin.product.index', ['filter' => 'expired']) }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">

                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Expired Subscription</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-22 font-weight-semibold mb-2">{{ $expiredSubscriptionsProduct }}
                                                </h4>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-warning-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


            </div>

        </div>


    </div> --}}

    <!-- row closed -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom">Podcast Details</h4>

            <div class="row">
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('podcastReport') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Total Completed Script</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalCompletedScripts }}
                                                </h4>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('podcastReport') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Total Completed Recording</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalCompletedRecoding }}
                                                </h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class="text-success font-weight-semibold"> +427</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('podcastReport') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Total Completed Editing</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalCompletedEditing }}
                                                </h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class="text-success font-weight-semibold"> +427</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ route('podcastReport') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Total Published Podcast</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalActivePodcasts }}
                                                </h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class="text-success font-weight-semibold"> +427</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- row start -->
    <!---pandit details-->

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4 class="card-title-custom">Pandit Details</h4>

            <div class="row">

                <div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ url('admin/manage-pandits') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12 ">Total Pandits</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalPandit }}</h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class="text-success font-weight-semibold"> +427</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ url('admin/manage-pandits') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Pending Pandits</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $pendingPandit }}</h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>
															<span class="font-weight-semibold text-danger"> -453</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-follow tx-16 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ url('admin/manage-orders') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Pandit Bookings</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalOrder }}</h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class=" text-success font-weight-semibold"> +788</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-secondary-transparent text-center align-self-center overflow-hidden">
                                        <i class="si si-user-following tx-16 text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
                    <a href="{{ url('admin/manage-users') }}" target="_blank">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">

                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">Total Users</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-22 font-weight-semibold mb-2">{{ $totalUser }}</h4>
                                            </div>
                                            {{-- <p class="mb-0 tx-12  text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>
															<span class="text-danger font-weight-semibold"> -693</span>
														</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="circle-icon bg-warning-transparent text-center align-self-center overflow-hidden">
                                        <i class="fa fa-user tx-16 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- row  -->
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pandit List</h4>
                </div>
                <div class="card-body pt-0 example1-table">
                    <div class="table-responsive">
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Slno</th>
                                    <th class="border-bottom-0">Name</th>
                                    <th class="border-bottom-0">Registered Date</th>
                                    <th class="border-bottom-0">Mobile No</th>
                                    <th class="border-bottom-0">Blood Group</th>
                                    <th class="border-bottom-0">Application Status</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pandit_profiles as $index => $profile)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td class="tb-col">
                                            <div class="media-group">
                                                <div class="media media-md media-middle media-circle">
                                                    <img src="{{ asset($profile->profile_photo) }}" alt="user">
                                                </div>
                                                <div class="media-text" style="color: blue">
                                                    <a style="color: blue"
                                                        href="{{ url('admin/pandit-profile/' . $profile->id) }}"
                                                        class="title">{{ $profile->name }}</a>
                                                    <span class="small text">{{ $profile->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($profile->created_at)->format('Y-m-d') }}</td>
                                        <td>{{ $profile->whatsappno }}</td>
                                        <td>{{ $profile->bloodgroup }}</td>
                                        <td>{{ $profile->pandit_status }}</td>
                                        <td>
                                            @if ($profile->pandit_status == 'accepted')
                                                <form action="{{ route('rejectPandit', $profile->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Reject</button>
                                                </form>
                                            @elseif($profile->pandit_status == 'rejected')
                                                <form action="{{ route('acceptPandit', $profile->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">Accept</button>
                                                </form>
                                            @elseif($profile->pandit_status == 'pending')
                                                <form action="{{ route('acceptPandit', $profile->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">Accept</button>
                                                </form>
                                                <form action="{{ route('rejectPandit', $profile->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Reject</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /row closed -->
@endsection

@section('scripts')
    <!-- Internal Chart.Bundle js-->
    <script src="{{ asset('assets/plugins/chartjs/Chart.bundle.min.js') }}"></script>

    <!-- Moment js -->
    <script src="{{ asset('assets/plugins/raphael/raphael.min.js') }}"></script>

    <!-- INTERNAL Apexchart js -->
    <script src="{{ asset('assets/js/apexcharts.js') }}"></script>

    <!--Internal Sparkline js -->
    <script src="{{ asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

    <!--Internal  index js -->
    <script src="{{ asset('assets/js/index.js') }}"></script>

    <!-- Chart-circle js -->
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script>

    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

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
@endsection
