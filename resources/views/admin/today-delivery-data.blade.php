@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #eef2f7;
            --brand: #0ea5e9;
            --brand2: #6366f1;
            --soft: #f8fafc;
        }

        .page-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #e0f2fe 0%, #ede9fe 100%);
            padding: 18px 20px;
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .page-hero .title {
            font-size: 22px;
            font-weight: 800;
            color: #1f2937;
            margin: 0;
        }

        .page-hero .chip {
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            font-weight: 700
        }

        .cardx {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, .06);
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin: 16px 0 12px;
        }

        .searchbar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 8px 12px;
        }

        .searchbar input {
            border: none;
            outline: none;
            width: 220px;
            max-width: 60vw;
            background: transparent;
        }

        .table thead th {
            background: linear-gradient(180deg, #1e3a8a 0%, #1d4ed8 100%);
            color: #fff;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .06em;
            vertical-align: middle;
            text-align: center;
            border: 0;
        }

        .table td,
        .table th {
            vertical-align: middle;
            text-align: center
        }

        .badge-soft {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #3730a3;
            font-weight: 700;
            padding: .35rem .6rem;
            border-radius: 999px;
        }

        .product-mini {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-start
        }

        .product-mini img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--line)
        }

        .statbar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stat {
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
        }

        .btn-pill {
            border-radius: 999px;
            border: 1px solid var(--line);
            padding: 8px 12px;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="page-hero">
            <i class="bi bi-flowers" style="font-size:22px;color:#0ea5e9"></i>
            <h2 class="title mb-0">Active Subscriptions</h2>
            <span class="chip">Total: {{ $activeSubscriptions->count() }}</span>
            <span class="chip">As of {{ $today->format('d M Y') }}</span>
        </div>

        <div class="cardx mt-3">
            <div class="toolbar">
                <div class="statbar">
                    <div class="stat"><i class="bi bi-people"></i> <strong>{{ $activeSubscriptions->count() }}</strong>
                        customers</div>
                    @php
                        $sumPerDay = $activeSubscriptions->sum(fn($s) => $s->computed->per_day ?? 0);
                    @endphp
                    <div class="stat"><i class="bi bi-cash"></i> ₹{{ number_format($sumPerDay, 2) }} / day</div>
                </div>

                <div class="searchbar">
                    <i class="bi bi-search"></i>
                    <input id="quickSearch" type="text" placeholder="Search name, phone, city, product...">
                    <button class="btn btn-sm btn-light btn-pill"
                        onclick="document.getElementById('quickSearch').value=''; filterRows();">
                        Clear
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle" id="subsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Days Left</th>
                                <th>₹/Day</th>
                                <th>Today Delivery</th>
                                <th>Assigned Rider</th> {{-- 👈 NEW --}}
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeSubscriptions as $i => $sub)
                                @php
                                    $user = $sub->users;
                                    $order = $sub->order;
                                    $addr = $order?->address;
                                    $prod = $sub->flowerProducts;
                                    $perDay = $sub->computed->per_day !== null ? number_format($sub->computed->per_day, 2) : '—';
                                    $todayDelivery = $sub->computed->todays_delivery ?? null;
                                    $apt = $addr?->apartment_name;
                                    $flat = $addr?->apartment_flat_plot; // 👈 NEW
                                    $local = $addr?->localityDetails?->locality_name;
                                    $localC = $addr?->localityDetails?->unique_code;

                                    $tooltip =
                                        trim(
                                            implode(
                                                ' | ',
                                                array_filter([
                                                    $apt ? 'Apartment: ' . $apt : null,
                                                    $flat ? 'Flat: ' . $flat : null, // 👈 NEW
                                                    $local
                                                        ? 'Locality: ' . $local . ($localC ? " ($localC)" : '')
                                                        : null,
                                                ]),
                                            ),
                                        ) ?:
                                        'No address details';

                                    $searchBlob = strtolower(
                                        implode(
                                            ' ',
                                            array_filter([
                                                $user?->name,
                                                $user?->mobile_number,
                                                $prod?->name,
                                                $addr?->city,
                                                $addr?->state,
                                                $addr?->pincode,
                                                $flat, // optional: searchable by flat
                                                $sub->subscription_id,
                                                $order?->order_id,
                                            ]),
                                        ),
                                    );

                                    $currentRiderName = $order?->rider?->rider_name;
                                    $currentRiderId = $order?->rider_id;
                                    $rowKey = 'riderModal' . $i;
                                @endphp

                                <tr data-search="{{ $searchBlob }}">
                                    <td>{{ $i + 1 }}</td>

                                    {{-- Customer --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"
                                                @if ($addr) data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="{{ $tooltip }}" @endif>
                                                {{ $user->name ?? '—' }}
                                            </span>
                                            <small class="text-muted">ID: {{ $sub->subscription_id }}</small>
                                        </div>
                                    </td>

                                    <td>{{ $user->mobile_number ?? '—' }}</td>

                                    <td>
                                        {{ $sub->computed->days_left ?? '—' }}
                                    </td>
                                    <td>₹{{ $perDay }}</td>
                                    <td>
                                        @if ($todayDelivery)
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="badge bg-success"><i class="bi bi-check2-circle"></i>
                                                    Delivered</span>
                                                <small class="text-muted mt-1">
                                                    {{ $todayDelivery->delivery_time ?? '—' }}
                                                    @if ($todayDelivery->rider?->rider_name)
                                                        · {{ $todayDelivery->rider->rider_name }}
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i>
                                                Not Delivered</span>
                                        @endif
                                    </td>

                                    {{-- Assigned Rider (editable) --}}
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span id="riderLabel-{{ $order?->order_id }}">
                                                {{ $currentRiderName ?? 'Unassigned' }}
                                            </span>

                                            <!-- Edit icon -->
                                            <button class="btn btn-sm btn-light border" data-bs-toggle="modal"
                                                data-bs-target="#{{ $rowKey }}" title="Change rider">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>

                                        <!-- Per-row Rider Assign Modal -->
                                        <div class="modal fade" id="{{ $rowKey }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white">Assign Rider</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="assign-rider-form"
                                                            data-order-id="{{ $order?->order_id }}"
                                                            data-label-id="riderLabel-{{ $order?->order_id }}"
                                                            data-bs-dismiss-target="#{{ $rowKey }}">
                                                            @csrf
                                                            <div class="mb-3 text-start">
                                                                <label class="form-label fw-semibold">Select Rider</label>
                                                                <select name="rider_id" class="form-select" required>
                                                                    <option value="">-- Choose rider --</option>
                                                                    @foreach ($riders as $r)
                                                                        <option value="{{ $r->rider_id }}"
                                                                            @selected($currentRiderId === $r->rider_id)>
                                                                            {{ $r->rider_name }} ({{ $r->rider_id }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button type="button" class="btn btn-light"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="bi bi-save"></i> Save
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Address --}}
                                    <td>
                                        @if ($addr)
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addr{{ $i }}">View</button>

                                            <div class="modal fade" id="addr{{ $i }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary">
                                                            <h5 class="modal-title text-white">Address Details</h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="list-group">
                                                                <li class="list-group-item"><strong>Apartment:</strong>
                                                                    {{ $addr->apartment_name ?? '—' }}</li>
                                                                <li class="list-group-item"><strong>Flat/Plot:</strong>
                                                                    {{ $addr->apartment_flat_plot ?? '—' }}</li>
                                                                <li class="list-group-item"><strong>Landmark:</strong>
                                                                    {{ $addr->landmark ?? '—' }}</li>
                                                                <li class="list-group-item"><strong>Area:</strong>
                                                                    {{ $addr->area ?? '—' }}</li>
                                                                <li class="list-group-item"><strong>City:</strong>
                                                                    {{ $addr->city ?? '—' }}</li>
                                                                <li class="list-group-item"><strong>State:</strong>
                                                                    {{ $addr->state ?? '—' }}</li>
                                                                <li class="list-group-item"><strong>Pincode:</strong>
                                                                    {{ $addr->pincode ?? '—' }}</li>
                                                                @if ($addr->localityDetails)
                                                                    <li class="list-group-item"><strong>Locality:</strong>
                                                                        {{ $addr->localityDetails->locality_name }}
                                                                        ({{ $addr->localityDetails->unique_code }})
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">No Address</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    @if ($activeSubscriptions->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inboxes" style="font-size:28px"></i>
                            <div class="mt-2">No active subscriptions found.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Quick search
            const q = document.getElementById('quickSearch');
            if (q) {
                q.addEventListener('input', () => {
                    const term = q.value.trim().toLowerCase();
                    document.querySelectorAll('#subsTable tbody tr').forEach(r => {
                        const hay = r.getAttribute('data-search') || '';
                        r.style.display = hay.includes(term) ? '' : 'none';
                    });
                });
            }

            // Bootstrap tooltips
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

                // Cleanup any orphaned backdrops/classes on first paint
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            });

            // CSRF
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Helper: ensure no leftover backdrops
            function cleanupBackdrops() {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            }

            // Per-row assign form (AJAX)
            document.addEventListener('submit', async (ev) => {
                const form = ev.target.closest('.assign-rider-form');
                if (!form) return;
                ev.preventDefault();

                const orderId = form.getAttribute('data-order-id'); // this is order.order_id
                const labelId = form.getAttribute('data-label-id');
                const modalSel = form.getAttribute('data-bs-dismiss-target'); // e.g. "#riderModal3"

                const formData = new FormData(form);
                if (!formData.get('rider_id')) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Select a rider',
                        text: 'Please choose a rider before saving.'
                    });
                }

                const btn = form.querySelector('button[type="submit"]');
                const prev = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';
                }

                try {
                    const url = `{{ route('orders.assignRider', ['order' => '___OID___']) }}`
                        .replace('___OID___', encodeURIComponent(orderId));

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        if (res.status === 422 && data.errors) {
                            const first = Object.values(data.errors)[0]?.[0] || 'Validation error.';
                            throw new Error(first);
                        }
                        throw new Error(data.message || 'Failed to assign rider.');
                    }

                    // Update label
                    const label = document.getElementById(labelId);
                    if (label) label.textContent = data.rider_name || 'Unassigned';

                    // Close modal gracefully, then show SweetAlert after the modal is fully hidden
                    const modalEl = modalSel ? document.querySelector(modalSel) : null;
                    if (modalEl) {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        // One-time listener
                        const onHidden = async () => {
                            modalEl.removeEventListener('hidden.bs.modal', onHidden);
                            cleanupBackdrops();
                            await Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: data.message || 'Rider assigned successfully.',
                                timer: 1400,
                                showConfirmButton: false,
                                didClose: cleanupBackdrops,
                                willClose: cleanupBackdrops
                            });
                        };
                        modalEl.addEventListener('hidden.bs.modal', onHidden, {
                            once: true
                        });
                        bsModal.hide();
                    } else {
                        // No modal? Just notify and cleanup
                        await Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: data.message || 'Rider assigned successfully.',
                            timer: 1400,
                            showConfirmButton: false,
                            didClose: cleanupBackdrops,
                            willClose: cleanupBackdrops
                        });
                    }

                } catch (err) {
                    cleanupBackdrops();
                    Swal.fire({
                        icon: 'error',
                        title: 'Could not assign rider',
                        text: err.message || 'Unexpected error occurred.'
                    });
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = prev;
                    }
                }
            });
        </script>

        {{-- Optional: show flash messages (non-AJAX fallbacks) --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    didClose: () => {
                        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                    }
                });
            </script>
        @endif
        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    didClose: () => {
                        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                    }
                });
            </script>
        @endif
    @endsection
