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

        /* ===== New Hover (custom tooltip) ===== */
        .income-hover {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .14rem .55rem;
            border-radius: 12px;
            cursor: pointer;
            user-select: none;

            border: 1px solid rgba(15, 23, 42, 0.15);
            background: rgba(241, 245, 249, 0.75);

            font-weight: 800;
            font-variant-numeric: tabular-nums;

            transition: transform .12s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;
        }

        .income-hover:hover {
            transform: translateY(-1px);
            background: rgba(219, 234, 254, 0.70);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 10px 22px rgba(2, 8, 20, 0.10);
        }

        .income-hover.sub {
            color: #166534;
            background: rgba(220, 252, 231, 0.65);
            border-color: rgba(22, 101, 52, 0.20);
        }
        .income-hover.sub:hover {
            background: rgba(187, 247, 208, 0.75);
            border-color: rgba(22, 101, 52, 0.35);
        }

        .income-hover.cust {
            color: #14532d;
            background: rgba(209, 250, 229, 0.55);
            border-color: rgba(20, 83, 45, 0.20);
        }
        .income-hover.cust:hover {
            background: rgba(167, 243, 208, 0.70);
            border-color: rgba(20, 83, 45, 0.35);
        }

        .income-hover .hint {
            font-weight: 900;
            font-size: .72rem;
            opacity: .75;
            border: 1px solid rgba(100, 116, 139, 0.30);
            background: rgba(255, 255, 255, 0.7);
            padding: 0 .35rem;
            border-radius: 999px;
            line-height: 1.2rem;
        }

        /* Global tooltip container */
        #incomeTooltip {
            position: fixed;
            z-index: 99999;
            max-width: 380px;
            pointer-events: none;

            background: #0f172a;
            color: #f8fafc;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.30);

            padding: 12px 12px;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity .12s ease, transform .12s ease;
        }

        #incomeTooltip.show {
            opacity: 1;
            transform: translateY(0);
        }

        #incomeTooltip .tt-head {
            font-weight: 900;
            font-size: .95rem;
            margin-bottom: 2px;
        }
        #incomeTooltip .tt-meta {
            font-size: .82rem;
            opacity: .85;
            margin-bottom: 8px;
        }
        #incomeTooltip .tt-empty {
            font-size: .85rem;
            opacity: .85;
        }

        #incomeTooltip .tt-scroll {
            max-height: 260px;
            overflow: auto;
            padding-right: 4px;
        }

        #incomeTooltip .tt-row {
            padding: 8px 6px;
            border-top: 1px solid rgba(148, 163, 184, 0.18);
        }
        #incomeTooltip .tt-row:first-child { border-top: 0; }

        #incomeTooltip .tt-name {
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #incomeTooltip .tt-id {
            font-size: .78rem;
            opacity: .75;
        }
        #incomeTooltip .tt-more {
            margin-top: 8px;
            font-size: .78rem;
            opacity: .75;
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

                    {{-- KPI hover tooltips for Sub + Cust --}}
                    <div class="small text-muted mt-1 d-flex flex-wrap gap-2 align-items-center">
                        <span class="income-hover sub"
                              data-income-tooltip="1"
                              data-tooltip-html='@json($monthTotals["subscription_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                            Sub ₹{{ number_format($monthTotals['subscription_income'] ?? 0) }}
                            <span class="hint">hover</span>
                        </span>

                        <span class="income-hover cust"
                              data-income-tooltip="1"
                              data-tooltip-html='@json($monthTotals["customize_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                            Cust ₹{{ number_format($monthTotals['customize_income'] ?? 0) }}
                            <span class="hint">hover</span>
                        </span>
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

                                                        {{-- Sub Income (custom tooltip) --}}
                                                        <td class="money col-finance">
                                                            <span class="income-hover sub"
                                                                  data-income-tooltip="1"
                                                                  data-tooltip-html='@json($d["finance"]["subscription_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                                ₹{{ number_format($d['finance']['subscription_income'] ?? 0) }}
                                                                <span class="hint">hover</span>
                                                            </span>
                                                        </td>

                                                        {{-- Customize Income (custom tooltip) --}}
                                                        <td class="money col-finance">
                                                            <span class="income-hover cust"
                                                                  data-income-tooltip="1"
                                                                  data-tooltip-html='@json($d["finance"]["customize_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                                ₹{{ number_format($d['finance']['customize_income'] ?? 0) }}
                                                                <span class="hint">hover</span>
                                                            </span>
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
                                                        <span class="income-hover sub"
                                                              data-income-tooltip="1"
                                                              data-tooltip-html='@json($w["totals"]["subscription_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                            ₹{{ number_format($w['totals']['subscription_income'] ?? 0) }}
                                                            <span class="hint">hover</span>
                                                        </span>
                                                    </td>

                                                    <td class="money col-finance">
                                                        <span class="income-hover cust"
                                                              data-income-tooltip="1"
                                                              data-tooltip-html='@json($w["totals"]["customize_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                            ₹{{ number_format($w['totals']['customize_income'] ?? 0) }}
                                                            <span class="hint">hover</span>
                                                        </span>
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
                                                        <span class="income-hover sub"
                                                              data-income-tooltip="1"
                                                              data-tooltip-html='@json($d["finance"]["subscription_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                            ₹{{ number_format($d['finance']['subscription_income'] ?? 0) }}
                                                            <span class="hint">hover</span>
                                                        </span>
                                                    </td>

                                                    <td class="money col-finance">
                                                        <span class="income-hover cust"
                                                              data-income-tooltip="1"
                                                              data-tooltip-html='@json($d["finance"]["customize_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                            ₹{{ number_format($d['finance']['customize_income'] ?? 0) }}
                                                            <span class="hint">hover</span>
                                                        </span>
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
                                                    <span class="income-hover sub"
                                                          data-income-tooltip="1"
                                                          data-tooltip-html='@json($monthTotals["subscription_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                        ₹{{ number_format($monthTotals['subscription_income'] ?? 0) }}
                                                        <span class="hint">hover</span>
                                                    </span>
                                                </td>

                                                <td class="money col-finance">
                                                    <span class="income-hover cust"
                                                          data-income-tooltip="1"
                                                          data-tooltip-html='@json($monthTotals["customize_income_tooltip"] ?? "", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                                        ₹{{ number_format($monthTotals['customize_income'] ?? 0) }}
                                                        <span class="hint">hover</span>
                                                    </span>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@push('scripts')
    {{-- Bootstrap needed for tabs/collapse (tooltips are custom JS now) --}}
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

        // ===== New Tooltip (no Bootstrap popover) =====
        function safeJsonParse(str) {
            try { return JSON.parse(str); } catch (e) { return ''; }
        }

        // Create one tooltip for whole page (only once)
        const tt = document.createElement('div');
        tt.id = 'incomeTooltip';
        document.body.appendChild(tt);

        let activeEl = null;

        function showTooltip(el) {
            const raw = el.getAttribute('data-tooltip-html') || '""';
            const html = safeJsonParse(raw) || "<div class='tt-empty'>No paid payments found.</div>";
            tt.innerHTML = html;
            tt.classList.add('show');
            activeEl = el;
        }

        function hideTooltip() {
            tt.classList.remove('show');
            activeEl = null;
        }

        function moveTooltip(e) {
            if (!activeEl) return;

            const pad = 14;
            const rect = tt.getBoundingClientRect();

            let x = e.clientX + pad;
            let y = e.clientY + pad;

            const vw = window.innerWidth;
            const vh = window.innerHeight;

            if (x + rect.width + 10 > vw) x = e.clientX - rect.width - pad;
            if (y + rect.height + 10 > vh) y = e.clientY - rect.height - pad;

            tt.style.left = x + 'px';
            tt.style.top  = y + 'px';
        }

        // Event delegation (works even inside collapsed accordion / tabs)
        document.addEventListener('mouseover', (e) => {
            const el = e.target.closest('[data-income-tooltip="1"]');
            if (!el) return;
            showTooltip(el);
        });

        document.addEventListener('mousemove', (e) => moveTooltip(e));

        document.addEventListener('mouseout', (e) => {
            const el = e.target.closest('[data-income-tooltip="1"]');
            if (!el) return;

            // hide only if leaving the hovered element
            if (activeEl && !el.contains(e.relatedTarget)) {
                hideTooltip();
            }
        });

        // Touch support: tap to toggle
        document.addEventListener('click', (e) => {
            const el = e.target.closest('[data-income-tooltip="1"]');
            if (!el) { hideTooltip(); return; }

            if (activeEl === el) hideTooltip();
            else showTooltip(el);

            moveTooltip(e);
        });

        window.addEventListener('scroll', () => hideTooltip(), { passive: true });
        window.addEventListener('resize', () => hideTooltip(), { passive: true });
    </script>
@endpush
