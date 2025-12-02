{{-- resources/views/admin/reports/flower-package.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Poppins (page) + Nunito Sans (table) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            /* Core palette */
            --brand-blue: #e9f2ff;
            --brand-blue-edge: #cfe0ff;
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;

            /* Table */
            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #64748b;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 16px;

            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --accent-border: #bfdbfe;
            --danger: #b42318;
            --danger-soft: #fef2f2;
            --success: #047857;
            --success-soft: #ecfdf3;
            --neutral-soft: #f3f4f6;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        .container-page {
            max-width: 1320px;
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        /* ===== Toolbar (filters) ===== */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            padding: .85rem 1rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: minmax(0, 1.2fr) auto;
            align-items: center;
            box-shadow: var(--shadow);
            margin-bottom: 1.1rem;
        }

        .date-range {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: .85rem;
        }

        .date-range span {
            font-weight: 500;
        }

        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 170px;
        }

        .date-range input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .22);
        }

        .btn-chip {
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #0f172a;
            padding: .42rem .9rem;
            border-radius: 999px;
            font-weight: 500;
            cursor: pointer;
            font-size: .82rem;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            text-decoration: none;
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

        .btn-chip--active {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
        }

        .btn-chip--active::before {
            content: '●';
            opacity: .8;
        }

        .btn-apply {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-apply::before {
            content: '↻';
            font-size: .75rem;
            opacity: .75;
        }

        /* ===== Header band (summary) ===== */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 18px;
            padding: .9rem 1.2rem;
            box-shadow: var(--shadow);
            margin-bottom: .9rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .band h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--header-text);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band h3 span.label {
            font-size: .78rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.07);
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .chip span.icon {
            font-size: .9rem;
        }

        .chip.green {
            background: var(--chip-green);
            color: var(--chip-green-text);
            border-color: #c9f0d6;
        }

        .chip.orange {
            background: var(--chip-orange);
            color: var(--chip-orange-text);
            border-color: #ffd9b3;
        }

        .chip.blue {
            background: var(--chip-blue);
            color: var(--chip-blue-text);
            border-color: #bae6fd;
        }

        /* ===== Workbook (card shell) ===== */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-top: 1rem;
        }

        .workbook-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
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
            color: #4b5563;
            font-size: .84rem;
        }

        .workbook-tools {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        /* ===== Table wrapper / “Excel” style ===== */
        .excel-wrap {
            padding: 1rem 1.1rem 1.1rem;
            overflow: auto;
        }

        .excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            font-size: .9rem;
            border: 1px solid var(--table-border);
            border-radius: 14px;
            overflow: hidden;

            /* TABLE FONT: Nunito Sans */
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .excel thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            padding: .6rem .7rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 0;
            border-right: 1px solid rgba(55, 65, 81, 0.7);
            white-space: nowrap;
        }

         .excel tfoot th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            padding: .6rem .7rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 0;
            border-right: 1px solid rgba(55, 65, 81, 0.7);
            white-space: nowrap;
        }

        .excel thead th:first-child {
            border-top-left-radius: 14px;
        }

        .excel thead th:last-child {
            border-top-right-radius: 14px;
            border-right: none;
        }

        .excel tbody td {
            border-top: 1px solid var(--table-border);
            border-right: 1px solid var(--table-border);
            padding: .55rem .7rem;
            vertical-align: middle;
            color: var(--text);
            font-weight: 400;
            background: #fff;
        }

        .excel tbody tr:nth-child(even) td {
            background: var(--table-zebra);
        }

        .excel tbody tr:last-child td:first-child {
            border-bottom-left-radius: 14px;
        }

        .excel tbody tr:last-child td:last-child {
            border-bottom-right-radius: 14px;
        }

        .excel tbody tr:hover td {
            background: var(--table-hover);
        }

        .excel th,
        .excel td {
            font-variant-numeric: tabular-nums;
        }

        .excel tr td:first-child,
        .excel thead th:first-child {
            border-left: none;
        }

        .excel tbody tr td:last-child {
            border-right: none;
        }

        /* Column helpers */
        .col-index {
            width: 56px;
            text-align: right;
            color: #6b7280;
            font-size: .8rem;
        }

        .col-date {
            white-space: nowrap;
            font-size: .86rem;
            color: #4b5563;
        }

        .col-text {
            font-weight: 500;
        }

        .col-money {
            text-align: right;
        }

        .col-money span.currency {
            color: #6b7280;
            font-size: .8rem;
            margin-right: .18rem;
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
                gap: .4rem;
            }

            .workbook-tools {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">
        @php
            // Tomorrow summary (still computed as in original file; used by bottom disclosure if you enable it later)
            $tProducts = $tomorrowEstimate['products'] ?? [];
            $tGrand = $tomorrowEstimate['grand_total_amount'] ?? 0;
            $tTotals = $tomorrowEstimate['totals_by_item'] ?? [];

            $catBase = ['weight' => 0.0, 'volume' => 0.0, 'count' => 0.0];
            $distinctItems = [];
            foreach ($tProducts as $row) {
                foreach ($row['items'] ?? [] as $it) {
                    $cat = $it['category'] ?? 'count';
                    $catBase[$cat] += (float) ($it['total_qty_base'] ?? 0);
                    $distinctItems[strtolower(trim($it['item_name']))] = true;
                }
            }
            $tomorrowDistinctItemCount = count($distinctItems);

            $fmtCat = function (float $qtyBase, string $cat): array {
                if ($cat === 'weight') {
                    return $qtyBase >= 1000 ? [round($qtyBase / 1000, 3), 'kg'] : [round($qtyBase, 3), 'g'];
                }
                if ($cat === 'volume') {
                    return $qtyBase >= 1000 ? [round($qtyBase / 1000, 3), 'L'] : [round($qtyBase, 3), 'ml'];
                }
                return [round($qtyBase, 3), 'pcs'];
            };
            [$wQty, $wUnit] = $fmtCat($catBase['weight'], 'weight');
            [$vQty, $vUnit] = $fmtCat($catBase['volume'], 'volume');
            [$cQty, $cUnit] = $fmtCat($catBase['count'], 'count');
        @endphp

        {{-- ========= FILTER TOOLBAR (report-style) ========= --}}
        <form method="GET" action="{{ route('admin.flowerPackage') }}" id="filterForm" class="toolbar">
            <div class="date-range">
                <span>From</span>
                <input type="date" name="start_date" value="{{ $start }}" />
                <span>To</span>
                <input type="date" name="end_date" value="{{ $end }}" />
            </div>

            <div style="display:flex; gap:.4rem; flex-wrap:wrap; justify-content:flex-end">
                {{-- View toggle --}}
                <a href="{{ route('admin.flowerPackage', array_merge(request()->query(), ['mode' => 'day'])) }}"
                    class="btn-chip {{ $mode === 'day' ? 'btn-chip--active' : '' }}">
                    Day
                </a>
               
                <button class="btn-chip {{ $preset === 'yesterday' ? 'btn-chip--active' : '' }}" data-preset="yesterday"
                    type="button">
                    Yesterday
                </button>
                <button class="btn-chip {{ $preset === 'tomorrow' ? 'btn-chip--active' : '' }}" data-preset="tomorrow"
                    type="button">
                    Tomorrow
                </button>
                <button class="btn-chip {{ $preset === 'this_month' ? 'btn-chip--active' : '' }}" data-preset="this_month"
                    type="button">
                    This Month
                </button>
                <button class="btn-chip {{ $preset === 'last_month' ? 'btn-chip--active' : '' }}" data-preset="last_month"
                    type="button">
                    Last Month
                </button>

                <button class="btn-chip btn-apply" type="submit">
                    Apply
                </button>
            </div>

            <input type="hidden" name="mode" value="{{ $mode }}">
            <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">
        </form>

        @php
            $hasDaily = !empty($dailyEstimates) && count($dailyEstimates) > 0;
            $hasMonthly = !empty($monthlyEstimates) && count($monthlyEstimates) > 0;
        @endphp

        {{-- ========= SUMMARY BAND (range overview) ========= --}}
        @if (($mode === 'day' && $hasDaily) || ($mode === 'month' && $hasMonthly))
            @php
                // Day-mode metrics (provided from controller)
                $rangeTotalSafe = $rangeTotal ?? 0;
                $rangeAvgPerDaySafe = $rangeAvgPerDay ?? 0;
                $rangeDaysSafe = $rangeDaysWithData ?? 0;

                // Month-mode metrics (aggregate from $monthlyEstimates)
                $monthCount = 0;
                $monthGrand = 0;
                if ($mode === 'month' && $hasMonthly) {
                    $monthCount = count($monthlyEstimates ?? []);
                    foreach ($monthlyEstimates as $mblock) {
                        $monthGrand += $mblock['grand_total'] ?? 0;
                    }
                }
                $monthAvg = $monthCount ? $monthGrand / $monthCount : 0;
            @endphp

            <div class="band">
              
                <div class="chips">
                    @if ($mode === 'day')
                        <span class="chip green">
                            <span class="icon">💰</span>
                            <span>Total Estimate</span>
                            ₹{{ number_format($rangeTotalSafe, 2) }}
                        </span>
                        <span class="chip orange">
                            <span class="icon">📅</span>
                            <span>Avg / Day</span>
                            ₹{{ number_format($rangeAvgPerDaySafe, 2) }}
                        </span>
                        <span class="chip blue">
                            <span class="icon">✅</span>
                            <span>Active Days</span>
                            {{ number_format($rangeDaysSafe) }}
                        </span>
                    @else
                        <span class="chip green">
                            <span class="icon">💰</span>
                            <span>Grand Total (All Months)</span>
                            ₹{{ number_format($monthGrand, 2) }}
                        </span>
                        <span class="chip orange">
                            <span class="icon">📅</span>
                            <span>Months</span>
                            {{ number_format($monthCount) }}
                        </span>
                            <span class="chip blue">
                                <span class="icon">📊</span>
                                <span>Avg / Month</span>
                                ₹{{ number_format($monthAvg, 2) }}
                            </span>
                    @endif
                </div>
            </div>
        @endif

        {{-- ========= DAY MODE: per-day table ========= --}}
        @if ($mode === 'day' && $hasDaily)
            <div class="workbook">
                <div class="workbook-head">
                    <div>
                        <div class="workbook-sub">
                            Range: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} —
                            {{ \Carbon\Carbon::parse($end)->format('d M Y') }} |
                            Days with data: <strong>{{ $rangeDaysWithData }}</strong>
                        </div>
                    </div>
                </div>
                <div class="excel-wrap">
                    <table class="excel">
                        <thead>
                            <tr>
                                <th class="col-index">#</th>
                                <th class="col-date">Date</th>
                                <th class="col-money">Total Estimate (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $row = 0; @endphp
                            @foreach ($dailyEstimates as $date => $payload)
                                @php
                                    $row++;
                                    $grand = $payload['grand_total_amount'] ?? 0;
                                @endphp
                                <tr>
                                    <td class="col-index">{{ $row }}</td>
                                    <td class="col-date">
                                        {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
                                    </td>
                                    <td class="col-money">
                                        <span class="currency">₹</span>{{ number_format($grand, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="col-text">
                                    Subtotal ({{ $rangeDaysWithData }} active
                                    day{{ $rangeDaysWithData == 1 ? '' : 's' }})
                                </th>
                                <th class="col-money">
                                    <span class="currency">₹</span>{{ number_format($rangeTotal, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @elseif ($mode === 'day' && !$hasDaily)
            <div class="workbook">
                <div class="workbook-head">
                    <div>
                        <div class="workbook-title">Estimate of Flower Cost — By Day</div>
                        <div class="workbook-sub">
                            No data for the selected range.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ========= DETAIL FLOWER ESTIMATE (DAY / MONTH) ========= --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Detail Flower Estimate</div>
                   
                </div>
            </div>
            <div class="excel-wrap">
                @if ($mode === 'day')
                    @if (!$hasDaily)
                        <div class="alert alert-info mb-0">No data for the selected range.</div>
                    @else
                        <div class="accordion" id="daysAccordion">
                            @foreach ($dailyEstimates as $date => $payload)
                                @php
                                    $dayId = 'day-' . \Illuminate\Support\Str::slug($date);
                                    $grand = $payload['grand_total_amount'] ?? 0;
                                    $products = $payload['products'] ?? [];
                                    $dayTotals = $payload['totals_by_item'] ?? [];
                                @endphp

                                <div class="accordion-item shadow-sm mb-3">
                                    <h2 class="accordion-header" id="{{ $dayId }}-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $dayId }}-body"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="{{ $dayId }}-body">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</strong>
                                                    <span class="text-muted ms-2">({{ number_format(count($products)) }}
                                                        products)</span>
                                                </div>
                                                <span class="badge bg-success fs-6">
                                                    Total Cost of Flower Per Day:
                                                    <span class="money">₹{{ number_format($grand, 2) }}</span>
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="{{ $dayId }}-body"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="{{ $dayId }}-header" data-bs-parent="#daysAccordion">
                                        <div class="accordion-body bg-white">
                                            @if (empty($products))
                                                <div class="alert alert-secondary mb-0">No active subscriptions on this day.
                                                </div>
                                            @else
                                                <div class="row g-3">
                                                    @foreach ($products as $pid => $row)
                                                        @php
                                                            $product = $row['product'];
                                                            $subsCount = $row['subs_count'] ?? 0;
                                                            $items = $row['items'] ?? [];
                                                            $productTotal = $row['product_total'] ?? 0;
                                                            $bundlePerSub = $row['bundle_total_per_sub'] ?? 0;
                                                        @endphp

                                                        <div class="col-12">
                                                            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                                                                <div class="card-body">
                                                                    <div
                                                                        class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                                                                        {{-- LEFT: Package info --}}
                                                                        <div class="d-flex align-items-start gap-3">
                                                                          <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0"
                                                                                style="width: 42px; height: 42px;">
                                                                                <i class="bi bi-box-seam-fill"></i>
                                                                            </div>

                                                                            <div>
                                                                                <div
                                                                                    class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                                                    <span
                                                                                        class="badge rounded-pill bg-warning text-dark text-uppercase small fw-semibold">
                                                                                        Package
                                                                                    </span>
                                                                                    <h5 class="mb-0">
                                                                                        {{ $product?->name ?? 'Product #' . $pid }}
                                                                                    </h5>
                                                                                </div>

                                                                                <div
                                                                                    class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                                                    <span
                                                                                        class="badge rounded-pill bg-light text-secondary border small">
                                                                                        <i
                                                                                            class="bi bi-people-fill me-1"></i>
                                                                                        {{ $subsCount }}
                                                                                        subscription{{ $subsCount == 1 ? '' : 's' }}
                                                                                    </span>

                                                                                    @if (!empty($product?->per_day_price))
                                                                                        <span
                                                                                            class="badge rounded-pill bg-info-subtle text-info-emphasis border-0 small">
                                                                                            <i
                                                                                                class="bi bi-currency-rupee me-1"></i>
                                                                                            Package Cost / day:
                                                                                            <span class="money">
                                                                                                ₹{{ number_format($product->per_day_price, 2) }}
                                                                                            </span>
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {{-- RIGHT: Flower cost (computed) --}}
                                                                        <div class="text-md-end">
                                                                            <div class="small text-muted mb-1">
                                                                                Computed flower cost
                                                                            </div>
                                                                            <div
                                                                                class="d-flex flex-column align-items-md-end gap-2">
                                                                                <span
                                                                                    class="badge bg-primary-subtle text-primary fw-semibold">
                                                                                    <i class="bi bi-flower1 me-1"></i>
                                                                                    Per subscription:
                                                                                    <span class="money">
                                                                                        ₹{{ number_format($bundlePerSub, 2) }}
                                                                                    </span>
                                                                                </span>

                                                                                <span
                                                                                    class="badge bg-success-subtle text-success fw-semibold">
                                                                                    <i class="bi bi-wallet2 me-1"></i>
                                                                                    Total flower cost:
                                                                                    <span class="money">
                                                                                        ₹{{ number_format($productTotal, 2) }}
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Items table --}}
                                                                    <div class="table-responsive mt-3">
                                                                        <table
                                                                            class="table table-sm table-hover align-middle mb-0">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th style="width:30%">Flowers</th>
                                                                                    <th class="text-end">Qty</th>
                                                                                    <th>Unit</th>
                                                                                    <th class="text-center">Unit Price (₹)</th>
                                                                                    <th class="text-center">Total Qty</th>
                                                                                    <th class="text-center">Total Price (₹)
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @forelse($items as $it)
                                                                                    <tr>
                                                                                        <td>{{ $it['item_name'] }}</td>
                                                                                        <td class="text-end">
                                                                                            {{ rtrim(rtrim(number_format($it['per_item_qty'], 3), '0'), '.') }}
                                                                                        </td>
                                                                                        <td>{{ strtoupper($it['per_item_unit']) }}
                                                                                        </td>
                                                                                        <td class="text-center money">
                                                                                            {{ number_format($it['item_price_per_sub'], 2) }}
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                                                            {{ $it['total_unit_disp'] }}
                                                                                        </td>
                                                                                        <td class="text-center money">
                                                                                            {{ number_format($it['total_price'], 2) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                @empty
                                                                                    <tr>
                                                                                        <td colspan="6" class="text-muted">
                                                                                            No package items configured for this
                                                                                            product.
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforelse
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- MONTH MODE --}}
                    @if (!$hasMonthly)
                        <div class="alert alert-info mb-0">No data for the selected range.</div>
                    @else
                        <div class="accordion" id="monthsAccordion">
                            @foreach ($monthlyEstimates as $mkey => $mblock)
                                @php
                                    $monthId = 'month-' . \Illuminate\Support\Str::slug($mkey);
                                    $grand = $mblock['grand_total'] ?? 0;
                                    $products = $mblock['products'] ?? [];
                                    $mTotals = $mblock['totals_by_item'] ?? [];
                                @endphp
                                <div class="accordion-item shadow-sm mb-3">
                                    <h2 class="accordion-header" id="{{ $monthId }}-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $monthId }}-body"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="{{ $monthId }}-body">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $mblock['month_label'] }}</strong>
                                                    <span class="text-muted ms-2">({{ number_format(count($products)) }}
                                                        products)</span>
                                                </div>
                                                <span class="badge bg-success fs-6">
                                                    Grand Total:
                                                    <span class="money">₹{{ number_format($grand, 2) }}</span>
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="{{ $monthId }}-body"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="{{ $monthId }}-header" data-bs-parent="#monthsAccordion">
                                        <div class="accordion-body bg-white">
                                            @if (empty($products))
                                                <div class="alert alert-secondary mb-0">No active subscriptions in this month.
                                                </div>
                                            @else
                                                <div class="row g-3">
                                                    @foreach ($products as $pid => $prow)
                                                        @php
                                                            $product = $prow['product'];
                                                            $items = $prow['items'] ?? [];
                                                            $productTotal = $prow['product_total'] ?? 0;
                                                            $subsDays = $prow['subs_days'] ?? 0;
                                                        @endphp
                                                        <div class="col-12">
                                                            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                                                                <div class="card-body">
                                                                    <div
                                                                        class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                                                                        {{-- LEFT: Package info --}}
                                                                        <div class="d-flex align-items-start gap-3">
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0"
                                                                                style="width: 42px; height: 42px;">
                                                                                <i class="bi bi-box-seam-fill"></i>
                                                                            </div>
                                                                            <div>
                                                                                <div
                                                                                    class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                                                    <span
                                                                                        class="badge rounded-pill bg-warning text-dark text-uppercase small fw-semibold">
                                                                                        Package
                                                                                    </span>
                                                                                    <h5 class="mb-0">
                                                                                        {{ $product?->name ?? 'Product #' . $pid }}
                                                                                    </h5>
                                                                                </div>
                                                                                <div
                                                                                    class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                                                    <span
                                                                                        class="badge rounded-pill bg-light text-secondary border small">
                                                                                        <i
                                                                                            class="bi bi-calendar-week me-1"></i>
                                                                                        {{ $subsDays }} subscription-days
                                                                                    </span>
                                                                                    @if (!empty($product?->per_day_price))
                                                                                        <span
                                                                                            class="badge rounded-pill bg-info-subtle text-info-emphasis border-0 small">
                                                                                            <i
                                                                                                class="bi bi-currency-rupee me-1"></i>
                                                                                            Package Cost / day:
                                                                                            <span class="money">
                                                                                                ₹{{ number_format($product->per_day_price, 2) }}
                                                                                            </span>
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {{-- RIGHT: product total --}}
                                                                        <div class="text-md-end">
                                                                            <span
                                                                                class="badge bg-primary-subtle text-primary fw-semibold">
                                                                                Product Total:
                                                                                <span class="money">
                                                                                    ₹{{ number_format($productTotal, 2) }}
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="table-responsive mt-3">
                                                                        <table
                                                                            class="table table-sm table-hover align-middle mb-0">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th style="width:30%">Item</th>
                                                                                    <th class="text-end">Total Qty (Month)</th>
                                                                                    <th class="text-end">Total Price (₹)</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @forelse($items as $it)
                                                                                    <tr>
                                                                                        <td>{{ $it['item_name'] }}</td>
                                                                                        <td class="text-end">
                                                                                            {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                                                            {{ $it['total_unit_disp'] }}
                                                                                        </td>
                                                                                        <td class="text-end money">
                                                                                            {{ number_format($it['total_price'], 2) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                @empty
                                                                                    <tr>
                                                                                        <td colspan="3" class="text-muted">
                                                                                            No items aggregated.
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforelse
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="card border-0 shadow-sm mt-3" style="border-radius: 1rem;">
                                                    <div class="card-header bg-white">
                                                        <strong>Totals by Item (All Products in Month)</strong>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th class="text-end">Total Qty</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($mTotals as $it)
                                                                        <tr>
                                                                            <td>{{ $it['item_name'] }}</td>
                                                                            <td class="text-end">
                                                                                {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                                                {{ $it['total_unit_disp'] }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="2" class="text-muted">No items.
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- ============== TOMORROW DISCLOSURE (bottom, still optional/commented in original) ================= --}}
        {{-- Keep your existing tomorrow section here if you decide to show it later --}}
    </div>
@endsection

@section('scripts')
    {{-- Bootstrap JS for accordions (same as your original file) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Presets
        document.querySelectorAll('[data-preset]').forEach(btn => {
            btn.addEventListener('click', () => {
                const presetInput = document.getElementById('presetInput');
                if (presetInput) {
                    presetInput.value = btn.getAttribute('data-preset');
                }
                document.getElementById('filterForm').submit();
            });
        });

        // Clear preset when manual dates change
        ['start_date', 'end_date'].forEach(n => {
            const el = document.querySelector(`input[name="${n}"]`);
            if (el) el.addEventListener('change', () => {
                const presetInput = document.getElementById('presetInput');
                if (presetInput) presetInput.value = '';
            });
        });

        // Keep accordion trigger state in sync (for day/month accordions)
        document.addEventListener('shown.bs.collapse', function(e) {
            const btn = document.querySelector('[data-bs-target="#' + e.target.id + '"]');
            if (btn) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
        document.addEventListener('hidden.bs.collapse', function(e) {
            const btn = document.querySelector('[data-bs-target="#' + e.target.id + '"]');
            if (btn) {
                btn.classList.add('collapsed');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
@endsection
