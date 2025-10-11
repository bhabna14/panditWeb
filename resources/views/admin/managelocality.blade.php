@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        :root{
            --line:#e5e7eb; --soft:#f8fafc; --ink:#0f172a; --muted:#64748b; --pri:#0ea5e9;
        }
        .page-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .chip { background:#eef2ff; color:#3730a3; border:1px solid #e2e8f0; border-radius:999px; padding:.35rem .6rem; font-size:.9rem; }
        .pincode { background:#f1f5f9; border:1px solid #e2e8f0; color:#0f172a; border-radius:999px; padding:.2rem .5rem; font-variant-numeric: tabular-nums; }
        .thead-soft th { background:#f8fafc; border-bottom:1px solid var(--line) !important; white-space:nowrap; }
        .card.custom-card { border-radius:14px; }
        .badge-soft { background:#ecfeff; color:#0c4a6e; border:1px solid #cffafe; border-radius:999px; padding:.35rem .6rem; }
        .actions .btn { padding:.25rem .5rem; }
        .table td, .table th { vertical-align: middle; }
        .apartment-list { max-height: 240px; overflow:auto; }
        .dt-buttons .btn { margin-right:.4rem; }
        @media (max-width: 576px){
            .page-head { flex-direction:column; align-items:flex-start; }
        }
    </style>
@endsection

@section('content')

    @if (session('success'))
        <div id="Message" class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    @if (session('danger'))
        <div id="Message" class="alert alert-danger mb-3">{{ session('danger') }}</div>
    @endif

    <!-- Page header -->
    <div class="page-head mb-3">
        <div>
            <h4 class="mb-1">Localities</h4>
            <div class="text-muted">Manage locality list, view apartments, and export data.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="chip">Total: {{ $localities->count() }}</span>
            <span class="chip">
                Active:
                {{ $localities->where('status','active')->count() }}
            </span>
            <span class="chip">
                Inactive:
                {{ $localities->where('status','inactive')->count() }}
            </span>
        </div>
    </div>

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered w-100">
                            <thead class="thead-soft">
                                <tr>
                                    <th>SlNo</th>
                                    <th>Locality</th>
                                    <th>Pincode</th>
                                    <th>Apartments</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($localities as $index => $locality)
                                    @php
                                        $apartments = $locality->apartment ?? collect();
                                        $modalId = 'aptsModal_'.$locality->id;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            <div class="fw-semibold">{{ $locality->locality_name }}</div>
                                            <div class="text-muted small">Code: <code>{{ $locality->unique_code }}</code></div>
                                        </td>

                                        <td>
                                            <span class="pincode">{{ $locality->pincode }}</span>
                                        </td>

                                        <td>
                                            @if ($apartments->isNotEmpty())
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                                    View ({{ $apartments->count() }})
                                                </button>

                                                <!-- Apartments Modal -->
                                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h6 class="modal-title">
                                                                    Apartments — {{ $locality->locality_name }}
                                                                    <span class="text-muted">({{ $locality->unique_code }})</span>
                                                                </h6>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="apartment-list">
                                                                    <ul class="list-group">
                                                                        @foreach ($apartments as $apartment)
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <span>{{ $apartment->apartment_name }}</span>
                                                                                @if(!empty($apartment->apartment_flat_plot))
                                                                                    <span class="badge-soft">{{ $apartment->apartment_flat_plot }}</span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No apartments</span>
                                            @endif
                                        </td>

                                        <td>
                                            @php $isActive = strtolower($locality->status) === 'active'; @endphp
                                            <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($locality->status) }}
                                            </span>
                                        </td>

                                        <td class="text-center actions">
                                            <div class="btn-group" role="group" aria-label="Actions">
                                                <a href="{{ route('editlocality', $locality->id) }}"
                                                   class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('deletelocality', $locality->id) }}" method="POST" onsubmit="return confirm('Delete this locality?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-soft">
                                <tr>
                                    <th>SlNo</th>
                                    <th>Locality</th>
                                    <th>Pincode</th>
                                    <th>Apartments</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- /table -->
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

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        // Hide flash messages after 3s
        setTimeout(function() {
            var msg = document.getElementById('Message');
            if (msg) msg.style.display = 'none';
        }, 3000);

        // Init DataTable with buttons & responsive
        $(function () {
            const table = $('#file-datatable').DataTable({
                responsive: true,
                lengthChange: true,
                pageLength: 10,
                order: [[1, 'asc']], // sort by Locality
                autoWidth: false,
                dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"Bf>rt<"d-flex align-items-center justify-content-between mt-2"lip>',
                buttons: [
                    { extend: 'csv', className: 'btn btn-outline-secondary btn-sm', title: 'localities' },
                    { extend: 'excel', className: 'btn btn-outline-secondary btn-sm', title: 'localities' },
                    { extend: 'pdf', className: 'btn btn-outline-secondary btn-sm', title: 'localities', orientation: 'landscape', pageSize: 'A4' },
                    { extend: 'print', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'colvis', className: 'btn btn-outline-secondary btn-sm', text: 'Columns' }
                ],
                columnDefs: [
                    { targets: 0, width: '60px' },
                    { targets: 2, width: '120px' },
                    { targets: 5, orderable: false, searchable: false, width: '120px' }
                ]
            });

            // Tooltips for action buttons
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
