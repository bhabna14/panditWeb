@extends('admin.layouts.apps')

@section('styles')
    <!-- Vendor styles -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <!-- Bootstrap Icons (nice micro-icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* ---- Page / Hero ---- */
        .page-hero {
            border-radius: 16px;
            background: linear-gradient(135deg, #6a8dff 0%, #7c4dff 45%, #ff6cab 100%);
            color: #fff;
            padding: 18px 20px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .10);
        }

        .page-hero .crumbs {
            opacity: .95;
            font-size: .95rem;
        }

        /* ---- Sticky Summary Card ---- */
        .subscription-card {
            position: sticky;
            top: 12px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(25, 42, 70, 0.09);
            padding: 18px 18px 10px 18px;
            font-family: Arial, sans-serif;
        }

        .card-header {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1f2937;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mini-actions .btn {
            padding: 4px 8px;
        }

        .details {
            line-height: 1.55;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin: 8px 0;
            padding: 10px 0;
            border-bottom: 1px dashed #eaeaea;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
        }

        .info-value {
            color: #111827;
        }

        .price-row .info-value {
            font-size: 1.1rem;
        }

        .divider {
            margin: 10px 0 2px;
            border-top: 1px solid #eee;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: .85rem;
            color: #fff;
            text-align: center;
            font-weight: 700;
        }

        .status-running {
            background-color: #22c55e;
        }

        .status-expired {
            background-color: #ef4444;
        }

        .status-paused {
            background-color: #f59e0b;
            color: #1f2937;
        }

        .note-warning {
            background: #fff7ed;
            border: 1px solid #ffedd5;
            padding: 10px;
            border-radius: 10px;
        }

        .text-warning {
            color: #b45309 !important;
            font-weight: 600;
        }

        /* ---- Stats row ---- */
        .stat-card {
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            padding: 12px 14px;
        }

        .stat-title {
            color: #6b7280;
            font-weight: 600;
            font-size: .9rem;
        }

        .stat-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #111827;
        }

        /* ---- Filter bar ---- */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .segmented {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 4px;
            display: inline-flex;
            gap: 4px;
        }

        .segmented a {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 8px;
            color: #374151;
            font-weight: 700;
        }

        .segmented a.active {
            background: #111827;
            color: #fff;
        }

        /* ---- Timeline ---- */
        .timeline-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(25, 42, 70, 0.09);
            padding: 14px 16px 18px;
        }

        .timeline-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .timeline-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: #4338ca;
            font-size: 18px;
        }

        .subtle {
            color: #6b7280;
            font-size: .9rem;
        }

        .date-header {
            margin: 12px 0 10px;
            font-weight: 800;
            color: #111827;
            font-size: .95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-header .line {
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .timeline {
            border-left: 3px solid #6c5ce7;
            margin: 4px 0 0 8px;
            padding-left: 18px;
            position: relative;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 16px;
            background: #fafbff;
            border: 1px solid #eef0ff;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .03);
        }

        .timeline-item::before {
            content: "";
            background: #6c5ce7;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #6c5ce7;
            border-radius: 50%;
            height: 14px;
            width: 14px;
            position: absolute;
            left: -28px;
            top: 14px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6b7280;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .timeline-title {
            font-size: 15px;
            font-weight: 800;
            margin: 4px 0 6px;
            color: #111827;
        }

        .timeline-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 13px;
            color: #374151;
        }

        .chip {
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 700;
        }

        .chip-map {
            background: #e6fffa;
            border-color: #b2f5ea;
        }

        .chip-id {
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        .timeline-empty {
            background: #fffdf5;
            border: 1px dashed #ffe08a;
            border-radius: 12px;
            padding: 16px;
            color: #946200;
            font-weight: 700;
            text-align: center;
        }

        /* Utility */
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <!-- Hero -->
    <div class="page-hero mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1"><i class="bi bi-bookmark-star me-2"></i>Booking Details</h5>
                <div class="crumbs">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    <span class="mx-1">/</span>
                    <span>Booking Details</span>
                </div>
            </div>

            <!-- Quick stats -->
            <div class="d-none d-md-flex gap-2">
                <div class="stat-card text-end">
                    <div class="stat-title">Deliveries ({{ $range === 'all' ? 'All' : 'This Period' }})</div>
                    <div class="stat-value">{{ $totalDeliveries }}</div>
                </div>
                <div class="stat-card text-end">
                    <div class="stat-title">Last Status</div>
                    <div class="stat-value">{{ $lastStatus ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container px-0">
        <div class="row g-3">
            <!-- LEFT: Sticky Summary -->
            <div class="col-lg-5">
                <div class="subscription-card">
                    <div class="card-header">
                        <span><i class="bi bi-receipt-cutoff me-2"></i>Order & Subscription Summary</span>
                        <div class="mini-actions">
                            <button class="btn btn-sm btn-outline-secondary" id="copyOrderId"
                                data-id="{{ $order->order_id }}">
                                <i class="bi bi-clipboard"></i> Copy ID
                            </button>
                        </div>
                    </div>

                    <div class="details">
                        <div class="info-row">
                            <span class="info-label">Order ID</span>
                            <span class="info-value">{{ $order->order_id }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Product</span>
                            <span class="info-value">{{ optional($order->flowerProducts)->name ?? '—' }}</span>
                        </div>

                        <div class="info-row price-row">
                            <span class="info-label">Total Price</span>
                            <span class="info-value">₹
                                {{ number_format(optional($order->order)->total_price ?? 0, 2) }}</span>
                        </div>

                        <div class="divider"></div>

                        @if ($order)
                            <div class="info-row">
                                <span class="info-label">Start Date</span>
                                <span class="info-value">
                                    {{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M, Y') : '—' }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">End Date</span>
                                <span class="info-value">
                                    {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M, Y') : '—' }}
                                </span>
                            </div>

                            @if ($order->pauseResumeLogs->count() > 0 && $order->new_date)
                                <div class="info-row note-warning">
                                    <span class="info-label"><i class="bi bi-info-circle"></i> Note</span>
                                    <span class="info-value text-warning">
                                        Subscription paused/resumed; extended end date:
                                        {{ \Carbon\Carbon::parse($order->new_date)->format('d M, Y') }}.
                                    </span>
                                </div>
                            @endif

                            <div class="info-row">
                                <span class="info-label">Status</span>
                                @php
                                    $status = strtolower($order->status ?? '');
                                    $statusClass =
                                        $status === 'active'
                                            ? 'status-running'
                                            : ($status === 'paused'
                                                ? 'status-paused'
                                                : 'status-expired');
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status ?? '—') }}</span>
                            </div>
                        @else
                            <div class="info-row">
                                <span class="info-label">Subscription</span>
                                <span class="status-badge status-expired">No active subscription</span>
                            </div>
                        @endif
                    </div>

                    <!-- Status counts mini row -->
                    @if (!empty($statusCounts) && count($statusCounts))
                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            @foreach ($statusCounts as $st => $cnt)
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-truck me-1"></i>{{ $st ?? 'Unknown' }}: <b>{{ $cnt }}</b>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- RIGHT: Logs + Delivery -->
            <div class="col-lg-7">

                <!-- Pause/Resume Logs -->
                @if ($order->pauseResumeLogs->count() > 0)
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header d-flex align-items-center justify-content-between bg-white">
                            <span><i class="bi bi-pause-circle me-2"></i>Subscription Pause/Resume Logs</span>
                            <span class="subtle">Total: {{ $order->pauseResumeLogs->count() }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Action</th>
                                        <th>Pause Start</th>
                                        <th>Pause End</th>
                                        <th>Resume</th>
                                        <th>New End</th>
                                        <th>Paused Days</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->pauseResumeLogs as $log)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ ucfirst($log->action) }}</span></td>
                                            <td>{{ $log->pause_start_date ? \Carbon\Carbon::parse($log->pause_start_date)->format('d M, Y') : 'N/A' }}
                                            </td>
                                            <td>{{ $log->pause_end_date ? \Carbon\Carbon::parse($log->pause_end_date)->format('d M, Y') : 'N/A' }}
                                            </td>
                                            <td>{{ $log->resume_date ? \Carbon\Carbon::parse($log->resume_date)->format('d M, Y') : 'N/A' }}
                                            </td>
                                            <td>{{ $log->new_end_date ? \Carbon\Carbon::parse($log->new_end_date)->format('d M, Y') : '—' }}
                                            </td>
                                            <td>{{ $log->paused_days }} days</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Delivery History -->
                <div class="timeline-card">
                    <div class="timeline-header">
                        <div class="timeline-title-row">
                            <span class="icon-circle"><i class="bi bi-truck"></i></span>
                            <div>
                                <div class="h6 mb-0">Delivery History</div>
                                <div class="subtle">
                                    Period:
                                    {{ $periodStart ? $periodStart->format('d M, Y') : '—' }}
                                    –
                                    {{ $periodEnd ? $periodEnd->format('d M, Y') : '—' }}
                                    &nbsp;|&nbsp; Deliveries:
                                    <span class="badge bg-primary">{{ $totalDeliveries }}</span>
                                    @if ($lastStatus)
                                        &nbsp;|&nbsp; Last Status:
                                        <span class="badge bg-info text-dark">{{ $lastStatus }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="filter-bar">
                            <div class="segmented">
                                <a href="{{ request()->fullUrlWithQuery(['range' => 'period']) }}"
                                    class="{{ $range === 'period' ? 'active' : '' }}">
                                    <i class="bi bi-calendar-range me-1"></i>This Period
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['range' => 'all']) }}"
                                    class="{{ $range === 'all' ? 'active' : '' }}">
                                    <i class="bi bi-infinity me-1"></i>All Time
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($deliveries->isEmpty())
                        <div class="timeline-empty">
                            <i class="bi bi-inboxes me-1"></i> No delivery history found for the selected range.
                        </div>
                    @else
                        @foreach ($groupedDeliveries as $ymd => $items)
                            @php $heading = \Carbon\Carbon::parse($ymd)->format('D, d M Y'); @endphp
                            <div class="date-header">
                                <i class="bi bi-calendar3"></i> {{ $heading }}
                                <div class="line"></div>
                            </div>

                            <div class="timeline">
                                @foreach ($items as $d)
                                    @php $created = \Carbon\Carbon::parse($d->created_at); @endphp
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            {{ $created->format('h:i A') }}
                                        </div>
                                        <div class="timeline-title">
                                            {{ $d->delivery_status ?? 'Status —' }}
                                        </div>
                                        <div class="timeline-meta">
                                            @if ($d->rider)
                                                <span class="chip">
                                                    <i class="bi bi-person-badge me-1"></i>
                                                    Rider: {{ $d->rider->name ?? $d->rider->rider_id }}
                                                </span>
                                            @endif

                                            @if (!is_null($d->latitude) && !is_null($d->longitude))
                                                <a class="chip chip-map text-decoration-none" target="_blank"
                                                    href="https://www.google.com/maps?q={{ $d->latitude }},{{ $d->longitude }}">
                                                    <i class="bi bi-geo me-1"></i>View Location
                                                </a>
                                            @endif

                                            <span class="chip chip-id">
                                                <i class="bi bi-hash me-1"></i>{{ $d->id }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Vendors -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        // Copy Order ID
        document.getElementById('copyOrderId')?.addEventListener('click', function() {
            const id = this.getAttribute('data-id') || '';
            if (!id) return;
            navigator.clipboard.writeText(id).then(() => {
                this.innerHTML = '<i class="bi bi-clipboard-check"></i> Copied';
                setTimeout(() => this.innerHTML = '<i class="bi bi-clipboard"></i> Copy ID', 1200);
            });
        });
    </script>
@endsection
