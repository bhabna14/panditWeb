@extends('admin.layouts.apps')

@section('styles')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Offer</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.offerDetails') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Offer</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Offer</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Main Header</th>
                                    <th>Sub Header</th>
                                    <th>Discount (%)</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Image</th>
                                    <th>Menu Items</th>
                                    <th>Content</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($offers as $index => $offer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $offer->main_header }}</td>
                                        <td>{{ $offer->sub_header }}</td>
                                        <td>{{ $offer->discount }}%</td>
                                        <td>{{ $offer->start_date ?? '-' }}</td>
                                        <td>{{ $offer->end_date ?? '-' }}</td>
                                        <td>
                                            @if ($offer->image)
                                                <button type="button" class="btn btn-sm btn-info view-image-btn"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    data-image="{{ asset($offer->image) }}">
                                                    View
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $offer->menu }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-content-btn"
                                                data-bs-toggle="modal" data-bs-target="#contentModal"
                                                data-content="{{ $offer->content }}">
                                                View
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning edit-offer-btn"
                                                data-id="{{ $offer->id }}" data-main_header="{{ $offer->main_header }}"
                                                data-sub_header="{{ $offer->sub_header }}"
                                                data-discount="{{ $offer->discount }}" data-menu="{{ $offer->menu }}"
                                                data-content="{{ $offer->content }}"
                                                data-start_date="{{ $offer->start_date }}"
                                                data-end_date="{{ $offer->end_date }}"
                                                data-image="{{ asset($offer->image) }}" data-bs-toggle="modal"
                                                data-bs-target="#editOfferModal">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('admin.deleteOfferDetails', $offer->id) }}"
                                                method="POST" class="delete-offer-form" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-offer-btn">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Content Modal -->
                        <div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contentModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Offer Content</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="modalContentText"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Modal -->
                        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Offer Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img id="modalImage" src="" class="img-fluid" alt="Offer Image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Offer Modal -->
                        <div class="modal fade" id="editOfferModal" tabindex="-1" aria-labelledby="editOfferModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" id="editOfferForm" action="{{ route('admin.updateOfferDetails') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" id="edit_id">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Offer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label>Main Header</label>
                                                <input type="text" name="main_header" id="edit_main_header"
                                                    class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Sub Header</label>
                                                <input type="text" name="sub_header" id="edit_sub_header"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-6">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" id="edit_start_date"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-6">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" id="edit_end_date"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Discount (%)</label>
                                                <input type="number" name="discount" id="edit_discount"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-8">
                                                <label>Menu Items</label>
                                                <div id="edit_menu_container"></div>
                                                <button type="button" class="btn btn-sm btn-success mt-2"
                                                    id="edit_add_menu">+ Add Menu</button>
                                            </div>

                                            <div class="col-md-12">
                                                <label>Content</label>
                                                <textarea name="content" id="edit_content" rows="3" class="form-control"></textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Current Image</label><br>
                                                <img id="edit_preview_image" src="" alt="Preview"
                                                    class="img-fluid rounded border" style="max-height: 120px;">
                                            </div>

                                            <div class="col-md-6">
                                                <label>Change Image</label>
                                                <input type="file" name="image" class="form-control"
                                                    accept="image/*">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update Offer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Content Modal
            document.querySelectorAll('.view-content-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const content = this.getAttribute('data-content') || 'No content available.';
                    document.getElementById('modalContentText').innerText = content;
                });
            });

            // Image Modal
            document.querySelectorAll('.view-image-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const imageUrl = this.getAttribute('data-image');
                    document.getElementById('modalImage').src = imageUrl;
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Populate edit modal
            document.querySelectorAll('.edit-offer-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit_id').value = this.getAttribute('data-id');
                    document.getElementById('edit_main_header').value = this.getAttribute(
                        'data-main_header');
                    document.getElementById('edit_sub_header').value = this.getAttribute(
                        'data-sub_header');
                    document.getElementById('edit_discount').value = this.getAttribute(
                        'data-discount');
                    document.getElementById('edit_content').value = this.getAttribute(
                        'data-content');
                    document.getElementById('edit_start_date').value = this.getAttribute(
                        'data-start_date');
                    document.getElementById('edit_end_date').value = this.getAttribute(
                        'data-end_date');

                    const menuItems = (this.getAttribute('data-menu') || '').split(',');
                    const menuContainer = document.getElementById('edit_menu_container');
                    menuContainer.innerHTML = '';

                    menuItems.forEach((item, index) => {
                        const group = document.createElement('div');
                        group.classList.add('input-group', 'mb-2');
                        group.innerHTML = `
                        <input type="text" name="menu[]" class="form-control" value="${item.trim()}">
                        <button type="button" class="btn btn-danger remove-menu">−</button>
                    `;
                        menuContainer.appendChild(group);
                    });

                    document.getElementById('edit_preview_image').src = this.getAttribute(
                        'data-image');
                });
            });

            // Add new menu field in edit modal
            document.getElementById('edit_add_menu').addEventListener('click', function() {
                const menuContainer = document.getElementById('edit_menu_container');
                const group = document.createElement('div');
                group.classList.add('input-group', 'mb-2');
                group.innerHTML = `
                <input type="text" name="menu[]" class="form-control">
                <button type="button" class="btn btn-danger remove-menu">−</button>
            `;
                menuContainer.appendChild(group);
            });

            // Remove menu field
            document.getElementById('edit_menu_container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-menu')) {
                    e.target.closest('.input-group').remove();
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-offer-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This offer will be permanently deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6'
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33'
                });
            @endif
        });
    </script>
@endsection
