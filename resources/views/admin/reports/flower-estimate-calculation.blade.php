@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- SheetJS for client-side Excel export --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            /* Excel-ish palette */
            --excel-green: #c4f5f3;
            --excel-green-2: #7acff9;
            --excel-light: #e8f5e9;
            --excel-border: #cfd8dc;
            --excel-zebra: #f8fbff;

            --accent-1: #6366f1;
            --accent-2: #06b6d4;
            --accent-3: #22c55e;
            --accent-4: #f59e0b;
            --accent-5: #64748b;
            --text: #0f172a;
            --muted: #64748b;
            --bg: #f6f7fb;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 8px 28px rgba(2, 6, 23, .08);
            --radius: 14px;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
        }

        .container-page {
            max-width: 1260px;
        }

        /* Toolbar */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            padding: .75rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: 1fr auto;
            align-items: center;
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
        }

        .date-range {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
        }

        .select-in,
        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 10px;
            padding: .5rem .75rem;
            background: #fff;
        }

        .select-in:focus,
        .date-range input:focus {
            outline: none;
            border-color: var(--accent-2);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, .2);
        }

        .btn-chip {
            border: none;
            padding: .55rem .9rem;
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .chip1 {
            background: linear-gradient(135deg, var(--accent-1), #8b5cf6);
        }

        .chip2 {
            background: linear-gradient(135deg, var(--accent-2), #22d3ee);
        }

        .chip3 {
            background: linear-gradient(135deg, var(--accent-3), #86efac);
            color: #065f46;
        }

        .chip4 {
            background: linear-gradient(135deg, var(--accent-4), #fbbf24);
            color: #7c2d12;
        }

        .chip5 {
            background: linear-gradient(135deg, var(--accent-5), #cbd5e1);
            color: #0f172a;
        }

        .btn-apply {
            background: linear-gradient(135deg, #111827, #1f2937);
            color: #fff;
        }

        /* Excel card */
        .excel-card {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
        }

        .excel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1rem;
            color: #060606;
            background: linear-gradient(90deg, var(--excel-green), var(--excel-green-2));
        }

        .excel-head .title {
            font-weight: 800;
            font-size: 1.05rem;
        }

        .excel-head .sub {
            font-size: .9rem;
            opacity: .95;
        }

        .excel-tools {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .export-btn,
        .mini-btn {
            border: none;
            border-radius: 8px;
            padding: .5rem .7rem;
            font-weight: 700;
            cursor: pointer;
        }

        .export-btn {
            background: #fff;
            color: var(--excel-green-2);
        }

        .mini-btn {
            background: #0ea5e9;
            color: #fff;
        }

        /* Excel table */
        .excel-wrap {
            padding: 1rem;
            overflow: auto;
        }

        .excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            font-size: .95rem;
            border: 1px solid var(--excel-border);
        }

        .excel thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: var(--excel-light);
            color: #0b3e1f;
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            border-bottom: 2px solid var(--excel-border);
            padding: .55rem .6rem;
            text-align: left;
        }

        .excel td {
            border-top: 1px solid var(--excel-border);
            border-right: 1px solid var(--excel-border);
            padding: .55rem .6rem;
            vertical-align: middle;
            color: #0f172a;
        }

        .excel tr:nth-child(even) td {
            background: var(--excel-zebra);
        }

        .excel tr td:first-child,
        .excel thead th:first-child {
            border-left: 1px solid var(--excel-border);
        }

        .excel tfoot td {
            background: #eef6f0;
            font-weight: 800;
            border-top: 2px solid var(--excel-border);
        }

        .pill {
            display: inline-block;
            padding: .18rem .55rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .78rem;
        }

        .pill-est {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #3730a3;
        }

        .pill-act {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #065f46;
        }

        .diff-up {
            color: #dc2626;
            font-weight: 800;
        }

        .diff-down {
            color: #16a34a;
            font-weight: 800;
        }

        /* Stats (top) */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin: 1rem 0;
        }

        .stat-card {
            padding: .9rem 1rem;
            border-radius: var(--radius);
            background: var(--card);
            border: 1px solid var(--ring);
            box-shadow: var(--shadow);
        }

        .stat-title {
            font-size: .85rem;
            color: var(--muted);
        }

        .stat-value {
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: .3px;
        }

        /* Pagination */
        .pagination {
            margin-top: 1rem;
        }

        .pagination .page-link {
            background: #fff;
            border: 1px solid var(--ring);
            color: var(--text);
        }

        .pagination .active .page-link {
            background: var(--accent-2);
            border-color: var(--accent-2);
            color: #00303a;
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Filters toolbar --}}
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
                $grandAmount = number_format($pickups->getCollection()->sum('total_price'), 2);
            @endphp

            <div class="stat-card">
                <div class="stat-title">Pickups</div>
                <div class="stat-value">{{ $totalPickups }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Vendors (page)</div>
                <div class="stat-value">{{ $totalVendors }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Items (page)</div>
                <div class="stat-value">{{ $totalItems }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Amount (page)</div>
                <div class="stat-value">₹ {{ $grandAmount }}</div>
            </div>
        </div>

        {{-- Global export (all visible cards -> multi-sheet workbook) --}}
        @if ($pickups->count())
            <div style="display:flex; justify-content:flex-end; margin-bottom:.5rem;">
                <button class="export-btn" id="exportAllBtn">Export All (XLSX)</button>
            </div>
        @endif

        {{-- Excel-like cards --}}
        @forelse($pickups as $i => $pickup)
            @php
                $vendorName = optional($pickup->vendor)->vendor_name ?? 'Unknown Vendor';
                $riderName = optional($pickup->rider)->rider_name ?? '—';
                $pkDate = $pickup->pickup_date ? \Carbon\Carbon::parse($pickup->pickup_date)->format('d M Y') : '—';
                $dvDate = $pickup->delivery_date ? \Carbon\Carbon::parse($pickup->delivery_date)->format('d M Y') : '—';

                $sumEstAmt = 0.0;
                $sumActAmt = (float) ($pickup->total_price ?? 0);
                $tableId = 'excelTable_' . $pickup->pick_up_id;
                $sheetName =
                    ($sheetTitlePrefix ?? 'Pickups') .
                    '_' .
                    ($pickup->pickup_date ? \Carbon\Carbon::parse($pickup->pickup_date)->format('Ymd') : 'NA') .
                    '_' .
                    preg_replace('/[^A-Za-z0-9]/', '', $vendorName);
            @endphp

            <div class="excel-card" data-sheet-name="{{ $sheetName }}" data-table-id="{{ $tableId }}">
                <div class="excel-head">
                    <div>
                        <div class="title">{{ $vendorName }}</div>
                        <div class="sub">
                            Pickup: {{ $pkDate }} • Delivery: {{ $dvDate }} • Rider: {{ $riderName }} •
                            Ref: #{{ $pickup->pick_up_id }}
                        </div>
                    </div>
                    <div class="excel-tools">
                        <button type="button" class="mini-btn" onclick="scrollIntoViewSmooth('#{{ $tableId }}')">Jump
                            to Table</button>
                        <button type="button" class="export-btn"
                            onclick="exportOne('{{ $tableId }}','{{ $sheetName }}')">Export this (XLSX)</button>
                    </div>
                </div>

                <div class="excel-wrap">
                    <table class="excel" id="{{ $tableId }}">
                        <thead>
                            <tr>
                                <th style="width:48px;">#</th>
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
                            @foreach ($pickup->flowerPickupItems as $idx => $it)
                                @php
                                    $ename = optional($it->estUnit)->unit_name ?? ($unitMap[$it->est_unit_id] ?? '—');
                                    $aname = optional($it->unit)->unit_name ?? ($unitMap[$it->unit_id] ?? '—');

                                    $eqty = (float) ($it->est_quantity ?? 0);
                                    $eprc = $it->est_price !== null ? (float) $it->est_price : null; // optional
                                    $aqty = (float) ($it->quantity ?? 0);
                                    $aprc = (float) ($it->price ?? 0);
                                    $ltotal =
                                        $it->item_total_price !== null ? (float) $it->item_total_price : $aprc * $aqty;

                                    $eamt = $eprc !== null ? $eqty * $eprc : null;

                                    if ($eamt !== null) {
                                        $sumEstAmt += $eamt;
                                    }

                                    // Differences
                                    $qdiff = round($aqty - $eqty, 2);
                                    $adiff = $eamt !== null ? round($ltotal - $eamt, 2) : null;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
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
                                    <td
                                        class="{{ !is_null($adiff) && $adiff > 0 ? 'diff-up' : (!is_null($adiff) && $adiff < 0 ? 'diff-down' : '') }}">
                                        {{ is_null($adiff) ? '—' : ($adiff > 0 ? '+' : '') . '₹ ' . number_format($adiff, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                // If actual sum not on pickup row, recompute from items:
                                $sumActAmt = $pickup->flowerPickupItems->sum(function ($it) {
                                    $aprc = (float) ($it->price ?? 0);
                                    $aqty = (float) ($it->quantity ?? 0);
                                    $ltotal =
                                        $it->item_total_price !== null ? (float) $it->item_total_price : $aprc * $aqty;
                                    return $ltotal;
                                });
                                $delta = $sumEstAmt > 0 ? $sumActAmt - $sumEstAmt : null;
                            @endphp
                            <tr>
                                <td colspan="5" style="text-align:right;">Estimated (sum)</td>
                                <td><strong>{{ $sumEstAmt > 0 ? '₹ ' . number_format($sumEstAmt, 2) : '—' }}</strong></td>
                                <td colspan="3" style="text-align:right;">Actual (sum)</td>
                                <td><strong>₹ {{ number_format($sumActAmt, 2) }}</strong></td>
                                <td style="text-align:right;">Diff (Amt)</td>
                                <td
                                    class="{{ !is_null($delta) && $delta > 0 ? 'diff-up' : (!is_null($delta) && $delta < 0 ? 'diff-down' : '') }}">
                                    <strong>{{ is_null($delta) ? '—' : ($delta > 0 ? '+' : '') . '₹ ' . number_format($delta, 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="excel-card" style="padding:1rem">
                <div style="color:var(--muted);">No pickups found for the selected range.</div>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $pickups->withQueryString()->links() }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Quick presets
        document.querySelectorAll('[data-preset]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('presetInput').value = btn.getAttribute('data-preset');
                document.getElementById('filterForm').submit();
            });
        });

        // Clear preset when dates change
        const startEl = document.querySelector('input[name="start"]');
        const endEl = document.querySelector('input[name="end"]');
        [startEl, endEl].forEach(el => el.addEventListener('change', () => {
            document.getElementById('presetInput').value = '';
        }));

        // Smooth jump to a table
        function scrollIntoViewSmooth(selector) {
            const el = document.querySelector(selector);
            if (el) {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
        window.scrollIntoViewSmooth = scrollIntoViewSmooth;

        // === Excel Export helpers (SheetJS) ===
        function tableToSheet(table) {
            return XLSX.utils.table_to_sheet(table, {
                raw: true
            });
        }

        function exportOne(tableId, sheetName) {
            const table = document.getElementById(tableId);
            if (!table) {
                return;
            }
            const wb = XLSX.utils.book_new();
            const ws = tableToSheet(table);
            XLSX.utils.book_append_sheet(wb, ws, (sheetName || 'Sheet').substring(0, 31));
            const fileName = (sheetName || 'Export') + '.xlsx';
            XLSX.writeFile(wb, fileName);
        }
        window.exportOne = exportOne;

        // Export all visible cards as multi-sheet workbook
        const exportAllBtn = document.getElementById('exportAllBtn');
        if (exportAllBtn) {
            exportAllBtn.addEventListener('click', () => {
                const cards = document.querySelectorAll('.excel-card');
                if (!cards.length) {
                    return;
                }
                const wb = XLSX.utils.book_new();
                cards.forEach((card, idx) => {
                    const tableId = card.getAttribute('data-table-id');
                    const sheetRaw = card.getAttribute('data-sheet-name') || ('Sheet_' + (idx + 1));
                    const sheet = sheetRaw.substring(0, 31); // Excel sheet name limit
                    const table = document.getElementById(tableId);
                    if (table) {
                        const ws = tableToSheet(table);
                        XLSX.utils.book_append_sheet(wb, ws, sheet || ('Sheet' + (idx + 1)));
                    }
                });
                XLSX.writeFile(wb, 'Pickups_Export.xlsx');
            });
        }
    </script>
@endsection
