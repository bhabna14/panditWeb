@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE FLOWER PROMOTION</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.promotionList') }}"
                        class="btn btn-info text-white">Add Promotion</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Temple</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vendor</li>
            </ol>
        </div>
    </div>


    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sl No.</th>
                                    <th class="border-bottom-0">Header</th>
                                    <th class="border-bottom-0">Body</th>
                                    <th class="border-bottom-0">Start Date</th>
                                    <th class="border-bottom-0">End Date</th>
                                    <th class="border-bottom-0">Photo</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promotions as $key => $promotion)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $promotion->header }}</td>
                                        <td>{{ $promotion->body }}</td>
                                        <td>{{ $promotion->start_date }}</td>
                                        <td>{{ $promotion->end_date }}</td>
                                        <td>
                                            @if ($promotion->photo)
                                                <img src="{{ $promotion->photo }}" alt="Promotion Photo" width="100">
                                            @else
                                                No Photo
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                data-id="{{ $promotion->id }}" data-header="{{ e($promotion->header) }}"
                                                data-body="{{ e($promotion->body) }}"
                                                data-start_date="{{ \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d') }}"
                                                data-end_date="{{ \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d') }}"
                                                data-photo="{{ $promotion->photo }}">
                                                Edit
                                            </button>

                                            <form action="{{ route('admin.deleteFlowerPromotion', $promotion->id) }}"
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $promotion->id }}">
                                                    {{-- trash icon (bootstrap icon or fontawesome as you like) --}}
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <div class="modal fade" id="updatePromotionModal" tabindex="-1"
                            aria-labelledby="updatePromotionLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <form id="updatePromotionForm" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updatePromotionLabel">Update Promotion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"> </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Header</label>
                                                    <input type="text" class="form-control" name="header"
                                                        id="edit_header" required>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">Body</label>
                                                    <textarea class="form-control" name="body" id="edit_body" rows="4" required></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Start Date</label>
                                                    <input type="date" class="form-control" name="start_date"
                                                        id="edit_start_date" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">End Date</label>
                                                    <input type="date" class="form-control" name="end_date"
                                                        id="edit_end_date" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Photo (optional)</label>
                                                    <input type="file" class="form-control" name="photo"
                                                        id="edit_photo" accept="image/*">
                                                    <small class="text-muted d-block mt-1">Leave empty to keep current
                                                        photo.</small>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label d-block">Current Photo</label>
                                                    <img id="edit_photo_preview" src="" alt="Current Photo"
                                                        width="140" class="border rounded">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Promotion</button>
                                        </div>
                                    </form>
                                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>

     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Bootstrap JS (needed for modal). If already included globally, you can remove this. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Success / Error toasts
        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}' });
        @elseif (session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}' });
        @endif

        // Validation errors (from last request)
        @if ($errors->any())
            let msgs = '';
            @foreach ($errors->all() as $err)
                msgs += '• {{ $err }}\n';
            @endforeach
            Swal.fire({ icon: 'error', title: 'Validation Error', text: msgs });
        @endif

        // Handle Edit button -> fill & open modal
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const header = this.dataset.header;
                const body = this.dataset.body;
                const start = this.dataset.start_date;
                const end = this.dataset.end_date;
                const photo = this.dataset.photo;

                // Fill fields
                document.getElementById('edit_header').value = header;
                document.getElementById('edit_body').value = body;
                document.getElementById('edit_start_date').value = start;
                document.getElementById('edit_end_date').value = end;
                document.getElementById('edit_photo').value = ''; // clear file input
                document.getElementById('edit_photo_preview').src = photo || '';

                // Set form action to the update route
                const form = document.getElementById('updatePromotionForm');
                form.action = "{{ url('/admin/promotions') }}/" + id; // matches route('admin.updateFlowerPromotion', id)

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('updatePromotionModal'));
                modal.show();
            });
        });

        // Delete with SweetAlert confirm
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function () {
                const form = this.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will permanently delete the promotion.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
