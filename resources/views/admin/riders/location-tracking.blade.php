@extends('admin.layouts.apps')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Optional: Icons (if your admin already includes icons, you can remove this) --}}
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

        #latestMap {
            height: 420px;
            border-radius: 14px;
        }

        #singleMap {
            height: 420px;
            border-radius: 14px;
        }

        /* Rider pill */
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

        /* Modal */
        .modal-content {
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-hover);
        }
    </style>
@endsection

@section('content')
    {{-- HERO HEADER --}}
    <div class="page-hero mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="title mb-0">Rider Location Tracking</h5>
                <div class="sub">Live overview of rider pings, latest positions, and history.</div>
            </div>
            <div class="d-flex gap-2">
                <span class="kpi-icon"><i class="fa-solid fa-location-dot"></i></span>
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
                                                    <div class="rider-name">
                                                        {{ $t->rider_name ?? 'Rider #' . $t->rider_id }}</div>
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
                                                <a target="_blank"
                                                    href="https://www.google.com/maps?q={{ $t->latitude }},{{ $t->longitude }}">
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
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const latestMarkers = @json($latestMarkers);

        // Latest map
        const latestMap = L.map('latestMap', {
            scrollWheelZoom: true
        });
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
                latestMap.fitBounds(bounds, {
                    padding: [30, 30]
                });
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
                modalMap = L.map('singleMap', {
                    scrollWheelZoom: true
                });
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(modalMap);
            }

            modalMap.setView([lat, lng], 16);

            if (modalMarker) {
                modalMarker.setLatLng([lat, lng]);
            } else {
                modalMarker = L.marker([lat, lng]).addTo(modalMap);
            }

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
    </script>
@endsection
