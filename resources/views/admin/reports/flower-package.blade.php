@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* --------- Global helpers --------- */
        .money {
            font-variant-numeric: tabular-nums;
        }

        .product-card {
            border-radius: 1rem;
        }

        .mini-stat {
            border-radius: .75rem;
        }

        /* --------- Sticky Filter Toolbar --------- */
        .filter-toolbar.sticky {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: saturate(1.1) blur(4px);
            background: rgba(248, 249, 250, .86);
            border-bottom: 1px solid #e9ecef;
        }

        .filter-toolbar .card {
            border-radius: 14px;
            border: 1px solid #e9ecef;
        }

        .filter-toolbar .card-body {
            padding: .9rem 1rem;
        }

        .filter-toolbar .row-tight {
            --bs-gutter-x: .75rem;
            --bs-gutter-y: .75rem;
        }

        /* Date input with icon */
        .date-wrap {
            position: relative;
        }

        .date-wrap .bi {
            position: absolute;
            left: .6rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }

        .date-wrap input[type="date"] {
            padding-left: 2rem;
        }

        .preset-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .preset-chips .btn {
            border-radius: 999px;
            padding: .35rem .75rem;
            line-height: 1.1;
        }

        /* Active state fix */
        .preset-chips .btn.active,
        .preset-chips .btn.btn-outline-secondary.active,
        .preset-chips .btn.btn-outline-secondary:active {
            font-weight: 700;
            color: #fff !important;
            /* make text visible */
            background-color: #6c757d;
            /* optional: match outline-secondary active bg */
            border-color: #6c757d;
            /* optional */
        }


        /* View segmented control */
        .segmented {
            display: inline-flex;
            border: 1px solid #ced4da;
            border-radius: .5rem;
            overflow: hidden;
            background: #fff;
        }

        .segmented a {
            padding: .45rem .85rem;
            text-decoration: none;
            color: #0d6efd;
            border-right: 1px solid #ced4da;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .segmented a:last-child {
            border-right: 0;
        }

        .segmented a.active {
            background: #0d6efd;
            color: #fff;
        }

        /* Actions alignment */
        .actions-wrap {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        @media (min-width: 992px) {
            .actions-wrap {
                justify-content: flex-end;
            }
        }

        /* --------- Native Disclosure (details/summary) --------- */
        details.disclosure {
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 2px 10px rgba(16, 24, 40, .04);
        }

        details.disclosure+details.disclosure {
            margin-top: .75rem;
        }

        details.disclosure>summary {
            list-style: none;
            cursor: pointer;
            padding: .9rem 1rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
        }

        details.disclosure>summary::-webkit-details-marker {
            display: none;
        }

        .summary-left h6,
        .summary-left h5 {
            margin: 0 0 .25rem 0;
        }

        .summary-left .text-muted {
            font-size: .95rem;
        }

        .summary-right {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .summary-right .badge {
            font-size: .9rem;
        }

        .chev {
            transition: transform .2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        details[open] .chev {
            transform: rotate(180deg);
        }

        .disclosure-body {
            border-top: 1px solid #e9ecef;
            padding: .9rem 1rem 1.1rem;
        }
    </style>
@endsection

@section('content')
    <div class="bg-light">
        <div class="container py-4">
              @php
                $tProducts = $tomorrowEstimate['products'] ?? [];
                $tGrand = $tomorrowEstimate['grand_total_amount'] ?? 0;
                $tTotals = $tomorrowEstimate['totals_by_item'] ?? [];

                // Build Tomorrow summary (from product items)
                $catBase = ['weight' => 0.0, 'volume' => 0.0, 'count' => 0.0]; // g, ml, pcs base
                $distinctItems = [];
                foreach ($tProducts as $row) {
                    foreach ($row['items'] ?? [] as $it) {
                        $cat = $it['category'] ?? 'count';
                        $catBase[$cat] += (float) ($it['total_qty_base'] ?? 0);
                        $distinctItems[strtolower(trim($it['item_name']))] = true;
                    }
                }
                $tomorrowDistinctItemCount = count($distinctItems);

                // Unit formatters for category totals
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

            <div class="filter-toolbar sticky">
                <form class="card shadow-sm" method="get" action="{{ route('admin.flowerEstimate') }}">
                    <input type="hidden" name="mode" value="{{ $mode }}" />
                    <div class="card-body">
                        <div class="row row-tight align-items-end">
                            <div class="col-12 col-lg-8">
                                <div class="row row-tight align-items-end">
                                    <div class="col-6 col-md-4">
                                        <label class="form-label mb-1">Start date</label>
                                        <div class="date-wrap">
                                            <i class="bi bi-calendar-event"></i>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ $start }}">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label mb-1">End date</label>
                                        <div class="date-wrap">
                                            <i class="bi bi-calendar-check"></i>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ $end }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label mb-1">View</label>
                                        <div class="segmented w-100">
                                            <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode' => 'day'])) }}"
                                                class="{{ $mode === 'day' ? 'active' : '' }}">
                                                <i class="bi bi-calendar-day"></i> Day
                                            </a>
                                            <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode' => 'month'])) }}"
                                                class="{{ $mode === 'month' ? 'active' : '' }}">
                                                <i class="bi bi-calendar3"></i> Month
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="actions-wrap mt-2 mt-lg-0">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-funnel"></i> Apply
                                    </button>
                                    <a href="{{ route('admin.flowerEstimate') }}"
                                        class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </div>

                        <div class="row row-tight mt-3">
                            <div class="col-12">
                                <label class="form-label mb-1">Quick presets</label>
                                <div class="preset-chips">
                                    <button type="submit" name="preset" value="today"
                                        class="btn btn-outline-secondary {{ $preset === 'today' ? 'active' : '' }}">Today</button>
                                    <button type="submit" name="preset" value="yesterday"
                                        class="btn btn-outline-secondary {{ $preset === 'yesterday' ? 'active' : '' }}">Yesterday</button>
                                    <button type="submit" name="preset" value="tomorrow"
                                        class="btn btn-outline-secondary {{ $preset === 'tomorrow' ? 'active' : '' }}">Tomorrow</button>
                                    <button type="submit" name="preset" value="this_month"
                                        class="btn btn-outline-secondary {{ $preset === 'this_month' ? 'active' : '' }}">This
                                        Month</button>
                                    <button type="submit" name="preset" value="last_month"
                                        class="btn btn-outline-secondary {{ $preset === 'last_month' ? 'active' : '' }}">Last
                                        Month</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @php
                $hasDaily = !empty($dailyEstimates) && count($dailyEstimates) > 0;
                $hasMonthly = !empty($monthlyEstimates) && count($monthlyEstimates) > 0;
            @endphp

            @if ($mode === 'day')
                @if (!$hasDaily)
                    <div class="alert alert-info mt-4">No data for the selected range.</div>
                @else
                    <div class="accordion mt-4" id="daysAccordion">
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
                                                Total Cost of Flower Per Day: <span
                                                    class="money">₹{{ number_format($grand, 2) }}</span>
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="{{ $dayId }}-body"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="{{ $dayId }}-header" data-bs-parent="#daysAccordion">
                                    <div class="accordion-body bg-white">
                                        @if (empty($products))
                                            <div class="alert alert-secondary">No active subscriptions on this day.</div>
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
                                                        <div class="card product-card border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                                    <div>
                                                                        <h5 class="mb-1">
                                                                            {{ $product?->name ?? 'Product #' . $pid }}
                                                                        </h5>
                                                                        <div class="text-muted">
                                                                            <strong>{{ $subsCount }}</strong> active
                                                                            subscription{{ $subsCount == 1 ? '' : 's' }}
                                                                            <span class="ms-2">(Flower Cost:
                                                                                ₹{{ number_format($bundlePerSub, 2) }})</span>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge bg-primary fs-6">
                                                                            Product Total: <span
                                                                                class="money">₹{{ number_format($productTotal, 2) }}</span>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="table-responsive mt-3">
                                                                    <table class="table table-sm table-hover align-middle">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th style="width:30%">Item</th>
                                                                                <th class="text-end">Per-Sub Qty</th>
                                                                                <th>Per-Sub Unit</th>
                                                                                <th class="text-end">Item Price (₹)</th>
                                                                                <th class="text-end">Total Qty</th>
                                                                                <th class="text-end">Total Price (₹)</th>
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
                                                                                    <td class="text-end money">
                                                                                        {{ number_format($it['item_price_per_sub'], 2) }}
                                                                                    </td>
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
                                                                                    <td colspan="6" class="text-muted">
                                                                                        No package items configured for this
                                                                                        product.</td>
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

                                            <div class="card border-0 shadow-sm mt-3">
                                                <div class="card-header bg-white"><strong>Totals by Item (All
                                                        Products)</strong></div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th class="text-end">Total Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($dayTotals as $it)
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
            @else
                @if (!$hasMonthly)
                    <div class="alert alert-info mt-4">No data for the selected range.</div>
                @else
                    <div class="accordion mt-4" id="monthsAccordion">
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
                                                Grand Total: <span class="money">₹{{ number_format($grand, 2) }}</span>
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="{{ $monthId }}-body"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="{{ $monthId }}-header" data-bs-parent="#monthsAccordion">
                                    <div class="accordion-body bg-white">
                                        @if (empty($products))
                                            <div class="alert alert-secondary">No active subscriptions in this month.</div>
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
                                                        <div class="card product-card border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                                    <div>
                                                                        <h5 class="mb-1">
                                                                            {{ $product?->name ?? 'Product #' . $pid }}
                                                                        </h5>
                                                                        <div class="text-muted">
                                                                            <strong>{{ $subsDays }}</strong>
                                                                            subscription-days
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge bg-primary fs-6">
                                                                            Product Total: <span
                                                                                class="money">₹{{ number_format($productTotal, 2) }}</span>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="table-responsive mt-3">
                                                                    <table class="table table-sm table-hover align-middle">
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
                                                                                        No items aggregated.</td>
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

                                            <div class="card border-0 shadow-sm mt-3">
                                                <div class="card-header bg-white"><strong>Totals by Item (All Products in
                                                        Month)</strong></div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm align-middle">
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

            <div class="row g-3">
                @foreach ($tProducts as $pid => $row)
                    @php
                        $product = $row['product'];
                        $subsCount = $row['subs_count'] ?? 0;
                        $items = $row['items'] ?? [];
                        $productTotal = $row['product_total'] ?? 0;
                        $bundlePerSub = $row['bundle_total_per_sub'] ?? 0;
                        $openFirst = $loop->first; // open only the first by default
                        $isRequests = ($product?->name ?? '') === 'On-demand Requests';
                    @endphp

                    <div class="col-12">
                        <details class="disclosure" {{ $openFirst ? 'open' : '' }}>
                            <summary>
                                <div class="summary-left">
                                    <h6 class="mb-1">{{ $product?->name ?? 'Product #' . $pid }}</h6>
                                    <div class="text-muted">
                                        <strong>{{ $subsCount }}</strong>
                                        {{ $isRequests ? 'request' : 'subscription' }}{{ $subsCount == 1 ? '' : 's' }}
                                        @if (!$isRequests && ($bundlePerSub ?? 0) > 0)
                                            <span class="ms-2">(Bundle / Sub:
                                                ₹{{ number_format($bundlePerSub, 2) }})</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="summary-right">
                                    <span class="badge bg-primary">
                                        Product Total: <span class="money">₹{{ number_format($productTotal, 2) }}</span>
                                    </span>
                                    <i class="bi bi-chevron-down chev"></i>
                                </div>
                            </summary>

                            <div class="disclosure-body">
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:30%">Item</th>
                                                <th class="text-end">Per-Sub Qty</th>
                                                <th>Per-Sub Unit</th>
                                                <th class="text-end">Item Price (₹)</th>
                                                <th class="text-end">Total Qty</th>
                                                <th class="text-end">Total Price (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($items as $it)
                                                <tr>
                                                    <td>{{ $it['item_name'] }}</td>
                                                    <td class="text-end">
                                                        {{ rtrim(rtrim(number_format($it['per_item_qty'], 3), '0'), '.') }}
                                                    </td>
                                                    <td>{{ strtoupper($it['per_item_unit']) }}</td>
                                                    <td class="text-end money">
                                                        {{ number_format($it['item_price_per_sub'], 2) }}</td>
                                                    <td class="text-end">
                                                        {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                        {{ $it['total_unit_disp'] }}
                                                    </td>
                                                    <td class="text-end money">
                                                        {{ number_format($it['total_price'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-muted">No package items
                                                        configured.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Keep accordion trigger state in sync (for day/month accordions only)
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
