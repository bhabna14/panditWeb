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
                    <div class="prof-details text-center">
                        <div class="card bg-light shadow-sm" style="min-width: 120px;">
                            <div class="card-body p-3">
                                <i class="fa fa-rupee-sign text-success custom-size-icon"></i>
                                <h6 class="mb-1">Total Refer</h6>
                                <p class="mb-0 font-weight-bold">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-header">
                <h4 class="card-title">Delivery History</h4>
            </div>
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
</div>
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
