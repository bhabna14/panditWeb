@extends('admin.layouts.apps')

@section('styles')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

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
                                    @php
                                        $pairsCount = min(
                                            count($offer->no_of_refer ?? []),
                                            count($offer->benefit ?? []),
                                        );
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-semibold">{{ $offer->offer_name }}</td>

                                        {{-- Compact summary in cell (first 2 pairs) --}}
                                        <td>
                                            @if ($pairsCount > 0)
                                                <div class="small text-muted">
                                                    @for ($i = 0; $i < min($pairsCount, 2); $i++)
                                                        <div>{{ $offer->no_of_refer[$i] }} → {{ $offer->benefit[$i] }}</div>
                                                    @endfor
                                                    @if ($pairsCount > 2)
                                                        <div class="text-secondary">+ {{ $pairsCount - 2 }} more…</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No pairs</span>
                                            @endif
                                        </td>

                                        <td>{{ $offer->description }}</td>

                                        <td>
                                            @if (strtolower($offer->status) === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($offer->status ?? 'inactive') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-view-pairs"
                                                data-offer="{{ e($offer->offer_name) }}"
                                                data-refer='@json($offer->no_of_refer ?? [])'
                                                data-benefit='@json($offer->benefit ?? [])'>
                                                View Pairs
                                            </button>
                                            {{-- Add Edit/Delete if needed --}}
                                            {{-- <a href="{{ route('refer.offer.edit', $offer->id) }}" class="btn btn-sm btn-outline-warning">Edit</a> --}}
                                            {{-- <button class="btn btn-sm btn-outline-danger">Delete</button> --}}
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
        <script src="{{ asset('assets/js/table-data.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- INTERNAL Select2 js -->
        <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(function() {
                // DataTable init (optional)
                if ($.fn.DataTable) {
                    $('#file-datatable').DataTable({
                        pageLength: 10,
                        order: [
                            [0, 'asc']
                        ],
                        columnDefs: [{
                            orderable: false,
                            targets: [2, 5]
                        }]
                    });
                }

                // Modal instance
                const pairsModalEl = document.getElementById('pairsModal');
                const pairsModal = pairsModalEl ? new bootstrap.Modal(pairsModalEl) : null;

                // Click handler: render pairs
                $(document).on('click', '.btn-view-pairs', function() {
                    const offerName = $(this).data('offer') || '';
                    const referArr = $(this).data('refer') || [];
                    const benefitArr = $(this).data('benefit') || [];

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
    @endsection
