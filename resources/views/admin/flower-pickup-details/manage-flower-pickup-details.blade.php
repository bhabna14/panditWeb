{{-- resources/views/admin/flower-pickup/manage.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/css/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Optional font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --page-bg: #f3f4f6;
            --surface: #ffffff;
            --border-subtle: #e5e7eb;
            --ink: #111827;
            --muted: #6b7280;
            --brand: #0ea5e9;
            --brand-deep: #0369a1;
            --accent: #22c55e;
            --accent-soft: #dcfce7;
            --accent-ink: #166534;
            --danger-soft: #fee2e2;
            --danger-ink: #b91c1c;
            --shadow-soft: 0 10px 26px rgba(15, 23, 42, .08);
            --shadow-subtle: 0 2px 8px rgba(15, 23, 42, .04);
        }

        body {
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif !important;
            background: var(--page-bg);
            color: var(--ink);
        }

        .page-shell {
            padding-inline: .25rem;
        }

        @media (min-width: 992px) {
            .page-shell {
                padding-inline: .75rem;
            }
        }

        /* HEADER */
        .page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
            margin-top: .5rem;
            margin-bottom: 1rem;
        }

        .page-header-main {
            display: flex;
            flex-direction: column;
            gap: .3rem;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .page-title span.icon-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #e0f2fe;
            color: var(--brand-deep);
        }

        .page-subtitle {
            font-size: .85rem;
            color: var(--muted);
        }

        .page-header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .page-header-actions .btn {
            border-radius: 999px;
            font-size: .8rem;
        }

        .today-total-chip {
            border-radius: 999px;
            padding: .4rem .9rem;
            background: var(--accent-soft);
            color: var(--accent-ink);
            font-size: .8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        /* CARDS */
        .card-elevated {
            border-radius: 16px;
            border: 1px solid var(--border-subtle);
            background: var(--surface);
            box-shadow: var(--shadow-subtle);
        }

        /* FILTER BAR */
        .filter-bar {
            margin-bottom: 1.25rem;
        }

        .filter-bar-label {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .filter-inner {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
        }

        .filter-inner .form-select {
            max-width: 320px;
            border-radius: 999px;
            border-color: var(--border-subtle);
            font-size: .86rem;
        }

        .filter-helper {
            font-size: .8rem;
            color: var(--muted);
        }

        /* TABLE CARD */
        .table-card-header {
            padding: .75rem 1.25rem .25rem;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .table-card-title {
            font-weight: 600;
            font-size: .95rem;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .table-card-title span.badge-tag {
            border-radius: 999px;
            padding: .12rem .6rem;
            background: #eff6ff;
            color: var(--brand-deep);
            font-size: .72rem;
            font-weight: 600;
        }

        .table-card-sub {
            font-size: .8rem;
            color: var(--muted);
        }

        .table-responsive {
            padding: .75rem 1.25rem 1rem;
        }

        .table {
            border-color: var(--border-subtle) !important;
            font-size: .85rem;
        }

        .table thead th {
            background: #f9fafb !important;
            color: #111827 !important;
            border-bottom: 1px solid var(--border-subtle) !important;
            font-weight: 600;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .table-hover tbody tr:hover {
            background: #f3f4f6;
        }

        .dataTables_processing {
            z-index: 10;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: .6rem .9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .9);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-subtle);
            font-size: .8rem;
        }

        /* BADGES / STATUS */
        .badge-payment-status {
            border-radius: 999px;
            padding: .2rem .65rem;
            font-size: .72rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-payment-paid {
            background: var(--accent-soft);
            color: var(--accent-ink);
            border: 1px solid #bbf7d0;
        }

        .badge-payment-pending {
            background: #fef9c3;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-status-active {
            background: #e0f2fe;
            color: var(--brand-deep);
            border-radius: 999px;
            padding: .2rem .65rem;
            font-size: .72rem;
            font-weight: 600;
        }

        .badge-status-inactive {
            background: var(--danger-soft);
            color: var(--danger-ink);
            border-radius: 999px;
            padding: .2rem .65rem;
            font-size: .72rem;
            font-weight: 600;
        }

        /* ACTION BUTTONS IN TABLE */
        .table-actions .btn {
            border-radius: 999px;
            font-size: .7rem;
        }

        /* MODALS */
        .modal-header.brand-header {
            background: var(--brand);
            color: #fff;
        }

        .modal-header.brand-header .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid page-shell">

        {{-- PAGE HEADER --}}
        <div class="page-header">
            <div class="page-header-main">
                <div class="page-title">
                    <span class="icon-pill">
                        <i class="fas fa-truck-loading"></i>
                    </span>
                    <span>Manage Flower Pickup Details</span>
                </div>
                <div class="page-subtitle">
                    Review all flower pickups, check payment status, and update payments if needed.
                </div>
                <div class="mt-2">
                    <span class="today-total-chip">
                        <i class="fas fa-indian-rupee-sign"></i>
                        Today’s total:
                        <strong>₹{{ number_format((float) $totalExpensesday, 2) }}</strong>
                    </span>
                </div>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('admin.addflowerpickuprequest') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-1"></i> Pickup Request
                </a>
                <a href="{{ route('admin.addflowerpickupdetails') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Pickup Details
                </a>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="card-elevated filter-bar mb-3">
            <div class="card-body">
                <div class="filter-bar-label">
                    <i class="fas fa-filter me-1"></i> Filter
                </div>
                <div class="filter-inner">
                    <select id="filter" class="form-select">
                        <option value="all">All</option>
                        <option value="todayexpenses">Today (All)</option>
                        <option value="todaypaidpickup">Today (Paid)</option>
                        <option value="todaypendingpickup">Today (Pending)</option>
                        <option value="monthlyexpenses">This Month (All)</option>
                        <option value="monthlypaidpickup">This Month (Paid)</option>
                        <option value="monthlypendingpickup">This Month (Pending)</option>
                    </select>
                    <div class="filter-helper">
                        Choose a quick view to highlight today’s or this month’s pickups and payment status.
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card-elevated">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">
                        <i class="fas fa-table"></i> Flower Pickup List
                        <span class="badge-tag">Live data</span>
                    </div>
                    <div class="table-card-sub">
                        Use the filter above, sort columns, or export the data using the built-in buttons.
                    </div>
                </div>
            </div>
            <div class="table-responsive export-table">
                <table id="file-datatable" class="table table-bordered table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pickup ID</th>
                            <th>Vendor</th>
                            <th>Rider</th>
                            <th>Flower Details</th>
                            <th>Pickup Date</th>
                            <th>Delivery Date</th>
                            <th>Total Price</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody><!-- Filled by DataTables --></tbody>
                </table>
            </div>
        </div>

        {{-- Flower Details Modal --}}
        <div class="modal fade" id="flowerDetailsModal" tabindex="-1"
             aria-labelledby="flowerDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header brand-header">
                        <h5 class="modal-title" id="flowerDetailsModalLabel">
                            <i class="fas fa-seedling me-1"></i> Flower Pickup Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group" id="flowerItemsList">
                            <li class="list-group-item text-muted">Loading...</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Modal --}}
        <div class="modal fade" id="paymentModal" tabindex="-1"
             aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header brand-header">
                        <h5 class="modal-title" id="paymentModalLabel">
                            <i class="fas fa-wallet me-1"></i> Add Payment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <form id="paymentForm" method="POST">
                        @csrf
                        <input type="hidden" name="pickup_id" id="pickup_id_hidden">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="payment_id" class="form-label">Payment ID</label>
                                <input type="text" class="form-control" id="payment_id" name="payment_id"
                                       placeholder="Enter Payment ID">
                            </div>
                            <div class="mb-3">
                                <label for="paid_by" class="form-label">Paid By</label>
                                <select class="form-control" name="paid_by" id="paid_by" required>
                                    <option value="">Select Name</option>
                                    <option value="Pankaj">Pankaj Sial</option>
                                    <option value="Subrata">Subrata</option>
                                    <option value="Basudha">Basudha</option>
                                </select>
                            </div>
                            <div id="paymentFeedback" class="small"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-light text-primary border border-primary">
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    {{-- IMPORTANT: do NOT include generic table-data.js that re-inits the same table --}}
    {{-- <script src="{{ asset('assets/js/table-data.js') }}"></script> --}}

    <script>
        (function() {
            const tableSel = '#file-datatable';
            const filterEl = $('#filter');

            $.ajaxSetup({
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if ($.fn.DataTable.isDataTable(tableSel)) {
                $(tableSel).DataTable().clear().destroy();
                $(tableSel).empty();
            }

            const dt = $(tableSel).DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                deferRender: true,
                retrieve: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [[5, 'desc']], // pickup_date column
                ajax: {
                    url: "{{ route('admin.flower-pickup-details.data') }}",
                    data: function(d) {
                        d.filter = filterEl.val();
                    },
                    error: function(xhr) {
                        console.error('DataTables AJAX error', xhr.status, xhr.responseText);
                        alert('Failed to load data (see console/network for details).');
                    }
                },
                columns: [
                    { data: 0, orderable: false, searchable: false }, // #
                    { data: 1 }, // pickup id
                    { data: 2 }, // vendor
                    { data: 3 }, // rider
                    {
                        data: 4,
                        orderable: false,
                        searchable: false
                    }, // flower details (buttons)
                    { data: 5 }, // pickup date
                    { data: 6 }, // delivery date
                    {
                        data: 7,
                        searchable: false
                    }, // total price
                    {
                        data: 8,
                        searchable: false
                    }, // payment status
                    { data: 9 }, // status
                    {
                        data: 10,
                        orderable: false,
                        searchable: false
                    }, // actions
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Flower_Pickups'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Flower_Pickups'
                    },
                    {
                        extend: 'print',
                        title: 'Flower Pickup Details'
                    }
                ],
            });

            // Filter change reload
            filterEl.on('change', function() {
                dt.ajax.reload();
            });

            // Flower items modal lazy-load
            $(tableSel).on('click', '.btn-view-items', function() {
                const id = $(this).data('id');
                const list = $('#flowerItemsList').empty()
                    .append('<li class="list-group-item text-muted">Loading...</li>');

                fetch("{{ url('/admin/flower-pickup-details') }}/" + id + "/items", {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(({ items }) => {
                        list.empty();
                        if (!items || !items.length) {
                            list.append('<li class="list-group-item text-muted">No items.</li>');
                            return;
                        }
                        items.forEach((it, idx) => {
                            list.append(`
                                <li class="list-group-item">
                                    <i class="fas fa-leaf"></i>
                                    <strong>Flower:</strong> ${it.flower} <br>
                                    <i class="fas fa-box"></i>
                                    <strong>Quantity:</strong> ${it.quantity} ${it.unit} <br>
                                    <i class="fas fa-indian-rupee-sign"></i>
                                    <strong>Price:</strong> ₹${it.price}
                                </li>
                            `);
                            if (idx !== items.length - 1) list.append('<hr>');
                        });
                    })
                    .catch(() => {
                        list.html('<li class="list-group-item text-danger">Failed to load items.</li>');
                    });
            });

            // Open payment modal
            $(tableSel).on('click', '.btn-open-payment', function() {
                const id = $(this).data('id');
                const action = $(this).data('action');
                $('#paymentForm').attr('action', action);
                $('#pickup_id_hidden').val(id);
                $('#paymentFeedback').removeClass('text-success text-danger').text('');
            });

            // Submit payment
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const formData = new FormData(this);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                $('#paymentFeedback').removeClass('text-success text-danger').text('Saving...');

                fetch($form.attr('action'), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                    },
                    body: formData
                })
                    .then(async (res) => {
                        if (!res.ok) {
                            const txt = await res.text();
                            throw new Error(txt || 'Failed');
                        }
                        return res.json().catch(() => ({}));
                    })
                    .then(() => {
                        $('#paymentFeedback').addClass('text-success').text('Saved.');
                        dt.ajax.reload(null, false);
                        setTimeout(() => $('#paymentModal').modal('hide'), 500);
                    })
                    .catch(() => {
                        $('#paymentFeedback').addClass('text-danger').text('Failed to save payment.');
                    });
            });
        })();
    </script>
@endsection
