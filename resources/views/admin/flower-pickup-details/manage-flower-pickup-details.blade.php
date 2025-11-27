@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/css/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .dataTables_processing {
            z-index: 10;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE Flower Pickup Details</span>
            <div class="text-muted mt-1">Today’s total: <strong>₹{{ number_format((float) $totalExpensesday, 2) }}</strong>
            </div>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.addflowerpickuprequest') }}"
                        class="btn btn-danger text-white">Add Flower Pickup Request</a></li>
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.addflowerpickupdetails') }}"
                        class="btn btn-info text-white">Add Flower Pickup Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Flower Pickup Details</li>
            </ol>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card custom-card mb-3">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">
            <label class="me-2 mb-0"><i class="fas fa-filter me-1"></i>Filter</label>
            <select id="filter" class="form-select" style="max-width:280px">
                <option value="all">All</option>
                <option value="todayexpenses">Today (All)</option>
                <option value="todaypaidpickup">Today (Paid)</option>
                <option value="todaypendingpickup">Today (Pending)</option>
                <option value="monthlyexpenses">This Month (All)</option>
                <option value="monthlypaidpickup">This Month (Paid)</option>
                <option value="monthlypendingpickup">This Month (Pending)</option>
            </select>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pickup Id</th>
                                    <th>Vendor</th>
                                    <th>Rider</th>
                                    <th>Flower Details</th>
                                    <th>PickUp Date</th>
                                    <th>Delivery Date</th>
                                    <th>Total Price</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody><!-- filled by DataTables --></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flower Details Modal (one shared) --}}
    <div class="modal fade" id="flowerDetailsModal" tabindex="-1" aria-labelledby="flowerDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="flowerDetailsModalLabel">
                        <i class="fas fa-seedling"></i> Flower Pickup Details
                    </h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group" id="flowerItemsList">
                        <li class="list-group-item text-muted">Loading...</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal (one shared) --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables (one set only) -->
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

    {{-- IMPORTANT: remove generic initializers that may re-init the same table --}}
    {{-- <script src="{{ asset('assets/js/table-data.js') }}"></script> --}}

    <script>
        (function() {
            const tableSel = '#file-datatable';
            const filterEl = $('#filter');

            // mark XHR globally (prevents auth redirects -> HTML)
            $.ajaxSetup({
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Avoid TN/3: destroy if already initialized (e.g. after PJAX or partial reload)
            if ($.fn.DataTable.isDataTable(tableSel)) {
                $(tableSel).DataTable().clear().destroy();
                $(tableSel).empty(); // optional, clears cloned headers
            }

            const dt = $(tableSel).DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                deferRender: true,
                retrieve: true, // return existing instance if called again
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [5, 'desc']
                ], // pickup_date
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
                columns: [{
                        data: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 1
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3
                    },
                    {
                        data: 4,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 5
                    },
                    {
                        data: 6
                    },
                    {
                        data: 7,
                        searchable: false
                    },
                    {
                        data: 8,
                        searchable: false
                    },
                    {
                        data: 9
                    },
                    {
                        data: 10,
                        orderable: false,
                        searchable: false
                    },
                ],
                dom: 'Bfrtip',
                buttons: [{
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

            // Re-load on filter change
            filterEl.on('change', function() {
                dt.ajax.reload();
            });

            // Lazy-load items into modal
            $(tableSel).on('click', '.btn-view-items', function() {
                const id = $(this).data('id');
                const list = $('#flowerItemsList').empty().append(
                    '<li class="list-group-item text-muted">Loading...</li>');
                fetch("{{ url('/admin/flower-pickup-details') }}/" + id + "/items", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(({
                        items
                    }) => {
                        list.empty();
                        if (!items || !items.length) {
                            list.append('<li class="list-group-item text-muted">No items.</li>');
                            return;
                        }
                        items.forEach((it, idx) => {
                            list.append(
                                `<li class="list-group-item">
                                    <i class="fas fa-leaf"></i> <strong>Flower:</strong> ${it.flower} <br>
                                    <i class="fas fa-box"></i> <strong>Quantity:</strong> ${it.quantity} ${it.unit} <br>
                                    <i class="fas fa-rupee-sign"></i> <strong>Price:</strong> ₹${it.price}
                                </li>`
                            );
                            if (idx !== items.length - 1) list.append('<hr>');
                        });
                    })
                    .catch(() => list.html(
                        '<li class="list-group-item text-danger">Failed to load items.</li>'));
            });

            // Open payment modal, set action + hidden id
            $(tableSel).on('click', '.btn-open-payment', function() {
                const id = $(this).data('id');
                const action = $(this).data('action');
                $('#paymentForm').attr('action', action);
                $('#pickup_id_hidden').val(id);
                $('#paymentFeedback').removeClass('text-success text-danger').text('');
            });

            // Submit payment via AJAX (with CSRF)
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
                            ...(csrf ? {
                                'X-CSRF-TOKEN': csrf
                            } : {})
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
                        dt.ajax.reload(null, false); // refresh current page
                        setTimeout(() => $('#paymentModal').modal('hide'), 500);
                    })
                    .catch(() => {
                        $('#paymentFeedback').addClass('text-danger').text('Failed to save payment.');
                    });
            });
        })();
    </script>
@endsection
