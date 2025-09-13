@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .badge {
            font-size: 0.8rem;
        }

        .table td,
        .table th {
            vertical-align: middle !important;
        }

        .card-stat {
            border-radius: 10px;
        }
    </style>
@endsection

@section('content')

    <!-- Breadcrumb -->
    <div class="breadcrumb-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="main-content-title">Manage Request Orders</h4>
        </div>
        <div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Request Orders</li>
            </ol>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Nav Tabs -->
    <div class="card custom-card mb-4">
        <div class="card-footer py-2">
            <nav class="nav nav-tabs card-header-tabs">
                <a class="nav-link {{ Request::is('admin/orders') ? 'active' : '' }}"
                    href="{{ route('admin.orders.index') }}">Subscription Orders</a>
                <a class="nav-link {{ Request::is('admin/manage-flower-request') ? 'active' : '' }}"
                    href="{{ route('flower-request') }}">Request Orders</a>
            </nav>
        </div>
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">{{ session()->get('success') }}</div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">{{ $errors->first('danger') }}</div>
    @endif

    <!-- Orders Table -->
    <div class="card custom-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="file-datatable" class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Request ID / User</th>
                            <th>Purchase Date</th>
                            <th>Delivery Date</th>
                            <th>Flower Items</th>
                            <th>Suggestion</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Actions</th>
                            <th>Rider</th>
                            <th>Address</th>
                            <th>Cancelled By</th>
                            <th>Cancel Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRequests as $request)
                            <tr>
                                <!-- Request ID + User -->
                                <td>
                                    <strong>#{{ $request->request_id }}</strong><br>
                                    <small class="text-muted">Name:</small> {{ $request->user->name ?? 'N/A' }}<br>
                                    <small class="text-muted">Phone:</small> {{ $request->user->mobile_number ?? 'N/A' }}
                                </td>

                                <!-- Purchase Date -->
                                <td>{{ optional($request->created_at)->format('d-m-Y h:i A') ?? 'N/A' }}</td>

                                <!-- Delivery Date -->
                                <td>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }} {{ $request->time }}</td>

                                <!-- Flower Items -->
                                <td>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($request->flowerRequestItems as $item)
                                            <li>{{ $item->flower_name }} - {{ $item->flower_quantity }}
                                                {{ $item->flower_unit }}</li>
                                        @endforeach
                                    </ul>
                                </td>

                                <!-- Suggestion -->
                                <td>
                                    @if ($request->suggestion)
                                        <span class="badge bg-secondary">{{ $request->suggestion }}</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td>
                                    @switch($request->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Order Placed</span>
                                            <div><small class="text-muted">Update the Price</small></div>
                                        @break

                                        @case('approved')
                                            <span class="badge bg-info">Payment Pending</span>
                                        @break

                                        @case('paid')
                                            <span class="badge bg-success">Payment Completed</span>
                                        @break

                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @break

                                        @default
                                            <span class="text-muted">Unknown</span>
                                    @endswitch
                                </td>

                                <!-- Price -->
                                <td>
                                    @if ($request->order && $request->order->total_price)
                                        <div><strong>Total:</strong> ₹{{ $request->order->total_price }}</div>
                                        <small>Flower: ₹{{ $request->order->requested_flower_price }}</small><br>
                                        <small>Delivery: ₹{{ $request->order->delivery_charge }}</small>
                                    @else
                                        <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST">
                                            @csrf
                                            <input type="number" name="requested_flower_price" class="form-control mb-2"
                                                placeholder="Enter Price" required>
                                            <input type="number" name="delivery_charge" class="form-control mb-2"
                                                placeholder="Enter Delivery Charge" required>
                                            <small class="form-text text-muted">Enter "0" if no delivery charge.</small>
                                            <button type="submit" class="btn btn-sm btn-primary mt-2">Save</button>
                                        </form>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td>
                                    <form id="markPaymentForm_{{ $request->request_id }}"
                                        action="{{ route('admin.markPayment', $request->request_id) }}" method="POST">
                                        @csrf
                                        @if ($request->status == 'approved')
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="confirmPayment('{{ $request->request_id }}')">Mark Paid</button>
                                        @elseif($request->status == 'paid')
                                            <button type="button" class="btn btn-success btn-sm" disabled>Paid</button>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm" disabled>N/A</button>
                                        @endif
                                    </form>
                                </td>

                                <!-- Rider -->
                                <td>
                                    @if ($request->order && $request->order->total_price)
                                        @if ($request->order->rider_id)
                                            <span class="badge bg-primary">{{ $request->order->rider->rider_name }}</span>
                                            @if ($request->status != 'paid')
                                                <a href="#" class="btn btn-sm btn-outline-info mt-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editRiderModal{{ $request->order->id }}">
                                                    Edit Rider
                                                </a>
                                            @endif
                                        @else
                                            <form action="{{ route('admin.orders.assignRider', $request->order->id) }}"
                                                method="POST">
                                                @csrf
                                                <select name="rider_id" class="form-select mb-2">
                                                    <option selected disabled>Choose Rider</option>
                                                    @foreach ($riders as $rider)
                                                        <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-success">Assign</button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted">Price not set</span>
                                    @endif
                                </td>

                                <!-- Address -->
                                <td>
                                    <strong>Address:</strong>
                                    {{ $request->address->apartment_flat_plot ?? '' }},
                                    {{ $request->address->apartment_name ?? '' }},
                                    {{ $request->address->locality_name ?? '' }}<br>
                                    <small><strong>Landmark:</strong> {{ $request->address->landmark ?? '' }}</small><br>
                                    <small><strong>City:</strong> {{ $request->address->city ?? '' }}</small><br>
                                    <small><strong>State:</strong> {{ $request->address->state ?? '' }}</small><br>
                                    <small><strong>Pin:</strong> {{ $request->address->pincode ?? '' }}</small>
                                </td>

                                <!-- Cancel Info -->
                                <td>
                                    @if ($request->cancel_by)
                                        <span class="badge bg-dark">{{ ucfirst($request->cancel_by) }}</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($request->cancel_reason)
                                        {{ $request->cancel_reason }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- SweetAlert for Payment Confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmPayment(requestId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to mark this payment as Paid.",
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
    </script>
@endsection
