{{-- Extend your base layout --}}
@extends('admin.layouts.apps')

{{-- SECTION: Styles --}}
@section('styles')
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
        :root{
            --pf-subtle:#F5F7FC; --pf-surface:#FFF; --pf-border:#E7E9F1;
            --pf-text:#111827; --pf-muted:#6B7280;
            --pf-primary:#7570FE; --pf-primary-600:#6B63F5; --pf-accent:#8151FB;
            --pf-action:#FF4D61; --pf-action-600:#E43E52;
            --pf-success-bg:#E8F5EE; --pf-success-fg:#166534;
            --pf-warning-bg:#FFF7E6; --pf-warning-fg:#92400E;
            --pf-danger-bg:#FDE8E8; --pf-danger-fg:#B91C1C;
            --pf-info-bg:#E6F0FF; --pf-info-fg:#1D4ED8;
            --pf-shadow-sm:0 2px 10px rgba(15,23,42,.05);
            --pf-shadow-md:0 10px 24px rgba(2,6,23,.08);
        }
        body{
            font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif !important;
            color:var(--pf-text);
            background:
              radial-gradient(1200px 600px at 10% -10%, rgba(129,81,251,.08), transparent 60%),
              radial-gradient(1200px 600px at 110% 10%, rgba(117,112,254,.10), transparent 55%),
              var(--pf-subtle);
        }
        .kpi-card{border-radius:16px;padding:18px 20px;background:linear-gradient(180deg,#fff,#FAFBFF);
            box-shadow:var(--pf-shadow-md);border:1px solid var(--pf-border);transition:transform .18s,box-shadow .18s;height:100%}
        .kpi-card:hover{transform:translateY(-2px);box-shadow:0 16px 30px rgba(2,6,23,.1)}
        .kpi-label{font-size:.85rem;color:var(--pf-muted);margin-bottom:6px}
        .kpi-value{font-weight:800;font-size:1.35rem;color:var(--pf-text)}
        .kpi-icon{color:var(--pf-accent);opacity:.9}

        .filter-card{border-radius:16px;border:1px solid var(--pf-border);box-shadow:var(--pf-shadow-sm);background:var(--pf-surface)}
        .range-btns .btn{border-radius:999px;border-color:var(--pf-border);color:var(--pf-primary);background:#fff}
        .range-btns .btn:hover{border-color:var(--pf-primary)}
        .range-btns .btn.active{background:var(--pf-primary);border-color:var(--pf-primary);color:#fff}
        .btn-go{background:var(--pf-action);border-color:var(--pf-action);color:#fff}
        .btn-go:hover{background:var(--pf-action-600);border-color:var(--pf-action-600);color:#fff}
        .form-control{border-radius:10px;border-color:var(--pf-border)}
        .form-label{color:var(--pf-muted);font-weight:600}

        .table{border-color:var(--pf-border)!important}
        .table thead.table-light th{background:#F3F6FF!important;color:#0F172A!important;border-bottom:1px solid var(--pf-border)!important;font-weight:700}
        table.dataTable tbody td{vertical-align:middle}
        .table-hover tbody tr:hover{background:#F8FBFF}

        .status-badge{padding:.38rem .65rem;border-radius:999px;font-size:.75rem;font-weight:700;text-transform:capitalize;border:1px solid transparent}
        .status-active{background:var(--pf-success-bg);color:var(--pf-success-fg);border-color:#cdebd7}
        .status-paused{background:var(--pf-warning-bg);color:var(--pf-warning-fg);border-color:#ffe3b3}
        .status-expired{background:var(--pf-danger-bg);color:var(--pf-danger-fg);border-color:#fac6c6}
        .status-resume{background:var(--pf-info-bg);color:var(--pf-info-fg);border-color:#c9daff}

        .customer-meta small{color:var(--pf-muted)}
        .action-btns .btn{border-radius:10px}
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
                            <div class="kpi-label">Total Subscription Revenue</div>
                            <div class="kpi-value" id="totalPrice">₹0</div>
                        </div>
                        <i class="bi bi-cash-coin fs-3 kpi-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Renew Customers Revenue</div>
                            <div class="kpi-value" id="renewCustomerTotalPrice">₹0</div>
                        </div>
                        <i class="bi bi-arrow-repeat fs-3 kpi-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">New Subscriptions Revenue</div>
                            <div class="kpi-value" id="newUserTotalPrice">₹0</div>
                        </div>
                        <i class="bi bi-person-plus fs-3 kpi-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card filter-card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Quick Range</label>
                        <div class="btn-group range-btns flex-wrap" role="group" aria-label="Quick Ranges">
                            <button type="button" class="btn btn-outline-primary btn-sm range-quick active" data-range="today">
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
                                <label for="from_date" class="form-label">From</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" disabled>
                            </div>
                            <div class="col-6">
                                <label for="to_date" class="form-label">To</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-1 d-grid">
                        <button type="button" id="searchBtn" class="btn btn-go">
                            <i class="fas fa-search me-1"></i> Go
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card" style="border-radius:16px; border:1px solid var(--pf-border); box-shadow: var(--pf-shadow-sm);">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="file-datatable" class="table table-bordered table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:280px">Customer</th>
                                <th>Purchase Period</th>
                                <th>Duration</th>
                                <th>Payment Method</th>   {{-- NEW --}}
                                <th class="text-end">Price</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button id="refreshBtn" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <a href="{{ route('subscription.report') }}?export=csv" id="exportCsv" class="btn btn-outline-primary btn-sm">
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
        $(function () {
            moment.locale('en-gb');

            const $from = $('#from_date');
            const $to   = $('#to_date');

            function setQuickRange(rangeKey) {
                const today = moment().startOf('day');
                let start = today.clone(), end = today.clone();

                switch (rangeKey) {
                    case 'today': start=today.clone(); end=today.clone(); break;
                    case 'week':  start=moment().startOf('isoWeek'); end=moment().endOf('isoWeek'); break;
                    case 'month': start=moment().startOf('month');   end=moment().endOf('month');   break;
                    case 'year':  start=moment().startOf('year');    end=moment().endOf('year');    break;
                    case 'custom':
                        $from.prop('disabled', false);
                        $to.prop('disabled', false);
                        if (!$from.val()) $from.val(today.format('YYYY-MM-DD'));
                        if (!$to.val())   $to.val(today.format('YYYY-MM-DD'));
                        return;
                }
                $from.val(start.format('YYYY-MM-DD')).prop('disabled', true);
                $to.val(end.format('YYYY-MM-DD')).prop('disabled', true);
            }
            setQuickRange('today');

            const table = $('#file-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                order: [[1, 'desc']],
                ajax: {
                    url: "{{ route('subscription.report') }}",
                    data: function (d) {
                        d.from_date = $from.val();
                        d.to_date   = $to.val();
                        d.range     = $('.range-quick.active').data('range');
                    },
                    dataSrc: function (json) {
                        const total        = parseFloat(json.total_price || 0);
                        const renewTotal   = parseFloat(json.renew_user_price || 0);
                        const newUserTotal = parseFloat(json.new_user_price || 0);
                        $('#totalPrice').text('₹' + total.toFixed(2));
                        $('#renewCustomerTotalPrice').text('₹' + renewTotal.toFixed(2));
                        $('#newUserTotalPrice').text('₹' + newUserTotal.toFixed(2));
                        return json.data || [];
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load data. Please try again.' });
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: true,
                        render: function (data, type, row) {
                            const user    = row.user || {};
                            const address = (user.address_details || {});
                            const userId  = user.userid ?? null;

                            const tooltip = `
                                <strong>Apartment:</strong> ${address.apartment_name || 'N/A'}<br>
                                <strong>No:</strong> ${address.apartment_flat_plot || 'N/A'}
                            `.trim();

                            const modalId = `addressModal${userId || Math.random().toString(36).slice(2)}`;

                            const viewBtn = userId
                                ? `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-primary btn-sm" title="View Customer">
                                        <i class="fas fa-eye"></i>
                                   </a>`
                                : '';

                            const addressHtml = `
                                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header text-white" style="background: var(--pf-primary);">
                                            <h5 class="modal-title"><i class="fas fa-home me-2"></i>Address Details</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-1"><strong>Address:</strong> ${(address.apartment_flat_plot || '')}, ${(address.apartment_name || '')}, ${(address.locality || '')}</p>
                                            <p class="mb-1"><strong>Landmark:</strong> ${(address.landmark || '')}</p>
                                            <p class="mb-1"><strong>Pin Code:</strong> ${(address.pincode || '')}</p>
                                            <p class="mb-1"><strong>City:</strong> ${(address.city || '')}</p>
                                            <p class="mb-0"><strong>State:</strong> ${(address.state || '')}</p>
                                        </div>
                                    </div></div>
                                </div>
                            `;

                            return `
                                <div class="customer-meta" data-bs-toggle="tooltip" data-bs-html="true" title="${tooltip}">
                                    <div class="fw-semibold">${user.name || 'N/A'}</div>
                                    <small><i class="bi bi-telephone me-1"></i>${user.mobile_number || 'N/A'}</small>
                                    <div class="mt-2 d-flex gap-2 action-btns">
                                        ${viewBtn}
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#${modalId}" title="Show Address">
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
                        render: function (data, type, row) {
                            const start = (row.purchase_date && row.purchase_date.start) ? row.purchase_date.start : (row.start_date || data?.start || data);
                            const end   = (row.purchase_date && row.purchase_date.end)   ? row.purchase_date.end   : (row.end_date   || data?.end   || data);
                            const s = start ? moment(start).format('DD MMM YYYY') : '-';
                            const e = end   ? moment(end).format('DD MMM YYYY')   : '-';
                            return `${s} — ${e}`;
                        }
                    },
                    {
                        data: 'duration',   // now server sends inclusive days; also guard on UI
                        className: 'text-center',
                        render: function (data, type, row) {
                            let days = parseInt(data || 0, 10);
                            if (!days || isNaN(days)) {
                                // Fallback: compute inclusive on client if missing
                                const start = row?.purchase_date?.start;
                                const end   = row?.purchase_date?.end;
                                if (start && end && moment(start).isValid() && moment(end).isValid()) {
                                    days = moment(end).diff(moment(start), 'days') + 1; // inclusive
                                }
                            }
                            return `${isNaN(days) ? 0 : days} days`;
                        }
                    },
                    {
                        data: 'payment_method',   // NEW column
                        className: 'text-nowrap',
                        render: function (val) {
                            return (val && val !== '') ? val : '—';
                        }
                    },
                    {
                        data: 'price',
                        className: 'text-end',
                        render: function (data) {
                            const val = parseFloat(data || 0);
                            return '₹' + (isNaN(val) ? '0.00' : val.toFixed(2));
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function (data) {
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

            // Quick range buttons
            $('.range-quick').on('click', function () {
                $('.range-quick').removeClass('active');
                $(this).addClass('active');
                setQuickRange($(this).data('range'));
                table.ajax.reload();
                updateExportLink();
            });

            // Manual date change (only when custom)
            $('#from_date, #to_date').on('change', function () {
                if ($('.range-quick.active').data('range') === 'custom') {
                    table.ajax.reload();
                    updateExportLink();
                }
            });

            // Go / Refresh
            $('#searchBtn').on('click', function () {
                table.ajax.reload();
                updateExportLink();
            });
            $('#refreshBtn').on('click', function () {
                table.ajax.reload(null, false);
            });

            function updateExportLink() {
                const params = new URLSearchParams({
                    export: 'csv',
                    from_date: $('#from_date').val() || '',
                    to_date: $('#to_date').val() || ''
                });
                $('#exportCsv').attr('href', "{{ route('subscription.report') }}?" + params.toString());
            }
            updateExportLink();

            // Re-init tooltips after table draw
            $('#file-datatable').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').each(function(){
                    const t = bootstrap.Tooltip.getInstance(this);
                    if (t) t.dispose();
                });
                $('[data-bs-toggle="tooltip"]').tooltip({ html: true, boundary: 'window', trigger: 'hover' });
            });
        });
    </script>
@endsection
