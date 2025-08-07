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

    </style>
@endsection

@section('content')

    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Request Orders</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Request Orders</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 full-width-tabs">
                            <a class="nav-link mb-2 mt-2 " href="{{ route('admin.orders.index') }}"
                                onclick="changeColor(this)">Subscription Orders</a>
                            <a class="nav-link mb-2 mt-2 {{ Request::is('admin/manage-flower-request') ? 'active' : '' }}"
                                href="{{ route('flower-request') }}" onclick="changeColor(this)">Request Orders</a>

                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('admin.orders.index', ['filter' => 'active']) }}">
                <div class="card bg-success text-dark mb-3">
                    <div class="card-header">
                        <i class="fas fa-check-circle"></i> Active Subscriptions
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-white">{{ $activeSubscriptions }}</h5>
                        <p class="card-text text-white">Users with an active subscription</p>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-md-4">
            <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}">
                <div class="card bg-warning text-dark mb-3">
                    <div class="card-header">
                        <i class="fas fa-pause-circle"></i> Paused Subscriptions
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $pausedSubscriptions }}</h5>
                        <p class="card-text">Users with a paused subscription</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('flower-request', ['filter' => 'today']) }}">
                <div class="card bg-info text-dark mb-3">
                    <div class="card-header">
                        <i class="fas fa-box"></i> Orders Requested Today
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $ordersRequestedToday }}</h5>
                        <p class="card-text">Requested Orders placed today</p>
                    </div>
                </div>
            </a>
        </div>
    </div>


    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <!-- <div>
                                            <h6 class="main-content-label mb-1">File export Datatables</h6>
                                            <p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
                                        </div> -->
                    @if (session()->has('success'))
                        <div class="alert alert-success" id="Message">
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    @if ($errors->has('danger'))
                        <div class="alert alert-danger" id="Message">
                            {{ $errors->first('danger') }}
                        </div>
                    @endif
                    <div class="table-responsive  export-table">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Purchase Date</th>
                                    <th>Delivery Date</th>
                                    <th>Flower Items</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                    <th>Assigned Rider</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingRequests as $request)
                                    <tr>
                                        <td>{{ $request->request_id }} <br>
                                            Name: {{ $request->user->name }} <br>
                                            Number : {{ $request->user->mobile_number }}
                                        </td>
                                        <td>{{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->format('d-m-Y h:i A') : 'N/A' }}
                                        </td>

                                        <td>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }}
                                            {{ $request->time }}</td>

                                        <td>
                                            <ul>
                                                @foreach ($request->flowerRequestItems as $item)
                                                    <li>
                                                        {{ $item->flower_name }} - {{ $item->flower_quantity }}
                                                        {{ $item->flower_unit }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            @if ($request->status == 'pending')
                                                <p>Order Placed <br> Update the Price</p>
                                            @elseif($request->status == 'approved')
                                                <p>Payment Pending</p>
                                            @elseif($request->status == 'paid')
                                                <p>Payment Completed</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($request->order && $request->order->total_price)
                                                <span>Total Price : {{ $request->order->total_price }} <br>
                                                    Price Of Flower : {{ $request->order->requested_flower_price }} <br>
                                                    Delivery Charge : {{ $request->order->delivery_charge }} </span>
                                            @else
                                                <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST"
                                                    style="display: inline;">
                                                    @csrf
                                                    <input type="number" name="requested_flower_price" class="form-control"
                                                        placeholder="Enter Price" required style="margin-bottom: 13px;">
                                                    <input type="number" name="delivery_charge" class="form-control"
                                                        placeholder="Enter Delivery Charge" required>
                                                    <span class="form-text text-muted"
                                                        style="font-size: 12px; margin-top: 5px;">
                                                        If the delivery charge is 0, please enter "0" instead of leaving it
                                                        blank.
                                                    </span>
                                                    <button type="submit" class="btn btn-primary mt-2">Save</button>
                                                </form>
                                            @endif
                                        </td>

                                        <td>
                                            <form id="markPaymentForm_{{ $request->request_id }}"
                                                action="{{ route('admin.markPayment', $request->request_id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                @if ($request->status == 'pending' || $request->status == 'paid')
                                                    <button type="button" class="btn btn-success mt-2"
                                                        disabled>Paid</button>
                                                @elseif ($request->status == 'approved')
                                                    <button type="button" class="btn btn-success mt-2"
                                                        onclick="confirmPayment('{{ $request->request_id }}')">Paid</button>
                                                @endif
                                            </form>
                                        </td>

                                        <td>
                                            @if ($request->order && $request->order->total_price)
                                                @if ($request->order->rider_id)
                                                    <span>{{ $request->order->rider->rider_name }}</span>
                                                    @if ($request->status == 'paid')
                                                    @else
                                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                            data-bs-target="#editRiderModal{{ $request->order->id }}">
                                                            Edit Rider
                                                        </a>
                                                    @endif
                                                @else
                                                    <form
                                                        action="{{ route('admin.orders.assignRider', $request->order->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <select name="rider_id" class="form-control">
                                                            <option selected>Choose Rider</option>
                                                            @foreach ($riders as $rider)
                                                                <option value="{{ $rider->rider_id }}"
                                                                    {{ $request->order->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                                    {{ $rider->rider_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-success mt-2">Assign
                                                            Rider</button>
                                                    </form>
                                                @endif
                                            @else
                                                <p>Need to Give the price</p>
                                            @endif
                                        </td>

                                        <td>
                                            <strong>Address:</strong>
                                            {{ $request->address->apartment_flat_plot ?? '' }},{{ $order->address->apartment_name ?? '' }},
                                            {{ $request->address->locality_name ?? '' }}<br>
                                            <strong>Landmark:</strong> {{ $request->address->landmark ?? '' }}<br>
                                            <strong>City:</strong> {{ $request->address->city ?? '' }}<br>
                                            <strong>State:</strong> {{ $request->address->state ?? '' }}<br>
                                            <strong>Pin Code:</strong> {{ $request->address->pincode ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @foreach ($pendingRequests as $order)
                            <div class="modal fade" id="editRiderModal{{ $order->id }}" tabindex="-1"
                                aria-labelledby="editRiderModalLabel{{ $order->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRiderModalLabel{{ $order->id }}">
                                                Change Rider for Order #{{ $order->order_id }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admin.orders.updateRider', $order->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="rider_id{{ $order->id }}" class="form-label">Select
                                                        Rider</label>
                                                    <select name="rider_id" id="rider_id{{ $order->id }}"
                                                        class="form-control">
                                                        @foreach ($riders as $rider)
                                                            <option value="{{ $rider->rider_id }}"
                                                                {{ $order->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                                {{ $rider->rider_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- Internal Data tables -->
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

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Save Order Button Click
            $('.save-order').on('click', function() {
                let requestId = $(this).data('request-id');
                let price = $(this).closest('tr').find('.price-input').val();

                if (!price) {
                    alert('Please enter a price');
                    return;
                }

                $.ajax({
                    url: '/api/save-order',
                    type: 'POST',
                    data: {
                        request_id: requestId,
                        price: price,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Order saved successfully');
                        $('.payment[data-request-id="' + requestId + '"]').prop('disabled',
                            false); // Enable Payment Button
                    }
                });
            });

            // Paid Button Click
            $('.payment').on('click', function() {
                let requestId = $(this).data('request-id');

                $.ajax({
                    url: '/api/mark-payment',
                    type: 'POST',
                    data: {
                        request_id: requestId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Payment marked as paid');
                    }
                });
            });
        });
    </script>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
