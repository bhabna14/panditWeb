@extends('admin.layouts.apps')

@php
    $pageUrl = route('flower.customize.request');
    $ajaxUrl = route('admin.flower-request.data');

    // for reject modal action building
    $rejectUrlTemplate = route('admin.flower-request.reject', ['flowerRequest' => '___ID___']);
@endphp

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .table td,
        .table th {
            vertical-align: middle !important;
        }

        .badge {
            font-size: .80rem;
        }

        /* Cards layout */
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
            margin: 14px 0 18px;
        }

        .filter-link {
            display: block;
            text-decoration: none !important;
        }

        .filter-card {
            border: 1px solid #e5e7eb !important;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
            min-height: 110px;
        }

        .filter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba(0, 0, 0, .08);
        }

        .filter-card.is-active {
            border-color: #c7d2fe !important;
            box-shadow: 0 12px 30px rgba(99, 102, 241, .16);
        }

        .filter-card .card-body {
            padding: 14px 14px 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stat-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-title {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .3px;
            color: #6b7280;
            text-transform: uppercase;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 900;
            line-height: 1;
            margin: 0;
        }

        .icon-chip {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eef2f7;
            background: #f9fafb;
        }

        .icon-chip i {
            font-size: 18px;
        }

        .chip-warning {
            background: rgba(245, 158, 11, .10);
            border-color: rgba(245, 158, 11, .18);
        }

        .chip-success {
            background: rgba(34, 197, 94, .10);
            border-color: rgba(34, 197, 94, .18);
        }

        .chip-info {
            background: rgba(59, 130, 246, .10);
            border-color: rgba(59, 130, 246, .18);
        }

        .chip-danger {
            background: rgba(239, 68, 68, .10);
            border-color: rgba(239, 68, 68, .18);
        }

        .chip-primary {
            background: rgba(99, 102, 241, .10);
            border-color: rgba(99, 102, 241, .18);
        }

        .stat-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: auto;
            font-size: 12px;
            color: #6b7280;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            color: #111827;
            font-weight: 800;
            white-space: nowrap;
        }

        .table-loading {
            padding: 48px 0;
            color: #6b7280;
        }

        .action-btns .btn {
            margin-bottom: 8px;
        }
    </style>
@endsection

@section('content')
    {{-- FILTER CARDS --}}
    <div class="filter-grid">

        {{-- All --}}
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

        {{-- Pending --}}
        <a href="{{ $pageUrl }}?filter=pending" class="filter-link card-filter" data-filter="pending">
            <div class="card filter-card {{ ($filter ?? 'all') === 'pending' ? 'is-active' : '' }}" data-card="pending">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Pending</div>
                            <div class="stat-value text-warning" id="pendingCount">{{ $pendingCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-warning"><i class="fa fa-hourglass-start text-warning"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Status</div>
                        <span class="meta-pill">Pending</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Approved --}}
        <a href="{{ $pageUrl }}?filter=approved" class="filter-link card-filter" data-filter="approved">
            <div class="card filter-card {{ ($filter ?? 'all') === 'approved' ? 'is-active' : '' }}" data-card="approved">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Approved</div>
                            <div class="stat-value text-primary" id="approvedCount">{{ $approvedCustomizeOrders ?? 0 }}
                            </div>
                        </div>
                        <div class="icon-chip chip-primary"><i class="fa fa-thumbs-up text-primary"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Status</div>
                        <span class="meta-pill">Approved</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Unpaid --}}
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
                        <div>To Collect</div>
                        <span class="meta-pill" id="unpaidAmount">
                            ₹{{ number_format((float) ($unpaidAmountToCollect ?? 0), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Paid --}}
        <a href="{{ $pageUrl }}?filter=paid" class="filter-link card-filter" data-filter="paid">
            <div class="card filter-card {{ ($filter ?? 'all') === 'paid' ? 'is-active' : '' }}" data-card="paid">
                <div class="card-body">
                    <div class="stat-top">
                        <div>
                            <div class="stat-title">Paid</div>
                            <div class="stat-value text-info" id="paidCount">{{ $paidCustomizeOrders ?? 0 }}</div>
                        </div>
                        <div class="icon-chip chip-info"><i class="fa fa-check-circle text-info"></i></div>
                    </div>
                    <div class="stat-meta">
                        <div>Collected</div>
                        <span class="meta-pill" id="paidAmount">
                            ₹{{ number_format((float) ($paidCollectedAmount ?? 0), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Rejected --}}
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
                        <div>Status</div>
                        <span class="meta-pill">Rejected</span>
                    </div>
                </div>
            </div>
        </a>

    </div>

    {{-- TABLE --}}
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
                            <th>Del. Status</th>
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

    {{-- REJECT MODAL --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="rejectForm" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Reject Order: <span id="rejectRequestCode"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label fw-bold">Reject Reason</label>
                        <textarea id="rejectReason" name="reason" class="form-control" rows="4" placeholder="Enter reject reason..."
                            required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Save Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- VIEW REJECT REASON MODAL --}}
    <div class="modal fade" id="viewRejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Rejected Reason: <span id="viewRejectRequestCode"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="p-3 bg-light rounded" id="viewRejectReasonText" style="white-space: pre-wrap;"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const AJAX_URL = @json($ajaxUrl);
        const REJECT_URL_TEMPLATE = @json($rejectUrlTemplate);

        function destroyDataTable() {
            if ($.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable().clear().destroy();
            }
        }

        function initDataTable() {
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
            $('#pendingCount').text(counts.pending ?? $('#pendingCount').text());
            $('#approvedCount').text(counts.approved ?? $('#approvedCount').text());
            $('#paidCount').text(counts.paid ?? $('#paidCount').text());
            $('#paidAmount').text(counts.paid_amount_fmt ?? $('#paidAmount').text());
            $('#unpaidCount').text(counts.unpaid ?? $('#unpaidCount').text());
            $('#unpaidAmount').text(counts.unpaid_amount_fmt ?? $('#unpaidAmount').text());
            $('#rejectedCount').text(counts.rejected ?? $('#rejectedCount').text());
        }

        function loadRequests(filter, pushUrl = true) {
            const $tbody = $('#requestsBody');

            // IMPORTANT FIX: destroy datatable before changing tbody
            destroyDataTable();

            $tbody.html('<tr><td colspan="11" class="text-center table-loading">Loading...</td></tr>');

            $.get(AJAX_URL, {
                    filter: filter
                })
                .done(function(res) {
                    if (res && res.rows_html !== undefined) {
                        $tbody.html(res.rows_html);
                        initDataTable();
                        updateCounts(res.counts || {});
                        setActiveCard(res.active || filter);

                        if (pushUrl) {
                            const url = new URL(window.location);
                            url.searchParams.set('filter', res.active || filter);
                            window.history.pushState({
                                filter: res.active || filter
                            }, '', url.toString());
                        }
                    } else {
                        $tbody.html(
                            '<tr><td colspan="11" class="text-center text-danger table-loading">Unexpected response</td></tr>'
                            );
                        initDataTable();
                    }
                })
                .fail(function() {
                    $tbody.html(
                        '<tr><td colspan="11" class="text-center text-danger table-loading">Failed to load</td></tr>'
                        );
                    initDataTable();
                    Swal.fire('Error', 'Failed to load data. Please try again.', 'error');
                });
        }

        // Card click filter
        $(document).on('click', '.card-filter', function(e) {
            e.preventDefault();
            const filter = $(this).data('filter') || 'all';
            loadRequests(filter, true);
        });

        // Browser back/forward support
        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const filter = params.get('filter') || 'all';
            loadRequests(filter, false);
        });

        // Initial
        $(document).ready(function() {
            initDataTable();
        });

        // Reject button -> open modal
        $(document).on('click', '.btn-reject', function() {
            const id = $(this).data('id'); // FlowerRequest numeric id
            const req = $(this).data('req'); // request_id string

            $('#rejectRequestCode').text('#' + req);
            $('#rejectReason').val('');

            const action = REJECT_URL_TEMPLATE.replace('___ID___', id);
            $('#rejectForm').attr('action', action);

            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        });

        // View reject reason
        $(document).on('click', '.btn-view-reject', function() {
            const req = $(this).data('req');
            const reason = $(this).data('reason') || '--';

            $('#viewRejectRequestCode').text('#' + req);
            $('#viewRejectReasonText').text(reason);

            const modal = new bootstrap.Modal(document.getElementById('viewRejectModal'));
            modal.show();
        });

        // SweetAlert payment method picker
        function confirmPayment(requestId) {
            Swal.fire({
                title: 'Confirm Payment',
                text: 'Select payment method to mark this order as PAID.',
                icon: 'question',
                input: 'select',
                inputOptions: {
                    upi: 'UPI',
                    razorpay: 'Razorpay',
                    cash: 'Cash'
                },
                inputPlaceholder: 'Select payment method',
                showCancelButton: true,
                confirmButtonText: 'Mark Paid',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) return 'Please select a payment method';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const method = result.value;
                    const formId = '#markPaymentForm_' + requestId;
                    const $form = $(formId);

                    $form.find('input[name="payment_method"]').val(method);
                    $form.submit();
                }
            });
        }

        // expose to global for inline onclick
        window.confirmPayment = confirmPayment;
    </script>
@endsection
