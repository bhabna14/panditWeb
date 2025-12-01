{{-- Extend your base layout --}}
@extends('admin.layouts.apps')

{{-- SECTION: Styles --}}
@section('styles')
    <!-- CSRF Token (if you really need it here; usually goes in <head> of layout) -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    {{-- Fonts: Poppins (page) + Nunito Sans (tables) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

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
        :root {
            /* Core palette inspired by your first page / pickups design */
            --brand-blue: #e9f2ff;
            --brand-blue-edge: #cfe0ff;
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;

            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #6b7280;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow-sm: 0 4px 12px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius-lg: 16px;

            --accent: #0ea5e9;
            --accent-strong: #0369a1;
            --accent-soft: #e0f2fe;
            --accent-border: #bae6fd;

            --success-bg: #dcfce7;
            --success-fg: #166534;
            --warning-bg: #fef9c3;
            --warning-fg: #92400e;
            --danger-bg: #fee2e2;
            --danger-fg: #b91c1c;
            --info-bg: #dbeafe;
            --info-fg: #1d4ed8;
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            color: var(--text);
            background: var(--bg);
        }

        .container-page {
            max-width: 1320px;
        }

        /* KPI cards (soft, elevated) */
        .kpi-card {
            border-radius: 14px;
            padding: 14px 18px;
            background: var(--card);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--ring);
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .14);
            background: #f9fafb;
        }

        .kpi-label {
            font-size: .85rem;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .kpi-value {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--text);
        }

        .kpi-icon {
            color: var(--accent-strong);
            opacity: .95;
        }

        /* Toolbar (quick range + dates) same family as pickups/ledger */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius-lg);
            padding: .85rem 1rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: minmax(0, 1.4fr) auto;
            align-items: center;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.1rem;
        }

        .toolbar-left {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
        }

        .toolbar-right {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: flex-end;
        }

        .date-range {
            display: flex;
            gap: .45rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: .84rem;
        }

        .date-range span.label {
            font-weight: 600;
        }

        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 160px;
        }

        .date-range input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.25);
        }

        .btn-chip {
            border-radius: 999px;
            border: 1px solid var(--ring);
            background: #fff;
            color: #0f172a;
            font-weight: 500;
            font-size: .8rem;
            padding: .4rem .95rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-chip::before {
            content: '⦿';
            font-size: .7rem;
            opacity: .5;
        }

        .btn-chip:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
        }

        .btn-chip.active {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border-color: #020617;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.active::before {
            content: '✓';
            opacity: .9;
        }

        .btn-chip.apply-btn {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.apply-btn::before {
            content: '↻';
            font-size: .75rem;
            opacity: .8;
        }

        /* Summary band, similar to pickups/ledger */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 18px;
            padding: .9rem 1.2rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .band h3 {
            margin: 0;
            font-size: .98rem;
            font-weight: 600;
            color: var(--header-text);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band h3 span.label {
            font-size: .75rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.08);
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .09em;
        }

        .band-sub {
            font-size: .85rem;
            color: var(--muted);
        }

        .chips-row {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .chip-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .chip-pill span.icon {
            font-size: .9rem;
        }

        .chip-pill.green {
            background: var(--chip-green);
            color: var(--chip-green-text);
            border-color: #c9f0d6;
        }

        .chip-pill.orange {
            background: var(--chip-orange);
            color: var(--chip-orange-text);
            border-color: #ffd9b3;
        }

        .chip-pill.blue {
            background: var(--chip-blue);
            color: var(--chip-blue-text);
            border-color: #bae6fd;
        }

        /* Workbook shell around tabs + DataTables */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .workbook-head {
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .workbook-title {
            font-weight: 600;
            color: #111827;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .workbook-title::before {
            content: '📊';
            font-size: 1.1rem;
        }

        .workbook-sub {
            font-size: .84rem;
            color: #4b5563;
        }

        .workbook-body {
            padding: 1rem 1.1rem 1.1rem;
        }

        /* Tabs styled as pills, like first-page chips */
        .subs-tabs {
            background: #e5e7eb;
            border-radius: 999px;
            padding: .25rem;
        }

        .subs-tabs .nav-link {
            border-radius: 999px;
            border: 0;
            margin: 0 .15rem;
            font-weight: 600;
            color: var(--muted);
            padding: .45rem .9rem;
            font-size: .86rem;
        }

        .subs-tabs .nav-link i {
            font-size: .95rem;
        }

        .subs-tabs .nav-link.active {
            background: #ffffff;
            color: var(--accent-strong);
            box-shadow: var(--shadow-sm);
        }

        /* Tables */
        .table {
            border-color: var(--table-border) !important;
            font-size: .88rem;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .table thead.table-light th {
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft)) !important;
            color: var(--table-head-text) !important;
            border-bottom: none !important;
            font-weight: 600;
            font-size: .72rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        table.dataTable tbody td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: var(--table-hover);
        }

        /* Status pills */
        .status-badge {
            padding: .3rem .7rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
            text-transform: capitalize;
            border: 1px solid transparent;
        }

        .status-active {
            background: var(--success-bg);
            color: var(--success-fg);
            border-color: #bbf7d0;
        }

        .status-paused {
            background: var(--warning-bg);
            color: var(--warning-fg);
            border-color: #fde68a;
        }

        .status-expired {
            background: var(--danger-bg);
            color: var(--danger-fg);
            border-color: #fca5a5;
        }

        .status-resume {
            background: var(--info-bg);
            color: var(--info-fg);
            border-color: #bfdbfe;
        }

        .customer-meta small {
            color: var(--muted);
        }

        .action-btns .btn {
            border-radius: 999px;
            font-size: 0.75rem;
        }

        .mono {
            font-variant-numeric: tabular-nums;
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .toolbar-left {
                justify-content: flex-start;
            }

            .toolbar-right {
                justify-content: flex-start;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endsection

{{-- SECTION: Content --}}
@section('content')
    <div class="container container-page py-3">

        {{-- Page header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-0" style="font-weight:600;">Subscription Revenue — Report</h4>
                <div class="small text-muted">Track renew vs new subscriptions over time</div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm" style="border-radius:999px;">
                ← Back
            </a>
        </div>

        {{-- KPI row --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Total Subscription Revenue</div>
                            <div class="kpi-value mono" id="totalPrice">₹0.00</div>
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
                            <div class="kpi-value mono" id="renewCustomerTotalPrice">₹0.00</div>
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
                            <div class="kpi-value mono" id="newUserTotalPrice">₹0.00</div>
                        </div>
                        <i class="bi bi-person-plus fs-3 kpi-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toolbar: quick ranges + date pickers (first-page style) --}}
        <div class="toolbar mb-3">
            <div class="toolbar-left">
                <div class="date-range">
                    <span class="label">From</span>
                    <input type="date" id="from_date" name="from_date" disabled>
                </div>
                <div class="date-range">
                    <span class="label">To</span>
                    <input type="date" id="to_date" name="to_date" disabled>
                </div>
            </div>
            <div class="toolbar-right">
                <button type="button" class="btn-chip range-quick active" data-range="today">
                    <i class="bi bi-calendar-day"></i><span>Today</span>
                </button>
                <button type="button" class="btn-chip range-quick" data-range="week">
                    <i class="bi bi-calendar-week"></i><span>This Week</span>
                </button>
                <button type="button" class="btn-chip range-quick" data-range="month">
                    <i class="bi bi-calendar3"></i><span>This Month</span>
                </button>
                <button type="button" class="btn-chip range-quick" data-range="year">
                    <i class="bi bi-calendar4-week"></i><span>This Year</span>
                </button>
                <button type="button" class="btn-chip range-quick" data-range="custom">
                    <i class="bi bi-sliders"></i><span>Custom</span>
                </button>
                <button type="button" id="searchBtn" class="btn-chip apply-btn">
                    <i class="fas fa-search"></i><span>Apply</span>
                </button>
            </div>
        </div>

        {{-- Band summary (range label + short info) --}}
        <div class="band mb-3">
            <h3>
                <span id="bandRangeLabel">Today</span>
                <span class="label">Subscription Summary</span>
            </h3>
            <div class="band-sub">
                Revenue shown below is filtered by the selected date range and split into renew vs new subscriptions.
            </div>
            <div class="chips-row">
                <span class="chip-pill green">
                    <span class="icon">💰</span>
                    <span>Total Revenue</span>
                    <span class="mono" id="bandTotalPrice">₹0.00</span>
                </span>
                <span class="chip-pill orange">
                    <span class="icon">🔁</span>
                    <span>Renew</span>
                    <span class="mono" id="bandRenewPrice">₹0.00</span>
                </span>
                <span class="chip-pill blue">
                    <span class="icon">🆕</span>
                    <span>New</span>
                    <span class="mono" id="bandNewPrice">₹0.00</span>
                </span>
            </div>
        </div>

        {{-- Workbook: tabs + tables --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Subscription Lists — Detailed</div>
                    <div class="workbook-sub">
                        Switch between renew and new subscriptions. Data is server-side loaded & filter aware.
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button id="refreshBtn" class="btn btn-outline-secondary btn-sm" style="border-radius:999px;">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <a href="{{ route('subscription.report') }}?export=csv" id="exportCsv"
                       class="btn btn-outline-primary btn-sm" style="border-radius:999px;">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </a>
                </div>
            </div>

            <div class="workbook-body">
                {{-- Tabs --}}
                <ul class="nav nav-pills nav-fill subs-tabs mb-3" id="subsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="renew-tab" data-bs-toggle="tab"
                                data-bs-target="#renew-pane" type="button" role="tab"
                                aria-controls="renew-pane" aria-selected="true" data-type="renew">
                            <i class="bi bi-arrow-repeat me-1"></i>Renew Subscriptions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="new-tab" data-bs-toggle="tab"
                                data-bs-target="#new-pane" type="button" role="tab"
                                aria-controls="new-pane" aria-selected="false" data-type="new">
                            <i class="bi bi-person-plus me-1"></i>New Subscriptions
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="subsTabContent">
                    {{-- Renew list --}}
                    <div class="tab-pane fade show active" id="renew-pane" role="tabpanel" aria-labelledby="renew-tab">
                        <div class="table-responsive">
                            <table id="renew-table" class="table table-bordered table-hover w-100">
                                <thead class="table-light">
                                <tr>
                                    <th style="min-width:280px">Customer</th>
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
                    <div class="tab-pane fade" id="new-pane" role="tabpanel" aria-labelledby="new-tab">
                        <div class="table-responsive">
                            <table id="new-table" class="table table-bordered table-hover w-100">
                                <thead class="table-light">
                                <tr>
                                    <th style="min-width:280px">Customer</th>
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
            </div> {{-- /.workbook-body --}}
        </div>{{-- /.workbook --}}
    </div>
@endsection

{{-- SECTION: Scripts --}}
@section('scripts')
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

            const $from = $('#from_date');
            const $to = $('#to_date');
            const $bandRangeLabel = $('#bandRangeLabel');

            function updateBandRangeLabel(rangeKey, start, end) {
                if (rangeKey === 'custom' && $from.val() && $to.val()) {
                    const s = moment($from.val()).format('DD MMM YYYY');
                    const e = moment($to.val()).format('DD MMM YYYY');
                    $bandRangeLabel.text(`${s} – ${e}`);
                    return;
                }

                switch (rangeKey) {
                    case 'today':
                        $bandRangeLabel.text('Today');
                        break;
                    case 'week':
                        $bandRangeLabel.text('This Week');
                        break;
                    case 'month':
                        $bandRangeLabel.text('This Month');
                        break;
                    case 'year':
                        $bandRangeLabel.text('This Year');
                        break;
                    default:
                        if (start && end) {
                            $bandRangeLabel.text(
                                `${moment(start).format('DD MMM YYYY')} – ${moment(end).format('DD MMM YYYY')}`
                            );
                        } else {
                            $bandRangeLabel.text('Custom Range');
                        }
                }
            }

            function setQuickRange(rangeKey) {
                const today = moment().startOf('day');
                let start = today.clone(),
                    end = today.clone();

                switch (rangeKey) {
                    case 'today':
                        start = today.clone();
                        end = today.clone();
                        $from.prop('disabled', true);
                        $to.prop('disabled', true);
                        break;
                    case 'week':
                        start = moment().startOf('isoWeek');
                        end = moment().endOf('isoWeek');
                        $from.prop('disabled', true);
                        $to.prop('disabled', true);
                        break;
                    case 'month':
                        start = moment().startOf('month');
                        end = moment().endOf('month');
                        $from.prop('disabled', true);
                        $to.prop('disabled', true);
                        break;
                    case 'year':
                        start = moment().startOf('year');
                        end = moment().endOf('year');
                        $from.prop('disabled', true);
                        $to.prop('disabled', true);
                        break;
                    case 'custom':
                        $from.prop('disabled', false);
                        $to.prop('disabled', false);
                        if (!$from.val()) $from.val(today.format('YYYY-MM-DD'));
                        if (!$to.val()) $to.val(today.format('YYYY-MM-DD'));
                        updateBandRangeLabel('custom');
                        return;
                }

                $from.val(start.format('YYYY-MM-DD'));
                $to.val(end.format('YYYY-MM-DD'));
                updateBandRangeLabel(rangeKey, start, end);
            }

            // Default range = today
            setQuickRange('today');

            // Common columns used for both tables
            const columns = [
                {
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

                        const modalId = `addressModal${userId || Math.random().toString(36).slice(2)}`;

                        const viewBtn = userId
                            ? `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-primary btn-sm" title="View Customer">
                                    <i class="fas fa-eye"></i>
                               </a>`
                            : '';

                        const addressHtml = `
                            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header text-white" style="background: var(--accent-strong);">
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
                    render: function(data, type, row) {
                        const start = (row.purchase_date && row.purchase_date.start)
                            ? row.purchase_date.start
                            : (row.start_date || data?.start || data);
                        const end = (row.purchase_date && row.purchase_date.end)
                            ? row.purchase_date.end
                            : (row.end_date || data?.end || data);
                        const s = start ? moment(start).format('DD MMM YYYY') : '-';
                        const e = end ? moment(end).format('DD MMM YYYY') : '-';
                        return `${s} — ${e}`;
                    }
                },
                {
                    data: 'duration',
                    className: 'text-center',
                    render: function(data, type, row) {
                        let days = parseInt(data || 0, 10);
                        if (!days || isNaN(days)) {
                            const start = row?.purchase_date?.start;
                            const end = row?.purchase_date?.end;
                            if (start && end && moment(start).isValid() && moment(end).isValid()) {
                                days = moment(end).diff(moment(start), 'days') + 1;
                            }
                        }
                        return `${isNaN(days) ? 0 : days} days`;
                    }
                },
                {
                    data: 'payment_method',
                    className: 'text-nowrap',
                    render: function(val) {
                        return (val && val !== '') ? val : '—';
                    }
                },
                {
                    data: 'price',
                    className: 'text-end mono',
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

                            // Mirror into band chips too
                            $('#bandTotalPrice').text('₹' + total.toFixed(2));
                            $('#bandRenewPrice').text('₹' + renewTotal.toFixed(2));
                            $('#bandNewPrice').text('₹' + newTotal.toFixed(2));

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

            function updateExportLink() {
                const activeType = $('.subs-tabs .nav-link.active').data('type');
                const params = new URLSearchParams({
                    export: 'csv',
                    from_date: $from.val() || '',
                    to_date: $to.val() || '',
                    type: activeType || ''
                });
                $('#exportCsv').attr('href', "{{ route('subscription.report') }}?" + params.toString());
            }

            function reloadAllTables() {
                renewTable.ajax.reload();
                newTable.ajax.reload();
                updateExportLink();
            }

            // Quick range buttons styling + logic
            $('.range-quick').on('click', function() {
                $('.range-quick').removeClass('active');
                $(this).addClass('active');
                const rk = $(this).data('range');
                setQuickRange(rk);
                reloadAllTables();
            });

            // Custom date change should reload when custom active
            $('#from_date, #to_date').on('change', function() {
                if ($('.range-quick.active').data('range') === 'custom') {
                    updateBandRangeLabel('custom');
                    reloadAllTables();
                }
            });

            // Apply button
            $('#searchBtn').on('click', function() {
                reloadAllTables();
            });

            // Refresh (only active tab)
            $('#refreshBtn').on('click', function() {
                const activeType = $('.subs-tabs .nav-link.active').data('type');
                if (activeType === 'renew') {
                    renewTable.ajax.reload(null, false);
                } else {
                    newTable.ajax.reload(null, false);
                }
            });

            // Export link respects tab + range
            updateExportLink();
            $('#subsTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                updateExportLink();
            });

            // Tooltips re-init after each draw
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
