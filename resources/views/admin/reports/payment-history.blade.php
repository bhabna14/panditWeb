@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Poppins (page) + Nunito Sans (table) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            /* Core palette (similar to your other first-page designs) */
            --brand-blue: #e0f2fe;
            --brand-blue-edge: #bfdbfe;
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;
            --chip-slate: #e5e7eb;
            --chip-slate-text: #111827;

            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #6b7280;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow-sm: 0 4px 12px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius-lg: 16px;

            --accent: #2563eb;
            --accent-strong: #1d4ed8;
            --accent-soft: #eff6ff;
            --accent-border: #bfdbfe;

            --success-soft: #ecfdf5;
            --success-fg: #166534;
            --warning-soft: #fff7ed;
            --warning-fg: #9a3412;
            --info-soft: #eef2ff;
            --info-fg: #3730a3;
            --danger-soft: #fef2f2;
            --danger-fg: #b91c1c;
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

        /* Page header */
        .page-header-title {
            font-weight: 600;
            color: #0f172a;
        }

        .page-header-sub {
            font-size: .86rem;
            color: var(--muted);
        }

        /* Summary band */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 18px;
            padding: .9rem 1.2rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }

        .band h3 {
            margin: 0;
            font-size: .98rem;
            font-weight: 600;
            color: var(--header-text);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band h3 span.label {
            font-size: .75rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.08);
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .09em;
        }

        .band-sub {
            font-size: .84rem;
            color: var(--muted);
        }

        .band-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .band-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .band-chip span.icon {
            font-size: .9rem;
        }

        .band-chip.green {
            background: var(--chip-green);
            color: var(--chip-green-text);
            border-color: #c9f0d6;
        }

        .band-chip.orange {
            background: var(--chip-orange);
            color: var(--chip-orange-text);
            border-color: #ffd9b3;
        }

        .band-chip.blue {
            background: var(--chip-blue);
            color: var(--chip-blue-text);
            border-color: #bae6fd;
        }

        .band-chip.slate {
            background: var(--chip-slate);
            color: var(--chip-slate-text);
            border-color: #d1d5db;
        }

        .mono {
            font-variant-numeric: tabular-nums;
        }

        /* Toolbar */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius-lg);
            padding: .85rem 1rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.2fr);
            align-items: center;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.1rem;
        }

        .toolbar-left {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
        }

        .toolbar-block {
            display: flex;
            flex-direction: column;
            gap: .25rem;
            min-width: 0;
        }

        .toolbar-label {
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
        }

        .toolbar-select,
        .toolbar-input {
            border-radius: 999px;
            border: 1px solid var(--ring);
            padding: .45rem .85rem;
            font-size: .85rem;
            font-weight: 500;
        }

        .toolbar-select:focus,
        .toolbar-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.22);
        }

        .toolbar-right {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            justify-content: flex-end;
            align-items: center;
        }

        .btn-chip {
            border-radius: 999px;
            border: 1px solid var(--ring);
            background: #fff;
            color: #0f172a;
            font-weight: 500;
            font-size: .8rem;
            padding: .4rem .9rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            text-decoration: none;
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-chip i {
            font-size: .9rem;
        }

        .btn-chip:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
            color: #0f172a;
            text-decoration: none;
        }

        .btn-chip.preset-active {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border-color: #020617;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.preset-active i {
            color: #e5e7eb;
        }

        .btn-chip.apply-btn {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.reset-btn {
            border-style: dashed;
        }

        .search-input {
            border-radius: 999px;
            border: 1px solid var(--ring);
            padding-left: 2.2rem;
            font-size: .85rem;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
        }

        .search-wrapper i {
            position: absolute;
            left: .7rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: .9rem;
        }

        /* Workbook wrapper */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }

        .workbook-head {
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .35rem;
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
            content: '💳';
            font-size: 1.1rem;
        }

        .workbook-sub {
            font-size: .84rem;
            color: var(--muted);
        }

        .workbook-body {
            padding: 1rem 1.1rem 1.1rem;
        }

        /* Table */
        .table-payments {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--table-border);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            font-size: .88rem;
        }

        .table-payments thead th {
            position: sticky;
            top: 0;
            z-index: 3;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .06em;
            padding: .55rem .7rem;
            font-weight: 600;
            border-bottom: 0;
            white-space: nowrap;
        }

        .table-payments tbody td {
            border-top: 1px solid var(--table-border);
            padding: .55rem .7rem;
            vertical-align: middle;
        }

        .table-payments tbody tr:nth-child(odd) td {
            background: var(--table-zebra);
        }

        .table-payments tbody tr:hover td {
            background: var(--table-hover);
        }

        .cell-datetime {
            min-width: 140px;
        }

        .cell-user {
            min-width: 220px;
        }

        .cell-method {
            min-width: 110px;
            text-align: center;
        }

        .cell-amount {
            min-width: 120px;
            text-align: right;
        }

        .cell-status {
            min-width: 110px;
            text-align: center;
        }

        .td-tight {
            padding-top: .45rem !important;
            padding-bottom: .45rem !important;
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        /* Method badges */
        .badge-method {
            font-weight: 600;
            border-radius: 999px;
            padding: .25rem .6rem;
            font-size: .78rem;
            border: 1px solid transparent;
        }

        .badge-upi {
            background: #ecfeff;
            color: #155e75;
            border-color: #bae6fd;
        }

        .badge-cash {
            background: var(--danger-soft);
            color: #7f1d1d;
            border-color: #fecaca;
        }

        .badge-card {
            background: #f0f9ff;
            color: #1e3a8a;
            border-color: #bae6fd;
        }

        .badge-soft {
            background: var(--info-soft);
            color: var(--info-fg);
            border-color: #c7d2fe;
        }

        /* Status badges */
        .badge-status {
            font-weight: 600;
            border-radius: 999px;
            padding: .25rem .7rem;
            font-size: .78rem;
            border: 1px solid transparent;
        }

        .badge-paid {
            background: var(--success-soft);
            color: var(--success-fg);
            border-color: #bbf7d0;
        }

        .badge-pending {
            background: var(--warning-soft);
            color: var(--warning-fg);
            border-color: #fed7aa;
        }

        /* Page footer info */
        .pagination-meta {
            font-size: .82rem;
            color: var(--muted);
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .toolbar-right {
                justify-content: flex-start;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Page header --}}
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h4 class="page-header-title mb-0">
                    <i class="bi bi-cash-coin me-1"></i> Payments — Subscription View
                </h4>
                <div class="page-header-sub">
                    Review subscription payments by user, type, method and status for a chosen date range.
                </div>
            </div>
        </div>

        {{-- Summary band --}}
        <div class="band">
            <h3>
                <span>Payments Summary</span>
                <span class="label">Filtered Range</span>
            </h3>
            <div class="band-sub">
                These values are calculated for the current filters and date range below.
            </div>
            <div class="band-chips">
                <span class="band-chip slate">
                    <span class="icon">🧾</span>
                    <span>Payments</span>
                    <span class="mono">{{ number_format($stats->cnt ?? 0) }}</span>
                </span>
                <span class="band-chip green">
                    <span class="icon">✅</span>
                    <span>Total Collected</span>
                    <span class="mono">₹{{ number_format($stats->sum_paid ?? 0, 2) }}</span>
                </span>
                <span class="band-chip orange">
                    <span class="icon">⏳</span>
                    <span>Pending Amount</span>
                    <span class="mono">₹{{ number_format($stats->sum_pending ?? 0, 2) }}</span>
                </span>
                <span class="band-chip blue">
                    <span class="icon">📊</span>
                    <span>Total (All)</span>
                    <span class="mono">₹{{ number_format($stats->sum_all ?? 0, 2) }}</span>
                </span>
            </div>
        </div>

        {{-- Toolbar (filters + presets + search) --}}
        <form class="toolbar mb-3" method="get" action="{{ route('admin.payments.index') }}">
            @php
                $p = $preset ?? '';
                $qAll = request()->query();
                $makeLink = function ($name) use ($qAll) {
                    return route('admin.payments.index', array_merge($qAll, [
                        'preset'     => $name,
                        'start_date' => null,
                        'end_date'   => null,
                    ]));
                };
            @endphp

            <div class="toolbar-left">
                {{-- User --}}
                <div class="toolbar-block flex-grow-1">
                    <span class="toolbar-label">User</span>
                    <select name="user_id" class="toolbar-select w-100">
                        <option value="">All users</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->userid }}" {{ ($userId ?? '') == $u->userid ? 'selected' : '' }}>
                                {{ $u->name }} — {{ $u->mobile_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="toolbar-block">
                    <span class="toolbar-label">Status</span>
                    <select name="status" class="toolbar-select">
                        <option value="">Any</option>
                        <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                {{-- Method --}}
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

                {{-- Dates --}}
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
                {{-- Preset range links (server-side) --}}
                <a href="{{ $makeLink('today') }}"
                   class="btn-chip {{ $p === 'today' ? 'preset-active' : '' }}">
                    <i class="bi bi-sun"></i><span>Today</span>
                </a>
                <a href="{{ $makeLink('yesterday') }}"
                   class="btn-chip {{ $p === 'yesterday' ? 'preset-active' : '' }}">
                    <i class="bi bi-chevron-left"></i><span>Yesterday</span>
                </a>
                <a href="{{ $makeLink('tomorrow') }}"
                   class="btn-chip {{ $p === 'tomorrow' ? 'preset-active' : '' }}">
                    <i class="bi bi-chevron-right"></i><span>Tomorrow</span>
                </a>
                <a href="{{ $makeLink('this_week') }}"
                   class="btn-chip {{ in_array($p, ['this_week', 'week', '']) ? 'preset-active' : '' }}">
                    <i class="bi bi-calendar-week"></i><span>This Week</span>
                </a>
                <a href="{{ $makeLink('this_month') }}"
                   class="btn-chip {{ in_array($p, ['this_month', 'month']) ? 'preset-active' : '' }}">
                    <i class="bi bi-calendar-month"></i><span>This Month</span>
                </a>

                {{-- Search box --}}
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           name="q"
                           class="search-input"
                           placeholder="Search order id / payment id / user..."
                           value="{{ $search }}">
                </div>

                {{-- Submit & reset --}}
                <button class="btn-chip apply-btn" type="submit">
                    <i class="bi bi-filter"></i><span>Apply</span>
                </button>
                <a href="{{ route('admin.payments.index') }}" class="btn-chip reset-btn">
                    <i class="bi bi-bootstrap-reboot"></i><span>Reset</span>
                </a>
            </div>
        </form>

        {{-- Workbook: table --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Payments — Detailed List</div>
                    <div class="workbook-sub">
                        Showing subscription payments with type, duration, method, and status.
                    </div>
                </div>
                <div class="workbook-sub">
                    @php
                        $startLabel = $start ? \Carbon\Carbon::parse($start)->toFormattedDateString() : '—';
                        $endLabel   = $end   ? \Carbon\Carbon::parse($end)->toFormattedDateString()   : '—';
                    @endphp
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

                                <tr class="td-tight">
                                    {{-- Date / Time --}}
                                    <td class="cell-datetime">
                                        <div class="fw-semibold">{{ $dt->format('d M Y') }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock"></i> {{ $dt->format('h:i A') }}
                                        </div>
                                    </td>

                                    {{-- User --}}
                                    <td class="cell-user">
                                        <div class="fw-semibold">
                                            <i class="bi bi-person-circle"></i>
                                            {{ $p->user_name ?? '—' }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone"></i>
                                            {{ $p->user_mobile ?? '' }}
                                        </div>
                                    </td>

                                    {{-- Type (Category + product + sub id) --}}
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $p->product_category ?? '—' }}
                                            @if ($p->product_name)
                                                <span class="text-muted small">({{ $p->product_name }})</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            @if ($p->subscription_id)
                                                Sub #{{ $p->subscription_id }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Duration --}}
                                    <td>
                                        @if ($startAt && $endAt)
                                            {{ $startAt->format('d M Y') }} — {{ $endAt->format('d M Y') }}
                                            <span class="text-muted small">({{ $days }}d)</span>
                                        @else
                                            —
                                        @endif

                                        @if (!empty($p->product_duration))
                                            <div class="text-muted small">
                                                Plan: {{ $p->product_duration }} days
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Method --}}
                                    <td class="cell-method">
                                        <span class="badge-method {{ $badgeMethod }}">
                                            {{ $p->payment_method ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="cell-amount money">
                                        ₹{{ number_format($p->paid_amount ?? 0, 2) }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="cell-status">
                                        <span class="badge-status {{ $badgeStatus }}">
                                            {{ ucfirst($p->payment_status ?? '—') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inboxes"></i>
                                        No payments found for the selected filters.
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

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <div class="pagination-meta">
                        Showing
                        <strong>{{ $payments->firstItem() ?? 0 }}</strong>–
                        <strong>{{ $payments->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $payments->total() }}</strong>
                    </div>
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Bootstrap JS (if not already in layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
