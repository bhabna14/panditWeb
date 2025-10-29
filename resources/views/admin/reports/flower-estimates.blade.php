@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* --------- Global helpers --------- */
        .money { font-variant-numeric: tabular-nums; }
        .product-card { border-radius: 1rem; }
        .mini-stat { border-radius: .75rem; }

        /* --------- Sticky Filter Toolbar --------- */
        .filter-toolbar.sticky {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: saturate(1.1) blur(4px);
            background: rgba(248,249,250,.86); /* bg-light w/ alpha */
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

        /* Date input with icon (pure CSS) */
        .date-wrap { position: relative; }
        .date-wrap .bi {
            position: absolute; left: .6rem; top: 50%;
            transform: translateY(-50%); color: #6c757d; pointer-events: none;
        }
        .date-wrap input[type="date"] { padding-left: 2rem; }

        /* Quick presets as pill chips */
        .preset-chips { display: flex; flex-wrap: wrap; gap: .5rem; }
        .preset-chips .btn {
            border-radius: 999px; padding: .35rem .75rem; line-height: 1.1;
        }
        .preset-chips .btn.active { font-weight: 700; }

        /* View segmented control */
        .segmented {
            display: inline-flex; border: 1px solid #ced4da; border-radius: .5rem; overflow: hidden; background: #fff;
        }
        .segmented a {
            padding: .45rem .85rem; text-decoration: none; color: #0d6efd; border-right: 1px solid #ced4da;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .segmented a:last-child { border-right: 0; }
        .segmented a.active { background: #0d6efd; color: #fff; }

        /* Actions alignment */
        .actions-wrap { display: flex; gap: .5rem; flex-wrap: wrap; }
        @media (min-width: 992px) {
            .actions-wrap { justify-content: flex-end; }
        }

        /* Section headings/tables (unchanged from your layout) */
        .nav-pills .nav-link.active { font-weight: 600; }
        .filter-card .row > [class*="col-"] { min-width: 0; }

        /* --- Tiny UX polish for collapsible product rows --- */
        .card-toggle {
            display: inline-flex; align-items: center; gap: .4rem; cursor: pointer;
        }
        .card-toggle .chev { transition: transform .2s ease; }
        .card-toggle[aria-expanded="true"] .chev { transform: rotate(180deg); }
    </style>
@endsection

@section('content')
<div class="bg-light">
    <div class="container py-4">

        {{-- ===================================== --}}
        {{-- FILTER TOOLBAR (Redesigned & Sticky) --}}
        {{-- ===================================== --}}
        <div class="filter-toolbar sticky">
            <form class="card shadow-sm" method="get" action="{{ route('admin.flowerEstimate') }}">
                {{-- Keep current view (day/month) on submit --}}
                <input type="hidden" name="mode" value="{{ $mode }}" />

                <div class="card-body">
                    {{-- Row 1: Dates + View --}}
                    <div class="row row-tight align-items-end">
                        <div class="col-12 col-lg-8">
                            <div class="row row-tight align-items-end">
                                <div class="col-6 col-md-4">
                                    <label class="form-label mb-1">Start date</label>
                                    <div class="date-wrap">
                                        <i class="bi bi-calendar-event"></i>
                                        <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label mb-1">End date</label>
                                    <div class="date-wrap">
                                        <i class="bi bi-calendar-check"></i>
                                        <input type="date" name="end_date" class="form-control" value="{{ $end }}">
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

                        {{-- Actions (Apply/Reset) --}}
                        <div class="col-12 col-lg-4">
                            <div class="actions-wrap mt-2 mt-lg-0">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-funnel"></i> Apply
                                </button>
                                <a href="{{ route('admin.flowerEstimate') }}" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Quick Presets --}}
                    <div class="row row-tight mt-3">
                        <div class="col-12">
                            <label class="form-label mb-1">Quick presets</label>
                            <div class="preset-chips">
                                <button type="submit" name="preset" value="today"
                                    class="btn btn-outline-secondary {{ $preset === 'today' ? 'active' : '' }}">
                                    Today
                                </button>
                                <button type="submit" name="preset" value="yesterday"
                                    class="btn btn-outline-secondary {{ $preset === 'yesterday' ? 'active' : '' }}">
                                    Yesterday
                                </button>
                                <button type="submit" name="preset" value="tomorrow"
                                    class="btn btn-outline-secondary {{ $preset === 'tomorrow' ? 'active' : '' }}">
                                    Tomorrow
                                </button>
                                <button type="submit" name="preset" value="this_month"
                                    class="btn btn-outline-secondary {{ $preset === 'this_month' ? 'active' : '' }}">
                                    This Month
                                </button>
                                <button type="submit" name="preset" value="last_month"
                                    class="btn btn-outline-secondary {{ $preset === 'last_month' ? 'active' : '' }}">
                                    Last Month
                                </button>
                            </div>
                        </div>
                    </div>
                </div> {{-- /card-body --}}
            </form>
        </div>

        {{-- ================================ --}}
        {{-- SELECTED RANGE — GRAND TOTALS    --}}
        {{-- ================================ --}}
        @php
            $rByCat = $rangeTotals['by_category'] ?? [];
            $rByItem = $rangeTotals['by_item'] ?? [];
        @endphp
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Selected Range — Grand Totals (Quantity)</h5>
                    <small class="text-muted">
                        From {{ \Carbon\Carbon::parse($start)->toFormattedDateString() }}
                        to {{ \Carbon\Carbon::parse($end)->toFormattedDateString() }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white"><strong>Totals by Item (All Products)</strong></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:55%">Item</th>
                                                <th class="text-end" style="width:45%">Total Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($rByItem as $it)
                                                <tr>
                                                    <td>{{ $it['item_name'] }}</td>
                                                    <td class="text-end">
                                                        {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                        {{ $it['total_unit_disp'] }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted">No items in range.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted">Units auto-scale (kg/g, L/ml, pcs).</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white"><strong>Totals by Category</strong></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Category</th>
                                                <th class="text-end">Total Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($rByCat as $row)
                                                <tr>
                                                    <td>{{ $row['label'] }}</td>
                                                    <td class="text-end">
                                                        {{ rtrim(rtrim(number_format($row['total_qty_disp'], 3), '0'), '.') }}
                                                        {{ $row['total_unit_disp'] }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-muted">No quantities in range.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================ --}}
        {{-- TOMORROW ESTIMATE (redesigned)  --}}
        {{-- ================================ --}}
        @php
            use Illuminate\Support\Str;

            $tProducts = $tomorrowEstimate['products'] ?? [];
            $tGrand    = $tomorrowEstimate['grand_total_amount'] ?? 0;
            $tTotals   = $tomorrowEstimate['totals_by_item'] ?? [];

            // --- Build Tomorrow summary (from product items we already have) ---
            $catBase = ['weight' => 0.0, 'volume' => 0.0, 'count' => 0.0]; // g, ml, pcs base
            $distinctItems = [];

            foreach ($tProducts as $row) {
                foreach (($row['items'] ?? []) as $it) {
                    $cat = $it['category'] ?? 'count';
                    $catBase[$cat] += (float)($it['total_qty_base'] ?? 0);
                    $distinctItems[strtolower(trim($it['item_name']))] = true;
                }
            }

            $tomorrowDistinctItemCount = count($distinctItems);

            // --- Simple auto unit formatters for category totals ---
            $fmtCat = function(float $qtyBase, string $cat): array {
                if ($cat === 'weight') {
                    // base grams → show kg if >= 1000
                    if ($qtyBase >= 1000) return [round($qtyBase / 1000, 3), 'kg'];
                    return [round($qtyBase, 3), 'g'];
                }
                if ($cat === 'volume') {
                    // base ml → show L if >= 1000
                    if ($qtyBase >= 1000) return [round($qtyBase / 1000, 3), 'L'];
                    return [round($qtyBase, 3), 'ml'];
                }
                // count
                return [round($qtyBase, 3), 'pcs'];
            };

            [$wQty, $wUnit] = $fmtCat($catBase['weight'], 'weight');
            [$vQty, $vUnit] = $fmtCat($catBase['volume'], 'volume');
            [$cQty, $cUnit] = $fmtCat($catBase['count' ], 'count' );
        @endphp

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Tomorrow Estimate — {{ \Carbon\Carbon::parse($tomorrowDate)->toFormattedDateString() }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.assignPickupForm', ['date' => $tomorrowDate]) }}" class="btn btn-warning">
                            <i class="bi bi-truck"></i> Assign Vendor
                        </a>
                        <span class="badge bg-success fs-6">
                            Grand Total: <span class="money">₹{{ number_format($tGrand, 2) }}</span>
                        </span>
                    </div>
                </div>
                <small class="text-muted d-block mt-1">
                    Rule: <code>start_date ≤ tomorrow ≤ COALESCE(new_date, end_date)</code>, excluding paused (between
                    <code>pause_start_date</code> and <code>pause_end_date</code>).
                </small>
            </div>
            <div class="card-body bg-white">
                @if (empty($tProducts))
                    <div class="alert alert-secondary">No active subscriptions tomorrow.</div>
                @else
                    {{-- ======= Summary strip ======= --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-3">
                            <div class="mini-stat p-3 bg-light border">
                                <div class="text-muted">Distinct Items</div>
                                <div class="fs-4 fw-bold">{{ number_format($tomorrowDistinctItemCount) }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mini-stat p-3 bg-light border">
                                <div class="text-muted">Total Weight</div>
                                <div class="fs-4 fw-bold">
                                    {{ rtrim(rtrim(number_format($wQty, 3), '0'), '.') }} {{ $wUnit }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mini-stat p-3 bg-light border">
                                <div class="text-muted">Total Volume</div>
                                <div class="fs-4 fw-bold">
                                    {{ rtrim(rtrim(number_format($vQty, 3), '0'), '.') }} {{ $vUnit }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mini-stat p-3 bg-light border">
                                <div class="text-muted">Total Count</div>
                                <div class="fs-4 fw-bold">
                                    {{ rtrim(rtrim(number_format($cQty, 3), '0'), '.') }} {{ $cUnit }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ======= Products (collapsible) ======= --}}
                    <div class="row g-3">
                        @foreach ($tProducts as $pid => $row)
                            @php
                                $product       = $row['product'];
                                $subsCount     = $row['subs_count'] ?? 0;
                                $items         = $row['items'] ?? [];
                                $productTotal  = $row['product_total'] ?? 0;
                                $bundlePerSub  = $row['bundle_total_per_sub'] ?? 0;

                                $panelId = 'tomorrow-p-' . \Illuminate\Support\Str::slug((string)$pid . '-' . ($product?->name ?? 'product'));
                                $showNow = $loop->first; // first one opened by default
                            @endphp

                            <div class="col-12">
                                <div class="card product-card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <h6 class="mb-1">{{ $product?->name ?? 'Product #' . $pid }}</h6>
                                                <div class="text-muted">
                                                    <strong>{{ $subsCount }}</strong> subscription{{ $subsCount == 1 ? '' : 's' }}
                                                    <span class="ms-2">(Bundle / Sub: ₹{{ number_format($bundlePerSub, 2) }})</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary">Product Total:
                                                    <span class="money">₹{{ number_format($productTotal, 2) }}</span>
                                                </span>
                                                <a class="card-toggle text-decoration-none"
                                                   data-bs-toggle="collapse"
                                                   href="#{{ $panelId }}"
                                                   role="button"
                                                   aria-expanded="{{ $showNow ? 'true' : 'false' }}"
                                                   aria-controls="{{ $panelId }}">
                                                    <span>Details</span>
                                                    <i class="bi bi-chevron-down chev"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="collapse {{ $showNow ? 'show' : '' }}" id="{{ $panelId }}">
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
                                                                <td>{{ strtoupper($it['per_item_unit']) }}</td>
                                                                <td class="text-end money">{{ number_format($it['item_price_per_sub'], 2) }}</td>
                                                                <td class="text-end">
                                                                    {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                                    {{ $it['total_unit_disp'] }}
                                                                </td>
                                                                <td class="text-end money">{{ number_format($it['total_price'], 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-muted">No package items configured.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> {{-- /collapse --}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ======= Tomorrow Totals by Item ======= --}}
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-white">
                            <strong>Tomorrow — Totals by Item (All Products)</strong>
                        </div>
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
                                        @forelse($tTotals as $it)
                                            <tr>
                                                <td>{{ $it['item_name'] }}</td>
                                                <td class="text-end">
                                                    {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                    {{ $it['total_unit_disp'] }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">No items.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ======= NEW: Tomorrow Totals by Category ======= --}}
                    @php
                        $catRows = [
                            ['label' => 'Weight', 'qty' => $wQty, 'unit' => $wUnit],
                            ['label' => 'Volume', 'qty' => $vQty, 'unit' => $vUnit],
                            ['label' => 'Count' , 'qty' => $cQty, 'unit' => $cUnit],
                        ];
                    @endphp
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-white">
                            <strong>Tomorrow — Totals by Category</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Total Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($catRows as $r)
                                            <tr>
                                                <td>{{ $r['label'] }}</td>
                                                <td class="text-end">
                                                    {{ rtrim(rtrim(number_format($r['qty'], 3), '0'), '.') }} {{ $r['unit'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <small class="text-muted">Units auto-scale (kg/g, L/ml, pcs).</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================================ --}}
        {{-- DAY / MONTH VIEWS (unchanged)   --}}
        {{-- ================================ --}}
        @php
            $hasDaily   = !empty($dailyEstimates) && count($dailyEstimates) > 0;
            $hasMonthly = !empty($monthlyEstimates) && count($monthlyEstimates) > 0;
        @endphp

        @if ($mode === 'day')
            @if (!$hasDaily)
                <div class="alert alert-info mt-4">No data for the selected range.</div>
            @else
                <div class="accordion mt-4" id="daysAccordion">
                    @foreach ($dailyEstimates as $date => $payload)
                        @php
                            $dayId     = 'day-' . \Illuminate\Support\Str::slug($date);
                            $grand     = $payload['grand_total_amount'] ?? 0;
                            $products  = $payload['products'] ?? [];
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
                                            <span class="text-muted ms-2">({{ number_format(count($products)) }} products)</span>
                                        </div>
                                        <span class="badge bg-success fs-6">
                                            Grand Total: <span class="money">₹{{ number_format($grand, 2) }}</span>
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="{{ $dayId }}-body" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 aria-labelledby="{{ $dayId }}-header" data-bs-parent="#daysAccordion">
                                <div class="accordion-body bg-white">
                                    @if (empty($products))
                                        <div class="alert alert-secondary">No active subscriptions on this day.</div>
                                    @else
                                        <div class="row g-3">
                                            @foreach ($products as $pid => $row)
                                                @php
                                                    $product      = $row['product'];
                                                    $subsCount    = $row['subs_count'] ?? 0;
                                                    $items        = $row['items'] ?? [];
                                                    $productTotal = $row['product_total'] ?? 0;
                                                    $bundlePerSub = $row['bundle_total_per_sub'] ?? 0;
                                                @endphp

                                                <div class="col-12">
                                                    <div class="card product-card border-0 shadow-sm">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                                <div>
                                                                    <h5 class="mb-1">{{ $product?->name ?? 'Product #' . $pid }}</h5>
                                                                    <div class="text-muted">
                                                                        <strong>{{ $subsCount }}</strong> active subscription{{ $subsCount == 1 ? '' : 's' }}
                                                                        <span class="ms-2">(Bundle / Sub: ₹{{ number_format($bundlePerSub, 2) }})</span>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <span class="badge bg-primary fs-6">Product Total:
                                                                        <span class="money">₹{{ number_format($productTotal, 2) }}</span>
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
                                                                                <td>{{ strtoupper($it['per_item_unit']) }}</td>
                                                                                <td class="text-end money">{{ number_format($it['item_price_per_sub'], 2) }}</td>
                                                                                <td class="text-end">
                                                                                    {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                                                    {{ $it['total_unit_disp'] }}
                                                                                </td>
                                                                                <td class="text-end money">{{ number_format($it['total_price'], 2) }}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-muted">No package items configured for this product.</td>
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

                                        {{-- Day Totals by Item --}}
                                        <div class="card border-0 shadow-sm mt-3">
                                            <div class="card-header bg-white">
                                                <strong>Totals by Item (All Products)</strong>
                                            </div>
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
                                                                    <td colspan="2" class="text-muted">No items.</td>
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
            @php $hasMonthly = !empty($monthlyEstimates) && count($monthlyEstimates) > 0; @endphp
            @if (!$hasMonthly)
                <div class="alert alert-info mt-4">No data for the selected range.</div>
            @else
                <div class="accordion mt-4" id="monthsAccordion">
                    @foreach ($monthlyEstimates as $mkey => $mblock)
                        @php
                            $monthId  = 'month-' . \Illuminate\Support\Str::slug($mkey);
                            $grand    = $mblock['grand_total'] ?? 0;
                            $products = $mblock['products'] ?? [];
                            $mTotals  = $mblock['totals_by_item'] ?? [];
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
                                            <span class="text-muted ms-2">({{ number_format(count($products)) }} products)</span>
                                        </div>
                                        <span class="badge bg-success fs-6">
                                            Grand Total: <span class="money">₹{{ number_format($grand, 2) }}</span>
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="{{ $monthId }}-body" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 aria-labelledby="{{ $monthId }}-header" data-bs-parent="#monthsAccordion">
                                <div class="accordion-body bg-white">
                                    @if (empty($products))
                                        <div class="alert alert-secondary">No active subscriptions in this month.</div>
                                    @else
                                        <div class="row g-3">
                                            @foreach ($products as $pid => $prow)
                                                @php
                                                    $product      = $prow['product'];
                                                    $items        = $prow['items'] ?? [];
                                                    $productTotal = $prow['product_total'] ?? 0;
                                                    $subsDays     = $prow['subs_days'] ?? 0;
                                                @endphp
                                                <div class="col-12">
                                                    <div class="card product-card border-0 shadow-sm">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                                <div>
                                                                    <h5 class="mb-1">{{ $product?->name ?? 'Product #' . $pid }}</h5>
                                                                    <div class="text-muted">
                                                                        <strong>{{ $subsDays }}</strong> subscription-days
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <span class="badge bg-primary fs-6">Product Total:
                                                                        <span class="money">₹{{ number_format($productTotal, 2) }}</span>
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
                                                                                <td class="text-end money">{{ number_format($it['total_price'], 2) }}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="3" class="text-muted">No items aggregated.</td>
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

                                        {{-- Month Totals by Item --}}
                                        <div class="card border-0 shadow-sm mt-3">
                                            <div class="card-header bg-white">
                                                <strong>Totals by Item (All Products in Month)</strong>
                                            </div>
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
                                                                    <td colspan="2" class="text-muted">No items.</td>
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
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
