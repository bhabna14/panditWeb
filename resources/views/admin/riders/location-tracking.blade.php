@extends('admin.layouts.apps')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        :root {
            --bg-soft: #f6f7fb;
            --card-radius: 16px;
            --shadow-soft: 0 10px 25px rgba(0, 0, 0, .06);
            --shadow-hover: 0 14px 30px rgba(0, 0, 0, .09);
        }

        /* Page header */
        .page-hero {
            border-radius: var(--card-radius);
            background: linear-gradient(135deg, #2b59ff, #7c3aed, #ff3d77);
            color: #fff;
            padding: 18px 18px;
            box-shadow: var(--shadow-soft);
        }

        .page-hero .title {
            font-weight: 800;
            letter-spacing: .4px;
            margin: 0;
            font-size: 1.2rem;
        }

        .page-hero .sub {
            opacity: .9;
            margin-top: 4px;
            font-size: .9rem;
        }

        /* Filter card */
        .filter-card {
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0, 0, 0, .04);
        }

        .filter-strip {
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(43, 89, 255, .10), rgba(124, 58, 237, .10));
            padding: 12px 12px;
        }

        .form-label {
            font-weight: 700;
            font-size: .88rem;
        }

        /* KPI cards */
        .kpi-card {
            border-radius: var(--card-radius);
            border: 1px solid rgba(0, 0, 0, .04);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .kpi-body {
            padding: 16px 16px;
        }

        .kpi-label {
            font-size: .85rem;
            opacity: .9;
            margin-bottom: 6px;
        }

        .kpi-value {
            font-size: 1.6rem;
            font-weight: 900;
            line-height: 1.1;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
        }

        .kpi-1 {
            background: linear-gradient(135deg, #00c2ff, #2b59ff);
            color: #fff;
        }

        .kpi-2 {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .kpi-3 {
            background: linear-gradient(135deg, #fb7185, #f97316);
            color: #fff;
        }

        /* Map / table cards */
        .panel-card {
            border-radius: var(--card-radius);
            border: 1px solid rgba(0, 0, 0, .04);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            background: #fff;
        }

        .panel-head {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, rgba(43, 89, 255, .08), rgba(124, 58, 237, .08));
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }

        .panel-title {
            margin: 0;
            font-weight: 900;
            font-size: 1rem;
            color: #111827;
        }

        .panel-note {
            font-size: .83rem;
            color: #6b7280;
        }

        #latestMap,
        #singleMap {
            height: 420px;
            border-radius: 14px;
        }

        /* Rider pill (table) */
        .rider-pill {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .45rem .6rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid rgba(0, 0, 0, .05);
        }

        .rider-pill img {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid rgba(43, 89, 255, .25);
            background: #fff;
        }

        .rider-name {
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
        }

        .rider-phone {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .15rem .45rem;
            border-radius: 999px;
            font-size: .78rem;
            color: #374151;
            background: rgba(43, 89, 255, .08);
        }

        /* Table */
        .table thead th {
            white-space: nowrap;
            font-weight: 800;
            color: #111827;
        }

        .table tbody tr:hover {
            background: rgba(124, 58, 237, .06);
        }

        .coord {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: .85rem;
        }

        .btn-soft {
            background: #f6f7fb;
            border: 1px solid #e9ecef;
            border-radius: 12px;
        }

        .btn-soft:hover {
            background: #eef1f7;
        }

        .btn-grad {
            border-radius: 12px;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #2b59ff, #7c3aed);
            box-shadow: 0 8px 18px rgba(43, 89, 255, .18);
        }

        .btn-grad:hover {
            color: #fff;
            filter: brightness(0.98);
        }

        .btn-stop {
            border-radius: 12px;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #ef4444, #f97316);
            box-shadow: 0 8px 18px rgba(239, 68, 68, .18);
        }

        .btn-stop:hover {
            color: #fff;
            filter: brightness(0.98);
        }

        /* Modal */
        .modal-content {
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-hover);
        }

        /* Rider Control Cards */
        .rider-control-wrap {
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0, 0, 0, .04);
            overflow: hidden;
        }

        .rider-control-head {
            padding: 14px 16px;
            background: linear-gradient(135deg, rgba(34, 197, 94, .10), rgba(43, 89, 255, .10));
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .rider-control-title {
            margin: 0;
            font-weight: 900;
            color: #111827;
        }

        .rider-card {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, .06);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            background: #fff;
            transition: transform .15s ease, box-shadow .15s ease;
            overflow: hidden;
            height: 100%;
        }

        .rider-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.10);
        }

        .rider-card-top {
            padding: 14px 14px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .rider-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            overflow: hidden;
            flex: 0 0 auto;
            border: 2px solid rgba(43, 89, 255, .18);
            background: #f8fafc;
        }

        .rider-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .rider-card-name {
            font-weight: 900;
            color: #111827;
            margin: 0;
            line-height: 1.1;
        }

        .rider-card-meta {
            color: #6b7280;
            font-size: .85rem;
        }

        .tracking-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .2rem .55rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 800;
            border: 1px solid rgba(0, 0, 0, .06);
            white-space: nowrap;
        }

        .tracking-on {
            background: rgba(34, 197, 94, .12);
            color: #166534;
        }

        .tracking-off {
            background: rgba(239, 68, 68, .10);
            color: #991b1b;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: currentColor;
            opacity: .9;
        }

        .rider-card-mid {
            padding: 0 14px 12px;
        }

        .mini-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: .85rem;
            color: #374151;
            padding: 8px 0;
            border-top: 1px dashed rgba(0, 0, 0, .08);
        }

        .mini-row:first-child {
            border-top: none;
            padding-top: 0;
        }

        .mini-label {
            color: #6b7280;
        }

        .rider-card-actions {
            padding: 12px 14px 14px;
            border-top: 1px solid rgba(0, 0, 0, .05);
            display: flex;
            gap: 10px;
        }

        .btn-card {
            border-radius: 12px;
            width: 100%;
        }

        /* Toast */
        .mini-toast {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 99999;
            min-width: 260px;
            max-width: 360px;
            background: #111827;
            color: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, .25);
            display: none;
        }

        .mini-toast.show {
            display: block;
        }

        .mini-toast .t-title {
            font-weight: 900;
            margin-bottom: 4px;
        }

        .mini-toast .t-sub {
            opacity: .9;
            font-size: .9rem;
        }
    </style>
@endsection

@section('content')
    {{-- HERO HEADER --}}
    <div class="page-hero mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="title mb-0">Rider Location Tracking</h5>
                <div class="sub">Live overview of rider pings, latest positions, history, and tracking control.</div>
            </div>
            <div class="d-flex gap-2">
                <span class="kpi-icon"><i class="fa-solid fa-location-dot"></i></span>
            </div>
        </div>
    </div>

    {{-- RIDER CONTROL CARDS --}}
    <div class="rider-control-wrap mb-3">
        <div class="rider-control-head">
            <div>
                <h6 class="rider-control-title mb-0">
                    <i class="fa-solid fa-toggle-on me-2"></i>Rider Tracking Control
                </h6>
                <div class="panel-note">Start/Stop tracking rider-wise (updates RiderDetails.tracking)</div>
            </div>
            <div class="panel-note">Total Riders: {{ number_format($riderCards->count()) }}</div>
        </div>

        <div class="p-3">
            <div class="row g-3">
                @foreach ($riderCards as $rc)
                    @php
                        $imgUrl = $rc['img'] ?: asset('assets/img/faces/6.jpg');
                        // IMPORTANT: use tracking_on boolean from controller
                        $isOn = (bool) ($rc['tracking_on'] ?? false);
                        $badgeClass = $isOn ? 'tracking-badge tracking-on' : 'tracking-badge tracking-off';
                        $badgeText = $isOn ? 'TRACKING ON' : 'TRACKING OFF';
                    @endphp

                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="rider-card js-rider-card" data-rider-id="{{ $rc['rider_id'] }}"
                            data-tracking="{{ $isOn ? 1 : 0 }}">
                            <div class="rider-card-top">
                                <div class="rider-avatar">
                                    <img src="{{ $imgUrl }}" alt="rider">
                                </div>

                                <div class="flex-grow-1">
                                    <p class="rider-card-name mb-1">{{ $rc['name'] }}</p>
                                    <div class="rider-card-meta">
                                        <i class="fa-solid fa-phone me-1"></i>{{ $rc['phone'] ?: '—' }}
                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="{{ $badgeClass }} js-tracking-badge">
                                        <span class="dot"></span>
                                        <span class="js-tracking-text">{{ $badgeText }}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="rider-card-mid">
                                <div class="mini-row">
                                    <div class="mini-label"><i class="fa-solid fa-clock me-1"></i>Last Ping</div>
                                    <div class="fw-semibold">{{ $rc['last_time'] ?: '—' }}</div>
                                </div>

                                <div class="mini-row">
                                    <div class="mini-label"><i class="fa-solid fa-location-crosshairs me-1"></i>Coords
                                    </div>
                                    <div class="fw-semibold coord">
                                        @if ($rc['lat'] !== null && $rc['lng'] !== null)
                                            {{ $rc['lat'] }}, {{ $rc['lng'] }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                <div class="mini-row">
                                    <div class="mini-label"><i class="fa-solid fa-map me-1"></i>Open</div>
                                    <div class="fw-semibold">
                                        @if ($rc['lat'] !== null && $rc['lng'] !== null)
                                            <a target="_blank"
                                                href="https://www.google.com/maps?q={{ $rc['lat'] }},{{ $rc['lng'] }}">Google
                                                Maps</a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="rider-card-actions">
                                <button type="button"
                                    class="btn {{ $isOn ? 'btn-stop' : 'btn-grad' }} btn-card js-toggle-tracking"
                                    data-rider-id="{{ $rc['rider_id'] }}" data-action="{{ $isOn ? 'stop' : 'start' }}">
                                    <i class="fa-solid {{ $isOn ? 'fa-circle-stop' : 'fa-circle-play' }} me-1"></i>
                                    <span class="js-btn-text">{{ $isOn ? 'Stop' : 'Start' }}</span>
                                </button>

                                <a class="btn btn-soft btn-card"
                                    href="{{ route('rider.location-tracking', ['rider_id' => $rc['rider_id']]) }}">
                                    <i class="fa-solid fa-filter me-1"></i> View Logs
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($riderCards->count() === 0)
                    <div class="col-12">
                        <div class="text-center text-muted p-4">
                            No riders found.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card filter-card mb-3">
        <div class="card-body">
            <div class="filter-strip mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div style="font-weight:900; color:#111827;">
                        <i class="fa-solid fa-filter me-1"></i> Filters
                    </div>
                    <div class="panel-note">Use filters to narrow by rider & date range</div>
                </div>
            </div>

            <form method="GET" action="{{ route('rider.location-tracking') }}" class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label">Rider</label>
                    <select name="rider_id" class="form-control">
                        <option value="">All Riders</option>
                        @foreach ($riders as $r)
                            <option value="{{ $r->rider_id }}"
                                {{ (string) $riderId === (string) $r->rider_id ? 'selected' : '' }}>
                                {{ $r->rider_name }} ({{ $r->phone_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" value="{{ $from }}" class="form-control">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" value="{{ $to }}" class="form-control">
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button class="btn btn-grad w-100" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Apply
                    </button>
                    <a class="btn btn-soft w-100" href="{{ route('rider.location-tracking') }}">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="kpi-card kpi-1">
                <div class="kpi-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Total Location Pings</div>
                        <div class="kpi-value">{{ number_format($totalPings) }}</div>
                    </div>
                    <div class="kpi-icon"><i class="fa-solid fa-wave-square"></i></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="kpi-card kpi-2">
                <div class="kpi-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Unique Riders</div>
                        <div class="kpi-value">{{ number_format($uniqueRiders) }}</div>
                    </div>
                    <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="kpi-card kpi-3">
                <div class="kpi-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Latest Update</div>
                        <div class="kpi-value" style="font-size:1.05rem;font-weight:900;">
                            {{ $latestPing ? \Carbon\Carbon::parse($latestPing)->format('d M Y, h:i A') : '—' }}
                        </div>
                    </div>
                    <div class="kpi-icon"><i class="fa-solid fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAP + TABLE --}}
    <div class="row">
        {{-- Map --}}
        <div class="col-lg-6 mb-3">
            <div class="panel-card">
                <div class="panel-head">
                    <h6 class="panel-title"><i class="fa-solid fa-map-location-dot me-2"></i>Latest Rider Positions</h6>
                    <div class="panel-note">OpenStreetMap (Leaflet)</div>
                </div>
                <div class="card-body">
                    <div id="latestMap"></div>
                    <div class="mt-2 panel-note">
                        Tip: Click a marker to see rider details and open in Google Maps.
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="col-lg-6 mb-3">
            <div class="panel-card">
                <div class="panel-head">
                    <h6 class="panel-title"><i class="fa-solid fa-list-check me-2"></i>Tracking History</h6>
                    <div class="panel-note">Showing {{ $trackings->count() }} / {{ $trackings->total() }}</div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Rider</th>
                                    <th>Coordinates</th>
                                    <th>Date/Time</th>
                                    <th style="width: 110px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trackings as $i => $t)
                                    @php
                                        $imgUrl = null;
                                        if (!empty($t->rider_img)) {
                                            try {
                                                $imgUrl = Storage::url($t->rider_img);
                                            } catch (\Throwable $e) {
                                                $imgUrl = null;
                                            }
                                        }
                                        $imgUrl = $imgUrl ?: asset('assets/img/faces/6.jpg');
                                    @endphp
                                    <tr>
                                        <td>{{ $trackings->firstItem() + $i }}</td>

                                        <td>
                                            <span class="rider-pill">
                                                <img src="{{ $imgUrl }}" alt="rider">
                                                <span>
                                                    <div class="rider-name">{{ $t->rider_name ?? 'Rider #' . $t->rider_id }}</div>
                                                    <div class="rider-phone mt-1">
                                                        <i class="fa-solid fa-phone"></i>
                                                        <span>{{ $t->phone_number ?? '—' }}</span>
                                                    </div>
                                                </span>
                                            </span>
                                        </td>

                                        <td class="coord">
                                            {{ $t->latitude }}, {{ $t->longitude }}
                                            <div class="text-muted" style="font-size: .8rem;">
                                                <a target="_blank" href="https://www.google.com/maps?q={{ $t->latitude }},{{ $t->longitude }}">
                                                    Open in Google Maps
                                                </a>
                                            </div>
                                        </td>

                                        <td>
                                            {{ $t->date_time ? \Carbon\Carbon::parse($t->date_time)->format('d M Y, h:i A') : '—' }}
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-grad w-100 js-view-map"
                                                data-lat="{{ $t->latitude }}" data-lng="{{ $t->longitude }}"
                                                data-name="{{ e($t->rider_name ?? 'Rider #' . $t->rider_id) }}"
                                                data-phone="{{ e($t->phone_number ?? '') }}"
                                                data-time="{{ e($t->date_time ? \Carbon\Carbon::parse($t->date_time)->format('d M Y, h:i A') : '') }}">
                                                <i class="fa-solid fa-eye me-1"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4 text-muted">
                                            No tracking records found for the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $trackings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, rgba(43,89,255,.10), rgba(124,58,237,.10));">
                    <div>
                        <h6 class="mb-0" id="modalTitle" style="font-weight:900;">Rider Location</h6>
                        <small class="text-muted" id="modalSub">—</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="singleMap"></div>
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <div class="text-muted" id="modalTime" style="font-size: .9rem;"></div>
                        <a id="modalGoogleLink" target="_blank" class="btn btn-grad btn-sm" href="#">
                            <i class="fa-solid fa-map-location-dot me-1"></i> Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div class="mini-toast" id="miniToast">
        <div class="t-title" id="toastTitle">Done</div>
        <div class="t-sub" id="toastSub">—</div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const latestMarkers = @json($latestMarkers);
        const trackingToggleUrl = "{{ route('rider.tracking.toggle') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toast
        const toastEl = document.getElementById('miniToast');
        const toastTitleEl = document.getElementById('toastTitle');
        const toastSubEl = document.getElementById('toastSub');
        let toastTimer = null;

        function showToast(title, sub) {
            toastTitleEl.textContent = title || 'Done';
            toastSubEl.textContent = sub || '';
            toastEl.classList.add('show');
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toastEl.classList.remove('show'), 2600);
        }

        // Latest map
        const latestMap = L.map('latestMap', { scrollWheelZoom: true });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(latestMap);

        const bounds = [];

        if (Array.isArray(latestMarkers) && latestMarkers.length) {
            latestMarkers.forEach(m => {
                if (!m.lat || !m.lng) return;

                const gmap = `https://www.google.com/maps?q=${m.lat},${m.lng}`;
                const popup = `
                    <div style="min-width:220px;">
                        <div style="font-weight:800;">${m.name}</div>
                        <div style="color:#6b7280;font-size:.85rem;">${m.phone ? m.phone : ''}</div>
                        <div style="margin-top:6px;font-size:.85rem;">Last: ${m.time ? m.time : '—'}</div>
                        <div style="margin-top:8px;">
                            <a href="${gmap}" target="_blank">Open in Google Maps</a>
                        </div>
                    </div>
                `;

                L.marker([m.lat, m.lng]).addTo(latestMap).bindPopup(popup);
                bounds.push([m.lat, m.lng]);
            });

            if (bounds.length) {
                latestMap.fitBounds(bounds, { padding: [30, 30] });
            } else {
                latestMap.setView([20.5937, 78.9629], 5);
            }
        } else {
            latestMap.setView([20.5937, 78.9629], 5);
        }

        // Modal map
        let modalMap = null;
        let modalMarker = null;

        const mapModalEl = document.getElementById('mapModal');
        const mapModal = new bootstrap.Modal(mapModalEl);

        function initModalMap(lat, lng) {
            if (!modalMap) {
                modalMap = L.map('singleMap', { scrollWheelZoom: true });
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(modalMap);
            }

            modalMap.setView([lat, lng], 16);

            if (modalMarker) modalMarker.setLatLng([lat, lng]);
            else modalMarker = L.marker([lat, lng]).addTo(modalMap);

            setTimeout(() => modalMap.invalidateSize(), 200);
        }

        document.querySelectorAll('.js-view-map').forEach(btn => {
            btn.addEventListener('click', () => {
                const lat = parseFloat(btn.dataset.lat);
                const lng = parseFloat(btn.dataset.lng);

                const name = btn.dataset.name || 'Rider Location';
                const phone = btn.dataset.phone || '';
                const time = btn.dataset.time || '';

                document.getElementById('modalTitle').innerText = name;
                document.getElementById('modalSub').innerText = phone ? phone : '—';
                document.getElementById('modalTime').innerText = time ? `Last update: ${time}` : '';

                const gmap = `https://www.google.com/maps?q=${lat},${lng}`;
                document.getElementById('modalGoogleLink').href = gmap;

                mapModal.show();
                setTimeout(() => initModalMap(lat, lng), 150);
            });
        });

        // Start/Stop Tracking
        function setCardUI(card, isOn) {
            card.dataset.tracking = isOn ? '1' : '0';

            const badge = card.querySelector('.js-tracking-badge');
            const badgeText = card.querySelector('.js-tracking-text');

            badge.classList.remove('tracking-on', 'tracking-off');
            badge.classList.add(isOn ? 'tracking-on' : 'tracking-off');
            badgeText.textContent = isOn ? 'TRACKING ON' : 'TRACKING OFF';

            const btn = card.querySelector('.js-toggle-tracking');
            const btnText = btn.querySelector('.js-btn-text');
            const icon = btn.querySelector('i');

            btn.classList.remove('btn-grad', 'btn-stop');
            btn.classList.add(isOn ? 'btn-stop' : 'btn-grad');

            btn.dataset.action = isOn ? 'stop' : 'start';
            btnText.textContent = isOn ? 'Stop' : 'Start';

            icon.classList.remove('fa-circle-play', 'fa-circle-stop');
            icon.classList.add(isOn ? 'fa-circle-stop' : 'fa-circle-play');
        }

        async function toggleTracking(riderId, action) {
            const res = await fetch(trackingToggleUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rider_id: riderId, action })
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Unable to update tracking right now.');
            }

            return data;
        }

        document.querySelectorAll('.js-toggle-tracking').forEach(btn => {
            btn.addEventListener('click', async () => {
                const riderId = btn.dataset.riderId;
                const action = btn.dataset.action;

                const card = btn.closest('.js-rider-card');
                if (!card) return;

                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> Please wait`;

                try {
                    const data = await toggleTracking(riderId, action);

                    // IMPORTANT: use tracking_on from controller response
                    const isOn = !!data.tracking_on;

                    setCardUI(card, isOn);

                    showToast(
                        'Tracking updated',
                        isOn ? 'Tracking started for this rider.' : 'Tracking stopped for this rider.'
                    );
                } catch (e) {
                    showToast('Action failed', e.message || 'Something went wrong');
                } finally {
                    btn.disabled = false;

                    const isOn = card.dataset.tracking === '1';
                    btn.innerHTML = `
                        <i class="fa-solid ${isOn ? 'fa-circle-stop' : 'fa-circle-play'} me-1"></i>
                        <span class="js-btn-text">${isOn ? 'Stop' : 'Start'}</span>
                    `;
                    btn.classList.remove('btn-grad', 'btn-stop');
                    btn.classList.add(isOn ? 'btn-stop' : 'btn-grad');
                    btn.dataset.action = isOn ? 'stop' : 'start';
                    btn.dataset.riderId = riderId;
                }
            });
        });
    </script>
@endsection
