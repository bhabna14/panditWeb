@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-bg: #eaf3ff;
            /* you can control this from a CSS variable */
        }

        .hero {
            background: linear-gradient(180deg, var(--brand-bg), #f1f2f3);
            border: 1px solid #e7ebf3;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .kpi {
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 18px rgba(25, 42, 70, .06);
            padding: 16px;
        }

        .kpi .label {
            font-size: .8rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #6c757d
        }

        .kpi .value {
            font-variant-numeric: tabular-nums
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px
        }

        @media (max-width: 992px) {
            .grid-3 {
                grid-template-columns: 1fr
            }
        }

        .table-tight td,
        .table-tight th {
            padding: .4rem .6rem;
            vertical-align: middle
        }

        .totals-row {
            font-weight: 700;
            background: #fffdf0
        }

        .accordion-button {
            font-weight: 600
        }

        .toolbar .btn {
            min-width: 120px
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Header / Filters --}}
        <div class="hero">
            <form class="row g-2 align-items-end" method="get" action="{{ route('admin.ops-report') }}">
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
                <div class="col-md-3">
                    <button class="btn btn-primary mt-4">Apply</button>
                    <button type="button" class="btn btn-outline-secondary mt-4" id="expandAll">Expand all weeks</button>
                    <button type="button" class="btn btn-outline-secondary mt-4" id="collapseAll">Collapse all</button>
                </div>
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="small text-muted">Range:</div>
                        <div><strong>{{ $monthStart->format('d M Y') }}</strong> →
                            <strong>{{ $monthEnd->format('d M Y') }}</strong></div>
                    </div>
                </div>
            </form>

            {{-- Month KPIs --}}
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

        {{-- Month → Weeks Accordion --}}
        <div class="accordion" id="monthAccordion">
            @foreach ($weeks as $i => $w)
                @php
                    $weekId = 'wk' . $i;
                    $title = $w['start']->format('d M') . ' - ' . $w['end']->format('d M');
                @endphp
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading-{{ $weekId }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $weekId }}" aria-expanded="false"
                            aria-controls="collapse-{{ $weekId }}">
                            Week {{ $i + 1 }} ({{ $title }}) — Income
                            ₹{{ number_format($w['totals']['income']) }}, Expenditure
                            ₹{{ number_format($w['totals']['expenditure']) }}, Deliveries
                            {{ $w['totals']['total_delivery'] }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $weekId }}" class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ $weekId }}" data-bs-parent="#monthAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-tight align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2">Date</th>
                                            <th rowspan="2">Day</th>
                                            <th colspan="2" class="text-center">Finance</th>
                                            <th colspan="4" class="text-center">Customer</th>
                                            <th colspan="{{ max(count($vendorColumns), 1) }}" class="text-center">Vendor
                                                Report</th>
                                            <th colspan="{{ max(count($pickupColumns), 1) }}" class="text-center">Flower
                                                Pickup</th>
                                            <th colspan="{{ 1 + max(count($deliveryCols), 1) }}" class="text-center">Rider
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Total Income</th>
                                            <th>Total Expenditure</th>

                                            <th>Renew</th>
                                            <th>New</th>
                                            <th>Pause</th>
                                            <th>Customize</th>

                                            @forelse($vendorColumns as $v)
                                                <th>{{ $v }}</th>
                                            @empty
                                                <th>—</th>
                                            @endforelse

                                            @forelse($pickupColumns as $r)
                                                <th>{{ $r }}</th>
                                            @empty
                                                <th>—</th>
                                            @endforelse

                                            <th>Total Delivery</th>
                                            @forelse($deliveryCols as $r)
                                                <th>{{ $r }}</th>
                                            @empty
                                                <th>—</th>
                                            @endforelse
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($w['days'] as $d)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                                <td>{{ $d['dow'] }}</td>

                                                <td class="text-end">{{ number_format($d['finance']['income']) }}</td>
                                                <td class="text-end">{{ number_format($d['finance']['expenditure']) }}</td>

                                                <td>{{ $d['customer']['renew'] }}</td>
                                                <td>{{ $d['customer']['new'] }}</td>
                                                <td>{{ $d['customer']['pause'] }}</td>
                                                <td>{{ $d['customer']['customize'] }}</td>

                                                @foreach ($vendorColumns as $v)
                                                    <td class="text-end">{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                                                @endforeach

                                                @foreach ($pickupColumns as $r)
                                                    <td class="text-end">{{ number_format($d['pickup'][$r] ?? 0) }}</td>
                                                @endforeach

                                                <td>{{ $d['total_delivery'] }}</td>
                                                @foreach ($deliveryCols as $r)
                                                    <td>{{ $d['riders'][$r] ?? 0 }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach

                                        {{-- Week Totals --}}
                                        <tr class="totals-row">
                                            <td colspan="2">Week Total</td>
                                            <td class="text-end">{{ number_format($w['totals']['income']) }}</td>
                                            <td class="text-end">{{ number_format($w['totals']['expenditure']) }}</td>

                                            <td>{{ $w['totals']['renew'] }}</td>
                                            <td>{{ $w['totals']['new'] }}</td>
                                            <td>{{ $w['totals']['pause'] }}</td>
                                            <td>{{ $w['totals']['customize'] }}</td>

                                            @foreach ($vendorColumns as $v)
                                                <td class="text-end">{{ number_format($w['totals']['vendors'][$v] ?? 0) }}
                                                </td>
                                            @endforeach

                                            @foreach ($pickupColumns as $r)
                                                <td class="text-end">{{ number_format($w['totals']['pickup'][$r] ?? 0) }}
                                                </td>
                                            @endforeach

                                            <td>{{ $w['totals']['total_delivery'] }}</td>
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const expandAllBtn = document.getElementById('expandAll');
        const collapseAllBtn = document.getElementById('collapseAll');

        function setAll(open) {
            document.querySelectorAll('#monthAccordion .accordion-collapse').forEach(el => {
                const bs = bootstrap.Collapse.getOrCreateInstance(el, {
                    toggle: false
                });
                open ? bs.show() : bs.hide();
            });
        }
        if (expandAllBtn) expandAllBtn.addEventListener('click', () => setAll(true));
        if (collapseAllBtn) collapseAllBtn.addEventListener('click', () => setAll(false));
    </script>
@endpush
