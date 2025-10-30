@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            /* Accent palette */
            --tab1: #6366f1;
            /* indigo */
            --tab2: #06b6d4;
            /* cyan   */
            --tab3: #22c55e;
            /* green  */
            --tab4: #f59e0b;
            /* amber  */

            /* Light theme tokens */
            --bg: #f7f8fb;
            --card: #ffffff;
            --muted: #64748b;
            /* slate-500 */
            --text: #0f172a;
            /* slate-900 */
            --text-soft: #334155;
            /* slate-700 */
            --ring: #e5e7eb;
            /* gray-200 */
            --ring-2: #cbd5e1;
            /* gray-300 */
            --ok: #16a34a;
            /* green-600 */
            --bad: #dc2626;
            /* red-600 */
            --head: #0f172a;

            --shadow-sm: 0 2px 6px rgba(2, 6, 23, .06);
            --shadow-md: 0 6px 18px rgba(2, 6, 23, .08);
            --shadow-lg: 0 14px 38px rgba(2, 6, 23, .10);
            --radius: 14px;
        }

        /* Page */
        html,
        body {
            background: var(--bg);
            color: var(--text);
        }

        .container-page {
            max-width: 1200px;
        }

        /* Sticky toolbar */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 30;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            padding: .75rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: 1fr auto;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        /* Inputs/selects */
        .date-range {
            display: flex;
            gap: .5rem;
            align-items: center;
            color: var(--text-soft);
        }

        .date-range input,
        .select-in {
            background: #fff;
            border: 1px solid var(--ring);
            color: var(--text);
            border-radius: 10px;
            padding: .5rem .75rem;
            box-shadow: var(--shadow-sm);
        }

        .date-range input:focus,
        .select-in:focus {
            outline: none;
            border-color: var(--tab2);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, .2);
        }

        /* Preset buttons (chips) */
        .btn-chip {
            border: none;
            padding: .55rem .9rem;
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: transform .08s ease, box-shadow .2s ease, filter .2s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-chip:hover {
            filter: brightness(1.05);
            box-shadow: var(--shadow-md);
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
            color: #065f46;
        }

        .chip4 {
            background: linear-gradient(135deg, var(--tab4), #fbbf24);
            color: #7c2d12;
        }

        .chip5 {
            background: linear-gradient(135deg, #64748b, #cbd5e1);
            color: #0f172a;
        }

        .btn-apply {
            background: linear-gradient(135deg, #111827, #1f2937);
            color: #fff;
        }

        /* Small tag */
        .tag {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .6rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .73rem;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid var(--ring);
        }

        /* Stats */
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
            box-shadow: var(--shadow-sm);
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

        /* Vendor Card */
        .vendor-card {
            border-radius: 16px;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--ring);
            box-shadow: var(--shadow-md);
        }

        .vendor-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1rem;
            color: #fff;
            background:
                linear-gradient(90deg, rgba(99, 102, 241, .95), rgba(6, 182, 212, .95));
        }

        .vendor-title {
            font-weight: 800;
            font-size: 1.05rem;
        }

        .vendor-sub {
            font-size: .85rem;
            opacity: .95;
        }

        .table-box {
            padding: .8rem 1rem;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 .5rem;
        }

        .items-table thead th {
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            color: var(--head);
            text-align: left;
            padding: .35rem .6rem;
        }

        .row-card {
            background: #ffffff;
            border: 1px solid var(--ring);
            box-shadow: var(--shadow-sm);
        }

        .row-card td {
            padding: .65rem .7rem;
            vertical-align: middle;
            color: var(--text-soft);
        }

        /* Pills */
        .pill {
            padding: .28rem .7rem;
            border-radius: 999px;
            font-weight: 700;
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
            color: var(--bad);
            font-weight: 800;
        }

        .diff-down {
            color: var(--ok);
            font-weight: 800;
        }

        /* Pagination */
        .pagination {
            margin-top: 1rem;
        }

        .pagination .page-link {
            background: #fff;
            border: 1px solid var(--ring);
            color: var(--text);
            box-shadow: var(--shadow-sm);
        }

        .pagination .active .page-link {
            background: var(--tab2);
            border-color: var(--tab2);
            color: #00303a;
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

            <div class="btns" style="display:flex; gap:.4rem; flex-wrap:wrap; justify-content:flex-end">
                <button class="btn-chip chip1" data-preset="today" type="button">Today</button>
                <button class="btn-chip chip5" data-preset="yesterday" type="button">Yesterday</button>
                <button class="btn-chip chip2" data-preset="tomorrow" type="button">Tomorrow</button>
                <button class="btn-chip chip3" data-preset="this_week" type="button">This Week</button>
                <button class="btn-chip chip4" data-preset="this_month" type="button">This Month</button>
                <button class="btn-chip btn-apply" type="submit">Apply</button>
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
                        <div class="vendor-sub">
                            Pickup: {{ $pkDate }} • Delivery: {{ $dvDate }} • Rider: {{ $riderName }}
                        </div>
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
                                    $eprc = $it->est_price ?? null; // optional if you store estimate price
                                    $aqty = $it->quantity ?? 0;
                                    $aprc = $it->price ?? 0;
                                    $ltotal = $it->item_total_price ?? ($aprc ?: 0) * ($aqty ?: 0);

                                    $eamt = $eprc !== null ? $eqty * $eprc : null;

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
                                        <div style="font-weight:700; color:var(--text);">
                                            {{ optional($it->flower)->name ?? '—' }}
                                        </div>
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
                                            <div style="color:#3730a3; font-size:.8rem; margin-top:.15rem">
                                                ₹ {{ number_format($eamt, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="pill pill-act">
                                            {{ $aqty ? number_format($aqty, 2) : '—' }} {{ $aname }}
                                        </span>
                                        <div style="color:#065f46; font-size:.8rem; margin-top:.15rem">
                                            ₹ {{ number_format($aprc ?: 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="{{ $diffCls }}" style="font-weight:800;">{{ $diffTxt }}</td>
                                    <td style="font-weight:800; color:var(--text);">
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
                            <span class="pill" style="background:#f8fafc; border:1px solid var(--ring);">
                                Diff: <span class="{{ $deltaCls }}"> {{ $sign }}₹
                                    {{ number_format($delta, 2) }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="vendor-card" style="padding:1rem">
                <div style="color:var(--text-soft);">No pickups found for the selected range.</div>
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
        document.addEventListener('DOMContentLoaded', () => {
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
        });
    </script>
@endsection
