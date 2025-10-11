@extends('admin.layouts.apps')

@section('styles')
<style>
    .mini-stat { border:1px solid #e9ecef; border-radius: 12px; padding:14px; background:#fff; }
    .money { font-variant-numeric: tabular-nums; }
    .table thead th { white-space: nowrap; }
    .badge-soft { background:#f1f5f9; color:#0f172a; border-radius:999px; padding:.35rem .6rem; }
    .subtle { color:#64748b; font-size:.9rem; }
    .card-collapsible { background:#fafafa; border:1px dashed #e5e7eb; border-radius:10px; padding:10px 12px; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Header / Breadcrumbs --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-1">Expenditure Details</h4>
            <div class="subtle">Date: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong></div>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    {{-- Filters --}}
    <form method="get" class="row g-2 align-items-end mb-3">
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

    {{-- Top cards --}}
    <div class="row g-3 mb-3">
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
                        <span class="badge badge-soft">
                            {{ optional($row->vendor)->vendor_name ?? ('Vendor '.$row->vendor_id) }}:
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
                    <thead>
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
                                    <div class="subtle">{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('h:i A') : '' }}</div>
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
                                    <span class="badge {{ $p->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $p->payment_status ?: '—' }}
                                    </span>
                                </td>
                                <td class="text-end money">₹{{ number_format($p->total_price, 2) }}</td>
                            </tr>
                            @if($p->flowerPickupItems && $p->flowerPickupItems->count())
                                <tr>
                                    <td></td>
                                    <td colspan="6">
                                        <div class="card-collapsible">
                                            <div class="subtle mb-2">Items</div>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Qty</th>
                                                            <th>Unit</th>
                                                            <th class="text-end">Price (₹)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($p->flowerPickupItems as $it)
                                                            <tr>
                                                                <td>{{ $it->item_name ?? $it->product_name ?? '—' }}</td>
                                                                <td>{{ $it->quantity ?? '—' }}</td>
                                                                <td>{{ $it->unit ?? $it->unit_name ?? '—' }}</td>
                                                                <td class="text-end">{{ number_format($it->price ?? 0, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No pickups found for this date.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($pickups->hasPages())
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
