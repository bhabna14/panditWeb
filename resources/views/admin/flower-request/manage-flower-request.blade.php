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

    <!-- Alerts -->
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

                                <!-- Items -->
                                <!-- Items -->
                                <td>
                                    <ul class="ps-3 mb-0">
                                        @foreach ($request->flowerRequestItems as $item)
                                            @if ($item->type === 'garland')
                                                <li>
                                                    <strong>Garland:</strong> {{ $item->garland_name ?? 'N/A' }}
                                                    (Qty: {{ $item->garland_quantity ?? 0 }})
                                                    @if ($item->garland_size)
                                                        - Size: {{ $item->garland_size }}
                                                    @endif
                                                </li>
                                            @else
                                                <li>
                                                    <strong>Flower:</strong> {{ $item->flower_name ?? 'N/A' }}
                                                    - {{ $item->flower_quantity ?? 0 }} {{ $item->flower_unit ?? '' }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
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
                                                <button type="submit" class="btn btn-sm btn-success w-100">Assign</button>
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
    </div>
@endsection

@section('scripts')
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

    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
@endsection
