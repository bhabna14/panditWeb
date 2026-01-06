@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* ... keep all your existing CSS ... */

        /* NEW: clickable type cards */
        .type-card-link{
            display:flex;
            text-decoration:none !important;
            color:inherit !important;
            cursor:pointer;
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
        }
        .type-card-link:hover{
            transform: translateY(-1px);
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.10);
            border-color: rgba(37, 99, 235, .35);
        }
        .type-card-active{
            border-color: rgba(37, 99, 235, .55) !important;
            box-shadow: 0 16px 40px rgba(37, 99, 235, 0.14) !important;
        }
        .type-filter-pill{
            display:inline-flex;
            align-items:center;
            gap:.4rem;
            border:1px solid rgba(15,23,42,.12);
            background:#fff;
            border-radius:999px;
            padding:.25rem .6rem;
            font-size:.8rem;
            font-weight:800;
            color:#0f172a;
        }
        .type-filter-pill a{
            color:#0f172a;
            text-decoration:none;
            border-left:1px solid rgba(15,23,42,.12);
            padding-left:.55rem;
            margin-left:.25rem;
        }
        .type-filter-pill a:hover{ text-decoration: underline; }
    </style>
@endsection

@section('content')
<div class="container container-page py-4">

    @php
        $startLabel = $start ? \Carbon\Carbon::parse($start)->toFormattedDateString() : '—';
        $endLabel   = $end   ? \Carbon\Carbon::parse($end)->toFormattedDateString()   : '—';

        $typeParam  = $type ?? '';

        // Build links that preserve current filters but change type; also reset pagination
        $qAll = request()->query();

        $typeLink = function(string $typeVal) use ($qAll) {
            $merged = array_merge($qAll, ['type' => $typeVal]);
            unset($merged['page']);
            return route('admin.payments.index', $merged);
        };

        $clearTypeLink = function() use ($qAll) {
            $merged = $qAll;
            unset($merged['type'], $merged['page']);
            return route('admin.payments.index', $merged);
        };
    @endphp

    {{-- 1) OVERALL SUMMARY CARDS --}}
    <div class="section-head">
        <div>
            <p class="section-title"><i class="bi bi-graph-up"></i> Summary (Selected Range)</p>
            <p class="section-sub">Range: <strong>{{ $startLabel }}</strong> — <strong>{{ $endLabel }}</strong></p>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card kpi-slate">
            <div class="kpi-left">
                <div class="kpi-icon"><i class="bi bi-receipt"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Payments</div>
                    <div class="kpi-value">{{ number_format($stats->cnt ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="kpi-left">
                <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Total Collected</div>
                    <div class="kpi-value">₹{{ number_format($stats->sum_paid ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-note">Paid only</div>
        </div>

        <div class="kpi-card kpi-orange">
            <div class="kpi-left">
                <div class="kpi-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Pending Amount</div>
                    <div class="kpi-value">₹{{ number_format($stats->sum_pending ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-note">Pending only</div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-left">
                <div class="kpi-icon"><i class="bi bi-bar-chart"></i></div>
                <div class="kpi-meta">
                    <div class="kpi-label">Total (All)</div>
                    <div class="kpi-value">₹{{ number_format($stats->sum_all ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-note">Paid + Pending</div>
        </div>
    </div>

    {{-- 2) PAYMENT METHOD-WISE CARDS --}}
    <div class="section-head">
        <div>
            <p class="section-title"><i class="bi bi-wallet2"></i> Collected by Payment Method</p>
            <p class="section-sub">Shows paid collection per method (also displays pending for visibility).</p>
        </div>
    </div>

    <div class="mini-grid">
        @forelse ($methodStats as $m)
            @php
                $methodName = $m->method ?? 'Unknown';
                $methodStr  = strtolower((string)$methodName);

                $icon = match (true) {
                    str_contains($methodStr, 'upi')  => 'bi-qr-code-scan',
                    str_contains($methodStr, 'cash') => 'bi-cash-coin',
                    str_contains($methodStr, 'card') => 'bi-credit-card',
                    default                          => 'bi-question-circle',
                };
            @endphp

            <div class="mini-card">
                <div class="mini-top">
                    <h6 class="mini-title">
                        <i class="bi {{ $icon }}"></i> {{ $methodName }}
                    </h6>
                    <span class="mini-badge">
                        Paid: {{ number_format($m->paid_count ?? 0) }}
                    </span>
                </div>

                <p class="mini-amount">₹{{ number_format($m->collected ?? 0, 2) }}</p>

                <div class="mini-sub">
                    <span>Pending: ₹{{ number_format($m->pending ?? 0, 2) }}</span>
                    <span>({{ number_format($m->pending_count ?? 0) }})</span>
                </div>
            </div>
        @empty
            <div class="mini-card">
                <div class="text-muted">No method-wise data found for this range.</div>
            </div>
        @endforelse
    </div>

    {{-- 3) SUBSCRIPTION vs CUSTOMIZE CARDS (CLICK TO FILTER TABLE) --}}
    <div class="section-head">
        <div>
            <p class="section-title"><i class="bi bi-diagram-3"></i> Collected by Order Type</p>
            <p class="section-sub">
                Click a card to filter the detailed table below.
                @if (!empty($typeParam))
                    <span class="ms-2 type-filter-pill">
                        <i class="bi bi-funnel"></i>
                        Type: {{ ucfirst($typeParam) }}
                        <a href="{{ $clearTypeLink() }}" title="Clear type filter"><i class="bi bi-x-lg"></i></a>
                    </span>
                @endif
            </p>
        </div>
    </div>

    <div class="type-grid">
        <a href="{{ $typeLink('subscription') }}"
           class="type-card type-sub type-card-link {{ ($typeParam === 'subscription') ? 'type-card-active' : '' }}">
            <div class="type-left">
                <div class="type-icon"><i class="bi bi-arrow-repeat"></i></div>
                <div class="type-meta">
                    <h4>Subscription Orders</h4>
                    <p>
                        Paid: {{ number_format($typeStats->subscription_paid_count ?? 0) }}
                        | Pending: {{ number_format($typeStats->subscription_pending_count ?? 0) }}
                    </p>
                </div>
            </div>
            <div class="type-right">
                <div class="big">₹{{ number_format($typeStats->subscription_collected ?? 0, 2) }}</div>
                <div class="small">Pending ₹{{ number_format($typeStats->subscription_pending ?? 0, 2) }}</div>
            </div>
        </a>

        <a href="{{ $typeLink('customize') }}"
           class="type-card type-cus type-card-link {{ ($typeParam === 'customize') ? 'type-card-active' : '' }}">
            <div class="type-left">
                <div class="type-icon"><i class="bi bi-sliders"></i></div>
                <div class="type-meta">
                    <h4>Customize Orders</h4>
                    <p>
                        Paid: {{ number_format($typeStats->customize_paid_count ?? 0) }}
                        | Pending: {{ number_format($typeStats->customize_pending_count ?? 0) }}
                    </p>
                </div>
            </div>
            <div class="type-right">
                <div class="big">₹{{ number_format($typeStats->customize_collected ?? 0, 2) }}</div>
                <div class="small">Pending ₹{{ number_format($typeStats->customize_pending ?? 0, 2) }}</div>
            </div>
        </a>
    </div>

    {{-- Toolbar (filters + presets) --}}
    <form class="toolbar" method="get" action="{{ route('admin.payments.index') }}">
        @php
            $p = $preset ?? '';
            $qAll = request()->query();
            $makeLink = function ($name) use ($qAll) {
                $merged = array_merge($qAll, [
                    'preset'     => $name,
                    'start_date' => null,
                    'end_date'   => null,
                ]);
                unset($merged['page']);
                return route('admin.payments.index', $merged);
            };
        @endphp

        {{-- NEW: keep type filter when applying --}}
        <input type="hidden" name="type" value="{{ $typeParam }}">

        <div class="toolbar-left">
            <div class="toolbar-block">
                <span class="toolbar-label">Status</span>
                <select name="status" class="toolbar-select">
                    <option value="">Any</option>
                    <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="toolbar-block">
                <span class="toolbar-label">Method</span>
                <select name="payment_method" class="toolbar-select">
                    <option value="">Any</option>
                    @foreach ($methods as $m)
                        <option value="{{ $m }}" {{ ($method ?? '') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="toolbar-block">
                <span class="toolbar-label">Start</span>
                <input type="date" name="start_date" class="toolbar-input" value="{{ $start }}">
            </div>

            <div class="toolbar-block">
                <span class="toolbar-label">End</span>
                <input type="date" name="end_date" class="toolbar-input" value="{{ $end }}">
            </div>
        </div>

        <div class="toolbar-right">
            <a href="{{ $makeLink('today') }}" class="btn-chip {{ $p === 'today' ? 'preset-active' : '' }}">
                <i class="bi bi-sun"></i><span>Today</span>
            </a>
            <a href="{{ $makeLink('yesterday') }}" class="btn-chip {{ $p === 'yesterday' ? 'preset-active' : '' }}">
                <i class="bi bi-chevron-left"></i><span>Yesterday</span>
            </a>
            <a href="{{ $makeLink('tomorrow') }}" class="btn-chip {{ $p === 'tomorrow' ? 'preset-active' : '' }}">
                <i class="bi bi-chevron-right"></i><span>Tomorrow</span>
            </a>
            <a href="{{ $makeLink('this_week') }}" class="btn-chip {{ in_array($p, ['this_week', 'week', '']) ? 'preset-active' : '' }}">
                <i class="bi bi-calendar-week"></i><span>Week</span>
            </a>
            <a href="{{ $makeLink('this_month') }}" class="btn-chip {{ in_array($p, ['this_month', 'month']) ? 'preset-active' : '' }}">
                <i class="bi bi-calendar-month"></i><span>Month</span>
            </a>

            <button class="btn-chip apply-btn" type="submit">
                <i class="bi bi-filter"></i><span>Apply</span>
            </button>
            <a href="{{ route('admin.payments.index') }}" class="btn-chip reset-btn">
                <i class="bi bi-bootstrap-reboot"></i><span>Reset</span>
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="workbook">
        <div class="workbook-head">
            <div>
                <div class="workbook-title">Payments — Detailed List</div>
                <div class="workbook-sub">
                    Showing payments with type, duration, method, and status.
                    @if (!empty($typeParam))
                        <span class="ms-2"><strong>Type Filter:</strong> {{ ucfirst($typeParam) }}</span>
                    @endif
                </div>
            </div>
            <div class="workbook-sub">
                Range: <strong>{{ $startLabel }}</strong> — <strong>{{ $endLabel }}</strong>
            </div>
        </div>

        <div class="workbook-body">
            <div class="table-responsive">
                <table class="table table-payments table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="cell-datetime">Date/Time</th>
                            <th class="cell-user">User</th>
                            <th style="min-width:220px">Type</th>
                            <th style="min-width:200px">Duration</th>
                            <th class="cell-method">Method</th>
                            <th class="cell-amount text-end">Amount</th>
                            <th class="cell-status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $p)
                            @php
                                $dt = \Carbon\Carbon::parse($p->created_at);
                                $methodStr = strtolower((string)($p->payment_method ?? ''));
                                $badgeMethod = match (true) {
                                    str_contains($methodStr, 'upi')  => 'badge-upi',
                                    str_contains($methodStr, 'cash') => 'badge-cash',
                                    str_contains($methodStr, 'card') => 'badge-card',
                                    default                          => 'badge-soft',
                                };
                                $badgeStatus = $p->payment_status === 'paid' ? 'badge-paid' : 'badge-pending';

                                $startAt = $p->start_date ? \Carbon\Carbon::parse($p->start_date) : null;
                                $endAt   = $p->end_date   ? \Carbon\Carbon::parse($p->end_date)   : null;
                                $days    = ($startAt && $endAt) ? ($startAt->diffInDays($endAt) + 1) : null;
                            @endphp

                            <tr>
                                <td class="cell-datetime">
                                    <div class="fw-semibold">{{ $dt->format('d M Y') }}</div>
                                    <div class="text-muted small"><i class="bi bi-clock"></i> {{ $dt->format('h:i A') }}</div>
                                </td>

                                <td class="cell-user">
                                    <div class="fw-semibold">
                                        <i class="bi bi-person-circle"></i> {{ $p->user_name ?? '—' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-telephone"></i> {{ $p->user_mobile ?? '' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $p->product_category ?? '—' }}
                                        @if ($p->product_name)
                                            <span class="text-muted small">({{ $p->product_name }})</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        @if ($p->subscription_id)
                                            Subscription #{{ $p->subscription_id }}
                                        @else
                                            Customize / Non-subscription
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if ($startAt && $endAt)
                                        {{ $startAt->format('d M Y') }} — {{ $endAt->format('d M Y') }}
                                        <span class="text-muted small">({{ $days }}d)</span>
                                    @else
                                        —
                                    @endif

                                    @if (!empty($p->product_duration))
                                        <div class="text-muted small">Plan: {{ $p->product_duration }} days</div>
                                    @endif
                                </td>

                                <td class="cell-method">
                                    <span class="badge-method {{ $badgeMethod }}">
                                        {{ $p->payment_method ?? '—' }}
                                    </span>
                                </td>

                                <td class="cell-amount money">
                                    ₹{{ number_format($p->paid_amount ?? 0, 2) }}
                                </td>

                                <td class="cell-status">
                                    <span class="badge-status {{ $badgeStatus }}">
                                        {{ ucfirst($p->payment_status ?? '—') }}
                                    </span>
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
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="5" class="text-end">Page Total:</th>
                                <th class="text-end money">
                                    ₹{{ number_format($payments->sum('paid_amount'), 2) }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3">
                <div class="pagination-meta">
                    Showing <strong>{{ $payments->firstItem() ?? 0 }}</strong>–<strong>{{ $payments->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $payments->total() }}</strong>
                </div>
                {{ $payments->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
