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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Active Subscription</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Active Subscription</li>
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
                                    <div class="table-responsive  export-table">
                                        <table id="file-datatable" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>User Name</th>
                                                    <th>Product Details</th>
                                                    <th>Address Details</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    {{-- <th>Pause Date</th> --}}
                                                    <th>Total Price</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activeSubscriptions as $subscription)
                                                <tr>
                                                    <td>{{ $subscription->id }}</td>
                                                    <td>Name: {{ $subscription->user->name }} <br>
                                                        Number : {{ $subscription->user->mobile_number }}
                                                    </td>
                                                    <td>{{ $subscription->flowerProduct->name }} <br>
                                                        ( {{ \Carbon\Carbon::parse($subscription->subscription->start_date)->format('F j, Y') }} - {{ $subscription->subscription->new_date ? \Carbon\Carbon::parse($subscription->subscription->new_date)->format('F j, Y') : \Carbon\Carbon::parse($subscription->subscription->end_date)->format('F j, Y') }} )
                                                     </td>
                                                     <td>
                                                        <strong>Address:</strong> {{ $subscription->address->apartment_flat_plot ?? "" }},{{ $subscription->address->apartment_name ?? "" }}, {{ $subscription->address->localityDetails->locality_name ?? "" }}<br>
                                                        <strong>Landmark:</strong> {{ $subscription->address->landmark ?? "" }}<br>
                                                        <strong>City:</strong> {{ $subscription->address->city ?? ""}}<br>
                                                        <strong>State:</strong> {{ $subscription->address->state ?? ""}}<br>
                                                        <strong>Pin Code:</strong> {{ $subscription->address->pincode ?? "" }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($subscription->subscription->start_date)->format('d M, Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($subscription->subscription->end_date)->format('d M, Y') }}</td>
                                                    {{-- <td>{{ \Carbon\Carbon::parse($subscription->paused_at)->format('d M, Y') }}</td> --}}
                                                    <td>{{ number_format($subscription->total_price, 2) }}</td>
                                                    <td>
                                                        <span class="status-badge 
                                                        {{ $subscription->subscription->status === 'active' ? 'status-running bg-success' : '' }}
                                                        {{ $subscription->subscription->status === 'paused' ? 'status-paused bg-warning' : '' }}
                                                        {{ $subscription->subscription->status === 'expired' ? 'status-expired bg-danger' : '' }}
                                                        {{ $subscription->subscription->status === 'pending' ? 'status-expired bg-danger' : '' }}">
                                                            {{ ucfirst($subscription->subscription->status) }}
                                                        </span>
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
