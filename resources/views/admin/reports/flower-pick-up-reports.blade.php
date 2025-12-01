@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables / Select2 / SweetAlert CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            /* Page + surfaces */
            --bg-subtle: #f4f5fb;
            --surface: #ffffff;
            --border: #e2e4f0;
            --ring: #d4d7e8;

            --text: #0f172a;
            --muted: #6b7280;

            /* Accent / gradient */
            --indigo: #6366f1;
            --indigo-600: #4f46e5;
            --cyan: #06b6d4;

            /* Chips / status colors */
            --success-soft: #ecfdf3;
            --success-fg: #166534;
            --warning-soft: #fff7ed;
            --warning-fg: #9a3412;
            --danger-soft: #fef2f2;
            --danger-fg: #b91c1c;
            --info-soft: #eff6ff;
            --info-fg: #1d4ed8;
            --neutral-soft: #e5e7eb;
            --neutral-fg: #111827;

            /* Shadows */
            --sh-sm: 0 4px 12px rgba(15, 23, 42, 0.06);
            --sh-md: 0 10px 28px rgba(15, 23, 42, 0.1);
        }

        html,
        body {
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 100% -10%, rgba(99, 102, 241, .14), transparent 60%),
                radial-gradient(900px 500px at 0% 10%, rgba(6, 182, 212, .10), transparent 55%),
                var(--bg-subtle);
        }

        .container-page {
            max-width: 1320px;
        }

        .mono {
            font-variant-numeric: tabular-nums;
        }

        /* ============================
           Page header
        ============================ */
        .page-header-title {
            font-weight: 600;
            color: #020617;
        }

        .page-header-sub {
            font-size: .85rem;
            color: var(--muted);
        }

        /* ============================
           KPI band (top summary)
        ============================ */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border-radius: 18px;
            border: 1px solid #c7d2fe;
            padding: .9rem 1.2rem;
            box-shadow: var(--sh-md);
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .band-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .band-title {
            font-weight: 600;
            font-size: .98rem;
            color: #0b2a5b;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .band-pill {
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .12rem .6rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, .1);
            color: #020617;
        }

        .band-sub {
            font-size: .82rem;
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
            gap: .4rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: .8rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(15, 23, 42, .12);
            background: #fff;
        }

        .band-chip span.icon {
            font-size: 1rem;
        }

        .band-chip.total {
            background: #ecfdf3;
            color: var(--success-fg);
            border-color: #bbf7d0;
        }

        .band-chip.today {
            background: #fef9c3;
            color: #92400e;
            border-color: #fde68a;
        }

        .band-chip.vendors {
            background: #eef2ff;
            color: #3730a3;
            border-color: #c7d2fe;
        }

        /* ============================
           Toolbar (filters + quick ranges)
        ============================ */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--surface);
            border: 1px solid var(--ring);
            border-radius: 18px;
            padding: .9rem 1.1rem;
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.2fr);
            gap: .75rem;
            align-items: center;
            box-shadow: var(--sh-md);
            margin-bottom: 1.25rem;
        }

        .toolbar-left {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: flex-end;
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

        .toolbar-input,
        .toolbar-select {
            border-radius: 999px;
            border: 1px solid var(--ring);
            padding: .45rem .9rem;
            font-size: .85rem;
            font-weight: 500;
        }

        .toolbar-input:focus,
        .toolbar-select:focus {
            outline: none;
            border-color: var(--indigo-600);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .25);
        }

        .toolbar-right {
            display: flex;
            flex-direction: column;
            gap: .45rem;
            align-items: flex-end;
        }

        .chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            justify-content: flex-end;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .4rem .8rem;
            border-radius: 999px;
            background: #fff;
            border: 1px dashed var(--ring);
            color: #334155;
            font-weight: 600;
            font-size: .8rem;
            cursor: pointer;
            transition: all .15s ease;
        }

        .chip:hover {
            border-color: var(--indigo);
            color: var(--indigo);
        }

        .chip.active {
            background: linear-gradient(135deg, var(--indigo), var(--cyan));
            border-color: transparent;
            color: #0b1120;
            box-shadow: 0 6px 16px rgba(6, 182, 212, .25);
        }

        .toolbar-actions {
            display: flex;
            gap: .4rem;
            justify-content: flex-end;
        }

        .btn-grad {
            border: none;
            color: #fff;
            font-weight: 700;
            letter-spacing: .02em;
            background-image: linear-gradient(120deg, var(--indigo-600), var(--cyan));
            border-radius: 999px;
            padding: .42rem 1.05rem;
            box-shadow: 0 8px 20px rgba(6, 182, 212, .32);
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .82rem;
        }

        .btn-grad i {
            font-size: .85rem;
        }

        .btn-grad:hover {
            filter: brightness(.96);
        }

        .btn-reset {
            border-radius: 999px;
            border: 1px dashed var(--ring);
            padding: .42rem .9rem;
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
            background: #fff;
        }

        .btn-reset:hover {
            color: #111827;
            border-color: #9ca3af;
        }

        /* ============================
           Vendor cards section
        ============================ */
        .section-title {
            font-size: .9rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: .35rem;
        }

        .section-sub {
            font-size: .8rem;
            color: var(--muted);
            margin-bottom: .6rem;
        }

        .vendor-card {
            position: relative;
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 14px 15px;
            box-shadow: var(--sh-sm);
            height: 100%;
            cursor: pointer;
            transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
        }

        .vendor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, .14);
            border-color: #c7d2fe;
        }

        .vendor-title {
            font-weight: 700;
            font-size: .92rem;
        }

        .vendor-sub {
            font-size: .8rem;
            color: var(--muted);
        }

        .vendor-amount {
            font-size: 1.15rem;
            font-weight: 800;
        }

        .vendor-chip {
            display: inline-block;
            padding: .2rem .55rem;
            border-radius: 999px;
            background: #eef2ff;
            border: 1px dashed #c7d2fe;
            font-size: .72rem;
            font-weight: 600;
            color: #1e3a8a;
        }

        /* ============================
           Table block
        ============================ */
        .table-shell {
            margin-top: 1rem;
            background: var(--surface);
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: var(--sh-md);
            padding: .9rem 1rem 1.1rem;
        }

        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: .4rem;
        }

        .table-title {
            font-size: .95rem;
            font-weight: 600;
            color: #111827;
        }

        .table-sub {
            font-size: .8rem;
            color: var(--muted);
        }

        .export-table .dataTables_wrapper .dt-buttons .btn {
            margin-left: .25rem;
        }

        .export-table .btn {
            border-radius: 999px !important;
            font-size: .75rem;
            padding: .3rem .7rem;
        }

        .table {
            border-color: var(--border) !important;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: .85rem;
        }

        .table thead th {
            background: #f3f4ff !important;
            border-bottom: 1px solid var(--border) !important;
            color: #020617 !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: .7rem;
            letter-spacing: .06em;
        }

        .table-hover tbody tr:hover {
            background: #f9fbff;
        }

        /* Status badge */
        .status-badge {
            padding: .32rem .7rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .78rem;
            color: #fff;
            border: 1px solid transparent;
            display: inline-block;
        }

        .status-badge--success {
            background: #16a34a;
            border-color: #15803d;
        }

        .status-badge--warning {
            background: #d97706;
            border-color: #b45309;
        }

        .status-badge--danger {
            background: #dc2626;
            border-color: #b91c1c;
        }

        .status-badge--info {
            background: #1d4ed8;
            border-color: #1e40af;
        }

        .status-badge--neutral {
            background: #334155;
            border-color: #1f2937;
        }

        .link-vendor {
            font-weight: 600;
            color: #1d4ed8;
            text-decoration: none;
        }

        .link-vendor:hover {
            text-decoration: underline;
        }

        /* Flower details mini-table */
        .flower-detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .78rem;
        }

        .flower-detail-table thead th {
            border-bottom: 1px solid #e5e7eb;
            padding: 2px 4px;
            font-weight: 700;
            color: #4b5563;
            background: #f9fafb;
        }

        .flower-detail-table tbody td {
            padding: 2px 4px;
            border-bottom: 1px dashed #e5e7eb;
            vertical-align: middle;
        }

        .flower-detail-table tbody tr:last-child td {
            border-bottom: none;
        }

        .flower-detail-table .text-end {
            text-align: right;
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .toolbar-right {
                align-items: flex-start;
            }

            .chip-row {
                justify-content: flex-start;
            }
        }

        @media (max-width: 767.98px) {
            .flower-detail-table {
                font-size: .72rem;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container container-page py-4">

        {{-- Page header --}}
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h4 class="page-header-title mb-0">
                    <i class="bi bi-flower3 me-1"></i> Flower Vendor Pickups
                </h4>
                <div class="page-header-sub">
                    Analyze pickup amounts by vendor, date range, payment mode and status.
                </div>
            </div>
        </div>

        {{-- KPI band --}}
        <div class="band">
            <div class="band-header">
                <div class="band-title">
                    <span>Pickup Revenue Overview</span>
                    <span class="band-pill">Live</span>
                </div>
            </div>
            <div class="band-sub">
                These values are calculated for the currently applied filters (date range, vendor, mode).
            </div>
            <div class="band-chips">
                <span class="band-chip total">
                    <span class="icon">💰</span>
                    <span>Total Price</span>
                    <span class="mono" id="totalPrice">₹{{ number_format((float) $total_price, 2) }}</span>
                </span>
                <span class="band-chip today">
                    <span class="icon">📅</span>
                    <span>Today's Price</span>
                    <span class="mono" id="todayPrice">₹{{ number_format((float) $today_price, 2) }}</span>
                </span>
                <span class="band-chip vendors">
                    <span class="icon">🏷️</span>
                    <span>Vendors in view</span>
                    <span class="mono">{{ isset($vendorSummariesAll) ? count($vendorSummariesAll) : 0 }}</span>
                </span>
            </div>
        </div>

        {{-- Toolbar with filters + quick ranges --}}
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="toolbar-block">
                    <span class="toolbar-label">From Date</span>
                    <input type="date" id="from_date" class="toolbar-input" value="{{ $fromDate }}">
                </div>
                <div class="toolbar-block">
                    <span class="toolbar-label">To Date</span>
                    <input type="date" id="to_date" class="toolbar-input" value="{{ $toDate }}">
                </div>
                <div class="toolbar-block flex-grow-1">
                    <span class="toolbar-label">Vendor Name</span>
                    <select id="vendor_id" class="toolbar-select select2 w-100">
                        <option value="">All Vendors</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="toolbar-block">
                    <span class="toolbar-label">Mode of Payment</span>
                    <select id="payment_mode" class="toolbar-select">
                        <option value="">All</option>
                        <option value="Cash">Cash</option>
                        <option value="Upi">UPI</option>
                    </select>
                </div>
            </div>

            <div class="toolbar-right">
                <div class="chip-row">
                    <button class="chip" data-range="today">Today</button>
                    <button class="chip" data-range="yesterday">Yesterday</button>
                    <button class="chip" data-range="week">This Week</button>
                    <button class="chip" data-range="month">This Month</button>
                    <button class="chip" data-range="last30">Last 30 Days</button>
                    <button class="chip" data-range="fy">FY (Apr–Mar)</button>
                </div>
                <div class="toolbar-actions">
                    <button id="searchBtn" class="btn-grad">
                        <i class="bi bi-funnel"></i> Search
                    </button>
                    <button id="resetBtn" class="btn btn-reset">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Vendor cards --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <div class="section-title">Vendors</div>
                    <div class="section-sub">Click a vendor card to filter the report by that vendor.</div>
                </div>
            </div>

            <div class="row g-3" id="vendorCards">
                @foreach ($vendorSummariesAll ?? [] as $v)
                    <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                        <div class="vendor-card" data-vendor-id="{{ $v['vendor_id'] }}">
                            <div class="vendor-title">{{ $v['vendor_name'] }}</div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div>
                                    <div class="vendor-sub">Total Amount</div>
                                    <div class="vendor-amount">₹{{ number_format($v['total_amount'], 2) }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="vendor-chip">{{ $v['pickups_count'] }} pickups</span>
                                    @if (!empty($v['last_pickup']))
                                        <div class="vendor-sub mt-1">
                                            Last: {{ \Carbon\Carbon::parse($v['last_pickup'])->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if (empty($vendorSummariesAll) || count($vendorSummariesAll) === 0)
                    <div class="col-12">
                        <div class="vendor-card text-center">
                            <span class="vendor-sub">No data available for current filters.</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Table block --}}
        <div class="table-shell export-table">
            <div class="table-header-row">
                <div>
                    <div class="table-title">Pickup Details</div>
                    <div class="table-sub">Flower-wise breakdown per pickup, grouped by vendor & rider.</div>
                </div>
                {{-- DataTables buttons will render to the right via DOM config --}}
            </div>

            <div class="table-responsive">
                <table id="file-datatable" class="table table-bordered table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>Pickup Date</th>
                            <th>Vendor Name</th>
                            <th>Flower Details</th>
                            <th class="text-end">Total Price</th>
                            <th>Status</th>
                            <th>Paid By</th>
                            <th>Rider Name</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        @foreach ($reportData as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->pickup_date)->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $vId = $item->vendor_id;
                                        $vName = $item->vendor->vendor_name ?? '—';
                                    @endphp
                                    @if ($vId && $vName !== '—')
                                        <a href="#" class="link-vendor" data-vendor-id="{{ $vId }}">
                                            {{ $vName }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Flower details mini-table --}}
                                <td>
                                    @if ($item->flowerPickupItems->isNotEmpty())
                                        @php
                                            $rows = $item->flowerPickupItems;
                                        @endphp
                                        <table class="flower-detail-table">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th class="text-end">Qty</th>
                                                    <th class="text-end">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rows as $f)
                                                    @php
                                                        $qty = $f->quantity ?? 0;
                                                        $qtyFormatted = rtrim(rtrim(number_format((float) $qty, 2, '.', ''), '0'), '.');
                                                        $price = $f->price ?? 0;
                                                        $priceFormatted = rtrim(rtrim(number_format((float) $price, 2, '.', ''), '0'), '.');
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $f->flower?->name ?? '—' }}</td>
                                                        <td class="text-end">{{ $qtyFormatted }} {{ $f->unit?->unit_name ?? '' }}</td>
                                                        <td class="text-end">₹{{ $priceFormatted }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="text-end">
                                    ₹{{ number_format((float) $item->total_price, 2) }}
                                </td>

                                <td>
                                    @php
                                        $s = strtolower($item->status ?? '');
                                        $map = [
                                            'success' => 'status-badge--success',
                                            'completed' => 'status-badge--success',
                                            'complete' => 'status-badge--success',
                                            'active' => 'status-badge--success',
                                            'ok' => 'status-badge--success',
                                            'paid' => 'status-badge--success',
                                            'delivered' => 'status-badge--success',
                                            'resume' => 'status-badge--success',
                                            'pending' => 'status-badge--warning',
                                            'processing' => 'status-badge--warning',
                                            'in-progress' => 'status-badge--warning',
                                            'on hold' => 'status-badge--warning',
                                            'hold' => 'status-badge--warning',
                                            'awaiting' => 'status-badge--warning',
                                            'cancel' => 'status-badge--danger',
                                            'cancelled' => 'status-badge--danger',
                                            'failed' => 'status-badge--danger',
                                            'rejected' => 'status-badge--danger',
                                            'expired' => 'status-badge--danger',
                                            'unpaid' => 'status-badge--danger',
                                        ];

                                        if (isset($map[$s])) {
                                            $badgeClass = $map[$s];
                                        } elseif (in_array($s, ['new', 'created', 'open'])) {
                                            $badgeClass = 'status-badge--neutral';
                                        } else {
                                            $badgeClass = 'status-badge--info';
                                        }
                                    @endphp

                                    <span class="status-badge {{ $badgeClass }}">
                                        {{ $item->status ? ucfirst($item->status) : '—' }}
                                    </span>
                                </td>

                                <td>{{ $item->paid_by ? ucfirst($item->paid_by) : '—' }}</td>
                                <td>{{ $item->rider->rider_name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div> {{-- /table-shell --}}
    </div> {{-- /container --}}
@endsection

@section('scripts')
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $.fn.dataTable.ext.errMode = 'none';

        function capFirst(str) {
            return (!str ? '—' : (str.charAt(0).toUpperCase() + str.slice(1)));
        }

        function money(n) {
            n = parseFloat(n || 0);
            return '₹' + n.toFixed(2);
        }

        function trim2(n) {
            return (parseFloat(n || 0)).toFixed(2).replace(/\.?0+$/, '');
        }

        $(function() {
            $('.select2').select2({ width: '100%' });

            const $from = $('#from_date'),
                $to = $('#to_date');

            function applyRange(key) {
                const today = moment().startOf('day');
                let start = today.clone(),
                    end = today.clone();

                switch (key) {
                    case 'today':
                        break;
                    case 'yesterday':
                        start = today.clone().subtract(1, 'day');
                        end = today.clone().subtract(1, 'day');
                        break;
                    case 'week':
                        start = moment().startOf('isoWeek');
                        end = moment().endOf('isoWeek');
                        break;
                    case 'month':
                        start = moment().startOf('month');
                        end = moment().endOf('month');
                        break;
                    case 'last30':
                        start = moment().subtract(29, 'days').startOf('day');
                        end = today.clone();
                        break;
                    case 'fy':
                        const y = moment().year();
                        const fyStart = moment({
                            year: (moment().month() >= 3 ? y : y - 1),
                            month: 3,
                            date: 1
                        }).startOf('day');
                        const fyEnd = fyStart.clone().add(1, 'year').subtract(1, 'day').endOf('day');
                        start = fyStart;
                        end = fyEnd;
                        break;
                }
                $('#from_date').val(start.format('YYYY-MM-DD'));
                $('#to_date').val(end.format('YYYY-MM-DD'));
            }

            $('.chip').on('click', function() {
                $('.chip').removeClass('active');
                $(this).addClass('active');
                applyRange($(this).data('range'));
                $('#searchBtn').trigger('click');
            });

            $('#resetBtn').on('click', function() {
                $from.val('');
                $to.val('');
                $('#vendor_id').val('').trigger('change');
                $('#payment_mode').val('');
                $('.chip').removeClass('active');
                $('#searchBtn').trigger('click');
            });

            function setVendorFilter(vendorId) {
                if (!vendorId) return;
                $('#vendor_id').val(String(vendorId)).trigger('change');
                $('#searchBtn').trigger('click');
            }

            function renderVendorCards(vendorSummaries) {
                const $wrap = $('#vendorCards');
                $wrap.empty();
                if (!vendorSummaries || vendorSummaries.length === 0) {
                    $wrap.append(
                        `<div class="col-12"><div class="vendor-card text-center"><span class="vendor-sub">No data available for current filters.</span></div></div>`
                    );
                    return;
                }
                vendorSummaries.forEach(v => {
                    const name = v.vendor_name || '—',
                        total = money(v.total_amount || 0),
                        count = v.pickups_count || 0,
                        last = v.last_pickup ? moment(v.last_pickup).format('DD MMM YYYY') : '';
                    $wrap.append(`
                        <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                            <div class="vendor-card" data-vendor-id="${v.vendor_id || ''}">
                                <div class="vendor-title">${name}</div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div>
                                        <div class="vendor-sub">Total Amount</div>
                                        <div class="vendor-amount">${total}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="vendor-chip">${count} pickups</span>
                                        ${last ? `<div class="vendor-sub mt-1">Last: ${last}</div>` : ``}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            // click vendor card -> filter
            $(document).on('click', '.vendor-card', function() {
                const id = $(this).data('vendor-id');
                setVendorFilter(id);
            });

            const table = $('#file-datatable').DataTable({
                responsive: true,
                searching: true,
                paging: true,
                info: true,
                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'print', text: 'Print', className: 'btn btn-outline-secondary btn-sm' }
                ],
                columnDefs: [
                    {
                        targets: 3,
                        className: 'text-end'
                    }
                ]
            });

            // click vendor name link inside table -> filter
            $(document).on('click', 'a.link-vendor', function(e) {
                e.preventDefault();
                const id = $(this).data('vendor-id');
                setVendorFilter(id);
            });

            $('#searchBtn').on('click', function() {
                const fromDate = $from.val(),
                    toDate = $to.val(),
                    vendorId = $('#vendor_id').val(),
                    paymentMode = $('#payment_mode').val();

                if (!fromDate || !toDate) {
                    Swal.fire('Warning', 'Please select both from and to dates.', 'warning');
                    return;
                }

                $.ajax({
                    url: '{{ route('report.flower.pickup') }}',
                    type: 'POST',
                    data: {
                        from_date: fromDate,
                        to_date: toDate,
                        vendor_id: vendorId,
                        payment_mode: paymentMode,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.clear();

                        (response.data || []).forEach(item => {
                            const rowsHtml = (item.flower_pickup_items || []).map(i => {
                                const name = (i.flower && i.flower.name) ? i.flower.name : '—';
                                const unit = (i.unit && i.unit.unit_name) ? i.unit.unit_name : '';
                                const qty = trim2(i.quantity);
                                const price = trim2(i.price || 0);
                                return `
                                    <tr>
                                        <td>${name}</td>
                                        <td class="text-end">${qty} ${unit}</td>
                                        <td class="text-end">₹${price}</td>
                                    </tr>
                                `;
                            }).join('');

                            const detailsTable = rowsHtml
                                ? `
                                    <table class="flower-detail-table">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-end">Qty</th>
                                                <th class="text-end">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rowsHtml}
                                        </tbody>
                                    </table>
                                  `
                                : '—';

                            const t = (item.status || '').toString().trim().toLowerCase();
                            let cls = 'status-badge--info';
                            if (['success', 'completed', 'complete', 'active', 'ok', 'paid', 'delivered', 'resume'].includes(t)) {
                                cls = 'status-badge--success';
                            } else if (['pending', 'processing', 'in-progress', 'on hold', 'hold', 'awaiting'].includes(t)) {
                                cls = 'status-badge--warning';
                            } else if (['cancel', 'cancelled', 'failed', 'rejected', 'expired', 'unpaid'].includes(t)) {
                                cls = 'status-badge--danger';
                            } else if (['new', 'created', 'open'].includes(t)) {
                                cls = 'status-badge--neutral';
                            }

                            const vId = (item.vendor && item.vendor.vendor_id) ? item.vendor.vendor_id : item.vendor_id;
                            const vName = (item.vendor && item.vendor.vendor_name) ? item.vendor.vendor_name : null;
                            const vendorCell = (vId && vName)
                                ? `<a href="#" class="link-vendor" data-vendor-id="${vId}">${vName}</a>`
                                : '—';

                            table.row.add([
                                moment(item.pickup_date).isValid()
                                    ? moment(item.pickup_date).format('DD MMM YYYY')
                                    : (item.pickup_date || '—'),
                                vendorCell,
                                detailsTable,
                                money(item.total_price),
                                `<span class="status-badge ${cls}">${capFirst(item.status || '—')}</span>`,
                                capFirst(item.paid_by),
                                (item.rider && item.rider.rider_name) ? item.rider.rider_name : '—'
                            ]);
                        });

                        table.draw(false);

                        // KPIs for filtered result
                        $('#totalPrice').text(money(response.total_price));
                        $('#todayPrice').text(money(response.today_price));

                        renderVendorCards(response.vendor_summaries_all || []);
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to fetch data.', 'error');
                    }
                });
            });
        });
    </script>
@endsection
