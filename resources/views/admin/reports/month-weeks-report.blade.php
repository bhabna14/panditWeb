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
            --tab3: #22c55e;
            --tab4: #f59e0b;
            --chip: #eff6ff;

            /* Column text colors */
            --col-date: #92400e;   /* brown for Date / Day */
            --col-finance: #047857;
            --col-vendor: #7c3aed;
            --col-rider: #0f766e;  /* teal for rider columns */

            /* Colorful table palette */
            --tbl-head-from: #0ea5e9;
            --tbl-head-to: #6366f1;
            --tbl-head-text: #f9fafb;
            --tbl-row-bg: rgba(248, 250, 252, 0.96);
            --tbl-row-hover: #eff6ff;
            --tbl-border: rgba(148, 163, 184, 0.6);
        }

        body,
        .container-fluid,
        .table,
        .btn {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif
        }

        .page-wrap {
            padding: 8px
        }

        .hero {
            background: linear-gradient(180deg, var(--brand-bg), #f1f2f3);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow)
        }

        /* KPIs */
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

        @media(max-width:992px) {
            .grid-3 {
                grid-template-columns: 1fr
            }
        }

        /* Pretty tabs */
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

        .nav-pills.nu .nav-link .badge {
            font-weight: 700
        }

        .nav-pills.nu .nav-link:hover {
            transform: translateY(-1px)
        }

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

        .tab-pane {
            padding: 16px
        }

        /* Tables / accordion shells */
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

        .table-card {
            border-radius: 0 0 14px 14px;
            overflow: clip
        }

        .table th,
        .table td {
            text-align: center !important;
            vertical-align: middle
        }

        .money {
            font-variant-numeric: tabular-nums
        }

        .totals-row {
            font-weight: 800;              /* BOLDER + bigger for Week/Month Total */
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

        .chip.income {
            background: #eafff3;
            color: #0d5f3c;
            border-color: #d9f7e7
        }

        .chip.exp {
            background: #fff3ea;
            color: #8a3a0c;
            border-color: #ffe1cc
        }

        .chip.deliv {
            background: #f0f5ff;
            color: #1e40af;
            border-color: #e1e9ff
        }

        .chip.vendor-fund {
            background: #ecfeff;
            color: #0369a1;
            border-color: #bae6fd;
        }

        .chip.balance {
            background: #eef2ff;
            color: #4338ca;
            border-color: #c7d2fe;
        }

        /* Column color coding */
        th.col-date,
        td.col-date,
        th.col-dow,
        td.col-dow {
            color: var(--col-date);
            font-weight: 600;
        }

        th.col-finance,
        td.col-finance {
            color: var(--col-finance);
            font-weight: 600;
        }

        th.col-vendor,
        td.col-vendor {
            color: var(--col-vendor);
            font-weight: 600;
        }

        th.col-rider,
        td.col-rider {
            color: var(--col-rider);
            font-weight: 600;
        }

        /* =================== COLORFUL TABLE BASE ===================== */

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

        /* Header band (default) */
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

        /* === CUSTOM HEADER COLORS ================= */

        /* Date header cell */
        .colorful-metrics-table thead th.col-date {
            background: rgba(56, 189, 248, 0.5) !important;
            color: #0f172a !important;
        }

        /* Day header cell */
        .colorful-metrics-table thead th.col-dow {
            background: rgba(56, 189, 248, 0.5) !important;
            color: #0f172a !important;
        }

        /* Income header cell */
        .colorful-metrics-table thead th.income-head {
            background: #16a34a !important;
            color: #f9fafb !important;
        }

        /* Purchase header cell */
        .colorful-metrics-table thead th.purchase-head {
            background: #16a34a !important;
            color: #f9fafb !important;
        }

        /* Vendor Fund header cell */
        .colorful-metrics-table thead th.vendorfund-head {
            background: #0284c7 !important;
            color: #f9fafb !important;
        }

        /* Vendor name headers (2nd header row) */
        .colorful-metrics-table thead tr:nth-child(2) th.col-vendor.vendor-odd,
        .colorful-metrics-table thead tr:nth-child(2) th.col-vendor.vendor-even {
            background: linear-gradient(90deg, rgba(236, 72, 153, 0.95), rgba(244, 114, 182, 0.95)) !important;
            color: #f9fafb !important;
        }

        /* Rider name headers */
        .colorful-metrics-table thead tr:nth-child(2) th.col-rider {
            background: rgba(129, 140, 248, 0.95) !important;
            color: #f9fafb !important;
        }

        /* kill default stripes */
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

        /* column-wise rules */

        /* 1: Date */
        .colorful-metrics-table tbody td:nth-child(1) {
            font-weight: 600;
            color:#7987A1;
        }

        /* 2: Day */
        .colorful-metrics-table tbody td:nth-child(2) {
            font-weight: 500;
            color: var(--col-date);
        }

        /* 3: Incm – green */
        .colorful-metrics-table tbody td:nth-child(3) {
            font-weight: 700;
            color: #16a34a;
        }

        /* 4: Purch – green */
        .colorful-metrics-table tbody td:nth-child(4) {
            font-weight: 700;
            color: #16a34a;
        }

        /* 5: Vendor Fund – blue */
        .colorful-metrics-table tbody td:nth-child(5) {
            font-weight: 700;
            color: #0369a1;
        }

        /* 6–9: Renew / New / Pause / Customize – soft pills */
        .colorful-metrics-table tbody td:nth-child(6),
        .colorful-metrics-table tbody td:nth-child(7),
        .colorful-metrics-table tbody td:nth-child(8),
        .colorful-metrics-table tbody td:nth-child(9) {
            text-align: center;
            font-weight: 600;
        }

        .colorful-metrics-table tbody td:nth-child(6) {
            background: rgba(34, 197, 94, 0.16) !important;
            color: #15803d;
        }

        .colorful-metrics-table tbody td:nth-child(7) {
            background: rgba(56, 189, 248, 0.16) !important;
            color: #0369a1;
        }

        .colorful-metrics-table tbody td:nth-child(8) {
            background: rgba(245, 158, 11, 0.18) !important;
            color: #b45309;
        }

        .colorful-metrics-table tbody td:nth-child(9) {
            background: rgba(244, 114, 182, 0.18) !important;
            color: #be185d;
        }

        /* vendors text (after col 9) */
        .colorful-metrics-table tbody td:nth-child(n+10) {
            font-weight: 600;
            color: #045e06;
        }

        /* ===== pair-wise vendor colors (BODY ONLY) ===== */

        .colorful-metrics-table tbody td.col-vendor.vendor-odd {
            background: rgba(129, 140, 248, 0.10) !important;
        }

        .colorful-metrics-table tbody td.col-vendor.vendor-even {
            background: rgba(244, 114, 182, 0.10) !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid page-wrap">
        {{-- FILTER + KPIs --}}
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
                                {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}</option>
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
                    <div class="h4 value">₹{{ number_format($monthTotals['income']) }}</div>
                </div>
                <div class="kpi">
                    <div class="label">Total Expenditure (Month)</div>
                    <div class="h4 value">₹{{ number_format($monthTotals['expenditure']) }}</div>
                </div>
                <div class="kpi">
                    <div class="label">Available Balance (Month)</div>
                    <div class="h4 value">
                        ₹{{ number_format($monthTotals['available_balance'] ?? 0) }}
                    </div>
                    <div class="small text-muted mt-1">
                        Vendor Fund ₹{{ number_format($monthTotals['vendor_fund'] ?? 0) }}
                        − Expense ₹{{ number_format($monthTotals['expenditure']) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TABS: Weeks FIRST (active), Month SECOND --}}
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
                {{-- ======================= WEEKS TAB (ACTIVE) ======================= --}}
                <div class="tab-pane fade show active" id="pane-weeks" role="tabpanel" aria-labelledby="tab-weeks"
                    tabindex="0">
                    <div class="accordion" id="weeksAccordion">
                        @foreach ($weeks as $i => $w)
                            @php
                                $weekId = 'wk' . $i;
                                $title = $w['start']->format('d M') . ' - ' . $w['end']->format('d M');
                                $weekVendorColumns = $w['vendorColumns'] ?? $vendorColumns; // fallback
                                $weekVendorCount = max(count($weekVendorColumns), 1);
                            @endphp
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header week-header" id="heading-{{ $weekId }}">
                                    <button class="accordion-button collapsed d-flex justify-content-between" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $weekId }}"
                                        aria-expanded="false" aria-controls="collapse-{{ $weekId }}">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="me-1">Week {{ $i + 1 }}
                                                <small class="text-muted">({{ $title }})</small>
                                            </span>

                                            <span class="chip income">
                                                Income ₹{{ number_format($w['totals']['income']) }}
                                            </span>

                                            <span class="chip exp">
                                                Expense ₹{{ number_format($w['totals']['expenditure']) }}
                                            </span>

                                            {{-- Weekly Vendor Fund --}}
                                            <span class="chip vendor-fund">
                                                Vendor Fund ₹{{ number_format($w['totals']['vendor_fund'] ?? 0) }}
                                            </span>

                                            {{-- Weekly Available Balance --}}
                                            <span class="chip balance">
                                                Avail Bal ₹{{ number_format($w['totals']['available_balance'] ?? 0) }}
                                            </span>
{{-- 
                                            <span class="chip deliv">
                                                Deliveries {{ $w['totals']['total_delivery'] }}
                                            </span> --}}
                                        </div>
                                    </button>
                                </h2>

                                <div id="collapse-{{ $weekId }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-{{ $weekId }}" data-bs-parent="#weeksAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive table-card">
                                            <table
                                                class="table table-sm table-striped table-hover align-middle mb-2 colorful-metrics-table">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" class="col-date">Date</th>
                                                        <th rowspan="2" class="col-dow">Day</th>
                                                        <th colspan="3" class="col-finance">Finance</th>
                                                        <th colspan="4">Customer</th>
                                                        <th colspan="{{ $weekVendorCount }}"
                                                            class="col-vendor">Vendor Report
                                                        </th>
                                                        <th colspan="{{ 1 + max(count($deliveryCols), 1) }}"
                                                            class="col-rider">Rider
                                                            Deliveries</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-finance income-head">Incm</th>
                                                        <th class="col-finance purchase-head">Purch</th>
                                                        <th class="col-finance vendorfund-head">VendF</th>

                                                        <th>Renew</th>
                                                        <th>New</th>
                                                        <th>Pause</th>
                                                        <th>Customize</th>

                                                        @forelse($weekVendorColumns as $v)
                                                            @php
                                                                $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                            @endphp
                                                            <th class="col-vendor {{ $vendorColClass }}" title="{{ $v }}">
                                                                {{ \Illuminate\Support\Str::substr($v, 0, 5) }}</th>
                                                        @empty
                                                            <th>—</th>
                                                        @endforelse

                                                        <th class="col-rider">Dlvy</th>
                                                        @forelse($deliveryCols as $r)
                                                            {{-- SHOW ONLY FIRST 4 LETTERS OF RIDER NAME --}}
                                                            <th title="{{ $r }}" class="col-rider">
                                                                {{ \Illuminate\Support\Str::substr($r, 0, 4) }}</th>
                                                        @empty
                                                            <th>—</th>
                                                        @endforelse
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($w['days'] as $d)
                                                        <tr>
                                                            <td class="col-date">
                                                                {{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}
                                                            </td>
                                                            <td class="text-muted col-dow">{{ $d['dow'] }}</td>

                                                            <td class="money col-finance">
                                                                ₹{{ number_format($d['finance']['income']) }}</td>
                                                            <td class="money col-finance">
                                                                ₹{{ number_format($d['finance']['expenditure']) }}</td>
                                                            <td class="money col-finance">
                                                                ₹{{ number_format($d['finance']['vendor_fund'] ?? 0) }}
                                                            </td>

                                                            <td><span
                                                                    class="badge bg-success-subtle text-success">{{ $d['customer']['renew'] }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="badge bg-primary-subtle text-primary">{{ $d['customer']['new'] }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="badge bg-warning-subtle text-warning">{{ $d['customer']['pause'] }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="badge bg-secondary-subtle text-secondary">{{ $d['customer']['customize'] }}</span>
                                                            </td>

                                                            @if (count($weekVendorColumns))
                                                                @foreach ($weekVendorColumns as $v)
                                                                    @php
                                                                        $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                                    @endphp
                                                                    <td class="money col-vendor {{ $vendorColClass }}">
                                                                        ₹{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                                                                @endforeach
                                                            @else
                                                                <td class="text-muted">—</td>
                                                            @endif

                                                            <td class="fw-semibold col-rider">
                                                                {{ $d['total_delivery'] }}</td>
                                                            @foreach ($deliveryCols as $r)
                                                                <td class="col-rider">{{ $d['riders'][$r] ?? 0 }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach

                                                    <tr class="totals-row">
                                                        <td colspan="2" class="col-date">Week Total</td>
                                                        <td class="money col-finance">
                                                            ₹{{ number_format($w['totals']['income']) }}
                                                        </td>
                                                        <td class="money col-finance">
                                                            ₹{{ number_format($w['totals']['expenditure']) }}</td>
                                                        <td class="money col-finance">
                                                            ₹{{ number_format($w['totals']['vendor_fund'] ?? 0) }}</td>

                                                        <td>{{ $w['totals']['renew'] }}</td>
                                                        <td>{{ $w['totals']['new'] }}</td>
                                                        <td>{{ $w['totals']['pause'] }}</td>
                                                        <td>{{ $w['totals']['customize'] }}</td>

                                                        @if (count($weekVendorColumns))
                                                            @foreach ($weekVendorColumns as $v)
                                                                @php
                                                                    $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                                @endphp
                                                                <td class="money col-vendor {{ $vendorColClass }}">
                                                                    ₹{{ number_format($w['totals']['vendors'][$v] ?? 0) }}</td>
                                                            @endforeach
                                                        @else
                                                            <td class="text-muted">—</td>
                                                        @endif

                                                        <td class="fw-semibold col-rider">
                                                            {{ $w['totals']['total_delivery'] }}</td>
                                                        @foreach ($deliveryCols as $r)
                                                            <td class="col-rider">{{ $w['totals']['riders'][$r] ?? 0 }}</td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> {{-- /table-responsive --}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ======================= MONTH TAB (SECOND) ======================= --}}
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
                                        <span class="chip income">Income
                                            ₹{{ number_format($monthTotals['income']) }}</span>
                                        <span class="chip exp">Expense
                                            ₹{{ number_format($monthTotals['expenditure']) }}</span>
                                        <span class="chip vendor-fund">
                                            Vendor Fund ₹{{ number_format($monthTotals['vendor_fund'] ?? 0) }}</span>
                                        <span class="chip balance">
                                            Avail Bal ₹{{ number_format($monthTotals['available_balance'] ?? 0) }}</span>
                                        <span class="chip deliv">Deliveries {{ $monthTotals['total_delivery'] }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-{{ $monthAllId }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-{{ $monthAllId }}" data-bs-parent="#monthAllAccordion">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive table-card">
                                        <table
                                            class="table table-sm table-striped table-hover align-middle mb-0 colorful-metrics-table">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="col-date">Date</th>
                                                    <th rowspan="2" class="col-dow">Day</th>
                                                    <th colspan="3" class="col-finance">Finance</th>
                                                    <th colspan="4">Customer</th>
                                                    <th colspan="{{ max(count($vendorColumns), 1) }}"
                                                        class="col-vendor">Vendor Report</th>
                                                    <th colspan="{{ 1 + max(count($deliveryCols), 1) }}"
                                                        class="col-rider">Rider Deliveries
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="col-finance income-head">Incm</th>
                                                    <th class="col-finance purchase-head">Purch</th>
                                                    <th class="col-finance vendorfund-head">VendF</th>
                                                    <th>Renew</th>
                                                    <th>New</th>
                                                    <th>Pause</th>
                                                    <th>Customize</th>

                                                    @forelse($vendorColumns as $v)
                                                        @php
                                                            $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                        @endphp
                                                        <th class="col-vendor {{ $vendorColClass }}" title="{{ $v }}">
                                                            {{ \Illuminate\Support\Str::substr($v, 0, 5) }}</th>
                                                    @empty
                                                        <th>—</th>
                                                    @endforelse

                                                    <th class="col-rider">Dlvy</th>
                                                    @forelse($deliveryCols as $r)
                                                        {{-- SHOW ONLY FIRST 4 LETTERS OF RIDER NAME --}}
                                                        <th title="{{ $r }}" class="col-rider">
                                                            {{ \Illuminate\Support\Str::substr($r, 0, 4) }}</th>
                                                    @empty
                                                        <th>—</th>
                                                    @endforelse
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($monthDays as $d)
                                                    <tr>
                                                        <td class="col-date">
                                                            {{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                        <td class="text-muted col-dow">{{ $d['dow'] }}</td>

                                                        <td class="money col-finance">
                                                            ₹{{ number_format($d['finance']['income']) }}
                                                        </td>
                                                        <td class="money col-finance">
                                                            ₹{{ number_format($d['finance']['expenditure']) }}</td>
                                                        <td class="money col-finance">
                                                            ₹{{ number_format($d['finance']['vendor_fund'] ?? 0) }}</td>

                                                        <td><span
                                                                class="badge bg-success-subtle text-success">{{ $d['customer']['renew'] }}</span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-primary-subtle text-primary">{{ $d['customer']['new'] }}</span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-warning-subtle text-warning">{{ $d['customer']['pause'] }}</span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-secondary-subtle text-secondary">{{ $d['customer']['customize'] }}</span>
                                                        </td>

                                                        @foreach ($vendorColumns as $v)
                                                            @php
                                                                $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                            @endphp
                                                            <td class="money col-vendor {{ $vendorColClass }}">
                                                                ₹{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                                                        @endforeach

                                                        <td class="fw-semibold col-rider">
                                                            {{ $d['total_delivery'] }}</td>
                                                        @foreach ($deliveryCols as $r)
                                                            <td class="col-rider">{{ $d['riders'][$r] ?? 0 }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach

                                                <tr class="totals-row">
                                                    <td colspan="2" class="col-date">Month Total</td>
                                                    <td class="money col-finance">
                                                        ₹{{ number_format($monthTotals['income']) }}</td>
                                                    <td class="money col-finance">
                                                        ₹{{ number_format($monthTotals['expenditure']) }}
                                                    </td>
                                                    <td class="money col-finance">
                                                        ₹{{ number_format($monthTotals['vendor_fund'] ?? 0) }}
                                                    </td>

                                                    <td>{{ $monthTotals['renew'] }}</td>
                                                    <td>{{ $monthTotals['new'] }}</td>
                                                    <td>{{ $monthTotals['pause'] }}</td>
                                                    <td>{{ $monthTotals['customize'] }}</td>

                                                    @foreach ($vendorColumns as $v)
                                                        @php
                                                            $vendorColClass = $loop->iteration % 2 === 1 ? 'vendor-odd' : 'vendor-even';
                                                        @endphp
                                                        <td class="money col-vendor {{ $vendorColClass }}">
                                                            ₹{{ number_format($monthTotals['vendors'][$v] ?? 0) }}</td>
                                                    @endforeach

                                                    <td class="fw-semibold col-rider">
                                                        {{ $monthTotals['total_delivery'] }}</td>
                                                    @foreach ($deliveryCols as $r)
                                                        <td class="col-rider">{{ $monthTotals['riders'][$r] ?? 0 }}</td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> {{-- /table-responsive --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- /tab-content --}}
        </div> {{-- /nu-tabs --}}
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Expand/Collapse all (weeks accordion in Weeks tab)
        const expandAllBtn = document.getElementById('expandAll');
        const collapseAllBtn = document.getElementById('collapseAll');

        function setAll(open) {
            document.querySelectorAll('#pane-weeks #weeksAccordion .accordion-collapse').forEach(el => {
                const bs = bootstrap.Collapse.getOrCreateInstance(el, {
                    toggle: false
                });
                open ? bs.show() : bs.hide();
            });
        }
        if (expandAllBtn) expandAllBtn.addEventListener('click', () => setAll(true));
        if (collapseAllBtn) collapseAllBtn.addEventListener('click', () => setAll(false));

        // Shadow on sticky header when open & scrolled
        const headEls = document.querySelectorAll('.week-header');
        const onScroll = () => {
            headEls.forEach(el => {
                const scrolled = el.getBoundingClientRect().top <= 58 && el.nextElementSibling?.classList
                    .contains('show');
                el.style.boxShadow = scrolled ? '0 6px 14px rgba(0,0,0,.05)' : 'none';
            });
        };
        document.addEventListener('scroll', onScroll, {
            passive: true
        });

        // Activate correct tab if hash is provided (#weeks or #month). Default to #weeks.
        const hash = window.location.hash;
        if (hash === '#month') {
            const tabMonth = document.querySelector('#tab-month');
            if (tabMonth) new bootstrap.Tab(tabMonth).show();
        } else {
            const tabWeeks = document.querySelector('#tab-weeks');
            if (tabWeeks) new bootstrap.Tab(tabWeeks).show();
        }

        // Update URL hash when switching tabs
        document.querySelectorAll('#reportTabs .nav-link').forEach(btn => {
            btn.addEventListener('shown.bs.tab', (e) => {
                const id = e.target.getAttribute('data-bs-target');
                if (id === '#pane-weeks') history.replaceState(null, '', '#weeks');
                if (id === '#pane-month') history.replaceState(null, '', '#month');
            });
        });
    </script>
@endpush
