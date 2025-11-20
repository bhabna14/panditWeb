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

        /* Quick presets as pill chips */
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

        .preset-chips .btn.active {
            font-weight: 700;
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

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Tomorrow Estimate —
                            {{ \Carbon\Carbon::parse($tomorrowDate)->toFormattedDateString() }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('admin.assignPickupForm', ['date' => $tomorrowDate]) }}"
                                class="btn btn-warning">
                                <i class="bi bi-truck"></i> Assign Vendor
                            </a>
                            <span class="badge bg-success fs-6">
                                Grand Total: <span class="money">₹{{ number_format($tGrand, 2) }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-white">
                    @if (empty($tProducts))
                        <div class="alert alert-secondary">No active subscriptions tomorrow.</div>
                    @else
                        {{-- Summary strip --}}
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
                                    <div class="fs-4 fw-bold">{{ rtrim(rtrim(number_format($wQty, 3), '0'), '.') }}
                                        {{ $wUnit }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="mini-stat p-3 bg-light border">
                                    <div class="text-muted">Total Volume</div>
                                    <div class="fs-4 fw-bold">{{ rtrim(rtrim(number_format($vQty, 3), '0'), '.') }}
                                        {{ $vUnit }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="mini-stat p-3 bg-light border">
                                    <div class="text-muted">Total Count</div>
                                    <div class="fs-4 fw-bold">{{ rtrim(rtrim(number_format($cQty, 3), '0'), '.') }}
                                        {{ $cUnit }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header bg-white"><strong>Tomorrow — Totals by Item (All Products)</strong>
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

                        {{-- Tomorrow Totals by Category --}}
                        @php
                            $catRows = [
                                ['label' => 'Weight', 'qty' => $wQty, 'unit' => $wUnit],
                                ['label' => 'Volume', 'qty' => $vQty, 'unit' => $vUnit],
                                ['label' => 'Count', 'qty' => $cQty, 'unit' => $cUnit],
                            ];
                        @endphp
                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header bg-white"><strong>Tomorrow — Totals by Category</strong></div>
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
                                                        {{ rtrim(rtrim(number_format($r['qty'], 3), '0'), '.') }}
                                                        {{ $r['unit'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <small class="text-muted">Units auto-scale (kg/g, L/ml, pcs).</small>
                                </div>
                            </div>
                        </div>

                        {{-- Products (native disclosure) --}}
                        {{-- <div class="row g-3">
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
                                                    Product Total: <span
                                                        class="money">₹{{ number_format($productTotal, 2) }}</span>
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
                        </div> --}}
                    @endif
                </div>
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
