@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Poppins (page) + Nunito Sans (table) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    {{-- SheetJS --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            /* Core palette */
            --brand-blue: #e9f2ff;
            --brand-blue-edge: #cfe0ff;
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;

            /* Table */
            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #64748b;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 16px;

            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --accent-border: #bfdbfe;
            --danger: #b42318;
            --danger-soft: #fef2f2;
            --success: #047857;
            --success-soft: #ecfdf3;
            --neutral-soft: #f3f4f6;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        .container-page {
            max-width: 1320px;
        }

        /* Toolbar */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            padding: .85rem 1rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: minmax(0, 1.2fr) auto;
            align-items: center;
            box-shadow: var(--shadow);
            margin-bottom: 1.1rem;
        }

        .date-range {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: .85rem;
        }

        .date-range span {
            font-weight: 500;
        }

        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 170px;
        }

        .date-range input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .22);
        }

        .btn-chip {
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #0f172a;
            padding: .42rem .9rem;
            border-radius: 999px;
            font-weight: 500;
            cursor: pointer;
            font-size: .82rem;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .btn-chip::before {
            content: '⦿';
            font-size: .7rem;
            opacity: .5;
        }

        .btn-chip:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
        }

        .btn-apply {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-apply::before {
            content: '↻';
            font-size: .75rem;
            opacity: .75;
        }

        /* Header band */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 18px;
            padding: .9rem 1.2rem;
            box-shadow: var(--shadow);
            margin-bottom: .9rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .band h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--header-text);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band h3 span.label {
            font-size: .78rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.07);
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .chip span.icon {
            font-size: .9rem;
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
            border-color: #bae6fd;
        }

        /* Workbook */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .workbook-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
        }

        .workbook-title {
            font-weight: 600;
            color: #111827;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .workbook-title::before {
            content: '📊';
            font-size: 1.1rem;
        }

        .workbook-sub {
            color: #4b5563;
            font-size: .84rem;
        }

        .workbook-tools {
            display: flex;
            gap: .5rem;
        }

        .export-btn {
            border: 1px solid var(--accent-border);
            border-radius: 999px;
            padding: .45rem .9rem;
            font-weight: 500;
            cursor: pointer;
            background: var(--accent-soft);
            color: #1d4ed8;
            font-size: .84rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            transition: all .15s ease;
        }

        .export-btn::before {
            content: '⬇';
            font-size: .8rem;
        }

        .export-btn:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        /* Table wrapper */
        .excel-wrap {
            padding: 1rem 1.1rem 1.1rem;
            overflow: auto;
        }

        .excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            font-size: .9rem;
            border: 1px solid var(--table-border);
            border-radius: 14px;
            overflow: hidden;

            /* TABLE FONT: Nunito Sans */
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .excel thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            padding: .6rem .7rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 0;
            border-right: 1px solid rgba(55, 65, 81, 0.7);
            white-space: nowrap;
        }

        .excel thead th:first-child {
            border-top-left-radius: 14px;
        }

        .excel thead th:last-child {
            border-top-right-radius: 14px;
            border-right: none;
        }

        .excel tbody td {
            border-top: 1px solid var(--table-border);
            border-right: 1px solid var(--table-border);
            padding: .55rem .7rem;
            vertical-align: middle;
            color: var(--text);
            font-weight: 400;
            background: #fff;
        }

        .excel tbody tr:nth-child(even) td:not(.group-row td) {
            background: var(--table-zebra);
        }

        .excel tbody tr:last-child td:first-child {
            border-bottom-left-radius: 14px;
        }

        .excel tbody tr:last-child td:last-child {
            border-bottom-right-radius: 14px;
        }

        .excel tbody tr:hover td {
            background: var(--table-hover);
        }

        .excel th,
        .excel td {
            font-variant-numeric: tabular-nums;
        }

        .excel tr td:first-child,
        .excel thead th:first-child {
            border-left: none;
        }

        .excel tbody tr td:last-child {
            border-right: none;
        }

        /* Column helpers */
        .col-index {
            width: 56px;
            text-align: right;
            color: #6b7280;
            font-size: .8rem;
        }

        .col-vendor {
            font-weight: 500;
            white-space: nowrap;
        }

        .col-date {
            white-space: nowrap;
            font-size: .86rem;
            color: #4b5563;
        }

        .col-text {
            font-weight: 500;
        }

        .col-num,
        .col-money,
        .col-diff {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .col-money span.currency {
            color: #6b7280;
            font-size: .8rem;
            margin-right: .18rem;
        }

        /* Qty + unit inside same cell */
        .qty-with-unit {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: .35rem;
        }

        .qty-with-unit .qty {
            font-weight: 500;
        }

        .qty-with-unit .unit-pill {
            display: inline-flex;
            align-items: center;
            padding: .1rem .55rem;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 500;
            background: var(--accent-soft);
            color: #1d4ed8;
            border: 1px solid var(--accent-border);
        }

        .qty-with-unit .unit-pill::before {
            content: '◦';
            font-size: .7rem;
            margin-right: .15rem;
        }

        /* Vendor subtotal row */
        .group-row td {
            background: #eef2ff !important;
            border-top: 2px solid var(--table-border);
            font-weight: 600;
            color: #1d283a;
            font-size: .88rem;
        }

        .group-caption {
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .group-caption::before {
            content: '⏺';
            font-size: .7rem;
            color: #4f46e5;
        }

        /* Diff styles */
        .diff-up {
            color: var(--danger);
            font-weight: 600;
        }

        .diff-down {
            color: var(--success);
            font-weight: 600;
        }

        .diff-zero {
            color: #6b7280;
            font-weight: 500;
        }

        .diff-pill {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: .25rem;
            padding: .16rem .55rem;
            border-radius: 999px;
            font-size: .78rem;
            min-width: 90px;
        }

        .diff-pill.up {
            background: var(--danger-soft);
        }

        .diff-pill.down {
            background: var(--success-soft);
        }

        .diff-pill.zero {
            background: var(--neutral-soft);
        }

        .diff-pill span.unit {
            opacity: .8;
            font-size: .76rem;
        }

        /* Pagination alignment override (if needed) */
        .pagination {
            margin-top: 1rem;
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
                gap: .4rem;
            }

            .workbook-tools {
                width: 100%;
                justify-content: flex-start;
            }

            .export-btn {
                width: auto;
            }
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

        {{-- Summary band --}}
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
                <span class="label">Pickup Summary</span>
            </h3>
            <div class="chips">
                <span class="chip green">
                    <span class="icon">💰</span>
                    <span>Income</span> ₹{{ number_format($pageActual, 2) }}
                </span>
                <span class="chip orange">
                    <span class="icon">📦</span>
                    <span>Items</span> {{ number_format($pageItems) }}
                </span>
                <span class="chip blue">
                    <span class="icon">🏪</span>
                    <span>Vendors</span> {{ number_format($pageVendors) }}
                </span>
            </div>
        </div>

        {{-- Workbook --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Pickup Items — Detailed</div>
                    <div class="workbook-sub">
                        Range: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} —
                        {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                    </div>
                </div>
                <div class="workbook-tools">
                    <button class="export-btn" id="exportAllBtn" type="button">
                        Export (XLSX)
                    </button>
                </div>
            </div>

            <div class="excel-wrap">
                <table class="excel" id="masterExcelTable">
                    <thead>
                        <tr>
                            <th class="col-index">#</th>
                            <th class="col-vendor">Vendor</th>
                            <th class="col-date">Pickup Date</th>
                            <th class="col-text">Item</th>
                            <th class="col-num">Est. Qty / Unit</th>
                            <th class="col-num">Act. Qty / Unit</th>
                            <th class="col-money">Unit Price</th>
                            <th class="col-money">Total Amount</th>
                            <th class="col-diff">Qty Diff</th>
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
                                        $ename = optional($it->estUnit)->unit_name ?? ($unitMap[$it->est_unit_id] ?? '—');
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

                                        $diffClass = $qdiff > 0 ? 'up' : ($qdiff < 0 ? 'down' : 'zero');
                                        $diffTextClass = $qdiff > 0 ? 'diff-up' : ($qdiff < 0 ? 'diff-down' : 'diff-zero');
                                    @endphp
                                    <tr>
                                        <td class="col-index">{{ $rowNo }}</td>
                                        <td class="col-vendor">{{ $vendorName }}</td>
                                        <td class="col-date">{{ $pkDate }}</td>
                                        <td class="col-text">{{ optional($it->flower)->name ?? '—' }}</td>

                                        {{-- Est Qty + Unit --}}
                                        <td class="col-num">
                                            @if ($eqty)
                                                <span class="qty-with-unit">
                                                    <span class="qty">{{ number_format($eqty, 2) }}</span>
                                                    @if ($ename && $ename !== '—')
                                                        <span class="unit-pill">{{ $ename }}</span>
                                                    @endif
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- Act Qty + Unit --}}
                                        <td class="col-num">
                                            @if ($aqty)
                                                <span class="qty-with-unit">
                                                    <span class="qty">{{ number_format($aqty, 2) }}</span>
                                                    @if ($aname && $aname !== '—')
                                                        <span class="unit-pill">{{ $aname }}</span>
                                                    @endif
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="col-money">
                                            <span class="currency">₹</span>{{ number_format($aprc, 2) }}
                                        </td>
                                        <td class="col-money">
                                            <span class="currency">₹</span>{{ number_format($ltotal, 2) }}
                                        </td>

                                        <td class="col-diff {{ $diffTextClass }}">
                                            <span class="diff-pill {{ $diffClass }}">
                                                @if ($qdiff > 0)
                                                    <span>+{{ number_format($qdiff, 2) }}</span>
                                                @elseif ($qdiff < 0)
                                                    <span>{{ number_format($qdiff, 2) }}</span>
                                                @else
                                                    <span>0.00</span>
                                                @endif
                                                @if ($diffUnit)
                                                    <span class="unit">{{ $diffUnit }}</span>
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            {{-- Subtotal row per vendor --}}
                            <tr class="group-row">
                                <td colspan="6">
                                    <span class="group-caption">Subtotal — {{ $vendorName }}</span>
                                </td>
                                <td></td>
                                <td class="col-money">
                                    <span class="currency">₹</span>{{ number_format($vendorActSum, 2) }}
                                </td>
                                <td class="col-diff">
                                    {{ $vendorQtyDiffSum > 0 ? '+' : '' }}{{ number_format($vendorQtyDiffSum, 2) }}
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
