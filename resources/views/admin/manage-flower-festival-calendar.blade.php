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
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Festival</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.offerDetails') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Fesitval</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Calendar</li>
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
                                    <th>Festival Name</th>
                                    <th>Festival Date</th>
                                    <th>Festival Image</th>
                                    <th>Package Price</th>
                                    <th>Related Flowers</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($festivals as $index => $festival)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $festival->festival_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($festival->festival_date)->format('d M, Y') }}</td>
                                        <td>
                                            @if ($festival->festival_image)
                                                <img src="{{ asset($festival->festival_image) }}" alt="Image"
                                                    width="50" height="50" data-bs-toggle="modal"
                                                    data-bs-target="#imageModal{{ $festival->id }}"
                                                    style="cursor:pointer;">
                                                <!-- Image Modal -->
                                                <div class="modal fade" id="imageModal{{ $festival->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Festival Image</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset($festival->festival_image) }}"
                                                                    class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                No Image
                                            @endif
                                        </td>
                                        <td>{{ $festival->package_price }}</td>
                                        <td>{{ $festival->related_flower }}</td>
                                        <td>{{ $festival->description }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $festival->id }}"
                                                data-name="{{ $festival->festival_name }}"
                                                data-date="{{ $festival->festival_date }}"
                                                data-price="{{ $festival->package_price }}"
                                                data-flowers="{{ $festival->related_flower }}"
                                                data-description="{{ $festival->description }}"
                                                data-image="{{ asset($festival->festival_image) }}">
                                                <i class="fas fa-edit"></i>
                                            </button>


                                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $festival->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <!-- Edit Festival Modal -->
                        <div class="modal fade" id="editModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <form id="editFestivalForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" id="edit_id">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Festival</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label>Festival Name</label>
                                                <input type="text" class="form-control" id="edit_name"
                                                    name="festival_name" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Festival Date</label>
                                                <input type="date" class="form-control" id="edit_date"
                                                    name="festival_date" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Package Price</label>
                                                <input type="number" class="form-control" id="edit_price"
                                                    name="package_price">
                                            </div>

                                            <div class="col-md-6">
                                                <label>Festival Image</label>
                                                <input type="file" class="form-control" name="festival_image">
                                                <img id="currentImage" src="" class="img-thumbnail mt-2"
                                                    width="100" alt="Current Image">
                                            </div>

                                            <div class="col-md-12">
                                                <label>Related Flowers</label>
                                                <div id="edit-flower-container">
                                                    <!-- dynamically added flower inputs will go here -->
                                                </div>
                                                <button type="button" class="btn btn-sm btn-success mt-2"
                                                    id="addFlowerBtn">+ Add Flower</button>
                                            </div>

                                            <div class="col-md-12">
                                                <label>Description</label>
                                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
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
                // Handle delete
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const festivalId = this.dataset.id;
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'This action cannot be undone!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Create form dynamically
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `/admin/delete-festival-calendar/${festivalId}`;

                                const token = document.createElement('input');
                                token.type = 'hidden';
                                token.name = '_token';
                                token.value = '{{ csrf_token() }}';
                                form.appendChild(token);

                                const method = document.createElement('input');
                                method.type = 'hidden';
                                method.name = '_method';
                                method.value = 'DELETE';
                                form.appendChild(method);

                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const flowerContainer = document.getElementById('edit-flower-container');
                const addFlowerBtn = document.getElementById('addFlowerBtn');
                const editModalElement = document.getElementById('editModal');
                const editModal = new bootstrap.Modal(editModalElement);

                // Function to add a flower input
                function addFlowerInput(value = '') {
                    const div = document.createElement('div');
                    div.classList.add('input-group', 'mb-2');
                    div.innerHTML = `
                <input type="text" name="related_flower[]" class="form-control" value="${value}">
                <button type="button" class="btn btn-danger remove-flower">−</button>
            `;
                    flowerContainer.appendChild(div);
                }

                // Add new flower input
                addFlowerBtn.addEventListener('click', () => addFlowerInput());

                // Remove flower input
                flowerContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-flower')) {
                        e.target.closest('.input-group').remove();
                    }
                });

                // Open modal and populate data
                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('edit_id').value = this.dataset.id;
                        document.getElementById('edit_name').value = this.dataset.name;
                        document.getElementById('edit_date').value = this.dataset.date;
                        document.getElementById('edit_price').value = this.dataset.price;
                        document.getElementById('edit_description').value = this.dataset.description;

                        const imageSrc = this.dataset.image;
                        const imageElement = document.getElementById('currentImage');
                        imageElement.src = imageSrc;
                        imageElement.style.display = imageSrc ? 'block' : 'none';

                        // Clear previous flower inputs
                        flowerContainer.innerHTML = '';
                        const flowers = this.dataset.flowers.split(',');
                        flowers.forEach(f => addFlowerInput(f.trim()));

                        editModal.show();
                    });
                });

                // Submit update via AJAX
                document.getElementById('editFestivalForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const id = document.getElementById('edit_id').value;
                    const formData = new FormData(this);

                    fetch(`/admin/update-festival-calendar/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(response => {
                            editModal.hide(); // ✅ close modal immediately

                            setTimeout(() => {
                                if (response.success) {
                                    Swal.fire('Updated!', response.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }, 300); // slight delay to allow modal close animation
                        })
                        .catch(() => {
                            editModal.hide();
                            setTimeout(() => {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }, 300);
                        });
                });
            });
        </script>
    @endsection
