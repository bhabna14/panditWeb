{{-- resources/views/admin/reports/subscription-report.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <!-- Fonts & Core CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        :root {
            --bg-page: #f3f4f6;
            --bg-surface: #ffffff;
            --border-subtle: #e5e7eb;
            --ink: #111827;
            --muted: #6b7280;
            --brand: #0ea5e9;
            --brand-deep: #0369a1;
            --accent: #22c55e;
            --accent-deep: #15803d;
            --danger: #ef4444;
            --shadow-soft: 0 8px 22px rgba(15, 23, 42, .08);
            --shadow-subtle: 0 2px 8px rgba(15, 23, 42, .04);
        }

        body {
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif !important;
            background: var(--bg-page);
            color: var(--ink);
        }

        .page-shell {
            padding-inline: .25rem;
        }

        @media (min-width: 992px) {
            .page-shell {
                padding-inline: .75rem;
            }
        }

        .page-header-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-top: .5rem;
            margin-bottom: 1rem;
        }

        .page-header-title {
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .page-header-sub {
            font-size: .85rem;
            color: var(--muted);
        }

        .page-header-pill {
            border-radius: 999px;
            padding: .35rem .8rem;
            border: 1px dashed rgba(15, 23, 42, .15);
            background: rgba(14, 165, 233, .04);
            font-size: .8rem;
            color: var(--muted);
        }

        /* KPI cards */
        .kpi-card {
            border-radius: 16px;
            padding: 14px 16px;
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-subtle);
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
            background: #f9fafb;
        }

        .kpi-label {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            margin-bottom: .2rem;
            font-weight: 600;
        }

        .kpi-value {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--ink);
        }

        .kpi-chip {
            border-radius: 999px;
            padding: .15rem .6rem;
            font-size: .7rem;
            font-weight: 600;
            color: var(--brand-deep);
            background: #e0f2fe;
        }

        .kpi-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: var(--brand-deep);
            box-shadow: 0 0 0 1px rgba(59, 130, 246, .2);
        }

        .kpi-icon-wrap.accent {
            background: #ecfdf3;
            color: var(--accent-deep);
            box-shadow: 0 0 0 1px rgba(34, 197, 94, .18);
        }

        .kpi-icon-wrap.danger {
            background: #fef2f2;
            color: var(--danger);
            box-shadow: 0 0 0 1px rgba(248, 113, 113, .18);
        }

        /* Filter card */
        .filter-card {
            border-radius: 16px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-surface);
            box-shadow: var(--shadow-subtle);
            margin-bottom: 1.25rem;
        }

        .filter-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .75rem;
        }

        .filter-title {
            font-weight: 600;
            font-size: .9rem;
        }

        .filter-helper {
            font-size: .8rem;
            color: var(--muted);
        }

        .range-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .range-chip-btn {
            border-radius: 999px !important;
            border: 1px solid var(--border-subtle) !important;
            font-size: .8rem;
            font-weight: 500;
            color: var(--muted);
            background: #ffffff;
            padding: .3rem .9rem;
        }

        .range-chip-btn i {
            font-size: .9rem;
        }

        .range-chip-btn.active {
            background: var(--brand);
            border-color: var(--brand) !important;
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(14, 165, 233, .35);
        }

        .form-control {
            border-radius: 12px;
            border-color: var(--border-subtle);
            box-shadow: none !important;
            font-size: .86rem;
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 1px rgba(14, 165, 233, .25) !important;
        }

        .form-label {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .btn-apply {
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), var(--brand-deep));
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: .9rem;
            box-shadow: 0 8px 18px rgba(37, 99, 235, .35);
        }

        .btn-apply:hover {
            opacity: .95;
            color: #fff;
        }

        /* Main card / tabs */
        .main-card {
            border-radius: 16px;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-soft);
            background: var(--bg-surface);
        }

        .subs-tabs {
            background: #e5e7eb;
            border-radius: 999px;
            padding: .25rem;
        }

        .subs-tabs .nav-link {
            border-radius: 999px;
            border: 0;
            font-weight: 600;
            font-size: .85rem;
            color: var(--muted);
            padding: .4rem .9rem;
        }

        .subs-tabs .nav-link i {
            font-size: .9rem;
        }

        .subs-tabs .nav-link.active {
            background: #ffffff;
            color: var(--brand-deep);
            box-shadow: var(--shadow-subtle);
        }

        .table {
            border-color: var(--border-subtle) !important;
            font-size: .85rem;
        }

        .table thead.table-light th {
            background: #f9fafb !important;
            color: #111827 !important;
            border-bottom: 1px solid var(--border-subtle) !important;
            font-weight: 600;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        table.dataTable tbody td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: #f3f4f6;
        }

        .status-badge {
            padding: .32rem .7rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            text-transform: capitalize;
            border: 1px solid transparent;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .status-paused {
            background: #fef9c3;
            color: #92400e;
            border-color: #fde68a;
        }

        .status-expired {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        .status-resume {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .customer-meta small {
            color: var(--muted);
        }

        .action-btns .btn {
            border-radius: 999px;
            font-size: .7rem;
        }

        .footer-actions .btn {
            border-radius: 999px;
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid page-shell">

        {{-- HEADER --}}
        <div class="page-header-bar">
            <div>
                <div class="page-header-title">
                    <span class="badge rounded-pill text-bg-info-subtle border border-info-subtle">
                        <i class="bi bi-graph-up-arrow me-1"></i> Subscription Report
                    </span>
                </div>
                <div class="page-header-sub">
                    Track subscription revenue for renewals and new customers across flexible date ranges.
                </div>
            </div>
            <div class="page-header-pill">
                <i class="bi bi-info-circle me-1"></i>
                Use quick ranges (Today, This Week, Month, Year) or switch to a custom period.
            </div>
        </div>

        {{-- KPI ROW --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label">Total Subscription Revenue</span>
                        <span class="kpi-icon-wrap">
                            <i class="bi bi-cash-coin"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="kpi-value" id="totalPrice">₹0</div>
                        <span class="kpi-chip">
                            All subscriptions
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label">Renew Customers Revenue</span>
                        <span class="kpi-icon-wrap accent">
                            <i class="bi bi-arrow-repeat"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="kpi-value" id="renewCustomerTotalPrice">₹0</div>
                        <span class="kpi-chip">
                            Existing customers
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label">New Subscriptions Revenue</span>
                        <span class="kpi-icon-wrap danger">
                            <i class="bi bi-person-plus"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="kpi-value" id="newUserTotalPrice">₹0</div>
                        <span class="kpi-chip">
                            First-time customers
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="filter-card-head">
                    <div>
                        <div class="filter-title">
                            <i class="bi bi-funnel me-1"></i> Filter by Date Range
                        </div>
                        <div class="filter-helper">
                            Choose a quick range or switch to custom dates for detailed analysis.
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Quick Ranges</label>
                        <div class="range-chips">
                            <button type="button"
                                    class="btn range-chip-btn range-quick active"
                                    data-range="today">
                                <i class="bi bi-calendar-day me-1"></i>Today
                            </button>
                            <button type="button"
                                    class="btn range-chip-btn range-quick"
                                    data-range="week">
                                <i class="bi bi-calendar-week me-1"></i>This Week
                            </button>
                            <button type="button"
                                    class="btn range-chip-btn range-quick"
                                    data-range="month">
                                <i class="bi bi-calendar3 me-1"></i>This Month
                            </button>
                            <button type="button"
                                    class="btn range-chip-btn range-quick"
                                    data-range="year">
                                <i class="bi bi-calendar4-week me-1"></i>This Year
                            </button>
                            <button type="button"
                                    class="btn range-chip-btn range-quick"
                                    data-range="custom">
                                <i class="bi bi-sliders me-1"></i>Custom
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="from_date" class="form-label">From</label>
                                <input type="date" id="from_date" name="from_date"
                                       class="form-control" disabled>
                            </div>
                            <div class="col-6">
                                <label for="to_date" class="form-label">To</label>
                                <input type="date" id="to_date" name="to_date"
                                       class="form-control" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-2 d-grid">
                        <button type="button" id="searchBtn" class="btn btn-apply">
                            <i class="fas fa-search me-1"></i> Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT: TABS + TABLES --}}
        <div class="card main-card mt-3 mb-2">
            <div class="card-body">
                <ul class="nav nav-pills nav-fill subs-tabs mb-3" id="subsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                                id="renew-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#renew-pane"
                                type="button"
                                role="tab"
                                aria-controls="renew-pane"
                                aria-selected="true"
                                data-type="renew">
                            <i class="bi bi-arrow-repeat me-1"></i> Renew Subscriptions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link"
                                id="new-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#new-pane"
                                type="button"
                                role="tab"
                                aria-controls="new-pane"
                                aria-selected="false"
                                data-type="new">
                            <i class="bi bi-person-plus me-1"></i> New Subscriptions
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="subsTabContent">
                    {{-- Renew list --}}
                    <div class="tab-pane fade show active"
                         id="renew-pane"
                         role="tabpanel"
                         aria-labelledby="renew-tab">
                        <div class="table-responsive">
                            <table id="renew-table" class="table table-bordered table-hover w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:260px">Customer</th>
                                        <th>Purchase Period</th>
                                        <th>Duration</th>
                                        <th>Payment Method</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- New list --}}
                    <div class="tab-pane fade"
                         id="new-pane"
                         role="tabpanel"
                         aria-labelledby="new-tab">
                        <div class="table-responsive">
                            <table id="new-table" class="table table-bordered table-hover w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:260px">Customer</th>
                                        <th>Purchase Period</th>
                                        <th>Duration</th>
                                        <th>Payment Method</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 footer-actions">
                    <button id="refreshBtn" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <a href="{{ route('subscription.report') }}?export=csv"
                       id="exportCsv"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/en-gb.min.js"></script>

    <script>
        $(function() {
            moment.locale('en-gb');

            const $from = $('#from_date');
            const $to   = $('#to_date');

            // ---- Quick range handler ----
            function setQuickRange(rangeKey) {
                const today = moment().startOf('day');
                let start = today.clone(),
                    end   = today.clone();

                switch (rangeKey) {
                    case 'today':
                        start = today.clone();
                        end   = today.clone();
                        break;
                    case 'week':
                        start = moment().startOf('isoWeek');
                        end   = moment().endOf('isoWeek');
                        break;
                    case 'month':
                        start = moment().startOf('month');
                        end   = moment().endOf('month');
                        break;
                    case 'year':
                        start = moment().startOf('year');
                        end   = moment().endOf('year');
                        break;
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

            // Default: today
            setQuickRange('today');

            // ---- Columns shared for both tables ----
            const columns = [
                {
                    data: null,
                    orderable: false,
                    searchable: true,
                    render: function(data, type, row) {
                        const user    = row.user || {};
                        const address = user.address_details || {};
                        const userId  = user.userid ?? null;

                        const tooltip = `
                            <strong>Apartment:</strong> ${address.apartment_name || 'N/A'}<br>
                            <strong>No:</strong> ${address.apartment_flat_plot || 'N/A'}
                        `.trim();

                        const modalId = `addr-${userId || Math.random().toString(36).slice(2)}`;

                        const viewBtn = userId
                            ? `<a href="/admin/show-customer/${userId}/details"
                                   class="btn btn-outline-primary btn-sm"
                                   title="View Customer">
                                   <i class="fas fa-eye"></i>
                               </a>`
                            : '';

                        const addressHtml = `
                            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header text-white" style="background:#0ea5e9;">
                                            <h5 class="modal-title">
                                                <i class="fas fa-home me-2"></i>Address Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-1">
                                                <strong>Address:</strong>
                                                ${(address.apartment_flat_plot || '')},
                                                ${(address.apartment_name || '')},
                                                ${(address.locality || '')}
                                            </p>
                                            <p class="mb-1"><strong>Landmark:</strong> ${(address.landmark || '')}</p>
                                            <p class="mb-1"><strong>Pin Code:</strong> ${(address.pincode || '')}</p>
                                            <p class="mb-1"><strong>City:</strong> ${(address.city || '')}</p>
                                            <p class="mb-0"><strong>State:</strong> ${(address.state || '')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        return `
                            <div class="customer-meta"
                                 data-bs-toggle="tooltip"
                                 data-bs-html="true"
                                 title="${tooltip}">
                                <div class="fw-semibold">${user.name || 'N/A'}</div>
                                <small>
                                    <i class="bi bi-telephone me-1"></i>${user.mobile_number || 'N/A'}
                                </small>
                                <div class="mt-2 d-flex gap-2 action-btns">
                                    ${viewBtn}
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#${modalId}"
                                            title="Show Address">
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
                        const start = (row.purchase_date && row.purchase_date.start)
                            ? row.purchase_date.start
                            : (row.start_date || data?.start || data);
                        const end = (row.purchase_date && row.purchase_date.end)
                            ? row.purchase_date.end
                            : (row.end_date || data?.end || data);

                        const s = start ? moment(start).format('DD MMM YYYY') : '-';
                        const e = end   ? moment(end).format('DD MMM YYYY')   : '-';
                        return `${s} — ${e}`;
                    }
                },
                {
                    data: 'duration',
                    className: 'text-center',
                    render: function(data, type, row) {
                        let days = parseInt(data || 0, 10);
                        if (!days || isNaN(days)) {
                            const s = row?.purchase_date?.start;
                            const e = row?.purchase_date?.end;
                            if (s && e && moment(s).isValid() && moment(e).isValid()) {
                                days = moment(e).diff(moment(s), 'days') + 1; // inclusive
                            }
                        }
                        return `${isNaN(days) ? 0 : days} days`;
                    }
                },
                {
                    data: 'payment_method',
                    className: 'text-nowrap',
                    render: val => (val && val !== '') ? val : '—'
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
                        if (s === 'paused')  cls = 'status-paused';
                        if (s === 'expired') cls = 'status-expired';
                        if (s === 'resume')  cls = 'status-resume';
                        return `<span class="status-badge ${cls}">${s || 'n/a'}</span>`;
                    }
                }
            ];

            function buildDataTable(selector, type) {
                return $(selector).DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    order: [[1, 'desc']],
                    ajax: {
                        url: "{{ route('subscription.report') }}",
                        data: function(d) {
                            d.from_date = $from.val();
                            d.to_date   = $to.val();
                            d.range     = $('.range-quick.active').data('range');
                            d.type      = type; // 'new' or 'renew'
                        },
                        dataSrc: function(json) {
                            const total      = parseFloat(json.total_price || 0);
                            const renewTotal = parseFloat(json.renew_user_price || 0);
                            const newTotal   = parseFloat(json.new_user_price || 0);

                            $('#totalPrice').text('₹' + total.toFixed(2));
                            $('#renewCustomerTotalPrice').text('₹' + renewTotal.toFixed(2));
                            $('#newUserTotalPrice').text('₹' + newTotal.toFixed(2));

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
                    columns: columns
                });
            }

            const renewTable = buildDataTable('#renew-table', 'renew');
            const newTable   = buildDataTable('#new-table', 'new');

            function reloadAllTables() {
                renewTable.ajax.reload();
                newTable.ajax.reload();
                updateExportLink();
            }

            // Quick range buttons
            $('.range-quick').on('click', function() {
                $('.range-quick').removeClass('active');
                $(this).addClass('active');
                setQuickRange($(this).data('range'));
                reloadAllTables();
            });

            // Manual date change (only when custom)
            $('#from_date, #to_date').on('change', function() {
                if ($('.range-quick.active').data('range') === 'custom') {
                    reloadAllTables();
                }
            });

            // Apply button
            $('#searchBtn').on('click', function() {
                reloadAllTables();
            });

            // Refresh button (only active tab)
            $('#refreshBtn').on('click', function() {
                const type = $('.subs-tabs .nav-link.active').data('type');
                if (type === 'renew') {
                    renewTable.ajax.reload(null, false);
                } else {
                    newTable.ajax.reload(null, false);
                }
            });

            // Export link respects range + active tab
            function updateExportLink() {
                const type = $('.subs-tabs .nav-link.active').data('type') || '';
                const params = new URLSearchParams({
                    export: 'csv',
                    from_date: $from.val() || '',
                    to_date: $to.val() || '',
                    type: type
                });
                $('#exportCsv').attr('href', "{{ route('subscription.report') }}?" + params.toString());
            }

            updateExportLink();

            // On tab change, update export link
            $('#subsTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                updateExportLink();
            });

            // Re-init tooltips for customer cells after draw
            $('#renew-table, #new-table').on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    const t = bootstrap.Tooltip.getInstance(this);
                    if (t) t.dispose();
                });
                $('[data-bs-toggle="tooltip"]').tooltip({
                    html: true,
                    boundary: 'window',
                    trigger: 'hover'
                });
            });
        });
    </script>
@endsection
