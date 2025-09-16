@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .stats-card {
            border-radius: 14px;
            padding: 20px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
            border: 1px solid rgb(186, 185, 185);
        }

        .stats-card:hover {
            transform: translateY(-4px);
        }
    </style>
@endsection

@section('content')
    
    <div class="row mb-4 mt-4">
        <div class="col-md-6">
            <div class="stats-card shadow-sm">
                <div class="card-body text-center py-2">
                    <h6 class="card-title text-success mb-1">Total Customize Total Price</h6>
                    <h4 class="fw-bold mb-0" id="totalPrice">₹0</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card shadow-sm">
                <div class="card-body text-center py-2">
                    <h6 class="card-title text-info mb-1">Today Customize Price</h6>
                    <h4 class="fw-bold mb-0" id="todayPrice">₹0</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
            <label for="from_date" class="form-label fw-semibold">From Date</label>
            <input type="date" id="from_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="to_date" class="form-label fw-semibold">To Date</label>
            <input type="date" id="to_date" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="searchBtn" class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i> Search
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive export-table">
        <table id="file-datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Purchase Date</th>
                    <th>Delivery Date</th>
                    <th>Flower Items</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <!-- Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTable Script -->
    <script>
        $(document).ready(function() {
            const table = $('#file-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.customize') }}",
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    },
                    dataSrc: function(json) {
                        // Update totals
                        $('#totalPrice').text('₹' + (json.total_price_sum ?? 0).toLocaleString());
                        $('#todayPrice').text('₹' + (json.today_price_sum ?? 0).toLocaleString());

                        return json.data;
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const user = row.user || {};
                            const address = user.address_details || {};
                            const userId = user.userid ?? null;

                             const tooltipContent = `
                            <strong>Apartment:</strong> ${address.apartment_name  || 'N/A'}<br>
                            <strong>No:</strong> ${address.apartment_flat_plot || 'N/A'}
                        `.trim();

                            const modalId = `addressModal${userId}`;
                            const viewBtn = userId ?
                                `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a>` :
                                '';

                            const addressHtml = `
                            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title"><i class="fas fa-home"></i> Address Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Address:</strong> ${address.apartment_flat_plot || ''}, ${address.apartment_name || ''}, ${address.locality || ''}</p>
                                            <p><strong>Landmark:</strong> ${address.landmark || ''}</p>
                                            <p><strong>Pin Code:</strong> ${address.pincode || ''}</p>
                                            <p><strong>City:</strong> ${address.city || ''}</p>
                                            <p><strong>State:</strong> ${address.state || ''}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                            return `
                            <div class="order-details" data-bs-toggle="tooltip" data-bs-html="true" title="${tooltipContent}">
                                <div><strong>Name:</strong> ${user.name || 'N/A'}</div>
                                <div><strong>No:</strong> ${user.mobile_number || 'N/A'}</div>
                                <div class="mt-1 d-flex gap-2">
                                    ${viewBtn}
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#${modalId}">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                </div>
                            </div>
                            ${addressHtml}
                        `;
                        }
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'flower_items',
                        name: 'flower_items',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const modalId = `modal_${row.request_id}`;
                            return `
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#${modalId}">
                                View Items
                            </button>
                            <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="${modalId}Label">Flower Items</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            ${data ? data.split(',').map(item => `<div>- ${item.trim()}</div>`).join('') : '<em>No items found.</em>'}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-capitalize',
                        orderable: false
                    },
                    {
                        data: 'price',
                        name: 'price',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Reload on date filter
            $('#searchBtn').click(function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
