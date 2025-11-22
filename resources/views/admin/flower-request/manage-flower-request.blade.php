@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .badge {
            font-size: 0.8rem;
        }

        .table td,
        .table th {
            vertical-align: middle !important;
        }

        .action-btns .btn {
            margin: 2px 0;
        }

        /* Metric cards: no bg, light border, subtle hover */
        .metric-card {
            background-color: transparent !important;
            border: 1px solid rgb(186, 185, 185) !important;
            transition: box-shadow .2s ease, transform .2s ease, opacity .2s ease;
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .metric-card .card-body {
            padding: 1rem 1.25rem;
        }

        .metric-card .icon {
            font-size: 2rem;
            line-height: 1;
        }

        .metric-card .label {
            font-weight: 600;
        }

        .metric-card.opacity-90 {
            opacity: .85;
        }

        .card-filter:hover .metric-card {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    {{-- counters --}}
    <div class="row mb-4 mt-4">
        {{-- Total --}}
        <div class="col-md-3">
            <a href="{{ route('flower.customize.request', ['filter' => 'all']) }}" class="card-filter text-decoration-none"
                data-filter="all">
                <div class="card metric-card h-100 {{ $filter === 'all' ? '' : 'opacity-90' }}" data-card="all">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="fa fa-list icon text-warning"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 label text-muted">Total Orders</h5>
                            <h3 class="mb-0 text-warning " id="totalCount">{{ $totalCustomizeOrders ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Today --}}
        <div class="col-md-3">
            <a href="{{ route('flower.customize.request', ['filter' => 'today']) }}" class="card-filter text-decoration-none"
                data-filter="today">
                <div class="card metric-card h-100 {{ $filter === 'today' ? '' : 'opacity-90' }}" data-card="today">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="fa fa-calendar-day icon text-success"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 label text-muted">Today's Orders</h5>
                            <h3 class="mb-0 text-success" id="todayCount">{{ $todayCustomizeOrders ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Paid --}}
        <div class="col-md-3">
            <a href="{{ route('flower.customize.request', ['filter' => 'paid']) }}" class="card-filter text-decoration-none"
                data-filter="paid">
                <div class="card metric-card h-100 {{ $filter === 'paid' ? '' : 'opacity-90' }}" data-card="paid">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="fa fa-check-circle icon text-info"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 label text-muted">Paid Orders</h5>
                            <h3 class="mb-0 text-info" id="paidCount">{{ $paidCustomizeOrders ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Rejected --}}
        <div class="col-md-3">
            <a href="{{ route('flower.customize.request', ['filter' => 'rejected']) }}" class="card-filter text-decoration-none"
                data-filter="rejected">
                <div class="card metric-card h-100 {{ $filter === 'rejected' ? '' : 'opacity-90' }}" data-card="rejected">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 ">
                            <i class="fa fa-ban icon text-primary"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 label text-muted">Rejected Orders</h5>
                            <h3 class="mb-0 text-primary" id="rejectedCount">{{ $rejectCustomizeOrders ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- table --}}
    <div class="card custom-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="file-datatable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th># / User</th>
                            <th>Purchase</th>
                            <th>Delivery</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Rider</th>
                            <th>Address</th>
                            <th>Cancel By</th>
                            <th>Cancel Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestsBody">
                        @include('admin.flower-request.partials._rows', [
                            'pendingRequests' => $pendingRequests,
                            'riders' => $riders,
                        ])
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Re-init DataTable safely after we replace tbody
        function reinitDataTable() {
            if ($.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable().destroy();
            }
            $('#file-datatable').DataTable({
                // If you rely on table-data.js defaults, keep this empty so it uses defaults.
                // Or paste your preferred options here.
            });
        }

        function setActiveCard(filter) {
            // Add opacity to all, remove from active
            $('[data-card]').addClass('opacity-90');
            $('[data-card="' + filter + '"]').removeClass('opacity-90');
        }

        function updateCounts(counts) {
            if (typeof counts !== 'object') return;
            $('#totalCount').text(counts.total ?? 0);
            $('#todayCount').text(counts.today ?? 0);
            $('#paidCount').text(counts.paid ?? 0);
            $('#rejectedCount').text(counts.rejected ?? 0);
        }

        function loadRequests(filter, pushUrl = true) {
            const url = "{{ route('admin.flower-request.data') }}";
            // Optional tiny loading state
            const $tbody = $('#requestsBody');
            const prevHtml = $tbody.html();
            $tbody.html('<tr><td colspan="11" class="text-center py-5">Loading...</td></tr>');

            $.get(url, {
                    filter: filter
                })
                .done(function(res) {
                    if (res && res.rows_html !== undefined) {
                        $tbody.html(res.rows_html);
                        reinitDataTable();
                        updateCounts(res.counts || {});
                        setActiveCard(res.active || filter);
                        if (pushUrl) {
                            const pageUrl = new URL(window.location);
                            pageUrl.searchParams.set('filter', res.active || filter);
                            window.history.pushState({
                                filter: res.active || filter
                            }, '', pageUrl.toString());
                        }
                    } else {
                        $tbody.html(
                            '<tr><td colspan="11" class="text-center py-5 text-danger">Unexpected response</td></tr>'
                        );
                    }
                })
                .fail(function(xhr) {
                    $tbody.html(prevHtml);
                    Swal.fire('Error', 'Failed to load data. Please try again.', 'error');
                });
        }

        $(document).on('click', '.card-filter', function(e) {
            e.preventDefault();
            const filter = $(this).data('filter') || 'all';
            loadRequests(filter, true);
        });

        // Handle back/forward navigation to keep filter in sync
        window.addEventListener('popstate', function(event) {
            const params = new URLSearchParams(window.location.search);
            const filter = params.get('filter') || 'all';
            loadRequests(filter, false);
        });

        // If your DataTable is already initialized by table-data.js on load,
        // ensure it gets created once:
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable();
            }
        });

        // Existing confirmPayment remains unchanged
        function confirmPayment(requestId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Mark this payment as Paid?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, mark as Paid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('markPaymentForm_' + requestId).submit();
                }
            });
        }
        window.confirmPayment = confirmPayment; // make accessible after AJAX swaps
    </script>
@endsection
