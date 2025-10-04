@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                <table class="table table-striped table-bordered align-middle" id="subsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Subscription</th>
                            <th>Product</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Days Left</th>
                            <th>₹/Day</th>
                            <th>Today Delivery</th> {{-- NEW COLUMN --}}
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

                                $daysLeft = $sub->computed->days_left ?? null;
                                $perDay =
                                    $sub->computed->per_day !== null ? number_format($sub->computed->per_day, 2) : '—';

                                // today's delivery (thanks to constrained eager-load)
$todayDelivery = $sub->computed->todays_delivery ?? null;

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
                                            $sub->subscription_id,
                                            $order?->order_id,
                                        ]),
                                    ),
                                );
                            @endphp

                            <tr data-search="{{ $searchBlob }}">
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $user->name ?? '—' }}</span>
                                        <small class="text-muted">ID: {{ $sub->subscription_id }}</small>
                                    </div>
                                </td>
                                <td>{{ $user->mobile_number ?? '—' }}</td>
                                <td><span class="badge-soft">{{ ucfirst($sub->status) }}</span></td>
                                <td>
                                    <div class="product-mini">
                                        @if ($prod?->product_image_url)
                                            <img src="{{ $prod->product_image_url }}" alt="product">
                                        @endif
                                        <div class="text-start">
                                            <div class="fw-semibold">{{ $prod->name ?? '—' }}</div>
                                            @if ($order?->total_price)
                                                <small class="text-muted">Pkg:
                                                    ₹{{ number_format($order->total_price, 2) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $sub->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('d M Y') : '—' }}
                                </td>
                                <td>{{ $sub->end_date ? \Carbon\Carbon::parse($sub->end_date)->format('d M Y') : '—' }}
                                </td>
                                <td>
                                    @if ($daysLeft !== null)
                                        <span
                                            class="fw-bold {{ $daysLeft <= 3 ? 'text-danger' : 'text-success' }}">{{ $daysLeft }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>₹{{ $perDay }}</td>

                                {{-- NEW CELL: Today Delivery --}}
                                <td>
                                    @if ($todayDelivery)
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-success"><i class="bi bi-check2-circle"></i>
                                                Delivered</span>
                                            <small class="text-muted mt-1">
                                                {{ $todayDelivery->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                                @if ($todayDelivery->rider?->rider_name)
                                                    · {{ $todayDelivery->rider->rider_name }}
                                                @endif
                                            </small>

                                            <button class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal"
                                                data-bs-target="#delv{{ $i }}">
                                                Details
                                            </button>
                                        </div>

                                        <!-- Delivery Detail Modal -->
                                        <div class="modal fade" id="delv{{ $i }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white">Today's Delivery</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul class="list-group">
                                                            <li class="list-group-item"><strong>Status:</strong>
                                                                {{ ucfirst($todayDelivery->delivery_status) }}</li>
                                                            <li class="list-group-item"><strong>Delivered At:</strong>
                                                                {{ $todayDelivery->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}
                                                            </li>
                                                            <li class="list-group-item"><strong>Rider:</strong>
                                                                {{ $todayDelivery->rider->rider_name ?? '—' }}</li>
                                                            @if (!empty($todayDelivery->notes))
                                                                <li class="list-group-item"><strong>Notes:</strong>
                                                                    {{ $todayDelivery->notes }}</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Pending
                                            / Not Delivered</span>
                                    @endif
                                </td>

                                {{-- Address --}}
                                <td>
                                    @if ($addr)
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addr{{ $i }}">View</button>

                                        <!-- Address Modal -->
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
                                                                    ({{ $addr->localityDetails->unique_code }})</li>
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
    <script>
        // simple client-side search
        const q = document.getElementById('quickSearch');
        q.addEventListener('input', filterRows);

        function filterRows() {
            const term = q.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#subsTable tbody tr');
            rows.forEach(r => {
                const hay = r.getAttribute('data-search') || '';
                r.style.display = hay.includes(term) ? '' : 'none';
            });
        }
    </script>
@endsection
