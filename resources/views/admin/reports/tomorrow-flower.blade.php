{{-- resources/views/admin/reports/tomorrow-flower.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Bootstrap / Icons (if not already in layout) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
            --success: #16a34a;

            --sh-sm: 0 4px 12px rgba(15, 23, 42, 0.05);
            --sh-md: 0 12px 32px rgba(15, 23, 42, 0.10);
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

        /* Summary band */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border-radius: 20px;
            border: 1px solid #c7d2fe;
            padding: 1rem 1.25rem;
            box-shadow: var(--sh-md);
            margin-bottom: 1.3rem;
        }

        .band-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .band-title {
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }

        .band-title-main {
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .band-title-main span.label-main {
            font-weight: 600;
            font-size: 1rem;
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

        .band-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
            justify-content: flex-end;
        }

        .btn-assign {
            border-radius: 999px;
            padding: .42rem .95rem;
            font-size: .82rem;
            font-weight: 600;
            border: none;
            background: linear-gradient(120deg, #f97316, #facc15);
            color: #1f2933;
            box-shadow: 0 10px 24px rgba(249, 115, 22, .35);
        }

        .btn-assign:hover {
            filter: brightness(.96);
        }

        .band-total-pill {
            border-radius: 999px;
            padding: .35rem .85rem;
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: .8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .band-total-pill i {
            font-size: .95rem;
        }

        /* Mini stats */
        .mini-stat {
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--sh-sm);
            padding: .9rem 1rem;
            height: 100%;
        }

        .mini-stat-label {
            font-size: .78rem;
            color: var(--muted);
            margin-bottom: .25rem;
        }

        .mini-stat-value {
            font-size: 1.35rem;
            font-weight: 800;
        }

        /* Cards */
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
            padding: .18rem .6rem;
            border-radius: 999px;
            border: 1px dashed #d4d4d8;
            background: #f8fafc;
            font-size: .75rem;
            font-weight: 600;
            color: #4b5563;
        }

        .chip-inline .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background-color: #22c55e;
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

        .section-title {
            font-size: .92rem;
            font-weight: 600;
            color: #111827;
        }
    </style>
@endsection

@section('content')
    @php
        $tProducts       = $tomorrowEstimate['products'] ?? [];
        $tGrand          = $tomorrowEstimate['grand_total_amount'] ?? 0;
        $tTotalsDetailed = $tomorrowEstimate['totals_by_item_detailed'] ?? [];
        $garlandTotals   = $garlandTotals ?? [];

        // Build Tomorrow summary (from product items)
        $catBase = ['weight' => 0.0, 'volume' => 0.0, 'count' => 0.0]; // g, ml, pcs
        $distinctItems = [];
        foreach ($tProducts as $row) {
            foreach ($row['items'] ?? [] as $it) {
                $cat = $it['category'] ?? 'count';
                $catBase[$cat] += (float) ($it['total_qty_base'] ?? 0);
                $distinctItems[strtolower(trim($it['item_name']))] = true;
            }
        }
        $tomorrowDistinctItemCount = count($distinctItems);

        // Formatter for category totals
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

        $tomorrowLabel = \Carbon\Carbon::parse($tomorrowDate)->toFormattedDateString();
    @endphp

    <div class="container container-page py-4">

        {{-- Page header --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="page-header-title mb-1">
                    Tomorrow’s Flower Requirement
                </h4>
                <div class="page-header-sub">
                    Combined estimate from Subscriptions & Customize orders for {{ $tomorrowLabel }}.
                </div>
            </div>
            <ol class="breadcrumb page-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tomorrow Estimate</li>
            </ol>
        </div>

        {{-- Summary band --}}
        <div class="band">
            <div class="band-header">
                <div class="band-title">
                    <div class="band-title-main">
                        <span class="label-main">Tomorrow Estimate</span>
                        <span class="band-pill">Forecast</span>
                    </div>
                    <div class="band-sub">
                        For <strong>{{ $tomorrowLabel }}</strong> · Based on active subscriptions and tomorrow’s customize
                        orders.
                    </div>
                </div>
                <div class="band-actions">
                    <a href="{{ route('admin.assignPickupForm', ['date' => $tomorrowDate]) }}"
                       class="btn btn-assign">
                        <i class="bi bi-truck"></i> Assign Vendor
                    </a>
                    <div class="band-total-pill">
                        <i class="bi bi-cash-coin"></i>
                        <span>Grand Total</span>
                        <span class="mono">₹{{ number_format($tGrand, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top KPIs --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-label">Distinct Items</div>
                    <div class="mini-stat-value mono">{{ number_format($tomorrowDistinctItemCount) }}</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-label">Total Weight</div>
                    <div class="mini-stat-value mono">
                        {{ $fmtNum($wQty) }} {{ $wUnit }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-label">Total Volume</div>
                    <div class="mini-stat-value mono">
                        {{ $fmtNum($vQty) }} {{ $vUnit }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-label">Total Count</div>
                    <div class="mini-stat-value mono">
                        {{ $fmtNum($cQty) }} {{ $cUnit }}
                    </div>
                </div>
            </div>
        </div>

        @if (empty($tProducts))
            <div class="card card-soft">
                <div class="card-body">
                    <div class="alert alert-secondary mb-0">
                        No active subscriptions or customize orders found for tomorrow.
                    </div>
                </div>
            </div>
        @else
            {{-- Item-wise split (Subscriptions vs Customize) --}}
            <div class="card card-soft mb-3">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="section-title mb-0">Tomorrow — Totals by Item</div>
                        <div class="text-muted small">
                            Break-up between Subscription bundles and Customize orders.
                        </div>
                    </div>
                    <span class="chip-inline">
                        <span class="dot"></span>
                        <span>Units auto-scale (kg/g, L/ml, pcs)</span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
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
                                    $unit  = $it['unit_disp'] ?? '';
                                    $subs  = (float) ($it['subs_qty_disp'] ?? 0);
                                    $req   = (float) ($it['req_qty_disp'] ?? 0);
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
                                    <td colspan="4" class="text-muted text-center">
                                        No items for tomorrow.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Customize Garlands Totals --}}
            <div class="card card-soft mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title mb-0">Tomorrow — Customize Garlands</div>
                        <div class="text-muted small">
                            All garlands from tomorrow’s customize orders.
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (empty($garlandTotals))
                        <div class="alert alert-secondary mb-0">
                            No garland items in customize orders for tomorrow.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Garland</th>
                                    <th>Size</th>
                                    <th class="text-end">Total Qty (Garlands)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($garlandTotals as $g)
                                    <tr>
                                        <td>{{ $g['garland_name'] }}</td>
                                        <td>{{ $g['garland_size'] ?: '—' }}</td>
                                        <td class="text-end">
                                            {{ $fmtNum($g['total_qty']) }} Garlands
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Totals by Category --}}
            @php
                $catRows = [
                    ['label' => 'Weight', 'qty' => $wQty, 'unit' => $wUnit],
                    ['label' => 'Volume', 'qty' => $vQty, 'unit' => $vUnit],
                    ['label' => 'Count',  'qty' => $cQty, 'unit' => $cUnit],
                ];
            @endphp
            <div class="card card-soft mb-4">
                <div class="card-header">
                    <div class="section-title mb-0">Tomorrow — Totals by Category</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-1">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total Qty</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($catRows as $r)
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td class="text-end mono">
                                        {{ $fmtNum($r['qty']) }} {{ $r['unit'] }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">
                        Units automatically switch between kg/g, L/ml, and pcs for readability.
                    </small>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    {{-- Bootstrap JS (if not already in layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
