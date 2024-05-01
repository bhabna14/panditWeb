@extends('layouts.app')

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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Sebayat List</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sebayat Lis</li>
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
                                    <div class="table-responsive  export-table">
                                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                                            <thead>
                                                <tr>
                                                <th class="border-bottom-0">#</th>

                                                    <th class="border-bottom-0">Name</th>
                                                    <th class="border-bottom-0">Registered Date</th>
                                                    <th class="border-bottom-0">Approved Date</th>
                                                    <th class="border-bottom-0">Added By</th>
                                                    <th class="border-bottom-0">Application Status</th>
                                                    
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($sebayatlists as $index => $sebayatlist)
                                            
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <a href="{{url('admin/viewsebayat/'.$sebayatlist->userid)}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                @if($sebayatlist->userphoto == '')
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                                @else
                                                                    <img src="{{asset('assets/uploads/userphoto/') }}/{{$sebayatlist->userphoto}}" alt="user">
                                                                @endif
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/viewsebayat/'.$sebayatlist->userid)}}" class="title">{{ $sebayatlist->first_name}} {{ $sebayatlist->last_name }}</a>
                                                                <span class="small text">{{ $sebayatlist->email }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>{{ $sebayatlist->created_at }}</td>
                                                   <td>{{ $sebayatlist->approved_date =="" ? "N/A" :  date_format($sebayatlist->approved_date,"j F Y")}}</td>
                                                   <td>{{  $sebayatlist->added_by }}</td>
                                                    <td>
                                                        @if($sebayatlist->application_status == 'rejected')
                                                            <span class="badge badge-primary">Rejected</span>
                                                        @elseif($sebayatlist->application_status == 'pending')
                                                          <span class="badge badge-orange">Pending</span>
                                                        @else
                                                            <span class="badge badge-success">{{ $sebayatlist->application_status }}</span> 
                                                        @endif
                                                    </td>
                                                    
                                                    <td>
                                                        <a href="{{url('admin/viewsebayat/'.$sebayatlist->userid)}}"><i class="fas fa-eye"></i></a> | 
                                                        <a href="{{url('admin/editsebayat/'.$sebayatlist->userid)}}"><i class="fa fa-edit"></i></a> | 
                                                        <a href="{{url('admin/dltsebayat/'.$sebayatlist->userid)}}" onClick="return confirm('Are you sure to delete ?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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
