@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .badge {
            font-size: 0.8rem;
        }

        .table td,
        .table th {
            vertical-align: middle !important;
        }

        .action-btns .btn {
            margin: 2px 0;
        }

        /* Metric cards: no bg, light border, subtle hover */
        .metric-card {
            background-color: transparent !important;
            border: 1px solid rgb(186, 185, 185) !important;
            transition: box-shadow .2s ease, transform .2s ease, opacity .2s ease;
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .metric-card .card-body {
            padding: 1rem 1.25rem;
        }

        .metric-card .icon {
            font-size: 2rem;
            line-height: 1;
        }

        .metric-card .label {
            font-weight: 600;
        }

        .metric-card.opacity-90 {
            opacity: .85;
        }

        .card-filter:hover .metric-card {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    {{-- counters --}}
   <div class="row mb-4 mt-4">
    {{-- Total --}}
    <div class="col-md-3">
        <a href="{{ route('flower-request', ['filter' => 'all']) }}" class="card-filter text-decoration-none" data-filter="all">
            <div class="card metric-card h-100 {{ $filter === 'all' ? '' : 'opacity-90' }}" data-card="all">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fa fa-list icon text-warning"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1 label text-muted">Total Orders</h5>
                        <h3 class="mb-0 text-warning " id="totalCount">{{ $totalCustomizeOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Today --}}
    <div class="col-md-3">
        <a href="{{ route('flower-request', ['filter' => 'today']) }}" class="card-filter text-decoration-none" data-filter="today">
            <div class="card metric-card h-100 {{ $filter === 'today' ? '' : 'opacity-90' }}" data-card="today">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fa fa-calendar-day icon text-success"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1 label text-muted">Today's Orders</h5>
                        <h3 class="mb-0 text-success" id="todayCount">{{ $todayCustomizeOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Paid --}}
    <div class="col-md-3">
        <a href="{{ route('flower-request', ['filter' => 'paid']) }}" class="card-filter text-decoration-none" data-filter="paid">
            <div class="card metric-card h-100 {{ $filter === 'paid' ? '' : 'opacity-90' }}" data-card="paid">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fa fa-check-circle icon text-info"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1 label text-muted">Paid Orders</h5>
                        <h3 class="mb-0 text-info" id="paidCount">{{ $paidCustomizeOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Rejected --}}
    <div class="col-md-3">
        <a href="{{ route('flower-request', ['filter' => 'rejected']) }}" class="card-filter text-decoration-none" data-filter="rejected">
            <div class="card metric-card h-100 {{ $filter === 'rejected' ? '' : 'opacity-90' }}" data-card="rejected">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 ">
                        <i class="fa fa-ban icon text-primary"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1 label text-muted">Rejected Orders</h5>
                        <h3 class="mb-0 text-primary" id="rejectedCount">{{ $rejectCustomizeOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>


    {{-- table --}}
    <div class="card custom-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="file-datatable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th># / User</th>
                            <th>Purchase</th>
                            <th>Delivery</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Rider</th>
                            <th>Address</th>
                            <th>Cancel By</th>
                            <th>Cancel Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestsBody">
                        @include('admin.flower-request.partials._rows', [
                            'pendingRequests' => $pendingRequests,
                            'riders' => $riders,
                        ])
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- 

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session()->get('success') }}</div>
    @endif
    @if ($errors->has('danger'))
        <div class="alert alert-danger">{{ $errors->first('danger') }}</div>
    @endif

    <!-- Orders Table -->
    <div class="card custom-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="file-datatable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th># / User</th>
                            <th>Purchase</th>
                            <th>Delivery</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Rider</th>
                            <th>Address</th>
                            <th>Cancel By</th>
                            <th>Cancel Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRequests as $request)
                            <tr>
                                <!-- Request ID + User -->
                                <td>
                                    <strong>#{{ $request->request_id }}</strong><br>
                                    <small class="text-muted">{{ $request->user->name ?? 'N/A' }}</small><br>
                                    <small class="text-muted">{{ $request->user->mobile_number ?? 'N/A' }}</small>
                                </td>

                                <!-- Purchase Date -->
                                <td>{{ optional($request->created_at)->format('d-m-Y h:i A') ?? 'N/A' }}</td>

                                <!-- Delivery Date -->
                                <td>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }} {{ $request->time }}</td>

                                <!-- Items Column -->
                                <td>
                                    <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal"
                                        data-bs-target="#itemsModal{{ $request->id }}">
                                        View Items
                                    </button>

                                    <!-- Items Modal -->
                                    <div class="modal fade" id="itemsModal{{ $request->id }}" tabindex="-1"
                                        aria-labelledby="itemsModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title" id="itemsModalLabel{{ $request->id }}">
                                                        Order Items - #{{ $request->request_id }}
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if ($request->flowerRequestItems->count())
                                                        <ul class="list-group">
                                                            @foreach ($request->flowerRequestItems as $item)
                                                                @if ($item->type === 'garland')
                                                                    <li class="list-group-item">
                                                                        <strong>Garland:</strong>
                                                                        {{ $item->garland_name ?? 'N/A' }}<br>
                                                                        <small>Quantity:
                                                                            {{ $item->garland_quantity ?? 0 }}</small><br>
                                                                        @if ($item->garland_size)
                                                                            <small>Size: {{ $item->garland_size }}</small>
                                                                        @endif
                                                                    </li>
                                                                @else
                                                                    <li class="list-group-item">
                                                                        <strong>Flower:</strong>
                                                                        {{ $item->flower_name ?? 'N/A' }}<br>
                                                                        <small>Quantity: {{ $item->flower_quantity ?? 0 }}
                                                                            {{ $item->flower_unit ?? '' }}</small>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-muted">No items found.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    @switch($request->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @break

                                        @case('approved')
                                            <span class="badge bg-info">Approved</span>
                                        @break

                                        @case('paid')
                                            <span class="badge bg-success">Paid</span>
                                        @break

                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </td>

                                <!-- Price -->
                                <td>
                                    @if ($request->order && $request->order->total_price)
                                        <div><strong>₹{{ $request->order->total_price }}</strong></div>
                                        <small>Flower: ₹{{ $request->order->requested_flower_price }}</small><br>
                                        <small>Delivery: ₹{{ $request->order->delivery_charge }}</small>
                                    @else
                                        <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST">
                                            @csrf
                                            <input type="number" name="requested_flower_price" class="form-control mb-2"
                                                placeholder="Flower Price" required>
                                            <input type="number" name="delivery_charge" class="form-control mb-2"
                                                placeholder="Delivery Charge" required>
                                            <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                                        </form>
                                    @endif
                                </td>

                                <!-- Rider (only when Paid) -->
                                <td>
                                    @if ($request->status == 'paid' && $request->order && $request->order->total_price)
                                        @if ($request->order->rider_id)
                                            <span class="badge bg-primary">{{ $request->order->rider->rider_name }}</span>
                                            <a href="#" class="btn btn-sm btn-outline-info mt-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editRiderModal{{ $request->order->id }}">
                                                Edit Rider
                                            </a>
                                        @else
                                            <form action="{{ route('admin.orders.assignRider', $request->order->id) }}"
                                                method="POST">
                                                @csrf
                                                <select name="rider_id" class="form-select mb-2">
                                                    <option disabled selected>Choose Rider</option>
                                                    @foreach ($riders as $rider)
                                                        <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                    class="btn btn-sm btn-success w-100">Assign</button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>

                                <!-- Address -->
                                <td>
                                    <small>{{ $request->address->apartment_flat_plot ?? '' }},
                                        {{ $request->address->apartment_name ?? '' }},
                                        {{ $request->address->locality_name ?? '' }}</small><br>
                                    <small class="text-muted">{{ $request->address->city ?? '' }},
                                        {{ $request->address->state ?? '' }},
                                        {{ $request->address->pincode ?? '' }}</small><br>
                                    <small class="text-muted">Landmark: {{ $request->address->landmark ?? 'N/A' }}</small>
                                </td>

                                <!-- Cancel By -->
                                <td>
                                    @if ($request->cancel_by)
                                        <span class="badge bg-dark">{{ ucfirst($request->cancel_by) }}</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>

                                <!-- Cancel Reason -->
                                <td>
                                    @if ($request->cancel_reason)
                                        {{ $request->cancel_reason }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="action-btns">
                                    <form id="markPaymentForm_{{ $request->request_id }}"
                                        action="{{ route('admin.markPayment', $request->request_id) }}" method="POST">
                                        @csrf
                                        @if ($request->status == 'approved')
                                            <button type="button" class="btn btn-success btn-sm w-100"
                                                onclick="confirmPayment('{{ $request->request_id }}')">Mark Paid</button>
                                        @elseif($request->status == 'paid')
                                            <button type="button" class="btn btn-success btn-sm w-100"
                                                disabled>Paid</button>
                                        @endif
                                    </form>

                                    <!-- View Details -->
                                    <button class="btn btn-outline-dark btn-sm w-100 mt-2" data-bs-toggle="modal"
                                        data-bs-target="#detailsModal{{ $request->id }}">
                                        Details
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1"
                                        aria-labelledby="detailsModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">Request Details</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Suggestion:</strong> {{ $request->suggestion ?? 'None' }}
                                                    </p>
                                                    <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Re-init DataTable safely after we replace tbody
        function reinitDataTable() {
            if ($.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable().destroy();
            }
            $('#file-datatable').DataTable({
                // If you rely on table-data.js defaults, keep this empty so it uses defaults.
                // Or paste your preferred options here.
            });
        }

        function setActiveCard(filter) {
            // Add opacity to all, remove from active
            $('[data-card]').addClass('opacity-90');
            $('[data-card="' + filter + '"]').removeClass('opacity-90');
        }

        function updateCounts(counts) {
            if (typeof counts !== 'object') return;
            $('#totalCount').text(counts.total ?? 0);
            $('#todayCount').text(counts.today ?? 0);
            $('#paidCount').text(counts.paid ?? 0);
            $('#rejectedCount').text(counts.rejected ?? 0);
        }

        function loadRequests(filter, pushUrl = true) {
            const url = "{{ route('admin.flower-request.data') }}";
            // Optional tiny loading state
            const $tbody = $('#requestsBody');
            const prevHtml = $tbody.html();
            $tbody.html('<tr><td colspan="11" class="text-center py-5">Loading...</td></tr>');

            $.get(url, {
                    filter: filter
                })
                .done(function(res) {
                    if (res && res.rows_html !== undefined) {
                        $tbody.html(res.rows_html);
                        reinitDataTable();
                        updateCounts(res.counts || {});
                        setActiveCard(res.active || filter);
                        if (pushUrl) {
                            const pageUrl = new URL(window.location);
                            pageUrl.searchParams.set('filter', res.active || filter);
                            window.history.pushState({
                                filter: res.active || filter
                            }, '', pageUrl.toString());
                        }
                    } else {
                        $tbody.html(
                            '<tr><td colspan="11" class="text-center py-5 text-danger">Unexpected response</td></tr>'
                            );
                    }
                })
                .fail(function(xhr) {
                    $tbody.html(prevHtml);
                    Swal.fire('Error', 'Failed to load data. Please try again.', 'error');
                });
        }

        $(document).on('click', '.card-filter', function(e) {
            e.preventDefault();
            const filter = $(this).data('filter') || 'all';
            loadRequests(filter, true);
        });

        // Handle back/forward navigation to keep filter in sync
        window.addEventListener('popstate', function(event) {
            const params = new URLSearchParams(window.location.search);
            const filter = params.get('filter') || 'all';
            loadRequests(filter, false);
        });

        // If your DataTable is already initialized by table-data.js on load,
        // ensure it gets created once:
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#file-datatable')) {
                $('#file-datatable').DataTable();
            }
        });

        // Existing confirmPayment remains unchanged
        function confirmPayment(requestId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Mark this payment as Paid?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, mark as Paid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('markPaymentForm_' + requestId).submit();
                }
            });
        }
        window.confirmPayment = confirmPayment; // make accessible after AJAX swaps
    </script>
@endsection
