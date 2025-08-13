@extends('admin.layouts.apps')

@section('styles')
    {{-- CSRF meta (use this if you need it for AJAX later) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Refer Offer</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ route('refer.offerCreate') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Offer</a>
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
                                    <th>Offer Name</th>
                                    <th>Refer & Benefit</th>
                                    <th>Description</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($offers as $idx => $offer)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-semibold">{{ $offer->offer_name }}</td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-view-pairs"
                                                data-offer="{{ e($offer->offer_name) }}"
                                                data-refer='@json($offer->no_of_refer ?? [])'
                                                data-benefit='@json($offer->benefit ?? [])'>
                                                View Pairs
                                            </button>
                                        </td>

                                        <td>{{ $offer->description }}</td>

                                        <td>
                                            @php $isActive = strtolower((string) ($offer->status ?? '')) === 'active'; @endphp
                                            @if ($isActive)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($offer->status ?? 'inactive') }}</span>
                                            @endif
                                        </td>

                                        <td class="text-nowrap">
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-edit-offer"
                                                title="Edit" data-id="{{ $offer->id }}"
                                                data-offer="{{ e($offer->offer_name) }}"
                                                data-description="{{ e($offer->description) }}"
                                                data-status="{{ strtolower((string) ($offer->status ?? 'inactive')) }}"
                                                data-refer='@json($offer->no_of_refer ?? [])'
                                                data-benefit='@json($offer->benefit ?? [])'>
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>

                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-offer"
                                                title="Delete" data-id="{{ $offer->id }}"
                                                data-name="{{ e($offer->offer_name) }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No offers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal to display pairs --}}
    <div class="modal fade" id="pairsModal" tabindex="-1" aria-labelledby="pairsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="pairsModalLabel">Refer & Benefit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <span class="fw-semibold">Offer:</span>
                        <span id="pairs-offer-name" class="ms-1"></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">#</th>
                                    <th>No. of Refer</th>
                                    <th>Benefit</th>
                                </tr>
                            </thead>
                            <tbody id="pairs-table-body">
                                {{-- populated via JS --}}
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

    <div class="modal fade" id="editOfferModal" tabindex="-1" aria-labelledby="editOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="edit-offer-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOfferModalLabel">Edit Offer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Offer Name</label>
                                <input type="text" class="form-control" name="offer_name" id="edit-offer_name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="edit-status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit-description" rows="3" required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Refer & Benefit</label>
                                <div id="edit-referBenefitFields">
                                    {{-- rows injected via JS --}}
                                </div>
                                <button type="button" class="btn btn-sm btn-success mt-2" id="edit-add-row">+ Add
                                    Row</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="edit-save-btn">Update</button>
                    </div>
                </div>
            </form>
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
    {{-- If your theme already auto-initializes tables via table-data.js, you can remove it to avoid double init --}}
    {{-- <script src="{{ asset('assets/js/table-data.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            // DataTable init (ensure it's not already initialized elsewhere)
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: [2, 5]
                        } // “Refer & Benefit” + “Actions” not sortable
                    ]
                });
            }

            const pairsModalEl = document.getElementById('pairsModal');
            const pairsModal = pairsModalEl ? new bootstrap.Modal(pairsModalEl) : null;

            // Click handler: render pairs in modal
            $(document).on('click', '.btn-view-pairs', function() {
                const offerName = $(this).data('offer') || '';
                let referArr = $(this).data('refer') || [];
                let benefitArr = $(this).data('benefit') || [];

                // In case some jQuery versions return strings, try parsing
                if (typeof referArr === 'string') {
                    try {
                        referArr = JSON.parse(referArr);
                    } catch (e) {}
                }
                if (typeof benefitArr === 'string') {
                    try {
                        benefitArr = JSON.parse(benefitArr);
                    } catch (e) {}
                }

                $('#pairs-offer-name').text(offerName);

                const tbody = $('#pairs-table-body').empty();
                const len = Math.min(referArr.length, benefitArr.length);

                if (len === 0) {
                    tbody.append(
                        '<tr><td colspan="3" class="text-center text-muted">No pairs found.</td></tr>');
                } else {
                    for (let i = 0; i < len; i++) {
                        const idx = i + 1;
                        const refer = referArr[i] ?? '';
                        const benefit = benefitArr[i] ?? '';
                        tbody.append(
                            `<tr>
                                <td>${idx}</td>
                                <td>${refer}</td>
                                <td>${benefit}</td>
                            </tr>`
                        );
                    }
                }

                if (pairsModal) pairsModal.show();
            });
        });
    </script>

    <script>
        $(function() {
            // Routes as templates for JS
            const updateRouteTmpl = @json(route('refer.offer.update', ['offer' => '__ID__']));
            const deleteRouteTmpl = @json(route('refer.offer.destroy', ['offer' => '__ID__']));

            const editModalEl = document.getElementById('editOfferModal');
            const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;

            function buildRow(referVal = '', benefitVal = '') {
                return $(`
            <div class="row g-2 align-items-end mb-2 refer-benefit-row">
                <div class="col-md-5">
                    <label class="form-label small mb-0">No. of Refer</label>
                    <input type="number" class="form-control" name="no_of_refer[]" min="1" value="${referVal}">
                </div>
                <div class="col-md-5">
                    <label class="form-label small mb-0">Benefit</label>
                    <input type="text" class="form-control" name="benefit[]" value="${benefitVal}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm edit-remove-row">-</button>
                </div>
            </div>
        `);
            }

            function refreshRemoveButtons() {
                const rows = $('#edit-referBenefitFields .refer-benefit-row');
                if (rows.length <= 1) {
                    rows.find('.edit-remove-row').prop('disabled', true);
                } else {
                    rows.find('.edit-remove-row').prop('disabled', false);
                }
            }

            // Open Edit Modal
            $(document).on('click', '.btn-edit-offer', function() {
                const id = $(this).data('id');
                const name = $(this).data('offer') || '';
                const desc = $(this).data('description') || '';
                const status = $(this).data('status') || 'inactive';
                let referArr = $(this).data('refer') || [];
                let benefitArr = $(this).data('benefit') || [];

                if (typeof referArr === 'string') {
                    try {
                        referArr = JSON.parse(referArr);
                    } catch (e) {}
                }
                if (typeof benefitArr === 'string') {
                    try {
                        benefitArr = JSON.parse(benefitArr);
                    } catch (e) {}
                }

                // Set form action
                const action = updateRouteTmpl.replace('__ID__', id);
                $('#edit-offer-form').attr('action', action);

                // Fill basics
                $('#edit-offer_name').val(name);
                $('#edit-description').val(desc);
                $('#edit-status').val(status);

                // Build rows
                const wrap = $('#edit-referBenefitFields').empty();
                const len = Math.max(referArr.length, benefitArr.length) || 1;

                for (let i = 0; i < len; i++) {
                    const r = referArr[i] ?? '';
                    const b = benefitArr[i] ?? '';
                    wrap.append(buildRow(r, b));
                }
                refreshRemoveButtons();

                if (editModal) editModal.show();
            });

            // Add row button
            $('#edit-add-row').on('click', function() {
                $('#edit-referBenefitFields').append(buildRow());
                refreshRemoveButtons();
            });

            // Remove row
            $(document).on('click', '.edit-remove-row', function() {
                $(this).closest('.refer-benefit-row').remove();
                if ($('#edit-referBenefitFields .refer-benefit-row').length === 0) {
                    $('#edit-referBenefitFields').append(buildRow());
                }
                refreshRemoveButtons();
            });

            // Save (confirm with SweetAlert, then submit)
            $('#edit-save-btn').on('click', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Update offer?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update',
                        cancelButtonText: 'Cancel'
                    }).then((res) => {
                        if (res.isConfirmed) {
                            $('#edit-offer-form')[0].submit();
                        }
                    });
                } else {
                    $('#edit-offer-form')[0].submit();
                }
            });

            // Delete offer
            $(document).on('click', '.btn-delete-offer', function() {
                const id = $(this).data('id');
                const name = $(this).data('name') || 'this offer';
                const action = deleteRouteTmpl.replace('__ID__', id);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete offer?',
                        html: `You are about to delete <strong>${name}</strong>. This cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel'
                    }).then((res) => {
                        if (res.isConfirmed) {
                            const form = $('#delete-offer-form');
                            form.attr('action', action);
                            form.trigger('submit');
                        }
                    });
                } else {
                    const form = $('#delete-offer-form');
                    form.attr('action', action);
                    form.trigger('submit');
                }
            });

            // Toast on success
            @if (session('success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: @json(session('success')),
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            @endif
        });
    </script>
@endsection
