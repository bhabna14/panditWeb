@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .vendor-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .vendor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .vendor-title {
            font-weight: 700;
            font-size: 16px;
        }

        .vendor-meta {
            font-size: 12px;
            color: #64748b;
        }

        .pill {
            background: #eef2ff;
            color: #3730a3;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 12px;
        }

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

        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 14px;
        }

        .search-input {
            max-width: 380px;
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
                    <a href="{{ route('admin.monthWiseFlowerPrice') }}" class="btn btn-info text-white">+ Add Flower
                        Price</a>
                </li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- Toolbar -->
            <div class="toolbar">
                <input id="vendorFilter" class="form-control search-input" type="text"
                    placeholder="Filter vendors by name...">
                <span class="text-muted">Vendors: <strong>{{ $vendors->count() }}</strong></span>
            </div>

            @forelse ($vendors as $vendor)
                <div class="vendor-card" data-vendor-name="{{ Str::lower($vendor->vendor_name) }}">
                    <div class="vendor-header" data-bs-toggle="collapse" data-bs-target="#vbody_{{ $vendor->vendor_id }}"
                        aria-expanded="true" role="button">
                        <div>
                            <div class="vendor-title">{{ $vendor->vendor_name }}</div>
                            <div class="vendor-meta">
                                @if ($vendor->phone_no)
                                    📞 {{ $vendor->phone_no }} &nbsp;
                                @endif
                                @if ($vendor->email_id)
                                    ✉️ {{ $vendor->email_id }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="pill">{{ $vendor->monthPrices->count() }} entries</span>
                        </div>
                    </div>

                    <div id="vbody_{{ $vendor->vendor_id }}" class="collapse show">
                        <div class="p-3">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:26%">Flower</th>
                                            <th style="width:22%">Date Range</th>
                                            <th style="width:12%">Qty</th>
                                            <th style="width:12%">Unit</th>
                                            <th style="width:14%">Price</th>
                                            <th style="width:14%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vendor->monthPrices as $row)
                                            <tr>
                                                <td>{{ $row->product?->name ?? $row->product_id }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $row->start_date?->format('d M Y') ?? \Carbon\Carbon::parse($row->start_date)->format('d M Y') }}
                                                        →
                                                        {{ $row->end_date?->format('d M Y') ?? \Carbon\Carbon::parse($row->end_date)->format('d M Y') }}
                                                    </span>
                                                </td>
                                                <td>{{ $row->quantity }}</td>
                                                <td>
                                                    {{-- show friendly if available, else id --}}
                                                    {{ $row->unit?->unit_name ?? $row->unit_id }}
                                                </td>
                                                <td><strong>₹{{ number_format($row->price_per_unit, 2) }}</strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning editBtn"
                                                        data-id="{{ $row->id }}"
                                                        data-start="{{ $row->start_date?->format('Y-m-d') ?? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') }}"
                                                        data-end="{{ $row->end_date?->format('Y-m-d') ?? \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') }}"
                                                        data-qty="{{ $row->quantity }}" data-unit="{{ $row->unit_id }}"
                                                        data-price="{{ $row->price_per_unit }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger deleteBtn"
                                                        data-id="{{ $row->id }}">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($vendor->monthPrices->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No entries for this
                                                    vendor.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted">No records found</div>
            @endforelse
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editForm" action="">
                @csrf
                <!-- using POST route -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Flower Price</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="edit_quantity" step="1"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Unit ID</label>
                            <input type="text" class="form-control" name="unit_id" id="edit_unit_id" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price per Unit</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_unit"
                                id="edit_price" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 (for deletes) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap collapse needs bundle (if not already in layout) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Vendor filter (client-side)
        document.getElementById('vendorFilter').addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.vendor-card').forEach(card => {
                const name = card.getAttribute('data-vendor-name') || '';
                card.style.display = name.includes(q) ? '' : 'none';
            });
        });

        // Open Edit Modal
        $(document).on('click', '.editBtn', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_start_date').val($(this).data('start'));
            $('#edit_end_date').val($(this).data('end'));
            $('#edit_quantity').val($(this).data('qty'));
            $('#edit_unit_id').val($(this).data('unit'));
            $('#edit_price').val($(this).data('price'));

            let id = $(this).data('id');
            // POST route (make sure route exists in web.php with admin prefix)
            $('#editForm').attr('action', '/admin/flower-price/update/' + id);

            $('#editModal').modal('show');
        });

        // Delete Confirmation
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
