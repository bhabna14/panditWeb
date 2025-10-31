@extends('admin.layouts.apps')

@section('styles')
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    :root{
        --ink:#0f172a; --muted:#64748b; --surface:#ffffff; --bg:#f6f7fb;
        --primary:#0d6efd; --primary-50:#eff6ff; --primary-100:#dbeafe;
        --success-50:#ecfdf5; --success-600:#16a34a;
        --warning-50:#fff7ed; --warning-600:#d97706;
        --info-50:#eef2ff; --info-600:#3730a3;
        --danger-50:#fef2f2; --danger-600:#b91c1c;
        --card-br:#e9ecef;
        --zebra:#fbfcff;
    }
    body{background:var(--bg); color:var(--ink);}
    .money{font-variant-numeric: tabular-nums;}
    .card-smooth{border-radius:16px; border:1px solid var(--card-br);}
    .toolbar{position:sticky; top:0; z-index:100; backdrop-filter:saturate(1.1) blur(4px); background:rgba(246,247,251,.9); border-bottom:1px solid var(--card-br);}
    .segmented{display:inline-flex; border:1px solid #ced4da; border-radius:.6rem; overflow:hidden; background:#fff}
    .segmented a{padding:.45rem .8rem; text-decoration:none; color:var(--primary); border-right:1px solid #ced4da; font-weight:600}
    .segmented a:last-child{border-right:0}
    .segmented a.active{background:var(--primary); color:#fff}
    .pill{border-radius:999px; padding:.25rem .6rem; border:1px solid #e2e8f0; background:#fff}
    .search-input::placeholder{color:#94a3b8}
    .table>:not(caption)>*>*{vertical-align: middle;}
    .table thead th{white-space:nowrap}
    .table tbody tr:nth-child(odd){background:var(--zebra)}
    .table-hover tbody tr:hover{background:#f3f6ff}
    .sticky-summary{position:sticky; bottom:0; background:#fff}
    .sticky-header thead th{position:sticky; top:0; background:#fff; z-index:5}
    .td-tight{padding-top:.55rem!important; padding-bottom:.55rem!important;}
    .cell-datetime{min-width:140px}
    .cell-user{min-width:220px}
    .cell-method{min-width:120px; text-align:center}
    .cell-amount{min-width:120px; text-align:right}
    .cell-status{min-width:120px; text-align:center}

    /* Badges */
    .badge-paid{background:linear-gradient(0deg, var(--success-50), var(--success-50)); color:#166534; border:1px solid #bbf7d0; font-weight:600}
    .badge-pending{background:linear-gradient(0deg, var(--warning-50), var(--warning-50)); color:#9a3412; border:1px solid #fed7aa; font-weight:600}
    .badge-soft{background:linear-gradient(0deg, var(--info-50), var(--info-50)); color:var(--info-600); border:1px solid #c7d2fe; font-weight:600}
    .badge-upi{background:linear-gradient(0deg,#ecfeff,#ecfeff); color:#155e75; border:1px solid #bae6fd; font-weight:600}
    .badge-cash{background:linear-gradient(0deg,var(--danger-50),var(--danger-50)); color:#7f1d1d; border:1px solid #fecaca; font-weight:600}
    .badge-card{background:linear-gradient(0deg,#f0f9ff,#f0f9ff); color:#1e3a8a; border:1px solid #bae6fd; font-weight:600}

    /* Mini stats */
    .mini .label{color:var(--muted); font-size:.9rem}
    .mini .value{font-size:1.45rem; font-weight:800}
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
                                <option value="{{ $u->userid }}" {{ ($userId ?? '') == $u->userid ? 'selected' : '' }}>
                                    {{ $u->name }} — {{ $u->mobile_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Any</option>
                            <option value="paid" {{ ($status ?? '')==='paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ ($status ?? '')==='pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label mb-1">Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Any</option>
                            @foreach ($methods as $m)
                                <option value="{{ $m }}" {{ ($method ?? '')===$m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3 col-lg-2">
                        <label class="form-label mb-1">Start</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <label class="form-label mb-1">End</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-3">
                    <div class="col-12 col-lg-8">
                        <div class="segmented">
                            @php
                                $p = $preset ?? '';
                                $link = fn($name) => route('admin.payments.index', array_merge(request()->query(), ['preset'=>$name, 'start_date'=>null, 'end_date'=>null]));
                            @endphp
                            <a href="{{ $link('today') }}" class="{{ $p==='today' ? 'active' : '' }}"><i class="bi bi-sun"></i> Today</a>
                            <a href="{{ $link('yesterday') }}" class="{{ $p==='yesterday' ? 'active' : '' }}"><i class="bi bi-chevron-left"></i> Yesterday</a>
                            <a href="{{ $link('tomorrow') }}" class="{{ $p==='tomorrow' ? 'active' : '' }}"><i class="bi bi-chevron-right"></i> Tomorrow</a>
                            <a href="{{ $link('this_week') }}" class="{{ in_array($p,['this_week','week','']) ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> This Week</a>
                            <a href="{{ $link('this_month') }}" class="{{ in_array($p,['this_month','month']) ? 'active' : '' }}"><i class="bi bi-calendar-month"></i> This Month</a>
                            <a href="{{ route('admin.payments.index') }}" class=""><i class="bi bi-bootstrap-reboot"></i> Reset</a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 d-flex gap-2">
                        <input type="text" name="q" class="form-control search-input" placeholder="Search order id / payment id / user..." value="{{ $search }}">
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
            <div class="card card-smooth shadow-sm mini">
                <div class="card-body">
                    <div class="label">Payments</div>
                    <div class="value">{{ number_format($stats->cnt ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card card-smooth shadow-sm mini">
                <div class="card-body">
                    <div class="label">Total Collected</div>
                    <div class="value money">₹{{ number_format($stats->sum_paid ?? 0, 2) }}</div>
                    <span class="badge badge-paid"><i class="bi bi-check2-circle"></i> Paid</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card card-smooth shadow-sm mini">
                <div class="card-body">
                    <div class="label">Pending Amount</div>
                    <div class="value money">₹{{ number_format($stats->sum_pending ?? 0, 2) }}</div>
                    <span class="badge badge-pending"><i class="bi bi-hourglass-split"></i> Pending</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card card-smooth shadow-sm mini">
                <div class="card-body">
                    <div class="label">Total (All)</div>
                    <div class="value money">₹{{ number_format($stats->sum_all ?? 0, 2) }}</div>
                    <span class="badge badge-soft"><i class="bi bi-funnel"></i> Filtered range</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- TABLE                --}}
    {{-- ===================== --}}
    <div class="card card-smooth shadow-sm mt-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-cash-coin"></i> Payments</strong>
        <span class="text-muted small">
            From {{ \Carbon\Carbon::parse($start)->toFormattedDateString() }}
            to {{ \Carbon\Carbon::parse($end)->toFormattedDateString() }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 sticky-header">
            <thead class="table-light">
                <tr>
                    <th class="cell-datetime">Date/Time</th>
                    <th class="cell-user">User</th>
                    <th style="min-width:220px">Type</th>       {{-- NEW --}}
                    <th style="min-width:200px">Duration</th>   {{-- NEW --}}
                    <th class="cell-method text-center">Method</th>
                    <th class="cell-amount text-end">Amount</th>
                    <th class="cell-status text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $p)
                    @php
                        $dt = \Carbon\Carbon::parse($p->created_at);
                        $method = strtolower((string)($p->payment_method ?? ''));
                        $badgeMethod = match(true){
                            str_contains($method,'upi')  => 'badge-upi',
                            str_contains($method,'cash') => 'badge-cash',
                            str_contains($method,'card') => 'badge-card',
                            default => 'badge-soft',
                        };
                        $badgeStatus = $p->payment_status === 'paid' ? 'badge-paid' : 'badge-pending';

                        // Duration (subscription window)
                        $startAt = $p->start_date ? \Carbon\Carbon::parse($p->start_date) : null;
                        $endAt   = $p->end_date   ? \Carbon\Carbon::parse($p->end_date)   : null;
                        $days    = ($startAt && $endAt) ? ($startAt->diffInDays($endAt) + 1) : null;
                    @endphp
                    <tr class="td-tight">
                        <td class="cell-datetime">
                            <div class="fw-semibold">{{ $dt->format('d M Y') }}</div>
                            <div class="text-muted small"><i class="bi bi-clock"></i> {{ $dt->format('h:i A') }}</div>
                        </td>

                        <td class="cell-user">
                            <div class="fw-semibold"><i class="bi bi-person-circle"></i> {{ $p->user_name ?? '—' }}</div>
                            <div class="text-muted small"><i class="bi bi-telephone"></i> {{ $p->user_mobile ?? '' }}</div>
                        </td>

                        {{-- NEW: Subscription Type (Category + Product name + Sub id) --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $p->product_category ?? '—' }}
                                @if ($p->product_name)
                                    <span class="text-muted small">({{ $p->product_name }})</span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                @if($p->subscription_id)
                                    Sub #{{ $p->subscription_id }}
                                @else
                                    —
                                @endif
                            </div>
                        </td>

                        {{-- NEW: Subscription Duration --}}
                        <td>
                            @if ($startAt && $endAt)
                                {{ $startAt->format('d M Y') }} — {{ $endAt->format('d M Y') }}
                                <span class="text-muted small">({{ $days }}d)</span>
                            @else
                                —
                            @endif

                            @if(!empty($p->product_duration))
                                <div class="text-muted small">Plan: {{ $p->product_duration }} days</div>
                            @endif
                        </td>

                        <td class="cell-method">
                            <span class="badge {{ $badgeMethod }}">{{ $p->payment_method ?? '—' }}</span>
                        </td>
                        <td class="cell-amount money">₹{{ number_format($p->paid_amount ?? 0, 2) }}</td>
                        <td class="cell-status">
                            <span class="badge {{ $badgeStatus }}">{{ ucfirst($p->payment_status ?? '—') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inboxes"></i> No payments found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if ($payments->count())
            <tfoot class="sticky-summary">
                <tr class="table-light">
                    <th colspan="5" class="text-end">Page Total:</th>
                    <th class="text-end money">₹{{ number_format($payments->sum('paid_amount'), 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing <strong>{{ $payments->firstItem() ?? 0 }}</strong>–<strong>{{ $payments->lastItem() ?? 0 }}</strong> of <strong>{{ $payments->total() }}</strong>
        </div>
        {{ $payments->links() }}
    </div>
</div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
