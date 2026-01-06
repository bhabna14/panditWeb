@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .page-hero {
            border-radius: 16px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 55%, #22c55e 100%);
            color: #fff;
            padding: 16px 18px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .14);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .6rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .28);
            font-size: .8rem;
            color: #fff;
        }

        .filter-card {
            border: 1px solid #e7ebf0;
            border-radius: 14px
        }

        .filter-card .card-body {
            padding: 1rem 1.25rem
        }

        .quick-chip {
            border: 1px dashed #cfd8e3;
            border-radius: 9999px;
            padding: .35rem .75rem;
            font-size: .825rem;
            cursor: pointer;
            user-select: none
        }

        .quick-chip:hover {
            background: #f8fafc
        }

        .quick-chip.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd
        }

        table.dataTable thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2
        }

        .table-hover tbody tr:hover {
            background-color: #f7faff;
        }

        .badge-status {
            font-weight: 600;
            font-size: .78rem
        }

        .addr {
            white-space: normal;
            line-height: 1.25rem
        }

        .addr small {
            color: #64748b
        }

        .text-xs {
            font-size: .75rem
        }

        .text-xxs {
            font-size: .68rem
        }

        .fw-600 {
            font-weight: 600
        }

        .nowrap {
            white-space: nowrap
        }

        .metric-card {
            background: #fff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            transition: .2s
        }

        .metric-card:hover {
            box-shadow: 0 12px 26px rgba(16, 24, 40, .06);
            transform: translateY(-2px)
        }

        .metric-card .value {
            font-size: 1.25rem;
            font-weight: 700
        }

        .metric-card .label {
            font-size: .8rem;
            color: #64748b
        }

        .nav-tabs .nav-link {
            font-weight: 700;
            border-radius: 12px 12px 0 0;
        }

        .tab-pane {
            padding-top: 14px;
        }
    </style>
@endsection

@section('content')
    <!-- Hero -->
    <div class="page-hero mb-3 mt-2 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="mb-1">Manage Delivery History</h5>
            <div class="opacity-90">Two tabs: Order Delivery History and Customize Delivery History.</div>
        </div>
        <span class="pill">
            Updated • {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">{{ session('success') }}</div>
    @endif
    @if ($errors->has('error'))
        <div class="alert alert-danger" id="Message">{{ $errors->first('error') }}</div>
    @endif

    <!-- Summary metrics (both) -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-2">
            <div class="metric-card p-3 h-100">
                <div class="label">Orders (range)</div>
                <div class="value">{{ number_format($metricsOrder['total'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="metric-card p-3 h-100">
                <div class="label">Orders Delivered</div>
                <div class="value text-success">{{ number_format($metricsOrder['delivered'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="metric-card p-3 h-100">
                <div class="label">Order Riders</div>
                <div class="value">{{ number_format($metricsOrder['unique_riders'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-6 col-md-2">
            <div class="metric-card p-3 h-100">
                <div class="label">Customize (range)</div>
                <div class="value">{{ number_format($metricsCustomize['total'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="metric-card p-3 h-100">
                <div class="label">Customize Delivered</div>
                <div class="value text-success">{{ number_format($metricsCustomize['delivered'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="metric-card p-3 h-100">
                <div class="label">Customize Riders</div>
                <div class="value">{{ number_format($metricsCustomize['unique_riders'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.managedeliveryhistory') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="form-control"
                            value="{{ old('from_date', $from_date ?? request('from_date')) }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="form-control"
                            value="{{ old('to_date', $to_date ?? request('to_date')) }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="rider_id" class="form-label">Rider</label>
                        <select id="rider_id" name="rider_id" class="form-select">
                            <option value="">All Riders</option>
                            @foreach ($riders as $rider)
                                <option value="{{ $rider->rider_id }}"
                                    {{ request('rider_id') == $rider->rider_id ? 'selected' : '' }}>
                                    {{ $rider->rider_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <button type="button" class="btn btn-outline-secondary mt-2" id="resetFilters">Reset</button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="quick-chip" data-range="today">Today</span>
                    <span class="quick-chip" data-range="yesterday">Yesterday</span>
                    <span class="quick-chip" data-range="last_7_days">Last 7 Days</span>
                    <span class="quick-chip" data-range="this_week">This Week</span>
                    <span class="quick-chip" data-range="this_month">This Month</span>
                    <span class="quick-chip" data-range="last_month">Last Month</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="deliveryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#ordersPane" type="button"
                role="tab" aria-controls="ordersPane" aria-selected="true">
                Order Delivery History (Today: {{ number_format($totalDeliveriesToday ?? 0) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customize-tab" data-bs-toggle="tab" data-bs-target="#customizePane"
                type="button" role="tab" aria-controls="customizePane" aria-selected="false">
                Customize Delivery History (Today: {{ number_format($totalCustomizeDeliveriesToday ?? 0) }})
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB 1: Orders -->
        <div class="tab-pane fade show active" id="ordersPane" role="tabpanel" aria-labelledby="orders-tab">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="orders-delivery-table" class="table table-bordered table-hover w-100 align-middle">
                            <thead>
                                <tr>
                                    <th data-priority="1">Order ID</th>
                                    <th data-priority="2">User Number</th>
                                    <th>Product</th>
                                    <th style="min-width:260px">Address</th>
                                    <th>Rider</th>
                                    <th data-priority="3">Status</th>
                                    <th>Location</th>
                                    <th data-priority="4">Delivery Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($deliveryHistory as $history)
                                    @php
                                        $order = optional($history->order);
                                        $user = optional($order->user);
                                        $product = optional($order->flowerProduct);
                                        $addr = optional($order->address);
                                        $locName = optional(optional($addr)->localityDetails)->locality_name;
                                        $rider = optional($history->rider);
                                        $lat = $history->latitude;
                                        $lng = $history->longitude;

                                        $status = trim($history->delivery_status ?? '');
                                        $statusLc = strtolower($status);
                                        $badge = 'secondary';
                                        if (in_array($statusLc, ['delivered', 'completed'])) {
                                            $badge = 'success';
                                        } elseif (
                                            in_array($statusLc, [
                                                'in_transit',
                                                'out_for_delivery',
                                                'dispatch',
                                                'shipped',
                                            ])
                                        ) {
                                            $badge = 'info';
                                        } elseif (in_array($statusLc, ['pending', 'awaiting'])) {
                                            $badge = 'warning';
                                        } elseif (in_array($statusLc, ['cancelled', 'canceled', 'failed'])) {
                                            $badge = 'danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="nowrap fw-600">{{ $order->order_id ?? 'N/A' }}</td>
                                        <td>{{ $user->mobile_number ?? 'N/A' }}</td>
                                        <td>
                                            <div class="fw-600">{{ $product->name ?? 'N/A' }}</div>
                                            @if (!empty($product->category))
                                                <div class="text-xxs text-muted">{{ $product->category }}</div>
                                            @endif
                                        </td>
                                        <td class="addr">
                                            @if ($addr)
                                                <div class="fw-600">
                                                    {{ $addr->apartment_flat_plot ?? '' }}{{ $addr->apartment_flat_plot && $locName ? ',' : '' }}
                                                    {{ $locName ?? '' }}
                                                </div>
                                                @if (!empty($addr->landmark))
                                                    <div><small>Landmark:</small> {{ $addr->landmark }}</div>
                                                @endif
                                                <div class="text-xs text-muted">
                                                    {{ $addr->city ?? '' }}{{ !empty($addr->state) ? ', ' . $addr->state : '' }}
                                                    {{ !empty($addr->pincode) ? ' - ' . $addr->pincode : '' }}
                                                </div>
                                            @else
                                                <span class="text-muted text-xs">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $rider->rider_name ?? 'N/A' }}</td>
                                        <td><span
                                                class="badge bg-{{ $badge }} badge-status">{{ $status ?: 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if (!empty($lat) && !empty($lng))
                                                <div class="text-xs"><span class="fw-600">{{ $lat }},
                                                        {{ $lng }}</span></div>
                                                <div class="text-xxs mt-1">
                                                    <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
                                                        target="_blank" rel="noopener">Open in Maps</a>
                                                    &nbsp;·&nbsp;
                                                    <a href="#" class="copy-coords"
                                                        data-coords="{{ $lat }}, {{ $lng }}">Copy</a>
                                                </div>
                                            @else
                                                <span class="text-muted text-xs">N/A</span>
                                            @endif
                                        </td>
                                        <td data-order="{{ optional($history->created_at)->timestamp ?? 0 }}"
                                            class="nowrap">
                                            {{ optional($history->created_at)->format('d-m-Y H:i:s') ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No order delivery history
                                            found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Customize -->
        <div class="tab-pane fade" id="customizePane" role="tabpanel" aria-labelledby="customize-tab">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="customize-delivery-table" class="table table-bordered table-hover w-100 align-middle">
                            <thead>
                                <tr>
                                    <th data-priority="1">Request ID</th>
                                    <th data-priority="2">User Number</th>
                                    <th>Product</th>
                                    <th style="min-width:260px">Address</th>
                                    <th>Rider</th>
                                    <th data-priority="3">Status</th>
                                    <th>Location</th>
                                    <th data-priority="4">Delivery Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customizeDeliveryHistory as $history)
                                    @php
                                        $req = optional($history->flowerRequest);
                                        $user = optional($req->user);
                                        $product = optional($req->flowerProduct);
                                        $addr = optional($req->address);
                                        $locName = optional(optional($addr)->localityDetails)->locality_name;
                                        $rider = optional($history->rider);
                                        $lat = $history->latitude;
                                        $lng = $history->longitude;

                                        $status = trim($history->delivery_status ?? '');
                                        $statusLc = strtolower($status);
                                        $badge = 'secondary';
                                        if (in_array($statusLc, ['delivered', 'completed'])) {
                                            $badge = 'success';
                                        } elseif (
                                            in_array($statusLc, [
                                                'in_transit',
                                                'out_for_delivery',
                                                'dispatch',
                                                'shipped',
                                            ])
                                        ) {
                                            $badge = 'info';
                                        } elseif (in_array($statusLc, ['pending', 'awaiting'])) {
                                            $badge = 'warning';
                                        } elseif (in_array($statusLc, ['cancelled', 'canceled', 'failed'])) {
                                            $badge = 'danger';
                                        }

                                        $dt = $history->delivery_time ?? $history->created_at;
                                    @endphp
                                    <tr>
                                        <td class="nowrap fw-600">{{ $history->request_id ?? 'N/A' }}</td>
                                        <td>{{ $user->mobile_number ?? 'N/A' }}</td>
                                        <td>
                                            <div class="fw-600">{{ $product->name ?? 'N/A' }}</div>
                                            @if (!empty($product->category))
                                                <div class="text-xxs text-muted">{{ $product->category }}</div>
                                            @endif
                                        </td>
                                        <td class="addr">
                                            @if ($addr)
                                                <div class="fw-600">
                                                    {{ $addr->apartment_flat_plot ?? '' }}{{ $addr->apartment_flat_plot && $locName ? ',' : '' }}
                                                    {{ $locName ?? '' }}
                                                </div>
                                                @if (!empty($addr->landmark))
                                                    <div><small>Landmark:</small> {{ $addr->landmark }}</div>
                                                @endif
                                                <div class="text-xs text-muted">
                                                    {{ $addr->city ?? '' }}{{ !empty($addr->state) ? ', ' . $addr->state : '' }}
                                                    {{ !empty($addr->pincode) ? ' - ' . $addr->pincode : '' }}
                                                </div>
                                            @else
                                                <span class="text-muted text-xs">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $rider->rider_name ?? 'N/A' }}</td>
                                        <td><span
                                                class="badge bg-{{ $badge }} badge-status">{{ $status ?: 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if (!empty($lat) && !empty($lng))
                                                <div class="text-xs"><span class="fw-600">{{ $lat }},
                                                        {{ $lng }}</span></div>
                                                <div class="text-xxs mt-1">
                                                    <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
                                                        target="_blank" rel="noopener">Open in Maps</a>
                                                    &nbsp;·&nbsp;
                                                    <a href="#" class="copy-coords"
                                                        data-coords="{{ $lat }}, {{ $lng }}">Copy</a>
                                                </div>
                                            @else
                                                <span class="text-muted text-xs">N/A</span>
                                            @endif
                                        </td>
                                        <td data-order="{{ optional($dt)->timestamp ?? 0 }}" class="nowrap">
                                            {{ optional($dt)->format('d-m-Y H:i:s') ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No customize delivery
                                            history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- jQuery & DataTables -->
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

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        (function() {
            $('#rider_id').select2({
                placeholder: 'All Riders',
                allowClear: true,
                width: '100%'
            });

            function toISODate(d) {
                const tzOffset = d.getTimezoneOffset() * 60000;
                return new Date(d.getTime() - tzOffset).toISOString().slice(0, 10);
            }

            function setRange(type) {
                const now = new Date();
                let from = new Date(),
                    to = new Date();
                switch (type) {
                    case 'today':
                        break;
                    case 'yesterday':
                        from.setDate(now.getDate() - 1);
                        to.setDate(now.getDate() - 1);
                        break;
                    case 'last_7_days':
                        from.setDate(now.getDate() - 6);
                        break;
                    case 'this_week': {
                        const day = now.getDay();
                        const diff = (day === 0 ? 6 : day - 1);
                        from.setDate(now.getDate() - diff);
                        break;
                    }
                    case 'this_month':
                        from = new Date(now.getFullYear(), now.getMonth(), 1);
                        to = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        break;
                    case 'last_month':
                        from = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        to = new Date(now.getFullYear(), now.getMonth(), 0);
                        break;
                }
                $('#from_date').val(toISODate(from));
                $('#to_date').val(toISODate(to));
            }

            $('.quick-chip').on('click', function() {
                $('.quick-chip').removeClass('active');
                $(this).addClass('active');
                setRange($(this).data('range'));
                $('#filterForm').trigger('submit');
            });

            $('#resetFilters').on('click', function() {
                const now = new Date();
                const seven = new Date();
                seven.setDate(now.getDate() - 6);
                $('#from_date').val(toISODate(seven));
                $('#to_date').val(toISODate(now));
                $('#rider_id').val('').trigger('change');
                $('.quick-chip').removeClass('active');
                $('.quick-chip[data-range="last_7_days"]').addClass('active');
                $('#filterForm').trigger('submit');
            });

            function dtButtons(title) {
                return [{
                        extend: 'copyHtml5',
                        title
                    },
                    {
                        extend: 'csvHtml5',
                        title: title.toLowerCase().replace(/\s+/g, '_')
                    },
                    {
                        extend: 'excelHtml5',
                        title: title.toLowerCase().replace(/\s+/g, '_')
                    },
                    {
                        extend: 'pdfHtml5',
                        title,
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        title
                    },
                    {
                        extend: 'colvis',
                        text: 'Columns'
                    }
                ];
            }

            const ordersDT = $('#orders-delivery-table').DataTable({
                responsive: true,
                stateSave: true,
                pageLength: 25,
                order: [
                    [7, 'desc']
                ],
                dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
                buttons: dtButtons('Order Delivery History'),
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ deliveries"
                }
            });

            const customizeDT = $('#customize-delivery-table').DataTable({
                responsive: true,
                stateSave: true,
                pageLength: 25,
                order: [
                    [7, 'desc']
                ],
                dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
                buttons: dtButtons('Customize Delivery History'),
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ deliveries"
                }
            });

            // Fix DataTables width when switching tabs
            document.getElementById('deliveryTabs').addEventListener('shown.bs.tab', function() {
                ordersDT.columns.adjust().responsive.recalc();
                customizeDT.columns.adjust().responsive.recalc();
            });

            // Copy coordinates
            $(document).on('click', '.copy-coords', function(e) {
                e.preventDefault();
                const coords = $(this).data('coords');
                navigator.clipboard.writeText(coords).then(() => {
                    const btn = $(this);
                    const old = btn.text();
                    btn.text('Copied!');
                    setTimeout(() => btn.text(old), 1200);
                });
            });
        })();
    </script>
@endsection
