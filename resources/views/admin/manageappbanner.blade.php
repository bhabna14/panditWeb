@extends('admin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />

@endsection

@section('content')

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage App Banner</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            <a href="{{url('admin/add-app-banner')}}" class="breadcrumb-item tx-15 btn btn-warning">Add App Banner</a>
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage App Banner</li>
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
                                        <table id="file-datatable" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th class="border-bottom-0">SlNo</th>
                                                    <th class="border-bottom-0">Title Text</th>
                                                    <th class="border-bottom-0">Banner Image</th>
                                                    <th class="border-bottom-0">Category</th>
                                                    
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                             
                                                
                                                @foreach ($banners as $index => $banner)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="border-bottom-0">{{ $banner->title_text }}</td>

                                                    <td>

                                                        <a href="{{ asset('uploads/banner/' . $banner->banner_img) }}" target="_blank"
                                                            class="btn btn-success">
                                                            View Image
                                                        </a>
                                                    </td>
                                                    <td class="border-bottom-0">{{ $banner->category }}</td>
                                                   
                                                    
                                                    <td>
                                                      
                                                        <a href="{{url('admin/editbanner/'.$banner->id)}}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a> | 
                                                        <a href="{{url('admin/deletebanner/'.$banner->id)}}" class="btn btn-sm btn-danger" onClick="return confirm('Are you sure to delete ?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                    </td>
                                                    {{-- <td class="border-bottom-0">{{ $podcast->description }}</td> --}}
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
    <script>
        setTimeout(function(){
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
@endsection
