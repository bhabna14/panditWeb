{{-- resources/views/admin/reports/tomorrow-flower.blade.php --}}
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

        .tag-pill {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .5rem;
            border-radius: 999px;
            font-size: .75rem;
            border: 1px solid #e5e7eb;
            background-color: #f8fafc;
        }

        .tag-pill .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background-color: #22c55e;
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
                $tTotalsDetailed = $tomorrowEstimate['totals_by_item_detailed'] ?? [];

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

                $fmtNum = function ($v) {
                    return rtrim(rtrim(number_format($v, 3), '0'), '.');
                };
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
                                    <div class="fs-4 fw-bold">
                                        {{ $fmtNum($wQty) }} {{ $wUnit }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="mini-stat p-3 bg-light border">
                                    <div class="text-muted">Total Volume</div>
                                    <div class="fs-4 fw-bold">
                                        {{ $fmtNum($vQty) }} {{ $vUnit }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="mini-stat p-3 bg-light border">
                                    <div class="text-muted">Total Count</div>
                                    <div class="fs-4 fw-bold">
                                        {{ $fmtNum($cQty) }} {{ $cUnit }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- NEW: Item-wise split (Subscriptions vs Customize) --}}
                        <div class="card border-0 shadow-sm mt-3">
                            <div
                                class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong>Tomorrow — Totals by Item</strong>
                                    <div class="text-muted small">Break-up by Subscriptions vs Customize Orders</div>
                                </div>
                                <span class="tag-pill">
                                    <span class="dot"></span>
                                    <span>Values auto-scale (kg/g, L/ml, pcs)</span>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-end">
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-box-seam"></i>
                                                        <span>Subscriptions</span>
                                                    </span>
                                                </th>
                                                <th class="text-end">
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-sliders2"></i>
                                                        <span>Customize Orders</span>
                                                    </span>
                                                </th>
                                                <th class="text-end">
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-sum"></i>
                                                        <span>Total</span>
                                                    </span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($tTotalsDetailed as $it)
                                                @php
                                                    $unit = $it['unit_disp'] ?? '';
                                                    $subs = (float) ($it['subs_qty_disp'] ?? 0);
                                                    $req = (float) ($it['req_qty_disp'] ?? 0);
                                                    $total = (float) ($it['total_qty_disp'] ?? 0);
                                                @endphp
                                                <tr>
                                                    <td>{{ $it['item_name'] }}</td>
                                                    <td class="text-end text-nowrap">
                                                        @if ($subs > 0)
                                                            {{ $fmtNum($subs) }} {{ $unit }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        @if ($req > 0)
                                                            {{ $fmtNum($req) }} {{ $unit }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap fw-semibold">
                                                        @if ($total > 0)
                                                            {{ $fmtNum($total) }} {{ $unit }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-muted text-center">No items.</td>
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
                                                        {{ $fmtNum($r['qty']) }} {{ $r['unit'] }}
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

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
