@extends('admin.layouts.apps')

@section('styles')
    {{-- CSRF meta (use this if you need it for AJAX later) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        .badge-status {
            letter-spacing: .2px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .claim-meta {
            font-size: 12px;
            color: #6c757d;
        }

        .pairs-pill {
            font-size: 12px;
        }
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
                <a href="{{ route('refer.offerClaim') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Offer
                    Claimed</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Offer</a></li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered table-striped align-middle">
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
                            <tbody>
                                @forelse ($claimedOffer as $idx => $c)
                                    @php
                                        $pairs = is_array($c->selected_pairs) ? $c->selected_pairs : [];
                                        $firstTwo = array_slice($pairs, 0, 2);
                                        $moreCount = max(count($pairs) - 2, 0);
                                        $user = $c->user;
                                        $offer = $c->offer;
                                        $statusLower = strtolower((string) $c->status);
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>

                                        <td>
                                            <div class="fw-semibold">{{ $user?->name ?? '-' }}</div>
                                            <div class="claim-meta">
                                                ID: {{ $c->user_id }}<br>
                                                @if ($user?->mobile_number)
                                                    Ph: {{ $user->mobile_number }}
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="fw-semibold">{{ $offer?->offer_name ?? '-' }}</div>
                                            <div class="claim-meta">Offer ID: {{ $c->offer_id }}</div>
                                        </td>

                                        <td>
                                            @if (count($pairs))
                                                @foreach ($firstTwo as $p)
                                                    <span class="badge bg-light text-dark pairs-pill me-1 mb-1">
                                                        Refer {{ $p['refer'] ?? '-' }} → {{ $p['benefit'] ?? '-' }}
                                                    </span>
                                                @endforeach
                                                @if ($moreCount > 0)
                                                    <span class="badge bg-secondary pairs-pill">+{{ $moreCount }}
                                                        more</span>
                                                @endif
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ optional($c->date_time)->format('d M Y, h:i A') ?? '-' }}
                                            <div class="claim-meta">Created: {{ $c->created_at?->format('d M Y, h:i A') }}
                                            </div>
                                        </td>

                                        <td>
                                            @if ($statusLower === 'approved')
                                                <span class="badge bg-success badge-status">Approved</span>
                                            @elseif ($statusLower === 'rejected')
                                                <span class="badge bg-secondary badge-status">Rejected</span>
                                            @else
                                                <span class="badge bg-primary badge-status">Claimed</span>
                                            @endif
                                        </td>

                                        <td class="text-nowrap">
                                            <button type="button" class="btn btn-sm btn-outline-info btn-view"
                                                title="View" data-claim='@json($c)'>
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            {{-- Approve with code-confirm --}}
                                            @if ($statusLower !== 'approved')
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success btn-approve-code"
                                                    title="Approve (Code)"
                                                    data-start-url="{{ route('refer.claim.approve.start', $c->id) }}"
                                                    data-verify-url="{{ route('refer.claim.approve.verify', $c->id) }}">
                                                    <i class="bi bi-check2-circle"></i>
                                                </button>
                                            @endif


                                            {{-- Reject --}}
                                            @if ($statusLower !== 'rejected')
                                                <button type="button" class="btn btn-sm btn-outline-warning btn-status"
                                                    data-action="{{ route('refer.claim.update', $c->id) }}"
                                                    data-status="rejected" title="Reject">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @endif

                                            {{-- Delete --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-action="{{ route('refer.claim.destroy', $c->id) }}" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No claims found.</td>
                                    </tr>
                                @endforelse
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
    {{-- <script src="{{ asset('assets/js/table-data.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            // DataTable
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [6]
                    }]
                });
            }

            const viewModalEl = document.getElementById('viewModal');
            const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;

            // View details
            $(document).on('click', '.btn-view', function() {
                const data = $(this).data('claim') || {};
                const pairs = Array.isArray(data.selected_pairs) ? data.selected_pairs : [];

                $('#v-user').text((data.user && data.user.name) ? data.user.name : '-');
                $('#v-user-meta').html(
                    `ID: ${data.user_id}${data.user && data.user.mobile_number ? '<br>Ph: '+data.user.mobile_number : ''}`
                );

                $('#v-offer').text((data.offer && data.offer.offer_name) ? data.offer.offer_name : '-');
                $('#v-offer-meta').text('Offer ID: ' + (data.offer_id ?? '-'));

                // datetime (server returns ISO; keep simple)
                $('#v-datetime').text((data.date_time ?? '').toString().replace('T', ' ').replace(
                    '.000000Z', '') || '-');

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
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    timer: 1800,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error'))
                });
            @endif
        });
    </script>

    <script>
$(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');

    // Approve flow: generate code -> admin re-enters code -> verify -> approve
    $(document).on('click', '.btn-approve-code', function () {
        const $btn      = $(this);
        const startUrl  = $btn.data('start-url');
        const verifyUrl = $btn.data('verify-url');

        $btn.prop('disabled', true);

        // 1) Ask server to generate/store a code
        $.ajax({
            url: startUrl,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            success: function (res) {
                $btn.prop('disabled', false);

                if (!res || res.success !== true || !res.code) {
                    Swal.fire({ icon: 'error', title: 'Error', text: (res && res.message) || 'Failed to start approval.' });
                    return;
                }

                const generatedCode = String(res.code);

                // 2) Show code and ask admin to type it to confirm
                Swal.fire({
                    title: 'Confirm Approval',
                    html:
                        `<div class="mb-2">Enter the following 6-digit code to approve:</div>
                         <div class="display-6 fw-bold mb-3">${generatedCode}</div>`,
                    input: 'text',
                    inputAttributes: { maxlength: 6, inputmode: 'numeric', autocapitalize:'off', autocorrect:'off' },
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
                        }).then(function (r) {
                            if (!r || r.success !== true) {
                                throw new Error((r && r.message) || 'Verification failed.');
                            }
                            return r;
                        }).catch(function (err) {
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
            error: function (xhr) {
                $btn.prop('disabled', false);
                const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to start approval.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });
});
</script>

@endsection
