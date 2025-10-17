{{-- resources/views/admin/reports/month-weeks-report.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-bg:#eaf3ff; --ink:#1d2433; --muted:#6b7280; --surface:#fff; --border:#e7ebf3; --shadow:0 8px 26px rgba(2,8,20,.06);
                --tab1:#6366f1; --tab2:#06b6d4; --tab3:#22c55e; --tab4:#f59e0b; --chip:#eff6ff }
        body, .container-fluid, .table, .btn { font-family:'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif }
        .page-wrap { padding:8px }
        .hero { background:linear-gradient(180deg, var(--brand-bg), #f1f2f3); border:1px solid var(--border); border-radius:16px; padding:18px; box-shadow:var(--shadow) }

        /* KPIs */
        .kpi { border:1px solid var(--border); border-radius:16px; background:#fff; box-shadow:var(--shadow); padding:16px; height:100% }
        .kpi .label { font-size:.78rem; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); margin-bottom:6px }
        .kpi .value { font-variant-numeric:tabular-nums; font-weight:700; color:var(--ink) }
        .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:12px }
        @media(max-width:992px){ .grid-3 { grid-template-columns:1fr } }

        /* Pretty tabs */
        .nu-tabs { border:1px solid var(--border); border-radius:16px; background:#fff; box-shadow:var(--shadow) }
        .nav-pills.nu .nav-link { color:#334155; font-weight:700; border-radius:999px; padding:10px 16px; transition:all .2s ease; display:flex; align-items:center; gap:10px; }
        .nav-pills.nu .nav-link .badge { font-weight:700 }
        .nav-pills.nu .nav-link:hover { transform:translateY(-1px) }
        .nav-pills.nu .nav-link.active { color:#fff; box-shadow:0 8px 22px rgba(2,8,20,.12) }
        .nav-pills.nu .nav-link[data-color="violet"].active  { background:linear-gradient(90deg, var(--tab1), #8b5cf6) }
        .nav-pills.nu .nav-link[data-color="cyan"].active    { background:linear-gradient(90deg, var(--tab2), #3b82f6) }
        .nav-pills.nu .nav-link[data-color="violet"] { border:1px solid #e9e7ff; background:#f7f7ff }
        .nav-pills.nu .nav-link[data-color="cyan"]   { border:1px solid #e1f6ff; background:#f0fbff }
        .tab-pane { padding:16px }

        /* Tables */
        .accordion-item { border:1px solid var(--border)!important; border-radius:14px!important; overflow:hidden; box-shadow:var(--shadow); background:#fff }
        .accordion-button { font-weight:600; padding:16px 20px; margin-bottom:10px }
        .accordion-button:not(.collapsed){ background:linear-gradient(180deg,#f6faff,#f2f6ff); color:var(--ink); border-bottom:1px solid var(--border) }
        .week-header { position:sticky; top:56px; z-index:5; background:#fff; border-bottom:1px solid var(--border) }

        .table-card { border-radius:0 0 14px 14px; overflow:clip }
        .table thead th { white-space:nowrap; font-weight:600; color:var(--muted); border-bottom:1px solid var(--border)!important }
        .table thead tr:first-child th { background:#f9fbff }
        .table thead tr:nth-child(2) th { background:#f3f6fb; font-size:.9rem }
        .table.table-hover tbody tr:hover { background:#fbfdff }
        .table-striped>tbody>tr:nth-of-type(odd){ --bs-table-accent-bg:#fcfdff }
        .table th, .table td { text-align:center!important; vertical-align:middle }
        .money { font-variant-numeric:tabular-nums }
        .totals-row { font-weight:700; background:#fffdf5; border-top:2px solid #f5e6b3 }

        .chip { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .6rem; border-radius:999px; font-size:.8rem; font-weight:700; background:var(--chip); color:#1e40af; border:1px solid #dbe9ff }
        .chip.income { background:#eafff3; color:#0d5f3c; border-color:#d9f7e7 }
        .chip.exp { background:#fff3ea; color:#8a3a0c; border-color:#ffe1cc }
        .chip.deliv { background:#f0f5ff; color:#1e40af; border-color:#e1e9ff }
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
                            <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}</option>
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
                    <div class="label">Total Deliveries (Month)</div>
                    <div class="h4 value">{{ $monthTotals['total_delivery'] }}</div>
                </div>
            </div>
        </div>

        {{-- TABS: Weeks FIRST (active), Month SECOND --}}
        <div class="nu-tabs p-3">
            <ul class="nav nav-pills nu mb-3" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-weeks" data-bs-toggle="pill" data-bs-target="#pane-weeks"
                            type="button" role="tab" aria-controls="pane-weeks" aria-selected="true" data-color="cyan">
                        Weekly Report
                        <span class="badge bg-light text-dark ms-1">{{ count($weeks) }} Weeks</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-month" data-bs-toggle="pill" data-bs-target="#pane-month"
                            type="button" role="tab" aria-controls="pane-month" aria-selected="false" data-color="violet">
                        Month Report
                        <span class="badge bg-light text-dark ms-1">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('M Y') }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">
                {{-- ======================= WEEKS TAB (ACTIVE) ======================= --}}
                <div class="tab-pane fade show active" id="pane-weeks" role="tabpanel" aria-labelledby="tab-weeks" tabindex="0">
                    <div class="accordion" id="weeksAccordion">
                        @foreach ($weeks as $i => $w)
                            @php
                                $weekId = 'wk' . $i;
                                $title = $w['start']->format('d M') . ' - ' . $w['end']->format('d M');
                            @endphp
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header week-header" id="heading-{{ $weekId }}">
                                    <button class="accordion-button collapsed d-flex justify-content-between" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $weekId }}" aria-expanded="false"
                                        aria-controls="collapse-{{ $weekId }}">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="me-1">Week {{ $i + 1 }} <small class="text-muted">({{ $title }})</small></span>
                                            <span class="chip income">Income ₹{{ number_format($w['totals']['income']) }}</span>
                                            <span class="chip exp">Expense ₹{{ number_format($w['totals']['expenditure']) }}</span>
                                            <span class="chip deliv">Deliveries {{ $w['totals']['total_delivery'] }}</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $weekId }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-{{ $weekId }}" data-bs-parent="#weeksAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive table-card">
                                            <table class="table table-sm table-striped table-hover align-middle mb-2">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">Date</th>
                                                        <th rowspan="2">Day</th>
                                                        <th colspan="2">Finance</th>
                                                        <th colspan="4">Customer</th>
                                                        <th colspan="{{ max(count($vendorColumns), 1) }}">Vendor Report</th>
                                                        <th colspan="{{ 1 + max(count($deliveryCols), 1) }}">Rider Deliveries</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Incm</th>
                                                        <th>Purch</th>

                                                        <th>Renew</th>
                                                        <th>New</th>
                                                        <th>Pause</th>
                                                        <th>Customize</th>

                                                        @forelse($vendorColumns as $v)
                                                            <th>{{ $v }}</th>
                                                        @empty
                                                            <th>—</th>
                                                        @endforelse

                                                        <th>Dlvy</th>
                                                        @forelse($deliveryCols as $r)
                                                            {{-- SHOW ONLY FIRST 4 LETTERS OF RIDER NAME --}}
                                                            <th title="{{ $r }}">{{ \Illuminate\Support\Str::substr($r, 0, 4) }}</th>
                                                        @empty
                                                            <th>—</th>
                                                        @endforelse
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($w['days'] as $d)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                            <td class="text-muted">{{ $d['dow'] }}</td>

                                                            <td class="money">₹{{ number_format($d['finance']['income']) }}</td>
                                                            <td class="money">₹{{ number_format($d['finance']['expenditure']) }}</td>

                                                            <td><span class="badge bg-success-subtle text-success">{{ $d['customer']['renew'] }}</span></td>
                                                            <td><span class="badge bg-primary-subtle text-primary">{{ $d['customer']['new'] }}</span></td>
                                                            <td><span class="badge bg-warning-subtle text-warning">{{ $d['customer']['pause'] }}</span></td>
                                                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $d['customer']['customize'] }}</span></td>

                                                            @foreach ($vendorColumns as $v)
                                                                <td class="money">₹{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                                                            @endforeach

                                                            <td class="fw-semibold">{{ $d['total_delivery'] }}</td>
                                                            @foreach ($deliveryCols as $r)
                                                                <td>{{ $d['riders'][$r] ?? 0 }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach

                                                    <tr class="totals-row">
                                                        <td colspan="2">Week Total</td>
                                                        <td class="money">₹{{ number_format($w['totals']['income']) }}</td>
                                                        <td class="money">₹{{ number_format($w['totals']['expenditure']) }}</td>

                                                        <td>{{ $w['totals']['renew'] }}</td>
                                                        <td>{{ $w['totals']['new'] }}</td>
                                                        <td>{{ $w['totals']['pause'] }}</td>
                                                        <td>{{ $w['totals']['customize'] }}</td>

                                                        @foreach ($vendorColumns as $v)
                                                            <td class="money">₹{{ number_format($w['totals']['vendors'][$v] ?? 0) }}</td>
                                                        @endforeach

                                                        <td class="fw-semibold">{{ $w['totals']['total_delivery'] }}</td>
                                                        @foreach ($deliveryCols as $r)
                                                            <td>{{ $w['totals']['riders'][$r] ?? 0 }}</td>
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
                                        <span class="chip income">Income ₹{{ number_format($monthTotals['income']) }}</span>
                                        <span class="chip exp">Expense ₹{{ number_format($monthTotals['expenditure']) }}</span>
                                        <span class="chip deliv">Deliveries {{ $monthTotals['total_delivery'] }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-{{ $monthAllId }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-{{ $monthAllId }}" data-bs-parent="#monthAllAccordion">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive table-card">
                                        <table class="table table-sm table-striped table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Date</th>
                                                    <th rowspan="2">Day</th>
                                                    <th colspan="2">Finance</th>
                                                    <th colspan="4">Customer</th>
                                                    <th colspan="{{ max(count($vendorColumns), 1) }}">Vendor Report</th>
                                                    <th colspan="{{ 1 + max(count($deliveryCols), 1) }}">Rider Deliveries</th>
                                                </tr>
                                                <tr>
                                                    <th>Incm</th>
                                                    <th>Purch</th>
                                                    <th>Renew</th>
                                                    <th>New</th>
                                                    <th>Pause</th>
                                                    <th>Customize</th>

                                                    @forelse($vendorColumns as $v)
                                                        <th>{{ $v }}</th>
                                                    @empty
                                                        <th>—</th>
                                                    @endforelse

                                                    <th>Dlvy</th>
                                                    @forelse($deliveryCols as $r)
                                                        {{-- SHOW ONLY FIRST 4 LETTERS OF RIDER NAME --}}
                                                        <th title="{{ $r }}">{{ \Illuminate\Support\Str::substr($r, 0, 4) }}</th>
                                                    @empty
                                                        <th>—</th>
                                                    @endforelse
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($monthDays as $d)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                        <td class="text-muted">{{ $d['dow'] }}</td>

                                                        <td class="money">₹{{ number_format($d['finance']['income']) }}</td>
                                                        <td class="money">₹{{ number_format($d['finance']['expenditure']) }}</td>

                                                        <td><span class="badge bg-success-subtle text-success">{{ $d['customer']['renew'] }}</span></td>
                                                        <td><span class="badge bg-primary-subtle text-primary">{{ $d['customer']['new'] }}</span></td>
                                                        <td><span class="badge bg-warning-subtle text-warning">{{ $d['customer']['pause'] }}</span></td>
                                                        <td><span class="badge bg-secondary-subtle text-secondary">{{ $d['customer']['customize'] }}</span></td>

                                                        @foreach ($vendorColumns as $v)
                                                            <td class="money">₹{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                                                        @endforeach

                                                        <td class="fw-semibold">{{ $d['total_delivery'] }}</td>
                                                        @foreach ($deliveryCols as $r)
                                                            <td>{{ $d['riders'][$r] ?? 0 }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach

                                                <tr class="totals-row">
                                                    <td colspan="2">Month Total</td>
                                                    <td class="money">₹{{ number_format($monthTotals['income']) }}</td>
                                                    <td class="money">₹{{ number_format($monthTotals['expenditure']) }}</td>

                                                    <td>{{ $monthTotals['renew'] }}</td>
                                                    <td>{{ $monthTotals['new'] }}</td>
                                                    <td>{{ $monthTotals['pause'] }}</td>
                                                    <td>{{ $monthTotals['customize'] }}</td>

                                                    @foreach ($vendorColumns as $v)
                                                        <td class="money">₹{{ number_format($monthTotals['vendors'][$v] ?? 0) }}</td>
                                                    @endforeach

                                                    <td class="fw-semibold">{{ $monthTotals['total_delivery'] }}</td>
                                                    @foreach ($deliveryCols as $r)
                                                        <td>{{ $monthTotals['riders'][$r] ?? 0 }}</td>
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
                const bs = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                open ? bs.show() : bs.hide();
            });
        }
        if (expandAllBtn) expandAllBtn.addEventListener('click', () => setAll(true));
        if (collapseAllBtn) collapseAllBtn.addEventListener('click', () => setAll(false));

        // Shadow on sticky header when open & scrolled
        const headEls = document.querySelectorAll('.week-header');
        const onScroll = () => {
            headEls.forEach(el => {
                const scrolled = el.getBoundingClientRect().top <= 58 && el.nextElementSibling?.classList.contains('show');
                el.style.boxShadow = scrolled ? '0 6px 14px rgba(0,0,0,.05)' : 'none';
            });
        };
        document.addEventListener('scroll', onScroll, { passive: true });

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
