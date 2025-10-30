@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-vendor {
            border-left: 6px solid #6366f1;
        }

        .badge-soft {
            background: #eef2ff;
            color: #4338ca;
        }

        .badge-ok {
            background: #ecfdf5;
            color: #065f46;
        }

        .badge-warn {
            background: #fff7ed;
            color: #9a3412;
        }

        .badge-bad {
            background: #fef2f2;
            color: #991b1b;
        }

        .diff-pos {
            color: #065f46;
            font-weight: 600;
        }

        /* better/under */
        .diff-neg {
            color: #991b1b;
            font-weight: 600;
        }

        /* worse/over  */
        .table-items th,
        .table-items td {
            vertical-align: middle;
        }

        .kpi {
            font-weight: 700;
        }

        .muted {
            color: #6b7280;
        }

        .chip {
            padding: .25rem .5rem;
            border-radius: 999px;
            font-size: .75rem;
        }

        .chip-unit {
            background: #f1f5f9;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
            <div>
                <h4 class="mb-0">Manage Pickups</h4>
                <div class="muted">Vendor-wise headers with line items and Estimate vs Actual.</div>
            </div>

            {{-- Filters --}}
            <form class="row gy-2 gx-2 align-items-end" method="get" action="{{ route('admin.pickups.manage') }}">
                <div class="col-auto">
                    <label class="form-label mb-1">Start</label>
                    <input type="date" name="start" value="{{ $start }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1">End</label>
                    <input type="date" name="end" value="{{ $end }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1">Vendor</label>
                    <select name="vendor_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($vendors as $v)
                            <option value="{{ $v->vendor_id }}" @selected($vendorId == $v->vendor_id)>{{ $v->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1">Rider</label>
                    <select name="rider_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($riders as $r)
                            <option value="{{ $r->rider_id }}" @selected($riderId == $r->rider_id)>{{ $r->rider_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
        </div>

        @forelse($pickups as $pickup)
            @php
                // Compute vendor-level totals (Actual)
                $actualTotal = 0.0;
                foreach ($pickup->flowerPickupItems as $it) {
                    $actualTotal += (float) ($it->item_total_price ?? ($it->price ?? 0) * ($it->quantity ?? 0));
                }

                // Compute estimate total if you store est_price (if not, we just show 0 / N/A)
                $estimateTotal = 0.0;
                $hasAnyEstPrice = false;
                foreach ($pickup->flowerPickupItems as $it) {
                    if (!is_null($it->est_price ?? null)) {
                        $hasAnyEstPrice = true;
                        $estimateTotal += (float) $it->est_price * (float) ($it->est_quantity ?? 0);
                    }
                }
                $estimateTotal = $hasAnyEstPrice ? $estimateTotal : null;

                $diffValue = $estimateTotal !== null ? $actualTotal - $estimateTotal : null;
            @endphp

            <div class="card card-vendor mb-4 shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-soft"># {{ $pickup->pick_up_id }}</span>
                        <h6 class="mb-0">
                            {{ $pickup->vendor->vendor_name ?? '—' }}
                        </h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="chip chip-unit">
                            Pickup: <strong>{{ $pickup->pickup_date?->toDateString() }}</strong>
                        </div>
                        <div class="chip chip-unit">
                            Delivery: <strong>{{ $pickup->delivery_date?->toDateString() }}</strong>
                        </div>
                        <div class="chip {{ $pickup->rider ? 'badge-ok' : 'badge-bad' }}">
                            Rider: <strong>{{ $pickup->rider->rider_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="chip badge-ok">Actual Total: <span class="kpi ms-1">₹
                                {{ number_format($actualTotal, 2) }}</span></div>
                        <div class="chip {{ $estimateTotal === null ? 'badge-warn' : 'badge-soft' }}">
                            Est. Total:
                            <span
                                class="kpi ms-1">{{ $estimateTotal === null ? 'N/A' : '₹ ' . number_format($estimateTotal, 2) }}</span>
                        </div>
                        @if (!is_null($diffValue))
                            <div class="chip {{ $diffValue > 0 ? 'badge-bad' : 'badge-ok' }}">
                                Diff (A−E):
                                <span class="kpi ms-1">{{ $diffValue > 0 ? '+' : '' }}₹
                                    {{ number_format($diffValue, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-items align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Flower</th>

                                    {{-- Estimate --}}
                                    <th class="text-center">Est. Unit</th>
                                    <th class="text-end">Est. Qty</th>
                                    <th class="text-end">Est. Price</th>
                                    <th class="text-end">Est. Subtotal</th>

                                    {{-- Actual --}}
                                    <th class="text-center">Actual Unit</th>
                                    <th class="text-end">Actual Qty</th>
                                    <th class="text-end">Actual Price</th>
                                    <th class="text-end">Actual Subtotal</th>

                                    <th class="text-end">Qty Δ</th>
                                    <th class="text-end">Value Δ (A−E)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pickup->flowerPickupItems as $item)
                                    @php
                                        $estUnitName =
                                            optional($item->estUnit)->unit_name ??
                                            ($unitMap[$item->est_unit_id] ?? null);
                                        $actUnitName =
                                            optional($item->unit)->unit_name ?? ($unitMap[$item->unit_id] ?? null);

                                        $estQty = (float) ($item->est_quantity ?? 0);
                                        $estPrice = isset($item->est_price) ? (float) $item->est_price : null; // if you saved it
                                        $estSubtotal = !is_null($estPrice) ? $estQty * $estPrice : null;

                                        $actQty = (float) ($item->quantity ?? 0);
                                        $actPrice = (float) ($item->price ?? 0);
                                        $actSubtotal = (float) ($item->item_total_price ?? $actPrice * $actQty);

                                        $qtyDiff = $actQty - $estQty;
                                        $valDiff = !is_null($estSubtotal) ? $actSubtotal - $estSubtotal : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->flower->name ?? '#' . $item->flower_id }}
                                            </div>
                                            <div class="small text-muted">
                                                Vendor: {{ $item->vendor->vendor_name ?? '—' }} |
                                                Rider: {{ $item->rider->rider_name ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Estimate --}}
                                        <td class="text-center">
                                            <span class="chip chip-unit">{{ $estUnitName ?? '—' }}</span>
                                        </td>
                                        <td class="text-end">{{ $estQty ? number_format($estQty, 2) : '—' }}</td>
                                        <td class="text-end">
                                            {{ !is_null($estPrice) ? '₹ ' . number_format($estPrice, 2) : '—' }}</td>
                                        <td class="text-end">
                                            {{ !is_null($estSubtotal) ? '₹ ' . number_format($estSubtotal, 2) : '—' }}</td>

                                        {{-- Actual --}}
                                        <td class="text-center">
                                            <span class="chip chip-unit">{{ $actUnitName ?? '—' }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($actQty, 2) }}</td>
                                        <td class="text-end">₹ {{ number_format($actPrice, 2) }}</td>
                                        <td class="text-end">₹ {{ number_format($actSubtotal, 2) }}</td>

                                        {{-- Diffs --}}
                                        <td class="text-end">
                                            @if ($estQty > 0 || $actQty > 0)
                                                <span
                                                    class="{{ $qtyDiff > 0 ? 'diff-neg' : ($qtyDiff < 0 ? 'diff-pos' : '') }}">
                                                    {{ $qtyDiff > 0 ? '+' : '' }}{{ number_format($qtyDiff, 2) }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if (!is_null($valDiff))
                                                <span
                                                    class="{{ $valDiff > 0 ? 'diff-neg' : ($valDiff < 0 ? 'diff-pos' : '') }}">
                                                    {{ $valDiff > 0 ? '+' : '' }}₹ {{ number_format($valDiff, 2) }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">No items.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="4" class="text-end">Totals:</th>
                                    <th class="text-end">
                                        {{ $estimateTotal === null ? '—' : '₹ ' . number_format($estimateTotal, 2) }}
                                    </th>
                                    <th colspan="3"></th>
                                    <th class="text-end">₹ {{ number_format($actualTotal, 2) }}</th>
                                    <th></th>
                                    <th class="text-end">
                                        @if (!is_null($diffValue))
                                            <span
                                                class="{{ $diffValue > 0 ? 'diff-neg' : ($diffValue < 0 ? 'diff-pos' : '') }}">
                                                {{ $diffValue > 0 ? '+' : '' }}₹ {{ number_format($diffValue, 2) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">No pickups found for the selected filters.</div>
        @endforelse

        <div class="d-flex justify-content-center">
            {{ $pickups->withQueryString()->links() }}
        </div>
    </div>
@endsection
