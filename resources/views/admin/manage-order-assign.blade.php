@extends('admin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .aligned-list {
    list-style-type: disc;
    padding-left: 20px;
    margin: 0;
}

</style>
@endsection

@section('content')

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Order Assign</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            <a href="{{url('admin/add-order-assign')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Order</a>
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rider</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   
                @if(session('success'))
                <div id = 'Message' class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('danger'))
                <div id = 'Message' class="alert alert-danger">
                    {{ session('danger') }}
                </div>
            @endif
                  

                    <!-- Row -->
                    <div class="row row-sm">
                        <div class="col-lg-12">
                            <div class="card custom-card overflow-hidden">
                                <div class="card-body">
                                    <!-- <div>
                                        <h6 class="main-content-label mb-1">File export Datatables</h6>
                                        <p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
                                    </div> -->
                                    <div class="table-responsive  export-table">
                                        <table id="file-datatable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sl No</th>
                                                    <th>Rider Name</th>
                                                    <th>Locality Name</th>
                                                    <th>Apartment Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($rider_details as $rider)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        
                                                        <!-- Rider Name -->
                                                        <td>{{ $rider->rider->rider_name ?? 'N/A' }}</td>
                                                        
                                                        <!-- Locality Name -->
                                                        <td>{{ $rider->locality->locality_name ?? 'N/A' }}</td>
                                                        
                                                        <!-- Apartments (UL List) -->
                                                        <td>
                                                            @php
                                                                $apartmentList = explode(',', $rider->apartment_id);
                                                            @endphp
                                                            <ul>
                                                                @foreach($apartmentList as $apartmentId)
                                                                    @php
                                                                        $apartmentName = App\Models\Apartment::find($apartmentId)->apartment_name ?? 'N/A';
                                                                    @endphp
                                                                    <li>{{ $apartmentName }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                        
                                                        <!-- Actions -->
                                                        <td>
                                                            <a href="{{ url('admin/edit-order-assign/'.$rider->id) }}" class="btn btn-sm btn-warning">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admin.deleteOrderAssign', $rider->id) }}" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this Order?');">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
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
    <script>
        setTimeout(function(){
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
@endsection
