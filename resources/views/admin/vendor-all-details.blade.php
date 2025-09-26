@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        /* ======= Page polish ======= */
        .page-hero {
            border-radius: 16px;
            background: linear-gradient(135deg, #eef2ff 0%, #e9fbff 100%);
            border: 1px solid #e9ecf5;
            padding: 18px 20px;
            margin-top: 6px;
            box-shadow: 0 8px 22px rgba(25, 42, 70, .06);
        }

        .hero-title {
            font-weight: 700;
            margin: 0;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #fff;
            font-weight: 600;
            font-size: .85rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }

        @media (max-width: 991.98px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 575.98px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .summary-card {
            background: #fff;
            border: 1px solid #e9ecf5;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, .05);
        }

        .summary-card .label {
            color: #6b7280;
            font-size: .85rem;
            margin-bottom: 4px;
        }

        .summary-card .value {
            font-weight: 800;
            font-size: 1.15rem;
        }

        .vendor-box {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 16px;
            align-items: center;
        }

        .avatar {
            width: 78px;
            height: 78px;
            border-radius: 16px;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
            background: #f3f4f6;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vendor-name {
            font-weight: 800;
            margin: 0;
        }

        .muted {
            color: #6b7280;
        }

        /* Table */
        table.dataTable tbody td {
            vertical-align: top;
        }

        .badge-soft {
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 700;
            font-size: .75rem;
        }

        .badge-soft-success {
            background: #ecfdf5;
            color: #0f766e;
            border: 1px solid #a7f3d0;
        }

        .badge-soft-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-soft-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        details.flowers {
            background: #fcfcff;
            border: 1px dashed #e5e7eb;
            border-radius: 10px;
            padding: 10px;
        }

        details.flowers summary {
            cursor: pointer;
            font-weight: 700;
            user-select: none;
            margin-bottom: 6px;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content d-flex align-items-center flex-nowrap">
            <a class="btn btn-primary me-3" href="{{ url('admin/manage-vendor-details') }}">Back</a>
            <span class="main-content-title" style="margin-top: 36px">Vendor Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Vendor Details</li>
            </ol>
        </div>
    </div>

    <div class="page-hero mt-3">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="vendor-box">
                    <div class="avatar">
                        <img src="{{ asset('images/profile.jpeg') }}" alt="Vendor">
                    </div>
                    <div>
                        <h4 class="vendor-name">
                            {{ $vendor->vendor_name ?? 'N/A' }}
                        </h4>
                        <div class="muted">
                            <i class="fa fa-phone me-2"></i>
                            {{ $vendor->phone_no ?? 'N/A' }}
                        </div>
                        <div class="mt-2 chip">
                            Vendor ID: <span>{{ $vendor->id ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="label">Total Pickups</div>
                        <div class="value">{{ number_format($summary['total_pickups'] ?? 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Items</div>
                        <div class="value">{{ number_format($summary['total_items'] ?? 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Paid</div>
                        <div class="value">{{ number_format($summary['paid_count'] ?? 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Unpaid</div>
                        <div class="value">{{ number_format($summary['unpaid_count'] ?? 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Amount</div>
                        <div class="value">₹{{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2 muted">
                    Last Pickup: <strong>{{ $summary['last_pickup_date'] ?? '—' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Vendor History</h4>
            <div class="chip">Responsive table • Export ready</div>
        </div>

        <div class="card-body">
            <div class="toolbar">
                <div class="muted">Showing {{ number_format($pickups->count()) }} records</div>
                <div></div>
            </div>

            <div class="table-responsive export-table">
                <table id="vendor-history" class="table table-bordered table-hover w-100">
                    <thead>
                        <tr>
                            <th style="width: 56px">#</th>
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
                        @php $row = 0; @endphp
                        @foreach ($pickupDetails as $dateKey => $group)
                            @foreach ($group->sortByDesc('created_at') as $detail)
                                @php
                                    $row++;
                                    $items = $detail->flowerPickupItems ?? collect();
                                    // fallback compute row total if total_price empty
                                    $rowTotal = $detail->total_price;
                                    if (is_null($rowTotal) || $rowTotal === '') {
                                        $rowTotal = $items->sum(function ($i) {
                                            return (float) ($i->price ?? 0);
                                        });
                                    }
                                    // Friendly date
                                    try {
                                        $friendlyDate = \Carbon\Carbon::parse($detail->pickup_date)->format('d M Y');
                                    } catch (\Throwable $e) {
                                        $friendlyDate = $detail->pickup_date ?? 'N/A';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $row }}</td>
                                    <td>{{ $detail->vendor?->vendor_name ?? 'N/A' }}</td>
                                    <td>{{ $detail->rider?->rider_name ?? 'N/A' }}</td>
                                    <td>
                                        <details class="flowers">
                                            <summary>{{ $items->count() }} item(s)</summary>
                                            @if ($items->count())
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($items as $i)
                                                        <li class="mb-2">
                                                            <div><strong>Flower:</strong> {{ $i->flower?->name ?? 'N/A' }}
                                                            </div>
                                                            <div><strong>Qty:</strong> {{ $i->quantity ?? 'N/A' }}
                                                                {{ $i->unit?->unit_name ?? '' }}</div>
                                                            <div><strong>Price:</strong>
                                                                ₹{{ number_format((float) ($i->price ?? 0), 2) }}</div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <em class="muted">No flower pickup items.</em>
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
                                        @if (trim((string) $detail->payment_status) === 'Paid')
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
            // DataTable setup
            const table = $('#vendor-history').DataTable({
                responsive: true,
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                order: [
                    [4, 'desc'],
                    [0, 'asc']
                ], // sort by Pickup Date desc, then row #
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
                ]
            });
        });
    </script>
@endsection
