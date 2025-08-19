@extends('admin.layouts.apps')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        .badge-status { letter-spacing: .2px; }
        .table td, .table th { vertical-align: middle; }
        .claim-meta { font-size: 12px; color: #6c757d; }
        .pairs-pill { font-size: 12px; }
    </style>

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Offer Claimed</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ route('refer.offerClaim') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Offer Claimed</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Offer</a></li>
            </ol>
        </div>
    </div>

    {{-- ⬇️ Tabs --}}
    @php
        $counts = $counts ?? collect();
        $status = $status ?? 'claimed';
        $cClaimed  = (int) ($counts['claimed'] ?? 0);
        $cApproved = (int) ($counts['approved'] ?? 0);
        $cRejected = (int) ($counts['rejected'] ?? 0);
    @endphp

    <ul class="nav nav-tabs mb-3" id="claimTabs" role="tablist" data-current-status="{{ $status }}">
        <li class="nav-item" role="presentation">
            <a href="#" class="nav-link claim-tab {{ $status === 'claimed' ? 'active' : '' }}" data-status="claimed" role="tab">
                Claimed <span class="badge bg-secondary" id="count-claimed">{{ $cClaimed }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#" class="nav-link claim-tab {{ $status === 'approved' ? 'active' : '' }}" data-status="approved" role="tab">
                Approved <span class="badge bg-secondary" id="count-approved">{{ $cApproved }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#" class="nav-link claim-tab {{ $status === 'rejected' ? 'active' : '' }}" data-status="rejected" role="tab">
                Rejected <span class="badge bg-secondary" id="count-rejected">{{ $cRejected }}</span>
            </a>
        </li>
    </ul>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">Sl No</th>
                                    <th>User</th>
                                    <th>Offer</th>
                                    <th>Selected Benefits</th>
                                    <th>Claim Date & Time</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="claims-tbody">
                                {{-- initial rows --}}
                                @include('refer.offer-claim-rows', ['claimedOffer' => $claimedOffer])
                            </tbody>
                        </table>

                        {{-- Hidden forms for update/delete --}}
                        <form id="status-form" method="POST" class="d-none">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" id="status-input">
                        </form>

                        <form id="delete-form" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- View Details Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Claim Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold">User</div>
                            <div id="v-user"></div>
                            <div class="claim-meta" id="v-user-meta"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold">Offer</div>
                            <div id="v-offer"></div>
                            <div class="claim-meta" id="v-offer-meta"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold">Claimed At</div>
                            <div id="v-datetime"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold">Status</div>
                            <div id="v-status"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="fw-semibold mb-2">Selected Benefits</div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">#</th>
                                    <th>No. of Refer</th>
                                    <th>Benefit</th>
                                </tr>
                            </thead>
                            <tbody id="v-pairs">
                                {{-- rows via JS --}}
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Internal Data tables -->
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Tabs + DataTable re-init + View/Status/Delete --}}
    <script>
        $(function() {
            const csrf = $('meta[name="csrf-token"]').attr('content');
            const listUrl = @json(route('refer.offerClaims.list'));
            let dt = null;

            function initDataTable() {
                if ($.fn.DataTable) {
                    if (dt) { dt.destroy(); dt = null; }
                    dt = $('#file-datatable').DataTable({
                        pageLength: 10,
                        order: [[0, 'asc']],
                        columnDefs: [{ orderable: false, targets: [6] }],
                        responsive: true,
                        autoWidth: false
                    });
                }
            }

            function setActive(status) {
                $('.claim-tab').removeClass('active');
                $('.claim-tab[data-status="'+status+'"]').addClass('active');
            }

            function updateCounts(counts) {
                if (!counts) return;
                $('#count-claimed').text(counts.claimed ?? 0);
                $('#count-approved').text(counts.approved ?? 0);
                $('#count-rejected').text(counts.rejected ?? 0);
            }

            function showLoading() {
                $('#claims-tbody').html(
                    '<tr><td colspan="7" class="text-center py-4">' +
                    '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>' +
                    '</td></tr>'
                );
            }

            function loadClaims(status) {
                setActive(status);
                showLoading();
                $.get(listUrl, { status: status })
                .done(function(resp) {
                    if (dt) { dt.destroy(); dt = null; }
                    $('#claims-tbody').html(resp.html);
                    updateCounts(resp.counts);
                    initDataTable();
                    // update query param without reload
                    if (history.pushState) {
                        const url = new URL(window.location);
                        url.searchParams.set('status', status);
                        history.replaceState(null, '', url);
                    }
                })
                .fail(function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to load data.';
                    $('#claims-tbody').html('<tr><td colspan="7" class="text-center text-danger">'+msg+'</td></tr>');
                });
            }

            // Initial DataTable
            initDataTable();

            // Tab click → load via AJAX
            $(document).on('click', '.claim-tab', function (e) {
                e.preventDefault();
                const status = $(this).data('status');
                loadClaims(status);
            });

            // View details (modal)
            const viewModalEl = document.getElementById('viewModal');
            const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;

            $(document).on('click', '.btn-view', function() {
                const data  = $(this).data('claim') || {};
                const pairs = Array.isArray(data.selected_pairs) ? data.selected_pairs : [];

                $('#v-user').text((data.user && data.user.name) ? data.user.name : '-');
                $('#v-user-meta').html(
                    `ID: ${data.user_id}${data.user && data.user.mobile_number ? '<br>Ph: '+data.user.mobile_number : ''}`
                );

                $('#v-offer').text((data.offer && data.offer.offer_name) ? data.offer.offer_name : '-');
                $('#v-offer-meta').text('Offer ID: ' + (data.offer_id ?? '-'));

                $('#v-datetime').text((data.date_time ?? '').toString().replace('T',' ').replace('.000000Z','') || '-');

                const s = (data.status || '').toLowerCase();
                let badge = '<span class="badge bg-primary">Claimed</span>';
                if (s === 'approved') badge = '<span class="badge bg-success">Approved</span>';
                if (s === 'rejected') badge = '<span class="badge bg-secondary">Rejected</span>';
                $('#v-status').html(badge);

                const $tbody = $('#v-pairs').empty();
                if (!pairs.length) {
                    $tbody.append('<tr><td colspan="3" class="text-center text-muted">None</td></tr>');
                } else {
                    pairs.forEach((p, i) => {
                        $tbody.append(`<tr>
                            <td>${i + 1}</td>
                            <td>${p.refer ?? '-'}</td>
                            <td>${p.benefit ?? '-'}</td>
                        </tr>`);
                    });
                }

                if (viewModal) viewModal.show();
            });

            // Approve/Reject with confirm
            $(document).on('click', '.btn-status', function() {
                const action = $(this).data('action');
                const status = $(this).data('status');

                Swal.fire({
                    title: `Confirm ${status}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: `Yes, ${status}`,
                    cancelButtonText: 'Cancel'
                }).then(res => {
                    if (res.isConfirmed) {
                        const $form = $('#status-form');
                        $('#status-input').val(status);
                        $form.attr('action', action).trigger('submit');
                    }
                });
            });

            // Delete with confirm
            $(document).on('click', '.btn-delete', function() {
                const action = $(this).data('action');

                Swal.fire({
                    title: 'Delete this claim?',
                    html: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then(res => {
                    if (res.isConfirmed) {
                        const $form = $('#delete-form');
                        $form.attr('action', action).trigger('submit');
                    }
                });
            });

            // Flash messages
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 1800, showConfirmButton: false });
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) });
            @endif
        });
    </script>

    {{-- Approve via 6-digit code (unchanged) --}}
    <script>
        $(function() {
            const csrf = $('meta[name="csrf-token"]').attr('content');

            $(document).on('click', '.btn-approve-code', function() {
                const $btn = $(this);
                const startUrl = $btn.data('start-url');
                const verifyUrl = $btn.data('verify-url');

                $btn.prop('disabled', true);

                // 1) Ask server to generate/store a code
                $.ajax({
                    url: startUrl,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    success: function(res) {
                        $btn.prop('disabled', false);

                        if (!res || res.success !== true || !res.code) {
                            Swal.fire({ icon: 'error', title: 'Error', text: (res && res.message) || 'Failed to start approval.' });
                            return;
                        }

                        const generatedCode = String(res.code);

                        // 2) Show code input and confirm
                        Swal.fire({
                            title: 'Confirm Approval',
                            html: `<div class="mb-2">Enter the following 6-digit code to approve:</div>`,
                            input: 'text',
                            inputAttributes: { maxlength: 6, inputmode: 'numeric', autocapitalize: 'off', autocorrect: 'off' },
                            inputValidator: (value) => {
                                if (!/^\d{6}$/.test(value)) return 'Please enter the 6-digit code.';
                                if (value !== generatedCode) return 'Code does not match. Please check and try again.';
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Verify & Approve',
                            cancelButtonText: 'Cancel',
                            preConfirm: (value) => {
                                // 3) Verify with server
                                return $.ajax({
                                    url: verifyUrl,
                                    type: 'POST',
                                    headers: { 'X-CSRF-TOKEN': csrf },
                                    data: { code: value }
                                }).then(function(r) {
                                    if (!r || r.success !== true) {
                                        throw new Error((r && r.message) || 'Verification failed.');
                                    }
                                    return r;
                                }).catch(function(err) {
                                    Swal.showValidationMessage(err.message || 'Verification failed.');
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved',
                                    text: 'Claim approved successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => window.location.reload());
                            }
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to start approval.';
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    }
                });
            });
        });
    </script>
@endsection
