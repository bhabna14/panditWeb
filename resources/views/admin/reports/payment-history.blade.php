@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --brand-blue: #e0f2fe;
            --brand-blue-edge: #bfdbfe;
            --header-text: #0b2a5b;

            --text: #0f172a;
            --muted: #6b7280;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow-sm: 0 4px 12px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius-lg: 16px;

            --accent: #2563eb;

            --success-soft: #ecfdf5;
            --success-fg: #166534;
            --warning-soft: #fff7ed;
            --warning-fg: #9a3412;
            --info-soft: #eef2ff;
            --info-fg: #3730a3;
            --danger-soft: #fef2f2;
            --danger-fg: #b91c1c;

            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .container-page {
            max-width: 1320px;
        }

        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
            margin: 1rem 0 .65rem;
        }

        .section-title {
            font-weight: 700;
            font-size: .98rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: 0;
        }

        .section-sub {
            font-size: .85rem;
            color: var(--muted);
            margin: 0;
        }

        /* === KPI CARDS === */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .85rem;
        }

        .kpi-card {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: 18px;
            box-shadow: var(--shadow-md);
            padding: .95rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            overflow: hidden;
        }

        .kpi-left {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            border: 1px solid rgba(15, 23, 42, .08);
            flex: 0 0 auto;
        }

        .kpi-meta {
            min-width: 0;
        }

        .kpi-label {
            font-size: .78rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kpi-value {
            font-weight: 800;
            font-size: 1.05rem;
            margin-top: .15rem;
            font-variant-numeric: tabular-nums;
        }

        .kpi-note {
            font-size: .78rem;
            color: var(--muted);
            margin-top: .15rem;
            font-variant-numeric: tabular-nums;
        }

        .kpi-slate .kpi-icon {
            background: #eef2f7;
            color: #0f172a;
        }

        .kpi-green .kpi-icon {
            background: var(--success-soft);
            color: var(--success-fg);
        }

        .kpi-orange .kpi-icon {
            background: var(--warning-soft);
            color: var(--warning-fg);
        }

        .kpi-blue .kpi-icon {
            background: #e0f2fe;
            color: #0b2a5b;
        }

        /* === METHOD GRID === */
        .mini-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .85rem;
        }

        .mini-card {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            padding: .9rem 1rem;
        }

        .mini-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            margin-bottom: .55rem;
        }

        .mini-title {
            font-weight: 800;
            font-size: .92rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .mini-badge {
            font-size: .74rem;
            font-weight: 800;
            padding: .2rem .55rem;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, .10);
            background: #f8fafc;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .mini-amount {
            font-weight: 900;
            font-size: 1.02rem;
            margin: 0;
            font-variant-numeric: tabular-nums;
        }

        .mini-sub {
            margin-top: .25rem;
            font-size: .82rem;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            font-variant-numeric: tabular-nums;
        }

        /* === TYPE GRID (2 cards) === */
        .type-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }

        .type-card {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border: 1px solid var(--ring);
            border-radius: 18px;
            box-shadow: var(--shadow-md);
            padding: 1rem 1.05rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: transform .14s ease, box-shadow .14s ease, border-color .14s ease;
        }

        .type-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .type-card:hover {
            transform: translateY(-1px);
        }

        .type-active {
            border-color: rgba(37, 99, 235, .45);
            box-shadow: 0 12px 34px rgba(37, 99, 235, 0.14);
            position: relative;
        }

        .type-active::after {
            content: 'Selected';
            position: absolute;
            top: .8rem;
            right: .9rem;
            font-size: .72rem;
            font-weight: 900;
            padding: .2rem .55rem;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.10);
            color: #1d4ed8;
            border: 1px solid rgba(37, 99, 235, 0.22);
        }

        .type-left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .type-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            border: 1px solid rgba(15, 23, 42, .08);
        }

        .type-meta h4 {
            margin: 0;
            font-size: .98rem;
            font-weight: 900;
        }

        .type-meta p {
            margin: .2rem 0 0;
            font-size: .82rem;
            color: var(--muted);
            font-variant-numeric: tabular-nums;
        }

        .type-right {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .type-right .big {
            font-weight: 950;
            font-size: 1.15rem;
        }

        .type-right .small {
            margin-top: .15rem;
            font-size: .82rem;
            color: var(--muted);
        }

        .type-sub .type-icon {
            background: var(--info-soft);
            color: var(--info-fg);
        }

        .type-cus .type-icon {
            background: #fff7ed;
            color: #9a3412;
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
            margin: 1.1rem 0 1.1rem;
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
            font-weight: 700;
            color: var(--muted);
        }

        .toolbar-select,
        .toolbar-input {
            border-radius: 999px;
            border: 1px solid var(--ring);
            padding: .45rem .85rem;
            font-size: .85rem;
            font-weight: 600;
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
            font-weight: 700;
            font-size: .8rem;
            padding: .4rem .9rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            text-decoration: none;
            cursor: pointer;
            transition: all .15s ease;
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

        .btn-chip.apply-btn {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.reset-btn {
            border-style: dashed;
        }

        /* Workbook wrapper + table */
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
            font-weight: 800;
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
            font-weight: 800;
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

        .money {
            font-variant-numeric: tabular-nums;
        }

        .badge-method {
            font-weight: 800;
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

        .badge-status {
            font-weight: 800;
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

        .pagination-meta {
            font-size: .82rem;
            color: var(--muted);
        }

        /* === Pagination Fix (Bootstrap 5) === */
        .pagination {
            margin: 0;
            gap: .35rem;
        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-link {
            border-radius: 12px;
            border: 1px solid var(--ring);
            padding: .42rem .75rem;
            font-weight: 800;
            font-size: .85rem;
            color: #0f172a;
            background: #fff;
            box-shadow: none;
            line-height: 1.15;
        }

        .pagination .page-link:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            border-color: #020617;
            color: #fff;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.18);
        }

        .pagination .page-item.disabled .page-link {
            opacity: .55;
            pointer-events: none;
        }

        /* Safety: if Tailwind SVG ever appears, keep it small */
        .pagination svg {
            width: 16px !important;
            height: 16px !important;
        }

        @media (max-width: 1200px) {
            .kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .toolbar-right {
                justify-content: flex-start;
            }

            .type-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .mini-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        @php
            $startLabel = $start ? \Carbon\Carbon::parse($start)->toFormattedDateString() : '—';
            $endLabel = $end ? \Carbon\Carbon::parse($end)->toFormattedDateString() : '—';

            $qAll = request()->query();
            $type = $type ?? ''; // subscription|customize|''

            $makeTypeLink = function (string $typeName) use ($qAll) {
                return route(
                    'admin.payments.index',
                    array_merge($qAll, [
                        'type' => $typeName,
                        'page' => null,
                    ]),
                );
            };

            $clearTypeLink = route(
                'admin.payments.index',
                array_merge($qAll, [
                    'type' => null,
                    'page' => null,
                ]),
            );
        @endphp

        {{-- Toolbar (filters + presets) --}}
        <form class="toolbar" method="get" action="{{ route('admin.payments.index') }}">
            @php
                $p = $preset ?? '';
                $makeLink = function ($name) use ($qAll) {
                    return route(
                        'admin.payments.index',
                        array_merge($qAll, [
                            'preset' => $name,
                            'start_date' => null,
                            'end_date' => null,
                            'page' => null,
                        ]),
                    );
                };
            @endphp

            {{-- keep type filter when Apply is pressed --}}
            <input type="hidden" name="type" value="{{ $type }}">

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
                <a href="{{ $makeLink('this_week') }}"
                    class="btn-chip {{ in_array($p, ['this_week', 'week', '']) ? 'preset-active' : '' }}">
                    <i class="bi bi-calendar-week"></i><span>Week</span>
                </a>
                <a href="{{ $makeLink('this_month') }}"
                    class="btn-chip {{ in_array($p, ['this_month', 'month']) ? 'preset-active' : '' }}">
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

        {{-- 1) OVERALL SUMMARY CARDS --}}
        <div class="section-head">
            <div>
                <p class="section-title"><i class="bi bi-graph-up"></i> Summary (Selected Range)</p>
                <p class="section-sub">Range: <strong>{{ $startLabel }}</strong> — <strong>{{ $endLabel }}</strong>
                </p>
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
                    $methodStr = strtolower((string) $methodName);

                    $icon = match (true) {
                        str_contains($methodStr, 'upi') => 'bi-qr-code-scan',
                        str_contains($methodStr, 'cash') => 'bi-cash-coin',
                        str_contains($methodStr, 'card') => 'bi-credit-card',
                        default => 'bi-question-circle',
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

        {{-- 3) SUBSCRIPTION vs CUSTOMIZE CARDS (CLICKABLE) --}}
        <div class="section-head">
            <div>
                <p class="section-title"><i class="bi bi-diagram-3"></i> Collected by Order Type</p>
                <p class="section-sub">Click a card to load only that type in the table below.</p>
            </div>
        </div>

        <div class="type-grid">
            <a class="type-card-link" href="{{ $makeTypeLink('subscription') }}">
                <div class="type-card type-sub {{ $type === 'subscription' ? 'type-active' : '' }}">
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
                </div>
            </a>

            <a class="type-card-link" href="{{ $makeTypeLink('customize') }}">
                <div class="type-card type-cus {{ $type === 'customize' ? 'type-active' : '' }}">
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
                </div>
            </a>
        </div>

        {{-- Table --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Payments — Detailed List</div>
                    <div class="workbook-sub">
                        Showing payments with type, duration, method, and status.
                        @if (!empty($type))
                            <span class="ms-2">
                                <strong>Type filter:</strong>
                                {{ $type === 'subscription' ? 'Subscription' : 'Customize' }}
                                <a class="btn-chip ms-2" href="{{ $clearTypeLink }}">
                                    <i class="bi bi-x-circle"></i><span>Clear</span>
                                </a>
                            </span>
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
                                    $methodStr = strtolower((string) ($p->payment_method ?? ''));
                                    $badgeMethod = match (true) {
                                        str_contains($methodStr, 'upi') => 'badge-upi',
                                        str_contains($methodStr, 'cash') => 'badge-cash',
                                        str_contains($methodStr, 'card') => 'badge-card',
                                        default => 'badge-soft',
                                    };
                                    $badgeStatus = $p->payment_status === 'paid' ? 'badge-paid' : 'badge-pending';

                                    $startAt = $p->start_date ? \Carbon\Carbon::parse($p->start_date) : null;
                                    $endAt = $p->end_date ? \Carbon\Carbon::parse($p->end_date) : null;
                                    $days = $startAt && $endAt ? $startAt->diffInDays($endAt) + 1 : null;
                                @endphp

                                <tr>
                                    <td class="cell-datetime">
                                        <div class="fw-semibold">{{ $dt->format('d M Y') }}</div>
                                        <div class="text-muted small"><i class="bi bi-clock"></i>
                                            {{ $dt->format('h:i A') }}</div>
                                    </td>

                                    <td class="cell-user">


                                        <a class="btn btn-warning btn-sm text-center"
                                            href="{{ route('showCustomerDetails', $p->userid) }}">
                                            <div class="fw-semibold">
                                                <i class="bi bi-person-circle"></i> {{ $p->user_name ?? '—' }}
                                            </div>
                                        </a>
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

                <div class="d-flex justify-content-between align-items-center pt-3 flex-wrap gap-2">
                    <div class="pagination-meta">
                        Showing
                        <strong>{{ $payments->firstItem() ?? 0 }}</strong>–<strong>{{ $payments->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $payments->total() }}</strong>
                    </div>

                    {{-- Force Bootstrap pagination view (fixes huge arrows) --}}
                    {{ $payments->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
