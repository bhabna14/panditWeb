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

        /* ===== CLICKABLE INCOME (NO HOVER TOOLTIP) ===== */
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

        /* Modal list */
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
        .income-user-list .amt {
            flex: 0 0 auto;
            font-weight: 800;
        }
    </style>
@endsection

@section('content')
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

                    {{-- KPI (click to modal) --}}
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
                        <span class="badge bg-light text-dark ms-1">{{ count($weeks) }} Weeks</span>
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
                                $weekVendorCount = max(count($weekVendorColumns), 1);
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
                                                    <th colspan="{{ 1 + max(count($deliveryCols), 1) }}" class="col-rider">Rider Deliveries</th>
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

                                                    @forelse($weekVendorColumns as $v)
                                                        @php $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even'; @endphp
                                                        <th class="col-vendor {{ $vendorColClass }}" title="{{ $v }}">
                                                            {{ \Illuminate\Support\Str::substr($v, 0, 5) }}
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
                                                    <tr>
                                                        <td class="col-date">{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                        <td class="text-muted col-dow">{{ $d['dow'] }}</td>

                                                        {{-- Sub Income (click → modal) --}}
                                                        <td class="money col-finance">
                                                            <button type="button"
                                                                    class="income-click sub"
                                                                    data-income-modal="1"
                                                                    data-title="Subscription Income ({{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }})"
                                                                    data-users='@json($d["finance"]["subscription_income_users"] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                                ₹{{ number_format($d['finance']['subscription_income'] ?? 0) }}
                                                            </button>
                                                        </td>

                                                        {{-- Customize Income (click → modal) --}}
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

                                                        @if (count($weekVendorColumns))
                                                            @foreach ($weekVendorColumns as $v)
                                                                @php $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even'; @endphp
                                                                <td class="money col-vendor {{ $vendorColClass }}">
                                                                    ₹{{ number_format($d['vendors'][$v] ?? 0) }}
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

                                                    @if (count($weekVendorColumns))
                                                        @foreach ($weekVendorColumns as $v)
                                                            @php $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even'; @endphp
                                                            <td class="money col-vendor {{ $vendorColClass }}">
                                                                ₹{{ number_format($w['totals']['vendors'][$v] ?? 0) }}
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
                    @php $monthAllId = 'month-all-days'; @endphp

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

                                                <th colspan="{{ max(count($vendorColumns), 1) }}" class="col-vendor">Vendor Report</th>
                                                <th colspan="{{ 1 + max(count($deliveryCols), 1) }}" class="col-rider">Rider Deliveries</th>
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

                                                @forelse($vendorColumns as $v)
                                                    @php $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even'; @endphp
                                                    <th class="col-vendor {{ $vendorColClass }}" title="{{ $v }}">
                                                        {{ \Illuminate\Support\Str::substr($v, 0, 5) }}
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

                                                    @foreach ($vendorColumns as $v)
                                                        @php $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even'; @endphp
                                                        <td class="money col-vendor {{ $vendorColClass }}">
                                                            ₹{{ number_format($d['vendors'][$v] ?? 0) }}
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

                                                @foreach ($vendorColumns as $v)
                                                    @php $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even'; @endphp
                                                    <td class="money col-vendor {{ $vendorColClass }}">
                                                        ₹{{ number_format($monthTotals['vendors'][$v] ?? 0) }}
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
    </div>

    {{-- ========= MODAL: Income Users List ========= --}}
    <div class="modal fade" id="incomeUsersModal" tabindex="-1" aria-labelledby="incomeUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incomeUsersModalLabel">Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                        <div class="text-muted small">
                            <div id="incomeUsersCount">Customers: 0</div>
                            <div id="incomeUsersTotal">Total: ₹0</div>
                        </div>

                        <input type="text" class="form-control form-control-sm" style="max-width: 240px"
                               id="incomeUsersSearch" placeholder="Search name...">
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
@endsection

@push('scripts')
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

        // ===== Modal: click to show user list =====
        const modalEl = document.getElementById('incomeUsersModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

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

        function renderUsers(users, q = '') {
            const query = (q || '').trim().toLowerCase();
            const filtered = query
                ? users.filter(u => String(u.name || '').toLowerCase().includes(query))
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

                left.appendChild(name);
                left.appendChild(uid);

                const amt = document.createElement('div');
                amt.className = 'amt';
                amt.textContent = formatINR(u.amt || 0);

                li.appendChild(left);
                li.appendChild(amt);

                listEl.appendChild(li);
            });
        }

        // Click handler (event delegation)
        document.addEventListener('click', (e) => {
            const el = e.target.closest('[data-income-modal="1"]');
            if (!el) return;

            const title = el.getAttribute('data-title') || 'Users';
            const rawUsers = el.getAttribute('data-users') || '[]';

            currentUsers = safeJsonParse(rawUsers, []);

            modalTitle.textContent = title;
            searchEl.value = '';
            renderUsers(currentUsers);

            modal.show();
        });

        // Search inside modal
        searchEl.addEventListener('input', (e) => {
            renderUsers(currentUsers, e.target.value);
        });

        // Reset on close
        modalEl.addEventListener('hidden.bs.modal', () => {
            searchEl.value = '';
            listEl.innerHTML = '';
            emptyEl.classList.add('d-none');
            countEl.textContent = 'Customers: 0';
            totalEl.textContent = 'Total: ₹0';
            currentUsers = [];
        });
    </script>
@endpush
