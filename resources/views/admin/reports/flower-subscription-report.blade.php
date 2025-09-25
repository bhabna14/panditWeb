{{-- Extend your base layout --}}
@extends('admin.layouts.apps')

{{-- SECTION: Styles --}}
@section('styles')
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- DataTables & Bootstrap CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons & Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        /* KPIs */
        .kpi-card {
            border-radius: 14px;
            padding: 16px 18px;
            background: linear-gradient(135deg, #ffffff, #fafafa);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
            border: 1px solid #eaeaea;
            transition: transform .18s ease;
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
        }

        .kpi-label {
            font-size: .85rem;
            opacity: .8;
            margin-bottom: 4px;
        }

        .kpi-value {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: .2px;
        }

        /* Filter bar */
        .filter-card {
            border-radius: 14px;
            border: 1px solid #ececec;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .05);
        }

        .range-btns .btn {
            border-radius: 999px;
        }

        .range-btns .btn.active {
            background: #0d6efd;
            color: #fff;
        }

        /* Table tweaks */
        table.dataTable tbody td {
            vertical-align: middle;
        }

        .status-badge {
            padding: .35rem .6rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-active {
            background: #e6f4ea;
            color: #1e7e34;
        }

        .status-paused {
            background: #fff3cd;
            color: #856404;
        }

        .status-expired {
            background: #fde7e9;
            color: #c21f3a;
        }

        .status-resume {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .customer-meta small {
            color: #6c757d;
        }
    </style>
@endsection

{{-- SECTION: Content --}}
@section('content')
    <div class="container-fluid px-0">

        {{-- KPIs --}}
        <div class="row g-3 mt-2 mb-3">
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label text-secondary">Total Subscription Revenue</div>
                            <div class="kpi-value" id="totalPrice">₹0</div>
                        </div>
                        <i class="bi bi-cash-coin fs-3 text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label text-secondary">Renew Customers Revenue</div>
                            <div class="kpi-value" id="renewCustomerTotalPrice">₹0</div>
                        </div>
                        <i class="bi bi-arrow-repeat fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label text-secondary">New Subscriptions Revenue</div>
                            <div class="kpi-value" id="newUserTotalPrice">₹0</div>
                        </div>
                        <i class="bi bi-person-plus fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card filter-card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold">Quick Range</label>
                        <div class="btn-group range-btns flex-wrap" role="group" aria-label="Quick Ranges">
                            <button type="button" class="btn btn-outline-primary btn-sm range-quick active"
                                data-range="today">
                                <i class="bi bi-calendar-day me-1"></i>Today
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm range-quick" data-range="week">
                                <i class="bi bi-calendar-week me-1"></i>This Week
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm range-quick" data-range="month">
                                <i class="bi bi-calendar3 me-1"></i>This Month
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm range-quick" data-range="year">
                                <i class="bi bi-calendar4-week me-1"></i>This Year
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm range-quick" data-range="custom">
                                <i class="bi bi-sliders me-1"></i>Custom
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="from_date" class="form-label fw-semibold">From</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" disabled>
                            </div>
                            <div class="col-6">
                                <label for="to_date" class="form-label fw-semibold">To</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-1 d-grid">
                        <button type="button" id="searchBtn" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Go
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="file-datatable" class="table table-bordered table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:280px">Customer</th>
                                <th>Purchase Period</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button id="refreshBtn" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <a href="{{ route('subscription.report') }}?export=csv" id="exportCsv"
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- SECTION: Scripts --}}
@section('scripts')
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/en-gb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            moment.locale('en-gb');

            // ------ Helpers: date range controls ------
            const $from = $('#from_date');
            const $to = $('#to_date');

            function setQuickRange(rangeKey) {
                const today = moment().startOf('day');
                let start = today.clone(),
                    end = today.clone();

                switch (rangeKey) {
                    case 'today':
                        start = today.clone();
                        end = today.clone();
                        break;
                    case 'week':
                        start = moment().startOf('isoWeek');
                        end = moment().endOf('isoWeek');
                        break;
                    case 'month':
                        start = moment().startOf('month');
                        end = moment().endOf('month');
                        break;
                    case 'year':
                        start = moment().startOf('year');
                        end = moment().endOf('year');
                        break;
                    case 'custom':
                        // enable fields and keep values as-is or default to today
                        $from.prop('disabled', false);
                        $to.prop('disabled', false);
                        if (!$from.val()) $from.val(today.format('YYYY-MM-DD'));
                        if (!$to.val()) $to.val(today.format('YYYY-MM-DD'));
                        return;
                }

                $from.val(start.format('YYYY-MM-DD')).prop('disabled', true);
                $to.val(end.format('YYYY-MM-DD')).prop('disabled', true);
            }

            // Initialize with "Today"
            setQuickRange('today');

            // Quick range buttons
            $('.range-quick').on('click', function() {
                $('.range-quick').removeClass('active');
                $(this).addClass('active');
                setQuickRange($(this).data('range'));
                table.ajax.reload();
                updateExportLink();
            });

            // Manual date change (only when custom)
            $from.add($to).on('change', function() {
                if ($('.range-quick.active').data('range') === 'custom') {
                    table.ajax.reload();
                    updateExportLink();
                }
            });

            function updateExportLink() {
                const params = new URLSearchParams({
                    export: 'csv',
                    from_date: $from.val() || '',
                    to_date: $to.val() || ''
                });
                $('#exportCsv').attr('href', "{{ route('subscription.report') }}?" + params.toString());
            }

            // ------ DataTable ------
            const table = $('#file-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                order: [
                    [1, 'desc']
                ], // order by period end by default
                ajax: {
                    url: "{{ route('subscription.report') }}",
                    data: function(d) {
                        d.from_date = $from.val();
                        d.to_date = $to.val();
                        d.range = $('.range-quick.active').data('range'); // optional on server
                    },
                    dataSrc: function(json) {
                        // KPI values (ensure numbers)
                        const total = parseFloat(json.total_price || 0);
                        const renewTotal = parseFloat(json.renew_user_price || 0);
                        const newUserTotal = parseFloat(json.new_user_price || 0);

                        $('#totalPrice').text('₹' + total.toFixed(2));
                        $('#renewCustomerTotalPrice').text('₹' + renewTotal.toFixed(2));
                        $('#newUserTotalPrice').text('₹' + newUserTotal.toFixed(2));

                        return json.data || [];
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load data. Please try again.'
                        });
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: true,
                        render: function(data, type, row) {
                            const user = row.user || {};
                            const address = (user.address_details || {});
                            const userId = user.userid ?? null;

                            const tooltip = `
                                <strong>Apartment:</strong> ${address.apartment_name || 'N/A'}<br>
                                <strong>No:</strong> ${address.apartment_flat_plot || 'N/A'}
                            `.trim();

                            const modalId =
                                `addressModal${userId || Math.random().toString(36).slice(2)}`;

                            const viewBtn = userId ?
                                `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-info btn-sm" title="View Customer">
                                        <i class="fas fa-eye"></i>
                                   </a>` :
                                '';

                            const addressHtml = `
                                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="fas fa-home me-2"></i>Address Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-1"><strong>Address:</strong> ${(address.apartment_flat_plot || '')}, ${(address.apartment_name || '')}, ${(address.locality || '')}</p>
                                                <p class="mb-1"><strong>Landmark:</strong> ${(address.landmark || '')}</p>
                                                <p class="mb-1"><strong>Pin Code:</strong> ${(address.pincode || '')}</p>
                                                <p class="mb-1"><strong>City:</strong> ${(address.city || '')}</p>
                                                <p class="mb-0"><strong>State:</strong> ${(address.state || '')}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            return `
                                <div class="customer-meta" data-bs-toggle="tooltip" data-bs-html="true" title="${tooltip}">
                                    <div class="fw-semibold">${user.name || 'N/A'}</div>
                                    <small><i class="bi bi-telephone me-1"></i>${user.mobile_number || 'N/A'}</small>
                                    <div class="mt-2 d-flex gap-2">
                                        ${viewBtn}
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#${modalId}" title="Show Address">
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
                        render: function(data, type, row) {
                            // support {start, end} OR simple date on server
                            const start = (row.purchase_date && row.purchase_date.start) ? row
                                .purchase_date.start : (row.start_date || data?.start || data);
                            const end = (row.purchase_date && row.purchase_date.end) ? row
                                .purchase_date.end : (row.end_date || data?.end || data);

                            const s = start ? moment(start).format('DD MMM YYYY') : '-';
                            const e = end ? moment(end).format('DD MMM YYYY') : '-';
                            return `${s} — ${e}`;
                        }
                    },
                    {
                        data: 'duration',
                        className: 'text-center',
                        render: function(data) {
                            const days = parseInt(data || 0, 10);
                            return `${isNaN(days) ? 0 : days} days`;
                        }
                    },
                    {
                        data: 'price',
                        className: 'text-end',
                        render: function(data) {
                            const val = parseFloat(data || 0);
                            return '₹' + (isNaN(val) ? '0.00' : val.toFixed(2));
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data) {
                            const s = (data || '').toString().toLowerCase();
                            let cls = 'status-active';
                            if (s === 'paused') cls = 'status-paused';
                            if (s === 'expired') cls = 'status-expired';
                            if (s === 'resume') cls = 'status-resume';
                            return `<span class="status-badge ${cls}">${s || 'n/a'}</span>`;
                        }
                    }
                ]
            });

            // Go / Refresh
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
                updateExportLink();
            });
            $('#refreshBtn').on('click', function() {
                table.ajax.reload(null, false);
            });

            // Re-init tooltips after table draw
            $('#file-datatable').on('draw.dt', function() {
                // Dispose any existing
                $('[data-bs-toggle="tooltip"]').each(function() {
                    const t = bootstrap.Tooltip.getInstance(this);
                    if (t) t.dispose();
                });
                // Init new
                $('[data-bs-toggle="tooltip"]').tooltip({
                    html: true,
                    boundary: 'window',
                    trigger: 'hover'
                });
            });

            // Initial export link
            updateExportLink();
        });
    </script>
@endsection
