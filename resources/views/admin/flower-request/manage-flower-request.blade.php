@extends('admin.layouts.apps')

@php
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | IMPORTANT
    |--------------------------------------------------------------------------
    | Change these route names ONLY if your project uses different names.
    | - $pageUrl:  main SSR page (showRequests)
    | - $ajaxUrl:  ajax endpoint (ajaxData)
    */
    $pageUrl = Route::has('flower.customize.request')
        ? route('flower.customize.request')
        : url()->current();

    $ajaxUrl = Route::has('admin.flower-request.ajaxData')
        ? route('admin.flower-request.ajaxData')
        : (Route::has('admin.flower-request.data')
            ? route('admin.flower-request.data')
            : url('/admin/flower-request/ajax-data'));
@endphp

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .badge { font-size: 0.8rem; }
        .table td, .table th { vertical-align: middle !important; }
        .action-btns .btn { margin: 2px 0; }

        /* Metric cards */
        .metric-card{
            border: 1px solid #e5e7eb !important;
            border-radius: 18px;
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: 0 4px 14px rgba(0,0,0,.04);
            transition: all .2s ease;
        }
        .metric-card .card-body{ padding: 16px 18px; }
        .metric-icon{
            width: 44px; height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
        }
        .metric-icon i{ font-size: 18px; }
        .metric-title{ font-size: 13px; font-weight: 700; letter-spacing: .2px; color: #6b7280; }
        .metric-value{ font-size: 26px; font-weight: 800; line-height: 1.1; margin: 0; }

        .card-filter:hover .metric-card{
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0,0,0,.08);
        }

        .metric-card.is-active{
            border-color: #c7d2fe !important;
            box-shadow: 0 10px 26px rgba(99,102,241,.16);
        }

        .metric-sub{
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }
        .metric-sub b{ color:#111827; }

        /* Loading row */
        .table-loading{
            padding: 48px 0;
            color:#6b7280;
        }
    </style>
@endsection

@section('content')

    {{-- Cards --}}
    <div class="row mt-4 mb-3">

        {{-- Total --}}
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ $pageUrl }}?filter=all" class="card-filter text-decoration-none" data-filter="all">
                <div class="card metric-card h-100 {{ ($filter ?? 'all') === 'all' ? 'is-active' : '' }}" data-card="all">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="metric-icon">
                            <i class="fa fa-list text-warning"></i>
                        </div>
                        <div>
                            <div class="metric-title">TOTAL ORDERS</div>
                            <div class="metric-value text-warning" id="totalCount">{{ $totalCustomizeOrders ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Today --}}
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ $pageUrl }}?filter=today" class="card-filter text-decoration-none" data-filter="today">
                <div class="card metric-card h-100 {{ ($filter ?? 'all') === 'today' ? 'is-active' : '' }}" data-card="today">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="metric-icon">
                            <i class="fa fa-calendar-day text-success"></i>
                        </div>
                        <div>
                            <div class="metric-title">TODAY'S ORDERS</div>
                            <div class="metric-value text-success" id="todayCount">{{ $todayCustomizeOrders ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Paid --}}
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ $pageUrl }}?filter=paid" class="card-filter text-decoration-none" data-filter="paid">
                <div class="card metric-card h-100 {{ ($filter ?? 'all') === 'paid' ? 'is-active' : '' }}" data-card="paid">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="metric-icon">
                            <i class="fa fa-check-circle text-info"></i>
                        </div>
                        <div>
                            <div class="metric-title">PAID ORDERS</div>
                            <div class="metric-value text-info" id="paidCount">{{ $paidCustomizeOrders ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Unpaid (NEW) --}}
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
            <a href="{{ $pageUrl }}?filter=unpaid" class="card-filter text-decoration-none" data-filter="unpaid">
                <div class="card metric-card h-100 {{ ($filter ?? 'all') === 'unpaid' ? 'is-active' : '' }}" data-card="unpaid">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="metric-icon">
                            <i class="fa fa-hourglass-half text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="metric-title">UNPAID ORDERS</div>
                            <div class="d-flex align-items-end justify-content-between gap-3">
                                <div class="metric-value text-danger" id="unpaidCount">{{ $unpaidCustomizeOrders ?? 0 }}</div>
                                <div class="metric-sub text-end">
                                    Collect:
                                    <b id="unpaidAmount">₹{{ number_format((float)($unpaidAmountToCollect ?? 0), 2) }}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Rejected --}}
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ $pageUrl }}?filter=rejected" class="card-filter text-decoration-none" data-filter="rejected">
                <div class="card metric-card h-100 {{ ($filter ?? 'all') === 'rejected' ? 'is-active' : '' }}" data-card="rejected">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="metric-icon">
                            <i class="fa fa-ban text-primary"></i>
                        </div>
                        <div>
                            <div class="metric-title">REJECTED</div>
                            <div class="metric-value text-primary" id="rejectedCount">{{ $rejectCustomizeOrders ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Upcoming --}}
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ $pageUrl }}?filter=upcoming" class="card-filter text-decoration-none" data-filter="upcoming">
                <div class="card metric-card h-100 {{ ($filter ?? 'all') === 'upcoming' ? 'is-active' : '' }}" data-card="upcoming">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="metric-icon">
                            <i class="fa fa-clock text-secondary"></i>
                        </div>
                        <div>
                            <div class="metric-title">UPCOMING (NEXT 3 DAYS)</div>
                            <div class="metric-value text-secondary" id="upcomingCount">{{ $upcomingCustomizeOrders ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- Table --}}
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
    {{-- Datatables JS (keep if not already loaded in layout) --}}
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/responsive.bootstrap5.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const AJAX_URL = @json($ajaxUrl);

        function reinitDataTable() {
            if ($.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable().destroy();
            }
            $('#file-datatable').DataTable({
                pageLength: 25,
                order: [],
            });
        }

        function setActiveCard(filter) {
            $('[data-card]').removeClass('is-active');
            $('[data-card="' + filter + '"]').addClass('is-active');
        }

        function updateCounts(counts) {
            if (!counts) return;

            $('#totalCount').text(counts.total ?? 0);
            $('#todayCount').text(counts.today ?? 0);
            $('#paidCount').text(counts.paid ?? 0);
            $('#rejectedCount').text(counts.rejected ?? 0);
            $('#upcomingCount').text(counts.upcoming ?? {{ (int)($upcomingCustomizeOrders ?? 0) }});

            // NEW: unpaid
            $('#unpaidCount').text(counts.unpaid ?? {{ (int)($unpaidCustomizeOrders ?? 0) }});
            $('#unpaidAmount').text(counts.unpaid_amount_fmt ?? '₹{{ number_format((float)($unpaidAmountToCollect ?? 0), 2) }}');
        }

        function loadRequests(filter, pushUrl = true) {
            const $tbody = $('#requestsBody');
            const prevHtml = $tbody.html();

            $tbody.html('<tr><td colspan="11" class="text-center table-loading">Loading...</td></tr>');

            $.get(AJAX_URL, { filter: filter })
                .done(function (res) {
                    if (res && res.rows_html !== undefined) {
                        $tbody.html(res.rows_html);
                        reinitDataTable();

                        updateCounts(res.counts || {});
                        setActiveCard(res.active || filter);

                        if (pushUrl) {
                            const pageUrl = new URL(window.location);
                            pageUrl.searchParams.set('filter', res.active || filter);
                            window.history.pushState({ filter: res.active || filter }, '', pageUrl.toString());
                        }
                    } else {
                        $tbody.html('<tr><td colspan="11" class="text-center text-danger table-loading">Unexpected response</td></tr>');
                    }
                })
                .fail(function (xhr) {
                    $tbody.html(prevHtml);
                    Swal.fire('Error', 'Failed to load data. Please try again.', 'error');
                });
        }

        $(document).on('click', '.card-filter', function (e) {
            e.preventDefault();
            const filter = $(this).data('filter') || 'all';
            loadRequests(filter, true);
        });

        window.addEventListener('popstate', function () {
            const params = new URLSearchParams(window.location.search);
            const filter = params.get('filter') || 'all';
            loadRequests(filter, false);
        });

        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable({
                    pageLength: 25,
                    order: [],
                });
            }
        });

        // Optional: your existing "Mark Paid" flow (keep only if you use it)
        function confirmPayment(requestId) {
            Swal.fire({
                title: 'Select payment method',
                input: 'radio',
                inputOptions: {
                    upi: 'UPI',
                    razorpay: 'Razorpay',
                    cash: 'Cash'
                },
                inputValidator: (value) => {
                    if (!value) return 'Please select a payment method';
                },
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Mark as Paid'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('markPaymentForm_' + requestId);
                    if (!form) return;

                    const input = form.querySelector('input[name="payment_method"]');
                    if (!input) return;

                    input.value = result.value;
                    form.submit();
                }
            });
        }
        window.confirmPayment = confirmPayment;
    </script>
@endsection
