@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --surface: #ffffff;
            --bg: #f6f7fb;
        }

        body {
            background: var(--bg);
            color: var(--ink);
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        .card-smooth {
            border-radius: 16px;
            border: 1px solid #e9ecef;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: saturate(1.1) blur(4px);
            background: rgba(246, 247, 251, .9);
            border-bottom: 1px solid #e9ecef;
        }

        .segmented {
            display: inline-flex;
            border: 1px solid #ced4da;
            border-radius: .5rem;
            overflow: hidden;
            background: #fff;
        }

        .segmented a {
            padding: .4rem .75rem;
            text-decoration: none;
            color: #0d6efd;
            border-right: 1px solid #ced4da;
        }

        .segmented a:last-child {
            border-right: 0;
        }

        .segmented a.active {
            background: #0d6efd;
            color: #fff;
        }

        .badge-soft {
            background: #eef2ff;
            color: #3730a3;
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        .badge-pending {
            background: #fff7ed;
            color: #9a3412;
        }

        .badge-upi {
            background: #ecfeff;
            color: #155e75;
        }

        .badge-cash {
            background: #fef2f2;
            color: #7f1d1d;
        }

        .badge-card {
            background: #f0f9ff;
            color: #1e3a8a;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .pill {
            border-radius: 999px;
            padding: .25rem .6rem;
            border: 1px solid #e2e8f0;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .sticky-summary {
            position: sticky;
            bottom: 0;
            background: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">

        {{-- ===================== --}}
        {{-- FILTER / TOOLBAR     --}}
        {{-- ===================== --}}
        <div class="toolbar mb-3">
            <form class="card card-smooth shadow-sm" method="get" action="{{ route('admin.payments.index') }}">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label mb-1">User</label>
                            <select name="user_id" class="form-select">
                                <option value="">All users</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->userid }}"
                                        {{ ($userId ?? '') == $u->userid ? 'selected' : '' }}>
                                        {{ $u->name }} — {{ $u->mobile_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Any</option>
                                <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label mb-1">Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Any</option>
                                @foreach ($methods as $m)
                                    <option value="{{ $m }}" {{ ($method ?? '') === $m ? 'selected' : '' }}>
                                        {{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label mb-1">Start</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label mb-1">End</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                        </div>
                    </div>

                    <div class="row g-2 align-items-center mt-3">
                        <div class="col-12 col-lg-8">
                            <div class="segmented">
                                @php
                                    $p = $preset ?? '';
                                    $link = fn($name) => route(
                                        'admin.payments.index',
                                        array_merge(request()->query(), [
                                            'preset' => $name,
                                            'start_date' => null,
                                            'end_date' => null,
                                        ]),
                                    );
                                @endphp
                                <a href="{{ $link('today') }}" class="{{ $p === 'today' ? 'active' : '' }}">Today</a>
                                <a href="{{ $link('yesterday') }}"
                                    class="{{ $p === 'yesterday' ? 'active' : '' }}">Yesterday</a>
                                <a href="{{ $link('tomorrow') }}"
                                    class="{{ $p === 'tomorrow' ? 'active' : '' }}">Tomorrow</a>
                                <a href="{{ $link('this_week') }}"
                                    class="{{ in_array($p, ['this_week', 'week', '']) ? 'active' : '' }}">This Week</a>
                                <a href="{{ $link('this_month') }}"
                                    class="{{ in_array($p, ['this_month', 'month']) ? 'active' : '' }}">This Month</a>
                                <a href="{{ route('admin.payments.index') }}" class="">Reset</a>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 d-flex gap-2">
                            <input type="text" name="q" class="form-control search-input"
                                placeholder="Search order id / payment id / user..." value="{{ $search }}">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===================== --}}
        {{-- SUMMARY STRIP        --}}
        {{-- ===================== --}}
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <div class="card card-smooth shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Payments</div>
                        <div class="fs-4 fw-bold">{{ number_format($stats->cnt ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-smooth shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Total Collected</div>
                        <div class="fs-4 fw-bold money">₹{{ number_format($stats->sum_paid ?? 0, 2) }}</div>
                        <span class="badge badge-paid">Paid</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-smooth shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Pending Amount</div>
                        <div class="fs-4 fw-bold money">₹{{ number_format($stats->sum_pending ?? 0, 2) }}</div>
                        <span class="badge badge-pending">Pending</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-smooth shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Total (All)</div>
                        <div class="fs-4 fw-bold money">₹{{ number_format($stats->sum_all ?? 0, 2) }}</div>
                        <span class="badge badge-soft">Filtered range</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- TABLE                --}}
        {{-- ===================== --}}
        <div class="card card-smooth shadow-sm mt-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Payments</strong>
                <span class="text-muted small">
                    From {{ \Carbon\Carbon::parse($start)->toFormattedDateString() }}
                    to {{ \Carbon\Carbon::parse($end)->toFormattedDateString() }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%">Date/Time</th>
                            <th style="width: 10%">Order ID</th>
                            <th style="width: 12%">Payment ID</th>
                            <th style="width: 22%">User</th>
                            <th style="width: 10%" class="text-center">Method</th>
                            <th style="width: 10%" class="text-end">Amount</th>
                            <th style="width: 10%" class="text-center">Status</th>
                            <th style="width: 14%">Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $p)
                            @php
                                $dt = \Carbon\Carbon::parse($p->created_at);
                                $method = strtolower((string) ($p->payment_method ?? ''));
                                $badgeMethod = match (true) {
                                    str_contains($method, 'upi') => 'badge-upi',
                                    str_contains($method, 'cash') => 'badge-cash',
                                    str_contains($method, 'card') => 'badge-card',
                                    default => 'badge-soft',
                                };
                                $badgeStatus = $p->payment_status === 'paid' ? 'badge-paid' : 'badge-pending';
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $dt->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ $dt->format('h:i A') }}</div>
                                </td>
                                <td><span class="pill">{{ $p->order_id }}</span></td>
                                <td><span class="pill">{{ $p->payment_id }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $p->user_name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $p->user_mobile ?? '' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeMethod }}">{{ $p->payment_method ?? '—' }}</span>
                                </td>
                                <td class="text-end money">₹{{ number_format($p->paid_amount ?? 0, 2) }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge {{ $badgeStatus }}">{{ ucfirst($p->payment_status ?? '—') }}</span>
                                </td>
                                <td>{{ $p->received_by ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No payments found for the selected
                                    filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($payments->count())
                        <tfoot class="sticky-summary">
                            <tr class="table-light">
                                <th colspan="5" class="text-end">Page Total:</th>
                                <th class="text-end money">
                                    ₹{{ number_format($payments->sum('paid_amount'), 2) }}
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
