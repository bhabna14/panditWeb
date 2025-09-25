@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables / Select2 / SweetAlert CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Base */
            --bg-subtle: #F5F7FC;
            --surface: #FFFFFF;
            --border: #E7EAF3;
            --text: #0F172A;
            --muted: #6B7280;

            /* Brand */
            --indigo: #6F6BFE;
            --indigo-600: #5F59F2;
            --cyan: #0EC5D7;

            /* Actions */
            --accent-red: #F24B5B;
            --accent-red-2: #E34050;

            /* Shadows */
            --sh-sm: 0 2px 10px rgba(15, 23, 42, .06);
            --sh-md: 0 12px 28px rgba(2, 6, 23, .10);
        }

        body {
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif !important;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 100% -10%, rgba(111, 107, 254, .08), transparent 60%),
                radial-gradient(900px 500px at 0% 10%, rgba(14, 197, 215, .08), transparent 55%),
                var(--bg-subtle);
        }

        /* KPI cards */
        .stats-card {
            border-radius: 16px;
            padding: 18px 20px;
            background: linear-gradient(180deg, #fff, #FAFBFF);
            box-shadow: var(--sh-md);
            border: 1px solid var(--border);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(2, 6, 23, .12);
        }

        .stats-card .card-title {
            color: var(--muted);
            font-weight: 600;
            letter-spacing: .2px;
        }

        .stats-card .fw-bold {
            font-weight: 800 !important;
        }

        /* Filter wrap */
        .filter-wrap {
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--sh-sm);
            padding: 16px;
        }

        .form-label {
            color: var(--muted);
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border-color: var(--border);
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }

        .select2-selection__arrow {
            height: 36px !important;
        }

        /* Chips */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            background: #fff;
            border: 1px dashed var(--border);
            color: #334155;
            font-weight: 700;
            font-size: .85rem;
            cursor: pointer;
            transition: all .15s ease;
        }

        .chip:hover {
            border-color: var(--indigo);
            color: var(--indigo);
        }

        .chip.active {
            background: linear-gradient(90deg, var(--indigo), var(--cyan));
            border-color: transparent;
            color: #fff;
            box-shadow: 0 6px 16px rgba(14, 197, 215, .25);
        }

        /* Buttons */
        .btn-grad {
            border: none;
            color: #fff;
            font-weight: 800;
            letter-spacing: .2px;
            background-image: linear-gradient(90deg, var(--indigo), var(--cyan));
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(14, 197, 215, .25);
        }

        .btn-grad:hover {
            filter: brightness(.96);
        }

        .btn-reset {
            color: #6b7280;
            font-weight: 700;
        }

        .btn-reset:hover {
            color: #374151;
        }

        /* Table */
        .export-table .dataTables_wrapper .dt-buttons .btn {
            margin-left: .4rem;
        }

        .table {
            border-color: var(--border) !important;
        }

        .table thead th {
            background: #F3F6FF !important;
            border-bottom: 1px solid var(--border) !important;
            color: #0F172A;
            font-weight: 800;
        }

        .table-hover tbody tr:hover {
            background: #F8FBFF;
        }

        /* Status badge — deep solid */
        .status-badge {
            padding: .38rem .68rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: .78rem;
            color: #fff;
            border: 1px solid transparent;
            display: inline-block;
        }

        .status-badge--success {
            background: #0E9F6E;
            border-color: #0A6B4B;
        }

        .status-badge--warning {
            background: #D97706;
            border-color: #B65F04;
        }

        .status-badge--danger {
            background: #DC2626;
            border-color: #A51B1B;
        }

        .status-badge--info {
            background: #1D4ED8;
            border-color: #153AA3;
        }

        .status-badge--neutral {
            background: #334155;
            border-color: #1F2937;
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- KPIs --}}
    <div class="row mb-4 mt-4">
        <div class="col-md-6">
            <div class="stats-card">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1">Total Price</h6>
                    <h4 class="fw-bold mb-0" id="totalPrice">₹{{ number_format((float) $total_price, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1">Today's Price</h6>
                    <h4 class="fw-bold mb-0" id="todayPrice">₹{{ number_format((float) $today_price, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-wrap mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control" value="{{ $fromDate }}">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control" value="{{ $toDate }}">
            </div>
            <div class="col-md-3">
                <label for="vendor_id" class="form-label">Vendor Name</label>
                <select id="vendor_id" class="form-select select2">
                    <option value="">All Vendors</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="payment_mode" class="form-label">Mode of Payment</label>
                <select id="payment_mode" class="form-select">
                    <option value="">All</option>
                    <option value="Cash">Cash</option>
                    <option value="Upi">UPI</option>
                </select>
            </div>

            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <button class="chip" data-range="today">Today</button>
                    <button class="chip" data-range="week">This Week</button>
                    <button class="chip" data-range="month">This Month</button>
                    <button class="chip" data-range="last30">Last 30 Days</button>
                    <button class="chip" data-range="fy">FY (Apr–Mar)</button>
                </div>
            </div>

            <div class="col-md-3 ms-auto d-flex align-items-end gap-2">
                <button id="searchBtn" class="btn btn-grad w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                <button id="resetBtn" class="btn btn-reset">Reset</button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive export-table">
        <table id="file-datatable" class="table table-bordered table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>Pickup Date</th>
                    <th>Vendor Name</th>
                    <th>Rider Name</th>
                    <th>Paid By</th>
                    <th>Flower Details</th>
                    <th>Status</th>
                    <th class="text-end">Total Price</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                @foreach ($reportData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->pickup_date)->format('d M Y') }}</td>
                        <td>{{ $item->vendor->vendor_name ?? '—' }}</td>
                        <td>{{ $item->rider->rider_name ?? '—' }}</td>
                        <td>{{ $item->paid_by ? ucfirst($item->paid_by) : '—' }}</td>
                        <td>
                            @forelse ($item->flowerPickupItems as $f)
                                {{ $f->flower?->name ?? '—' }}
                                ({{ rtrim(rtrim(number_format((float) $f->quantity, 2), '0'), '.') }}
                                {{ $f->unit?->unit_name ?? '—' }})
                                <br>
                            @empty
                                —
                            @endforelse
                        </td>
                        <td>
                            @php $s = strtolower($item->status ?? ''); @endphp
                            @php
                                $cls = 'status-badge--info';
                                if (
                                    in_array($s, [
                                        'success',
                                        'completed',
                                        'complete',
                                        'active',
                                        'ok',
                                        'paid',
                                        'delivered',
                                        'resume',
                                    ])
                                ) {
                                    $cls = 'status-badge--success';
                                } elseif (
                                    in_array($s, [
                                        'pending',
                                        'processing',
                                        'in-progress',
                                        'on hold',
                                        'hold',
                                        'awaiting',
                                    ])
                                ) {
                                    $cls = 'status-badge--warning';
                                } elseif (
                                    in_array($s, ['cancel', 'cancelled', 'failed', 'rejected', 'expired', 'unpaid'])
                                ) {
                                    $cls = 'status-badge--danger';
                                } elseif (in_array($s, ['new', 'created', 'open'])) {
                                    $cls = 'status-badge--neutral';
                                }
                            @endphp
                            <span
                                class="status-badge {{ $cls }}">{{ $item->status ? ucfirst($item->status) : '—' }}</span>
                        </td>
                        <td class="text-end">₹{{ number_format((float) $item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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
        // prevent DT alerts
        $.fn.dataTable.ext.errMode = 'none';

        function capFirst(str) {
            return (!str ? '—' : (str.charAt(0).toUpperCase() + str.slice(1)));
        }

        function money(n) {
            n = parseFloat(n || 0);
            return '₹' + n.toFixed(2);
        }

        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            // Quick ranges
            const $from = $('#from_date');
            const $to = $('#to_date');

            function applyRange(key) {
                const today = moment().startOf('day');
                let start = today.clone(),
                    end = today.clone();

                switch (key) {
                    case 'today':
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
                    case 'fy': {
                        const y = moment().year();
                        const fyStart = moment({
                            year: (moment().month() >= 3 ? y : y - 1),
                            month: 3,
                            day: 1
                        }).startOf('day'); // Apr 1
                        const fyEnd = moment(fyStart).add(1, 'year').subtract(1, 'day').endOf('day'); // Mar 31
                        start = fyStart;
                        end = fyEnd;
                        break;
                    }
                }
                $from.val(start.format('YYYY-MM-DD'));
                $to.val(end.format('YYYY-MM-DD'));
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

            // DataTable
            const table = $('#file-datatable').DataTable({
                responsive: true,
                searching: true,
                paging: true,
                info: true,
                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                        extend: 'copyHtml5',
                        text: 'Copy',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-outline-secondary btn-sm'
                    }
                ],
                columnDefs: [{
                        targets: 6,
                        className: 'text-end'
                    } // price right-aligned
                ]
            });

            // Search (AJAX)
            $('#searchBtn').on('click', function() {
                const fromDate = $from.val();
                const toDate = $to.val();
                const vendorId = $('#vendor_id').val();
                const paymentMode = $('#payment_mode').val();

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
                            const items = (item.flower_pickup_items || []).map(i => {
                                const flowerName = i.flower?.name || '—';
                                const unitName = i.unit?.unit_name || '';
                                const qty = (parseFloat(i.quantity || 0))
                                    .toString();
                                return `${flowerName} (${qty} ${unitName})`;
                            }).join('<br>');

                            // status class mapping
                            const t = (item.status || '').toString().trim()
                            .toLowerCase();
                            let cls = 'status-badge--info';
                            if (['success', 'completed', 'complete', 'active', 'ok',
                                    'paid', 'delivered', 'resume'
                                ].includes(t)) cls = 'status-badge--success';
                            else if (['pending', 'processing', 'in-progress', 'on hold',
                                    'hold', 'awaiting'
                                ].includes(t)) cls = 'status-badge--warning';
                            else if (['cancel', 'cancelled', 'failed', 'rejected',
                                    'expired', 'unpaid'
                                ].includes(t)) cls = 'status-badge--danger';
                            else if (['new', 'created', 'open'].includes(t)) cls =
                                'status-badge--neutral';

                            table.row.add([
                                moment(item.pickup_date).isValid() ? moment(item
                                    .pickup_date).format('DD MMM YYYY') : (item
                                    .pickup_date || '—'),
                                item.vendor?.vendor_name || '—',
                                item.rider?.rider_name || '—',
                                capFirst(item.paid_by),
                                items || '—',
                                `<span class="status-badge ${cls}">${capFirst(item.status || '—')}</span>`,
                                money(item.total_price)
                            ]);
                        });

                        table.draw(false);

                        $('#totalPrice').text(money(response.total_price));
                        $('#todayPrice').text(money(response.today_price));
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to fetch data.', 'error');
                    }
                });
            });
        });
    </script>
@endsection
