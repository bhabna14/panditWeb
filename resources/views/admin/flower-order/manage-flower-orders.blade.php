@extends('admin.layouts.apps')

@section('styles')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        /* Dashboard cards */
        .stats-card {
            border-radius: 14px;
            padding: 20px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
            border: 1px solid #e0e0e0;
        }

        .stats-card:hover {
            transform: translateY(-4px);
        }

        .stats-title {
            font-size: 14px;
            color: #6c757d;
        }

        .stats-value {
            font-size: 22px;
            font-weight: 700;
        }

        .stats-icon {
            font-size: 28px;
            background: #007bff;
            color: #fff;
            border-radius: 10px;
            padding: 10px;
        }

        /* Table styling */
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 14px;
        }

        .order-details {
            font-size: 13px;
            line-height: 1.6;
        }

        /* Modal improvements */
        .modal-header {
            border-bottom: 0;
        }

        .modal-header.bg-primary,
        .modal-header.bg-info,
        .modal-header.bg-warning {
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- 📊 Dashboard Stats --}}
        <div class="row g-3 mb-4 mt-3">
            @php
                $cards = [
                    [
                        'title' => 'Subscriptions Placed Today',
                        'value' => $ordersRequestedToday,
                        'filter' => 'renew',
                        'icon' => 'fa-calendar-plus',
                    ],
                    [
                        'title' => 'Active Subscriptions',
                        'value' => $activeSubscriptions,
                        'filter' => 'active',
                        'icon' => 'fa-check-circle',
                    ],
                    [
                        'title' => 'Paused Subscriptions',
                        'value' => $pausedSubscriptions,
                        'filter' => 'paused',
                        'icon' => 'fa-pause-circle',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-md-4">
                    <a href="{{ route('admin.orders.index', ['filter' => $card['filter']]) }}" class="text-decoration-none">
                        <div class="stats-card d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-title">{{ $card['title'] }}</div>
                                <div class="stats-value">{{ $card['value'] }}</div>
                            </div>
                            <div class="stats-icon">
                                <i class="fas {{ $card['icon'] }}"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- ✅ Success/Error Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->has('danger'))
            <div class="alert alert-danger alert-dismissible fade show">{{ $errors->first('danger') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 🔍 Filters --}}
        <form id="filter-form" class="row g-3 align-items-end bg-light p-3 rounded mb-4">
            <div class="col-md-3">
                <label class="form-label">Customer Name</label>
                <select class="form-select select2" name="customer_name" id="customer_name">
                    <option value="">All</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mobile Number</label>
                <select class="form-select select2" name="mobile_number" id="mobile_number">
                    <option value="">All</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->mobile_number }}">{{ $user->mobile_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Apartment Name</label>
                <select class="form-select select2" name="apartment_name" id="apartment_name">
                    <option value="">All</option>
                    @foreach ($addresses as $addr)
                        <option value="{{ $addr->apartment_name }}">{{ $addr->apartment_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Apartment Number</label>
                <select class="form-select select2" name="apartment_flat_plot" id="apartment_flat_plot">
                    <option value="">All</option>
                    @foreach ($addresses as $addr)
                        <option value="{{ $addr->apartment_flat_plot }}">{{ $addr->apartment_flat_plot }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" id="search-btn" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>

        {{-- 📋 Subscriptions Table --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <table id="subscriptions-table" class="table table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>First Purchase</th>
                            <th>Period</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Rider</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="edit-status-form" method="POST"
                    action="{{ route('admin.subscriptions.updateStatus', ['id' => 0]) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Update Subscription Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="subscription_id" id="status-sub-id">
                            <div class="mb-3">
                                <label for="status-select">Status</label>
                                <select class="form-select" name="status" id="status-select" required>
                                    <option value="active">Active</option>
                                    <option value="paused">Paused</option>
                                    <option value="pending">Pending</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="editDatesModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="edit-dates-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Edit Subscription Dates</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="subscription_id" id="sub-id">
                            <div class="mb-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="sub-start" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="sub-end" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Global Edit Rider Modal -->
        <div class="modal fade" id="editRiderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="edit-rider-form" method="POST">
                    @csrf @method('POST')
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Assign/Change Rider</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="subscription_id" id="rider-sub-id">
                            <div class="mb-3">
                                <label>Rider</label>
                                <select name="rider_id" id="rider-select" class="form-control" required>
                                    <option value="">Choose Rider</option>
                                    @foreach ($riders as $rider)
                                        <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="editPauseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="edit-pause-form" method="POST" action="">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">Edit Pause Dates</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="subscription_id" id="pause-sub-id">
                            <div class="mb-3">
                                <label for="pause-start">Pause Start Date</label>
                                <input type="date" name="pause_start_date" id="pause-start" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="pause-end">Pause End Date</label>
                                <input type="date" name="pause_end_date" id="pause-end" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
    <!-- End Row -->
@endsection
@section('scripts')
    <!-- Dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#customer_name').select2({
                placeholder: 'Select Customer',
                allowClear: true
            });
            $('#mobile_number').select2({
                placeholder: 'Select Mobile',
                allowClear: true
            });
            $('#apartment_name').select2({
                placeholder: 'Select Apartment',
                allowClear: true
            });
            $('#apartment_flat_plot').select2({
                placeholder: 'Select Flat/Plot',
                allowClear: true
            });
        });
    </script>
    <script>
        $(function() {
            const table = $('#file-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.orders.index') }}",
                    data: function(d) {
                        d.filter = '{{ request('filter', '') }}';
                        d.customer_name = $('#customer_name').val();
                        d.mobile_number = $('#mobile_number').val();
                        d.apartment_name = $('#apartment_name').val();
                        d.apartment_flat_plot = $('#apartment_flat_plot').val();
                    }
                },

                columns: [{
                        data: null,
                        name: 'users.name',
                        orderable: false,
                        searchable: false,
                        render: function(r) {
                            const userId = r.users?.userid || '';
                            const orderId = r.id;
                            const address = r.order?.address || {};
                            const locality = r.order?.address?.localityDetails?.locality_name || '';

                            const tooltip = `
                                <p><i class='fas fa-map-marker-alt text-primary'></i> <strong>Address:</strong>
                                ${address.apartment_flat_plot || ''}, ${address.apartment_name || ''}, ${locality}</p>
                            `.replace(/"/g, '&quot;'); // Escape double quotes

                            return `
                                <div class="order-details" data-bs-toggle="tooltip" data-bs-html="true" title="${tooltip}">
                                    <strong>Ord:</strong> ${r.order?.order_id || 'N/A'}<br>
                                    <strong>Name:</strong> ${r.users?.name || 'N/A'}<br>
                                    <strong>No:</strong> ${r.users?.mobile_number || 'N/A'}<br>
                                    ${userId ? `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a>` : ''}
                                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addressModal${orderId}">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editAddressModal${orderId}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>

                                <!-- View Address Modal -->
                                <div class="modal fade" id="addressModal${orderId}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="fas fa-home"></i> Address Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Address:</strong> ${address.apartment_flat_plot || ''}, ${address.apartment_name || ''}, ${locality}</p>
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

                                <!-- Edit Address Modal -->
                                <div class="modal fade" id="editAddressModal${orderId}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="/admin/orders/${address.id}/update-address">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="PUT">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Address</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Flat/Plot</label>
                                                        <input type="text" name="apartment_flat_plot" class="form-control" value="${address.apartment_flat_plot || ''}" />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Apartment Name</label>
                                                        <input type="text" name="apartment_name" class="form-control" value="${address.apartment_name || ''}" />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Locality</label>
                                                        <input type="text" name="locality_name" class="form-control" value="${address.locality || ''}" />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Landmark</label>
                                                        <input type="text" name="landmark" class="form-control" value="${address.landmark || ''}" />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Pin Code</label>
                                                        <input type="text" name="pincode" class="form-control" value="${address.pincode || ''}" />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">City</label>
                                                        <input type="text" name="city" class="form-control" value="${address.city || ''}" />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">State</label>
                                                        <input type="text" name="state" class="form-control" value="${address.state || ''}" />
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    },

                    {
                        data: null,
                        name: 'created_at',
                        render: function(r) {
                            const createdAt = r.created_at ? moment(r.created_at).format(
                                'DD-MM-YYYY h:mm A') : 'N/A';

                            if (r.status === 'paused') {
                                const start = moment(r.pause_start_date).format('DD-MM-YYYY');
                                const end = moment(r.pause_end_date).format('DD-MM-YYYY');
                                return `
                                ${createdAt}
                                <div style="margin-top: 8px; padding: 8px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">
                                    <strong><i class="fas fa-pause-circle me-2"></i></strong> ${start}<br>
                                    <strong><i class="fas fa-play-circle me-2"></i></strong> ${end}
                                    <button class="btn btn-sm btn-outline-secondary edit-pause-dates mt-2"
                                        data-id="${r.id}"
                                        data-start="${r.pause_start_date}"
                                        data-end="${r.pause_end_date}"
                                        data-bs-toggle="modal" data-bs-target="#editPauseModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            `;
                            }

                            return createdAt;
                        }
                    },

                    {
                        data: null,
                        name: 'start_date',
                        render: function(r) {
                            const start = moment(r.start_date).format('MMM D, YYYY');
                            const end = r.new_date ? moment(r.new_date).format('MMM D, YYYY') :
                                moment(r.end_date).format('MMM D, YYYY');
                            return `${start}<br> — <br>${end}<br>
                        <button class="btn btn-sm btn-outline-secondary edit-dates" data-id="${r.id}">
                            <i class="fas fa-edit"></i>
                        </button>`;
                        }
                    },
                    {
                        data: 'order.total_price',
                        name: 'order.total_price',
                        render: function(p) {
                            return `₹ ${parseFloat(p).toFixed(2)}`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(s, t, r) {
                            const classes = {
                                active: 'bg-success',
                                paused: 'bg-warning',
                                expired: 'bg-primary',
                                dead: 'bg-danger',
                                pending: 'bg-danger'
                            };
                            return `<span class="badge ${classes[s] || ''}">${s.toUpperCase()}</span>
                        <button class="btn btn-sm btn-outline-info edit-status-btn mt-1"
                            data-bs-toggle="modal" data-bs-target="#editStatusModal"
                            data-id="${r.id}" data-status="${s}">
                            <i class="fas fa-edit"></i>
                        </button>`;
                        }
                    },
                    {
                        data: null,
                        name: 'order.rider_id',
                        render: function(r) {
                            return `${r.order?.rider?.rider_name || 'Unassigned'}<br>
                        <button class="btn btn-sm btn-outline-info edit-rider"
                            data-id="${r.id}" data-order-id="${r.order.id}" data-rider-id="${r.order?.rider?.rider_id || ''}">
                            <i class="fas fa-edit"></i>
                        </button>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(r) {
                            let btn =
                                `<a href="/admin/flower-orders/${r.id}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>`;
                            if (r.status === 'active')
                                btn +=
                                ` <a href="/admin/subscription/pause-page/${r.id}" class="btn btn-sm btn-warning"><i class="fas fa-pause"></i></a>`;
                            if (r.status === 'paused')
                                btn +=
                                ` <a href="/admin/subscription/resume-page/${r.id}" class="btn btn-sm btn-warning"><i class="fas fa-play"></i></a>`;
                            return btn;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });

            table.on('draw', function() {
                // Re-initialize all Bootstrap tooltips
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
            });

            $('#search-btn').on('click', function() {
                table.ajax.reload();
            });

            $('#file-datatable').on('click', '.edit-dates', function() {
                const row = table.row($(this).closest('tr')).data();
                $('#sub-id').val(row.id);
                $('#sub-start').val(moment(row.start_date).format('YYYY-MM-DD'));
                $('#sub-end').val(moment(row.new_date || row.end_date).format('YYYY-MM-DD'));
                $('#edit-dates-form').attr('action', `/admin/subscriptions/${row.id}/updateDates`);
                new bootstrap.Modal($('#editDatesModal')[0]).show();
            });

            $('#edit-dates-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success');
                            bootstrap.Modal.getInstance($('#editDatesModal')[0]).hide();
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let message = '';
                            for (const key in errors) {
                                message += `${errors[key][0]}<br>`;
                            }
                            Swal.fire({
                                title: 'Validation Error',
                                html: message,
                                icon: 'warning'
                            });
                        } else {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'An unknown error occurred.', 'error');
                        }
                    }
                });
            });

            // -- Edit Rider --
            $('#file-datatable').on('click', '.edit-rider', function() {
                const row = table.row($(this).closest('tr')).data();
                $('#rider-sub-id').val(row.id);
                $('#rider-select').val(row.order.rider?.rider_id || '');
                $('#edit-rider-form').attr('action', `/admin/orders/${row.order.id}/updateRider`);
                new bootstrap.Modal($('#editRiderModal')[0]).show();
            });

            $('#edit-rider-form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: this.action,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: () => {
                        Swal.fire('Updated', 'Rider assigned.', 'success');
                        $('#editRiderModal').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: () => Swal.fire('Error', 'Failed to update rider.', 'error')
                });
            });

            // -- Edit Status --
            $('#file-datatable').on('click', '.edit-status-btn', function() {
                const id = $(this).data('id');
                const status = $(this).data('status');
                $('#status-sub-id').val(id);
                $('#status-select').val(status);
                $('#edit-status-form').attr('action', `/admin/subscriptions/${id}/update-status`);
            });

            $('#edit-status-form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: this.action,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                text: res.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // 🔄 Auto-refresh the page
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let message = '';
                            for (const key in errors) {
                                message += `${errors[key][0]}<br>`;
                            }
                            Swal.fire({
                                title: 'Validation Error',
                                html: message,
                                icon: 'warning'
                            });
                        } else {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to update status.', 'error');
                        }
                    }
                });
            });


            // // -- Edit Pause Dates --
            // $('#file-datatable').on('click', '.edit-pause-dates', function() {
            //     const id = $(this).data('id');
            //     const start = $(this).data('start');
            //     const end = $(this).data('end');
            //     $('#pause-sub-id').val(id);
            //     $('#pause-start').val(moment(start).format('YYYY-MM-DD'));
            //     $('#pause-end').val(moment(end).format('YYYY-MM-DD'));
            //     $('#edit-pause-form').attr('action', `/admin/subscriptions/${id}/updatePauseDates`);
            //     new bootstrap.Modal($('#editPauseModal')[0]).show();
            // });

            // $('#edit-pause-form').submit(function(e) {
            //     e.preventDefault();
            //     $.ajax({
            //         url: this.action,
            //         type: 'POST',
            //         data: $(this).serialize(),
            //         success: () => {
            //             Swal.fire('Updated', 'Pause dates updated.', 'success');
            //             $('#editPauseModal').modal('hide');
            //             table.ajax.reload(null, false);
            //         },
            //         error: () => Swal.fire('Error', 'Failed to update pause dates.', 'error')
            //     });
            // });


            $('#file-datatable').on('click', '.edit-pause-dates', function() {
                const id = $(this).data('id');
                const start = $(this).data('start');
                const end = $(this).data('end');

                $('#pause-sub-id').val(id);
                $('#pause-start').val(moment(start).format('YYYY-MM-DD'));
                $('#pause-end').val(moment(end).format('YYYY-MM-DD'));
                $('#edit-pause-form').attr('action', `/admin/subscriptions/${id}/update-pause-dates`);
            });

            $('#edit-pause-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        Swal.fire('Updated', res.message || 'Pause dates updated.', 'success');
                        $('#editPauseModal').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let msg = '';
                            for (const key in errors) {
                                msg += `${errors[key][0]}<br>`;
                            }
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validation Error',
                                html: msg
                            });
                        } else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    }
                });
            });

        });
    </script>
@endsection
