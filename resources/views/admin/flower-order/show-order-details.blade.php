@extends('admin.layouts.apps')

@section('styles')
    <!-- Add any required styles -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .card-header {
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
    <style>
        .subscription-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, 0.06);
            padding: 20px;
            max-width: 420px;
            font-family: Arial, sans-serif;
        }

        .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: left;
            color: #333;
        }

        .details {
            line-height: 1.5;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px dashed #eaeaea;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #222;
        }

        .price-row {
            font-size: 1.05em;
            color: #2c3e50;
        }

        .divider {
            margin: 12px 0;
            border-top: 1px solid #eaeaea;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.85em;
            color: #fff;
            text-align: center;
            font-weight: 600;
        }

        .status-running {
            background-color: #28a745;
        }

        .status-expired {
            background-color: #dc3545;
        }

        .status-paused {
            background-color: #ffc107;
            color: #222;
        }

        .note-warning {
            background-color: #fff8e1;
            border: 1px solid #ffe08a;
            padding: 10px;
            border-radius: 8px;
        }

        .text-warning {
            color: #b55b00 !important;
            font-weight: 600;
        }

        /* ===== Delivery Timeline Styles ===== */
        .timeline-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, 0.06);
            padding: 16px 18px;
        }

        .timeline-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .timeline-header .badge {
            border-radius: 999px;
            padding: 6px 10px;
            font-weight: 600;
        }

        .timeline {
            border-left: 3px solid #6c5ce7;
            margin: 10px 0 0 8px;
            padding-left: 18px;
            position: relative;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: -6px;
            top: 0;
            bottom: 0;
            width: 3px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 18px;
            background: #fafbff;
            border: 1px solid #eef0ff;
            border-radius: 10px;
            padding: 12px 14px 12px 14px;
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
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .timeline-title {
            font-size: 15px;
            font-weight: 700;
            margin: 4px 0 6px;
            color: #2b2b2b;
        }

        .timeline-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: #374151;
        }

        .timeline-chip {
            background: #eef2ff;
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 600;
        }

        .timeline-empty {
            background: #fffdf5;
            border: 1px dashed #ffe08a;
            border-radius: 10px;
            padding: 14px;
            color: #946200;
            font-weight: 600;
        }

        .subtle {
            color: #6b7280;
            font-size: 12px;
        }

        .icon-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Booking Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Booking Details</li>
            </ol>
        </div>
    </div>

    <div class="container">
        <div class="row g-3">
            <!-- LEFT: Order & Subscription Summary -->
            <div class="col-md-5">
                <div class="subscription-card">
                    <div class="card-header">Order & Subscription Summary</div>
                    <div class="details">
                        <div class="info-row">
                            <span class="info-label">Order ID:</span>
                            <span class="info-value">{{ $order->order_id }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Product:</span>
                            <span class="info-value">{{ optional($order->flowerProducts)->name ?? '—' }}</span>
                        </div>

                        <div class="info-row price-row">
                            <span class="info-label">Total Price:</span>
                            <span class="info-value">₹
                                {{ number_format(optional($order->order)->total_price ?? 0, 2) }}</span>
                        </div>

                        <div class="divider"></div>

                        @if ($order)
                            <div class="info-row">
                                <span class="info-label">Start Date:</span>
                                <span class="info-value">
                                    {{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M, Y') : '—' }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">End Date:</span>
                                <span class="info-value">
                                    {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M, Y') : '—' }}
                                </span>
                            </div>

                            @if ($order->pauseResumeLogs->count() > 0 && $order->new_date)
                                <div class="info-row note-warning">
                                    <span class="info-label">Note:</span>
                                    <span class="info-value text-warning">
                                        Subscription paused/resumed; extended end date:
                                        {{ \Carbon\Carbon::parse($order->new_date)->format('d M, Y') }}.
                                    </span>
                                </div>
                            @endif

                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                @php
                                    $status = strtolower($order->status ?? '');
                                    $statusClass =
                                        $status === 'active'
                                            ? 'status-running'
                                            : ($status === 'paused'
                                                ? 'status-paused'
                                                : 'status-expired');
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst($order->status ?? '—') }}
                                </span>
                            </div>
                        @else
                            <div class="info-row">
                                <span class="info-label">Subscription:</span>
                                <span class="status-badge status-expired">No active subscription</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT: Pause/Resume Logs + Delivery History -->
            <div class="col-md-7">
                <!-- Pause/Resume Logs -->
                @if ($order->pauseResumeLogs->count() > 0)
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span>Subscription Pause/Resume Logs</span>
                            <span class="subtle">
                                Total: {{ $order->pauseResumeLogs->count() }}
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
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

                <!-- Delivery History (filtered within subscription window) -->
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="icon-circle">🚚</span>
                        <div>
                            <div class="h6 mb-0">Delivery History</div>
                            <div class="subtle">
                                Period:
                                {{ $periodStart ? $periodStart->format('d M, Y') : '—' }}
                                –
                                {{ $periodEnd ? $periodEnd->format('d M, Y') : '—' }}
                                &nbsp;|&nbsp; Deliveries: <span class="badge bg-primary">{{ $totalDeliveries }}</span>
                                @if ($lastStatus)
                                    &nbsp;|&nbsp; Last Status: <span
                                        class="badge bg-info text-dark">{{ $lastStatus }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($deliveries->isEmpty())
                        <div class="timeline-empty">
                            No delivery history found for this subscription period.
                        </div>
                    @else
                        <div class="timeline">
                            @foreach ($deliveries as $d)
                                @php
                                    $created = \Carbon\Carbon::parse($d->created_at);
                                @endphp
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        {{ $created->format('D, d M Y') }} • {{ $created->format('h:i A') }}
                                    </div>
                                    <div class="timeline-title">
                                        {{ $d->delivery_status ?? 'Status —' }}
                                    </div>
                                    <div class="timeline-meta">
                                        @if ($d->rider)
                                            <span class="timeline-chip">Rider:
                                                {{ $d->rider->name ?? $d->rider->rider_id }}</span>
                                        @endif
                                        @if (!is_null($d->latitude) && !is_null($d->longitude))
                                            <a class="timeline-chip text-decoration-none" target="_blank"
                                                href="https://www.google.com/maps?q={{ $d->latitude }},{{ $d->longitude }}">
                                                View Location
                                            </a>
                                        @endif
                                        <span class="subtle">#{{ $d->id }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
@endsection
