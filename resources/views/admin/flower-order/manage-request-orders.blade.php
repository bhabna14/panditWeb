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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Flower Order</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Flower Order</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-footer py-0">
                                <div class="profile-tab tab-menu-heading border-bottom-0">
                                    <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 full-width-tabs">
                                        <a class="nav-link mb-2 mt-2 " href="{{ route('admin.orders.index') }}"
                                            onclick="changeColor(this)">Subscription Orders</a>
                                        <a class="nav-link mb-2 mt-2 {{ Request::is('admin/flower-request-orders') ? 'active' : '' }}" href="{{ route('admin.requestorder.index') }}"
                                            onclick="changeColor(this)">Request Orders</a>
                                       
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <div class="table-responsive  export-table">
                                        <table id="file-datatable" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Order Date</th>
                                                    <th>Payment Status</th>
                                                    <th>User Name</th>
                                                    <th>Flower Product</th>
                                                    <th>Address</th>
                                                    <!-- Add any additional columns needed for your data -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($requestedOrders as $request)
                                                    @if($request->order)
                                                        <tr>
                                                            <td>{{ $request->order->order_id }}</td>
                                                            <td>{{ $request->order->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                @if($request->order->flowerPayments->isNotEmpty())
                                                                    {{ $request->order->flowerPayments->first()->status }}
                                                                @else
                                                                    No Payment
                                                                @endif
                                                            </td>
                                                            <td>{{ $request->order->user->name ?? 'N/A' }}</td>
                                                            <td>{{ $request->order->flowerProduct->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <strong>Address:</strong> {{ $order->address->area ?? "" }}<br>
                                                                <strong>City:</strong> {{ $order->address->city ?? ""}}<br>
                                                                <strong>State:</strong> {{ $order->address->state ?? ""}}<br>
                                                                <strong>Zip Code:</strong> {{ $order->address->pincode ?? "" }}
                                                            </td>
                                                            <!-- Add more data fields if necessary -->
                                                        </tr>
                                                    @endif
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
