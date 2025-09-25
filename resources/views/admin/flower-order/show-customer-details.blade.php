@extends('admin.layouts.apps')

@section('styles')
    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- SmartPhoto -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">

    <style>
        /* ===== Pretty, colorful profile header ===== */
        .profile-hero {
            background: linear-gradient(135deg, #f3f4ff 0%, #e9fbff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 22px rgba(25, 42, 70, 0.08);
        }

        .profile-image {
            position: relative;
            display: inline-block;
            border-radius: 18px;
            padding: 6px;
            background: linear-gradient(135deg, #7c4dff, #00c2ff);
        }

        .profile-image img {
            width: 104px;
            height: 104px;
            object-fit: cover;
            border-radius: 14px;
            background: #fff;
            border: 4px solid #fff;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .12);
        }

        .profile-online {
            width: 12px;
            height: 12px;
            position: absolute;
            right: 10px;
            bottom: 10px;
            border: 2px solid #fff;
            border-radius: 50%;
        }

        .metric-card {
            border: 1px solid #eef1f7;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(75, 85, 99, .08);
            transition: transform .15s ease, box-shadow .2s ease;
            min-width: 140px;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(75, 85, 99, .15);
        }

        .metric-title {
            font-size: .8rem;
            letter-spacing: .04em;
            color: #64748b;
            text-transform: uppercase;
        }

        .metric-value {
            font-weight: 800;
            font-size: 1.35rem;
            color: #0f172a;
        }

        .badge-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .85rem;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        /* ===== Status pills ===== */
        .status-pill {
            padding: .38rem .65rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: .78rem;
            letter-spacing: .02em;
        }

        .status-running {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #065f46;
        }

        .status-paused {
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
        }

        .status-expired {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .status-pending {
            background: #e0e7ff;
            border: 1px solid #c7d2fe;
            color: #3730a3;
        }

        /* ===== Tabs ===== */
        .profile-nav-line .nav-link {
            border: 1px solid transparent;
            border-radius: 999px;
            margin-right: .5rem;
            padding: .55rem .95rem;
            font-weight: 700;
            color: #111827;
        }

        .profile-nav-line .nav-link.active {
            background: linear-gradient(135deg, #7c4dff, #00c2ff);
            color: #fff !important;
            border-color: transparent;
            box-shadow: 0 6px 16px rgba(17, 24, 39, .18);
        }

        /* ===== Tables ===== */
        table.table th {
            white-space: nowrap;
            background: #f8fafc;
            font-weight: 800;
        }

        table.table td {
            vertical-align: middle;
        }

        .custom-size-icon {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .addr-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
        }

        .addr-card .badge {
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PROFILE</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </div>
    </div>

    <!-- Profile Header -->
    <div class="row">
        <div class="col-12">
            <div class="profile-hero d-md-flex align-items-center">
                <div class="me-3 pos-relative">
                    <span class="profile-image">
                        <a href="{{ asset($user->userphoto ? 'storage/' . $user->userphoto : 'front-assets/img/images.jfif') }}"
                            class="js-smartPhoto" data-caption="{{ $user->name }}">
                            <img alt="{{ $user->name }}"
                                src="{{ asset($user->userphoto ? 'storage/' . $user->userphoto : 'front-assets/img/images.jfif') }}">
                        </a>
                        <span class="bg-success text-white profile-online"></span>
                    </span>
                </div>

                <div class="my-md-auto mt-4 me-3">
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>

                    <div class="d-flex flex-column gap-1 text-muted">
                        <span><i class="fa fa-phone me-2"></i><strong>Phone:</strong> {{ $user->mobile_number }}</span>
                        <span><i class="fa fa-envelope me-2"></i><strong>Email:</strong> {{ $user->email }}</span>
                        <span><i class="fa fa-venus-mars me-2"></i><strong>Gender:</strong> {{ $user->gender }}</span>
                        <span><i class="fa fa-birthday-cake me-2"></i><strong>DOB:</strong> {{ $user->dob }}</span>
                        <span><i class="fa fa-calendar-alt me-2"></i><strong>Joined:</strong>
                            {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M j, Y') : 'NA' }}</span>
                        <span>
                            <i class="fa fa-calendar-check me-2"></i>
                            <strong>First Subscription Start:</strong>
                            {{ $orders->count()
                                ? (optional($orders->sortBy('start_date')->first())->start_date
                                    ? \Carbon\Carbon::parse($orders->sortBy('start_date')->first()->start_date)->format('M j, Y')
                                    : 'NA')
                                : 'NA' }}
                        </span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 ms-auto">
                    <div class="metric-card p-3 text-center">
                        <i class="fa fa-shopping-cart text-primary custom-size-icon"></i>
                        <div class="metric-title">Total Orders</div>
                        <div class="metric-value">{{ $totalOrders }}</div>
                    </div>
                    <div class="metric-card p-3 text-center">
                        <i class="fa fa-hourglass-half text-warning custom-size-icon"></i>
                        <div class="metric-title">Running</div>
                        <div class="metric-value">{{ $ongoingOrders }}</div>
                    </div>
                    <div class="metric-card p-3 text-center">
                        <i class="fa fa-rupee-sign text-success custom-size-icon"></i>
                        <div class="metric-title">Total Spend</div>
                        <div class="metric-value">₹{{ number_format($totalSpend, 2) }}</div>
                    </div>
                    <div class="metric-card p-3 text-center">
                        <i class="fa fa-user-friends text-info custom-size-icon"></i>
                        <div class="metric-title">Total Refer</div>
                        <div class="metric-value">{{ $totalRefer ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="card custom-card mt-3">
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-3 profile-nav-line border-0 br-5 mb-0">
                            <a class="nav-link active" data-bs-toggle="tab" href="#order">
                                Subscription Orders
                                <span class="badge bg-dark ms-2">{{ $orders->count() }}</span>
                            </a>
                            <a class="nav-link" data-bs-toggle="tab" href="#requestorder">
                                Customize Orders
                                <span class="badge bg-dark ms-2">{{ $pendingRequests->count() }}</span>
                            </a>
                            <a class="nav-link" data-bs-toggle="tab" href="#address">
                                Addresses
                                <span class="badge bg-dark ms-2">{{ $addressdata->count() }}</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === Tabs === --}}
    <div class="row row-sm">
        <div class="col-12">
            <div class="custom-card main-content-body-profile">
                <div class="tab-content">

                    {{-- Subscription Orders --}}
                    <div class="main-content-body tab-pane border-top-0 active" id="order">
                        <div class="border-0 p-3">
                            <div class="table-responsive">
                                <table id="subsTable" class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Purchase Date</th>
                                            <th>Start Date</th>
                                            <th>Product Details</th>
                                            <th>Payment Mode</th>
                                            <th>Payment Date</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            @php
                                                $start = $order->start_date
                                                    ? \Carbon\Carbon::parse($order->start_date)
                                                    : null;
                                                $endBase = $order->new_date ?: $order->end_date;
                                                $end = $endBase ? \Carbon\Carbon::parse($endBase) : null;
                                                $purchase = $order->created_at
                                                    ? \Carbon\Carbon::parse($order->created_at)
                                                    : null;

                                                // Prefer the latest *paid* payment; if none, fall back to most recent payment record (any status).
                                                $latestPaid = $order->flowerPayments
                                                    ? $order->flowerPayments
                                                        ->where('payment_status', 'paid')
                                                        ->sortByDesc('created_at')
                                                        ->first()
                                                    : null;
                                                if (!$latestPaid && isset($order->flowerPayments)) {
                                                    $latestPaid = $order->flowerPayments
                                                        ->sortByDesc('created_at')
                                                        ->first();
                                                }
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">{{ $order->order_id }}</td>
                                                <td>{{ $purchase ? $purchase->format('M j, Y') : 'NA' }}</td>
                                                <td>{{ $start ? $start->format('M j, Y') : 'NA' }}</td>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ optional($order->flowerProducts)->name ?? 'NA' }}</div>
                                                    @if ($start && $end)
                                                        <small class="text-muted">({{ $start->format('F j, Y') }} –
                                                            {{ $end->format('F j, Y') }})</small>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $latestPaid && $latestPaid->payment_method
                                                        ? ucwords(str_replace('_', ' ', $latestPaid->payment_method))
                                                        : 'Unpaid' }}
                                                </td>
                                                <td>
                                                    {{ $latestPaid && $latestPaid->created_at ? $latestPaid->created_at->format('M j, Y') : 'Unpaid' }}
                                                </td>

                                                <td>₹{{ number_format(optional($order->order)->total_price ?? 0, 2) }}</td>

                                                <td>
                                                    @php
                                                        $status = strtolower($order->status ?? '');
                                                        $pill = match ($status) {
                                                            'active' => 'status-running',
                                                            'paused' => 'status-paused',
                                                            'expired' => 'status-expired',
                                                            'pending' => 'status-pending',
                                                            default => 'status-pending',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="status-pill {{ $pill }}">{{ ucfirst($order->status ?? 'NA') }}</span>
                                                </td>

                                                <td>
                                                    @if (isset($order->id))
                                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            View Details
                                                        </a>
                                                    @else
                                                        <span class="text-muted">NA</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Customize Orders (with garland details + price breakdown) --}}
                    <div class="main-content-body tab-pane border-top-0" id="requestorder">
                        <div class="border-0 p-3">
                            <div class="table-responsive">
                                <table id="reqTable" class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Request</th>
                                            <th>Delivery Date</th>
                                            <th>Items (Flowers / Garlands)</th>
                                            <th class="text-end">Flower Price</th>
                                            <th class="text-end">Delivery Charge</th>
                                            <th class="text-end">Total Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingRequests as $rq)
                                            @php
                                                $rqDate = $rq->date
                                                    ? \Carbon\Carbon::parse($rq->date)->format('M j, Y')
                                                    : 'NA';
                                                $rqStatus = strtolower($rq->status ?? 'pending');
                                                $rqPill = match ($rqStatus) {
                                                    'pending' => 'status-pending',
                                                    'approved' => 'status-running',
                                                    'paid' => 'status-running',
                                                    'cancelled' => 'status-expired',
                                                    default => 'status-pending',
                                                };

                                                // If an order was attached in the controller, show its financials
                                                $rqOrder = $rq->order ?? null;
                                                $reqFlowerPrice = $rqOrder?->requested_flower_price ?? 0;
                                                $deliveryCharge = $rqOrder?->delivery_charge ?? 0;
                                                $totalAmount = $rqOrder?->total_price ?? 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">#{{ $rq->request_id }}</div>
                                                    <small class="text-muted d-block mt-1">
                                                        {{ optional($rq->user)->name }} —
                                                        {{ optional($rq->user)->mobile_number }}
                                                    </small>
                                                    @if ($rq->flowerProduct)
                                                        <small class="text-muted d-block">Product:
                                                            {{ $rq->flowerProduct->name }}</small>
                                                    @endif
                                                </td>

                                                <td>{{ $rqDate }}</td>

                                                <td>
                                                    @php
                                                        $hasItems =
                                                            $rq->flowerRequestItems && $rq->flowerRequestItems->count();
                                                    @endphp
                                                    @if ($hasItems)
                                                        <ul class="mb-0 ps-3">
                                                            @foreach ($rq->flowerRequestItems as $item)
                                                                @if (strtolower($item->type ?? '') === 'garland')
                                                                    <li>
                                                                        <strong>Garland:</strong>
                                                                        {{ $item->garland_name ?? '—' }}
                                                                        @if ($item->garland_size)
                                                                            — Size: {{ $item->garland_size }}
                                                                        @endif
                                                                        @if ($item->garland_quantity)
                                                                            — Qty: {{ $item->garland_quantity }}
                                                                        @endif
                                                                        @if ($item->flower_count)
                                                                            — Flower Count: {{ $item->flower_count }}
                                                                        @endif
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <strong>Flower:</strong>
                                                                        {{ $item->flower_name ?? '—' }}
                                                                        @if ($item->flower_quantity || $item->flower_unit)
                                                                            — {{ $item->flower_quantity ?? '' }}
                                                                            {{ $item->flower_unit ?? '' }}
                                                                        @endif
                                                                        @if ($item->size)
                                                                            — Size: {{ $item->size }}
                                                                        @endif
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">No items</span>
                                                    @endif
                                                </td>

                                                <td class="text-end">₹{{ number_format($reqFlowerPrice, 2) }}</td>
                                                <td class="text-end">₹{{ number_format($deliveryCharge, 2) }}</td>
                                                <td class="text-end fw-bold">₹{{ number_format($totalAmount, 2) }}</td>

                                                <td>
                                                    <span
                                                        class="status-pill {{ $rqPill }}">{{ ucfirst($rq->status ?? 'Pending') }}</span>
                                                    <div class="small text-muted mt-1">
                                                        @if ($rq->status == 'pending')
                                                            Order placed — update price
                                                        @elseif ($rq->status == 'approved')
                                                            Payment pending
                                                        @elseif ($rq->status == 'paid')
                                                            Payment completed
                                                        @endif
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Addresses --}}
                    <div class="main-content-body tab-pane border-top-0" id="address">
                        <div class="border-0 p-3">
                            <div class="row g-3">
                                @forelse ($addressdata as $address)
                                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                        <div class="card addr-card h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-bold">{{ $address->address_type ?? 'Address' }}
                                                    </h6>
                                                    @if ($address->default == 1)
                                                        <span class="badge bg-success">Default</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    <div><strong>Address:</strong>
                                                        {{ $address->apartment_flat_plot ?? '' }}</div>
                                                    <div><strong>Apartment:</strong> {{ $address->apartment_name ?? '' }}
                                                    </div>
                                                    <div><strong>Locality:</strong>
                                                        {{ optional($address->localityDetails)->locality_name ?? '' }}
                                                    </div>
                                                    <div><strong>Landmark:</strong> {{ $address->landmark ?? '' }}</div>
                                                    <div><strong>City:</strong> {{ $address->city ?? '' }}</div>
                                                    <div><strong>State:</strong> {{ $address->state ?? '' }}</div>
                                                    <div><strong>PIN:</strong> {{ $address->pincode ?? '' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">No active addresses found.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- About (kept, hidden by default) --}}
                    <div class="main-content-body tab-pane border-top-0" id="about">
                        <div class="card">
                            <div class="card-body p-0 border-0 rounded-10">
                                <div class="p-4">
                                    <h4 class="tx-15 text-uppercase mb-3">About</h4>
                                    <p class="m-b-5">{{ $user->about ?? 'No additional information available.' }}</p>
                                </div>
                                <div class="border-top"></div>
                            </div>
                        </div>
                    </div>

                </div> {{-- tab-content --}}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <!-- SmartPhoto -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>

    <script>
        // Init SmartPhoto for profile image
        document.querySelectorAll('.js-smartPhoto').forEach(function(el) {
            new SmartPhoto(el, {
                showAnimation: false
            });
        });

        // Initialize DataTables if present
        (function() {
            if (window.jQuery && $.fn.DataTable) {
                $('#subsTable').DataTable({
                    pageLength: 10,
                    order: [
                        [1, 'desc']
                    ],
                    autoWidth: false,
                    responsive: true
                });
                $('#reqTable').DataTable({
                    pageLength: 10,
                    order: [
                        [1, 'desc']
                    ],
                    autoWidth: false,
                    responsive: true
                });
            }
        })();
    </script>
@endsection
