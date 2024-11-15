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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Puja List</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
							<a class="btn ripple btn-primary me-3" data-bs-target="#modaldemo1" data-bs-toggle="modal" href="">Add Puja List</a>

                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Puja List</li>
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
                   <!-- Basic modal -->
                   <div class="modal fade" id="modaldemo1">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-content-demo">
                            <div class="modal-header">
                                <h6 class="modal-title">Add Pooja List</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <form action="{{url('admin/saveitem')}}" method="post">
                             @csrf

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="event_name">Item Name</label>
                                            <input type="text" class="form-control" id="puja_name" name="item_name" placeholder="Enter Pooja Item Name">
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn ripple btn-primary" type="button">Save</button>
                                <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Basic modal -->

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
                                                    <th class="border-bottom-0">Item Name</th>
                                                    <th class="border-bottom-0">Variant Title</th>
                                                    <th class="border-bottom-0">Price</th>
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($poojaitems as $index => $poojaitem)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $poojaitem->item_name }}</td>
                                                    <td>{{ $poojaitem->variant_title }}</td>
                                                    <td>{{ $poojaitem->price }}</td>
                                                    <td>
                                                        <a class="btn ripple btn-primary me-3 edit-item" href="javascript:void(0);" data-id="{{ $poojaitem->product_id }}" data-name="{{ $poojaitem->item_name }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a class="btn ripple btn-primary" href="{{ url('admin/dltitem/'.$poojaitem->product_id) }}" onClick="return confirm('Are you sure to delete ?');">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                <tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Row -->

                   <!-- update the items modal -->
                    <div class="modal fade" id="modaldemo2">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">Edit Pooja Item</h6>
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="editItemForm" action="{{url('admin/updateitem')}}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="itemId" name="id">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="item_name">Item Name</label>
                                                    <input type="text" class="form-control" id="itemName" name="item_name" placeholder="Enter Pooja Item Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn ripple btn-primary">Save</button>
                                        <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Basic modal -->

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
    

    <script>
        $(document).ready(function(){
            $('.edit-item').on('click', function() {
                var itemId = $(this).data('id');
                var itemName = $(this).data('name');
                
                $('#itemId').val(itemId);
                $('#itemName').val(itemName);
                
                $('#modaldemo2').modal('show');
            });
        });
    </script>
@endsection
