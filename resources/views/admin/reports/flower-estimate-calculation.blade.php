@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inter font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    {{-- SheetJS --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            /* Screenshot-like palette */
            --brand-blue: #e9f2ff;
            /* header band bg */
            --brand-blue-edge: #cfe0ff;
            /* header band border */
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e9f2ff;
            --chip-blue-text: #0b2a5b;

            --table-head: #eef2ff;
            --table-border: #dbe4ff;
            --table-zebra: #f8fafc;

            --text: #0f172a;
            --muted: #667085;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 8px 28px rgba(2, 6, 23, .08);
            --radius: 14px;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
            font-weight: 400;
        }

        .container-page {
            max-width: 1280px;
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

        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 10px;
            padding: .55rem .8rem;
            background: #fff;
            font-weight: 500;
        }

        .date-range input:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, .2);
        }

        .btn-chip {
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #0f172a;
            padding: .55rem .9rem;
            border-radius: 999px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-chip:hover {
            background: #f2f4f7;
        }

        .btn-apply {
            background: linear-gradient(135deg, #0f172a, #334155);
            color: #fff;
            border: none;
        }

        /* Header band (like screenshot) */
        .band {
            background: var(--brand-blue);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 12px;
            padding: .8rem 1rem;
            box-shadow: var(--shadow);
            margin-bottom: .6rem;
        }

        .band h3 {
            margin: 0 0 .2rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--header-text);
        }

        .chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .6rem;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
            border: 1px solid transparent;
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
            border-color: #cfe0ff;
        }

        /* Workbook */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .workbook-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .8rem 1rem;
            background: var(--brand-blue);
            border-bottom: 1px solid var(--brand-blue-edge);
        }

        .workbook-title {
            font-weight: 600;
            color: var(--header-text);
            font-size: .98rem;
        }

        .workbook-sub {
            color: #1c3b73;
            font-size: .86rem;
        }

        .workbook-tools {
            display: flex;
            gap: .5rem;
        }

        .export-btn {
            border: 1px solid #cfe0ff;
            border-radius: 8px;
            padding: .48rem .7rem;
            font-weight: 500;
            cursor: pointer;
            background: #fff;
            color: #0b2a5b;
        }

        /* Table */
        .excel-wrap {
            padding: 1rem;
            overflow: auto;
        }

        .excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            font-size: .92rem;
            border: 1px solid var(--table-border);
        }

        .excel thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: var(--table-head);
            color: #132f63;
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .06em;
            border-bottom: 2px solid var(--table-border);
            padding: .5rem .6rem;
            text-align: left;
            font-weight: 600;
        }

        .excel td {
            border-top: 1px solid var(--table-border);
            border-right: 1px solid var(--table-border);
            padding: .5rem .6rem;
            vertical-align: middle;
            color: var(--text);
            font-weight: 400;
        }

        .excel tr:nth-child(even) td {
            background: var(--table-zebra);
        }

        .excel tr td:first-child,
        .excel thead th:first-child {
            border-left: 1px solid var(--table-border);
        }

        /* Vendor subtotal row */
        .group-row td {
            background: #f0f6ff;
            border-top: 2px solid var(--table-border);
            font-weight: 600;
            color: #0f2f59;
        }

        .group-caption {
            font-weight: 600;
        }

        /* Diff colors */
        .diff-up {
            color: #b42318;
            font-weight: 600;
        }

        /* red-ish */
        .diff-down {
            color: #027a48;
            font-weight: 600;
        }

        /* green-ish */

        /* Stats (kept minimal) */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .6rem;
            margin: .8rem 0 1rem;
        }

        .stat-card {
            padding: .7rem .9rem;
            border-radius: 12px;
            background: var(--card);
            border: 1px solid var(--ring);
        }

        .stat-title {
            font-size: .82rem;
            color: var(--muted);
        }

        .stat-value {
            font-weight: 600;
            font-size: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Filters --}}
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

        {{-- Screenshot-style top band --}}
        @php
            // Page totals for chips
            $pageActual = $pickups->getCollection()->flatMap->flowerPickupItems->reduce(function ($carry, $it) {
                $aprc = (float) ($it->price ?? 0);
                $aqty = (float) ($it->quantity ?? 0);
                $lt = $it->item_total_price !== null ? (float) $it->item_total_price : $aprc * $aqty;
                return $carry + $lt;
            }, 0.0);
            $pageItems = $pickups->getCollection()->flatMap->flowerPickupItems->count();
            $pageVendors = $pickups->getCollection()->pluck('vendor.vendor_id')->filter()->unique()->count();
        @endphp
        <div class="band">
            <h3>
                {{ \Carbon\Carbon::parse($start)->format('M d') }} – {{ \Carbon\Carbon::parse($end)->format('M d, Y') }}
                (All Days)
            </h3>
            <div class="chips">
                <span class="chip green">Income ₹{{ number_format($pageActual, 2) }}</span>
                <span class="chip orange">Items {{ number_format($pageItems) }}</span>
                <span class="chip blue">Vendors {{ number_format($pageVendors) }}</span>
            </div>
        </div>

        {{-- Workbook --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Pickup Items — Detailed</div>
                    <div class="workbook-sub">Range: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} —
                        {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</div>
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
                            <th>Item</th>
                            <th>Est. Qty</th>
                            <th>Est. Unit</th>
                            <th>Act. Qty</th>
                            <th>Act. Unit</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>
                            <th>Qty Diff</th> {{-- now shows unit --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNo = 0;
                            $byVendor = $pickups
                                ->getCollection()
                                ->groupBy(fn($p) => optional($p->vendor)->vendor_name ?? 'Unknown Vendor');
                        @endphp

                        @foreach ($byVendor as $vendorName => $vendorPickups)
                            @php
                                $vendorActSum = 0.0;
                                $vendorQtyDiffSum = 0.0;
                            @endphp

                            @foreach ($vendorPickups as $pickup)
                                @php
                                    $pkDate = $pickup->pickup_date
                                        ? \Carbon\Carbon::parse($pickup->pickup_date)->format('d M Y')
                                        : '—';
                                @endphp

                                @foreach ($pickup->flowerPickupItems as $it)
                                    @php
                                        $rowNo++;
                                        $ename =
                                            optional($it->estUnit)->unit_name ?? ($unitMap[$it->est_unit_id] ?? '—');
                                        $aname = optional($it->unit)->unit_name ?? ($unitMap[$it->unit_id] ?? '—');

                                        $eqty = (float) ($it->est_quantity ?? 0);
                                        $aqty = (float) ($it->quantity ?? 0);
                                        $aprc = (float) ($it->price ?? 0);
                                        $ltotal =
                                            $it->item_total_price !== null
                                                ? (float) $it->item_total_price
                                                : $aprc * $aqty;

                                        // Prefer actual unit for diff; fallback to estimate unit
                                        $diffUnit = $aname ?: $ename ?: '';
                                        $qdiff = round($aqty - $eqty, 2);

                                        $vendorActSum += $ltotal;
                                        $vendorQtyDiffSum += $qdiff;
                                    @endphp
                                    <tr>
                                        <td>{{ $rowNo }}</td>
                                        <td>{{ $vendorName }}</td>
                                        <td>{{ $pkDate }}</td>
                                        <td>{{ optional($it->flower)->name ?? '—' }}</td>

                                        <td>{{ $eqty ? number_format($eqty, 2) : '—' }}</td>
                                        <td>{{ $ename }}</td>

                                        <td>{{ $aqty ? number_format($aqty, 2) : '—' }}</td>
                                        <td>{{ $aname }}</td>
                                        <td>₹ {{ number_format($aprc, 2) }}</td>
                                        <td>₹ {{ number_format($ltotal, 2) }}</td>

                                        <td class="{{ $qdiff > 0 ? 'diff-up' : ($qdiff < 0 ? 'diff-down' : '') }}">
                                            {{ $qdiff > 0 ? '+' : '' }}{{ number_format($qdiff, 2) }}
                                            @if ($diffUnit)
                                                <span style="opacity:.8">{{ $diffUnit }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            <tr class="group-row">
                                <td colspan="5"><span class="group-caption">Subtotal — {{ $vendorName }}</span></td>
                                <td></td>
                                <td colspan="3" style="text-align:right;">Actual (sum)</td>
                                <td>₹ {{ number_format($vendorActSum, 2) }}</td>
                                <td>{{ $vendorQtyDiffSum > 0 ? '+' : '' }}{{ number_format($vendorQtyDiffSum, 2) }}</td>
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

        // Export whole table
        const exportBtn = document.getElementById('exportAllBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                const table = document.getElementById('masterExcelTable');
                if (!table) return;
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table, {
                    raw: true
                });
                XLSX.utils.book_append_sheet(wb, ws, 'Pickups');
                XLSX.writeFile(wb, 'Pickups_All.xlsx');
            });
        }
    </script>
@endsection
