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
            --muted: #64748B;

            /* Brand + accents */
            --indigo: #6F6BFE;
            --indigo-600: #5F59F2;
            --violet: #8151FB;
            --cyan: #0EC5D7;
            --pink: #FF6B9A;

            /* Status */
            --green-50: #E8F5EE;
            --green-600: #167C45;
            --green-200: #CDEBD7;
            --amber-50: #FFF7E6;
            --amber-700: #92400E;
            --amber-200: #FFE3B3;
            --red-50: #FDE8E8;
            --red-600: #B91C1C;
            --red-200: #FAC6C6;
            --blue-50: #E6F0FF;
            --blue-600: #1D4ED8;
            --blue-200: #C9DAFF;
            --gray-50: #F3F4F6;
            --gray-600: #374151;
            --gray-200: #E5E7EB;

            /* Shadows */
            --sh-sm: 0 2px 10px rgba(15, 23, 42, .06);
            --sh-md: 0 12px 28px rgba(2, 6, 23, .10);
        }

        body {
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif !important;
            color: var(--text);
            background:
                radial-gradient(800px 450px at 100% -10%, rgba(129, 81, 251, .10), transparent 60%),
                radial-gradient(900px 500px at 0% 10%, rgba(14, 197, 215, .10), transparent 55%),
                var(--bg-subtle);
        }

        /* -------- Hero strip -------- */
        .page-hero {
            border-radius: 16px;
            background: linear-gradient(135deg, var(--indigo) 0%, var(--violet) 45%, var(--cyan) 100%);
            color: #fff;
            padding: 16px 18px;
            box-shadow: var(--sh-md);
            border: 1px solid rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .page-hero h6 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .pill-date {
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            padding: .3rem .6rem;
            border-radius: 999px;
            font-weight: 700;
        }

        /* -------- KPI cards (glassy) -------- */
        .stats-card {
            border-radius: 16px;
            padding: 18px 20px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .9), #fff);
            backdrop-filter: blur(6px);
            box-shadow: var(--sh-md);
            border: 1px solid var(--border);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
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

        .kpi-accent {
            color: var(--cyan);
        }

        /* -------- Filter box -------- */
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

        .form-control {
            border-radius: 12px;
            border-color: var(--border);
            background: #fff;
            transition: box-shadow .15s ease, border-color .15s ease;
        }

        .form-control:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 .2rem rgba(111, 107, 254, .15);
        }

        /* -------- Quick chips -------- */
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

        /* -------- Buttons -------- */
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

        /* DataTables export buttons (colorful) */
        .btn-pill {
            border: none;
            border-radius: 999px;
            padding: .45rem .9rem;
            font-weight: 800;
            color: #fff;
        }

        .btn-copy {
            background: linear-gradient(90deg, #94a3b8, #64748b);
        }

        /* slate */
        .btn-csv {
            background: linear-gradient(90deg, #F59E0B, #F97316);
        }

        /* amber/orange */
        .btn-excel {
            background: linear-gradient(90deg, #22C55E, #16A34A);
        }

        /* green */
        .btn-pdf {
            background: linear-gradient(90deg, #EF4444, #DC2626);
        }

        /* red */
        .btn-print {
            background: linear-gradient(90deg, #4F46E5, #4338CA);
        }

        /* indigo */

        /* -------- Table -------- */
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

        /* Category pill */
        .cat-pill {
            background: #EEF2FF;
            color: #4F46E5;
            padding: .25rem .6rem;
            border-radius: 999px;
            font-weight: 800;
            text-decoration: none;
        }

        .cat-pill:hover {
            background: #E0E7FF;
            color: #4338CA;
            text-decoration: none;
        }

        /* Status badges (auto-colored) */
        .status-badge {
            padding: .38rem .68rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: .78rem;
            border: 1px solid transparent;
            display: inline-block;
        }

        .s-ok,
        .s-active,
        .s-completed {
            background: var(--green-50);
            color: var(--green-600);
            border-color: var(--green-200);
        }

        .s-pending,
        .s-processing {
            background: var(--amber-50);
            color: var(--amber-700);
            border-color: var(--amber-200);
        }

        .s-cancelled,
        .s-failed,
        .s-rejected {
            background: var(--red-50);
            color: var(--red-600);
            border-color: var(--red-200);
        }

        .s-default {
            background: var(--blue-50);
            color: var(--blue-600);
            border-color: var(--blue-200);
        }
    </style>
@endsection

@section('content')
    <!-- Hero -->
    <div class="page-hero mb-3">
        <h6 class="fw-bold">Customize Orders Report</h6>
        <span class="pill-date" id="todayStamp"></span>
    </div>

    <!-- KPIs -->
    <div class="row mb-4 mt-2">
        <div class="col-md-6">
            <div class="stats-card">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1">Total Customize Total Price</h6>
                    <h4 class="fw-bold mb-0"><span class="kpi-accent">₹</span><span id="totalPrice">0</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1">Today Customize Price</h6>
                    <h4 class="fw-bold mb-0"><span class="kpi-accent">₹</span><span id="todayPrice">0</span></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-wrap mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control" placeholder="dd-mm-yyyy">
            </div>
            <div class="col-md-4">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control" placeholder="dd-mm-yyyy">
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button id="searchBtn" class="btn btn-grad w-100"><i class="fas fa-search me-1"></i> Search</button>
                <button id="resetBtn" class="btn btn-reset">Reset</button>
            </div>

            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button class="chip" data-range="today">Today</button>
                    <button class="chip" data-range="week">This Week</button>
                    <button class="chip" data-range="month">This Month</button>
                    <button class="chip" data-range="last30">Last 30 Days</button>
                    <button class="chip" data-range="fy">FY (Apr–Mar)</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive export-table">
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
@endsection

@section('scripts')
    <!-- Libraries -->
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
        $(function() {
            // Stamp hero date
            const todayDisp = moment().format('DD MMM YYYY');
            $('#todayStamp').text(todayDisp);

            // Quick range helpers
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
                        }).startOf('day');
                        const fyEnd = moment(fyStart).add(1, 'year').subtract(1, 'day').endOf('day');
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
                table.ajax.reload();
            });

            $('#resetBtn').on('click', function() {
                $from.val('');
                $to.val('');
                $('.chip').removeClass('active');
                table.ajax.reload();
            });

            function statusClass(s) {
                if (!s) return 's-default';
                const t = s.toString().toLowerCase();
                if (['ok', 'success', 'done', 'completed', 'active'].includes(t))
            return 's-ok s-active s-completed';
                if (['pending', 'process', 'processing', 'in-progress'].includes(t))
            return 's-pending s-processing';
                if (['cancel', 'cancelled', 'failed', 'rejected'].includes(t))
                return 's-cancelled s-failed s-rejected';
                return 's-default';
            }

            // DataTable
            const table = $('#file-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                        extend: 'copyHtml5',
                        text: 'Copy',
                        className: 'btn btn-pill btn-copy'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        className: 'btn btn-pill btn-csv'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-pill btn-excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-pill btn-pdf'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-pill btn-print'
                    }
                ],
                ajax: {
                    url: "{{ route('report.customize') }}",
                    data: function(d) {
                        d.from_date = $from.val();
                        d.to_date = $to.val();
                    },
                    dataSrc: function(json) {
                        $('#totalPrice').text(Number(json.total_price_sum ?? 0).toLocaleString(
                        'en-IN', {
                            maximumFractionDigits: 2
                        }));
                        $('#todayPrice').text(Number(json.today_price_sum ?? 0).toLocaleString(
                        'en-IN', {
                            maximumFractionDigits: 2
                        }));
                        return json.data || [];
                    }
                },
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: null,
                        orderable: false,
                        render: function(_, __, row) {
                            const user = row.user || {};
                            const address = user.address_details || {};
                            const userId = user.userid ?? null;

                            const tooltip = `
                                <strong>Apartment:</strong> ${address.apartment_name || 'N/A'}<br>
                                <strong>No:</strong> ${address.apartment_flat_plot || 'N/A'}
                            `.trim();

                            const modalId = `addr_${userId || Math.random().toString(36).slice(2)}`;
                            const viewBtn = userId ?
                                `<a href="/admin/show-customer/${userId}/details" class="btn btn-sm btn-outline-primary">View</a>` :
                                '';

                            const addressHtml = `
                                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header text-white" style="background: var(--indigo);">
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
                                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#${modalId}">Address</button>
                                </div>
                              </div>
                              ${addressHtml}
                            `;
                        }
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    },
                    {
                        data: 'flower_items',
                        name: 'flower_items',
                        orderable: false,
                        render: function(data, type, row) {
                            const cat = row.category_name ?
                                `<a href="javascript:void(0)" class="cat-pill">${row.category_name}</a>` :
                                '';
                            const modalId = `items_${row.request_id}`;
                            return `
                              <div class="d-flex align-items-center gap-2">
                                ${cat}
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#${modalId}">View Items</button>
                              </div>
                              <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                  <div class="modal-content">
                                    <div class="modal-header text-white" style="background: var(--indigo);">
                                      <h5 class="modal-title">Flower Items</h5>
                                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                      ${data ? data.split(',').map(i => `<div>• ${i.trim()}</div>`).join('') : '<em>No items found.</em>'}
                                    </div>
                                  </div>
                                </div>
                              </div>
                            `;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(s) {
                            const cls = statusClass(s);
                            return `<span class="status-badge ${cls}">${(s||'').toString()}</span>`;
                        }
                    },
                    {
                        data: 'price',
                        name: 'price',
                        className: 'text-end',
                        render: v => '₹' + Number(v || 0).toLocaleString('en-IN', {
                            minimumFractionDigits: 2
                        })
                    }
                ]
            });

            // Search
            $('#searchBtn').on('click', () => table.ajax.reload());

            // Tooltips after draw
            $('#file-datatable').on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').each(function() {
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
