@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS (kept if you add filters later) -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .page-title {
            font-weight: 700
        }

        .toolbar {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center
        }

        .search-wrap {
            max-width: 320px
        }

        .thumb {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e5e7eb
        }

        .cell-muted {
            color: #64748b;
            font-size: .85rem
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1
        }

        .badge-phone {
            background: #eef2ff;
            color: #3730a3;
            font-weight: 600
        }

        .rider-name a {
            text-decoration: none
        }

        .actions .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: .5rem
        }

        /* compact rows on md- screens */
        @media (max-width: 992px) {
            table.dataTable tbody td {
                padding-top: .5rem;
                padding-bottom: .5rem
            }
        }
    </style>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if (session('success'))
        <div id="Message" class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('danger'))
        <div id="Message" class="alert alert-danger">{{ session('danger') }}</div>
    @endif

    <!-- Header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="page-title">Riders</span>
            <p class="mb-0 text-muted">Manage rider profiles and quick actions.</p>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riders</li>
            </ol>
        </div>
    </div>

    <!-- Table Card -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">

                    <!-- Toolbar -->
                    <div class="toolbar mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ url('admin/add-rider-details') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add Rider
                            </a>
                        </div>
                        <div class="search-wrap">
                            <input id="tableSearch" type="search" class="form-control" placeholder="Search riders...">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive export-table">
                        <table id="riders-table" class="table table-bordered table-hover align-middle w-100">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Rider</th>
                                    <th>Phone</th>
                                    <th>Photo</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rider_details as $rider)
                                    <tr>
                                        <td class="text-nowrap">{{ $loop->iteration }}</td>
                                        <td class="rider-name">
                                            <div class="fw-semibold">
                                                <a class="text-primary"
                                                    href="{{ route('admin.riderAllDetails', $rider->id) }}">
                                                    {{ $rider->rider_name }}
                                                </a>
                                            </div>
                                            <div class="cell-muted">ID: #{{ $rider->id }}</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-phone">+91 {{ $rider->phone_number }}</span>
                                        </td>
                                        <td>
                                            @if ($rider->rider_img)
                                                <a href="{{ Storage::url($rider->rider_img) }}" target="_blank"
                                                    rel="noopener" title="Open full image">
                                                    <img class="thumb" src="{{ Storage::url($rider->rider_img) }}"
                                                        alt="{{ $rider->rider_name }}">
                                                </a>
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td class="text-wrap" style="max-width:360px">{{ $rider->description }}</td>
                                        <td class="actions">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/edit-rider-details/' . $rider->id) }}">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger btn-delete" href="#"
                                                            data-id="{{ $rider->id }}"
                                                            data-url="{{ route('admin.deleteRiderDetails', $rider->id) }}">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Rider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this rider? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables -->
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

    <!-- Select2 (reserved for future filters) -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        // Hide flash after 3s
        setTimeout(function() {
            const msg = document.getElementById('Message');
            if (msg) msg.style.display = 'none';
        }, 3000);

        // DataTable init (use custom id to avoid conflicts with generic initializers)
        const table = new DataTable('#riders-table', {
            responsive: true,
            stateSave: true,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            order: [
                [0, 'asc']
            ],
            dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
            buttons: [{
                    extend: 'copyHtml5',
                    title: 'Riders'
                },
                {
                    extend: 'csvHtml5',
                    title: 'riders'
                },
                {
                    extend: 'excelHtml5',
                    title: 'riders'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Riders',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    title: 'Riders'
                },
                {
                    extend: 'colvis',
                    text: 'Columns'
                }
            ],
            columnDefs: [{
                    targets: [4],
                    width: '35%'
                },
                {
                    targets: [5],
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // External search box hook
        const searchInput = document.getElementById('tableSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                table.search(this.value).draw();
            });
        }

        // Delete modal logic
        let deleteUrl = '#';
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                deleteUrl = this.dataset.url;
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            });
        });
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            window.location.href = deleteUrl;
        });
    </script>
@endsection
