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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Category</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
							<a class="btn ripple btn-primary me-3" data-bs-target="#modaldemo1" data-bs-toggle="modal" href="">Add Category</a>

                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Category</li>
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
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                   <!-- Basic modal -->
                <!-- Basic modal -->
                <div class="modal fade" id="modaldemo1">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-content-demo">
                            <div class="modal-header">
                                <h6 class="modal-title">Add Category</h6>
                                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <form action="{{ route('savecategory') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <!-- Category Name Field -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="category_name">Category Name</label>
                                                <input type="text" class="form-control @error('category_name') is-invalid @enderror" 
                                                    id="category_name" name="category_name" placeholder="Enter Category" 
                                                    value="{{ old('category_name') }}" required>
                                                @error('category_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Category Image Field -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="category_img">Category Image</label>
                                                <input type="file" class="form-control @error('category_img') is-invalid @enderror" 
                                                    id="category_img" name="category_img" accept="image/*">
                                                @error('category_img')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Description Field -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter description">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="submit" class="btn ripple btn-primary">Save</button>
                                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Basic modal -->
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
                                                    <th class="border-bottom-0">Category Name</th>
                                                    <th class="border-bottom-0">Category Image</th>
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($categories as $index => $category)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $category->category_name }}</td>
                                                        <td>
                                                            @if($category->category_img)
                                                                <img src="{{ asset('storage/' . $category->category_img) }}" alt="Category Image" width="50" height="50">
                                                            @else
                                                                <span>No Image</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a class="btn ripple btn-primary me-3 edit-item" href="javascript:void(0);"
                                                               data-id="{{ $category->id }}"
                                                               data-name="{{ $category->category_name }}"
                                                               data-description="{{ $category->description }}"
                                                               data-image="{{ $category->category_img }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <a class="btn ripple btn-primary" href="{{ url('admin/deletecategory/'.$category->id) }}"
                                                               onClick="return confirm('Are you sure to delete?');">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
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
                    <!-- End Row -->

             <!-- Update Category Modal -->
             <div class="modal fade" id="modaldemo2">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-content-demo">
                        <div class="modal-header">
                            <h6 class="modal-title">Edit Category</h6>
                            <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="editItemForm" action="{{ url('admin/updatecategory') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="itemId" name="id">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="category_name">Category Name</label>
                                            <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter Category">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="category_img">Category Image</label>
                                            <input type="file" class="form-control" id="category_img" name="category_img">
                                            <!-- Display current image -->
                                            <img id="currentCategoryImage" src="" alt="Current Image" style="max-width: 100px; margin-top: 10px; display: none;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
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
            
            <!-- End Update Category Modal -->


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
                var panditTitle = $(this).data('name'); // Updated to data-name
                
                $('#itemId').val(itemId);
                $('#panditTitle').val(panditTitle); // Updated to unitName
                
                $('#modaldemo2').modal('show');
            });
        });
    </script>
<script>
    $(document).on('click', '.edit-item', function() {
        // Retrieve data from data attributes
        let categoryId = $(this).data('id');
        let categoryName = $(this).data('name');
        let categoryDescription = $(this).data('description');
        let categoryImage = $(this).data('image');

        // Populate the modal fields
        $('#editItemForm #itemId').val(categoryId);
        $('#editItemForm #category_name').val(categoryName);
        $('#editItemForm #description').val(categoryDescription);

        // If you want to display the current image as a preview
        if (categoryImage) {
            $('#currentCategoryImage')
                .attr('src', `{{ asset('storage/') }}/${categoryImage}`)
                .show();
        } else {
            $('#currentCategoryImage').hide();
        }

        // Open the modal
        $('#modaldemo2').modal('show');
    });
</script>

    
@endsection
