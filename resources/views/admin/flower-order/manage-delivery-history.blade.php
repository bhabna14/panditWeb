@extends('admin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
<style>
    
</style>
@endsection

@section('content')

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Delivery History</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Delivery History</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   

              

                    <!-- Row -->
                    <div class="row row-sm">
                        <div class="col-lg-12">
                            <div class="card custom-card overflow-hidden">
                                <div class="card-body">
                                    <!-- <div>
                                        <h6 class="main-content-label mb-1">File export Datatables</h6>
                                        <p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
                                    </div> -->

                                    
                                    @if (session()->has('success'))
                                    <div class="alert alert-success" id="Message">
                                        {{ session()->get('success') }}
                                    </div>
                                    @endif

                                    @if ($errors->has('danger'))
                                    <div class="alert alert-danger" id="Message">
                                        {{ $errors->first('danger') }}
                                    </div>
                                    @endif
                                    <div class="table-responsive">
                                        <table id="file-datatable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>User Number</th>
                                                    <th>Product Name</th>
                                                    <th>Payment Details</th>
                                                    <th>Address</th>
                                                    <th>Rider Name</th>
                                                    <th>Delivery Status</th>
                                                    <th>Location</th>
                                                    <th>Delivery Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($deliveryHistory as $history)
                                                    <tr>
                                                        <td>{{ $history->order->order_id }}</td>
                                                        <td>{{ $history->order->user->mobile_number ?? 'N/A' }}</td>
                                                        <td>{{ $history->order->flowerProduct->name ?? 'N/A' }}</td>
                                                        <td>
                                                            @foreach($history->order->flowerPayments as $payment)
                                                                <p> ₹{{ $payment->paid_amount }}</p>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <strong>Address:</strong> {{ $history->order->address->apartment_flat_plot ?? "" }}, {{ $history->order->address->localityDetails->locality_name ?? "" }}<br>
                                                            <strong>Landmark:</strong> {{ $history->order->address->landmark ?? "" }}<br>
    
                                                            <strong>City:</strong> {{ $history->order->address->city ?? ""}}<br>
                                                            <strong>State:</strong> {{ $history->order->address->state ?? ""}}<br>
                                                            <strong>Pin Code:</strong> {{ $history->order->address->pincode ?? "" }}
                                                        </td>
                                                        </td>
                                                        <td>{{ $history->rider->rider_name ?? 'N/A' }}</td>
                                                        <td>{{ $history->delivery_status }}</td>
                                                        <td>{{ $history->longitude }} ,{{ $history->latitude }}</td>

                                                        <td>{{ $history->created_at->format('d-m-Y H:i:s') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Row -->

@endsection

@section('scripts')

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

    <!-- INTERNAL Select2 js -->
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>

@endsection
