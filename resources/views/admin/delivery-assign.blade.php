@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .breadcrumb-header {
            background: #0056b3;
            padding: 15px;
            border-radius: 10px;
            color: #fff;
        }

        .table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
        }

        .table thead {
            background: #003366;
            color: #fff;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge-active {
            background-color: #007bff !important;
            color: #fff;
        }

        .badge-paused {
            background-color: #ffc107 !important;
            color: #000;
        }

        .badge-resume {
            background-color: #17a2b8 !important;
            color: #fff;
        }

        .badge-inactive {
            background-color: #6c757d !important;
            color: #fff;
        }

        .card {
            border: none;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
        }

        .btn-manage {
            background: #007bff;
            color: #fff;
            border-radius: 5px;
        }

        .btn-manage:hover {
            background: #0056b3;
        }

        .chip {
            display: inline-block;
            border: 1px solid #e9ecf5;
            border-radius: 999px;
            padding: .35rem .7rem;
            font-weight: 600;
            background: #fff;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb Header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1 text-white">🚴 Rider Order Assignment</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ url('admin/manage-title') }}" class="btn btn-manage">
                        <i class="fas fa-tasks"></i> Manage Delivery Assign
                    </a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="breadcrumb-item active tx-15" aria-current="page"><i class="fas fa-truck"></i> Delivery Assign
                </li>
            </ol>
        </div>
    </div>

    <!-- Rider Details -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 text-primary">{{ $rider->rider_name }}</h4>
                        <p class="mb-0 text-muted"><i class="fas fa-phone"></i> {{ $rider->phone_number }}</p>
                    </div>
                    <div>
                        <span class="chip">Assigned Orders: {{ $orders->count() }}</span>
                        <span class="chip">Today’s Deliveries: {{ $deliveryHistory->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

    <!-- Orders Currently Assigned -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3"><i class="fas fa-list"></i> Assigned Orders</h5>
                @if ($orders->isEmpty())
                    <div class="text-muted">No active/paused/resume orders assigned to this rider.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>User</th>
                                    <th>Phone</th>
                                    <th>Product</th>
                                    <th>Delivery Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $idx => $order)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->user->mobile_number ?? 'N/A' }}</td>
                                        <td>{{ $order->flowerProduct->name ?? 'N/A' }}</td>
                
                                        <td>{{ $order->delivery_time }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-address-btn"
                                                data-bs-toggle="modal" data-bs-target="#addressModal"
                                                data-order-id="{{ $order->order_id }}"
                                                data-user="{{ $order->user->name ?? 'N/A' }}"
                                                data-phone="{{ $order->user->mobile_number ?? 'N/A' }}"
                                                data-address="{{ $addressMap[$order->order_id] ?? 'Address not available' }}">
                                                <i class="fas fa-map-marker-alt"></i> Address
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Today's Delivery History -->
    <div class="row mt-4">
        <div class="col-12">
            @if ($deliveryHistory->isNotEmpty())
                <div class="card p-3 shadow-sm">
                    <h5 class="mb-3"><i class="fas fa-calendar-day"></i> Today's Delivery History</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryHistory as $index => $history)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $history->order->order_id ?? 'N/A' }}</td>
                                        <td>{{ $history->order->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <span
                                                class="badge
                                                @if ($history->delivery_status == 'delivered') bg-success
                                                @elseif ($history->delivery_status == 'pending') bg-warning text-dark
                                                @else bg-secondary @endif">
                                                {{ ucfirst($history->delivery_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $history->delivery_time }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-muted mt-3 text-center">
                    <i class="fas fa-info-circle"></i> No deliveries made today.
                </div>
            @endif
        </div>
    </div>

    <!-- Transfer Order -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <h5 class="text-primary mb-3"><i class="fas fa-exchange-alt"></i> Transfer Order to Another Rider</h5>
                <form action="{{ route('admin.transferOrder') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Select Order(s)</label>
                            <select class="form-control select2" name="order_ids[]" multiple="multiple" required>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->order_id }}">
                                        {{ $order->order_id }} — {{ $order->user->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Select New Rider</label>
                            <select class="form-control" name="new_rider_id" required>
                                <option value="">-- Select Rider --</option>
                                @foreach ($allRiders as $r)
                                    <option value="{{ $r->rider_id }}">{{ $r->rider_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-exchange-alt"></i> Transfer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addressModalLabel"><i class="fas fa-map-marker-alt"></i> Delivery Address
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong>Order:</strong> <span id="am-order"></span></div>
                    <div class="mb-2"><strong>User:</strong> <span id="am-user"></span></div>
                    <div class="mb-2"><strong>Phone:</strong> <span id="am-phone"></span></div>
                    <hr>
                    <div><strong>Address:</strong></div>
                    <div id="am-address" class="mt-1"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- jQuery (if not already in layout) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    {{-- Bootstrap JS (if not already in layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2').select2({
                width: '100%'
            });
        });

        // Address modal population
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-address-btn');
            if (!btn) return;
            document.getElementById('am-order').textContent = btn.getAttribute('data-order-id') || 'N/A';
            document.getElementById('am-user').textContent = btn.getAttribute('data-user') || 'N/A';
            document.getElementById('am-phone').textContent = btn.getAttribute('data-phone') || 'N/A';
            document.getElementById('am-address').textContent = btn.getAttribute('data-address') ||
                'Address not available';
        }, false);
    </script>
@endsection
