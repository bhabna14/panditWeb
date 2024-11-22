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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Podcast</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            <a href="{{url('admin/add-podcast')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Podcast</a>
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Podcast</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   
                @if(session()->has('success'))
                <div class="alert alert-success" id="Message">
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
                                                
                                                    <th class="border-bottom-0">Sl No.</th>
                                                    <th class="border-bottom-0">Podcast Name</th> 
                                                    <th class="border-bottom-0">Language</th>                                                   
                                                    <th class="border-bottom-0">View Image</th>
                                                    <th class="border-bottom-0">View Audio</th>
                                                    <th class="border-bottom-0">Status</th>
                                                    <th class="border-bottom-0">Action</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                           
                                            
                                                @foreach ($podcasts as $index => $podcast)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="border-bottom-0">{{ $podcast->name }}</td>
                                                    <td class="border-bottom-0">{{ $podcast->language }}</td>
                                                    <td>

                                                        <a href="{{ asset('storage/' . $podcast->image) }}" target="_blank"
                                                            class="btn btn-success">
                                                            View Image
                                                        </a>
                                                    </td>
                                                    <td>

                                                        <a href="{{ asset('storage/' . $podcast->music) }}" target="_blank"
                                                            class="btn btn-success">
                                                            View Audio
                                                        </a>
                                                    </td>
                                                    <td class="border-bottom-0">
                                                        {{-- <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                                                            <label class="form-check-label" for="flexSwitchCheckChecked">Active</label>
                                                          </div> --}}
                                                        {{ $podcast->status }}</td>

                                                    <td>
                                                      
                                                        <a href="{{url('admin/editpodcast/'.$podcast->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a> | 
                                                        <a href="{{url('admin/dltpodcast/'.$podcast->id)}}" class="btn btn-danger" onClick="return confirm('Are you sure to delete ?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
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

@endsection
