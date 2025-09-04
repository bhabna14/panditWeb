@extends('admin.layouts.apps')

@section('styles')
    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- SmartPhoto -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">

    <style>
        :root {
            --pf-bg: #ffffff;
            --pf-surface: #f8fafc;
            --pf-border: #e5e7eb;
            --pf-primary: #4f46e5;
            /* indigo */
            --pf-primary-weak: #eef2ff;
            --pf-accent: #06b6d4;
            /* cyan */
            --pf-danger: #ef4444;
            --pf-success: #16a34a;
            --pf-warning: #f59e0b;
            --pf-text: #0f172a;
            --pf-muted: #64748b;
            --pf-shadow: 0 8px 30px rgba(2, 6, 23, .06);
        }

        /* ===== Page Scaffolding ===== */
        body {
            background: var(--pf-bg);
        }

        .page-card {
            border: 1px solid var(--pf-border);
            border-radius: 16px;
            background: var(--pf-bg);
            box-shadow: var(--pf-shadow);
        }

        /* ===== Profile Header ===== */
        .profile-hero {
            background: linear-gradient(135deg, #f3f4ff 0%, #e9fbff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--pf-shadow);
        }

        .profile-image img {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: 12px;
            border: 4px solid #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .profile-online {
            width: 12px;
            height: 12px;
            position: absolute;
            right: 6px;
            bottom: 6px;
            border: 2px solid #fff;
            border-radius: 999px;
        }

        .custom-size-icon {
            font-size: 26px;
            margin-bottom: 10px;
        }

        /* ===== Metric Cards ===== */
        .metric-card {
            border: 1px solid #eef1f7;
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--pf-shadow);
            transition: transform .15s ease, box-shadow .2s ease;
            min-width: 180px;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 42px rgba(2, 6, 23, .12);
        }

        .metric-title {
            font-size: .85rem;
            letter-spacing: .02em;
            color: var(--pf-muted);
            text-transform: uppercase;
        }

        .metric-value {
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--pf-text);
        }

        /* ===== Segmented Tabs (New) ===== */
        .seg-tabs-wrap {
            position: sticky;
            top: 68px;
            z-index: 6;
            background: var(--pf-bg);
            border-bottom: 1px solid var(--pf-border);
        }

        .seg-tabs {
            display: flex;
            gap: .5rem;
            overflow-x: auto;
            padding: .75rem;
            scrollbar-width: thin;
        }

        .seg-tab {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .55rem .9rem;
            border: 1px solid var(--pf-border);
            border-radius: 999px;
            background: #fff;
            color: #111827;
            text-decoration: none;
            font-weight: 600;
            white-space: nowrap;
            transition: background .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .seg-tab .count {
            background: var(--pf-surface);
            border: 1px solid var(--pf-border);
            padding: 0 .5rem;
            border-radius: 999px;
            font-size: .75rem;
        }

        .seg-tab.active,
        .seg-tab[aria-selected="true"] {
            background: var(--pf-primary-weak);
            border-color: var(--pf-primary);
            color: #111827;
            box-shadow: 0 6px 16px rgba(79, 70, 229, .18);
        }

        .seg-tab .icon {
            font-size: 1rem;
        }

        /* ===== Tables ===== */
        .table-modern thead th {
            background: var(--pf-surface);
            border-bottom: 1px solid var(--pf-border);
            font-weight: 700;
        }

        .table-modern tbody td {
            vertical-align: middle;
        }

        .table-toolbar {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: .75rem;
        }

        .table-toolbar .form-control {
            max-width: 280px;
            border-radius: 10px;
            border-color: var(--pf-border);
        }

        /* ===== Status Pills ===== */
        .status-pill {
            padding: .35rem .6rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .78rem;
            color: #111827;
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

        /* ===== Address Cards ===== */
        .addr-card {
            border: 1px solid var(--pf-border);
            border-radius: 14px;
        }

        .addr-card .badge {
            font-weight: 700;
        }

        /* Minor tweaks */
        .text-muted-2 {
            color: var(--pf-muted);
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .85rem;
            border: 1px solid var(--pf-border);
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
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
                    <span class="profile-image pos-relative">
                        <a href="{{ asset($user->userphoto ? 'storage/' . $user->userphoto : 'front-assets/img/images.jfif') }}"
                            class="js-smartPhoto" data-caption="{{ $user->name }}">
                            <img class="br-5" alt="{{ $user->name }}"
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
                        <span><i class="fa fa-calendar-alt me-2"></i><strong>Joined:</strong>
                            {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M j, Y') : 'NA' }}</span>
                        <span><i class="fa fa-calendar-check me-2"></i><strong>First Subscription Start:</strong>
                            {{ $orders->count()
                                ? (optional($orders->sortBy('start_date')->first())->start_date
                                    ? \Carbon\Carbon::parse($orders->sortBy('start_date')->first()->start_date)->format('M j, Y')
                                    : 'NA')
                                : 'NA' }}</span>
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
                        <div class="metric-title">Ongoing Subscriptions</div>
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

            <!-- Segmented Tabs (new look) -->
            <div class="seg-tabs-wrap page-card mt-3">
                <div class="seg-tabs" role="tablist" aria-label="Profile sections">
                    <a class="seg-tab active" id="tab-orders" data-bs-toggle="tab" href="#pane-orders" role="tab"
                        aria-controls="pane-orders" aria-selected="true">
                        <i class="icon fa fa-list"></i> Subscription Orders
                        <span class="count">{{ $orders->count() }}</span>
                    </a>
                    <a class="seg-tab" id="tab-requests" data-bs-toggle="tab" href="#pane-requests" role="tab"
                        aria-controls="pane-requests" aria-selected="false">
                        <i class="icon fa fa-tools"></i> Customize Orders
                        <span class="count">{{ $pendingRequests->count() }}</span>
                    </a>
                    <a class="seg-tab" id="tab-addresses" data-bs-toggle="tab" href="#pane-addresses" role="tab"
                        aria-controls="pane-addresses" aria-selected="false">
                        <i class="icon fa fa-map-marker-alt"></i> Addresses
                        <span class="count">{{ $addressdata->count() }}</span>
                    </a>
                    <a class="seg-tab" id="tab-about" data-bs-toggle="tab" href="#pane-about" role="tab"
                        aria-controls="pane-about" aria-selected="false">
                        <i class="icon fa fa-user"></i> About
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Panes ===== --}}
    <div class="row row-sm mt-3">
        <div class="col-12">
            <div class="tab-content">

                {{-- Orders Pane --}}
                <div class="tab-pane fade show active" id="pane-orders" role="tabpanel" aria-labelledby="tab-orders">
                    <div class="page-card p-3 mt-2">
                        <div class="table-toolbar">
                            <input id="searchSubs" type="text" class="form-control"
                                placeholder="Search subscriptions..." aria-label="Search subscriptions">
                        </div>
                        <div class="table-responsive">
                            <table id="subsTable" class="table table-bordered table-hover align-middle table-modern">
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
                                        <th style="min-width:120px;">Actions</th>
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

                                            $latestPaid = $order->latestPaidPayment ?? null;
                                            if (!$latestPaid && isset($order->flowerPayments)) {
                                                $latestPaid = $order->flowerPayments->sortByDesc('created_at')->first();
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
                                                    <small class="text-muted-2">({{ $start->format('F j, Y') }} –
                                                        {{ $end->format('F j, Y') }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $latestPaid && $latestPaid->payment_method
                                                    ? ucwords(str_replace('_', ' ', $latestPaid->payment_method))
                                                    : 'NA' }}
                                            </td>
                                            <td>
                                                {{ $latestPaid && $latestPaid->created_at ? $latestPaid->created_at->format('M j, Y') : 'NA' }}
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
                                                        default => 'badge bg-secondary',
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
                            @if (!$orders->count())
                                <div class="alert alert-info mb-0">No subscription orders yet.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Customize / Request Orders Pane --}}
                <div class="tab-pane fade" id="pane-requests" role="tabpanel" aria-labelledby="tab-requests">
                    <div class="page-card p-3 mt-2">
                        <div class="table-toolbar">
                            <input id="searchReq" type="text" class="form-control"
                                placeholder="Search custom requests..." aria-label="Search custom requests">
                        </div>
                        <div class="table-responsive">
                            <table id="reqTable" class="table table-bordered table-hover align-middle table-modern">
                                <thead>
                                    <tr>
                                        <th>Request</th>
                                        <th>Delivery Date</th>
                                        <th>Flower Items</th>
                                        <th>Status</th>
                                        <th style="min-width:120px;">Actions</th>
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
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">#{{ $rq->request_id }}</div>
                                                <small class="text-muted-2 d-block mt-1">
                                                    {{ optional($rq->user)->name }} —
                                                    {{ optional($rq->user)->mobile_number }}
                                                </small>
                                            </td>
                                            <td>{{ $rqDate }}</td>
                                            <td>
                                                @if ($rq->flowerRequestItems && $rq->flowerRequestItems->count())
                                                    <ul class="mb-0 ps-3">
                                                        @foreach ($rq->flowerRequestItems as $item)
                                                            <li>{{ $item->flower_name }} — {{ $item->flower_quantity }}
                                                                {{ $item->flower_unit }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No items</span>
                                                @endif
                                            </td>
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
                                            <td>
                                                <span class="chip"><i
                                                        class="fa fa-info-circle"></i>{{ strtoupper($rq->status ?? 'NA') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (!$pendingRequests->count())
                                <div class="alert alert-info mb-0">No customized requests found.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Addresses Pane --}}
                <div class="tab-pane fade" id="pane-addresses" role="tabpanel" aria-labelledby="tab-addresses">
                    <div class="page-card p-3 mt-2">
                        <div class="row g-3">
                            @forelse ($addressdata as $address)
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                    <div class="card addr-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 fw-bold">{{ $address->address_type ?? 'Address' }}</h6>
                                                @if ($address->default == 1)
                                                    <span class="badge bg-success">Default</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">
                                                <div><strong>Address:</strong> {{ $address->apartment_flat_plot ?? '' }}
                                                </div>
                                                <div><strong>Apartment:</strong> {{ $address->apartment_name ?? '' }}</div>
                                                <div><strong>Locality:</strong>
                                                    {{ optional($address->localityDetails)->locality_name ?? '' }}</div>
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

                {{-- About Pane --}}
                <div class="tab-pane fade" id="pane-about" role="tabpanel" aria-labelledby="tab-about">
                    <div class="page-card p-0 mt-2">
                        <div class="p-4">
                            <h4 class="tx-15 text-uppercase mb-3">About</h4>
                            <p class="m-b-5">{{ $user->about ?? 'No additional information available.' }}</p>
                        </div>
                        <div class="border-top"></div>
                    </div>
                </div>

            </div> {{-- tab-content --}}
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

        // Sync segmented-tabs 'active' class with Bootstrap tabs
        document.querySelectorAll('.seg-tab').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function() {
                document.querySelectorAll('.seg-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            });
        });

        // Defensive DataTables + search toolbars
        (function() {
            if (window.jQuery && $.fn.DataTable) {
                var subsDT = $('#subsTable').DataTable({
                    pageLength: 10,
                    order: [
                        [1, 'desc']
                    ],
                    autoWidth: false,
                    responsive: true
                });
                var reqDT = $('#reqTable').DataTable({
                    pageLength: 10,
                    order: [
                        [1, 'desc']
                    ],
                    autoWidth: false,
                    responsive: true
                });

                // Toolbar search bindings
                $('#searchSubs').on('keyup change', function() {
                    subsDT.search(this.value).draw();
                });
                $('#searchReq').on('keyup change', function() {
                    reqDT.search(this.value).draw();
                });
            }
        })();
    </script>
@endsection
