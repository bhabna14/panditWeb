@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .search-input {
            max-width: 380px;
        }

        .accordion-button {
            font-weight: 600;
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

        .meta {
            font-size: 12px;
            color: #64748b;
        }

        .flower-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .flower-head {
            background: #fbfdff;
            border-bottom: 1px solid #eef2f7;
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .flower-title {
            font-weight: 600;
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
                <div class="btn-group">
                    <button id="expandAllVendors" class="btn btn-outline-secondary btn-sm">Expand All Vendors</button>
                    <button id="collapseAllVendors" class="btn btn-outline-secondary btn-sm">Collapse All Vendors</button>
                </div>
                <span class="text-muted ms-auto">Vendors: <strong>{{ $vendors->count() }}</strong></span>
            </div>

            @if ($vendors->isEmpty())
                <div class="text-center text-muted py-3">No records found</div>
            @else
                <div class="accordion" id="vendorAccordion">
                    @foreach ($vendors as $i => $vendor)
                        @php
                            $accId = 'v_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $vendor->vendor_id);
                            $open = $i === 0 ? 'show' : ''; // open first vendor by default
                            $expanded = $i === 0 ? 'true' : 'false';

                            // Group this vendor's rows by flower (product_id)
$byFlower = $vendor->monthPrices->groupBy('product_id');
                        @endphp

                        <div class="accordion-item vendor-item" data-vendor-name="{{ strtolower($vendor->vendor_name) }}">
                            <h2 class="accordion-header" id="heading_{{ $accId }}">
                                <button class="accordion-button {{ $open ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse_{{ $accId }}"
                                    aria-expanded="{{ $expanded }}" aria-controls="collapse_{{ $accId }}">
                                    <div class="w-100 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $vendor->vendor_name }}</div>
                                            <div class="meta">
                                                @if ($vendor->phone_no)
                                                    📞 {{ $vendor->phone_no }} &nbsp;
                                                @endif
                                                @if ($vendor->email_id)
                                                    ✉️ {{ $vendor->email_id }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="pill">{{ $byFlower->count() }} flowers</span>
                                            <span class="pill">{{ $vendor->monthPrices->count() }} entries</span>
                                        </div>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse_{{ $accId }}" class="accordion-collapse collapse {{ $open }}"
                                aria-labelledby="heading_{{ $accId }}" data-bs-parent="#vendorAccordion">
                                <div class="accordion-body">

                                    <div class="d-flex justify-content-end mb-2">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary expand-flowers"
                                                data-vendor="{{ $accId }}">Expand Flowers</button>
                                            <button class="btn btn-outline-secondary collapse-flowers"
                                                data-vendor="{{ $accId }}">Collapse Flowers</button>
                                        </div>
                                    </div>

                                    {{-- Flower groups --}}
                                    <div id="flowers_{{ $accId }}">
                                        @forelse ($byFlower as $productId => $rows)
                                            @php
                                                $first = $rows->first();
                                                $flowerName = $first->product?->name ?? $productId;
                                                $flowerCardId =
                                                    'f_' .
                                                    $accId .
                                                    '_' .
                                                    preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $productId);
                                            @endphp

                                            <div class="flower-card">
                                                <div class="flower-head" data-bs-toggle="collapse"
                                                    data-bs-target="#body_{{ $flowerCardId }}" aria-expanded="false">
                                                    <div class="flower-title">{{ $flowerName }}</div>
                                                    <div class="pill">{{ $rows->count() }} entries</div>
                                                </div>

                                                <div id="body_{{ $flowerCardId }}" class="collapse">
                                                    <div class="p-3">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered align-middle mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:26%">Date Range</th>
                                                                        <th style="width:14%">Qty</th>
                                                                        <th style="width:14%">Unit</th>
                                                                        <th style="width:18%">Price</th>
                                                                        <th style="width:28%">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($rows as $row)
                                                                        <tr>
                                                                            <td>
                                                                                <span class="badge bg-light text-dark">
                                                                                    {{ $row->start_date?->format('d M Y') ?? \Carbon\Carbon::parse($row->start_date)->format('d M Y') }}
                                                                                    →
                                                                                    {{ $row->end_date?->format('d M Y') ?? \Carbon\Carbon::parse($row->end_date)->format('d M Y') }}
                                                                                </span>
                                                                            </td>
                                                                            <td>{{ $row->quantity }}</td>
                                                                            <td>{{ $row->unit?->unit_name ?? $row->unit_id }}
                                                                            </td>
                                                                            <td><strong>₹{{ number_format($row->price_per_unit, 2) }}</strong>
                                                                            </td>
                                                                            <td>
                                                                                <button
                                                                                    class="btn btn-sm btn-warning editBtn"
                                                                                    data-id="{{ $row->id }}"
                                                                                    data-start="{{ $row->start_date?->format('Y-m-d') ?? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') }}"
                                                                                    data-end="{{ $row->end_date?->format('Y-m-d') ?? \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') }}"
                                                                                    data-qty="{{ $row->quantity }}"
                                                                                    data-unit="{{ $row->unit_id }}"
                                                                                    data-price="{{ $row->price_per_unit }}">
                                                                                    Edit
                                                                                </button>
                                                                                <button
                                                                                    class="btn btn-sm btn-danger deleteBtn"
                                                                                    data-id="{{ $row->id }}">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted">No flowers found for this vendor.</div>
                                        @endforelse
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editForm" action="">
                @csrf
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
                            <input type="number" class="form-control" name="quantity" id="edit_quantity"
                                step="1" required>
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
    <!-- SweetAlert2 for deletes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap bundle (collapse) if not already in layout -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Filter vendors by name
        document.getElementById('vendorFilter').addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.vendor-item').forEach(item => {
                const name = item.getAttribute('data-vendor-name') || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });

        // Expand/Collapse all vendors
        document.getElementById('expandAllVendors').addEventListener('click', function() {
            document.querySelectorAll('#vendorAccordion .accordion-collapse').forEach(el => {
                if (!el.classList.contains('show')) new bootstrap.Collapse(el, {
                    toggle: true
                });
            });
        });
        document.getElementById('collapseAllVendors').addEventListener('click', function() {
            document.querySelectorAll('#vendorAccordion .accordion-collapse.show').forEach(el => {
                new bootstrap.Collapse(el, {
                    toggle: true
                });
            });
        });

        // Per-vendor: expand/collapse all flowers
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('expand-flowers')) {
                const id = e.target.getAttribute('data-vendor');
                document.querySelectorAll('#flowers_' + id + ' .collapse').forEach(el => {
                    if (!el.classList.contains('show')) new bootstrap.Collapse(el, {
                        toggle: true
                    });
                });
            }
            if (e.target.classList.contains('collapse-flowers')) {
                const id = e.target.getAttribute('data-vendor');
                document.querySelectorAll('#flowers_' + id + ' .collapse.show').forEach(el => {
                    new bootstrap.Collapse(el, {
                        toggle: true
                    });
                });
            }
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
            $('#editForm').attr('action', '/admin/flower-price/update/' + id); // POST route

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
