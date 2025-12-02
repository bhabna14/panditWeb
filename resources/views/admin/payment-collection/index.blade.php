@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts: Poppins (page) + Nunito Sans (table) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- SheetJS for export --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            /* Core palette */
            --brand-blue: #e9f2ff;
            --brand-blue-edge: #cfe0ff;
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;

            /* Table */
            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #64748b;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 16px;

            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --accent-border: #bfdbfe;
            --danger: #b42318;
            --danger-soft: #fef2f2;
            --success: #047857;
            --success-soft: #ecfdf3;
            --neutral-soft: #f3f4f6;
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

        /* Toolbar (filters) */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            padding: .85rem 1rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            align-items: center;
            box-shadow: var(--shadow);
            margin-bottom: 1.1rem;
        }

        .date-range {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: .85rem;
        }

        .date-range span.label {
            font-weight: 500;
        }

        .date-range input,
        .date-range select {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 150px;
        }

        .date-range input:focus,
        .date-range select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .22);
        }

        .toolbar-right {
            display: flex;
            flex-direction: column;
            gap: .4rem;
            align-items: flex-end;
        }

        .toolbar-search-row {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            width: 100%;
            justify-content: flex-end;
        }

        .toolbar-search-row input[type="text"] {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-size: .88rem;
            min-width: 200px;
            flex: 1 1 200px;
        }

        .toolbar-search-row input[type="text"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .22);
        }

        .toolbar-chips-row {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: flex-end;
        }

        .btn-chip {
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #0f172a;
            padding: .42rem .9rem;
            border-radius: 999px;
            font-weight: 500;
            cursor: pointer;
            font-size: .82rem;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            text-decoration: none;
        }

        .btn-chip::before {
            content: '⦿';
            font-size: .7rem;
            opacity: .5;
        }

        .btn-chip:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
        }

        .btn-apply {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-apply::before {
            content: '↻';
            font-size: .75rem;
            opacity: .75;
        }

        .btn-reset {
            color: #4b5563;
        }

        .btn-reset::before {
            content: '⟲';
            font-size: .75rem;
            opacity: .7;
        }

        .btn-chip.rows {
            padding-inline: .65rem;
            font-size: .8rem;
        }

        .btn-chip.rows::before {
            content: '';
        }

        .btn-chip.rows.active {
            background: var(--accent-soft);
            border-color: var(--accent-border);
            color: #1d4ed8;
        }

        /* Header band */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 18px;
            padding: .9rem 1.2rem;
            box-shadow: var(--shadow);
            margin-bottom: .9rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .band h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--header-text);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band h3 span.label {
            font-size: .78rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.07);
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .chip span.icon {
            font-size: .9rem;
        }

        .chip.green {
            background: var(--chip-green);
            color: var(--chip-green-text);
            border-color: #c9f0d6;
        }

        .chip.orange {
            background: var(--chip-orange);
            color: var(--chip-orange-text);
            border-color: #ffd9b3;
        }

        .chip.blue {
            background: var(--chip-blue);
            color: var(--chip-blue-text);
            border-color: #bae6fd;
        }

        .chip.gray {
            background: #f3f4f6;
            color: #4b5563;
            border-color: #e5e7eb;
        }

        .chip.purple {
            background: #f3e8ff;
            color: #6b21a8;
            border-color: #e9d5ff;
        }

        /* Workbook */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .workbook-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
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
            color: #4b5563;
            font-size: .84rem;
        }

        .workbook-tools {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
            justify-content: flex-end;
        }

        .rows-label {
            font-size: .8rem;
            color: var(--muted);
            margin-right: .25rem;
        }

        .export-btn {
            border: 1px solid var(--accent-border);
            border-radius: 999px;
            padding: .45rem .9rem;
            font-weight: 500;
            cursor: pointer;
            background: var(--accent-soft);
            color: #1d4ed8;
            font-size: .84rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            transition: all .15s ease;
        }

        .export-btn::before {
            content: '⬇';
            font-size: .8rem;
        }

        .export-btn:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        /* Tabs */
        .payment-tabs {
            padding: 0 1.1rem;
            border-bottom: 1px solid var(--ring);
            background: #f9fafb;
        }

        .payment-tabs .nav-link {
            border: none;
            border-radius: 999px 999px 0 0;
            padding: .45rem .9rem;
            font-size: .83rem;
            font-weight: 500;
            color: var(--muted);
            margin-right: .35rem;
            background: transparent;
        }

        .payment-tabs .nav-link.active {
            background: #ffffff;
            color: #111827;
            box-shadow: 0 -2px 0 var(--accent) inset;
        }

        .payment-tabs .nav-link:focus {
            outline: none;
        }

        /* Table wrapper */
        .excel-wrap {
            padding: 1rem 1.1rem 1.1rem;
            overflow: auto;
        }

        .excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            font-size: .9rem;
            border: 1px solid var(--table-border);
            border-radius: 14px;
            overflow: hidden;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .excel thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            padding: .6rem .7rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 0;
            border-right: 1px solid rgba(55, 65, 81, 0.7);
            white-space: nowrap;
        }

        .excel thead th:first-child {
            border-top-left-radius: 14px;
        }

        .excel thead th:last-child {
            border-top-right-radius: 14px;
            border-right: none;
        }

        .excel tbody td {
            border-top: 1px solid var(--table-border);
            border-right: 1px solid var(--table-border);
            padding: .55rem .7rem;
            vertical-align: middle;
            color: var(--text);
            font-weight: 400;
            background: #fff;
        }

        .excel tbody tr:nth-child(even) td {
            background: var(--table-zebra);
        }

        .excel tbody tr:last-child td:first-child {
            border-bottom-left-radius: 14px;
        }

        .excel tbody tr:last-child td:last-child {
            border-bottom-right-radius: 14px;
        }

        .excel tbody tr:hover td {
            background: var(--table-hover);
        }

        .excel th,
        .excel td {
            font-variant-numeric: tabular-nums;
        }

        .excel tr td:first-child,
        .excel thead th:first-child {
            border-left: none;
        }

        .excel tbody tr td:last-child {
            border-right: none;
        }

        /* Column helpers */
        .col-index {
            width: 56px;
            text-align: right;
            color: #6b7280;
            font-size: .8rem;
        }

        .col-text {
            font-weight: 500;
        }

        .col-date {
            white-space: nowrap;
            font-size: .86rem;
            color: #4b5563;
        }

        .col-money {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .col-money span.currency {
            color: #6b7280;
            font-size: .8rem;
            margin-right: .18rem;
        }

        .col-actions {
            white-space: nowrap;
        }

        .amount-cell {
            font-weight: 700;
        }

        /* Badges */
        .badge-soft {
            border-radius: 999px;
            padding: .28rem .7rem;
            font-size: .75rem;
            font-weight: 500;
        }

        .badge-expired {
            background: #fef2f2;
            color: #b91c1c;
        }

        .badge-paid {
            background: #ecfdf3;
            color: #047857;
        }

        /* Pagination spacing */
        .pagination {
            margin-top: 1rem;
            margin-bottom: .5rem;
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .toolbar-right {
                align-items: flex-start;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
                gap: .4rem;
            }

            .workbook-tools {
                width: 100%;
                justify-content: flex-start;
            }

            .export-btn {
                width: auto;
            }
        }
    </style>
@endsection

@section('content')
    @php use Carbon\Carbon; @endphp

    <div class="container container-page py-4">

        {{-- ====== FILTER TOOLBAR (New design) ====== --}}
        <form method="GET" action="{{ route('payment.collection.index') }}" id="filterForm" class="toolbar">
            {{-- Left: Date + Method --}}
            <div class="date-range">
                <span class="label">From</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" />
                <span class="label">To</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" />
                <span class="label">Method</span>
                <select name="method">
                    <option value="">All</option>
                    @foreach ($methods as $m)
                        <option value="{{ $m }}" {{ ($filters['method'] ?? '') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Right: Search + Preset chips + actions --}}
            <div class="toolbar-right">
                <div class="toolbar-search-row">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                        placeholder="Search: name, mobile, order..." />
                </div>
                <div class="toolbar-chips-row">
                    <button class="btn-chip" data-preset="today" type="button">Today</button>
                    <button class="btn-chip" data-preset="yesterday" type="button">Yesterday</button>
                    <button class="btn-chip" data-preset="tomorrow" type="button">Tomorrow</button>
                    <button class="btn-chip" data-preset="this_week" type="button">This Week</button>
                    <button class="btn-chip" data-preset="this_month" type="button">This Month</button>

                    <button class="btn-chip btn-apply" type="submit">Apply</button>
                    <a href="{{ route('payment.collection.index') }}" class="btn-chip btn-reset">Reset</a>
                </div>
                <input type="hidden" name="preset" id="presetInput" value="{{ $filters['preset'] ?? '' }}">
            </div>
        </form>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        {{-- ====== SUMMARY BAND ====== --}}
        @php
            $fromLabel = $filters['from'] ?? null;
            $toLabel = $filters['to'] ?? null;
            $fromText = $fromLabel ? Carbon::parse($fromLabel)->format('d M Y') : '—';
            $toText = $toLabel ? Carbon::parse($toLabel)->format('d M Y') : '—';
        @endphp

        <div class="band">
            <h3>
                Payment Collection
                <span class="label" id="bandLabel">Pending</span>
            </h3>
            <div class="chips" style="margin-bottom:.2rem;">
                <span class="chip gray">
                    <span class="icon">📅</span>
                    <span>Range</span> {{ $fromText }} – {{ $toText }}
                </span>
            </div>
            <div class="chips" id="summaryChips">
                {{-- JS will inject chips based on active tab --}}
            </div>
        </div>

        {{-- ====== WORKBOOK (Tabs + Tables) ====== --}}
        @php
            $sizes = [10, 25, 50, 100];
            $currentPerPage = $perPage ?? 10;
            $mkPerPage = function ($v) {
                return route('payment.collection.index', array_merge(request()->query(), ['per_page' => $v]));
            };
        @endphp

        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">
                        Payments — <span id="workbookModeLabel">Pending</span>
                    </div>
                    <div class="workbook-sub">
                        Showing <span id="workbookCountLabel">{{ $pendingCount ?? 0 }}</span> records in current view.
                    </div>
                </div>
                <div class="workbook-tools">
                    <div class="d-flex align-items-center gap-1">
                        <span class="rows-label">Rows</span>
                        @foreach ($sizes as $sz)
                            <a href="{{ $mkPerPage($sz) }}"
                                class="btn-chip rows {{ (string) $currentPerPage === (string) $sz ? 'active' : '' }}">
                                {{ $sz }}
                            </a>
                        @endforeach
                        <a href="{{ $mkPerPage('all') }}"
                            class="btn-chip rows {{ (string) $currentPerPage === 'all' ? 'active' : '' }}">
                            All
                        </a>
                    </div>

                    <button class="export-btn" id="exportActiveBtn" type="button">
                        Export (XLSX)
                    </button>
                </div>
            </div>

            {{-- Tabs header --}}
            <ul class="nav nav-tabs payment-tabs" id="paymentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                        type="button" role="tab" data-mode="pending" aria-controls="pending" aria-selected="true">
                        Pending
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button"
                        role="tab" data-mode="paid" aria-controls="paid" aria-selected="false">
                        Paid
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired"
                        type="button" role="tab" data-mode="expired" aria-controls="expired"
                        aria-selected="false">
                        Expired
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="paymentTabsContent">
                {{-- ======= PENDING TAB ======= --}}
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    @if ($perPage !== 'all' && method_exists($pendingPayments, 'links'))
                        <div class="d-flex justify-content-end px-3 pt-3">
                            {{ $pendingPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif

                    <div class="excel-wrap">
                        <table class="excel" id="pendingExcelTable">
                            <thead>
                                <tr>
                                    <th class="col-index">#</th>
                                    <th class="col-text">User</th>
                                    <th>Mobile</th>
                                    <th class="col-date">Duration</th>
                                    <th class="col-text">Type</th>
                                    <th class="col-money">Amount (Due)</th>
                                    <th class="col-date">Since</th>
                                    <th class="col-actions">Notify</th>
                                    <th class="col-actions">Collect</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendingRows = is_iterable($pendingPayments) ? $pendingPayments : collect();
                                    $pendingFirst = method_exists($pendingPayments, 'firstItem')
                                        ? $pendingPayments->firstItem() ?? 1
                                        : 1;
                                @endphp
                                @forelse($pendingRows as $i => $row)
                                    @php
                                        $start = $row->start_date ? Carbon::parse($row->start_date) : null;
                                        $end = $row->end_date ? Carbon::parse($row->end_date) : null;
                                        $durationDays = $start && $end ? $start->diffInDays($end) + 1 : 0;
                                        $since = $row->latest_pending_since
                                            ? Carbon::parse($row->latest_pending_since)
                                            : null;
                                    @endphp
                                    <tr data-row-id="{{ $row->latest_payment_row_id }}">
                                        <td class="col-index text-muted">{{ $pendingFirst + $i }}</td>
                                        <td class="col-text">
                                            <div class="fw-semibold">{{ $row->user_name }}</div>
                                            <div class="text-muted small">Sub #{{ $row->subscription_id ?? '—' }}</div>
                                        </td>
                                        <td>{{ $row->mobile_number }}</td>
                                        <td class="col-date">
                                            @if ($start && $end)
                                                {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                                <span class="text-muted small">({{ $durationDays }}d)</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="col-text">
                                            {{ $row->product_category ?? '—' }}
                                            @if ($row->product_name)
                                                <span class="text-muted small">({{ $row->product_name }})</span>
                                            @endif
                                        </td>
                                        <td class="col-money amount-cell">
                                            <span class="currency">₹</span>{{ number_format($row->due_amount ?? 0, 2) }}
                                        </td>
                                        <td class="col-date">
                                            @if ($since)
                                                <span
                                                    class="badge badge-soft badge-expired">{{ $since->diffForHumans() }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="col-actions">
                                            <a href="{{ route('admin.notification.create', ['user' => $row->user_id]) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Send notification to {{ $row->user_name }}">
                                                Notify
                                            </a>
                                        </td>
                                        <td class="col-actions">
                                            <button type="button" class="btn btn-sm btn-success btn-collect"
                                                data-id="{{ $row->latest_payment_row_id }}"
                                                data-order="{{ $row->latest_order_id }}"
                                                data-user="{{ $row->user_name }}"
                                                data-amount="{{ $row->due_amount ?? 0 }}"
                                                data-method="{{ $row->payment_method ?? '' }}"
                                                data-url="{{ route('payment.collection.collect', $row->latest_payment_row_id) }}"
                                                data-bs-toggle="modal" data-bs-target="#collectModal">
                                                Collect
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">No pending payments 🎉</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($perPage !== 'all' && method_exists($pendingPayments, 'links'))
                        <div class="px-3 pb-3">
                            {{ $pendingPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                </div>

                {{-- ======= PAID TAB ======= --}}
                <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
                    @if ($perPage !== 'all' && method_exists($paidPayments, 'links'))
                        <div class="d-flex justify-content-end px-3 pt-3">
                            {{ $paidPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif

                    <div class="excel-wrap">
                        <table class="excel" id="paidExcelTable">
                            <thead>
                                <tr>
                                    <th class="col-index">#</th>
                                    <th class="col-text">User</th>
                                    <th>Mobile</th>
                                    <th>Order</th>
                                    <th class="col-text">Type</th>
                                    <th class="col-money">Amount</th>
                                    <th>Method</th>
                                    <th class="col-date">Paid On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $paidRows = is_iterable($paidPayments) ? $paidPayments : collect();
                                    $paidFirst = method_exists($paidPayments, 'firstItem')
                                        ? $paidPayments->firstItem() ?? 1
                                        : 1;
                                @endphp
                                @forelse($paidRows as $i => $row)
                                    @php
                                        $start = $row->start_date ? Carbon::parse($row->start_date) : null;
                                        $end = $row->end_date ? Carbon::parse($row->end_date) : null;
                                        $paidAt = $row->paid_at ? Carbon::parse($row->paid_at) : null;
                                    @endphp
                                    <tr>
                                        <td class="col-index text-muted">{{ $paidFirst + $i }}</td>
                                        <td class="col-text">
                                            <div class="fw-semibold">{{ $row->user_name }}</div>
                                        </td>
                                        <td>{{ $row->mobile_number }}</td>
                                        <td>#{{ $row->order_id }}</td>
                                        <td class="col-text">
                                            {{ $row->product_category ?? '—' }}
                                            @if ($row->product_name)
                                                <span class="text-muted small">({{ $row->product_name }})</span>
                                            @endif
                                            <div class="text-muted small">
                                                @if ($start && $end)
                                                    {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="col-money">
                                            <span class="currency">₹</span>{{ number_format($row->paid_amount ?? 0, 2) }}
                                        </td>
                                        <td>{{ $row->payment_method ?? '—' }}</td>
                                        <td class="col-date">
                                            @if ($paidAt)
                                                <span class="badge badge-soft badge-paid">
                                                    {{ $paidAt->format('d M Y, h:i A') }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No paid payments.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($perPage !== 'all' && method_exists($paidPayments, 'links'))
                        <div class="px-3 pb-3">
                            {{ $paidPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                </div>

                {{-- ======= EXPIRED TAB ======= --}}
                <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
                    @if ($perPage !== 'all' && method_exists($expiredSubs, 'links'))
                        <div class="d-flex justify-content-end px-3 pt-3">
                            {{ $expiredSubs->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif

                    <div class="excel-wrap">
                        <table class="excel" id="expiredExcelTable">
                            <thead>
                                <tr>
                                    <th class="col-index">#</th>
                                    <th class="col-text">User</th>
                                    <th>Mobile</th>
                                    <th class="col-date">Duration</th>
                                    <th class="col-text">Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $expiredRows = is_iterable($expiredSubs) ? $expiredSubs : collect();
                                    $expiredFirst = method_exists($expiredSubs, 'firstItem')
                                        ? $expiredSubs->firstItem() ?? 1
                                        : 1;
                                @endphp
                                @forelse($expiredRows as $i => $row)
                                    @php
                                        $start = Carbon::parse($row->start_date);
                                        $end = Carbon::parse($row->end_date);
                                        $durationDays = $start->diffInDays($end) + 1;
                                    @endphp
                                    <tr>
                                        <td class="col-index text-muted">{{ $expiredFirst + $i }}</td>
                                        <td class="col-text">
                                            <div class="fw-semibold">{{ $row->user_name }}</div>
                                            <div class="text-muted small">
                                                Order #{{ $row->order_id }} • Sub #{{ $row->subscription_id }}
                                            </div>
                                        </td>
                                        <td>{{ $row->mobile_number }}</td>
                                        <td class="col-date">
                                            {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                            <span class="text-muted small">({{ $durationDays }}d)</span>
                                        </td>
                                        <td class="col-text">
                                            {{ $row->product_category ?? '—' }}
                                            @if ($row->product_name)
                                                <span class="text-muted small">({{ $row->product_name }})</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-soft badge-expired">Expired</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No expired subscriptions.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($perPage !== 'all' && method_exists($expiredSubs, 'links'))
                        <div class="px-3 pb-3">
                            {{ $expiredSubs->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ======= Collect Modal ======= --}}
        <div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <form id="collectForm" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="collectModalLabel">Collect Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="payment_id" id="payment_id">
                        <input type="hidden" id="collectUrl" value="">
                        <div class="mb-2">
                            <div class="small text-muted" id="collectInfo">Order —</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="amount"
                                id="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mode of Payment</label>
                            <select class="form-select" name="payment_method" id="payment_method" required>
                                <option value="" disabled selected>Select method</option>
                                @foreach ($methods as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Received By</label>
                            <input type="text" class="form-control" name="received_by" id="received_by"
                                value="{{ auth('admins')->user()->name ?? '' }}" maxlength="100" required>
                        </div>
                        <div class="form-text">Confirm the amount and who received the payment.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="collectSubmit">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            // ---- DATE PRESET HANDLING (client-side) ----
            const filterForm = document.getElementById('filterForm');
            const presetInput = document.getElementById('presetInput');
            const fromInput = document.querySelector('input[name="from"]');
            const toInput = document.querySelector('input[name="to"]');

            function formatDateInput(date) {
                const d = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
                return d.toISOString().slice(0, 10);
            }

            function setRange(preset) {
                const today = new Date();
                let start = new Date(today);
                let end = new Date(today);

                switch (preset) {
                    case 'today':
                        break;
                    case 'yesterday':
                        start.setDate(start.getDate() - 1);
                        end.setDate(end.getDate() - 1);
                        break;
                    case 'tomorrow':
                        start.setDate(start.getDate() + 1);
                        end.setDate(end.getDate() + 1);
                        break;
                    case 'this_week':
                        // Monday–Sunday of current week
                        const day = today.getDay(); // 0 (Sun) - 6 (Sat)
                        const diffToMonday = (day === 0 ? -6 : 1 - day);
                        start.setDate(today.getDate() + diffToMonday);
                        end = new Date(start);
                        end.setDate(start.getDate() + 6);
                        break;
                    case 'this_month':
                        start = new Date(today.getFullYear(), today.getMonth(), 1);
                        end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    default:
                        return;
                }

                fromInput.value = formatDateInput(start);
                toInput.value = formatDateInput(end);
            }

            document.querySelectorAll('.btn-chip[data-preset]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const preset = btn.getAttribute('data-preset');
                    presetInput.value = preset;
                    setRange(preset);
                    filterForm.submit();
                });
            });

            ['from', 'to', 'method', 'q'].forEach(name => {
                const el = document.querySelector(`[name="${name}"]`);
                if (el) {
                    el.addEventListener('change', () => {
                        presetInput.value = '';
                    });
                }
            });

            // ---- SUMMARY CHIPS (band) ----
            const chipsHost = document.getElementById('summaryChips');
            const bandLabel = document.getElementById('bandLabel');
            const workbookModeLabel = document.getElementById('workbookModeLabel');
            const workbookCountLabel = document.getElementById('workbookCountLabel');

            const summaryTpl = {
                pending: {
                    label: 'Pending',
                    count: {{ (int) ($pendingCount ?? 0) }},
                    html: `
                        <span class="chip green">
                            <span class="icon">💰</span>
                            <span>Pending Amount</span>
                            ₹{{ number_format($pendingTotalAmount ?? 0, 2) }}
                        </span>
                        <span class="chip orange">
                            <span class="icon">🕒</span>
                            <span>Pending Rows</span>
                            {{ (int) ($pendingCount ?? 0) }}
                        </span>
                        <span class="chip gray">
                            <span class="icon">📦</span>
                            <span>Expired Subs</span>
                            {{ (int) ($expiredCount ?? 0) }}
                        </span>
                    `
                },
                paid: {
                    label: 'Paid',
                    count: {{ (int) ($paidCount ?? 0) }},
                    html: `
                        <span class="chip blue">
                            <span class="icon">✅</span>
                            <span>Paid Total</span>
                            ₹{{ number_format($paidTotalAmount ?? 0, 2) }}
                        </span>
                        <span class="chip purple">
                            <span class="icon">🧾</span>
                            <span>Paid Rows</span>
                            {{ (int) ($paidCount ?? 0) }}
                        </span>
                        <span class="chip gray">
                            <span class="icon">📦</span>
                            <span>Expired Subs</span>
                            {{ (int) ($expiredCount ?? 0) }}
                        </span>
                    `
                },
                expired: {
                    label: 'Expired',
                    count: {{ (int) ($expiredCount ?? 0) }},
                    html: `
                        <span class="chip gray">
                            <span class="icon">📦</span>
                            <span>Expired Subs</span>
                            {{ (int) ($expiredCount ?? 0) }}
                        </span>
                        <span class="chip green">
                            <span class="icon">💰</span>
                            <span>Pending Total</span>
                            ₹{{ number_format($pendingTotalAmount ?? 0, 2) }}
                        </span>
                        <span class="chip blue">
                            <span class="icon">✅</span>
                            <span>Paid Total</span>
                            ₹{{ number_format($paidTotalAmount ?? 0, 2) }}
                        </span>
                    `
                }
            };

            function activateSummary(key) {
                const conf = summaryTpl[key] || summaryTpl.pending;
                bandLabel.textContent = conf.label;
                workbookModeLabel.textContent = conf.label;
                workbookCountLabel.textContent = conf.count;
                chipsHost.innerHTML = conf.html;
            }

            const initialMode =
                document.querySelector('#paymentTabs .nav-link.active')?.getAttribute('data-mode') || 'pending';
            activateSummary(initialMode);

            document.getElementById('paymentTabs').addEventListener('shown.bs.tab', function(e) {
                const key = e.target.getAttribute('data-mode') || 'pending';
                activateSummary(key);
            });

            // ---- EXPORT ACTIVE TABLE ----
            const exportBtn = document.getElementById('exportActiveBtn');
            if (exportBtn && window.XLSX) {
                exportBtn.addEventListener('click', () => {
                    const activePane = document.querySelector('#paymentTabsContent .tab-pane.active');
                    if (!activePane) return;
                    const table = activePane.querySelector('table.excel');
                    if (!table) return;

                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.table_to_sheet(table, {
                        raw: true
                    });
                    XLSX.utils.book_append_sheet(wb, ws, 'Payments');

                    const mode =
                        document.querySelector('#paymentTabs .nav-link.active')?.getAttribute('data-mode') ||
                        'payments';
                    const fileName =
                        'Payments_' + mode.charAt(0).toUpperCase() + mode.slice(1) + '.xlsx';

                    XLSX.writeFile(wb, fileName);
                });
            }

            // ---- COLLECT MODAL (AJAX submit) ----
            $(function() {
                let currentUrl = null;

                $('#collectModal').on('show.bs.modal', function(event) {
                    const button = $(event.relatedTarget);
                    const id = button.data('id');
                    const order = button.data('order');
                    const user = button.data('user');
                    const amount = parseFloat(button.data('amount') || 0);
                    const method = button.data('method') || '';
                    currentUrl = button.data('url');

                    $('#payment_id').val(id);
                    $('#amount').val(isNaN(amount) ? '' : amount.toFixed(2));
                    $('#payment_method').val(method || '');
                    $('#collectInfo').text(`Order #${order || '—'} • ${user || ''}`);
                    $('#collectUrl').val(currentUrl || '');
                });

                $('#collectForm').on('submit', function(e) {
                    e.preventDefault();
                    const url = $('#collectUrl').val();
                    if (!url) return;

                    const payload = {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        payment_id: $('#payment_id').val(),
                        amount: $('#amount').val(),
                        payment_method: $('#payment_method').val(),
                        received_by: $('#received_by').val(),
                    };

                    $('#collectSubmit').prop('disabled', true);

                    $.post(url, payload)
                        .done(function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment recorded',
                                text: res.message || 'The payment has been marked as paid.',
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .fail(function(xhr) {
                            let msg = 'Something went wrong.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg
                            });
                        })
                        .always(function() {
                            $('#collectSubmit').prop('disabled', false);
                        });
                });
            });
        })();
    </script>
@endsection
