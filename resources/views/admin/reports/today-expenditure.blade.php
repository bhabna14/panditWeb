@extends('admin.layouts.apps')

@section('styles')
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e9ecef;
            --soft: #f8fafc;
            --pri: #0ea5e9;
        }

        .mini-stat {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            background: #fff;
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        .table thead th {
            white-space: nowrap;
        }

        .badge-soft {
            background: #eef2ff;
            color: #3730a3;
            border-radius: 999px;
            padding: .4rem .65rem;
        }

        .subtle {
            color: var(--muted);
            font-size: .92rem;
        }

        .card-collapsible {
            background: #fafafa;
            border: 1px dashed #e5e7eb;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: .35rem .6rem;
            color: #0f172a;
        }

        .sticky-filter {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #fff;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .thead-soft th {
            background: #f8fafc;
            border-bottom: 1px solid var(--line) !important;
        }

        .row-divider {
            border-top: 1px dashed #e5e7eb;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Expenditure Details</h4>
                <div class="subtle">
                    Date:
                    <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                    <span class="ms-2 chip">{{ $totalPickupsCount }} pickups</span>
                    <span class="ms-1 chip">{{ $totalItemsCount }} items</span>
                </div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        {{-- Filters --}}
        <div class="sticky-filter">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vendor ID</label>
                    <input type="text" name="vendor_id" class="form-control" value="{{ $vendorId }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rider ID</label>
                    <input type="text" name="rider_id" class="form-control" value="{{ $riderId }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" value="{{ $paymentMethod }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment Status</label>
                    <input type="text" name="payment_status" class="form-control" value="{{ $paymentStatus }}">
                </div>
                <div class="col-md-12 d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-funnel"></i> Apply Filters</button>
                    <a href="{{ route('flower.expenditure.today') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>

        {{-- Top cards --}}
        <div class="row g-3 my-3">
            <div class="col-md-4">
                <div class="mini-stat">
                    <div class="subtle">Total Expenditure</div>
                    <div class="h4 money mb-0">₹{{ number_format($totalForDay, 2) }}</div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="mini-stat">
                    <div class="subtle mb-2">By Vendor</div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($byVendor as $row)
                            <span class="badge-soft">
                                {{ optional($row->vendor)->vendor_name ?? 'Vendor ' . $row->vendor_id }}:
                                ₹{{ number_format($row->total, 2) }}
                            </span>
                        @empty
                            <span class="text-muted">No data</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="thead-soft">
                            <tr>
                                <th>#</th>
                                <th>Pickup Date/Time</th>
                                <th>Vendor</th>
                                <th>Rider</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-end">Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pickups as $i => $p)
                                <tr>
                                    <td>{{ $pickups->firstItem() + $i }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($p->pickup_date)->format('d M Y') }}
                                        <div class="subtle">
                                            {{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('h:i A') : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ optional($p->vendor)->vendor_name ?? '—' }}</div>
                                        <div class="subtle">{{ $p->vendor_id }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ optional($p->rider)->rider_name ?? '—' }}</div>
                                        <div class="subtle">{{ $p->rider_id }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $p->payment_method ?: '—' }}</div>
                                        <div class="subtle">Paid By: {{ $p->paid_by ?: '—' }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $p->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $p->payment_status ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="text-end money">₹{{ number_format($p->total_price, 2) }}</td>
                                </tr>

                                {{-- Items for this pickup --}}
                                <tr class="row-divider">
                                    <td></td>
                                    <td colspan="6">
                                        <div class="card-collapsible">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="subtle">Items ({{ $p->flowerPickupItems->count() }})</div>
                                                @php
                                                    $itemsSubtotal = $p->flowerPickupItems->sum(function ($it) {
                                                        return (float) $it->price;
                                                    });
                                                @endphp
                                                <div class="subtle">Items Total:
                                                    <strong>₹{{ number_format($itemsSubtotal, 2) }}</strong></div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th style="min-width: 220px;">Item</th>
                                                            <th>Qty</th>
                                                            <th>Unit</th>
                                                            <th class="text-end">Price (₹)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($p->flowerPickupItems as $it)
                                                            <tr>
                                                                {{-- Item name from related FlowerProduct; fallback to ID --}}
                                                                <td>{{ optional($it->flower)->name ?? 'Flower #' . $it->flower_id }}
                                                                </td>
                                                                {{-- Quantity --}}
                                                                <td>{{ $it->quantity ?? '—' }}</td>
                                                                {{-- Unit name from related PoojaUnit; fallback to N/A (from withDefault) --}}
                                                                <td>{{ optional($it->unit)->unit_name }}</td>
                                                                {{-- Price --}}
                                                                <td class="text-end">
                                                                    {{ number_format((float) $it->price, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No pickups found for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if ($pickups->hasPages())
                            <tfoot>
                                <tr>
                                    <td colspan="7">
                                        {{ $pickups->links() }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
