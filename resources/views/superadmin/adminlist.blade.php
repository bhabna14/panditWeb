@extends('superadmin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .media-group {
    display: flex;
    align-items: center;
    flex-grow: 1;
}
.media-md {
    width: 2.375rem;
    height: 2.375rem;
    font-size: .7916666667rem;
}
.media-middle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.media-circle {
    border-radius: 50rem;
}
.media-group .media+.media-text {
    margin-left: .625rem;
}
.media-text {
    display: flex;
    flex-direction: column;
}
.media img {
    border-radius: inherit;
}
</style>
@endsection

@section('content')

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Admin List</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        
                        <ol class="breadcrumb">
                            <a href="{{url('superadmin/addadmin')}}" class="btn btn-primary btn-style">Add Admin</a>
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Admin List</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(session()->has('success'))
                <div class="alert alert-success"  id="Message">
                    {{ session()->get('success') }}
                </div>
                @endif
                @if ($errors->has('danger'))
                <div class="alert alert-danger" id="Message">
                    {{ $errors->first('danger') }}
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
                                        <table id="file-datatable" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                <th class="border-bottom-0">#</th>

                                                    <th class="border-bottom-0">Name</th>
                                                    <th class="border-bottom-0">Email</th>
                                                    <th class="border-bottom-0">Phone Number</th>
                                                    
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($adminlists as $index => $adminlist)
                                            
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                   <td>{{ $adminlist->name}}</td>
                                                   <td>{{ $adminlist->email}}</td>
                                                   <td>{{  $adminlist->phonenumber }}</td>
                                                   
                                                    <td>
                                                        {{-- <a href="{{url('admin/viewsebayat/'.$adminlist->userid)}}"><i class="fas fa-eye"></i></a> |  --}}
                                                        <a href="{{url('superadmin/editadmin/'.$adminlist->id)}}"><i class="fa fa-edit"></i></a> | 
                                                        <a href="{{url('superadmin/dltadmin/'.$adminlist->id)}}"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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
