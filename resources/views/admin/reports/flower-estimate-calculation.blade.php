@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Font: Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- SheetJS for client-side Excel export --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            /* Fresh workbook palette */
            --excel-green: #c4f5f3;
            --excel-green-2: #7acff9;
            --excel-light: #eef9ff;
            --excel-border: #cfd8dc;
            --excel-zebra: #f8fbff;

            --accent-1: #6366f1;
            --accent-2: #06b6d4;
            --accent-3: #22c55e;
            --accent-4: #f59e0b;
            --accent-5: #64748b;

            --text: #0f172a;
            --muted: #64748b;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 8px 28px rgba(2, 6, 23, .08);
            --radius: 14px;
        }

        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            font-weight: 400;
        }

        .container-page { max-width: 1280px; }

        /* Toolbar */
        .toolbar {
            position: sticky; top: 0; z-index: 20; background: var(--card);
            border: 1px solid var(--ring); border-radius: var(--radius);
            padding: .75rem; display: grid; gap: .75rem;
            grid-template-columns: 1fr auto; align-items: center;
            box-shadow: var(--shadow); margin-bottom: 1rem;
        }
        .date-range { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; color: var(--muted); }
        .select-in, .date-range input {
            border: 1px solid var(--ring); border-radius: 10px; padding: .55rem .8rem; background: #fff;
            font-weight: 500;
        }
        .select-in:focus, .date-range input:focus {
            outline: none; border-color: var(--accent-2);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, .2);
        }

        .btn-chip {
            border: none; padding: .6rem .95rem; border-radius: 999px; color: #fff;
            font-weight: 700; cursor: pointer; box-shadow: var(--shadow);
        }
        .chip1 { background: linear-gradient(135deg, var(--accent-1), #8b5cf6); }
        .chip2 { background: linear-gradient(135deg, var(--accent-2), #22d3ee); }
        .chip3 { background: linear-gradient(135deg, var(--accent-3), #86efac); color: #065f46; }
        .chip4 { background: linear-gradient(135deg, var(--accent-4), #fbbf24); color: #7c2d12; }
        .chip5 { background: linear-gradient(135deg, var(--accent-5), #cbd5e1); color: #0f172a; }
        .btn-apply { background: linear-gradient(135deg, #111827, #1f2937); color: #fff; }

        /* Workbook card */
        .workbook {
            background: var(--card); border: 1px solid var(--ring); border-radius: 16px;
            overflow: hidden; box-shadow: var(--shadow);
        }
        .workbook-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 1rem; color: #0b1720;
            background: linear-gradient(90deg, var(--excel-green), var(--excel-green-2));
        }
        .workbook-title { font-weight: 800; font-size: 1.05rem; }
        .workbook-sub   { font-size: .9rem; opacity: .95; }
        .workbook-tools { display: flex; gap: .5rem; flex-wrap: wrap; }

        .export-btn {
            border: none; border-radius: 8px; padding: .55rem .8rem; font-weight: 700; cursor: pointer;
            background: #fff; color: #0772a6;
        }

        /* Master Excel table */
        .excel-wrap { padding: 1rem; overflow: auto; }
        .excel {
            width: 100%; border-collapse: separate; border-spacing: 0;
            background: white; font-size: .94rem; border: 1px solid var(--excel-border);
        }
        .excel thead th {
            position: sticky; top: 0; z-index: 1; background: var(--excel-light);
            color: #003a52; text-transform: uppercase; font-size: .72rem; letter-spacing: .08em;
            border-bottom: 2px solid var(--excel-border); padding: .55rem .6rem; text-align: left; font-weight: 700;
        }
        .excel td {
            border-top: 1px solid var(--excel-border);
            border-right: 1px solid var(--excel-border);
            padding: .55rem .6rem; vertical-align: middle; color: var(--text);
        }
        .excel tr:nth-child(even) td { background: var(--excel-zebra); }
        .excel tr td:first-child, .excel thead th:first-child { border-left: 1px solid var(--excel-border); }

        /* Group subtotal row */
        .group-row td {
            background: #f0f9ff; border-top: 2px solid var(--excel-border);
            font-weight: 700;
        }
        .group-caption {
            font-weight: 800; font-size: .95rem; color: #0b3e1f;
        }

        .diff-up { color: #dc2626; font-weight: 800; }
        .diff-down { color: #16a34a; font-weight: 800; }

        /* Stats (top) */
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .75rem; margin: 1rem 0; }
        .stat-card { padding: .9rem 1rem; border-radius: var(--radius); background: var(--card); border: 1px solid var(--ring); box-shadow: var(--shadow); }
        .stat-title { font-size: .85rem; color: var(--muted); }
        .stat-value { font-weight: 800; font-size: 1.1rem; letter-spacing: .3px; }

        .pagination { margin-top: 1rem; }
        .pagination .page-link { background: #fff; border: 1px solid var(--ring); color: var(--text); }
        .pagination .active .page-link { background: var(--accent-2); border-color: var(--accent-2); color: #00303a; }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.pickups.manage') }}" id="filterForm" class="toolbar">
            <div class="date-range">
                <strong>From</strong>
                <input type="date" name="start" value="{{ $start }}" />
                <strong>To</strong>
                <input type="date" name="end" value="{{ $end }}" />

                <select class="select-in" name="vendor_id" aria-label="Vendor">
                    <option value="">All Vendors</option>
                    @foreach ($vendors as $v)
                        <option value="{{ $v->vendor_id }}" {{ $vendorId == $v->vendor_id ? 'selected' : '' }}>
                            {{ $v->vendor_name }}
                        </option>
                    @endforeach
                </select>

                <select class="select-in" name="rider_id" aria-label="Rider">
                    <option value="">All Riders</option>
                    @foreach ($riders as $r)
                        <option value="{{ $r->rider_id }}" {{ $riderId == $r->rider_id ? 'selected' : '' }}>
                            {{ $r->rider_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; gap:.4rem; flex-wrap:wrap; justify-content:flex-end">
                <button class="btn-chip chip1" data-preset="today" type="button">Today</button>
                <button class="btn-chip chip5" data-preset="yesterday" type="button">Yesterday</button>
                <button class="btn-chip chip2" data-preset="tomorrow" type="button">Tomorrow</button>
                <button class="btn-chip chip3" data-preset="this_week" type="button">This Week</button>
                <button class="btn-chip chip4" data-preset="this_month" type="button">This Month</button>
                <button class="btn-chip btn-apply" type="submit">Apply</button>
            </div>
            <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">
        </form>

        {{-- Stats --}}
        <div class="stats">
            @php
                $totalPickups = $pickups->total();
                $totalVendors = $pickups->getCollection()->pluck('vendor.vendor_id')->filter()->unique()->count();
                $totalItems = $pickups->getCollection()->flatMap->flowerPickupItems->count();
                $grandAmount = number_format($pickups->getCollection()->flatMap->flowerPickupItems->map(function($it){
                    $aprc = (float)($it->price ?? 0);
                    $aqty = (float)($it->quantity ?? 0);
                    return $it->item_total_price !== null ? (float)$it->item_total_price : ($aprc * $aqty);
                })->sum(), 2);
            @endphp

            <div class="stat-card"><div class="stat-title">Pickups</div><div class="stat-value">{{ $totalPickups }}</div></div>
            <div class="stat-card"><div class="stat-title">Vendors (page)</div><div class="stat-value">{{ $totalVendors }}</div></div>
            <div class="stat-card"><div class="stat-title">Items (page)</div><div class="stat-value">{{ $totalItems }}</div></div>
            <div class="stat-card"><div class="stat-title">Total Amount (page)</div><div class="stat-value">₹ {{ $grandAmount }}</div></div>
        </div>

        {{-- Workbook --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Pickup Items (All Vendors)</div>
                    <div class="workbook-sub">
                        Range: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                        @if($vendorId) • Vendor: {{ optional($vendors->firstWhere('vendor_id', $vendorId))->vendor_name ?? $vendorId }} @endif
                        @if($riderId)  • Rider: {{ optional($riders->firstWhere('rider_id', $riderId))->rider_name ?? $riderId }} @endif
                    </div>
                </div>
                <div class="workbook-tools">
                    <button class="export-btn" id="exportAllBtn" type="button">Export (XLSX)</button>
                </div>
            </div>

            <div class="excel-wrap">
                <table class="excel" id="masterExcelTable">
                    <thead>
                        <tr>
                            <th style="width:56px;">#</th>
                            <th>Vendor</th>
                            <th>Pickup Date</th>
                            <th>Delivery Date</th>
                            <th>Rider</th>
                            <th>Item</th>

                            <th>Est. Qty</th>
                            <th>Est. Unit</th>
                            <th>Est. Price</th>
                            <th>Est. Amount</th>

                            <th>Act. Qty</th>
                            <th>Act. Unit</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>

                            <th>Qty Diff</th>
                            <th>Amt Diff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNo = 0;
                            // Group current page's pickups by vendor to produce per-vendor subtotals
                            $byVendor = $pickups->getCollection()->groupBy(fn($p) => optional($p->vendor)->vendor_name ?? 'Unknown Vendor');
                        @endphp

                        @foreach ($byVendor as $vendorName => $vendorPickups)
                            @php
                                $vendorEstSum = 0.0;
                                $vendorActSum = 0.0;
                            @endphp

                            @foreach ($vendorPickups as $pickup)
                                @php
                                    $pkDate = $pickup->pickup_date ? \Carbon\Carbon::parse($pickup->pickup_date)->format('d M Y') : '—';
                                    $dvDate = $pickup->delivery_date ? \Carbon\Carbon::parse($pickup->delivery_date)->format('d M Y') : '—';
                                    $riderName = optional($pickup->rider)->rider_name ?? '—';
                                @endphp

                                @foreach ($pickup->flowerPickupItems as $it)
                                    @php
                                        $rowNo++;

                                        $ename = optional($it->estUnit)->unit_name ?? ($unitMap[$it->est_unit_id] ?? '—');
                                        $aname = optional($it->unit)->unit_name    ?? ($unitMap[$it->unit_id]    ?? '—');

                                        $eqty  = (float)($it->est_quantity ?? 0);
                                        $eprc  = $it->est_price !== null ? (float)$it->est_price : null; // optional
                                        $aqty  = (float)($it->quantity ?? 0);
                                        $aprc  = (float)($it->price ?? 0);

                                        $ltotal= $it->item_total_price !== null ? (float)$it->item_total_price : ($aprc * $aqty);
                                        $eamt  = $eprc !== null ? $eqty * $eprc : null;

                                        if (!is_null($eamt)) $vendorEstSum += $eamt;
                                        $vendorActSum += $ltotal;

                                        $qdiff = round($aqty - $eqty, 2);
                                        $adiff = $eamt !== null ? round($ltotal - $eamt, 2) : null;
                                    @endphp
                                    <tr>
                                        <td>{{ $rowNo }}</td>
                                        <td><strong>{{ $vendorName }}</strong></td>
                                        <td>{{ $pkDate }}</td>
                                        <td>{{ $dvDate }}</td>
                                        <td>{{ $riderName }}</td>
                                        <td><strong>{{ optional($it->flower)->name ?? '—' }}</strong></td>

                                        <td>{{ $eqty ? number_format($eqty, 2) : '—' }}</td>
                                        <td>{{ $ename }}</td>
                                        <td>{{ !is_null($eprc) ? '₹ ' . number_format($eprc, 2) : '—' }}</td>
                                        <td>{{ !is_null($eamt) ? '₹ ' . number_format($eamt, 2) : '—' }}</td>

                                        <td>{{ $aqty ? number_format($aqty, 2) : '—' }}</td>
                                        <td>{{ $aname }}</td>
                                        <td>₹ {{ number_format($aprc, 2) }}</td>
                                        <td><strong>₹ {{ number_format($ltotal, 2) }}</strong></td>

                                        <td class="{{ $qdiff > 0 ? 'diff-up' : ($qdiff < 0 ? 'diff-down' : '') }}">
                                            {{ $qdiff > 0 ? '+' : '' }}{{ $qdiff }} {{ $aname }}
                                        </td>
                                        <td class="{{ !is_null($adiff) && $adiff > 0 ? 'diff-up' : (!is_null($adiff) && $adiff < 0 ? 'diff-down' : '') }}">
                                            {{ is_null($adiff) ? '—' : ($adiff > 0 ? '+' : '') . '₹ ' . number_format($adiff, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @php
                                $vendorDelta = $vendorEstSum > 0 ? ($vendorActSum - $vendorEstSum) : null;
                            @endphp
                            <tr class="group-row">
                                <td colspan="5">
                                    <span class="group-caption">Subtotal — {{ $vendorName }}</span>
                                </td>
                                <td></td>
                                <td colspan="3" style="text-align:right;">Estimated (sum)</td>
                                <td><strong>{{ $vendorEstSum > 0 ? '₹ ' . number_format($vendorEstSum, 2) : '—' }}</strong></td>
                                <td colspan="3" style="text-align:right;">Actual (sum)</td>
                                <td><strong>₹ {{ number_format($vendorActSum, 2) }}</strong></td>
                                <td style="text-align:right;">Diff (Amt)</td>
                                <td class="{{ (!is_null($vendorDelta) && $vendorDelta>0) ? 'diff-up' : ((!is_null($vendorDelta) && $vendorDelta<0) ? 'diff-down' : '') }}">
                                    <strong>{{ is_null($vendorDelta) ? '—' : (($vendorDelta>0?'+':'') . '₹ ' . number_format($vendorDelta, 2)) }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $pickups->withQueryString()->links() }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Presets
        document.querySelectorAll('[data-preset]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('presetInput').value = btn.getAttribute('data-preset');
                document.getElementById('filterForm').submit();
            });
        });
        // Clear preset when manual dates change
        ['start','end'].forEach(n => {
            const el = document.querySelector(`input[name="${n}"]`);
            if (el) el.addEventListener('change', () => document.getElementById('presetInput').value = '');
        });

        // Export whole master table
        const exportBtn = document.getElementById('exportAllBtn');
        if (exportBtn){
            exportBtn.addEventListener('click', () => {
                const table = document.getElementById('masterExcelTable');
                if (!table) return;
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table, { raw: true });
                XLSX.utils.book_append_sheet(wb, ws, 'Pickups');
                XLSX.writeFile(wb, 'Pickups_All.xlsx');
            });
        }
    </script>
@endsection
