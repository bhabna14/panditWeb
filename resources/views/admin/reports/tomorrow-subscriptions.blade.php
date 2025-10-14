@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --brand-bg:#dcf8f9; --brand-accent:#2563eb; --brand-soft:#e7f0ff; --card-border:#e5e7eb; }

        .page-header { background:linear-gradient(180deg, var(--brand-bg), #f1f2f3); color:#090909; border-radius:1rem; padding:1.25rem; }
        .page-subtitle { opacity:.85 }

        .kpi-grid { display:grid; grid-template-columns:repeat(12,1fr); gap:.9rem; }
        @media (max-width: 991.98px){ .kpi-grid{ grid-template-columns:repeat(8,1fr);} }
        @media (max-width: 767.98px){ .kpi-grid{ grid-template-columns:repeat(4,1fr);} }

        .kpi-card{ grid-column:span 4; background:#fff; border:1px solid var(--card-border); border-radius:1rem; overflow:hidden; transition:transform .18s ease, box-shadow .18s ease; position:relative; isolation:isolate; }
        .kpi-card:hover{ transform:translateY(-2px); box-shadow:0 10px 26px rgba(0,0,0,.06); }
        .kpi-card .kpi-accent{ position:absolute; inset:0;
            background: radial-gradient(1200px 200px at 100% -30%, rgba(37,99,235,.08), transparent 60%),
                        radial-gradient(900px 160px at -10% 120%, rgba(14,165,233,.08), transparent 55%); z-index:0; }
        .kpi-body{ position:relative; z-index:1; display:flex; align-items:center; gap:.9rem; padding:1rem; min-height:96px; }
        .kpi-icon{ width:46px; height:46px; border-radius:12px; display:grid; place-items:center; background:linear-gradient(135deg,#eff6ff,#e0f2fe); border:1px solid #e2e8f0; flex:0 0 46px; }
        .kpi-icon i{ font-size:1.25rem; color:#0f172a; }
        .kpi-meta .label{ font-size:.84rem; color:#6b7280; font-weight:600; letter-spacing:.2px; }
        .kpi-meta .value{ font-size:1.8rem; font-weight:800; color:#0f172a; line-height:1.1; }
        .kpi-meta .hint{ font-size:.78rem; color:#64748b; }

        .table-tight td,.table-tight th{ padding:.55rem .65rem }
        .badge-soft{ background:var(--brand-soft); color:#1e3a8a; border:1px solid #bfdbfe; }
        .row-tools{ gap:.5rem }
        .btn-ghost{ background:#fff; border:1px solid var(--card-border) }
        .pill-count{ border-radius:999px; background:#f1f5f9; color:#0f172a; padding:.2rem .55rem; font-weight:600; font-size:.825rem }
        .address-col{ min-width:280px } .rider-col{ min-width:160px }

        /* Totals grid */
        .totals-grid{ display:grid; grid-template-columns:repeat(12,1fr); gap:.9rem; }
        @media (max-width:1399.98px){ .totals-grid{ grid-template-columns:repeat(9,1fr);} }
        @media (max-width:1199.98px){ .totals-grid{ grid-template-columns:repeat(8,1fr);} }
        @media (max-width:991.98px){ .totals-grid{ grid-template-columns:repeat(6,1fr);} }
        @media (max-width:767.98px){ .totals-grid{ grid-template-columns:repeat(4,1fr);} }
        @media (max-width:575.98px){ .totals-grid{ grid-template-columns:repeat(2,1fr);} }

        .total-card{ grid-column:span 4; background:#fff; border:1px solid var(--card-border); border-radius:12px; padding:.9rem; display:flex; flex-direction:column; gap:.35rem; min-height:92px; transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
        .total-card:hover{ transform:translateY(-2px); box-shadow:0 10px 26px rgba(0,0,0,.06); border-color:#dbe3f0; }
        .total-top{ display:flex; justify-content:space-between; gap:.6rem; align-items:flex-start; }
        .total-name{ font-weight:700; color:#0f172a; line-height:1.2; }
        .total-qty{ font-size:1.25rem; font-weight:800; color:#0b1528; }
        .total-unit{ font-size:.85rem; font-weight:700; opacity:.7; margin-left:.25rem; }
        .unit-chip{ display:inline-flex; align-items:center; gap:.35rem; font-size:.75rem; font-weight:600; border-radius:999px; padding:.25rem .55rem; border:1px solid #e5e7eb; background:#f8fafc; color:#0f172a; }
        .unit-chip .dot{ width:8px; height:8px; border-radius:999px; display:inline-block; }
        .chip-weight{ background:#f0f9ff; border-color:#cfe8ff; } .chip-weight .dot{ background:#2563eb; }
        .chip-volume{ background:#f1f5ff; border-color:#dbe2ff; } .chip-volume .dot{ background:#7c3aed; }
        .chip-count{ background:#f0fdf4; border-color:#ccebd6; } .chip-count .dot{ background:#16a34a; }
        .total-actions{ display:flex; gap:.4rem; }
        .btn-icon{ border:1px solid #e5e7eb; background:#fff; border-radius:8px; padding:.35rem .5rem; line-height:1; }
        .btn-icon:hover{ background:#f8fafc; }
        .empty-totals{ border:1px dashed #d1d5db; border-radius:12px; padding:1rem; background:#fafafa; }

        /* Lock screen */
        .lock-wrap { max-width: 720px; margin: 48px auto; }
        .lock-card { border:1px solid var(--card-border); border-radius:16px; }
        .countdown { font-size: 1.35rem; font-weight: 800; }
    </style>
@endsection

@section('content')
    @php
        // $canView is set by controller. If not set (older calls), default to true.
        $canView = $canView ?? true;
        $role    = $role    ?? 'super_admin';
    @endphp

    @if(!$canView)
        {{-- ===== LOCKED VIEW (Admin before 5 PM IST) ===== --}}
        <div class="container py-4">
            <div class="lock-wrap">
                <div class="card lock-card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="bi bi-shield-lock fs-2 text-warning"></i>
                            <h4 class="mb-0">Access locked until 5:00 PM</h4>
                        </div>
                        <p class="text-muted mb-3">
                            Hello <strong>{{ ucfirst($role) }}</strong>! This page becomes available for admins after
                            <strong>5:00 PM IST</strong> each day. Super admins can access it anytime.
                        </p>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <div class="small text-muted">Time remaining</div>
                                <div class="countdown" id="countdownText">--:--:--</div>
                            </div>
                            <div>
                                <button id="unlockBtn" class="btn btn-primary" disabled>
                                    <i class="bi bi-arrow-clockwise"></i> Load Page
                                </button>
                                <button id="checkNowBtn" class="btn btn-outline-secondary">
                                    <i class="bi bi-clock-history"></i> Check again
                                </button>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="small text-muted">
                            Server time (IST): <strong>{{ $nowIst ?? '' }}</strong><br>
                            Unlocks at: <strong>{{ $unlockAt ?? '' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ===== FULL PAGE (Allowed) ===== --}}
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

            {{-- KPIs (5) --}}
            <div class="kpi-grid mb-4">
                <div class="kpi-card">
                    <div class="kpi-accent"></div>
                    <div class="kpi-body">
                        <div class="kpi-icon"><i class="bi bi-truck"></i></div>
                        <div class="kpi-meta">
                            <div class="label">Tomorrow Delivery</div>
                            <div class="value">{{ count($activeTomorrow) }}</div>
                            <div class="hint">Subscriptions scheduled</div>
                        </div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-accent"></div>
                    <div class="kpi-body">
                        <div class="kpi-icon"><i class="bi bi-calendar-plus"></i></div>
                        <div class="kpi-meta">
                            <div class="label">Starting Tomorrow</div>
                            <div class="value">{{ count($startingTomorrow) }}</div>
                            <div class="hint">New subscriptions begin</div>
                        </div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-accent"></div>
                    <div class="kpi-body">
                        <div class="kpi-icon"><i class="bi bi-pause-circle"></i></div>
                        <div class="kpi-meta">
                            <div class="label">Pausing from Tomorrow</div>
                            <div class="value">{{ count($pausingTomorrow) }}</div>
                            <div class="hint">On hold from tomorrow</div>
                        </div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-accent"></div>
                    <div class="kpi-body">
                        <div class="kpi-icon"><i class="bi bi-sliders2"></i></div>
                        <div class="kpi-meta">
                            <div class="label">Customize Orders (Tomorrow)</div>
                            <div class="value">{{ count($customizeTomorrow) }}</div>
                            <div class="hint">Requests scheduled</div>
                        </div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-accent"></div>
                    <div class="kpi-body">
                        <div class="kpi-icon"><i class="bi bi-play-circle"></i></div>
                        <div class="kpi-meta">
                            <div class="label">Pause → Active (Tomorrow)</div>
                            <div class="value">{{ count($resumingTomorrow) }}</div>
                            <div class="hint">Resume from tomorrow</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Totals grid (cards) --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>Tomorrow — Totals by Item (All Products)</strong>
                </div>

                <div class="card-body">
                    @php
                        $inferCategory = function($u){
                            $u = strtolower((string)$u);
                            if (in_array($u, ['kg','g'])) return 'weight';
                            if (in_array($u, ['l','ml'])) return 'volume';
                            return 'count';
                        };
                    @endphp

                    @if (empty($tTotals))
                        <div class="empty-totals text-center text-muted">
                            <i class="bi bi-box-seam"></i> No items.
                        </div>
                    @else
                        <div class="totals-grid" id="totalsGrid">
                            @foreach ($tTotals as $it)
                                @php
                                    $unit = strtoupper($it['total_unit_disp'] ?? '');
                                    $category = $inferCategory($unit);
                                    $chipClass = $category === 'weight' ? 'chip-weight' : ($category === 'volume' ? 'chip-volume' : 'chip-count');
                                    $qty = rtrim(rtrim(number_format($it['total_qty_disp'] ?? 0, 3), '0'), '.');
                                @endphp

                                <div class="total-card"
                                     data-name="{{ strtolower($it['item_name']) }}"
                                     data-unit="{{ $category }}"
                                     data-qty="{{ (float)($it['total_qty_disp'] ?? 0) }}">
                                    <div class="total-top">
                                        <div class="total-name">{{ $it['item_name'] }}</div>
                                        <div class="unit-chip {{ $chipClass }}">
                                            <span class="dot"></span>
                                            <span class="text-uppercase">{{ $unit }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline justify-content-between">
                                        <div>
                                            <span class="total-qty">{{ $qty }}</span>
                                            <span class="total-unit">{{ $unit }}</span>
                                        </div>
                                        <div class="total-actions">
                                            <button class="btn btn-sm btn-icon copy-line" title="Copy">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- TABS --}}
            <div class="card shadow-sm mb-3 mt-3">
                <div class="card-body">
                    <div class="tabs-wrap">
                        <ul class="nav nav-pills flex-wrap" id="sectionsTabs" role="tablist">
                            @php
                                $sections = [
                                    ['key' => 'active', 'title' => 'Tomorrow Delivery', 'count' => count($activeTomorrow)],
                                    ['key' => 'start', 'title' => 'Starting Tomorrow', 'count' => count($startingTomorrow)],
                                    ['key' => 'pause','title' => 'Pausing from Tomorrow','count' => count($pausingTomorrow)],
                                    ['key' => 'custom','title' => 'Tomorrow Customize Orders','count' => count($customizeTomorrow)],
                                    ['key' => 'resume','title' => 'Pause → Active (Tomorrow)','count' => count($resumingTomorrow)],
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
                @php
                    function renderSubsTable($rows)
                    {
                        if (empty($rows)) { echo '<div class="alert alert-secondary mb-0">No subscriptions found.</div>'; return; }
                        echo '<div class="table-responsive"><table class="table table-sm table-tight align-middle">';
                        echo '<thead class="table-light">';
                        echo '<tr><th>Customer</th><th>Order</th><th>Product</th><th>Status</th><th>Start</th><th>End</th><th>Pause</th><th class="rider-col">Rider</th><th class="address-col">Address</th></tr>';
                        echo '</thead><tbody>';
                        foreach ($rows as $r) {
                            $pause = $r['pause_start'] || $r['pause_end'] ? ($r['pause_start'] ?? '—').' → '.($r['pause_end'] ?? '—') : '—';
                            $status = strtolower($r['status'] ?? '');
                            $badgeClass = 'badge-soft';
                            if (in_array($status, ['active']))  { $badgeClass = 'bg-success-subtle text-success'; }
                            if (in_array($status, ['paused']))  { $badgeClass = 'bg-warning-subtle text-warning'; }
                            if (in_array($status, ['pending'])) { $badgeClass = 'bg-info-subtle text-info'; }
                            if (in_array($status, ['expired','ended'])) { $badgeClass = 'bg-danger-subtle text-danger'; }
                            $apt = $r['apartment_name'] ?? '';
                            $riderName = $r['rider_name'] ?? '—';

                            echo '<tr class="row-item" '.
                                ' data-name="'.e(strtolower($r['customer'] ?? '')).'"'.
                                ' data-mobile="'.e(strtolower($r['phone'] ?? '')).'"'.
                                ' data-apt="'.e(strtolower($apt)).'"'.
                                ' data-rider="'.e(strtolower($riderName)).'">';

                            echo '<td><div class="fw-semibold">'.e($r['customer']).'</div>';
                            if ($r['phone'] || $r['email']) {
                                echo '<div class="text-muted small">'.e($r['phone'] ?? '').($r['phone'] && $r['email'] ? ' • ' : '').e($r['email'] ?? '').'</div>';
                            }
                            echo '</td>';

                            echo '<td>#'.e($r['order_id']).'</td>';
                            echo '<td>'.e($r['product']).'</td>';
                            echo '<td><span class="badge '.$badgeClass.'">'.e($r['status']).'</span></td>';
                            echo '<td>'.e($r['start_date'] ?? '—').'</td>';
                            echo '<td>'.e($r['new_date'] ?? ($r['end_date'] ?? '—')).'</td>';
                            echo '<td>'.e($pause).'</td>';
                            echo '<td>'.e($riderName).'</td>';

                            $addrSafe = e($r['address']);
                            echo '<td>';
                            echo '<button type="button" class="btn btn-sm btn-outline-primary view-address" data-address="'.$addrSafe.'" data-bs-toggle="modal" data-bs-target="#addressModal">';
                            echo '<i class="bi bi-geo-alt"></i> View</button></td>';

                            echo '</tr>';
                        }
                        echo '</tbody></table></div>';
                    }

                    function renderCustomizeTable($rows)
                    {
                        if (empty($rows)) { echo '<div class="alert alert-secondary mb-0">No customize orders found for tomorrow.</div>'; return; }
                        echo '<div class="table-responsive"><table class="table table-sm table-tight align-middle">';
                        echo '<thead class="table-light">';
                        echo '<tr><th>Customer</th><th>Request</th><th>Product</th><th>Status</th><th>Date</th><th>Time</th><th class="rider-col">Rider</th><th>Items</th><th class="address-col">Address</th></tr>';
                        echo '</thead><tbody>';
                        foreach ($rows as $r) {
                            $apt = $r['apartment_name'] ?? '';
                            $riderName = $r['rider_name'] ?? '—';
                            $reqId = $r['request_id'] ? '#'.$r['request_id'] : '—';
                            $ordId = $r['order_id'] ? '#'.$r['order_id'] : null;

                            $itemsJson = e(json_encode($r['items'] ?? []));

                            echo '<tr class="row-item" '.
                                ' data-name="'.e(strtolower($r['customer'] ?? '')).'"'.
                                ' data-mobile="'.e(strtolower($r['phone'] ?? '')).'"'.
                                ' data-apt="'.e(strtolower($apt)).'"'.
                                ' data-rider="'.e(strtolower($riderName)).'">';

                            echo '<td><div class="fw-semibold">'.e($r['customer']).'</div>';
                            if ($r['phone'] || $r['email']) {
                                echo '<div class="text-muted small">'.e($r['phone'] ?? '').($r['phone'] && $r['email'] ? ' • ' : '').e($r['email'] ?? '').'</div>';
                            }
                            echo '</td>';

                            echo '<td>';
                            echo e($reqId);
                            if ($ordId) echo ' <span class="text-muted small">(&nbsp;Order '.e($ordId).'&nbsp;)</span>';
                            echo '</td>';

                            echo '<td>'.e($r['product'] ?? '—').'</td>';
                            echo '<td><span class="badge bg-info-subtle text-info">'.e($r['status'] ?? '—').'</span></td>';
                            echo '<td>'.e($r['date'] ?? '—').'</td>';
                            echo '<td>'.e($r['time'] ?? '—').'</td>';
                            echo '<td>'.e($riderName).'</td>';

                            echo '<td><button type="button" class="btn btn-sm btn-outline-secondary view-items" data-items="'.$itemsJson.'" data-bs-toggle="modal" data-bs-target="#itemsModal">';
                            echo '<i class="bi bi-list-ul"></i> Items</button></td>';

                            $addrSafe = e($r['address'] ?? '—');
                            echo '<td><button type="button" class="btn btn-sm btn-outline-primary view-address" data-address="'.$addrSafe.'" data-bs-toggle="modal" data-bs-target="#addressModal">';
                            echo '<i class="bi bi-geo-alt"></i> View</button></td>';

                            echo '</tr>';
                        }
                        echo '</tbody></table></div>';
                    }
                @endphp

                {{-- Tab 1: Tomorrow Delivery --}}
                <div class="tab-pane fade show active" id="pane-active" role="tabpanel" aria-labelledby="tab-active">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <strong>Tomorrow Delivery</strong>
                            <span class="pill-count">{{ count($activeTomorrow) }}</span>
                        </div>
                        <div class="card-body">
                            {!! renderSubsTable($activeTomorrow) !!}
                        </div>
                    </div>
                </div>

                {{-- Tab 2: Starting Tomorrow --}}
                <div class="tab-pane fade" id="pane-start" role="tabpanel" aria-labelledby="tab-start">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <strong>Starting Tomorrow</strong>
                            <span class="pill-count">{{ count($startingTomorrow) }}</span>
                        </div>
                        <div class="card-body">
                            {!! renderSubsTable($startingTomorrow) !!}
                        </div>
                    </div>
                </div>

                {{-- Tab 3: Pausing from Tomorrow --}}
                <div class="tab-pane fade" id="pane-pause" role="tabpanel" aria-labelledby="tab-pause">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <strong>Pausing from Tomorrow</strong>
                            <span class="pill-count">{{ count($pausingTomorrow) }}</span>
                        </div>
                        <div class="card-body">
                            {!! renderSubsTable($pausingTomorrow) !!}
                        </div>
                    </div>
                </div>

                {{-- Tab 4: Tomorrow Customize Orders --}}
                <div class="tab-pane fade" id="pane-custom" role="tabpanel" aria-labelledby="tab-custom">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <strong>Tomorrow Customize Orders</strong>
                            <span class="pill-count">{{ count($customizeTomorrow) }}</span>
                        </div>
                        <div class="card-body">
                            {!! renderCustomizeTable($customizeTomorrow) !!}
                        </div>
                    </div>
                </div>

                {{-- Tab 5: Pause → Active (Tomorrow) --}}
                <div class="tab-pane fade" id="pane-resume" role="tabpanel" aria-labelledby="tab-resume">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <strong>Pause → Active (from Tomorrow)</strong>
                            <span class="pill-count">{{ count($resumingTomorrow) }}</span>
                        </div>
                        <div class="card-body">
                            {!! renderSubsTable($resumingTomorrow) !!}
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

        {{-- ITEMS MODAL --}}
        <div class="modal fade" id="itemsModal" tabindex="-1" aria-labelledby="itemsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="itemsModalLabel"><i class="bi bi-list-ul"></i> Customize Order Items</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="itemsContainer"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            // ===== Lock screen countdown (only present when locked) =====
            const unlockBtn   = document.getElementById('unlockBtn');
            const checkNowBtn = document.getElementById('checkNowBtn');
            const countdownEl = document.getElementById('countdownText');

            // These are provided only on the lock screen by the controller.
            const unlockAtMs  = @json($unlockAtMs ?? null);
            const serverNowMs = @json($serverNowMs ?? null);

            if (unlockAtMs && serverNowMs && countdownEl) {
                const clientNow   = Date.now();
                const skew        = serverNowMs - clientNow; // align client with server time
                const pad = (n)=> String(n).padStart(2,'0');

                function updateCountdown() {
                    const now = Date.now() + skew;
                    let diff  = unlockAtMs - now;

                    if (diff <= 0) {
                        countdownEl.textContent = '00:00:00';
                        if (unlockBtn) {
                            unlockBtn.disabled = false;
                            unlockBtn.classList.remove('btn-secondary');
                            unlockBtn.classList.add('btn-primary');
                        }
                        return;
                    }
                    const hrs = Math.floor(diff / 3600000); diff -= hrs * 3600000;
                    const min = Math.floor(diff / 60000);   diff -= min * 60000;
                    const sec = Math.floor(diff / 1000);
                    countdownEl.textContent = `${pad(hrs)}:${pad(min)}:${pad(sec)}`;
                    if (unlockBtn) unlockBtn.disabled = true;
                    requestAnimationFrame(updateCountdown);
                }
                updateCountdown();

                unlockBtn && unlockBtn.addEventListener('click', function(){
                    // After 5pm this will be enabled
                    window.location.reload();
                });
                checkNowBtn && checkNowBtn.addEventListener('click', function(){
                    window.location.reload();
                });
            }

            // ===== Address & Items modals (only present on full page) =====
            const addressBody = document.getElementById('addressModalBody');
            const copyBtn = document.getElementById('copyAddressBtn');

            document.addEventListener('click', function (e) {
                const addrBtn = e.target.closest('.view-address');
                if (addrBtn) {
                    const addr = addrBtn.getAttribute('data-address') || '—';
                    if (addressBody) addressBody.textContent = addr;
                }

                const itemsBtn = e.target.closest('.view-items');
                if (itemsBtn) {
                    const raw = itemsBtn.getAttribute('data-items') || '[]';
                    let items = [];
                    try { items = JSON.parse(raw); } catch (e) { items = []; }
                    renderItems(items);
                }

                const copyLineBtn = e.target.closest('.copy-line');
                if (copyLineBtn) {
                    const card = copyLineBtn.closest('.total-card');
                    if (card) {
                        const name = (card.getAttribute('data-name') || '').toUpperCase();
                        const qty  = card.getAttribute('data-qty') || '';
                        const unit = card.querySelector('.unit-chip .text-uppercase')?.textContent || '';
                        const text = `${name} - ${qty} ${unit}`;
                        navigator.clipboard.writeText(text).then(() => {
                            copyLineBtn.innerHTML = '<i class="bi bi-check2"></i>';
                            setTimeout(() => copyLineBtn.innerHTML = '<i class="bi bi-clipboard"></i>', 1100);
                        }).catch(() => {});
                    }
                }
            });

            copyBtn && copyBtn.addEventListener('click', async function () {
                try {
                    await navigator.clipboard.writeText(addressBody.textContent || '');
                    copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copied';
                    setTimeout(() => copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Copy', 1200);
                } catch (e) {}
            });

            function renderItems(items) {
                const el = document.getElementById('itemsContainer');
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML = '<div class="alert alert-secondary mb-0">No items found for this request.</div>';
                    return;
                }

                let html = '';
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm table-striped align-middle">';
                html += '<thead class="table-light">';
                html += '<tr>';
                html += '<th>#</th><th>Type</th><th>Garland</th><th>Garland Qty</th><th>Garland Size</th>';
                html += '<th>Flower</th><th>Unit</th><th>Flower Qty</th><th>Flower Count</th><th>Size</th>';
                html += '</tr></thead><tbody>';

                items.forEach((it, idx) => {
                    html += '<tr>';
                    html += '<td>' + (idx + 1) + '</td>';
                    html += '<td>' + escapeHtml(it.type || '') + '</td>';
                    html += '<td>' + escapeHtml(it.garland_name || '') + '</td>';
                    html += '<td>' + escapeHtml(it.garland_quantity || '') + '</td>';
                    html += '<td>' + escapeHtml(it.garland_size || '') + '</td>';
                    html += '<td>' + escapeHtml(it.flower_name || '') + '</td>';
                    html += '<td>' + escapeHtml(it.flower_unit || '') + '</td>';
                    html += '<td>' + escapeHtml(it.flower_quantity || '') + '</td>';
                    html += '<td>' + escapeHtml(it.flower_count || '') + '</td>';
                    html += '<td>' + escapeHtml(it.size || '') + '</td>';
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                el.innerHTML = html;
            }

            function escapeHtml(str) {
                return (str || '').toString()
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        })();
    </script>
@endsection
