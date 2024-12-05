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
        <a class="btn btn-primary me-3" href="{{ url('admin/manage-vendor-details') }}">BACK</a>
        <span class="main-content-title" style="margin-top: 36px">VENDOR DETAILS</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendor Details</li>
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
                        src="{{ asset('images/profile.jpeg') }}" 
                        alt="Vendor Image"
                        style="width: 100px; height: 100px; object-fit: cover;"
                    >
                    </div>

                    <!-- Rider Details -->
                    <div class="profile-details">
                        <h4 class="mb-1">{{ optional($pickupDetails->first()->first()->vendor)->vendor_name ?? 'N/A' }}</h4>
                        <p class="mb-0 text-muted">
                            <i class="fa fa-phone me-2"></i>Phone: {{ optional($pickupDetails->first()->first()->vendor)->phone_no ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Rider Statistics -->
                
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
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Rider</th>
                            <th>Flower Details</th>
                            <th>PickUp Date</th>
                            <th>Total Price</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pickupDetails->flatten()->sortByDesc('created_at') as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->vendor?->vendor_name ?? 'N/A' }}</td>
                                <td>{{ $detail->rider?->rider_name ?? 'N/A' }}</td>
                                <td>
                                    <ul>
                                        @foreach ($detail->flowerPickupItems as $item)
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
                                <td>{{ $detail->pickup_date }}</td>
                                <td>
                                    @if ($detail->total_price)
                                        ₹{{ $detail->total_price }}
                                    @else
                                        <span class="text-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($detail->payment_status === 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ $detail->status ?? 'N/A' }}</td>
                                
                                
                            </tr>
                            <!-- Payment Modal -->
                         
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
