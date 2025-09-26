@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        /* ===== Shell / Page ===== */
        .page-hero {
            border-radius: 16px;
            background: linear-gradient(135deg, #f7f7ff 0%, #eef7ff 100%);
            border: 1px solid #e8ecf4;
            padding: 18px 20px;
            box-shadow: 0 10px 24px rgba(25, 42, 70, .06)
        }

        .hero-title {
            font-weight: 800;
            margin: 0
        }

        .subtle {
            color: #6b7280
        }

        .chip {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            background: #fff;
            border: 1px solid #e8ecf4;
            border-radius: 999px;
            padding: 6px 10px;
            font-weight: 600;
            font-size: .85rem
        }

        /* ===== Vendor card ===== */
        .vendor-wrap {
            display: flex;
            gap: 16px;
            align-items: center
        }

        .avatar {
            width: 78px;
            height: 78px;
            border-radius: 16px;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
            background: #f3f4f6
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .vendor-name {
            font-weight: 800;
            margin: 0
        }

        /* ===== Stats ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px
        }

        @media (max-width:991.98px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        @media (max-width:575.98px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e9ecf5;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, .05)
        }

        .stat-card .label {
            color: #6b7280;
            font-size: .82rem;
            margin-bottom: 4px
        }

        .stat-card .value {
            font-weight: 900;
            font-size: 1.2rem
        }

        /* ===== Table ===== */
        .table-shell {
            border: 1px solid #e9ecf5;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(25, 42, 70, .06)
        }

        table.dataTable thead th {
            background: #f8fafc;
            font-weight: 800;
            border-bottom: 1px solid #edf2f7
        }

        table.dataTable tbody td {
            vertical-align: top
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background: rgba(249, 250, 251, .5)
        }

        .badge-soft {
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 800;
            font-size: .72rem
        }

        .badge-soft-success {
            background: #ecfdf5;
            color: #0f766e;
            border: 1px solid #a7f3d0
        }

        .badge-soft-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca
        }

        .badge-soft-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a
        }

        details.items {
            background: #fcfcff;
            border: 1px dashed #e5e7eb;
            border-radius: 10px;
            padding: 10px
        }

        details.items summary {
            cursor: pointer;
            font-weight: 800;
            user-select: none;
            margin-bottom: 6px
        }

        /* ===== Date group header ===== */
        .date-group {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #eef6ff;
            border-top: 1px solid #dbeafe;
            border-bottom: 1px solid #dbeafe
        }

        .date-chip {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            background: #fff;
            border: 1px solid #dbeafe;
            border-radius: 999px;
            padding: .25rem .6rem;
            font-weight: 700
        }

        .group-totals {
            font-weight: 700
        }

        /* ===== Toolbar ===== */
        .toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin: 10px 0
        }

        .btn-filter {
            border: 1px solid #d1d5db;
            background: #fff;
            border-radius: 999px;
            padding: .35rem .8rem;
            font-weight: 700
        }

        .btn-filter.active {
            background: #111827;
            color: #fff;
            border-color: #111827
        }

        /* ===== Empty state ===== */
        .empty {
            border: 1px dashed #d1d5db;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            background: #fafafa
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content d-flex align-items-center flex-nowrap">
            <a class="btn btn-primary me-3" href="{{ url('admin/manage-vendor-details') }}">Back</a>
            <span class="main-content-title" style="margin-top:36px">Vendor Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Vendor Details</li>
            </ol>
        </div>
    </div>

    <div class="page-hero mt-3">
        <div class="row g-3 align-items-center">
            <div class="col-lg-6">
                <div class="vendor-wrap">
                    <div class="avatar">
                        <img src="{{ asset('images/profile.jpeg') }}" alt="Vendor">
                    </div>
                    <div>
                        <h4 class="vendor-name">{{ $vendor->vendor_name ?? 'N/A' }}</h4>
                        <div class="subtle"><i class="fa fa-phone me-2"></i>{{ $vendor->phone_no ?? 'N/A' }}</div>
                        <div class="mt-2 chip">Vendor ID: <span>{{ $vendor->id ?? '—' }}</span></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="label">Total Pickups</div>
                        <div class="value">{{ number_format($summary['total_pickups'] ?? 0) }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Total Items</div>
                        <div class="value">{{ number_format($summary['total_items'] ?? 0) }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Paid</div>
                        <div class="value">{{ number_format($summary['paid_count'] ?? 0) }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Unpaid</div>
                        <div class="value">{{ number_format($summary['unpaid_count'] ?? 0) }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Total Amount</div>
                        <div class="value">₹{{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2 subtle">Last Pickup: <strong>{{ $summary['last_pickup_date'] ?? '—' }}</strong></div>
            </div>
        </div>
    </div>

    @if (($pickups->count() ?? 0) === 0)
        <div class="empty mt-3">
            <div class="mb-1" style="font-weight:800;font-size:1.05rem">No pickup records yet</div>
            <div class="subtle">When this vendor has pickups, they’ll appear here with totals and export options.</div>
        </div>
    @else
        <div class="card custom-card mt-3 table-shell">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Vendor History</h4>
                <div class="chip">Responsive • Grouped by date • Export</div>
            </div>

            <div class="card-body">
                <div class="toolbar">
                    <div class="subtle">Showing {{ number_format($pickups->count()) }} records</div>
                    <div class="d-flex gap-2">
                        <button class="btn-filter active" data-filter="all" type="button">All</button>
                        <button class="btn-filter" data-filter="Paid" type="button">Paid</button>
                        <button class="btn-filter" data-filter="Unpaid" type="button">Unpaid</button>
                    </div>
                </div>

                <div class="table-responsive export-table">
                    <table id="vendor-history" class="table table-bordered table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th style="width:56px">#</th>
                                <th>Vendor</th>
                                <th>Rider</th>
                                <th>Flower Details</th>
                                <th>Pickup Date</th>
                                <th>Total Price</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $row=0; @endphp
                            @foreach ($pickupDetails as $dateKey => $group)
                                @php
                                    // Friendly date header + per-day totals
                                    try {
                                        $friendly = \Carbon\Carbon::parse($dateKey)->format('d M Y');
                                    } catch (\Throwable $e) {
                                        $friendly = $dateKey;
                                    }

                                    $dayTotal = $group->sum(function ($p) {
                                        if (!is_null($p->total_price) && $p->total_price !== '') {
                                            return (float) $p->total_price;
                                        }
                                        return optional($p->flowerPickupItems)->sum(fn($i) => (float) ($i->price ?? 0));
                                    });
                                    $dayPaid = $group->where('payment_status', 'Paid')->count();
                                    $dayUnpaid = $group->count() - $dayPaid;
                                @endphp

                                <!-- Sticky date group header row -->
                                <tr class="date-group">
                                    <td colspan="8">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="date-chip">{{ $friendly }}</div>
                                            <div class="group-totals subtle">
                                                Day Total: <strong>₹{{ number_format($dayTotal, 2) }}</strong> ·
                                                Paid: <strong>{{ $dayPaid }}</strong> ·
                                                Unpaid: <strong>{{ $dayUnpaid }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($group->sortByDesc('created_at') as $detail)
                                    @php
                                        $row++;
                                        $items = $detail->flowerPickupItems ?? collect();
                                        $rowTotal = $detail->total_price;
                                        if (is_null($rowTotal) || $rowTotal === '') {
                                            $rowTotal = $items->sum(fn($i) => (float) ($i->price ?? 0));
                                        }
                                        try {
                                            $friendlyDate = \Carbon\Carbon::parse($detail->pickup_date)->format(
                                                'd M Y',
                                            );
                                        } catch (\Throwable $e) {
                                            $friendlyDate = $detail->pickup_date ?? 'N/A';
                                        }

                                        $paidTag =
                                            trim((string) $detail->payment_status) === 'Paid' ? 'Paid' : 'Unpaid';
                                    @endphp
                                    <tr data-paid="{{ $paidTag }}">
                                        <td>{{ $row }}</td>
                                        <td>{{ $detail->vendor?->vendor_name ?? 'N/A' }}</td>
                                        <td>{{ $detail->rider?->rider_name ?? 'N/A' }}</td>
                                        <td>
                                            <details class="items">
                                                <summary>{{ $items->count() }} item(s)</summary>
                                                @if ($items->count())
                                                    <ul class="mb-0 ps-3">
                                                        @foreach ($items as $i)
                                                            <li class="mb-2">
                                                                <div><strong>Flower:</strong>
                                                                    {{ $i->flower?->name ?? 'N/A' }}</div>
                                                                <div><strong>Qty:</strong> {{ $i->quantity ?? 'N/A' }}
                                                                    {{ $i->unit?->unit_name ?? '' }}</div>
                                                                <div><strong>Price:</strong>
                                                                    ₹{{ number_format((float) ($i->price ?? 0), 2) }}</div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em class="subtle">No flower pickup items.</em>
                                                @endif
                                            </details>
                                        </td>
                                        <td>{{ $friendlyDate }}</td>
                                        <td>
                                            @if ($rowTotal && $rowTotal > 0)
                                                ₹{{ number_format($rowTotal, 2) }}
                                            @else
                                                <span class="badge-soft badge-soft-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($paidTag === 'Paid')
                                                <span class="badge-soft badge-soft-success">Paid</span>
                                            @else
                                                <span class="badge-soft badge-soft-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>{{ $detail->status ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>

    <!-- DataTables -->
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

    <script>
        $(function() {
            // init DataTable
            const table = $('#vendor-history').DataTable({
                responsive: true,
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                // We inserted date header rows (class=date-group). Tell DT to not order those (it ignores automatically),
                // and sort rows by actual Pickup Date then #.
                order: [
                    [4, 'desc'],
                    [0, 'asc']
                ],
                dom: 'Bflrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'csvHtml5',
                        className: 'btn btn-outline-secondary btn-sm',
                        title: 'vendor-history'
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-outline-secondary btn-sm',
                        title: 'vendor-history'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-outline-secondary btn-sm',
                        title: 'vendor-history',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'colvis',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                ],
                // Prevent date-group rows from being treated as data rows
                rowCallback: function(row, data, index) {
                    if ($(row).hasClass('date-group')) {
                        // DataTables sometimes clones nodes; ensure header rows stay full-width
                        $(row).attr('role', 'rowgroup');
                    }
                }
            });

            // Quick filters (All / Paid / Unpaid) looking at Payment Status column (index 6)
            function setFilter(val) {
                if (val === 'all') {
                    table.column(6).search('').draw();
                    return;
                }
                table.column(6).search(val, true, false).draw(); // exact-ish
            }
            $('.btn-filter').on('click', function() {
                $('.btn-filter').removeClass('active');
                $(this).addClass('active');
                const mode = $(this).data('filter');
                setFilter(mode === 'all' ? 'all' : mode);
            });
        });
    </script>
@endsection
