@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .sticky-top-filter {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .product-card {
            border-radius: 1rem;
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        .nav-pills .nav-link.active {
            font-weight: 600;
        }

        .mini-stat {
            border-radius: .75rem;
        }

        .filter-card .row>[class*="col-"] {
            min-width: 0;
        }

        .btn-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .btn-wrap .btn {
            flex: 1 1 calc(50% - .5rem);
        }

        .view-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .view-wrap .btn {
            flex: 1 1 calc(50% - .5rem);
        }

        .action-stack {
            display: grid;
            gap: .5rem;
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {

            .btn-wrap .btn,
            .view-wrap .btn {
                flex: 0 0 auto;
            }

            .action-stack {
                grid-template-columns: auto;
            }
        }
    </style>
@endsection

@section('content')
    <div class="bg-light">
        <div class="container py-4">

            {{-- FILTER BAR --}}
            <div class="sticky-top-filter bg-light pb-3">
                <form class="card card-body shadow-sm filter-card" method="get"
                    action="{{ route('admin.flowerEstimate') }}">
                    {{-- IMPORTANT: keep current view (day/month) on submit --}}
                    <input type="hidden" name="mode" value="{{ $mode }}" />

                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-8">
                            <div class="row g-3">
                                <div class="col-6 col-md-4">
                                    <label class="form-label">Start date</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ $start }}">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label">End date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label d-block">Preset</label>
                                    <div class="btn-wrap">
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

                        <div class="col-12 col-md-4">
                            <label class="form-label d-block">View</label>
                            <div class="view-wrap">
                                <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode' => 'day'])) }}"
                                    class="btn btn-{{ $mode === 'day' ? 'primary' : 'outline-primary' }}"><i
                                        class="bi bi-calendar-day"></i> Day</a>
                                <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode' => 'month'])) }}"
                                    class="btn btn-{{ $mode === 'month' ? 'primary' : 'outline-primary' }}"><i
                                        class="bi bi-calendar3"></i> Month</a>
                                <div class="action-stack ms-auto">
                                    <button type="submit" class="btn btn-success"><i class="bi bi-funnel"></i>
                                        Apply</button>
                                    <a href="{{ route('admin.flowerEstimate') }}"
                                        class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-2">
                        Tip: “Tomorrow” uses <code>COALESCE(new_date, end_date)</code> and skips subscriptions paused
                        tomorrow.
                    </small>
                </form>
            </div>

            {{-- SELECTED RANGE — GRAND TOTALS (QUANTITY) --}}
            @php
                $rByCat = $rangeTotals['by_category'] ?? [];
                $rByItem = $rangeTotals['by_item'] ?? [];
            @endphp
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Selected Range — Grand Totals (Quantity)</h5>
                        <small class="text-muted">From {{ \Carbon\Carbon::parse($start)->toFormattedDateString() }}
                            to {{ \Carbon\Carbon::parse($end)->toFormattedDateString() }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
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
                    </div>
                </div>
            </div>

            {{-- TOMORROW ESTIMATE (always shown) --}}
            @php
                $tProducts = $tomorrowEstimate['products'] ?? [];
                $tGrand = $tomorrowEstimate['grand_total_amount'] ?? 0;
                $tTotals = $tomorrowEstimate['totals_by_item'] ?? [];
            @endphp

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Tomorrow Estimate —
                            {{ \Carbon\Carbon::parse($tomorrowDate)->toFormattedDateString() }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-warning" id="openAssignModal">
                                <i class="bi bi-truck"></i> Assign Vendor
                            </button>
                            <span class="badge bg-success fs-6">Grand Total:
                                <span class="money">₹{{ number_format($tGrand, 2) }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-white">
                    @if (empty($tProducts))
                        <div class="alert alert-secondary">No active subscriptions tomorrow.</div>
                    @else
                        <div class="row g-3">
                            @foreach ($tProducts as $pid => $row)
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
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                <div>
                                                    <h6 class="mb-1">{{ $product?->name ?? 'Product #' . $pid }}</h6>
                                                    <div class="text-muted">
                                                        <strong>{{ $subsCount }}</strong>
                                                        subscription{{ $subsCount == 1 ? '' : 's' }}
                                                        <span class="ms-2">(Bundle / Sub:
                                                            ₹{{ number_format($bundlePerSub, 2) }})</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge bg-primary">Product Total:
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
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tomorrow Totals by Item --}}
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
                    @endif
                </div>
            </div>

            {{-- DAY / MONTH VIEWS --}}
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
                                            <span class="badge bg-success fs-6">Grand Total:
                                                <span class="money">₹{{ number_format($grand, 2) }}</span></span>
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
                                                                            {{ $product?->name ?? 'Product #' . $pid }}</h5>
                                                                        <div class="text-muted">
                                                                            <strong>{{ $subsCount }}</strong> active
                                                                            subscription{{ $subsCount == 1 ? '' : 's' }}
                                                                            <span class="ms-2">(Bundle / Sub:
                                                                                ₹{{ number_format($bundlePerSub, 2) }})</span>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge bg-primary fs-6">Product Total:
                                                                            <span
                                                                                class="money">₹{{ number_format($productTotal, 2) }}</span></span>
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
                                            <span class="badge bg-success fs-6">Grand Total:
                                                <span class="money">₹{{ number_format($grand, 2) }}</span></span>
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
                                                                            {{ $product?->name ?? 'Product #' . $pid }}</h5>
                                                                        <div class="text-muted">
                                                                            <strong>{{ $subsDays }}</strong>
                                                                            subscription-days</div>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge bg-primary fs-6">Product Total:
                                                                            <span
                                                                                class="money">₹{{ number_format($productTotal, 2) }}</span></span>
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

    {{-- ASSIGN PICKUP MODAL --}}
<div class="modal fade" id="assignPickupModal" tabindex="-1" aria-labelledby="assignPickupLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.saveFlowerPickupAssignRider') }}" novalidate id="assignPickupForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="assignPickupLabel">Assign Vendor for Tomorrow Items</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          {{-- PICKUP INFO --}}
          <div class="nu-card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h6 class="section-title mb-0">Pickup Information</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="vendor_id" class="form-label required">Vendor</label>
                  <select id="vendor_id" name="vendor_id" class="form-control" required>
                    <option value="" selected>Choose</option>
                    @foreach ($vendors as $vendor)
                      <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="pickup_date" class="form-label required">Pickup Date</label>
                  <input type="date" id="pickup_date" name="pickup_date"
                         class="form-control"
                         value="{{ $tomorrowDate }}" min="{{ now()->toDateString() }}" required>
                  <div class="form-text">Defaults to tomorrow.</div>
                </div>
              </div>
            </div>
          </div>

          {{-- FLOWERS LIST --}}
          <div class="nu-card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-12">
              <h6 class="section-title m-0">Flower Items</h6>
              <div class="d-flex align-items-center gap-12">
                <span class="badge bg-light text-dark row-badge">
                  Rows: <span id="rowCount">0</span>
                </span>
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">Add Row</button>
                <button type="button" class="btn btn-outline-secondary" id="clearAllBtn">Clear All</button>
              </div>
            </div>
            <div class="card-body">
              <div id="rowsContainer"></div>

              {{-- Template for new rows --}}
              <template id="rowTemplate">
                <div class="pickup-row" data-row>
                  <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                      <label class="form-label required">Flower</label>
                      <select name="flower_id[]" class="form-control" required>
                        <option value="" selected>Choose flower</option>
                        @foreach ($flowers as $flower)
                          <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label required">Unit</label>
                      <select name="unit_id[]" class="form-control" required>
                        <option value="" selected>Choose unit</option>
                        @foreach ($units as $unit)
                          <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label required">Quantity</label>
                      <input type="number" name="quantity[]" class="form-control" placeholder="e.g. 10"
                             inputmode="decimal" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-1 d-grid">
                      <button type="button" class="btn btn-outline-danger remove-row-btn">Remove</button>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>

          {{-- RIDER ASSIGNMENT --}}
          <div class="nu-card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h6 class="section-title m-0">Assign Rider</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="rider_id" class="form-label required">Rider</label>
                  <select id="rider_id" name="rider_id" class="form-control" required>
                    <option value="" selected>Choose</option>
                    @foreach ($riders as $rider)
                      <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>

        </div>{{-- /modal-body --}}

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit Pickup</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap modal
    const assignModalEl = document.getElementById('assignPickupModal');
    const assignModal   = new bootstrap.Modal(assignModalEl);

    // Buttons / containers
    const openBtn       = document.getElementById('openAssignModal');
    const rowsContainer = document.getElementById('rowsContainer');
    const rowTemplate   = document.getElementById('rowTemplate');
    const rowCountEl    = document.getElementById('rowCount');
    const addRowBtn     = document.getElementById('addRowBtn');
    const clearAllBtn   = document.getElementById('clearAllBtn');

    // Data from backend
    const tomorrowTotals = @json(array_values($tomorrowEstimate['totals_by_item'] ?? []));
    const flowerNameToId = @json($flowerNameToId);
    const unitSymbolToId = @json($unitSymbolToId);

    // Helper: update row count badge
    function updateRowCount() {
        rowCountEl.textContent = rowsContainer.querySelectorAll('[data-row]').length;
    }

    // Add new empty row
    function addEmptyRow() {
        const node = document.importNode(rowTemplate.content, true);
        // attach remove
        node.querySelector('.remove-row-btn').addEventListener('click', function (e) {
            e.target.closest('[data-row]').remove();
            updateRowCount();
        });
        rowsContainer.appendChild(node);
        updateRowCount();
    }

    // Clear all rows
    function clearAll() {
        rowsContainer.innerHTML = '';
        updateRowCount();
    }

    addRowBtn.addEventListener('click', addEmptyRow);
    clearAllBtn.addEventListener('click', clearAll);

    // Prefill from tomorrowTotals
    function prefillFromTomorrow() {
        clearAll();

        // Normalize unit symbols map, plus a few aliases
        const alias = {
            kg: 'kg', g: 'g', l: 'l', lt: 'l', litre: 'l', liters: 'l', ml: 'ml', pcs: 'pcs', piece:'pcs', pieces:'pcs', count:'pcs'
        };

        tomorrowTotals.forEach(item => {
            // item: { item_name, total_qty_disp, total_unit_disp, category }
            const node = document.importNode(rowTemplate.content, true);
            const row  = node.querySelector('[data-row]');
            const flowerSel = row.querySelector('select[name="flower_id[]"]');
            const unitSel   = row.querySelector('select[name="unit_id[]"]');
            const qtyInput  = row.querySelector('input[name="quantity[]"]');

            // Try to select flower by exact name
            const name = (item.item_name || '').trim();
            const flowerId = flowerNameToId[name] ?? '';
            if (flowerId) flowerSel.value = String(flowerId);

            // Unit: map display unit to unit_id (kg/g/L/ml/pcs -> lowercase key)
            let dispUnit = String(item.total_unit_disp || '').toLowerCase();
            if (alias[dispUnit]) dispUnit = alias[dispUnit];

            const unitId = unitSymbolToId[dispUnit] ?? '';
            if (unitId) unitSel.value = String(unitId);

            // Quantity: just place the display quantity (already scaled)
            qtyInput.value = (item.total_qty_disp ?? 0);

            // remove handler
            row.querySelector('.remove-row-btn').addEventListener('click', function (e) {
                e.target.closest('[data-row]').remove();
                updateRowCount();
            });

            rowsContainer.appendChild(node);
        });

        updateRowCount();
    }

    // When opening modal, build rows from tomorrow items
    openBtn?.addEventListener('click', function () {
        prefillFromTomorrow();
        assignModal.show();
    });
});
</script>

@endsection
