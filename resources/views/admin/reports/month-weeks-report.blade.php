{{-- resources/views/admin/reports/month-weeks-report.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-bg: #eaf3ff;
            --ink: #1d2433;
            --muted: #6b7280;
            --surface: #fff;
            --border: #e7ebf3;
            --shadow: 0 8px 26px rgba(2, 8, 20, .06);
            --tab1: #6366f1;
            --tab2: #06b6d4;

            --col-date: #92400e;
            --col-finance: #047857;
            --col-vendor: #7c3aed;
            --col-rider: #0f766e;

            --tbl-head-from: #0ea5e9;
            --tbl-head-to: #6366f1;
            --tbl-head-text: #f9fafb;
            --tbl-row-bg: rgba(248, 250, 252, 0.96);
            --tbl-row-hover: #eff6ff;
            --tbl-border: rgba(148, 163, 184, 0.6);

            --chip: #eff6ff;
        }

        body, .container-fluid, .table, .btn {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif
        }

        .page-wrap { padding: 8px }

        .hero {
            background: linear-gradient(180deg, var(--brand-bg), #f1f2f3);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow)
        }

        .kpi {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--shadow);
            padding: 16px;
            height: 100%
        }

        .kpi .label {
            font-size: .78rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px
        }

        .kpi .value {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
            color: var(--ink)
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px
        }

        @media(max-width:992px) { .grid-3 { grid-template-columns: 1fr } }

        .nu-tabs {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--shadow)
        }

        .nav-pills.nu .nav-link {
            color: #334155;
            font-weight: 700;
            border-radius: 999px;
            padding: 10px 16px;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-pills.nu .nav-link .badge { font-weight: 700 }
        .nav-pills.nu .nav-link:hover { transform: translateY(-1px) }

        .nav-pills.nu .nav-link.active {
            color: #fff;
            box-shadow: 0 8px 22px rgba(2, 8, 20, .12)
        }
        .nav-pills.nu .nav-link[data-color="violet"].active {
            background: linear-gradient(90deg, var(--tab1), #8b5cf6)
        }
        .nav-pills.nu .nav-link[data-color="cyan"].active {
            background: linear-gradient(90deg, var(--tab2), #3b82f6)
        }
        .nav-pills.nu .nav-link[data-color="violet"] {
            border: 1px solid #e9e7ff;
            background: #f7f7ff
        }
        .nav-pills.nu .nav-link[data-color="cyan"] {
            border: 1px solid #e1f6ff;
            background: #f0fbff
        }

        .tab-pane { padding: 16px }

        .accordion-item {
            border: 1px solid var(--border) !important;
            border-radius: 14px !important;
            overflow: hidden;
            box-shadow: var(--shadow);
            background: #fff
        }

        .accordion-button {
            font-weight: 600;
            padding: 14px 20px;
            margin-bottom: 0;
            border-bottom: 1px solid var(--border);
        }
        .accordion-button:not(.collapsed) {
            background: linear-gradient(180deg, #f6faff, #f2f6ff);
            color: var(--ink);
        }

        .week-header {
            position: sticky;
            top: 56px;
            z-index: 5;
            background: #fff;
            border-bottom: 1px solid var(--border)
        }

        .table-card { border-radius: 0 0 14px 14px; overflow: clip }
        .table th, .table td { text-align: center !important; vertical-align: middle }
        .money { font-variant-numeric: tabular-nums }

        .totals-row {
            font-weight: 800;
            font-size: 14px;
            background: linear-gradient(90deg, #fee2e2, #fffbeb);
            border-top: 2px solid #fecaca;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .6rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 700;
            background: var(--chip);
            color: #1e40af;
            border: 1px solid #dbe9ff
        }
        .chip.income { background: #eafff3; color: #0d5f3c; border-color: #d9f7e7 }
        .chip.exp { background: #fff3ea; color: #8a3a0c; border-color: #ffe1cc }
        .chip.vendor-fund { background: #ecfeff; color: #0369a1; border-color: #bae6fd; }
        .chip.balance { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }

        th.col-date, td.col-date, th.col-dow, td.col-dow { color: var(--col-date); font-weight: 600; }
        th.col-finance, td.col-finance { color: var(--col-finance); font-weight: 600; }
        th.col-vendor, td.col-vendor { color: var(--col-vendor); font-weight: 600; }
        th.col-rider, td.col-rider { color: var(--col-rider); font-weight: 600; }

        .colorful-metrics-table {
            border-collapse: separate !important;
            border-spacing: 0;
            background: var(--tbl-row-bg);
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--tbl-border);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
            margin-bottom: 0.75rem;
        }
        .colorful-metrics-table thead th {
            background: linear-gradient(90deg, var(--tbl-head-from), var(--tbl-head-to)) !important;
            color: var(--tbl-head-text) !important;
            border-bottom: none !important;
            border-top: none !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            padding-top: 9px;
            padding-bottom: 9px;
        }
        .colorful-metrics-table thead:first-child tr:first-child th:first-child {
            border-top-left-radius: 18px;
            padding-top: 65px;
        }
        .colorful-metrics-table thead:first-child tr:first-child th:last-child {
            border-top-right-radius: 18px;
        }
        .colorful-metrics-table thead th.col-date,
        .colorful-metrics-table thead th.col-dow {
            background: rgba(56, 189, 248, 0.5) !important;
            color: #0f172a !important;
        }

        .colorful-metrics-table thead th.income-sub-head { background: #16a34a !important; color: #f9fafb !important; }
        .colorful-metrics-table thead th.income-cust-head { background: #22c55e !important; color: #f9fafb !important; }
        .colorful-metrics-table thead th.purchase-head { background: #16a34a !important; color: #f9fafb !important; }
        .colorful-metrics-table thead th.vendorfund-head { background: #0284c7 !important; color: #f9fafb !important; }

        .colorful-metrics-table thead tr:nth-child(2) th.col-vendor.vendor-odd,
        .colorful-metrics-table thead tr:nth-child(2) th.col-vendor.vendor-even {
            background: linear-gradient(90deg, rgba(236, 72, 153, 0.95), rgba(244, 114, 182, 0.95)) !important;
            color: #f9fafb !important;
        }
        .colorful-metrics-table thead tr:nth-child(2) th.col-rider {
            background: rgba(129, 140, 248, 0.95) !important;
            color: #f9fafb !important;
        }

        .colorful-metrics-table.table-striped>tbody>tr:nth-of-type(odd)>*,
        .colorful-metrics-table.table-striped>tbody>tr:nth-of-type(even)>* {
            background-color: var(--tbl-row-bg) !important;
        }
        .colorful-metrics-table tbody tr {
            background: var(--tbl-row-bg) !important;
            transition: background .18s ease, transform .1s ease, box-shadow .1s ease;
        }
        .colorful-metrics-table tbody tr:hover {
            background: var(--tbl-row-hover) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.10);
        }
        .colorful-metrics-table tbody td {
            border-top: 1px solid rgba(148, 163, 184, 0.30) !important;
            font-size: 13px;
            padding-bottom: 6px;
            padding-top: 6px;
        }

        .colorful-metrics-table tbody td:nth-child(1) { font-weight: 600; color:#7987A1; }
        .colorful-metrics-table tbody td:nth-child(2) { font-weight: 500; color: var(--col-date); }
        .colorful-metrics-table tbody td:nth-child(3) { font-weight: 800; color: #16a34a; }
        .colorful-metrics-table tbody td:nth-child(4) { font-weight: 800; color: #15803d; }
        .colorful-metrics-table tbody td:nth-child(5) { font-weight: 700; color: #16a34a; }
        .colorful-metrics-table tbody td:nth-child(6) { font-weight: 700; color: #0369a1; }

        .colorful-metrics-table tbody td:nth-child(7),
        .colorful-metrics-table tbody td:nth-child(8),
        .colorful-metrics-table tbody td:nth-child(9),
        .colorful-metrics-table tbody td:nth-child(10) {
            text-align: center;
            font-weight: 600;
        }
        .colorful-metrics-table tbody td:nth-child(7) { background: rgba(34, 197, 94, 0.16) !important; color: #15803d; }
        .colorful-metrics-table tbody td:nth-child(8) { background: rgba(56, 189, 248, 0.16) !important; color: #0369a1; }
        .colorful-metrics-table tbody td:nth-child(9) { background: rgba(245, 158, 11, 0.18) !important; color: #b45309; }
        .colorful-metrics-table tbody td:nth-child(10) { background: rgba(244, 114, 182, 0.18) !important; color: #be185d; }

        .colorful-metrics-table tbody td:nth-child(n+11) { font-weight: 600; color: #045e06; }
        .colorful-metrics-table tbody td.col-vendor.vendor-odd { background: rgba(129, 140, 248, 0.10) !important; }
        .colorful-metrics-table tbody td.col-vendor.vendor-even { background: rgba(244, 114, 182, 0.10) !important; }

        /* ===== CLICKABLE INCOME ===== */
        .income-click {
            appearance: none;
            border: 0;
            background: transparent;
            padding: 0;
            margin: 0;
            cursor: pointer;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            text-decoration: none;
        }
        .income-click:focus {
            outline: none;
            box-shadow: 0 0 0 .25rem rgba(59, 130, 246, 0.20);
            border-radius: 6px;
        }
        .income-click.sub { color: #166534; }
        .income-click.cust { color: #14532d; }

        /* ===== CLICKABLE VENDOR PAYMENTS (FULL CELL) ===== */
        .vendor-pay-click{
            appearance:none;
            border:0;
            background:transparent;
            padding:0;
            margin:0;
            cursor:pointer;
            font-weight:800;
            color:inherit;
            font-variant-numeric: tabular-nums;
            display:block;
            width:100%;
            text-align:center;
        }
        .vendor-pay-click:focus{
            outline:none;
            box-shadow: 0 0 0 .25rem rgba(124, 58, 237, 0.18);
            border-radius: 6px;
        }

        /* ===== Income modal list ===== */
        .income-user-list .list-group-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .income-user-list .left {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .income-user-list .uname {
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .income-user-list .uid {
            font-size: .82rem;
            color: #64748b;
        }
        /* NEW: payment method line */
        .income-user-list .pm {
            font-size: .82rem;
            color: #0f766e;
            font-weight: 600;
        }
        /* NEW: payment date line */
        .income-user-list .pdate {
            font-size: .82rem;
            color: #7c2d12;
            font-weight: 600;
        }
        .income-user-list .amt {
            flex: 0 0 auto;
            font-weight: 800;
        }
    </style>
@endsection

@section('content')
@php
    if (!function_exists('safe_count')) {
        function safe_count($value): int {
            return is_countable($value) ? count($value) : 0;
        }
    }

    $weeks = $weeks ?? [];
    $monthDays = $monthDays ?? [];
    $vendorNameToId = $vendorNameToId ?? [];
@endphp

<div class="container-fluid page-wrap">
    <div class="hero mb-3">
        <form class="row g-3 align-items-end" method="get" action="{{ route('admin.ops-report') }}">
            <div class="col-md-2">
                <label class="form-label mb-1">Year</label>
                <select class="form-select" name="year">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label mb-1">Month</label>
                <select class="form-select" name="month">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($m == $month)>
                            {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary">Apply</button>
                <button type="button" class="btn btn-outline-secondary" id="expandAll">Expand all</button>
                <button type="button" class="btn btn-outline-secondary" id="collapseAll">Collapse all</button>
            </div>

            <div class="col-md-3 d-flex flex-column justify-content-end align-items-md-end">
                <div class="text-muted">Range</div>
                <div class="fw-semibold">{{ $monthStart->format('d M Y') }} → {{ $monthEnd->format('d M Y') }}</div>
            </div>
        </form>

        <div class="mt-3 grid-3">
            <div class="kpi">
                <div class="label">Total Income (Month)</div>
                <div class="h4 value">₹{{ number_format($monthTotals['income_total'] ?? 0) }}</div>

                <div class="small text-muted mt-1 d-flex flex-wrap gap-2 align-items-center">
                    <button type="button"
                            class="income-click sub"
                            data-income-modal="1"
                            data-title="Subscription Income (Month)"
                            data-users='@json($monthTotals["subscription_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                        Sub ₹{{ number_format($monthTotals['subscription_income'] ?? 0) }}
                    </button>

                    <button type="button"
                            class="income-click cust"
                            data-income-modal="1"
                            data-title="Customize Income (Month)"
                            data-users='@json($monthTotals["customize_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                        Cust ₹{{ number_format($monthTotals['customize_income'] ?? 0) }}
                    </button>
                </div>
            </div>

            <div class="kpi">
                <div class="label">Total Expenditure (Month)</div>
                <div class="h4 value">₹{{ number_format($monthTotals['expenditure'] ?? 0) }}</div>
            </div>

            <div class="kpi">
                <div class="label">Available Balance (Month)</div>
                <div class="h4 value">₹{{ number_format($monthTotals['available_balance'] ?? 0) }}</div>
                <div class="small text-muted mt-1">
                    Vendor Fund ₹{{ number_format($monthTotals['vendor_fund'] ?? 0) }}
                    − Expense ₹{{ number_format($monthTotals['expenditure'] ?? 0) }}
                </div>
            </div>
        </div>
    </div>

    <div class="nu-tabs p-3">
        <ul class="nav nav-pills nu mb-3 row g-2" id="reportTabs" role="tablist">
            <li class="nav-item col-12 col-lg-6" role="presentation">
                <button class="nav-link active w-100 d-flex justify-content-center align-items-center gap-2 text-center"
                        id="tab-weeks" data-bs-toggle="pill" data-bs-target="#pane-weeks" type="button" role="tab"
                        aria-controls="pane-weeks" aria-selected="true" data-color="cyan">
                    Weekly Report
                    <span class="badge bg-light text-dark ms-1">{{ safe_count($weeks) }} Weeks</span>
                </button>
            </li>
            <li class="nav-item col-12 col-lg-6" role="presentation">
                <button class="nav-link w-100 d-flex justify-content-center align-items-center gap-2 text-center"
                        id="tab-month" data-bs-toggle="pill" data-bs-target="#pane-month" type="button" role="tab"
                        aria-controls="pane-month" aria-selected="false" data-color="violet">
                    Month Report
                    <span class="badge bg-light text-dark ms-1">
                        {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('M Y') }}
                    </span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="reportTabsContent">
            {{-- ======================= WEEKS TAB ======================= --}}
            <div class="tab-pane fade show active" id="pane-weeks" role="tabpanel" aria-labelledby="tab-weeks" tabindex="0">
                <div class="accordion" id="weeksAccordion">
                    @foreach ($weeks as $i => $w)
                        @php
                            $weekId = 'wk' . $i;
                            $title = $w['start']->format('d M') . ' - ' . $w['end']->format('d M');
                            $weekVendorColumns = $w['vendorColumns'] ?? $vendorColumns;
                            $weekVendorCount = max(safe_count($weekVendorColumns), 1);
                            $weekFrom = $w['start']->toDateString();
                            $weekTo   = $w['end']->toDateString();
                        @endphp

                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header week-header" id="heading-{{ $weekId }}">
                                <button class="accordion-button collapsed d-flex justify-content-between" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $weekId }}"
                                        aria-expanded="false" aria-controls="collapse-{{ $weekId }}">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="me-1">
                                            Week {{ $i + 1 }}
                                            <small class="text-muted">({{ $title }})</small>
                                        </span>

                                        <span class="chip income">
                                            Income ₹{{ number_format($w['totals']['income_total'] ?? 0) }}
                                            <small class="text-muted">
                                                (S {{ number_format($w['totals']['subscription_income'] ?? 0) }}
                                                · C {{ number_format($w['totals']['customize_income'] ?? 0) }})
                                            </small>
                                        </span>

                                        <span class="chip exp">Expense ₹{{ number_format($w['totals']['expenditure'] ?? 0) }}</span>
                                        <span class="chip vendor-fund">Vendor Fund ₹{{ number_format($w['totals']['vendor_fund'] ?? 0) }}</span>
                                        <span class="chip balance">Avail Bal ₹{{ number_format($w['totals']['available_balance'] ?? 0) }}</span>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse-{{ $weekId }}" class="accordion-collapse collapse"
                                 aria-labelledby="heading-{{ $weekId }}" data-bs-parent="#weeksAccordion">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive table-card">
                                        <table class="table table-sm table-striped table-hover align-middle mb-2 colorful-metrics-table">
                                            <thead>
                                            <tr>
                                                <th rowspan="2" class="col-date">Date</th>
                                                <th rowspan="2" class="col-dow">Day</th>

                                                <th colspan="4" class="col-finance">Finance</th>
                                                <th colspan="4">Customer</th>

                                                <th colspan="{{ $weekVendorCount }}" class="col-vendor">Vendor Report</th>
                                                <th colspan="{{ 1 + max(safe_count($deliveryCols), 1) }}" class="col-rider">Rider Deliveries</th>
                                            </tr>

                                            <tr>
                                                <th class="col-finance income-sub-head">SubIncm</th>
                                                <th class="col-finance income-cust-head">CusIncm</th>
                                                <th class="col-finance purchase-head">Purch</th>
                                                <th class="col-finance vendorfund-head">VendF</th>

                                                <th>Renew</th>
                                                <th>New</th>
                                                <th>Pause</th>
                                                <th>Customize</th>

                                                {{-- Vendor headers clickable --}}
                                                @forelse($weekVendorColumns as $v)
                                                    @php
                                                        $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                        $vid = $vendorNameToId[$v] ?? '';
                                                    @endphp
                                                    <th class="col-vendor {{ $vendorColClass }}" title="{{ $v }}">
                                                        <button type="button"
                                                                class="vendor-pay-click vendor-pay-open"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#vendorItemsModal"
                                                                data-vendor-name="{{ $v }}"
                                                                data-vendor-id="{{ $vid }}"
                                                                data-from="{{ $weekFrom }}"
                                                                data-to="{{ $weekTo }}">
                                                            {{ \Illuminate\Support\Str::substr($v, 0, 5) }}
                                                        </button>
                                                    </th>
                                                @empty
                                                    <th>—</th>
                                                @endforelse

                                                <th class="col-rider">Dlvy</th>
                                                @forelse($deliveryCols as $r)
                                                    <th title="{{ $r }}" class="col-rider">
                                                        {{ \Illuminate\Support\Str::substr($r, 0, 4) }}
                                                    </th>
                                                @empty
                                                    <th>—</th>
                                                @endforelse
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @foreach ($w['days'] as $d)
                                                @php
                                                    $dayFrom = \Carbon\Carbon::parse($d['date'])->toDateString();
                                                    $dayTo   = $dayFrom;
                                                @endphp
                                                <tr>
                                                    <td class="col-date">{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                    <td class="text-muted col-dow">{{ $d['dow'] }}</td>

                                                    <td class="money col-finance">
                                                        <button type="button"
                                                                class="income-click sub"
                                                                data-income-modal="1"
                                                                data-title="Subscription Income ({{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }})"
                                                                data-users='@json($d["finance"]["subscription_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                            ₹{{ number_format($d['finance']['subscription_income'] ?? 0) }}
                                                        </button>
                                                    </td>

                                                    <td class="money col-finance">
                                                        <button type="button"
                                                                class="income-click cust"
                                                                data-income-modal="1"
                                                                data-title="Customize Income ({{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }})"
                                                                data-users='@json($d["finance"]["customize_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                            ₹{{ number_format($d['finance']['customize_income'] ?? 0) }}
                                                        </button>
                                                    </td>

                                                    <td class="money col-finance">₹{{ number_format($d['finance']['expenditure'] ?? 0) }}</td>
                                                    <td class="money col-finance">₹{{ number_format($d['finance']['vendor_fund'] ?? 0) }}</td>

                                                    <td><span class="badge bg-success-subtle text-success">{{ $d['customer']['renew'] }}</span></td>
                                                    <td><span class="badge bg-primary-subtle text-primary">{{ $d['customer']['new'] }}</span></td>
                                                    <td><span class="badge bg-warning-subtle text-warning">{{ $d['customer']['pause'] }}</span></td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $d['customer']['customize'] }}</span></td>

                                                    {{-- Vendor cells clickable --}}
                                                    @if (safe_count($weekVendorColumns))
                                                        @foreach ($weekVendorColumns as $v)
                                                            @php
                                                                $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                                $vid = $vendorNameToId[$v] ?? '';
                                                                $amt = (float)($d['vendors'][$v] ?? 0);
                                                            @endphp
                                                            <td class="money col-vendor {{ $vendorColClass }}">
                                                                <button type="button"
                                                                        class="vendor-pay-click vendor-pay-open"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#vendorItemsModal"
                                                                        data-vendor-name="{{ $v }}"
                                                                        data-vendor-id="{{ $vid }}"
                                                                        data-from="{{ $dayFrom }}"
                                                                        data-to="{{ $dayTo }}">
                                                                    ₹{{ number_format($amt) }}
                                                                </button>
                                                            </td>
                                                        @endforeach
                                                    @else
                                                        <td class="text-muted">—</td>
                                                    @endif

                                                    <td class="fw-semibold col-rider">{{ $d['total_delivery'] }}</td>
                                                    @foreach ($deliveryCols as $r)
                                                        <td class="col-rider">{{ $d['riders'][$r] ?? 0 }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach

                                            {{-- Week Totals --}}
                                            <tr class="totals-row">
                                                <td colspan="2" class="col-date">Week Total</td>

                                                <td class="money col-finance">
                                                    <button type="button"
                                                            class="income-click sub"
                                                            data-income-modal="1"
                                                            data-title="Subscription Income (Week {{ $i + 1 }}: {{ $title }})"
                                                            data-users='@json($w["totals"]["subscription_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                        ₹{{ number_format($w['totals']['subscription_income'] ?? 0) }}
                                                    </button>
                                                </td>

                                                <td class="money col-finance">
                                                    <button type="button"
                                                            class="income-click cust"
                                                            data-income-modal="1"
                                                            data-title="Customize Income (Week {{ $i + 1 }}: {{ $title }})"
                                                            data-users='@json($w["totals"]["customize_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                        ₹{{ number_format($w['totals']['customize_income'] ?? 0) }}
                                                    </button>
                                                </td>

                                                <td class="money col-finance">₹{{ number_format($w['totals']['expenditure'] ?? 0) }}</td>
                                                <td class="money col-finance">₹{{ number_format($w['totals']['vendor_fund'] ?? 0) }}</td>

                                                <td>{{ $w['totals']['renew'] ?? 0 }}</td>
                                                <td>{{ $w['totals']['new'] ?? 0 }}</td>
                                                <td>{{ $w['totals']['pause'] ?? 0 }}</td>
                                                <td>{{ $w['totals']['customize'] ?? 0 }}</td>

                                                {{-- Week total vendor cells clickable --}}
                                                @if (safe_count($weekVendorColumns))
                                                    @foreach ($weekVendorColumns as $v)
                                                        @php
                                                            $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                            $vid = $vendorNameToId[$v] ?? '';
                                                            $amt = (float)($w['totals']['vendors'][$v] ?? 0);
                                                        @endphp
                                                        <td class="money col-vendor {{ $vendorColClass }}">
                                                            <button type="button"
                                                                    class="vendor-pay-click vendor-pay-open"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#vendorItemsModal"
                                                                    data-vendor-name="{{ $v }}"
                                                                    data-vendor-id="{{ $vid }}"
                                                                    data-from="{{ $weekFrom }}"
                                                                    data-to="{{ $weekTo }}">
                                                                ₹{{ number_format($amt) }}
                                                            </button>
                                                        </td>
                                                    @endforeach
                                                @else
                                                    <td class="text-muted">—</td>
                                                @endif

                                                <td class="fw-semibold col-rider">{{ $w['totals']['total_delivery'] ?? 0 }}</td>
                                                @foreach ($deliveryCols as $r)
                                                    <td class="col-rider">{{ $w['totals']['riders'][$r] ?? 0 }}</td>
                                                @endforeach
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ======================= MONTH TAB ======================= --}}
            <div class="tab-pane fade" id="pane-month" role="tabpanel" aria-labelledby="tab-month" tabindex="0">
                @php
                    $monthAllId = 'month-all-days';
                    $monthFrom = $monthStart->toDateString();
                    $monthTo   = $monthEnd->toDateString();
                @endphp

                <div class="accordion mb-3" id="monthAllAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $monthAllId }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $monthAllId }}" aria-expanded="false"
                                    aria-controls="collapse-{{ $monthAllId }}">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span>Month (All Days)</span>
                                    <span class="chip income">
                                        Income ₹{{ number_format($monthTotals['income_total'] ?? 0) }}
                                        <small class="text-muted">
                                            (S {{ number_format($monthTotals['subscription_income'] ?? 0) }}
                                            · C {{ number_format($monthTotals['customize_income'] ?? 0) }})
                                        </small>
                                    </span>
                                    <span class="chip exp">Expense ₹{{ number_format($monthTotals['expenditure'] ?? 0) }}</span>
                                    <span class="chip vendor-fund">Vendor Fund ₹{{ number_format($monthTotals['vendor_fund'] ?? 0) }}</span>
                                    <span class="chip balance">Avail Bal ₹{{ number_format($monthTotals['available_balance'] ?? 0) }}</span>
                                    <span class="chip">Deliveries {{ $monthTotals['total_delivery'] ?? 0 }}</span>
                                </div>
                            </button>
                        </h2>

                        <div id="collapse-{{ $monthAllId }}" class="accordion-collapse collapse"
                             aria-labelledby="heading-{{ $monthAllId }}" data-bs-parent="#monthAllAccordion">
                            <div class="accordion-body p-0">
                                <div class="table-responsive table-card">
                                    <table class="table table-sm table-striped table-hover align-middle mb-0 colorful-metrics-table">
                                        <thead>
                                        <tr>
                                            <th rowspan="2" class="col-date">Date</th>
                                            <th rowspan="2" class="col-dow">Day</th>

                                            <th colspan="4" class="col-finance">Finance</th>
                                            <th colspan="4">Customer</th>

                                            <th colspan="{{ max(safe_count($vendorColumns), 1) }}" class="col-vendor">Vendor Report</th>
                                            <th colspan="{{ 1 + max(safe_count($deliveryCols), 1) }}" class="col-rider">Rider Deliveries</th>
                                        </tr>

                                        <tr>
                                            <th class="col-finance income-sub-head">SubIncm</th>
                                            <th class="col-finance income-cust-head">CusIncm</th>
                                            <th class="col-finance purchase-head">Purch</th>
                                            <th class="col-finance vendorfund-head">VendF</th>

                                            <th>Renew</th>
                                            <th>New</th>
                                            <th>Pause</th>
                                            <th>Customize</th>

                                            {{-- Month vendor headers clickable --}}
                                            @forelse($vendorColumns as $v)
                                                @php
                                                    $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                    $vid = $vendorNameToId[$v] ?? '';
                                                @endphp
                                                <th class="col-vendor {{ $vendorColClass }}" title="{{ $v }}">
                                                    <button type="button"
                                                            class="vendor-pay-click vendor-pay-open"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#vendorItemsModal"
                                                            data-vendor-name="{{ $v }}"
                                                            data-vendor-id="{{ $vid }}"
                                                            data-from="{{ $monthFrom }}"
                                                            data-to="{{ $monthTo }}">
                                                        {{ \Illuminate\Support\Str::substr($v, 0, 5) }}
                                                    </button>
                                                </th>
                                            @empty
                                                <th>—</th>
                                            @endforelse

                                            <th class="col-rider">Dlvy</th>
                                            @forelse($deliveryCols as $r)
                                                <th title="{{ $r }}" class="col-rider">
                                                    {{ \Illuminate\Support\Str::substr($r, 0, 4) }}
                                                </th>
                                            @empty
                                                <th>—</th>
                                            @endforelse
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach ($monthDays as $d)
                                            @php
                                                $dayFrom = \Carbon\Carbon::parse($d['date'])->toDateString();
                                                $dayTo   = $dayFrom;
                                            @endphp
                                            <tr>
                                                <td class="col-date">{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                <td class="text-muted col-dow">{{ $d['dow'] }}</td>

                                                <td class="money col-finance">
                                                    <button type="button"
                                                            class="income-click sub"
                                                            data-income-modal="1"
                                                            data-title="Subscription Income ({{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }})"
                                                            data-users='@json($d["finance"]["subscription_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                        ₹{{ number_format($d['finance']['subscription_income'] ?? 0) }}
                                                    </button>
                                                </td>

                                                <td class="money col-finance">
                                                    <button type="button"
                                                            class="income-click cust"
                                                            data-income-modal="1"
                                                            data-title="Customize Income ({{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }})"
                                                            data-users='@json($d["finance"]["customize_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                        ₹{{ number_format($d['finance']['customize_income'] ?? 0) }}
                                                    </button>
                                                </td>

                                                <td class="money col-finance">₹{{ number_format($d['finance']['expenditure'] ?? 0) }}</td>
                                                <td class="money col-finance">₹{{ number_format($d['finance']['vendor_fund'] ?? 0) }}</td>

                                                <td><span class="badge bg-success-subtle text-success">{{ $d['customer']['renew'] }}</span></td>
                                                <td><span class="badge bg-primary-subtle text-primary">{{ $d['customer']['new'] }}</span></td>
                                                <td><span class="badge bg-warning-subtle text-warning">{{ $d['customer']['pause'] }}</span></td>
                                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $d['customer']['customize'] }}</span></td>

                                                {{-- Month vendor cells clickable --}}
                                                @foreach ($vendorColumns as $v)
                                                    @php
                                                        $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                        $vid = $vendorNameToId[$v] ?? '';
                                                        $amt = (float)($d['vendors'][$v] ?? 0);
                                                    @endphp
                                                    <td class="money col-vendor {{ $vendorColClass }}">
                                                        <button type="button"
                                                                class="vendor-pay-click vendor-pay-open"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#vendorItemsModal"
                                                                data-vendor-name="{{ $v }}"
                                                                data-vendor-id="{{ $vid }}"
                                                                data-from="{{ $dayFrom }}"
                                                                data-to="{{ $dayTo }}">
                                                            ₹{{ number_format($amt) }}
                                                        </button>
                                                    </td>
                                                @endforeach

                                                <td class="fw-semibold col-rider">{{ $d['total_delivery'] }}</td>
                                                @foreach ($deliveryCols as $r)
                                                    <td class="col-rider">{{ $d['riders'][$r] ?? 0 }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach

                                        {{-- Month Totals --}}
                                        <tr class="totals-row">
                                            <td colspan="2" class="col-date">Month Total</td>

                                            <td class="money col-finance">
                                                <button type="button"
                                                        class="income-click sub"
                                                        data-income-modal="1"
                                                        data-title="Subscription Income (Month Total)"
                                                        data-users='@json($monthTotals["subscription_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                    ₹{{ number_format($monthTotals['subscription_income'] ?? 0) }}
                                                </button>
                                            </td>

                                            <td class="money col-finance">
                                                <button type="button"
                                                        class="income-click cust"
                                                        data-income-modal="1"
                                                        data-title="Customize Income (Month Total)"
                                                        data-users='@json($monthTotals["customize_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                    ₹{{ number_format($monthTotals['customize_income'] ?? 0) }}
                                                </button>
                                            </td>

                                            <td class="money col-finance">₹{{ number_format($monthTotals['expenditure'] ?? 0) }}</td>
                                            <td class="money col-finance">₹{{ number_format($monthTotals['vendor_fund'] ?? 0) }}</td>

                                            <td>{{ $monthTotals['renew'] ?? 0 }}</td>
                                            <td>{{ $monthTotals['new'] ?? 0 }}</td>
                                            <td>{{ $monthTotals['pause'] ?? 0 }}</td>
                                            <td>{{ $monthTotals['customize'] ?? 0 }}</td>

                                            {{-- Month total vendor cells clickable --}}
                                            @foreach ($vendorColumns as $v)
                                                @php
                                                    $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                    $vid = $vendorNameToId[$v] ?? '';
                                                    $amt = (float)($monthTotals['vendors'][$v] ?? 0);
                                                @endphp
                                                <td class="money col-vendor {{ $vendorColClass }}">
                                                    <button type="button"
                                                            class="vendor-pay-click vendor-pay-open"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#vendorItemsModal"
                                                            data-vendor-name="{{ $v }}"
                                                            data-vendor-id="{{ $vid }}"
                                                            data-from="{{ $monthFrom }}"
                                                            data-to="{{ $monthTo }}">
                                                        ₹{{ number_format($amt) }}
                                                    </button>
                                                </td>
                                            @endforeach

                                            <td class="fw-semibold col-rider">{{ $monthTotals['total_delivery'] ?? 0 }}</td>
                                            @foreach ($deliveryCols as $r)
                                                <td class="col-rider">{{ $monthTotals['riders'][$r] ?? 0 }}</td>
                                            @endforeach
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ========= MODAL: Income Users List (UPDATED: Method + Date) ========= --}}
    <div class="modal fade" id="incomeUsersModal" tabindex="-1" aria-labelledby="incomeUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incomeUsersModalLabel">Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                        <div class="text-muted small">
                            <div id="incomeUsersCount">Customers: 0</div>
                            <div id="incomeUsersTotal">Total: ₹0</div>
                        </div>

                        <input type="text" class="form-control form-control-sm" style="max-width: 260px"
                               id="incomeUsersSearch" placeholder="Search name / id / method / date...">
                    </div>

                    <div id="incomeUsersEmpty" class="alert alert-info d-none mb-0">
                        No paid customers found.
                    </div>

                    <ul class="list-group income-user-list" id="incomeUsersList"></ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========= MODAL: Vendor Payment Items ========= --}}
    <div class="modal fade" id="vendorItemsModal" tabindex="-1" aria-labelledby="vendorItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-column">
                        <h5 class="modal-title" id="vendorItemsModalLabel">Vendor Payment Items</h5>
                        <div class="text-muted small" id="vpMetaLine">—</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <div class="p-2 rounded-3 border bg-light">
                                <div class="text-muted small">Vendor</div>
                                <div class="fw-bold" id="vpVendor">—</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 rounded-3 border bg-light">
                                <div class="text-muted small">Range</div>
                                <div class="fw-bold" id="vpRange">—</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 rounded-3 border bg-light">
                                <div class="text-muted small">Pickups</div>
                                <div class="fw-bold" id="vpPickupsCount">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 rounded-3 border bg-light">
                                <div class="text-muted small">Grand Total</div>
                                <div class="fw-bold" id="vpGrandTotal">₹0</div>
                            </div>
                        </div>
                    </div>

                    <div id="vpLoading" class="d-none text-center py-3">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <div class="text-muted small mt-2">Loading items…</div>
                    </div>

                    <div id="vpError" class="alert alert-danger d-none"></div>

                    <div class="accordion" id="vpPickupsAccordion"></div>

                    <div id="vpEmpty" class="text-center text-muted py-4 d-none">
                        No pickups found for this vendor in the selected range.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Expand / Collapse all (weeks)
        const expandAllBtn = document.getElementById('expandAll');
        const collapseAllBtn = document.getElementById('collapseAll');

        function setAll(open) {
            document.querySelectorAll('#pane-weeks #weeksAccordion .accordion-collapse').forEach(el => {
                const bs = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                open ? bs.show() : bs.hide();
            });
        }
        if (expandAllBtn) expandAllBtn.addEventListener('click', () => setAll(true));
        if (collapseAllBtn) collapseAllBtn.addEventListener('click', () => setAll(false));

        // Hash tabs
        const hash = window.location.hash;
        if (hash === '#month') {
            const tabMonth = document.querySelector('#tab-month');
            if (tabMonth) new bootstrap.Tab(tabMonth).show();
        } else {
            const tabWeeks = document.querySelector('#tab-weeks');
            if (tabWeeks) new bootstrap.Tab(tabWeeks).show();
        }
        document.querySelectorAll('#reportTabs .nav-link').forEach(btn => {
            btn.addEventListener('shown.bs.tab', (e) => {
                const id = e.target.getAttribute('data-bs-target');
                if (id === '#pane-weeks') history.replaceState(null, '', '#weeks');
                if (id === '#pane-month') history.replaceState(null, '', '#month');
            });
        });

        // ===== Modal: Income Users (UPDATED: Method + Date) =====
        const incomeModalEl = document.getElementById('incomeUsersModal');
        const incomeModal = bootstrap.Modal.getOrCreateInstance(incomeModalEl);

        const modalTitle = document.getElementById('incomeUsersModalLabel');
        const listEl = document.getElementById('incomeUsersList');
        const emptyEl = document.getElementById('incomeUsersEmpty');
        const countEl = document.getElementById('incomeUsersCount');
        const totalEl = document.getElementById('incomeUsersTotal');
        const searchEl = document.getElementById('incomeUsersSearch');

        let currentUsers = [];

        function safeJsonParse(str, fallback) {
            try { return JSON.parse(str); } catch (e) { return fallback; }
        }

        function formatINR(n) {
            const num = Number(n || 0);
            return '₹' + num.toLocaleString('en-IN', { maximumFractionDigits: 0 });
        }

        function getPaymentMethodText(u) {
            const pm = (u && (u.payment_methods ?? u.payment_method)) ?? '';
            return String(pm || '').trim();
        }

        function normalizeDateText(raw) {
            const s = String(raw || '').trim();
            if (!s) return '';

            // YYYY-MM-DD -> DD/MM/YYYY
            const m1 = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
            if (m1) return `${m1[3]}/${m1[2]}/${m1[1]}`;

            // ISO date-time -> locale date
            if (s.includes('T')) {
                const d = new Date(s);
                if (!isNaN(d.getTime())) {
                    const dd = String(d.getDate()).padStart(2, '0');
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const yy = d.getFullYear();
                    return `${dd}/${mm}/${yy}`;
                }
            }

            return s; // fallback: show as-is
        }

        function getPaymentDateText(u) {
            // controller can send any of these; we normalize for display
            const raw = (u && (u.payment_date ?? u.paid_at ?? u.date ?? u.created_at)) ?? '';
            return normalizeDateText(raw);
        }

        function renderUsers(users, q = '') {
            const query = (q || '').trim().toLowerCase();

            const filtered = query
                ? users.filter(u => {
                    const name = String(u.name ?? '').toLowerCase();
                    const uid  = String(u.user_id ?? '').toLowerCase();
                    const pm   = getPaymentMethodText(u).toLowerCase();
                    const pd   = getPaymentDateText(u).toLowerCase();
                    return name.includes(query) || uid.includes(query) || pm.includes(query) || pd.includes(query);
                })
                : users;

            listEl.innerHTML = '';

            const total = filtered.reduce((sum, u) => sum + Number(u.amt || 0), 0);

            countEl.textContent = 'Customers: ' + filtered.length;
            totalEl.textContent = 'Total: ' + formatINR(total);

            if (!filtered.length) {
                emptyEl.classList.remove('d-none');
                return;
            }
            emptyEl.classList.add('d-none');

            filtered.forEach(u => {
                const li = document.createElement('li');
                li.className = 'list-group-item';

                const left = document.createElement('div');
                left.className = 'left';

                const name = document.createElement('div');
                name.className = 'uname';
                name.textContent = (u.name ?? 'Unknown');

                const uid = document.createElement('div');
                uid.className = 'uid';
                uid.textContent = u.user_id ? ('#' + u.user_id) : '';

                const pmText = getPaymentMethodText(u);
                const pm = document.createElement('div');
                pm.className = 'pm';
                pm.textContent = pmText ? ('Method: ' + pmText) : 'Method: -';

                const pdText = getPaymentDateText(u);
                const pd = document.createElement('div');
                pd.className = 'pdate';
                pd.textContent = pdText ? ('Date: ' + pdText) : 'Date: -';

                left.appendChild(name);
                left.appendChild(uid);
                left.appendChild(pm);
                left.appendChild(pd);

                const amt = document.createElement('div');
                amt.className = 'amt';
                amt.textContent = formatINR(u.amt || 0);

                li.appendChild(left);
                li.appendChild(amt);

                listEl.appendChild(li);
            });
        }

        document.addEventListener('click', (e) => {
            const el = e.target.closest('[data-income-modal="1"]');
            if (!el) return;

            const title = el.getAttribute('data-title') || 'Users';
            const rawUsers = el.getAttribute('data-users') || '[]';

            currentUsers = safeJsonParse(rawUsers, []);

            if (Array.isArray(currentUsers)) {
                currentUsers.sort((a, b) => Number(b?.amt || 0) - Number(a?.amt || 0));
            } else {
                currentUsers = [];
            }

            modalTitle.textContent = title;

            searchEl.value = '';
            renderUsers(currentUsers);

            incomeModal.show();
        });

        searchEl.addEventListener('input', (e) => {
            renderUsers(currentUsers, e.target.value);
        });

        incomeModalEl.addEventListener('hidden.bs.modal', () => {
            searchEl.value = '';
            listEl.innerHTML = '';
            emptyEl.classList.add('d-none');
            countEl.textContent = 'Customers: 0';
            totalEl.textContent = 'Total: ₹0';
            currentUsers = [];
        });

        // ============================
        // Vendor Payment Items Modal
        // ============================
        const vendorItemsUrl = @json(route('admin.ops-report.vendor-payment-items'));

        const vpModalEl = document.getElementById('vendorItemsModal');

        const vpMetaLineEl = document.getElementById('vpMetaLine');
        const vpVendorEl = document.getElementById('vpVendor');
        const vpRangeEl = document.getElementById('vpRange');
        const vpPickupsCountEl = document.getElementById('vpPickupsCount');
        const vpGrandTotalEl = document.getElementById('vpGrandTotal');

        const vpLoadingEl = document.getElementById('vpLoading');
        const vpErrorEl = document.getElementById('vpError');
        const vpAccEl = document.getElementById('vpPickupsAccordion');
        const vpEmptyEl = document.getElementById('vpEmpty');

        function vpReset() {
            vpMetaLineEl.textContent = '—';
            vpVendorEl.textContent = '—';
            vpRangeEl.textContent = '—';
            vpPickupsCountEl.textContent = '0';
            vpGrandTotalEl.textContent = '₹0';

            vpErrorEl.classList.add('d-none');
            vpErrorEl.textContent = '';
            vpAccEl.innerHTML = '';
            vpEmptyEl.classList.add('d-none');
            vpLoadingEl.classList.add('d-none');
        }

        function vpINR(n) {
            const num = Number(n || 0);
            return '₹' + num.toLocaleString('en-IN', { maximumFractionDigits: 2 });
        }
        function vpSafe(v) {
            return (v === null || v === undefined || v === '') ? '—' : v;
        }

        async function vpLoad(vendorId, vendorName, from, to) {
            vpLoadingEl.classList.remove('d-none');
            vpErrorEl.classList.add('d-none');
            vpErrorEl.textContent = '';
            vpAccEl.innerHTML = '';
            vpEmptyEl.classList.add('d-none');

            vpVendorEl.textContent = vendorName || '—';
            vpRangeEl.textContent = `${from || '—'} → ${to || '—'}`;
            vpMetaLineEl.textContent = `Vendor: ${vendorName || '—'} | Range: ${from || '—'} → ${to || '—'}`;

            try {
                const url = `${vendorItemsUrl}?vendor_id=${encodeURIComponent(vendorId)}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);

                const data = await res.json();

                const meta = data.meta || {};
                const pickups = Array.isArray(data.pickups) ? data.pickups : [];

                vpVendorEl.textContent = vpSafe(meta.vendor_name || vendorName);
                vpRangeEl.textContent = `${vpSafe(meta.from || from)} → ${vpSafe(meta.to || to)}`;
                vpPickupsCountEl.textContent = String(meta.pickups_count ?? pickups.length ?? 0);
                vpGrandTotalEl.textContent = vpINR(meta.grand_total ?? 0);

                if (!pickups.length) {
                    vpEmptyEl.classList.remove('d-none');
                    return;
                }

                vpAccEl.innerHTML = pickups.map((p, i) => {
                    const pid = vpSafe(p.pick_up_id);
                    const headingId = `vpHeading_${i}`;
                    const collapseId = `vpCollapse_${i}`;
                    const items = Array.isArray(p.items) ? p.items : [];

                    const rows = items.length
                        ? items.map((it, idx) => `
                            <tr>
                                <td>${idx + 1}</td>
                                <td>${vpSafe(it.flower_name)}</td>
                                <td>${vpSafe(it.unit)}</td>
                                <td>${it.quantity ?? '—'}</td>
                                <td>${it.price != null ? vpINR(it.price) : '—'}</td>
                                <td class="fw-bold">${it.item_total != null ? vpINR(it.item_total) : '—'}</td>
                                <td>${vpSafe(it.est_unit)}</td>
                                <td>${it.est_quantity ?? '—'}</td>
                                <td>${it.est_price != null ? vpINR(it.est_price) : '—'}</td>
                                <td class="fw-bold">${it.est_total != null ? vpINR(it.est_total) : '—'}</td>
                            </tr>
                        `).join('')
                        : `<tr><td colspan="10" class="text-center text-muted py-3">No items found for this pickup.</td></tr>`;

                    return `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="${headingId}">
                                <button class="accordion-button ${i === 0 ? '' : 'collapsed'}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#${collapseId}"
                                        aria-expanded="${i === 0 ? 'true' : 'false'}" aria-controls="${collapseId}">
                                    <div class="d-flex flex-wrap align-items-center gap-2 w-100">
                                        <span class="fw-bold">Pickup: ${pid}</span>
                                        <span class="badge bg-light text-dark">Pickup: ${vpSafe(p.pickup_date)}</span>
                                        <span class="badge bg-light text-dark">Delivery: ${vpSafe(p.delivery_date)}</span>
                                        <span class="badge bg-info text-white">${vpSafe(p.payment_status)}</span>
                                        <span class="badge bg-secondary">${vpSafe(p.payment_method)}</span>
                                        <span class="ms-auto fw-bold">${vpINR(p.grand_total_price || 0)}</span>
                                    </div>
                                </button>
                            </h2>

                            <div id="${collapseId}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}"
                                 aria-labelledby="${headingId}" data-bs-parent="#vpPickupsAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Flower</th>
                                                    <th>Unit</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Item Total</th>
                                                    <th>Est Unit</th>
                                                    <th>Est Qty</th>
                                                    <th>Est Price</th>
                                                    <th>Est Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>${rows}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

            } catch (err) {
                vpErrorEl.classList.remove('d-none');
                vpErrorEl.textContent = 'Failed to load vendor payment items. ' + (err?.message || '');
            } finally {
                vpLoadingEl.classList.add('d-none');
            }
        }

        vpModalEl.addEventListener('show.bs.modal', function (event) {
            vpReset();

            const trigger = event.relatedTarget;
            const vendorId = trigger?.getAttribute('data-vendor-id') || '';
            const vendorName = trigger?.getAttribute('data-vendor-name') || '';
            const from = trigger?.getAttribute('data-from') || '';
            const to = trigger?.getAttribute('data-to') || '';

            if (!vendorId) {
                vpErrorEl.classList.remove('d-none');
                vpErrorEl.textContent = 'Vendor ID not found. Please ensure vendorNameToId mapping is passed from controller.';
                return;
            }

            vpLoad(vendorId, vendorName, from, to);
        });

        vpModalEl.addEventListener('hidden.bs.modal', vpReset);
    </script>
@endsection
