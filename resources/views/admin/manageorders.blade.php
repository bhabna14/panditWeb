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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Orders</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Pandits</li>
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

                                                    <th class="border-bottom-0">Pandit Name</th>
                                                    <th class="border-bottom-0">Booking Date</th>
                                                    <th class="border-bottom-0">Total Payment</th>
                                                    <th class="border-bottom-0">Approved By</th>
                                                    <th class="border-bottom-0">Application Status</th>
                                                    
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                           
                                            
                                                <tr>
                                                    <td>1</td>
                                                    <a href="{{url('admin/pandit-profile/')}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/pandit-profile/')}}" class="title">demo demo</a>
                                                                <span class="small text">demo@gmail.com</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>14/05/2024</td>
                                                   <td>2000</td>
                                                   <td>Pandit</td>
                                                    <td>
                                                            <span class="badge badge-success">Approved</span> 
                                                       
                                                    </td>
                                                    
                                                    <td>
                                                        <a href="{{url('admin/pandit-profile/')}}"><i class="fas fa-eye"></i></a> | 
                                                        <a href="{{url('admin/editsebayat/')}}"><i class="fa fa-edit"></i></a> | 
                                                        <a href="{{url('admin/dltsebayat/')}}" onClick="return confirm('Are you sure to delete ?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                </tr>
                                             
                                              
                                                
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