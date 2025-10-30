@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --tab1: #6366f1;
            --tab2: #06b6d4;
            --tab3: #22c55e;
            --tab4: #f59e0b;

            --bg: #f2f3f6;
            /* slate-900 */
            --card: #f2f3f6;
            /* deep bluish */
            --muted: #94a3b8;
            /* slate-400 */
            --ring: rgba(255, 255, 255, .08);
            --success: #22c55e;
            --danger: #ef4444;
        }

        body {
            background: linear-gradient(135deg, #0b1220, #111827 45%, #0b1220);
        }

        .container-page {
            max-width: 1200px;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(10px);
            background: rgba(11, 18, 32, .7);
            border: 1px solid var(--ring);
            border-radius: 14px;
            padding: .75rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: 1fr auto;
            align-items: center;
        }

        .btn-chip {
            border: none;
            padding: .55rem .9rem;
            border-radius: 999px;
            color: #fff;
            font-weight: 600;
            letter-spacing: .2px;
            cursor: pointer;
            transition: transform .08s ease;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .35), inset 0 0 0 1px rgba(255, 255, 255, .07);
        }

        .btn-chip:active {
            transform: translateY(1px) scale(.99);
        }

        .chip1 {
            background: linear-gradient(135deg, var(--tab1), #8b5cf6);
        }

        .chip2 {
            background: linear-gradient(135deg, var(--tab2), #22d3ee);
        }

        .chip3 {
            background: linear-gradient(135deg, var(--tab3), #86efac);
            color: #052e16;
        }

        .chip4 {
            background: linear-gradient(135deg, var(--tab4), #fbbf24);
            color: #3b1d00;
        }

        .chip5 {
            background: linear-gradient(135deg, #64748b, #cbd5e1);
            color: #0b1220;
        }

        .date-range {
            display: flex;
            gap: .5rem;
            align-items: center;
            color: #e2e8f0;
        }

        .date-range input,
        .select-in {
            background: #0b1220;
            border: 1px solid var(--ring);
            color: #e2e8f0;
            border-radius: 10px;
            padding: .5rem .75rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin: 1rem 0;
        }

        .stat-card {
            padding: .85rem 1rem;
            border-radius: 14px;
            background: #0b1220;
            color: #e2e8f0;
            border: 1px solid var(--ring);
        }

        .stat-title {
            font-size: .85rem;
            color: var(--muted);
        }

        .stat-value {
            font-weight: 800;
            letter-spacing: .5px;
            font-size: 1.1rem;
        }

        .vendor-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--ring);
            color: #e5e7eb;
            background: linear-gradient(135deg, rgba(99, 102, 241, .16), rgba(11, 18, 32, .6));
        }

        .vendor-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1rem;
            background:
                radial-gradient(1200px 200px at -10% -100%, rgba(255, 255, 255, .06), transparent 40%),
                linear-gradient(135deg, var(--tab1), var(--tab2));
            color: #fff;
        }

        .vendor-title {
            font-weight: 800;
            font-size: 1.05rem;
            text-shadow: 0 2px 18px rgba(0, 0, 0, .25);
        }

        .vendor-sub {
            font-size: .85rem;
            opacity: .9;
        }

        .table-box {
            padding: .6rem .8rem;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 .5rem;
        }

        .items-table thead th {
            text-transform: uppercase;
            font-size: .7rem;
            letter-spacing: .09em;
            color: #c7d2fe;
            text-align: left;
            padding: .35rem .6rem;
        }

        .row-card {
            background: #0b1220;
            border: 1px solid var(--ring);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        }

        .row-card td {
            padding: .6rem .65rem;
            vertical-align: middle;
            color: #e5e7eb;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .55rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .73rem;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .diff-up {
            color: var(--danger);
            font-weight: 800;
        }

        .diff-down {
            color: var(--success);
            font-weight: 800;
        }

        .pill {
            padding: .25rem .6rem;
            border-radius: 999px;
            font-weight: 700;
        }

        .pill-est {
            background: rgba(99, 102, 241, .2);
            border: 1px solid rgba(99, 102, 241, .35);
            color: #c7d2fe;
        }

        .pill-act {
            background: rgba(34, 197, 94, .2);
            border: 1px solid rgba(34, 197, 94, .35);
            color: #bbf7d0;
        }

        .pagination {
            margin-top: 1rem;
        }

        .pagination .page-link {
            background: #0b1220;
            border: 1px solid var(--ring);
            color: #e2e8f0;
        }

        .pagination .active .page-link {
            background: var(--tab2);
            border-color: var(--tab2);
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Sticky toolbar --}}
        <form method="GET" action="{{ route('admin.pickups.manage') }}" id="filterForm" class="toolbar">
            <div class="date-range">
                <span class="tag" title="From date">From</span>
                <input type="date" name="start" value="{{ $start }}" />
                <span class="tag" title="To date">To</span>
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

            <div class="btns" style="display:flex; gap:.4rem; flex-wrap: wrap; justify-content:flex-end">
                <button class="btn-chip chip1" data-preset="today" type="button">Today</button>
                <button class="btn-chip chip5" data-preset="yesterday" type="button">Yesterday</button>
                <button class="btn-chip chip2" data-preset="tomorrow" type="button">Tomorrow</button>
                <button class="btn-chip chip3" data-preset="this_week" type="button">This Week</button>
                <button class="btn-chip chip4" data-preset="this_month" type="button">This Month</button>
                <button class="btn-chip" style="background:linear-gradient(135deg,#111827,#1f2937);"
                    type="submit">Apply</button>
            </div>
            <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">
        </form>

        {{-- Top stats --}}
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

        {{-- Vendor grouped cards --}}
        @forelse($pickups as $pickup)
            @php
                $vendorName = optional($pickup->vendor)->vendor_name ?? 'Unknown Vendor';
                $riderName = optional($pickup->rider)->rider_name ?? '—';
                $pkDate = optional($pickup->pickup_date)->format('d M Y');
                $dvDate = optional($pickup->delivery_date)->format('d M Y');
                $sumEst = 0;
                $sumAct = (float) $pickup->total_price;
            @endphp

            <div class="vendor-card mb-3">
                <div class="vendor-head">
                    <div>
                        <div class="vendor-title">{{ $vendorName }}</div>
                        <div class="vendor-sub">Pickup: {{ $pkDate }} • Delivery: {{ $dvDate }} • Rider:
                            {{ $riderName }}</div>
                    </div>
                    <div style="display:flex; gap:.4rem;">
                        <span class="pill pill-act">Actual: ₹ {{ number_format($sumAct, 2) }}</span>
                    </div>
                </div>

                <div class="table-box">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Estimate</th>
                                <th>Actual</th>
                                <th>Diff</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pickup->flowerPickupItems as $it)
                                @php
                                    $ename = optional($it->estUnit)->unit_name ?? ($unitMap[$it->est_unit_id] ?? '—');
                                    $aname = optional($it->unit)->unit_name ?? ($unitMap[$it->unit_id] ?? '—');

                                    $eqty = $it->est_quantity ?? 0;
                                    $eprc = $it->est_price ?? null; // if you store estimate price
                                    $aqty = $it->quantity ?? 0;
                                    $aprc = $it->price ?? 0;
                                    $ltotal = $it->item_total_price ?? ($aprc ?: 0) * ($aqty ?: 0);

                                    // Estimate amount (fallback if you store price)
                                    $eamt = $eprc !== null ? $eqty * $eprc : null;

                                    // Difference logic (by quantity if same unit name; otherwise show “—”)
                                    $diffTxt = '—';
                                    $diffCls = '';
                                    if ($ename && $aname && $ename === $aname) {
                                        $qdiff = round(($aqty ?: 0) - ($eqty ?: 0), 2);
                                        if ($qdiff > 0) {
                                            $diffTxt = "+{$qdiff} $aname";
                                            $diffCls = 'diff-up';
                                        } elseif ($qdiff < 0) {
                                            $diffTxt = "{$qdiff} $aname";
                                            $diffCls = 'diff-down';
                                        } else {
                                            $diffTxt = '0';
                                        }
                                    }

                                    if ($eamt !== null) {
                                        $sumEst += $eamt;
                                    }
                                @endphp
                                <tr class="row-card">
                                    <td>
                                        <div style="font-weight:700">{{ optional($it->flower)->name ?? '—' }}</div>
                                        <div style="color:var(--muted); font-size:.78rem">
                                            @if ($it->vendor?->vendor_name)
                                                <span class="tag">{{ $it->vendor->vendor_name }}</span>
                                            @endif
                                            @if ($it->rider?->rider_name)
                                                <span class="tag">{{ $it->rider->rider_name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="pill pill-est">
                                            {{ $eqty ? number_format($eqty, 2) : '—' }} {{ $ename }}
                                        </span>
                                        @if (!is_null($eamt))
                                            <div style="color:#c7d2fe; font-size:.8rem; margin-top:.15rem">₹
                                                {{ number_format($eamt, 2) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="pill pill-act">
                                            {{ $aqty ? number_format($aqty, 2) : '—' }} {{ $aname }}
                                        </span>
                                        <div style="color:#bbf7d0; font-size:.8rem; margin-top:.15rem">
                                            ₹ {{ number_format($aprc ?: 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="{{ $diffCls }}" style="font-weight:800;">
                                        {{ $diffTxt }}
                                    </td>
                                    <td style="font-weight:800;">
                                        ₹ {{ number_format($ltotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="display:flex; gap:.6rem; justify-content:flex-end; margin-top:.6rem;">
                        @if ($sumEst > 0)
                            <span class="pill pill-est">Estimated (sum): ₹ {{ number_format($sumEst, 2) }}</span>
                        @endif
                        <span class="pill pill-act">Actual (sum): ₹ {{ number_format($sumAct, 2) }}</span>
                        @if ($sumEst > 0)
                            @php
                                $delta = $sumAct - $sumEst;
                                $deltaCls = $delta > 0 ? 'diff-up' : ($delta < 0 ? 'diff-down' : '');
                                $sign = $delta > 0 ? '+' : '';
                            @endphp
                            <span class="pill" style="background:rgba(255,255,255,.06); border:1px solid var(--ring);">
                                Diff: <span class="{{ $deltaCls }}"> {{ $sign }}₹
                                    {{ number_format($delta, 2) }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="vendor-card" style="padding:1rem">
                <div style="color:#e2e8f0;">No pickups found for the selected range.</div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Quick preset buttons
            document.querySelectorAll('[data-preset]').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('presetInput').value = btn.getAttribute('data-preset');
                    document.getElementById('filterForm').submit();
                });
            });

            // If user manually changes date inputs, clear preset to “custom”
            const startEl = document.querySelector('input[name="start"]');
            const endEl = document.querySelector('input[name="end"]');
            [startEl, endEl].forEach(el => {
                el.addEventListener('change', () => {
                    document.getElementById('presetInput').value = '';
                });
            });
        });
    </script>
@endsection
