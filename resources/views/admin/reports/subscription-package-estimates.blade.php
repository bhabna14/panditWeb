@extends('admin.layouts.apps')

@section('styles')
    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-subtle: #f4f5fb;
            --surface: #ffffff;
            --border: #e2e4f0;
            --ring: #d4d7e8;

            --text: #0f172a;
            --muted: #6b7280;

            --indigo: #6366f1;
            --indigo-600: #4f46e5;
            --cyan: #06b6d4;

            --success-soft: #ecfdf3;
            --success-fg: #166534;
            --warning-soft: #fff7ed;
            --warning-fg: #9a3412;

            --sh-sm: 0 4px 12px rgba(15, 23, 42, 0.06);
            --sh-md: 0 10px 28px rgba(15, 23, 42, 0.10);
        }

        html,
        body {
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif !important;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 100% -10%, rgba(99, 102, 241, .14), transparent 60%),
                radial-gradient(900px 500px at 0% 10%, rgba(6, 182, 212, .10), transparent 55%),
                var(--bg-subtle);
        }

        .container-page {
            max-width: 1320px;
        }

        .mono {
            font-variant-numeric: tabular-nums;
        }

        /* Page header */
        .page-header-title {
            font-weight: 600;
            color: #020617;
        }

        .page-header-sub {
            font-size: .86rem;
            color: var(--muted);
        }

        .page-breadcrumb {
            font-size: .78rem;
        }

        .page-breadcrumb a {
            text-decoration: none;
        }

        /* Band summary */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border-radius: 18px;
            border: 1px solid #c7d2fe;
            padding: .9rem 1.2rem;
            box-shadow: var(--sh-md);
            margin-bottom: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .band-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .band-title {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band-title span.label-main {
            font-weight: 600;
            font-size: .98rem;
            color: #0b2a5b;
        }

        .band-pill {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .12rem .6rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, .08);
            color: #020617;
        }

        .band-sub {
            font-size: .8rem;
            color: var(--muted);
        }

        .band-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .band-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            border: 1px solid transparent;
            background: #fff;
            font-size: .8rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(15, 23, 42, .10);
        }

        .band-chip span.icon {
            font-size: 1rem;
        }

        .band-chip.day-total {
            background: var(--success-soft);
            color: var(--success-fg);
            border-color: #bbf7d0;
        }

        .band-chip.month-total {
            background: #fef9c3;
            color: #92400e;
            border-color: #fde68a;
        }

        .band-chip.filter {
            background: #eef2ff;
            color: #3730a3;
            border-color: #c7d2fe;
        }

        /* Toolbar */
        .toolbar-card {
            background: var(--surface);
            border-radius: 18px;
            border: 1px solid var(--border);
            padding: .9rem 1rem;
            box-shadow: var(--sh-md);
            margin-bottom: 1.3rem;
        }

        .toolbar-row-main {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .toolbar-block {
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }

        .toolbar-label {
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
        }

        .toolbar-input,
        .toolbar-select {
            border-radius: 999px;
            border: 1px solid var(--ring);
            padding: .42rem .9rem;
            font-size: .85rem;
            font-weight: 500;
        }

        .toolbar-input:focus,
        .toolbar-select:focus {
            outline: none;
            border-color: var(--indigo-600);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .25);
        }

        .toolbar-subtext {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .25rem;
        }

        .toolbar-row-bottom {
            display: flex;
            flex-wrap: wrap;
            margin-top: .85rem;
            gap: .6rem;
            align-items: center;
            justify-content: space-between;
        }

        .chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .38rem .8rem;
            border-radius: 999px;
            background: #fff;
            border: 1px dashed var(--ring);
            color: #334155;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s ease;
        }

        .chip:hover {
            border-color: var(--indigo);
            color: var(--indigo);
        }

        .chip.active {
            background: linear-gradient(135deg, var(--indigo), var(--cyan));
            border-color: transparent;
            color: #0b1120;
            box-shadow: 0 6px 16px rgba(6, 182, 212, .25);
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: flex-end;
        }

        .btn-grad {
            border: none;
            color: #0a0909;
            font-weight: 700;
            letter-spacing: .02em;
            background-image: linear-gradient(120deg, var(--indigo-600), var(--cyan));
            border-radius: 999px;
            padding: .42rem 1.1rem;
            box-shadow: 0 8px 20px rgba(6, 182, 212, .32);
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .82rem;
        }

        .btn-grad:hover {
            filter: brightness(.96);
        }

        .btn-reset {
            border-radius: 999px;
            border: 1px dashed var(--ring);
            padding: .42rem .9rem;
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
            background: #fff;
        }

        .btn-reset:hover {
            color: #111827;
            border-color: #9ca3af;
        }

        .btn-export {
            border-radius: 999px;
            border: 1px solid #dbeafe;
            padding: .42rem .9rem;
            font-size: .8rem;
            font-weight: 600;
            color: #1d4ed8;
            background: #eff6ff;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            text-decoration: none;
        }

        .btn-export:hover {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Cards / tables */
        .card-soft {
            border-radius: 18px;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--sh-md);
        }

        .card-soft .card-header {
            border-bottom: 1px solid var(--border);
            background: #f9fafb;
        }

        .chip-inline {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .2rem .55rem;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: .75rem;
            font-weight: 600;
        }

        .amount {
            font-variant-numeric: tabular-nums;
        }

        .table {
            border-color: var(--border) !important;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: .84rem;
        }

        .table thead th {
            background: #f3f4ff !important;
            border-bottom: 1px solid var(--border) !important;
            color: #020617 !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: .7rem;
            letter-spacing: .06em;
        }

        .table-hover tbody tr:hover {
            background: #f9fbff;
        }

        .hstack {
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        @media (max-width: 992px) {
            .toolbar-row-main {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 576px) {
            .toolbar-row-main {
                grid-template-columns: 1fr;
            }

            .toolbar-actions {
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Page header --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="page-header-title mb-1">
                    Subscription Package — Estimates
                </h4>
              
            </div>
            <ol class="breadcrumb page-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Subscription Package Estimates</li>
            </ol>
        </div>

        {{-- Summary band --}}
        <div class="band">
            <div class="band-header">
               
                <div class="band-sub">
                    Date: <strong>{{ $date->toFormattedDateString() }}</strong> · Month:
                    <strong>{{ $monthStart->format('F Y') }}</strong>
                </div>
            </div>

            <div class="band-chips">
                <span class="band-chip day-total">
                    <span class="icon">📦</span>
                    <span>Day Estimate</span>
                    <span class="mono">₹ {{ number_format($dayEstimate['total_cost'] ?? 0, 2) }}</span>
                </span>
                <span class="band-chip month-total">
                    <span class="icon">🗓️</span>
                    <span>Month Estimate</span>
                    <span class="mono">₹ {{ number_format($monthEstimate['total_cost'] ?? 0, 2) }}</span>
                </span>
                <span class="band-chip filter">
                    <span class="icon">🎯</span>
                    <span>Per-Day Price Filter:</span>
                    <span class="mono">
                        @if($selectedPdp === 'all')
                            All Subscription products
                        @elseif($selectedPdp === 'has')
                            Only with per-day price
                        @else
                            ₹ {{ number_format((float) $selectedPdp, 2) }}
                        @endif
                    </span>
                </span>
            </div>
        </div>

        {{-- Filter toolbar --}}
        <form method="GET" action="{{ route('admin.subscriptionPackageEstimate') }}" class="toolbar-card">
            <div class="toolbar-row-main">
                <div class="toolbar-block">
                    <span class="toolbar-label">Day</span>
                    <input type="date" name="date" class="toolbar-input" value="{{ $selectedDate }}" required>
                </div>

                <div class="toolbar-block">
                    <span class="toolbar-label">Month</span>
                    <input type="month" name="month" class="toolbar-input" value="{{ $selectedMonth }}" required>
                </div>

                <div class="toolbar-block">
                    <span class="toolbar-label">Per-Day Price Filter (Subscription)</span>
                    <select name="per_day_price" class="toolbar-select">
                        <option value="all" @selected($selectedPdp === 'all')>All Subscription products</option>
                        <option value="has" @selected($selectedPdp === 'has')>Only with per-day price</option>
                        @foreach ($perDayPriceOptions as $opt)
                            <option value="{{ $opt }}" @selected((string)$selectedPdp === (string)$opt)>
                                ₹ {{ number_format((float) $opt, 2) }}
                            </option>
                        @endforeach
                    </select>
                   
                </div>

                <div class="toolbar-block d-flex align-items-end justify-content-end">
                    <div class="toolbar-actions w-100 justify-content-end">
                        <button type="submit" class="btn-grad">
                            <i class="bi bi-calculator"></i> Calculate
                        </button>
                    </div>
                </div>
            </div>

            <div class="toolbar-row-bottom">
               
                <a class="btn-export"
                   href="{{ route('admin.reports.subscription_package_estimates.export', ['date'=>$selectedDate,'month'=>$selectedMonth,'per_day_price'=>$selectedPdp]) }}">
                    <i class="bi bi-filetype-csv"></i> Export CSV
                </a>
            </div>
        </form>

        {{-- Day Summary --}}
        <div class="card card-soft mb-4">
            <div class="card-header">
                <div class="hstack">
                    <div><strong>Date:</strong> {{ $date->toFormattedDateString() }}</div>
                    <div class="chip-inline">
                        <span>Lines</span>
                        <span class="mono">{{ count($dayEstimate['lines']) }}</span>
                    </div>
                    <div class="chip-inline">
                        <span>Est. Day Value</span>
                        <span class="mono">₹ {{ number_format($dayEstimate['total_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (empty($dayEstimate['lines']))
                    <div class="text-muted">
                        No active Subscription deliveries on this day with the selected per-day price filter.
                    </div>
                @else
                    {{-- Subscriptions contributing today (by product) --}}
                    @if (!empty($dayEstimate['by_product']))
                        <div class="mb-2 fw-semibold">
                            Subscription products active on {{ $date->toFormattedDateString() }}
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:36px;">#</th>
                                        <th>Subscription Product</th>
                                        <th class="text-end">Active Subs (today)</th>
                                        <th class="text-end">Bundle Total / sub</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; $bpSum = 0; @endphp
                                    @foreach ($dayEstimate['by_product'] as $row)
                                        @php $bpSum += $row['subtotal']; @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $row['product_name'] }}</td>
                                            <td class="text-end mono">{{ $row['subscriptions'] }}</td>
                                            <td class="text-end mono">₹ {{ number_format($row['bundle_total'], 2) }}</td>
                                            <td class="text-end mono">
                                                <strong>₹ {{ number_format($row['subtotal'], 2) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Day Total</th>
                                        <th class="text-end mono">
                                            <strong>₹ {{ number_format($bpSum, 2) }}</strong>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    {{-- Price List per product (like your modal) --}}
                    @if (!empty($dayEstimate['by_product_items']))
                        @foreach ($dayEstimate['by_product_items'] as $pid => $group)
                            <div class="mb-1 fw-semibold">
                                {{ $group['product_name'] }} — Price List (per subscription)
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:36px;">#</th>
                                            <th>Item</th>
                                            <th class="text-end">Qty</th>
                                            <th>Unit</th>
                                            <th class="text-end">Item Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($group['items'] as $it)
                                            <tr>
                                                <td>{{ $it['idx'] }}</td>
                                                <td>{{ $it['item_name'] }}</td>
                                                <td class="text-end mono">{{ number_format($it['quantity'], 2) }}</td>
                                                <td>{{ $it['unit'] }}</td>
                                                <td class="text-end mono">
                                                    ₹ {{ number_format($it['item_price'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total</th>
                                            <th class="text-end mono">
                                                <strong>₹ {{ number_format($group['total'], 2) }}</strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endforeach
                    @endif

                    {{-- If you ever want to re-enable the per-unit item math, you already have it commented below --}}
                @endif
            </div>
        </div>

        {{-- Month Summary --}}
        <div class="card card-soft mb-4">
            <div class="card-header">
                <div class="hstack">
                    <div><strong>Month:</strong> {{ $monthStart->format('F Y') }}</div>
                    <div class="chip-inline">
                        <span>Distinct Items</span>
                        <span class="mono">{{ count($monthEstimate['by_item']) }}</span>
                    </div>
                    <div class="chip-inline">
                        <span>Est. Month Value</span>
                        <span class="mono">₹ {{ number_format($monthEstimate['total_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (empty($monthEstimate['by_item']))
                    <div class="text-muted">
                        No data for this month with the selected per-day price filter.
                    </div>
                @else
                    <div class="mb-2 fw-semibold">
                        Item-wise totals for {{ $monthStart->format('F Y') }}
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:36px;">#</th>
                                    <th>Item</th>
                                    <th class="text-end">Total Qty</th>
                                    <th class="text-end">
                                        Unit Price
                                        <small class="text-muted">(per unit)</small>
                                    </th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; $sumAmt = 0; @endphp
                                @foreach ($monthEstimate['by_item'] as $row)
                                    @php $sumAmt += $row['subtotal']; @endphp
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row['item_name'] }}</td>
                                        <td class="text-end mono">
                                            {{ number_format($row['qty'], 2) }}
                                            <span class="chip-inline">{{ $row['unit'] }}</span>
                                        </td>
                                        <td class="text-end mono">₹ {{ number_format($row['unit_price'], 2) }}</td>
                                        <td class="text-end mono">
                                            <strong>₹ {{ number_format($row['subtotal'], 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3"></th>
                                    <th>Month Totals</th>
                                    <th class="text-end mono">
                                        <strong>₹ {{ number_format($sumAmt, 2) }}</strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Day-by-day breakdown --}}
                    <details class="mt-2">
                        <summary class="mb-2 fw-semibold" style="cursor:pointer;">
                            Day-by-day breakdown
                        </summary>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Items</th>
                                        <th class="text-end">Est. Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthEstimate['per_day'] as $d => $data)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($d)->format('D, d M Y') }}</td>
                                            <td class="text-end mono">{{ count($data['lines']) }}</td>
                                            <td class="text-end mono">
                                                ₹ {{ number_format($data['total_cost'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif
            </div>
        </div>
    </div>
@endsection
