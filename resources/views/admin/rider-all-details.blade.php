@extends('admin.layouts.app')

@section('styles')
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/SmartPhoto-master/smartphoto.css')}}" rel="stylesheet">
 <!-- Data table css -->
 <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
 <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
 <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

<style>
    .custom-size-icon { font-size: 26px; margin-bottom: 10px; }
</style>
@endsection

@section('content')

<div class="breadcrumb-header justify-content-between">
    <div class="left-content d-flex align-items-center flex-nowrap">
        <a class="btn btn-primary me-3" href="{{ url('admin/manage-rider-details') }}">BACK</a>
        <span class="main-content-title" style="margin-top: 36px">RIDER PROFILE</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Rider Profile</li>
        </ol>
    </div>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <!-- Rider Image and Details -->
                <div class="d-flex align-items-center">
                    <!-- Rider Image -->
                    <div class="profile-image-container me-4 mb-3 mb-md-0">
                        <img 
                            class="profile-image br-5" 
                            src="{{ $rider->rider_img ? Storage::url($rider->rider_img) : asset('default-user.png') }}" 
                            alt="Rider Image"
                            style="width: 100px; height: 100px; object-fit: cover;"
                        >
                    </div>

                    <!-- Rider Details -->
                    <div class="profile-details">
                        <h4 class="mb-1">{{ $rider->rider_name }}</h4>
                        <p class="mb-0 text-muted">
                            <i class="fa fa-phone me-2"></i>Phone: {{ $rider->phone_number }}
                        </p>
                    </div>
                </div>

                <!-- Rider Statistics -->
                <div class="d-flex flex-wrap align-items-center">
                    <!-- Total Orders -->
                    <div class="prof-details text-center me-4 mb-3 mb-md-0">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-shopping-cart text-primary custom-size-icon"></i>
                                <h6 class="mb-1">Total Orders</h6>
                                <p class="mb-0 font-weight-bold">{{ $totalOrders }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Orders -->
                    <div class="prof-details text-center me-4 mb-3 mb-md-0">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-calendar-alt text-warning custom-size-icon"></i>
                                <h6 class="mb-1">Current Monthly</h6>
                                <p class="mb-0 font-weight-bold">{{ $monthlyOrders }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Refer -->
                    <div class="prof-details text-center me-4 mb-3 mb-md-0">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-rupee-sign text-success custom-size-icon"></i>
                                <h6 class="mb-1">Total Refer</h6>
                                <p class="mb-0 font-weight-bold">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="prof-details text-center me-4 mb-3 mb-md-0">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-dollar-sign text-info custom-size-icon"></i>
                                <h6 class="mb-1">Total Price</h6>
                                <p class="mb-0 font-weight-bold">₹{{ number_format($total_price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- // total total_paid --}}
                    <div class="prof-details text-center me-4 mb-3 mb-md-0">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-dollar-sign text-info custom-size-icon"></i>
                                <h6 class="mb-1">Total Paid</h6>
                                <p class="mb-0 font-weight-bold">₹{{ number_format($total_paid, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    {{-- // total total_unpaid --}}
                    <div class="prof-details text-center me-4 mb-3 mb-md-0">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-dollar-sign text-info custom-size-icon"></i>
                                <h6 class="mb-1">Total Unpaid</h6>
                                <p class="mb-0 font-weight-bold">₹{{ number_format($total_unpaid, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-0">
                <div class="profile-tab tab-menu-heading border-bottom-0">
                    <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0">
                       
                        <a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#deliveryhistory">Delivery History</a>
                        <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#pickuphistory">Pickup History</a>

                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


	<!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="custom-card main-content-body-profile">
                <div class="tab-content">
                    <div class="main-content-body  tab-pane border-top-0 active" id="deliveryhistory">
                        <div class="border-0">
                            <div class="main-content-body main-content-body-profile">
                                <div class="main-profile-body p-0">
                                    <div class="row row-sm">
                                        <div class="table-responsive  export-table">
                                            <table id="file-datatable" class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>User Name</th>
                                                        <th>Product Name</th>
                                                        <th>Payment Details</th>
                                                        <th>Address</th>
                                                        <th>Delivery Status</th>
                                                        <th>Location</th>
                                                        <th>Delivered At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($deliveryHistory as $history)
                                                        <tr>
                                                            <td>{{ $history->order->order_id }}</td>
                                                            <td>{{ $history->order->user->name ?? 'N/A' }}</td>
                                                            <td>{{ $history->order->flowerProduct->name ?? 'N/A' }}</td>
                                                            <td>
                                                                @foreach($history->order->flowerPayments as $payment)
                                                                    ₹{{ $payment->paid_amount }}<br>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                {{ $history->order->address->apartment_flat_plot ?? 'N/A' }},
                                                                {{ $history->order->address->localityDetails->locality_name ?? 'N/A' }}
                                                            </td>
                                                            <td>{{ $history->delivery_status }}</td>
                                                            <td>{{ $history->longitude }}, {{ $history->latitude }}</td>
                                                            <td>{{ $history->created_at->format('d-m-Y H:i:s') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-profile-body -->
                            </div>
                        </div>
                    </div>

                    <div class="main-content-body  tab-pane border-top-0" id="pickuphistory">
                        <div class="border-0">
                            <div class="main-content-body main-content-body-profile">
                                <div class="main-profile-body p-0">
                                    <div class="row row-sm">
                                        <div class="table-responsive  export-table">
                                            <table id="file-datatable" class="table table-bordered ">
                                                    <thead>
                                                        <tr>
                                                            <th>Pickup Id</th>
                                                            <th>Vendor Name</th>
                                                            <th>Pickup Items</th>
                                                            <th>Pickup Date</th>
                                                            <th>Total Price</th>
                                                            <th>Payment Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pickupHistory as $history)
                                                        <tr>
                                                            <!-- Pickup ID -->
                                                            <td>{{ $history->pick_up_id }}</td>
                                                            
                                                            <!-- Vendor Name -->
                                                            <td>{{ $history->vendor->vendor_name ?? 'N/A' }}</td>
                                                            
                
                                                            <td>
                                                                <ul>
                                                                    @foreach ($history->flowerPickupItems as $item)
                                                                        <li>
                                                                            <strong>Flower:</strong> {{ $item->flower?->name ?? 'N/A' }} <br>
                                                                            <strong>Quantity:</strong> {{ $item->quantity ?? 'N/A' }} {{ $item->unit?->unit_name ?? 'N/A' }} <br>
                                                                            <strong>Price:</strong> ₹{{ $item->price ?? 'N/A' }}
                                                                        </li>
                                                                        @if (!$loop->last)
                                                                            <hr>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                            
                                                            <!-- Pickup Date -->
                                                            <td>{{ \Carbon\Carbon::parse($history->pickup_date)->format('d-m-Y') }}</td>
                                                            
                                                            <!-- Total Price -->
                                                            <td>₹ {{ number_format($history->total_price, 2) }} </td>
                                                            
                                                            <!-- Payment Status -->
                                                            <td>{{ ucfirst($history->payment_status) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                              
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-profile-body -->
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{asset('assets/plugins/SmartPhoto-master/smartphoto.js')}}"></script>

 <!-- Internal Data tables -->
 <script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/pdfmake/pdfmake.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/pdfmake/vfs_fonts.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
 <script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
 <script src="{{asset('assets/js/table-data.js')}}"></script>
@endsection
