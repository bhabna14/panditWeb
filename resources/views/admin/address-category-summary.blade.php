@extends('admin.layouts.apps')

@section('styles')
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    {{-- DataTables (Bootstrap 5 build) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.7/datatables.min.css"/>

    <style>
        .tile {
            border: 1px solid #eef0f6;
            border-radius: 16px;
            padding: 18px;
            background: linear-gradient(135deg, #f7f9ff 0%, #f2fffb 100%);
            box-shadow: 0 10px 24px rgba(25,42,70,.06);
            cursor: pointer;
            transition: .25s ease;
            height: 100%;
        }
        .tile:hover { transform: translateY(-3px); }
        .tile .icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: grid; place-items: center;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,.06);
        }
        .pill {
            display:inline-block; padding:.25rem .6rem; border-radius:999px;
            background:#fff; border:1px solid #e9ecf5; font-size:.8rem;
        }
        .shadow-soft { box-shadow: 0 8px 20px rgba(0,0,0,.05); }
        .apartment-row { cursor: pointer; }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row g-3 align-items-center mb-3">
        <div class="col">
            <h4 class="mb-0">Address Categories</h4>
            <div class="text-muted">Explore customers by category, drill down by apartment, and edit quickly.</div>
        </div>
        <div class="col-auto">
            <span class="pill">Updated • {{ now()->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    {{-- Tiles --}}
    @php
        $cards = [
            'apartment'  => ['icon'=>'bi-building',   'label'=>'Apartments'],
            'individual' => ['icon'=>'bi-house-door', 'label'=>'Individuals'],
            'temple'     => ['icon'=>'bi-bank',       'label'=>'Temples'],
            'business'   => ['icon'=>'bi-briefcase',  'label'=>'Businesses'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($cards as $key => $meta)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="tile card-click" data-category="{{ $key }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon"><i class="bi {{ $meta['icon'] }} fs-4"></i></div>
                        <div>
                            <div class="fw-semibold">{{ $meta['label'] }}</div>
                            <div class="fs-3 fw-bold">{{ $addressCounts[$key] ?? 0 }}</div>
                            <div class="text-muted small">Tap to view</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="card shadow-soft">
        <div class="card-body">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1">Category</label>
                    <select id="filterCategory" class="form-select">
                        <option value="">Choose…</option>
                        <option value="apartment">Apartment</option>
                        <option value="individual">Individual</option>
                        <option value="temple">Temple</option>
                        <option value="business">Business</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1">Search</label>
                    <input id="filterSearch" type="text" class="form-control" placeholder="Filter apartments here…">
                </div>
                <div class="col-12 col-md-4">
                    <button id="applyFilters" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Apply</button>
                </div>
            </div>

            {{-- Grouped table (apartment => count) --}}
            <div class="table-responsive" id="groupedContainer">
                <div class="py-4 text-center text-muted">Select a category above to load data.</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.7/datatables.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tiles = document.querySelectorAll('.card-click');
        const filterCategory = document.getElementById('filterCategory');
        const filterSearch   = document.getElementById('filterSearch');
        const applyBtn       = document.getElementById('applyFilters');
        const container      = document.getElementById('groupedContainer');

        // Tile click => set category + load
        tiles.forEach(tile => {
            tile.addEventListener('click', () => {
                filterCategory.value = tile.dataset.category;
                loadGrouped();
            });
        });

        applyBtn.addEventListener('click', loadGrouped);

        // Load grouped data
        function loadGrouped() {
            const cat = filterCategory.value;
            if (!cat) {
                container.innerHTML = '<div class="py-4 text-center text-muted">Please choose a category.</div>';
                return;
            }
            container.innerHTML = '<div class="py-4 text-center text-muted">Loading…</div>';

            const params = new URLSearchParams({ category: cat });
            fetch(`{{ route('admin.address.category.users') }}?` + params.toString())
                .then(r => r.json())
                .then(data => {
                    const q = (filterSearch.value || '').toLowerCase().trim();

                    // Filter by search (apartment name)
                    const filtered = Object.fromEntries(
                        Object.entries(data).filter(([apt]) => !q || (apt || '').toLowerCase().includes(q))
                    );

                    const hasRows = Object.keys(filtered).length > 0;
                    if (!hasRows) {
                        container.innerHTML = '<div class="py-4 text-center text-muted">No matching apartments.</div>';
                        return;
                    }

                    let html = `
                        <table class="table table-bordered table-hover align-middle" id="apartmentListTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>Apartment</th>
                                    <th>User Count</th>
                                    <th style="width: 120px">Open</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    let idx = 1;
                    Object.entries(filtered).sort().forEach(([apt, users]) => {
                        const safeApt = apt === null ? '—' : apt;
                        const url = `{{ url('/admin/apartment-users') }}/${encodeURIComponent(safeApt)}`;
                        html += `
                            <tr>
                                <td>${idx++}</td>
                                <td>${safeApt}</td>
                                <td>${users.length}</td>
                                <td><a class="btn btn-sm btn-outline-primary" href="${url}">
                                    <i class="bi bi-box-arrow-up-right"></i> View
                                </a></td>
                            </tr>
                        `;
                    });
                    html += `</tbody></table>`;
                    container.innerHTML = html;

                    // Enhance
                    new DataTable('#apartmentListTable', {
                        paging: true,
                        searching: false,
                        order: [[2, 'desc']]
                    });
                })
                .catch(() => {
                    container.innerHTML = '<div class="py-4 text-center text-danger">Failed to load data.</div>';
                });
        }
    });
    </script>
@endsection
