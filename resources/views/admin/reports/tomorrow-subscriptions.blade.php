@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --brand-bg: #0f172a;
            /* slate-900 */
            --brand-accent: #2563eb;
            /* blue-600 */
            --brand-soft: #e7f0ff;
            /* blue-50-ish */
            --card-border: #e5e7eb;
        }

        .page-header {
            background: linear-gradient(180deg, var(--brand-bg), #111827);
            color: #fff;
            border-radius: 1rem;
            padding: 1.25rem 1.25rem;
        }

        .page-subtitle {
            opacity: .85
        }

        .kpi .card {
            border: 1px solid var(--card-border)
        }

        .kpi .value {
            font-size: 1.625rem;
            font-weight: 700;
            letter-spacing: .2px
        }

        .filter-card {
            border: 1px solid var(--card-border);
            border-radius: .75rem
        }

        .filter-card .form-label {
            font-weight: 600
        }

        .tabs-wrap .nav-link {
            border-radius: 999px;
            padding: .4rem .9rem
        }

        .tabs-wrap .nav-link.active {
            background: var(--brand-accent);
            color: #fff !important;
            box-shadow: 0 6px 18px rgba(37, 99, 235, .25)
        }

        .table-tight td,
        .table-tight th {
            padding: .55rem .65rem
        }

        .badge-soft {
            background: var(--brand-soft);
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
        }

        .row-tools {
            gap: .5rem
        }

        .btn-ghost {
            background: #fff;
            border: 1px solid var(--card-border)
        }

        .pill-count {
            border-radius: 999px;
            background: #f1f5f9;
            color: #0f172a;
            padding: .2rem .55rem;
            font-weight: 600;
            font-size: .825rem
        }

        .address-col {
            min-width: 280px
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="page-header mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-end">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-week fs-4"></i>
                        <h4 class="m-0">Tomorrow’s Subscriptions</h4>
                    </div>
                    <div class="page-subtitle mt-1">
                        Today: <strong>{{ \Carbon\Carbon::parse($today)->toFormattedDateString() }}</strong>
                        &nbsp;•&nbsp;
                        Tomorrow: <strong>{{ \Carbon\Carbon::parse($tomorrow)->toFormattedDateString() }}</strong>
                    </div>
                </div>
                <div class="row-tools d-flex">
                    <a href="{{ url()->current() }}" class="btn btn-light btn-ghost">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </a>
                </div>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="row g-3 kpi mb-3">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted">Active tomorrow</div>
                        <div class="value">{{ count($activeTomorrow) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted">Starting tomorrow</div>
                        <div class="value">{{ count($startingTomorrow) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted">Pausing from tomorrow</div>
                        <div class="value">{{ count($pausingTomorrow) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted">Ending today</div>
                        <div class="value">{{ count($endingToday) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted">Ending tomorrow</div>
                        <div class="value">{{ count($endingTomorrow) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS + TABS --}}
        <div class="card filter-card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Filter by Name</label>
                        <input type="text" id="fName" class="form-control" placeholder="e.g. John">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Filter by Mobile</label>
                        <input type="text" id="fMobile" class="form-control" placeholder="e.g. 98xxxxx123">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Filter by Apartment</label>
                        <input type="text" id="fApartment" class="form-control" placeholder="e.g. Sunrise Towers">
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" id="btnApplyFilters"><i class="bi bi-funnel"></i>
                            Apply</button>
                        <button class="btn btn-outline-secondary" id="btnClearFilters">Clear</button>
                    </div>
                </div>

                <div class="mt-3 tabs-wrap">
                    <ul class="nav nav-pills flex-wrap" id="sectionsTabs" role="tablist">
                        @php
                            $sections = [
                                ['key' => 'active', 'title' => 'Active Tomorrow', 'count' => count($activeTomorrow)],
                                ['key' => 'start', 'title' => 'Starting Tomorrow', 'count' => count($startingTomorrow)],
                                [
                                    'key' => 'pause',
                                    'title' => 'Pausing from Tomorrow',
                                    'count' => count($pausingTomorrow),
                                ],
                                ['key' => 'end_today', 'title' => 'Ending Today', 'count' => count($endingToday)],
                                ['key' => 'end_tom', 'title' => 'Ending Tomorrow', 'count' => count($endingTomorrow)],
                            ];
                        @endphp
                        @foreach ($sections as $i => $s)
                            <li class="nav-item me-2 mb-2" role="presentation">
                                <button class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-{{ $s['key'] }}"
                                    data-bs-toggle="tab" data-bs-target="#pane-{{ $s['key'] }}" type="button"
                                    role="tab" aria-controls="pane-{{ $s['key'] }}"
                                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                                    {{ $s['title'] }}
                                    <span class="pill-count ms-1">{{ $s['count'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- TAB PANES --}}
        <div class="tab-content" id="sectionsContent">

            {{-- Helper to render a table --}}
            @php
                function renderTable($rows)
                {
                    if (empty($rows)) {
                        echo '<div class="alert alert-secondary mb-0">No subscriptions found.</div>';
                        return;
                    }
                    echo '<div class="table-responsive"><table class="table table-sm table-tight align-middle">';
                    echo '<thead class="table-light">';
                    echo '<tr><th>Customer</th><th>Order</th><th>Product</th><th>Status</th><th>Start</th><th>End</th><th>Pause</th><th class="address-col">Address</th></tr>';
                    echo '</thead><tbody>';
                    foreach ($rows as $r) {
                        $pause =
                            $r['pause_start'] || $r['pause_end']
                                ? ($r['pause_start'] ?? '—') . ' → ' . ($r['pause_end'] ?? '—')
                                : '—';

                        // Prepare a friendly badge for status
                        $status = strtolower($r['status'] ?? '');
                        $badgeClass = 'badge-soft';
                        if (in_array($status, ['active'])) {
                            $badgeClass = 'bg-success-subtle text-success';
                        }
                        if (in_array($status, ['paused'])) {
                            $badgeClass = 'bg-warning-subtle text-warning';
                        }
                        if (in_array($status, ['pending'])) {
                            $badgeClass = 'bg-info-subtle text-info';
                        }
                        if (in_array($status, ['expired', 'ended'])) {
                            $badgeClass = 'bg-danger-subtle text-danger';
                        }

                        // Try to pass apartment name if controller provided it, else empty
                        $apt = $r['apartment_name'] ?? '';

                        echo '<tr class="row-item" ' .
                            ' data-name="' .
                            e(strtolower($r['customer'] ?? '')) .
                            '"' .
                            ' data-mobile="' .
                            e(strtolower($r['phone'] ?? '')) .
                            '"' .
                            ' data-apt="' .
                            e(strtolower($apt)) .
                            '">';

                        echo '<td><div class="fw-semibold">' . e($r['customer']) . '</div>';
                        if ($r['phone'] || $r['email']) {
                            echo '<div class="text-muted small">' .
                                e($r['phone'] ?? '') .
                                ($r['phone'] && $r['email'] ? ' • ' : '') .
                                e($r['email'] ?? '') .
                                '</div>';
                        }
                        echo '</td>';

                        echo '<td>#' . e($r['order_id']) . '</td>';
                        echo '<td>' . e($r['product']) . '</td>';
                        echo '<td><span class="badge ' . $badgeClass . '">' . e($r['status']) . '</span></td>';
                        echo '<td>' . e($r['start_date'] ?? '—') . '</td>';
                        echo '<td>' . e($r['new_date'] ?? ($r['end_date'] ?? '—')) . '</td>';
                        echo '<td>' . e($pause) . '</td>';

                        $addrSafe = e($r['address']);
                        $addrId = 'addr-' . e($r['subscription_id']);
                        echo '<td>';
                        echo '<button type="button" class="btn btn-sm btn-outline-primary view-address" data-address="' .
                            $addrSafe .
                            '" data-bs-toggle="modal" data-bs-target="#addressModal">';
                        echo '<i class="bi bi-geo-alt"></i> View';
                        echo '</button>';
                        echo '</td>';

                        echo '</tr>';
                    }
                    echo '</tbody></table></div>';
                }
            @endphp

            {{-- Active --}}
            <div class="tab-pane fade show active" id="pane-active" role="tabpanel" aria-labelledby="tab-active">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Active Tomorrow</strong>
                        <span class="pill-count">{{ count($activeTomorrow) }}</span>
                    </div>
                    <div class="card-body">
                        {!! renderTable($activeTomorrow) !!}
                    </div>
                </div>
            </div>

            {{-- Starting --}}
            <div class="tab-pane fade" id="pane-start" role="tabpanel" aria-labelledby="tab-start">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Starting Tomorrow</strong>
                        <span class="pill-count">{{ count($startingTomorrow) }}</span>
                    </div>
                    <div class="card-body">
                        {!! renderTable($startingTomorrow) !!}
                    </div>
                </div>
            </div>

            {{-- Pausing --}}
            <div class="tab-pane fade" id="pane-pause" role="tabpanel" aria-labelledby="tab-pause">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Pausing from Tomorrow</strong>
                        <span class="pill-count">{{ count($pausingTomorrow) }}</span>
                    </div>
                    <div class="card-body">
                        {!! renderTable($pausingTomorrow) !!}
                    </div>
                </div>
            </div>

            {{-- Ending Today --}}
            <div class="tab-pane fade" id="pane-end_today" role="tabpanel" aria-labelledby="tab-end_today">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Ending Today</strong>
                        <span class="pill-count">{{ count($endingToday) }}</span>
                    </div>
                    <div class="card-body">
                        {!! renderTable($endingToday) !!}
                    </div>
                </div>
            </div>

            {{-- Ending Tomorrow --}}
            <div class="tab-pane fade" id="pane-end_tom" role="tabpanel" aria-labelledby="tab-end_tom">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Ending Tomorrow</strong>
                        <span class="pill-count">{{ count($endingTomorrow) }}</span>
                    </div>
                    <div class="card-body">
                        {!! renderTable($endingTomorrow) !!}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ADDRESS MODAL --}}
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addressModalLabel"><i class="bi bi-geo-alt"></i> Delivery Address</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="mb-0" id="addressModalBody"
                        style="white-space:pre-wrap;font-family:system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif;"></pre>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="copyAddressBtn"><i class="bi bi-clipboard"></i> Copy</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const fName = document.getElementById('fName');
            const fMobile = document.getElementById('fMobile');
            const fApartment = document.getElementById('fApartment');
            const btnApply = document.getElementById('btnApplyFilters');
            const btnClear = document.getElementById('btnClearFilters');

            function activePane() {
                return document.querySelector('#sectionsContent .tab-pane.active.show') || document.querySelector(
                    '#sectionsContent .tab-pane.active') || document.querySelector(
                    '#sectionsContent .tab-pane.show');
            }

            function normalize(v) {
                return (v || '').toString().trim().toLowerCase();
            }

            function applyFilters() {
                const pane = activePane();
                if (!pane) return;
                const nameVal = normalize(fName.value);
                const mobileVal = normalize(fMobile.value);
                const aptVal = normalize(fApartment.value);

                pane.querySelectorAll('tbody .row-item').forEach(tr => {
                    const dn = tr.getAttribute('data-name') || '';
                    const dm = tr.getAttribute('data-mobile') || '';
                    const da = tr.getAttribute('data-apt') || '';

                    const passName = !nameVal || dn.includes(nameVal);
                    const passMobile = !mobileVal || dm.includes(mobileVal);
                    const passApt = !aptVal || da.includes(aptVal);

                    tr.style.display = (passName && passMobile && passApt) ? '' : 'none';
                });
            }

            function clearFilters() {
                fName.value = '';
                fMobile.value = '';
                fApartment.value = '';
                applyFilters();
            }

            btnApply.addEventListener('click', function(e) {
                e.preventDefault();
                applyFilters();
            });
            btnClear.addEventListener('click', function(e) {
                e.preventDefault();
                clearFilters();
            });

            // Re-apply when switching tabs
            document.getElementById('sectionsTabs').addEventListener('shown.bs.tab', applyFilters);

            // Address modal
            const addressModal = document.getElementById('addressModal');
            const addressBody = document.getElementById('addressModalBody');
            const copyBtn = document.getElementById('copyAddressBtn');

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.view-address');
                if (!btn) return;
                const addr = btn.getAttribute('data-address') || '—';
                addressBody.textContent = addr;
            });

            copyBtn.addEventListener('click', async function() {
                try {
                    await navigator.clipboard.writeText(addressBody.textContent || '');
                    copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copied';
                    setTimeout(() => copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Copy', 1200);
                } catch (e) {}
            });

            // Initial filter (no-op) to ensure consistency
            applyFilters();
        })();
    </script>
@endsection
