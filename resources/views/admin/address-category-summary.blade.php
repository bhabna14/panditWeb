@extends('admin.layouts.apps')

@section('styles')
    {{-- Required: CSRF for POST/PUT in modals (usually already in your layout) --}}
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

    {{-- Icons + DataTables --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.css" />

    <style>
        /* ===== Page chrome ===== */
        .page-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #6a8dff 0%, #7c4dff 40%, #ff6cab 100%);
            color: #fff;
            padding: 18px 20px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .14);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .6rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .28);
            font-size: .8rem;
            color: #fff;
        }

        /* ===== Stat tiles ===== */
        .tile {
            border: 1px solid #eef0f6;
            border-radius: 16px;
            padding: 16px;
            background: linear-gradient(180deg, #f9fbff 0%, #f7fff9 100%);
            box-shadow: 0 10px 24px rgba(25, 42, 70, .06);
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease;
            height: 100%;
        }

        .tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(25, 42, 70, .08);
        }

        .tile .icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #fff;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .06);
        }

        .tile .count {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .shadow-soft {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .05);
        }

        /* ===== Table polish ===== */
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8fafc;
            border-bottom: 1px solid #e9eef5;
        }

        .table-hover tbody tr:hover {
            background-color: #f7faff;
        }

        .apartment-row {
            cursor: pointer;
        }

        /* ===== Skeleton / empty states ===== */
        .skeleton {
            height: 14px;
            border-radius: 6px;
            background: linear-gradient(90deg, #f2f5fb 0px, #e8edf7 40px, #f2f5fb 80px);
            background-size: 600px;
            animation: shimmer 1.2s infinite linear;
        }

        @keyframes shimmer {
            0% {
                background-position: -600px 0
            }

            100% {
                background-position: 600px 0
            }
        }

        .empty {
            padding: 48px 16px;
            text-align: center;
            color: #94a3b8;
        }

        .empty i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
            color: #a3bffa;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Hero --}}
        <div class="page-hero mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Address Categories</h4>
                    <div class="opacity-90">Explore customers by category, drill down by apartment, and edit quickly.</div>
                </div>
                <span class="pill">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Updated • {{ now()->format('d M Y, h:i A') }}
                </span>
            </div>
        </div>

        {{-- Tiles --}}
        @php
            $cards = [
                'apartment' => ['icon' => 'bi-building', 'label' => 'Apartments', 'accent' => '#4f46e5'],
                'individual' => ['icon' => 'bi-house-door', 'label' => 'Individuals', 'accent' => '#16a34a'],
                'temple' => ['icon' => 'bi-bank', 'label' => 'Temples', 'accent' => '#0ea5e9'],
                'business' => ['icon' => 'bi-briefcase', 'label' => 'Businesses', 'accent' => '#f59e0b'],
            ];
        @endphp

        <div class="row g-3 mb-4">
            @foreach ($cards as $key => $meta)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="tile card-click" data-category="{{ $key }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon" style="border:1px solid {{ $meta['accent'] }}1a">
                                <i class="bi {{ $meta['icon'] }} fs-4" style="color:{{ $meta['accent'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-semibold">{{ $meta['label'] }}</div>
                                    <span class="badge rounded-pill text-bg-light">Tap to view</span>
                                </div>
                                <div class="count mt-1">{{ $addressCounts[$key] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filters + table --}}
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
                    <div class="col-12 col-md-5">
                        <label class="form-label mb-1">Search apartment</label>
                        <input id="filterSearch" type="text" class="form-control" placeholder="Type apartment name…">
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button id="applyFilters" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Apply
                        </button>
                    </div>
                </div>

                <div id="groupedContainer" class="table-responsive">
                    <div class="empty">
                        <i class="bi bi-arrow-up-right-circle"></i>
                        Select a category to load apartments.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tiles = document.querySelectorAll('.card-click');
            const filterCategory = document.getElementById('filterCategory');
            const filterSearch = document.getElementById('filterSearch');
            const applyBtn = document.getElementById('applyFilters');
            const container = document.getElementById('groupedContainer');

            // Stats tile -> set category + load
            tiles.forEach(tile => {
                tile.addEventListener('click', () => {
                    filterCategory.value = tile.dataset.category;
                    loadGrouped();
                });
            });

            applyBtn.addEventListener('click', loadGrouped);

            function renderSkeleton(rows = 6) {
                let html = `
                <table class="table table-borderless">
                    <tbody>`;
                for (let i = 0; i < rows; i++) {
                    html += `
                    <tr>
                        <td style="width:60px"><div class="skeleton" style="width:36px"></div></td>
                        <td><div class="skeleton" style="width:280px"></div></td>
                        <td><div class="skeleton" style="width:120px"></div></td>
                        <td><div class="skeleton" style="width:80px"></div></td>
                    </tr>`;
                }
                html += `</tbody></table>`;
                container.innerHTML = html;
            }

            // Load grouped data (apartment => users[])
            function loadGrouped() {
                const cat = filterCategory.value;
                if (!cat) {
                    container.innerHTML = `<div class="empty">
                    <i class="bi bi-info-circle"></i> Please choose a category.
                </div>`;
                    return;
                }

                renderSkeleton();

                const params = new URLSearchParams({
                    category: cat
                });
                fetch(`{{ route('admin.address.category.users') }}?` + params.toString())
                    .then(r => r.json())
                    .then(data => {
                        const q = (filterSearch.value || '').toLowerCase().trim();

                        const filtered = Object.fromEntries(
                            Object.entries(data).filter(([apt]) => !q || (apt || '').toLowerCase().includes(
                                q))
                        );

                        const hasRows = Object.keys(filtered).length > 0;
                        if (!hasRows) {
                            container.innerHTML = `<div class="empty">
                            <i class="bi bi-search"></i> No matching apartments.
                        </div>`;
                            return;
                        }

                        let html = `
                        <table class="table table-striped table-hover table-bordered align-middle" id="apartmentListTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Apartment</th>
                                    <th style="width:160px">User Count</th>
                                    <th style="width:140px">Open</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                        let idx = 1;
                        Object.entries(filtered)
                            .sort((a, b) => (a[0] ?? '').localeCompare(b[0] ?? ''))
                            .forEach(([apt, users]) => {
                                const safeApt = apt ?? '—';
                                const url =
                                    `{{ url('/admin/apartment-users') }}/${encodeURIComponent(safeApt)}`;
                                html += `
                                <tr class="apartment-row">
                                    <td>${idx++}</td>
                                    <td class="fw-semibold">${safeApt}</td>
                                    <td><span class="fw-semibold">${users.length}</span></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="${url}">
                                            <i class="bi bi-box-arrow-up-right"></i> View
                                        </a>
                                    </td>
                                </tr>`;
                            });
                        html += `</tbody></table>`;
                        container.innerHTML = html;

                        // Enhance
                        const dt = new DataTable('#apartmentListTable', {
                            responsive: true,
                            paging: true,
                            searching: false,
                            order: [
                                [2, 'desc']
                            ],
                            language: {
                                info: "Showing _START_ to _END_ of _TOTAL_ apartments"
                            }
                        });

                        // Row click = open
                        container.querySelectorAll('.apartment-row').forEach(row => {
                            row.addEventListener('click', (e) => {
                                if (e.target.closest('a,button')) return;
                                row.querySelector('a')?.click();
                            });
                        });
                    })
                    .catch(() => {
                        container.innerHTML = `<div class="empty text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Failed to load data.
                    </div>`;
                    });
            }
        });
    </script>
@endsection
