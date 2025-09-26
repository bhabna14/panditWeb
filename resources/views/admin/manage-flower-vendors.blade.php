@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 (if used elsewhere) -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* ===== Stats cards ===== */
        .metric-card {
            background: #fff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            transition: .2s;
            cursor: pointer
        }

        .metric-card:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .06);
            transform: translateY(-2px)
        }

        .metric-card .icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem
        }

        .metric-card .label {
            color: #6c757d;
            font-weight: 600;
            margin: 0
        }

        .metric-card .value {
            font-size: 1.6rem;
            margin: 0
        }

        .icon-total {
            background: #e7f1ff;
            color: #0d6efd
        }

        .icon-active {
            background: #eaf7ef;
            color: #198754
        }

        .icon-inactive {
            background: #fff3cd;
            color: #b7791f
        }

        .metric-card.active {
            outline: 2px solid #0d6efd22
        }

        /* ===== View toggles / filters ===== */
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin: .5rem 0 1rem
        }

        .filter-pills .btn {
            border-radius: 999px
        }

        .filter-pills .btn.active {
            color: #fff;
            background: #0d6efd;
            border-color: #0d6efd
        }

        .view-toggle .btn {
            border: 1px solid #e7ebf0
        }

        .view-toggle .btn.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd
        }

        /* ===== Card grid ===== */
        #cardsGrid {
            display: none
        }

        .vendor-card {
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            padding: 14px;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .vendor-card .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 10px
        }

        .vendor-card .name {
            font-weight: 700;
            margin: 0
        }

        .vendor-card .badge {
            font-size: .75rem
        }

        .muted {
            color: #6c757d
        }

        .kv {
            font-size: .9rem
        }

        .kv strong {
            color: #212529
        }

        .card-actions {
            margin-top: auto;
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        /* ===== Modal doc viewer ===== */
        #docContent {
            min-height: 60vh
        }

        /* keep table compact */
        table.dataTable tbody td {
            vertical-align: middle
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== Stats row ===== --}}
    <div class="row g-3 mb-3 mt-4">
        <div class="col-md-4">
            <div class="metric-card p-3" data-filter="all" id="cardAll" style="border: 1px solid rgb(186, 185, 185);">
                <div class="d-flex align-items-center gap-3">
                    <span class="icon icon-total"><i class="fa fa-users"></i></span>
                    <div>
                        <p class="label">Total Vendors</p>
                        <p class="value" id="totalVendorsCount">{{ $totalVendors ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card p-3" data-filter="active" id="cardActive" style="border: 1px solid rgb(186, 185, 185);">
                <div class="d-flex align-items-center gap-3">
                    <span class="icon icon-active"><i class="fa fa-user-check"></i></span>
                    <div>
                        <p class="label">Active Vendors</p>
                        <p class="value" id="activeVendorsCount">{{ $activeVendors ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card p-3" data-filter="inactive" id="cardInactive"
                style="border: 1px solid rgb(186, 185, 185);">
                <div class="d-flex align-items-center gap-3">
                    <span class="icon icon-inactive"><i class="fa fa-user-times"></i></span>
                    <div>
                        <p class="label">Inactive Vendors</p>
                        <p class="value" id="inactiveVendorsCount">{{ $inactiveVendors ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Toolbar: filter pills + view toggle ===== --}}
    <div class="toolbar">
        <div class="btn-group filter-pills" role="group" aria-label="Filter">
            <button class="btn btn-outline-secondary active" data-filter="all" id="pillAll">All</button>
            <button class="btn btn-outline-success" data-filter="active" id="pillActive">Active</button>
            <button class="btn btn-outline-warning" data-filter="inactive" id="pillInactive">Inactive</button>
        </div>
        <div class="btn-group view-toggle" role="group" aria-label="View">
            <button class="btn btn-light active" id="btnTable"><i class="fa fa-table me-1"></i> Table</button>
            <button class="btn btn-light" id="btnCards"><i class="fa fa-th-large me-1"></i> Cards</button>
        </div>
    </div>

    {{-- ===== Table view ===== --}}
    <div class="card custom-card overflow-hidden" id="tableWrap">
        <div class="card-body">
            <div class="table-responsive export-table">
                <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom w-100">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Vendor Name</th>
                            <th>Phone</th>
                            <th>Category</th>
                            <th>Banks</th>
                            <th>Flowers</th>
                            <th>Joined On</th>
                            <th>Document</th>
                            <th>GST</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendor_details as $index => $vendor)
                            @php
                                $status = strtolower($vendor->status ?? 'deleted');
                                $banks = ($vendor->vendorBanks ?? collect())
                                    ->map(function ($b) {
                                        return [
                                            'bank_name' => $b->bank_name,
                                            'account_no' => $b->account_no,
                                            'ifsc_code' => $b->ifsc_code,
                                            'upi_id' => $b->upi_id,
                                        ];
                                    })
                                    ->values();
                                $flowerIds = $vendor->flower_ids;
                                if (is_string($flowerIds)) {
                                    $flowerIds = json_decode($flowerIds, true) ?? [];
                                }
                                if (!is_array($flowerIds)) {
                                    $flowerIds = [];
                                }
                                $docUrl = $vendor->vendor_document
                                    ? asset('storage/' . $vendor->vendor_document)
                                    : null;
                            @endphp
                            <tr data-status="{{ $status }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a class="text-primary fw-bold"
                                        href="{{ route('admin.vendorAllDetails', $vendor->vendor_id) }}">
                                        {{ $vendor->vendor_name }}
                                    </a>
                                </td>
                                <td>{{ $vendor->phone_no }}</td>
                                <td>{{ $vendor->vendor_category }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#vendorBankModal" data-banks='@json($banks)'
                                        data-vendor="{{ $vendor->vendor_name }}">
                                        View Banks
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#flowerModal" data-flower-ids='@json($flowerIds)'
                                        data-vendor="{{ $vendor->vendor_name }}">
                                        View Flowers
                                    </button>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($vendor->date_of_joining)->format('d-m-Y') }}</td>
                                <td>
                                    @if ($docUrl)
                                        <button type="button" class="btn btn-outline-primary btn-sm view-doc-btn"
                                            data-bs-toggle="modal" data-bs-target="#vendorDocModal"
                                            data-doc-url="{{ $docUrl }}"
                                            data-doc-name="Document — {{ $vendor->vendor_name }}">
                                            View
                                        </button>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $vendor->vendor_gst ?: '—' }}</td>
                                <td>{{ $vendor->vendor_address ?: '—' }}</td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.editVendorDetails', $vendor->vendor_id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.deletevendor', $vendor->vendor_id) }}"
                                            method="POST" onsubmit="return confirm('Delete this vendor?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Cards view ===== --}}
    <div class="row g-3" id="cardsGrid">
        @foreach ($vendor_details as $vendor)
            @php
                $status = strtolower($vendor->status ?? 'deleted');
                $banks = ($vendor->vendorBanks ?? collect())
                    ->map(function ($b) {
                        return [
                            'bank_name' => $b->bank_name,
                            'account_no' => $b->account_no,
                            'ifsc_code' => $b->ifsc_code,
                            'upi_id' => $b->upi_id,
                        ];
                    })
                    ->values();
                $flowerIds = $vendor->flower_ids;
                if (is_string($flowerIds)) {
                    $flowerIds = json_decode($flowerIds, true) ?? [];
                }
                if (!is_array($flowerIds)) {
                    $flowerIds = [];
                }
                $docUrl = $vendor->vendor_document ? asset('storage/' . $vendor->vendor_document) : null;
            @endphp
            <div class="col-xl-3 col-lg-4 col-md-6" data-status="{{ $status }}">
                <div class="vendor-card h-100">
                    <div class="header">
                        <div>
                            <p class="name mb-1">{{ $vendor->vendor_name }}</p>
                            <span class="badge {{ $status === 'active' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                        <a href="{{ route('admin.vendorAllDetails', $vendor->vendor_id) }}"
                            class="btn btn-sm btn-outline-primary">
                            Details
                        </a>
                    </div>
                    <div class="kv"><strong>Phone:</strong> <span class="muted">{{ $vendor->phone_no }}</span></div>
                    <div class="kv"><strong>Email:</strong> <span
                            class="muted">{{ $vendor->email_id ?: '—' }}</span></div>
                    <div class="kv"><strong>Category:</strong> <span
                            class="muted">{{ $vendor->vendor_category }}</span></div>
                    <div class="kv"><strong>Joined:</strong> <span
                            class="muted">{{ \Carbon\Carbon::parse($vendor->date_of_joining)->format('d-m-Y') }}</span>
                    </div>

                    <div class="card-actions">
                        @if ($docUrl)
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#vendorDocModal" data-doc-url="{{ $docUrl }}"
                                data-doc-name="Document — {{ $vendor->vendor_name }}">
                                Document
                            </button>
                        @endif

                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#vendorBankModal" data-banks='@json($banks)'
                            data-vendor="{{ $vendor->vendor_name }}">
                            Banks
                        </button>

                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#flowerModal" data-flower-ids='@json($flowerIds)'
                            data-vendor="{{ $vendor->vendor_name }}">
                            Flowers
                        </button>

                        <a href="{{ route('admin.editVendorDetails', $vendor->vendor_id) }}"
                            class="btn btn-primary btn-sm">
                            Edit
                        </a>
                        <form action="{{ route('admin.deletevendor', $vendor->vendor_id) }}" method="POST"
                            onsubmit="return confirm('Delete this vendor?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== Document Modal ===== --}}
    <div class="modal fade" id="vendorDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vendor Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="docContent" class="w-100 d-flex justify-content-center align-items-center">
                        <div class="text-muted">Loading…</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="docDownloadBtn" href="#" class="btn btn-secondary" target="_blank" rel="noopener">Open
                        in new tab</a>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Banks Modal (filled by JS) ===== --}}
    <div class="modal fade" id="vendorBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bank Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Bank Name</th>
                                    <th>Account No</th>
                                    <th>IFSC</th>
                                    <th>UPI</th>
                                </tr>
                            </thead>
                            <tbody id="bank-details-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Flowers Modal (filled by JS) ===== --}}
    <div class="modal fade" id="flowerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Vendor Flower List</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul id="flowerList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    <!-- Select2 (if needed) -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- Bootstrap (if not already on layout) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ====== DataTables init ======
        let vendorTable;

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#file-datatable')) {
                vendorTable = $('#file-datatable').DataTable();
            } else {
                vendorTable = $('#file-datatable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
                });
            }
        }
        initDataTable();

        // ====== Filter helpers ======
        const STATUS_COL_INDEX = 5; // "Status" column index in table header
        function applyFilter(filter) {
            // pills
            document.querySelectorAll('.filter-pills .btn').forEach(b => b.classList.remove('active'));
            document.querySelector(`.filter-pills .btn[data-filter="${filter}"]`)?.classList.add('active');

            // stats cards active border
            document.querySelectorAll('.metric-card').forEach(c => c.classList.remove('active'));
            document.querySelector(`.metric-card[data-filter="${filter}"]`)?.classList.add('active');

            // table filter (use regex exact match on status text)
            if (vendorTable) {
                if (filter === 'active') {
                    vendorTable.column(STATUS_COL_INDEX).search('^Active$', true, false).draw();
                } else if (filter === 'deleted') {
                    vendorTable.column(STATUS_COL_INDEX).search('^Inactive$', true, false).draw();
                } else {
                    vendorTable.column(STATUS_COL_INDEX).search('').draw();
                }
            }

            // cards filter
            document.querySelectorAll('#cardsGrid [data-status]').forEach(card => {
                const st = card.getAttribute('data-status') || 'deleted';
                card.style.display = (filter === 'all' || filter === st) ? '' : 'none';
            });
        }

        // ====== View toggle ======
        const btnTable = document.getElementById('btnTable');
        const btnCards = document.getElementById('btnCards');
        const tableWrap = document.getElementById('tableWrap');
        const cardsGrid = document.getElementById('cardsGrid');

        function setView(view) {
            btnTable.classList.toggle('active', view === 'table');
            btnCards.classList.toggle('active', view === 'cards');
            tableWrap.style.display = (view === 'table') ? '' : 'none';
            cardsGrid.style.display = (view === 'cards') ? 'flex' : 'none';
        }

        btnTable.addEventListener('click', () => setView('table'));
        btnCards.addEventListener('click', () => setView('cards'));

        // default
        setView('table');
        applyFilter('all');

        // ====== Wire up stats cards + pills ======
        document.querySelectorAll('.metric-card').forEach(card => {
            card.addEventListener('click', () => {
                applyFilter(card.getAttribute('data-filter'));
            });
        });
        document.querySelectorAll('.filter-pills .btn').forEach(btn => {
            btn.addEventListener('click', () => {
                applyFilter(btn.getAttribute('data-filter'));
            });
        });

        // ====== Document Modal preview ======
        (function() {
            const modalEl = document.getElementById('vendorDocModal');

            modalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-doc-url');
                const name = button.getAttribute('data-doc-name') || 'Vendor Document';

                const clean = (url || '').split('?')[0];
                const ext = (clean.split('.').pop() || '').toLowerCase();

                const contentEl = modalEl.querySelector('#docContent');
                const downloadEl = modalEl.querySelector('#docDownloadBtn');
                modalEl.querySelector('.modal-title').textContent = name;
                downloadEl.href = url;
                contentEl.innerHTML = '';

                if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) {
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = name;
                    img.className = 'img-fluid rounded shadow-sm';
                    contentEl.appendChild(img);
                } else if (ext === 'pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = url + '#zoom=page-width';
                    iframe.width = '100%';
                    iframe.height = '600';
                    iframe.style.border = '0';
                    contentEl.appendChild(iframe);
                } else {
                    contentEl.innerHTML = '<p class="text-muted">Preview not available. ' +
                        '<a href="' + url + '" target="_blank" rel="noopener">Open in new tab</a>.</p>';
                }
            });

            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.querySelector('#docContent').innerHTML = '';
            });
        })();

        // ====== Banks Modal fill ======
        (function() {
            const modalEl = document.getElementById('vendorBankModal');
            const tbody = document.getElementById('bank-details-body');

            modalEl.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const banksJson = btn.getAttribute('data-banks') || '[]';
                const vendorName = btn.getAttribute('data-vendor') || 'Vendor';
                let banks = [];
                try {
                    banks = JSON.parse(banksJson);
                } catch (e) {
                    banks = [];
                }

                modalEl.querySelector('.modal-title').textContent = `Bank Details — ${vendorName}`;

                tbody.innerHTML = '';
                if (!banks.length) {
                    tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center text-muted">No bank details</td></tr>';
                } else {
                    banks.forEach(b => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${b.bank_name ?? '—'}</td>
                            <td>${b.account_no ?? '—'}</td>
                            <td>${b.ifsc_code ?? '—'}</td>
                            <td>${b.upi_id ?? '—'}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
        })();

        // ====== Flowers Modal fill ======
        (function() {
            const allFlowers = @json($flowers);
            const modalEl = document.getElementById('flowerModal');
            const listEl = document.getElementById('flowerList');

            function findFlowerById(pid) {
                return (allFlowers || []).find(f => String(f.product_id) === String(pid));
            }

            modalEl.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const idsJson = btn.getAttribute('data-flower-ids') || '[]';
                const vendorName = btn.getAttribute('data-vendor') || 'Vendor';
                let ids = [];
                try {
                    ids = JSON.parse(idsJson) || [];
                } catch (e) {
                    ids = [];
                }

                modalEl.querySelector('.modal-title').textContent = `Vendor Flower List — ${vendorName}`;
                listEl.innerHTML = '';

                if (!ids.length) {
                    listEl.innerHTML = '<li class="list-group-item text-muted">No flowers assigned.</li>';
                } else {
                    ids.forEach(id => {
                        const f = findFlowerById(id);
                        const name = f ? (f.name + (f.odia_name ? ` (${f.odia_name})` : '')) : `#${id}`;
                        const li = document.createElement('li');
                        li.className = 'list-group-item';
                        li.textContent = name;
                        listEl.appendChild(li);
                    });
                }
            });
        })();
    </script>
@endsection
