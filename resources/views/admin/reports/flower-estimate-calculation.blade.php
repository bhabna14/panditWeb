@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Font: Inter (lighter look) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- SheetJS for client-side Excel export --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            --excel-green: #c4f5f3;
            --excel-green-2: #7acff9;
            --excel-light: #eef9ff;
            --excel-border: #cfd8dc;
            --excel-zebra: #f8fbff;

            --accent-1: #3b82f6;
            --accent-2: #06b6d4;
            --text: #0f172a;
            --muted: #667085;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 8px 28px rgba(2, 6, 23, .08);
            --radius: 14px;
        }

        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            font-weight: 400;
        }

        .container-page { max-width: 1280px; }

        /* Toolbar (date + presets only) */
        .toolbar {
            position: sticky; top: 0; z-index: 20; background: var(--card);
            border: 1px solid var(--ring); border-radius: var(--radius);
            padding: .75rem; display: grid; gap: .75rem;
            grid-template-columns: 1fr auto; align-items: center;
            box-shadow: var(--shadow); margin-bottom: 1rem;
        }
        .date-range { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; color: var(--muted); }
        .date-range input {
            border: 1px solid var(--ring); border-radius: 10px; padding: .55rem .8rem; background: #fff;
            font-weight: 500;
        }
        .date-range input:focus { outline: none; border-color: var(--accent-2); box-shadow: 0 0 0 3px rgba(6, 182, 212, .2); }

        .btn-chip {
            border: 1px solid #d0d5dd; background:#fff; color:#0f172a;
            padding: .55rem .9rem; border-radius: 999px; font-weight: 500; cursor: pointer;
        }
        .btn-chip:hover { background:#f2f4f7; }
        .btn-apply { background: linear-gradient(135deg, #0f172a, #334155); color:#fff; border:none; }

        /* Workbook card */
        .workbook {
            background: var(--card); border: 1px solid var(--ring); border-radius: 16px;
            overflow: hidden; box-shadow: var(--shadow);
        }
        .workbook-head {
            display:flex; justify-content:space-between; align-items:center;
            padding: .9rem 1rem; color:#0b1720;
            background: linear-gradient(90deg, var(--excel-green), var(--excel-green-2));
        }
        .workbook-title { font-weight: 600; font-size: 1.0rem; }
        .workbook-sub { font-size: .9rem; color:#0b3a4a; }

        .workbook-tools { display:flex; gap:.5rem; }
        .export-btn {
            border: none; border-radius: 8px; padding: .5rem .75rem; font-weight: 500; cursor: pointer;
            background:#fff; color:#075985;
        }

        /* Master Excel table */
        .excel-wrap { padding: 1rem; overflow:auto; }
        .excel {
            width: 100%; border-collapse: separate; border-spacing: 0;
            background: #fff; font-size: .93rem; border: 1px solid var(--excel-border);
        }
        .excel thead th {
            position: sticky; top:0; z-index:1; background: var(--excel-light);
            color:#0b3a52; text-transform: uppercase; font-size:.72rem; letter-spacing:.07em;
            border-bottom: 2px solid var(--excel-border); padding:.5rem .6rem; text-align:left; font-weight:600;
        }
        .excel td {
            border-top:1px solid var(--excel-border);
            border-right:1px solid var(--excel-border);
            padding:.5rem .6rem; vertical-align:middle; color:var(--text); font-weight:400;
        }
        .excel tr:nth-child(even) td { background: var(--excel-zebra); }
        .excel tr td:first-child, .excel thead th:first-child { border-left:1px solid var(--excel-border); }

        /* Group subtotal row (per vendor) */
        .group-row td {
            background:#f0f9ff; border-top:2px solid var(--excel-border);
            font-weight:600;
        }
        .group-caption { font-weight:600; color:#0b3e1f; }

        .diff-up { color:#dc2626; font-weight:600; }
        .diff-down { color:#16a34a; font-weight:600; }

        /* Stats */
        .stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; margin:1rem 0; }
        .stat-card { padding:.8rem .95rem; border-radius:12px; background:var(--card); border:1px solid var(--ring); }
        .stat-title { font-size:.84rem; color:var(--muted); }
        .stat-value { font-weight:600; font-size:1.05rem; letter-spacing:.2px; }

        .pagination { margin-top:1rem; }
        .pagination .page-link { background:#fff; border:1px solid var(--ring); color:var(--text); }
        .pagination .active .page-link { background: var(--accent-2); border-color: var(--accent-2); color:#00303a; }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Filters (date + presets) --}}
        <form method="GET" action="{{ route('admin.pickups.manage') }}" id="filterForm" class="toolbar">
            <div class="date-range">
                <span>From</span>
                <input type="date" name="start" value="{{ $start }}" />
                <span>To</span>
                <input type="date" name="end" value="{{ $end }}" />
            </div>

            <div style="display:flex; gap:.4rem; flex-wrap:wrap; justify-content:flex-end">
                <button class="btn-chip" data-preset="today" type="button">Today</button>
                <button class="btn-chip" data-preset="yesterday" type="button">Yesterday</button>
                <button class="btn-chip" data-preset="tomorrow" type="button">Tomorrow</button>
                <button class="btn-chip" data-preset="this_week" type="button">This Week</button>
                <button class="btn-chip" data-preset="this_month" type="button">This Month</button>
                <button class="btn-chip btn-apply" type="submit">Apply</button>
            </div>
            <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">
        </form>

        {{-- Stats --}}
        <div class="stats">
            @php
                $totalPickups = $pickups->total();
                $totalVendors = $pickups->getCollection()->pluck('vendor.vendor_id')->filter()->unique()->count();
                $totalItems   = $pickups->getCollection()->flatMap->flowerPickupItems->count();
                $grandAmount  = number_format(
                    $pickups->getCollection()->flatMap->flowerPickupItems->map(function($it){
                        $aprc = (float)($it->price ?? 0);
                        $aqty = (float)($it->quantity ?? 0);
                        return $it->item_total_price !== null ? (float)$it->item_total_price : ($aprc * $aqty);
                    })->sum()
                , 2);
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

                            <th>Act. Qty</th>
                            <th>Act. Unit</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>

                            <th>Qty Diff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNo = 0;
                            // Group current page's pickups by vendor to insert per-vendor subtotals
                            $byVendor = $pickups->getCollection()->groupBy(fn($p) => optional($p->vendor)->vendor_name ?? 'Unknown Vendor');
                        @endphp

                        @foreach ($byVendor as $vendorName => $vendorPickups)
                            @php
                                $vendorActSum = 0.0;
                                $vendorQtyDiffSum = 0.0;
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
                                        $aqty  = (float)($it->quantity ?? 0);
                                        $aprc  = (float)($it->price ?? 0);

                                        $ltotal = $it->item_total_price !== null ? (float)$it->item_total_price : ($aprc * $aqty);

                                        // Quantity difference only
                                        $qdiff = round($aqty - $eqty, 2);

                                        $vendorActSum     += $ltotal;
                                        $vendorQtyDiffSum += $qdiff;
                                    @endphp
                                    <tr>
                                        <td>{{ $rowNo }}</td>
                                        <td>{{ $vendorName }}</td>
                                        <td>{{ $pkDate }}</td>
                                        <td>{{ $dvDate }}</td>
                                        <td>{{ $riderName }}</td>
                                        <td>{{ optional($it->flower)->name ?? '—' }}</td>

                                        <td>{{ $eqty ? number_format($eqty, 2) : '—' }}</td>
                                        <td>{{ $ename }}</td>

                                        <td>{{ $aqty ? number_format($aqty, 2) : '—' }}</td>
                                        <td>{{ $aname }}</td>
                                        <td>₹ {{ number_format($aprc, 2) }}</td>
                                        <td>₹ {{ number_format($ltotal, 2) }}</td>

                                        <td class="{{ $qdiff > 0 ? 'diff-up' : ($qdiff < 0 ? 'diff-down' : '') }}">
                                            {{ $qdiff > 0 ? '+' : '' }}{{ number_format($qdiff, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            <tr class="group-row">
                                <td colspan="5">
                                    <span class="group-caption">Subtotal — {{ $vendorName }}</span>
                                </td>
                                <td></td>
                                <td colspan="4" style="text-align:right;">Actual (sum)</td>
                                <td>₹ {{ number_format($vendorActSum, 2) }}</td>
                                <td class="{{ $vendorQtyDiffSum>0 ? 'diff-up' : ($vendorQtyDiffSum<0 ? 'diff-down' : '') }}">
                                    {{ $vendorQtyDiffSum>0 ? '+' : '' }}{{ number_format($vendorQtyDiffSum, 2) }}
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
        ['start', 'end'].forEach(n => {
            const el = document.querySelector(`input[name="${n}"]`);
            if (el) el.addEventListener('change', () => document.getElementById('presetInput').value = '');
        });

        // Export whole master table
        const exportBtn = document.getElementById('exportAllBtn');
        if (exportBtn) {
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
