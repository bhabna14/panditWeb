@extends('admin.layouts.apps')

@php
    use Illuminate\Support\Facades\Route;

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

        /* ============ FILTER GRID (Perfect alignment) ============ */
        .filter-grid{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        .filter-link{
            display:block;
            text-decoration:none !important;
        }
        .filter-card{
            border: 1px solid #e5e7eb !important;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(0,0,0,.04);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
            min-height: 110px;
        }
        .filter-card::before{
            content:'';
            position:absolute;
            inset:0;
            background: linear-gradient(135deg, rgba(249,250,251,.95), rgba(255,255,255,1));
            pointer-events:none;
        }
        .filter-card .card-body{
            position:relative;
            padding: 14px 14px 12px;
            display:flex;
            flex-direction:column;
            gap: 10px;
        }
        .filter-card:hover{
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba(0,0,0,.08);
        }
        .filter-card.is-active{
            border-color: #c7d2fe !important;
            box-shadow: 0 12px 30px rgba(99,102,241,.16);
        }

        .stat-top{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
        }
        .stat-title{
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .3px;
            color: #6b7280;
            text-transform: uppercase;
            line-height: 1.2;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stat-value{
            font-size: 26px;
            font-weight: 900;
            line-height: 1;
            margin: 0;
        }

        .icon-chip{
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
            border: 1px solid #eef2f7;
            background: #f9fafb;
        }
        .icon-chip i{ font-size: 18px; }

        .chip-warning{ background: rgba(245,158,11,.10); border-color: rgba(245,158,11,.18); }
        .chip-success{ background: rgba(34,197,94,.10);  border-color: rgba(34,197,94,.18); }
        .chip-info{    background: rgba(59,130,246,.10);  border-color: rgba(59,130,246,.18); }
        .chip-danger{  background: rgba(239,68,68,.10);   border-color: rgba(239,68,68,.18); }
        .chip-primary{ background: rgba(99,102,241,.10);  border-color: rgba(99,102,241,.18); }
        .chip-muted{   background: rgba(107,114,128,.10); border-color: rgba(107,114,128,.18); }

        .stat-meta{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            margin-top:auto;
            font-size: 12px;
            color:#6b7280;
        }

        .meta-pill{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background:#f9fafb;
            color:#111827;
            font-weight: 800;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-loading{
            padding: 48px 0;
            color:#6b7280;
        }
    </style>
@endsection

@section('content')

    {{-- Filter Cards --}}
    <div class="filter-grid">

        {{-- Total --}}
        <a href="{{ $pageUrl }}?filter=all" class="filter-link card-filter" data-filter="all">
            <div class="card filter-card {{ ($filter ?? 'all') === 'all' ? 'is-active' : '' }}" data-card="all">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Total Orders</div>
                            <div class="stat-value text-warning" id="totalCount">{{ $totalCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-warning"><i class="fa fa-list text-warning"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>All time</div>
                        <span class="meta-pill">Overview</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Today --}}
        <a href="{{ $pageUrl }}?filter=today" class="filter-link card-filter" data-filter="today">
            <div class="card filter-card {{ ($filter ?? 'all') === 'today' ? 'is-active' : '' }}" data-card="today">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Today's Orders</div>
                            <div class="stat-value text-success" id="todayCount">{{ $todayCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-success"><i class="fa fa-calendar-day text-success"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Scheduled today</div>
                        <span class="meta-pill">Today</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Approved (NEW CARD) --}}
        <a href="{{ $pageUrl }}?filter=approved" class="filter-link card-filter" data-filter="approved">
            <div class="card filter-card {{ ($filter ?? 'all') === 'approved' ? 'is-active' : '' }}" data-card="approved">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Approved</div>
                            <div class="stat-value text-primary" id="approvedCount">{{ $approvedCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-primary"><i class="fa fa-thumbs-up text-primary"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Status: Approved</div>
                        <span class="meta-pill">Approved</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Unpaid (Approved but not Paid) --}}
        <a href="{{ $pageUrl }}?filter=unpaid" class="filter-link card-filter" data-filter="unpaid">
            <div class="card filter-card {{ ($filter ?? 'all') === 'unpaid' ? 'is-active' : '' }}" data-card="unpaid">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Unpaid</div>
                            <div class="stat-value text-danger" id="unpaidCount">{{ $unpaidCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-danger"><i class="fa fa-hourglass-half text-danger"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>To collect</div>
                        <span class="meta-pill" id="unpaidAmount">
                            ₹{{ number_format((float)($unpaidAmountToCollect ?? 0), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Paid (with collected amount) --}}
        <a href="{{ $pageUrl }}?filter=paid" class="filter-link card-filter" data-filter="paid">
            <div class="card filter-card {{ ($filter ?? 'all') === 'paid' ? 'is-active' : '' }}" data-card="paid">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Paid Orders</div>
                            <div class="stat-value text-info" id="paidCount">{{ $paidCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-info"><i class="fa fa-check-circle text-info"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Collected</div>
                        <span class="meta-pill" id="paidAmount">
                            ₹{{ number_format((float)($paidCollectedAmount ?? 0), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Rejected (ONLY Rejected) --}}
        <a href="{{ $pageUrl }}?filter=rejected" class="filter-link card-filter" data-filter="rejected">
            <div class="card filter-card {{ ($filter ?? 'all') === 'rejected' ? 'is-active' : '' }}" data-card="rejected">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Rejected</div>
                            <div class="stat-value text-danger" id="rejectedCount">{{ $rejectCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-danger"><i class="fa fa-ban text-danger"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Status: Rejected</div>
                        <span class="meta-pill">Rejected</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Upcoming --}}
        <a href="{{ $pageUrl }}?filter=upcoming" class="filter-link card-filter" data-filter="upcoming">
            <div class="card filter-card {{ ($filter ?? 'all') === 'upcoming' ? 'is-active' : '' }}" data-card="upcoming">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Upcoming</div>
                            <div class="stat-value text-secondary" id="upcomingCount">{{ $upcomingCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-muted"><i class="fa fa-clock text-secondary"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Next 3 days</div>
                        <span class="meta-pill">Upcoming</span>
                    </div>
                </div>
            </div>
        </a>

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

            $('#totalCount').text(counts.total ?? $('#totalCount').text());
            $('#todayCount').text(counts.today ?? $('#todayCount').text());
            $('#upcomingCount').text(counts.upcoming ?? $('#upcomingCount').text());

            $('#approvedCount').text(counts.approved ?? $('#approvedCount').text());

            $('#paidCount').text(counts.paid ?? $('#paidCount').text());
            $('#paidAmount').text(counts.paid_amount_fmt ?? $('#paidAmount').text());

            $('#unpaidCount').text(counts.unpaid ?? $('#unpaidCount').text());
            $('#unpaidAmount').text(counts.unpaid_amount_fmt ?? $('#unpaidAmount').text());

            $('#rejectedCount').text(counts.rejected ?? $('#rejectedCount').text());
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
                .fail(function () {
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
                $('#file-datatable').DataTable({ pageLength: 25, order: [] });
            }
        });

        // Mark Paid flow (unchanged)
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
