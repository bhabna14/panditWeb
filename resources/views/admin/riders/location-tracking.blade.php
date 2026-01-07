@extends('admin.layouts.apps')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        .stat-card { border-radius: 14px; }
        .stat-value { font-size: 1.6rem; font-weight: 700; }
        .stat-label { color: #6c757d; font-size: .9rem; }

        #latestMap { height: 420px; border-radius: 14px; }
        #singleMap { height: 420px; border-radius: 14px; }

        .rider-pill {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .35rem .6rem; border-radius: 999px;
            background: #f6f7fb;
        }
        .rider-pill img { width: 28px; height: 28px; border-radius: 999px; object-fit: cover; }
        .coord {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: .85rem;
        }
        .table thead th { white-space: nowrap; }
        .btn-soft { background: #f6f7fb; border: 1px solid #e9ecef; }
        .btn-soft:hover { background: #eef1f7; }
    </style>
@endsection

@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">RIDER LOCATION TRACKING</span>
    </div>
</div>

<div class="card custom-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('rider.location-tracking') }}" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">Rider</label>
                <select name="rider_id" class="form-control">
                    <option value="">All Riders</option>
                    @foreach($riders as $r)
                        <option value="{{ $r->rider_id }}" {{ (string)$riderId === (string)$r->rider_id ? 'selected' : '' }}>
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
                <button class="btn btn-primary w-100" type="submit">Apply</button>
                <a class="btn btn-soft w-100" href="{{ route('rider.location-tracking') }}">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="card custom-card stat-card">
            <div class="card-body">
                <div class="stat-label">Total Location Pings</div>
                <div class="stat-value">{{ number_format($totalPings) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card custom-card stat-card">
            <div class="card-body">
                <div class="stat-label">Unique Riders</div>
                <div class="stat-value">{{ number_format($uniqueRiders) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card custom-card stat-card">
            <div class="card-body">
                <div class="stat-label">Latest Update</div>
                <div class="stat-value" style="font-size: 1.05rem;">
                    {{ $latestPing ? \Carbon\Carbon::parse($latestPing)->format('d M Y, h:i A') : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-3">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Latest Rider Positions</h6>
                <small class="text-muted">OpenStreetMap (Leaflet)</small>
            </div>
            <div class="card-body">
                <div id="latestMap"></div>
                <div class="mt-2 text-muted" style="font-size: .85rem;">
                    Tip: Click a marker to see rider details and open in Google Maps.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-3">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Tracking History</h6>
                <small class="text-muted">Showing {{ $trackings->count() }} / {{ $trackings->total() }}</small>
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
                            <tr>
                                <td>{{ $trackings->firstItem() + $i }}</td>
                                <td>
                                    <span class="rider-pill">
                                        @php
                                            $img = $t->rider_img ? asset($t->rider_img) : asset('assets/img/faces/6.jpg');
                                        @endphp
                                        <img src="{{ $img }}" alt="rider">
                                        <span>
                                            <div style="font-weight: 700;">{{ $t->rider_name ?? ('Rider #' . $t->rider_id) }}</div>
                                            <div class="text-muted" style="font-size: .85rem;">{{ $t->phone_number ?? '—' }}</div>
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
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-soft w-100 js-view-map"
                                        data-lat="{{ $t->latitude }}"
                                        data-lng="{{ $t->longitude }}"
                                        data-name="{{ e($t->rider_name ?? ('Rider #' . $t->rider_id)) }}"
                                        data-phone="{{ e($t->phone_number ?? '') }}"
                                        data-time="{{ e($t->date_time ? \Carbon\Carbon::parse($t->date_time)->format('d M Y, h:i A') : '') }}"
                                    >
                                        View
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

<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header">
                <div>
                    <h6 class="mb-0" id="modalTitle">Rider Location</h6>
                    <small class="text-muted" id="modalSub">—</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="singleMap"></div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <div class="text-muted" id="modalTime" style="font-size: .9rem;"></div>
                    <a id="modalGoogleLink" target="_blank" class="btn btn-sm btn-primary" href="#">
                        Open in Google Maps
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
        // Markers are already prepared in Controller (so Blade stays safe)
        const latestMarkers = @json($latestMarkers);

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
                        <div style="font-weight:700;">${m.name}</div>
                        <div style="color:#6c757d;font-size:.85rem;">${m.phone ? m.phone : ''}</div>
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

                const name  = btn.dataset.name || 'Rider Location';
                const phone = btn.dataset.phone || '';
                const time  = btn.dataset.time || '';

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
