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

        .page-hero {
            border-radius: var(--card-radius);
            background: linear-gradient(135deg, #2b59ff, #7c3aed, #ff3d77);
            color: #fff;
            padding: 18px;
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

        .filter-card {
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0, 0, 0, .04);
        }

        .filter-strip {
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(43, 89, 255, .10), rgba(124, 58, 237, .10));
            padding: 12px;
        }

        .form-label {
            font-weight: 700;
            font-size: .88rem;
        }

        .kpi-card {
            border-radius: var(--card-radius);
            border: 1px solid rgba(0, 0, 0, .04);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .kpi-body { padding: 16px; }

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

        .kpi-1 { background: linear-gradient(135deg, #00c2ff, #2b59ff); color: #fff; }
        .kpi-2 { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; }
        .kpi-3 { background: linear-gradient(135deg, #fb7185, #f97316); color: #fff; }

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

        #latestMap, #singleMap {
            height: 420px;
            border-radius: 14px;
        }

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
        .btn-soft:hover { background: #eef1f7; }

        .btn-grad {
            border-radius: 12px;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #2b59ff, #7c3aed);
            box-shadow: 0 8px 18px rgba(43, 89, 255, .18);
        }
        .btn-grad:hover { color: #fff; filter: brightness(.98); }

        .btn-stop {
            border-radius: 12px;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #ef4444, #f97316);
            box-shadow: 0 8px 18px rgba(239, 68, 68, .18);
        }
        .btn-stop:hover { color: #fff; filter: brightness(.98); }

        .modal-content {
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-hover);
        }

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
            box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
            background: #fff;
            transition: transform .15s ease, box-shadow .15s ease;
            overflow: hidden;
            height: 100%;
            padding: 14px;
        }

        .rider-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(15, 23, 42, .10);
        }

        .rider-mini-name {
            font-weight: 900;
            color: #111827;
            margin-bottom: 12px;
            line-height: 1.2;
            font-size: .98rem;
        }

        .btn-card { border-radius: 12px; width: 100%; }
    </style>
@endsection

@section('content')
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

    {{-- RIDER CONTROL CARDS (ONLY: NAME + START/STOP BUTTON) --}}
    <div class="rider-control-wrap mb-3">
        <div class="rider-control-head">
            <div>
                <h6 class="rider-control-title mb-0">
                    <i class="fa-solid fa-toggle-on me-2"></i>Rider Tracking Control
                </h6>
                <div class="panel-note">Start/Stop tracking rider-wise (updates flower__rider_details.tracking)</div>
            </div>
            <div class="panel-note">Total Riders: {{ number_format($riderCards->count()) }}</div>
        </div>

        <div class="p-3">
            <div class="row g-3">
                @foreach ($riderCards as $rc)
                    @php $isOn = (bool) ($rc['tracking_on'] ?? false); @endphp

                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="rider-card js-rider-card"
                             data-rider-id="{{ $rc['rider_id'] }}"
                             data-tracking="{{ $isOn ? 1 : 0 }}">

                            <div class="rider-mini-name">{{ $rc['name'] }}</div>

                            <button type="button"
                                    class="btn {{ $isOn ? 'btn-stop' : 'btn-grad' }} btn-card js-toggle-tracking"
                                    data-rider-id="{{ $rc['rider_id'] }}"
                                    data-action="{{ $isOn ? 'stop' : 'start' }}">
                                <i class="fa-solid {{ $isOn ? 'fa-circle-stop' : 'fa-circle-play' }} me-1 js-btn-icon"></i>
                                <span class="js-btn-text">{{ $isOn ? 'Stop' : 'Start' }}</span>
                            </button>
                        </div>
                    </div>
                @endforeach

                @if ($riderCards->count() === 0)
                    <div class="col-12">
                        <div class="text-center text-muted p-4">No riders found.</div>
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
                    <a class="btn btn-soft w-100" href="{{ route('rider.location-tracking') }}">Reset</a>
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
        <div class="col-lg-6 mb-3">
            <div class="panel-card">
                <div class="panel-head">
                    <h6 class="panel-title"><i class="fa-solid fa-map-location-dot me-2"></i>Latest Rider Positions</h6>
                    <div class="panel-note">OpenStreetMap (Leaflet)</div>
                </div>
                <div class="card-body">
                    <div id="latestMap"></div>
                    <div class="mt-2 panel-note">Tip: Click a marker to see rider details and open in Google Maps.</div>
                </div>
            </div>
        </div>

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
                                    <tr>
                                        <td>{{ $trackings->firstItem() + $i }}</td>

                                        <td>
                                            <div style="font-weight:800;color:#111827;">
                                                {{ $t->rider_name ?? 'Rider #' . $t->rider_id }}
                                            </div>
                                            <div class="text-muted" style="font-size:.85rem;">
                                                <i class="fa-solid fa-phone me-1"></i>{{ $t->phone_number ?? '—' }}
                                            </div>
                                        </td>

                                        <td class="coord">
                                            {{ $t->latitude }}, {{ $t->longitude }}
                                            <div class="text-muted" style="font-size: .8rem;">
                                                <a target="_blank" href="https://www.google.com/maps?q={{ $t->latitude }},{{ $t->longitude }}">Open in Google Maps</a>
                                            </div>
                                        </td>

                                        <td>{{ $t->date_time ? \Carbon\Carbon::parse($t->date_time)->format('d M Y, h:i A') : '—' }}</td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-grad w-100 js-view-map"
                                                data-lat="{{ $t->latitude }}"
                                                data-lng="{{ $t->longitude }}"
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

                    <div class="p-3">{{ $trackings->links() }}</div>
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

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const latestMarkers = @json($latestMarkers);
            const trackingToggleUrl = "{{ route('rider.tracking.toggle') }}";

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            // ---------- SweetAlert helpers ----------
            function swalSuccess(message) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: message || 'Updated successfully.',
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true
                });
            }

            function swalError(message) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: message || 'Something went wrong.',
                    showConfirmButton: false,
                    timer: 2600,
                    timerProgressBar: true
                });
            }

            // ---------- Latest map ----------
            const latestMapEl = document.getElementById('latestMap');
            let latestMap = null;

            if (latestMapEl) {
                latestMap = L.map('latestMap', { scrollWheelZoom: true });
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

                    if (bounds.length) latestMap.fitBounds(bounds, { padding: [30, 30] });
                    else latestMap.setView([20.5937, 78.9629], 5);
                } else {
                    latestMap.setView([20.5937, 78.9629], 5);
                }
            }

            // ---------- Modal map ----------
            const mapModalEl = document.getElementById('mapModal');
            const singleMapEl = document.getElementById('singleMap');

            let modalMap = null;
            let modalMarker = null;
            let mapModal = null;

            if (mapModalEl && typeof bootstrap !== 'undefined') {
                mapModal = new bootstrap.Modal(mapModalEl);
            }

            function initModalMap(lat, lng) {
                if (!singleMapEl) return;

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

                    const t1 = document.getElementById('modalTitle');
                    const t2 = document.getElementById('modalSub');
                    const t3 = document.getElementById('modalTime');
                    const link = document.getElementById('modalGoogleLink');

                    if (t1) t1.innerText = name;
                    if (t2) t2.innerText = phone ? phone : '—';
                    if (t3) t3.innerText = time ? `Last update: ${time}` : '';

                    const gmap = `https://www.google.com/maps?q=${lat},${lng}`;
                    if (link) link.href = gmap;

                    if (mapModal) {
                        mapModal.show();
                        setTimeout(() => initModalMap(lat, lng), 150);
                    }
                });
            });

            // ---------- Start/Stop Tracking UI ----------
            function setCardUI(card, isOn) {
                card.dataset.tracking = isOn ? '1' : '0';

                const btn = card.querySelector('.js-toggle-tracking');
                if (!btn) return;

                const btnText = btn.querySelector('.js-btn-text');
                const icon = btn.querySelector('.js-btn-icon');

                // button style
                btn.classList.remove('btn-grad', 'btn-stop');
                btn.classList.add(isOn ? 'btn-stop' : 'btn-grad');

                // next action
                btn.dataset.action = isOn ? 'stop' : 'start';

                // label
                if (btnText) btnText.textContent = isOn ? 'Stop' : 'Start';

                // icon
                if (icon) {
                    icon.classList.remove('fa-spinner', 'fa-spin', 'fa-circle-play', 'fa-circle-stop');
                    icon.classList.add(isOn ? 'fa-circle-stop' : 'fa-circle-play');
                }
            }

            function setBtnLoading(btn, loading) {
                const btnText = btn.querySelector('.js-btn-text');
                const icon = btn.querySelector('.js-btn-icon');

                if (loading) {
                    btn.disabled = true;
                    if (btnText) btnText.textContent = 'Please wait';
                    if (icon) {
                        icon.classList.remove('fa-circle-play', 'fa-circle-stop');
                        icon.classList.add('fa-spinner', 'fa-spin');
                    }
                } else {
                    btn.disabled = false;
                    // icon/text will be restored by setCardUI()
                    if (icon) icon.classList.remove('fa-spinner', 'fa-spin');
                }
            }

            // ---------- NO JSON request: read TEXT ----------
            async function toggleTracking(riderId, action) {
                const res = await fetch(trackingToggleUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ rider_id: riderId, action })
                });

                const text = await res.text().catch(() => '');

                if (!res.ok) {
                    throw new Error(text || 'Unable to update tracking right now.');
                }

                return text || 'Tracking updated successfully.';
            }

            document.querySelectorAll('.js-toggle-tracking').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const riderId = btn.dataset.riderId;
                    const action = btn.dataset.action; // start/stop
                    const card = btn.closest('.js-rider-card');
                    if (!card) return;

                    // current state before request (for rollback on error)
                    const prevIsOn = (card.dataset.tracking === '1');

                    setBtnLoading(btn, true);

                    try {
                        const message = await toggleTracking(riderId, action);

                        // since response is text only, we determine new state from clicked action
                        const isOn = (action === 'start');

                        setCardUI(card, isOn);
                        swalSuccess(message);
                    } catch (e) {
                        // rollback UI to previous state
                        setCardUI(card, prevIsOn);
                        swalError(e.message || 'Something went wrong.');
                    } finally {
                        setBtnLoading(btn, false);
                    }
                });
            });
        });
    </script>
@endsection
