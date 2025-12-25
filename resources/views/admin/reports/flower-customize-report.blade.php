@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables / Select2 / SweetAlert CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --brand-blue: #e9f2ff;
            --brand-blue-edge: #cfe0ff;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;

            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #6b7280;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius-lg: 16px;

            --accent: #6f6bfe;
            --accent-strong: #5f59f2;

            --accent-red: #f24b5b;
            --accent-red-2: #e34050;
        }

        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .container-page { max-width: 1320px; }
        .page-header-title { font-weight: 600; color: #0f172a; }

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

        .band-chips { display: flex; flex-wrap: wrap; gap: .45rem; }

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

        .band-chip.green { background: var(--chip-green); color: var(--chip-green-text); border-color: #c9f0d6; }
        .band-chip.blue  { background: var(--chip-blue);  color: var(--chip-blue-text);  border-color: #bae6fd; }
        .mono { font-variant-numeric: tabular-nums; }

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
            grid-template-columns: minmax(0, 1.2fr) auto;
            align-items: center;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.1rem;
        }

        .toolbar-left { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; }
        .date-range { display: flex; gap: .45rem; flex-wrap: wrap; align-items: center; color: var(--muted); font-size: .84rem; }
        .date-range span.label { font-weight: 600; }

        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 160px;
        }

        .date-range input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(111, 107, 254, 0.25);
        }

        .toolbar-right { display: flex; flex-wrap: wrap; gap: .35rem; justify-content: flex-end; }

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
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-chip::before { content: '⦿'; font-size: .7rem; opacity: .5; }
        .btn-chip:hover { background: #f3f4f6; border-color: #cbd5e1; }

        .btn-chip.active {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border-color: #020617;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.active::before { content: '✓'; opacity: .9; }

        .btn-chip.apply-btn {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.apply-btn::before { content: '↻'; font-size: .75rem; opacity: .8; }
        .btn-chip.reset-btn { border-style: dashed; }

        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .workbook-head {
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .workbook-title {
            font-weight: 600;
            color: #111827;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .workbook-title::before { content: '📑'; font-size: 1.1rem; }

        .workbook-body { padding: 1rem 1.1rem 1.1rem; }

        .export-table .dataTables_wrapper .dt-buttons .btn {
            margin-left: .4rem;
            border-radius: 999px;
        }

        .btn-pill-red {
            background: var(--accent-red);
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: .45rem .9rem;
            font-weight: 700;
        }

        .btn-pill-red:hover { background: var(--accent-red-2); color: #fff; }

        .table {
            border-color: var(--table-border) !important;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            font-size: .88rem;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft)) !important;
            border-bottom: none !important;
            color: var(--table-head-text) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .06em;
        }

        .table-hover tbody tr:hover { background: var(--table-hover); }

        .status-badge {
            padding: .38rem .68rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: .75rem;
            color: #fff;
            border: 1px solid transparent;
            display: inline-block;
        }
        .status-badge--success { background: #0e9f6e; border-color: #0a6b4b; }
        .status-badge--warning { background: #d97706; border-color: #b65f04; }
        .status-badge--danger  { background: #dc2626; border-color: #a51b1b; }
        .status-badge--info    { background: #1d4ed8; border-color: #153aa3; }
        .status-badge--neutral { background: #334155; border-color: #1f2937; }

        @media (max-width: 992px) {
            .toolbar { grid-template-columns: 1fr; }
            .toolbar-left, .toolbar-right { justify-content: flex-start; }
            .workbook-head { flex-direction: column; align-items: flex-start; }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-3">

        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h4 class="page-header-title mb-0">Customize Orders — Report</h4>
            </div>
        </div>

        <div class="band">
            <div class="band-chips">
                <span class="band-chip green">
                    <span class="icon">💰</span>
                    <span>Total Customize Order Price</span>
                    <span class="mono" id="totalPrice">₹0.00</span>
                </span>
                <span class="band-chip blue">
                    <span class="icon">📅</span>
                    <span>Today Customize Price</span>
                    <span class="mono" id="todayPrice">₹0.00</span>
                </span>
            </div>
        </div>

        <div class="toolbar mb-3">
            <div class="toolbar-left">
                <div class="date-range">
                    <span class="label">F</span>
                    <input type="date" id="from_date">
                </div>
                <div class="date-range">
                    <span class="label">T</span>
                    <input type="date" id="to_date">
                </div>
            </div>
            <div class="toolbar-right">
                <button class="btn-chip" type="button" data-range="today">
                    <i class="bi bi-calendar-day"></i><span>Today</span>
                </button>
                <button class="btn-chip" type="button" data-range="week">
                    <i class="bi bi-calendar-week"></i><span>This Week</span>
                </button>
                <button class="btn-chip" type="button" data-range="month">
                    <i class="bi bi-calendar3"></i><span>This Month</span>
                </button>

                <button id="searchBtn" class="btn-chip apply-btn" type="button">
                    <i class="fas fa-search"></i><span>Apply</span>
                </button>
                <button id="resetBtn" class="btn-chip reset-btn" type="button">
                    <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                </button>
            </div>
        </div>

        <div class="workbook">
            <div class="workbook-head">
                <div class="workbook-title">Customize Orders — Detailed Table</div>
            </div>

            <div class="workbook-body export-table">
                <div class="table-responsive">
                    <table id="file-datatable" class="table table-bordered table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>Customer Details</th>
                                <th>Date</th>
                                <th>Delivery Date</th>
                                <th>Categories / Items</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
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

    <script>
        $(function () {
            const $from = $('#from_date');
            const $to   = $('#to_date');

            function applyRange(key) {
                const today = moment().startOf('day');
                let start = today.clone(), end = today.clone();

                if (key === 'week') {
                    start = moment().startOf('isoWeek');
                    end   = moment().endOf('isoWeek');
                } else if (key === 'month') {
                    start = moment().startOf('month');
                    end   = moment().endOf('month');
                }

                $from.val(start.format('YYYY-MM-DD'));
                $to.val(end.format('YYYY-MM-DD'));
            }

            // Default today
            applyRange('today');
            $('[data-range="today"]').addClass('active');

            const table = $('#file-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                dom:
                    "<'row'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                buttons: [
                    { extend: 'copyHtml5',  text: 'Copy',  className: 'btn btn-pill-red' },
                    { extend: 'csvHtml5',   text: 'CSV',   className: 'btn btn-pill-red' },
                    { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-pill-red' },
                    { extend: 'pdfHtml5',   text: 'PDF',   className: 'btn btn-pill-red' },
                    { extend: 'print',      text: 'Print', className: 'btn btn-pill-red' }
                ],

                ajax: {
                    url: "{{ route('report.customize') }}",
                    type: "GET",
                    data: function (d) {
                        d.from_date = $from.val();
                        d.to_date   = $to.val();
                    },
                    error: function (xhr) {
                        // This will show the real Laravel error in console
                        console.error('DataTables Ajax error:', xhr.status);
                        console.error(xhr.responseText);
                    },
                    dataSrc: function (json) {
                        const total = parseFloat(json.total_price_sum ?? 0) || 0;
                        const today = parseFloat(json.today_price_sum ?? 0) || 0;

                        $('#totalPrice').text('₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#todayPrice').text('₹' + today.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                        return json.data || [];
                    }
                },

                // Sort by Date column (created_at)
                order: [[1, 'desc']],

                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false, // IMPORTANT (computed)
                        render: function (_, __, row) {
                            const user = row.user || {};
                            const address = user.address_details || {};
                            const userId = user.userid ?? null;

                            const tooltip = `
                                <strong>Apartment:</strong> ${address.apartment_name || 'N/A'}<br>
                                <strong>No:</strong> ${address.apartment_flat_plot || 'N/A'}
                            `.trim();

                            const modalId = `addr_${userId || Math.random().toString(36).slice(2)}`;
                            const viewBtn = userId
                                ? `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-primary btn-sm">View</a>`
                                : '';

                            const addressHtml = `
                                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header text-white" style="background: var(--accent-strong);">
                                        <h5 class="modal-title">Address</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Address:</strong> ${(address.apartment_flat_plot || '')}, ${(address.apartment_name || '')}, ${(address.locality || '')}</p>
                                        <p><strong>Landmark:</strong> ${(address.landmark || '')}</p>
                                        <p><strong>Pin Code:</strong> ${(address.pincode || '')}</p>
                                        <p><strong>City:</strong> ${(address.city || '')}</p>
                                        <p class="mb-0"><strong>State:</strong> ${(address.state || '')}</p>
                                    </div>
                                  </div></div>
                                </div>`;

                            return `
                              <div data-bs-toggle="tooltip" data-bs-html="true" title="${tooltip}">
                                <div class="fw-semibold">${user.name || 'N/A'}</div>
                                <small class="text-muted">${user.mobile_number || ''}</small>
                                <div class="mt-1 d-flex gap-2">
                                  ${viewBtn}
                                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#${modalId}">
                                    Address
                                  </button>
                                </div>
                              </div>
                              ${addressHtml}
                            `;
                        }
                    },
                    {
                        data: 'purchase_date',
                        name: 'flower_requests.created_at', // IMPORTANT real SQL column
                        searchable: true
                    },
                    {
                        data: 'delivery_date',
                        orderable: false,
                        searchable: false // IMPORTANT (computed)
                    },
                    {
                        data: 'flower_items',
                        orderable: false,
                        searchable: false // IMPORTANT (computed)
                    },
                    {
                        data: 'status',
                        name: 'flower_requests.status', // IMPORTANT real SQL column (if exists)
                        searchable: true,
                        className: 'text-center',
                        render: function (s) {
                            const t = (s || '').toString().trim().toLowerCase();
                            let cls = 'status-badge--info';

                            if (['success','completed','complete','active','ok','paid','resume','delivered'].includes(t)) {
                                cls = 'status-badge--success';
                            } else if (['pending','processing','in-progress','on hold','hold','awaiting'].includes(t)) {
                                cls = 'status-badge--warning';
                            } else if (['cancel','cancelled','failed','rejected','expired','unpaid'].includes(t)) {
                                cls = 'status-badge--danger';
                            } else if (['new','created','open'].includes(t)) {
                                cls = 'status-badge--neutral';
                            }

                            return `<span class="status-badge ${cls}">${(s || '').toString()}</span>`;
                        }
                    },
                    {
                        data: 'price',
                        name: 'price_amount',   // IMPORTANT: alias from controller addSelect
                        searchable: false,      // safest to avoid SQL issues
                        className: 'text-end mono',
                        render: function (v) {
                            const amount = parseFloat(v ?? 0) || 0;
                            return '₹' + amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                ]
            });

            // Range buttons
            $('[data-range]').on('click', function () {
                $('[data-range]').removeClass('active');
                $(this).addClass('active');

                applyRange($(this).data('range'));
                table.ajax.reload();
            });

            // Apply (manual)
            $('#searchBtn').on('click', function () {
                table.ajax.reload();
            });

            // Reset
            $('#resetBtn').on('click', function () {
                applyRange('today');
                $('[data-range]').removeClass('active');
                $('[data-range="today"]').addClass('active');
                table.ajax.reload();
            });

            // Re-init tooltips on each draw
            $('#file-datatable').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    const t = bootstrap.Tooltip.getInstance(this);
                    if (t) t.dispose();
                });

                $('[data-bs-toggle="tooltip"]').tooltip({
                    html: true,
                    boundary: 'window',
                    trigger: 'hover'
                });
            });
        });
    </script>
@endsection
