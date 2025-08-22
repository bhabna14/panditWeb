@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }

        .badge {
            font-size: 12px;
            padding: 5px 8px;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Flower Price</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.monthWiseFlowerPrice') }}" class="btn btn-info text-white">
                        + Add Flower Price
                    </a>
                </li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive export-table">
                <table id="flower-price-table" class="table table-bordered text-nowrap key-buttons border-bottom">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Flower</th>
                            <th>Date Range</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $index => $t)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $t->vendor?->vendor_name ?? '-' }}</td>
                                <td>{{ $t->product?->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ \Carbon\Carbon::parse($t->start_date)->format('d M Y') }} →
                                        {{ \Carbon\Carbon::parse($t->end_date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>{{ $t->quantity }}</td>
                                <td>{{ $t->unit_id }}</td>
                                <td><strong>₹{{ number_format($t->price_per_unit, 2) }}</strong></td>
                                <td>
                                    <button class="btn btn-sm btn-warning editBtn" data-id="{{ $t->id }}"
                                        data-vendor="{{ $t->vendor_id }}" data-product="{{ $t->product_id }}"
                                        data-start="{{ $t->start_date }}" data-end="{{ $t->end_date }}"
                                        data-qty="{{ $t->quantity }}" data-unit="{{ $t->unit_id }}"
                                        data-price="{{ $t->price_per_unit }}">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger deleteBtn"
                                        data-id="{{ $t->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($transactions->isEmpty())
                    <div class="text-center text-muted py-3">No records found</div>
                @endif
            </div>
        </div>
    </div>

    <!-- ✅ Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Flower Price</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                        </div>
                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="edit_quantity" required>
                        </div>
                        <div class="mb-3">
                            <label>Unit ID</label>
                            <input type="text" class="form-control" name="unit_id" id="edit_unit_id" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_unit" id="edit_price"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables -->
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

    <script>
        // ✅ DataTable init
        $(function() {
            $('#flower-price-table').DataTable({
                pageLength: 10,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-info'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-sm btn-danger'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-primary'
                    }
                ]
            });
        });

        // ✅ Open Edit Modal
       $(document).ready(function () {
    $('.editBtn').on('click', function () {
        let id = $(this).data('id');
        $('#edit_id').val(id);
        $('#edit_start_date').val($(this).data('start'));
        $('#edit_end_date').val($(this).data('end'));
        $('#edit_quantity').val($(this).data('qty'));
        $('#edit_unit_id').val($(this).data('unit'));
        $('#edit_price').val($(this).data('price'));

        // set form action URL
        $('#editForm').attr('action', '/flower-price/update/' + id);

        $('#editModal').modal('show');
    });
});

        // ✅ Delete Confirmation
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This record will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.deleteFlowerPrice', '') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success')
                                .then(() => location.reload());
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
