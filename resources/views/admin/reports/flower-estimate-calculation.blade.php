@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Poppins font (similar to screenshot) --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- SheetJS for export --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            --pink: #ff5c8d;
            --pink-soft: #ffe4ed;
            --text-main: #222222;
            --text-muted: #6b7280;
            --border-soft: #f1f1f1;
            --bg-page: #fafbff;
            --bg-card: #ffffff;
        }

        html, body {
            background: var(--bg-page);
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text-main);
            font-size: 14px;
        }

        .pickup-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 10px 40px;
        }

        /* FILTER BAR
        ------------------------------------------------------------------ */
        .pickup-filter-form {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .pickup-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-pill {
            border-radius: 999px;
            border: 2px solid var(--pink);
            padding: 8px 24px;
            font-size: 13px;
            font-weight: 500;
            color: var(--pink);
            background: #fff;
            cursor: pointer;
            outline: none;
            box-shadow: 0 4px 10px rgba(255, 92, 141, 0.18);
            transition: background .15s, color .15s, box-shadow .15s, transform .1s;
        }

        .filter-pill:hover {
            background: var(--pink-soft);
            box-shadow: 0 6px 14px rgba(255, 92, 141, 0.22);
            transform: translateY(-1px);
        }

        .filter-pill.active {
            background: var(--pink);
            color: #fff;
        }

        .pickup-date-range {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pickup-date-range label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .pickup-date-range input[type="date"] {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 6px 10px;
            font-size: 12px;
            min-width: 120px;
        }

        .pickup-date-range input[type="date"]:focus {
            outline: none;
            border-color: var(--pink);
            box-shadow: 0 0 0 2px rgba(255, 92, 141, .16);
        }

        .btn-apply {
            border-radius: 999px;
            border: none;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 500;
            background: linear-gradient(135deg, #ff5c8d, #ff7fa6);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(255, 92, 141, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-apply:hover {
            filter: brightness(1.02);
        }

        /* HEADER ABOVE TABLE
        ------------------------------------------------------------------ */
        .pickup-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
            padding: 0 4px;
        }

        .pickup-header-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pickup-title {
            font-size: 16px;
            font-weight: 600;
        }

        .pickup-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .pickup-summary {
            font-size: 12px;
            color: var(--text-muted);
        }

        .btn-export {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 7px 14px;
            background: #fff;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06);
        }

        .btn-export:hover {
            background: #f9fafb;
        }

        /* TABLE
        ------------------------------------------------------------------ */
        .table-card {
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .table-scroll-wrap {
            overflow-x: auto;
        }

        .pickup-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .pickup-table thead th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
            background: #fafafa;
            border-bottom: 1px solid var(--border-soft);
            padding: 8px 10px;
            text-align: left;
        }

        .pickup-table tbody td {
            border-bottom: 1px solid var(--border-soft);
            padding: 10px 10px;
            font-size: 13px;
            vertical-align: middle;
            color: var(--text-main);
            background: #fff;
        }

        .pickup-table tbody tr:last-child td {
            border-bottom: none;
        }

        .pickup-table tbody tr:hover td {
            background: #fcfcff;
        }

        .pickup-table td:nth-child(1) {
            width: 40px;
        }

        .pickup-table td:nth-child(2) {
            min-width: 160px;
        }

        .pickup-table td,
        .pickup-table th {
            white-space: nowrap;
        }

        .pickup-table td:nth-child(4) {
            white-space: normal;
        }

        /* Vendor subtotal row */
        .group-row td {
            font-weight: 600;
            background: #fdf2f7;
            border-top: 1px solid #f9d7e5;
            border-bottom: 1px solid #f9d7e5;
        }

        .group-row .group-caption {
            color: #b42360;
        }

        /* Qty diff colouring */
        .diff-up {
            color: #b42318;
            font-weight: 600;
        }

        .diff-down {
            color: #027a48;
            font-weight: 600;
        }

        /* Pagination container tweak */
        .pickup-pagination {
            padding: 10px 16px 14px;
        }

        @media (max-width: 768px) {
            .pickup-filter-form {
                align-items: flex-start;
            }

            .pickup-date-range {
                justify-content: flex-start;
            }

            .pickup-header {
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="pickup-page">

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.pickups.manage') }}" id="filterForm" class="pickup-filter-form">
            <div class="pickup-pills">
                <button class="filter-pill {{ $preset === 'today' ? 'active' : '' }}" data-preset="today" type="button">
                    Today
                </button>
                <button class="filter-pill {{ $preset === 'yesterday' ? 'active' : '' }}" data-preset="yesterday"
                        type="button">
                    Yesterday
                </button>
                <button class="filter-pill {{ $preset === 'tomorrow' ? 'active' : '' }}" data-preset="tomorrow"
                        type="button">
                    Tomorrow
                </button>
                <button class="filter-pill {{ $preset === 'this_week' ? 'active' : '' }}" data-preset="this_week"
                        type="button">
                    This Week
                </button>
                <button class="filter-pill {{ $preset === 'this_month' ? 'active' : '' }}" data-preset="this_month"
                        type="button">
                    This Month
                </button>
            </div>

            <div class="pickup-date-range">
                <label>
                    From
                    <input type="date" name="start" value="{{ $start }}">
                </label>
                <label>
                    To
                    <input type="date" name="end" value="{{ $end }}">
                </label>
                <button class="btn-apply" type="submit">
                    Apply
                </button>
            </div>

            <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">
        </form>

        {{-- SMALL HEADER ABOVE TABLE --}}
        @php
            $pageActual = $pickups->getCollection()->flatMap->flowerPickupItems->reduce(function ($carry, $it) {
                $aprc = (float) ($it->price ?? 0);
                $aqty = (float) ($it->quantity ?? 0);
                $lt = $it->item_total_price !== null ? (float) $it->item_total_price : $aprc * $aqty;
                return $carry + $lt;
            }, 0.0);
            $pageItems = $pickups->getCollection()->flatMap->flowerPickupItems->count();
            $pageVendors = $pickups->getCollection()->pluck('vendor.vendor_id')->filter()->unique()->count();
        @endphp

        <div class="pickup-header">
            <div class="pickup-header-left">
                <div class="pickup-title">Vendor Flower Pickups</div>
                <div class="pickup-subtitle">
                    Range:
                    {{ \Carbon\Carbon::parse($start)->format('d M Y') }}
                    –
                    {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                </div>
            </div>
            <div class="pickup-summary">
                Income: ₹{{ number_format($pageActual, 2) }} ·
                Items: {{ number_format($pageItems) }} ·
                Vendors: {{ number_format($pageVendors) }}
            </div>
            <div>
                <button class="btn-export" id="exportAllBtn" type="button">
                    Export (XLSX)
                </button>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="table-card">
            <div class="table-scroll-wrap">
                <table class="pickup-table" id="masterExcelTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>Pickup Date</th>
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
                        $byVendor = $pickups->getCollection()
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
                                    $ltotal = $it->item_total_price !== null
                                        ? (float) $it->item_total_price
                                        : $aprc * $aqty;

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
                                            <span style="opacity:.8"> {{ $diffUnit }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        {{-- vendor subtotal --}}
                        <tr class="group-row">
                            <td colspan="4">
                                <span class="group-caption">Subtotal — {{ $vendorName }}</span>
                            </td>
                            <td colspan="5"></td>
                            <td>₹ {{ number_format($vendorActSum, 2) }}</td>
                            <td>{{ $vendorQtyDiffSum > 0 ? '+' : '' }}{{ number_format($vendorQtyDiffSum, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pickup-pagination d-flex justify-content-center">
                {{ $pickups->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Preset pills click
        document.querySelectorAll('[data-preset]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('presetInput').value = btn.getAttribute('data-preset');
                document.getElementById('filterForm').submit();
            });
        });

        // Clear preset when manual dates change
        ['start', 'end'].forEach(name => {
            const el = document.querySelector(`input[name="${name}"]`);
            if (!el) return;
            el.addEventListener('change', () => {
                document.getElementById('presetInput').value = '';
                // also remove active state from pills visually
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            });
        });

        // Export whole table to XLSX
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
