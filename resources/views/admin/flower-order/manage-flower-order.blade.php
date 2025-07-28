@extends('admin.layouts.apps')

@section('styles')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">



    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .btn {
            text-align: center;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        /* View Button */
        .btn-view {
            background-color: #4CAF50;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-view:hover {
            background-color: #45a049;
        }

        /* Action Buttons (Pause/Resume) */
        .btn-action {
            background-color: #c80100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-action:hover {
            background-color: #a00000;
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-footer {
            border-top: none;
        }

        .modal-header {
            background-color: #007bff;
            color: #fff;
            border-bottom: none;
        }

        .modal-body {
            font-size: 16px;
            line-height: 1.8;
        }

        .modal-body p {
            margin-bottom: 10px;
        }

        .modal-footer {
            border-top: none;
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: #fff;
        }

        .order-id,
        .customer-name,
        .customer-number {
            white-space: nowrap;
            /* Prevent line breaks */
            overflow: hidden;
            /* Ensure content doesn't overflow */
            text-overflow: ellipsis;
            /* Show ellipsis for truncated content */
            display: block;
            /* Ensure consistent block-level display */
        }

        .order-details {
            word-wrap: break-word;
            /* Handle word wrapping for long text elsewhere */
            max-width: 100%;
            /* Keep the div responsive */
        }

        .table-responsive {
            overflow-x: auto;
            /* Enable horizontal scrolling for the table */
        }

        .table {
            width: 100%;
            /* Ensure the table takes full width */
            table-layout: auto;
            /* Allow dynamic column widths */
        }

        .order-details {
            background-color: #f9f9f9;
            /* Light background for a premium feel */
            border: 1px solid #ddd;
            /* Subtle border for separation */
            border-radius: 8px;
            /* Rounded corners */
            padding: 15px;
            /* Spacing inside the container */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Light shadow for depth */
        }

        .order-details p {
            margin: 0 0 6px;
            /* Space between paragraphs */
            font-size: 12px;
            /* Readable font size */
            color: #333;
            /* Dark text for better readability */
        }

        .order-details .text-muted {
            color: #999;
            /* Muted color for unavailable data */
        }

        .btn-view-customer {
            display: inline-block;
            background-color: #ffc107;
            /* Bootstrap warning color */
            color: #fff;
            /* White text */
            text-decoration: none;
            /* Remove underline */
            font-weight: 600;
            /* Semi-bold text */
            border-radius: 5px;
            /* Rounded corners */
            transition: all 0.3s ease-in-out;
            /* Smooth hover transition */
        }

        .btn-view-customer:hover {
            background-color: #ffca2c;
            /* Slightly lighter hover effect */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            /* Shadow on hover */
            color: #fff;
            /* Ensure text remains white */
        }

        .product-details {
            padding: 10px;
            /* Add some spacing inside the cell */
            font-size: 14px;
            /* Adjust font size for better readability */
            color: #333;
            /* Dark text color for clarity */
            line-height: 1.5;
            /* Ensure proper spacing between lines */
            word-wrap: break-word;
            /* Prevents content from overflowing */
        }

        .product-details .product-name {
            margin-bottom: 8px;
            /* Space after product name */
            font-weight: 600;
            /* Make the product name bold */
            color: #0056b3;
            /* Add a subtle color for emphasis */
            white-space: nowrap;
            /* Prevent wrapping for the product name */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Use ellipsis if text overflows */
        }

        .subscription-dates {
            margin-bottom: 8px;
            /* Space after subscription dates */
            font-size: 13px;
            /* Slightly smaller text */
            color: #000;
            /* Solid black for dates */
            white-space: nowrap;
            /* Prevent wrapping for dates */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Use ellipsis if text overflows */
        }

        .no-subscription {
            font-size: 13px;
            /* Smaller font size for muted text */
            color: #999;
            /* Muted text for no subscription */
            white-space: nowrap;
            /* Prevent wrapping for no subscription text */
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Flower Order</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Flower Order</li>
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
                            <a class="nav-link mb-2 mt-2 {{ Request::is('admin/flower-orders') ? 'active' : '' }}"
                                href="{{ route('admin.orders.index') }}" onclick="changeColor(this)">Subscription Orders</a>
                            <a class="nav-link mb-2 mt-2" href="{{ route('flower-request') }}"
                                onclick="changeColor(this)">Request Orders</a>

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
            <a href="{{ route('admin.orders.index', ['filter' => 'renew']) }}">
                <div class="card bg-info text-dark mb-3">
                    <div class="card-header">
                        <i class="fas fa-box"></i>Subscription Placed today
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $ordersRequestedToday }}</h5>
                        <p class="card-text">Subscription Placed today</p>
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
                    <div class="table-responsive ">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>Customer Details</th>
                                    <th>Purchase Date</th>
                                    <th>Duration</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Assigned Rider</th>
                                    <th>Reffered By</th>
                                    <th>Subscription</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td style="padding: 15px; vertical-align: top;">
                                            <div class="order-details" data-bs-toggle="tooltip" data-bs-html="true"
                                                title="
                                                <p><i class='fas fa-map-marker-alt text-primary'></i>
                                                <strong>Address:</strong>
                                                {{ $order->order->address->apartment_flat_plot ?? '' }},
                                                {{ $order->order->address->apartment_name ?? '' }},
                                                {{ $order->order->address->localityDetails->locality_name ?? '' }}</p>
                                            ">
                                                <!-- Order ID -->
                                                <p class="order-id">
                                                    <strong>Ord No :</strong> {{ $order->order_id }}
                                                </p>

                                                <!-- Customer Name -->
                                                @if (!empty($order->users->name))
                                                    <p class="customer-name">
                                                        <strong>Name :</strong> {{ $order->users->name }}
                                                    </p>
                                                @else
                                                    <p class="customer-name text-muted">
                                                        <strong>Name :</strong> Not Available
                                                    </p>
                                                @endif

                                                <!-- Customer Number -->
                                                @if (!empty($order->users->mobile_number))
                                                    <p class="customer-number">
                                                        <strong>No :</strong> {{ $order->users->mobile_number }}
                                                    </p>
                                                @else
                                                    <p class="customer-number text-muted">
                                                        <strong>No :</strong> Not Available
                                                    </p>
                                                @endif

                                                <!-- View Customer Button -->
                                                @if (!empty($order->users->userid))
                                                    <a href="{{ route('showCustomerDetails', $order->users->userid) }}"
                                                        class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addressModal{{ $order->id }}">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="addressModal{{ $order->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="addressModalLabel{{ $order->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <!-- Modal Header -->
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title"
                                                                        id="addressModalLabel{{ $order->id }}">
                                                                        <i class="fas fa-home"></i> Address Details
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>

                                                                <!-- Modal Body -->
                                                                <div class="modal-body">
                                                                    <p><i class="fas fa-map-marker-alt text-primary"></i>
                                                                        <strong>Address:</strong>
                                                                        {{ $order->order->address->apartment_flat_plot ?? '' }},
                                                                        {{ $order->order->address->apartment_name ?? '' }},
                                                                        {{ $order->order->address->localityDetails->locality_name ?? '' }}
                                                                    </p>
                                                                    <p><i class="fas fa-landmark text-primary"></i>
                                                                        <strong>Landmark:</strong>
                                                                        {{ $order->order->address->landmark ?? '' }}
                                                                    </p>
                                                                    <p><i class="fas fa-envelope text-primary"></i>
                                                                        <strong>Pin
                                                                            Code:</strong>
                                                                        {{ $order->order->address->pincode ?? '' }}
                                                                    </p>
                                                                    <p><i class="fas fa-city text-primary"></i>
                                                                        <strong>City:</strong>
                                                                        {{ $order->order->address->city ?? '' }}
                                                                    </p>
                                                                    <p><i class="fas fa-flag text-primary"></i>
                                                                        <strong>State:</strong>
                                                                        {{ $order->order->address->state ?? '' }}
                                                                    </p>

                                                                </div>

                                                                <!-- Modal Footer -->
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        <i class="fas fa-times"></i> Close
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Edit Address Button -->
                                                    <a href="#" class="btn btn-outline-secondary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editAddressModal{{ $order->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Edit Address Modal -->
                                                    <div class="modal fade" id="editAddressModal{{ $order->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="editAddressModalLabel{{ $order->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <!-- Modal Header -->
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title"
                                                                        id="editAddressModalLabel{{ $order->id }}">
                                                                        <i class="fas fa-edit"></i> Edit Address
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>

                                                                <!-- Modal Body -->
                                                                <div class="modal-body">
                                                                    <form
                                                                        action="{{ route('admin.orders.updateAddress', $order->order->address->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="row">
                                                                            <div class="mb-3">
                                                                                <label for="apartment_flat_plot"
                                                                                    class="form-label">Flat/Plot</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="apartment_flat_plot"
                                                                                    name="apartment_flat_plot"
                                                                                    value="{{ $order->order->address->apartment_flat_plot }}">
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label for="apartment_name"
                                                                                    class="form-label">Apartment
                                                                                    Name</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="apartment_name"
                                                                                    name="apartment_name"
                                                                                    value="{{ $order->order->address->apartment_name }}">
                                                                            </div>

                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="mb-3">
                                                                                <label for="locality_name"
                                                                                    class="form-label">Locality</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="locality_name"
                                                                                    name="locality_name"
                                                                                    value="{{ $order->order->address->locality }}">
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label for="landmark"
                                                                                    class="form-label">Landmark</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="landmark" name="landmark"
                                                                                    value="{{ $order->order->address->landmark }}">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="mb-3">
                                                                                <label for="pincode"
                                                                                    class="form-label">Pin
                                                                                    Code</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="pincode" name="pincode"
                                                                                    value="{{ $order->order->address->pincode }}">
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label for="city"
                                                                                    class="form-label">City</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="city" name="city"
                                                                                    value="{{ $order->order->address->city }}">
                                                                            </div>
                                                                        </div>


                                                                        <div class="mb-3">
                                                                            <label for="state"
                                                                                class="form-label">State</label>
                                                                            <input type="text" class="form-control"
                                                                                id="state" name="state"
                                                                                value="{{ $order->order->address->state }}">
                                                                        </div>

                                                                        <!-- Modal Footer -->
                                                                        <div class="modal-footer">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">
                                                                                <i class="fas fa-save"></i> Save Changes
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">
                                                                                <i class="fas fa-times"></i> Close
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td style="text-align: left; padding: 10px; font-size: 14px; color: #333;">
                                            {{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d-m-Y h:i A') : 'N/A' }}

                                            @if ($order && $order->status == 'paused')
                                                <div
                                                    style="margin-top: 8px; padding: 8px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">
                                                    <strong><i class="fas fa-pause-circle me-2"></i></strong>
                                                    {{ \Carbon\Carbon::parse($order->pause_start_date)->format('d-m-Y') }}<br>
                                                    <strong><i class="fas fa-play-circle me-2"></i></strong>
                                                    {{ \Carbon\Carbon::parse($order->pause_end_date)->format('d-m-Y') }}

                                                    <!-- ✏️ Edit icon for modal -->
                                                    <button class="btn btn-sm btn-outline-secondary edit-pause-dates mt-2"
                                                        data-id="{{ $order->id }}"
                                                        data-start="{{ $order->pause_start_date }}"
                                                        data-end="{{ $order->pause_end_date }}" data-bs-toggle="modal"
                                                        data-bs-target="#editPauseModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>


                                        <td style="padding: 15px; vertical-align: top;">
                                            <div class="product-details">
                                                @if ($order)
                                                    <p class="subscription-dates">
                                                        {{ \Carbon\Carbon::parse($order->start_date)->format('F j, Y') }}
                                                        <br>
                                                        <strong style="margin-left: 40%"> - </strong> <br>
                                                        {{ $order->new_date
                                                            ? \Carbon\Carbon::parse($order->new_date)->format('F j, Y')
                                                            : \Carbon\Carbon::parse($order->end_date)->format('F j, Y') }}
                                                    </p>

                                                    <!-- Edit Icon -->
                                                    <a href="#" class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editDatesModal{{ $order->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editDatesModal{{ $order->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="editDatesModalLabel{{ $order->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="POST"
                                                                action="{{ route('admin.subscriptions.updateDates', $order->id) }}">
                                                                @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title"
                                                                            id="editDatesModalLabel{{ $order->id }}">
                                                                            Edit Subscription Dates
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="start_date{{ $order->id }}"
                                                                                class="form-label">Start Date</label>
                                                                            <input type="date" class="form-control"
                                                                                id="start_date{{ $order->id }}"
                                                                                name="start_date"
                                                                                value="{{ \Carbon\Carbon::parse($order->start_date)->toDateString() }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="end_date{{ $order->id }}"
                                                                                class="form-label">End Date</label>
                                                                            <input type="date" class="form-control"
                                                                                id="end_date{{ $order->id }}"
                                                                                name="end_date"
                                                                                value="{{ \Carbon\Carbon::parse($order->new_date ?? $order->end_date)->toDateString() }}"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-success">
                                                                            <i class="fas fa-save me-1"></i> Save
                                                                        </button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">
                                                                            <i class="fas fa-times me-1"></i> Cancel
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="no-subscription text-muted">
                                                        <strong>Subscription:</strong> No subscription data
                                                    </p>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <span style="font-weight: bold">₹
                                                {{ number_format($order->order->total_price, 2) }}</span>
                                        </td>

                                        <td>
                                            <span
                                                class="badge 
                                                    {{ $order->status === 'active' ? 'bg-success' : '' }}
                                                    {{ $order->status === 'paused' ? 'bg-warning' : '' }}
                                                    {{ $order->status === 'expired' ? 'bg-primary' : '' }}
                                                    {{ in_array($order->status, ['dead', 'pending']) ? 'bg-danger' : '' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <button class="btn btn-sm btn-outline-info edit-status-btn mt-1"
                                                data-id="{{ $order->id }}" data-status="{{ $order->status }}"
                                                data-bs-toggle="modal" data-bs-target="#editStatusModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>


                                        <td>
                                            @if ($order->order->rider_id)
                                                <span>{{ $order->order->rider->rider_name ?? '' }}</span>
                                                <a href="#editRiderModal{{ $order->order->id }}"
                                                    class="btn btn-sm btn-outline-info" data-bs-toggle="modal"><i
                                                        class="fas fa-edit"></i></a>
                                            @else
                                                <form action="{{ route('admin.orders.assignRider', $order->order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="d-flex flex-column">
                                                        <select name="rider_id" class="form-control" required>
                                                            <option value="" selected>Choose</option>
                                                            @foreach ($riders as $rider)
                                                                <option value="{{ $rider->rider_id }}"
                                                                    {{ $order->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                                    {{ $rider->rider_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button style="font-weight: bold" type="submit"
                                                            class="btn btn-sm btn-success mt-2">
                                                            Save</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->order->referral_id)
                                                @php
                                                    $referralRider = $riders->firstWhere(
                                                        'rider_id',
                                                        $order->referral_id,
                                                    );
                                                @endphp
                                                @if ($referralRider)
                                                    <span>{{ $referralRider->rider_name }}</span>
                                                @else
                                                    <span>No Referral Rider Found</span>
                                                @endif
                                            @else
                                                <form action="{{ route('admin.orders.refferRider', $order->order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="d-flex flex-column">
                                                        <select name="referral_id" class="form-control" required>
                                                            <option value="" selected>Choose</option>
                                                            @foreach ($riders as $rider)
                                                                <option value="{{ $rider->rider_id }}"
                                                                    {{ $order->referral_id == $rider->rider_id ? 'selected' : '' }}>
                                                                    {{ $rider->rider_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success mt-2">Save</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </td>

                                        <td style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                            <!-- View Button -->
                                            <!-- View Button -->
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn d-flex align-items-center"
                                                style="background:linear-gradient(90deg, #48fadf 0%, #2ebae5 100%);">
                                                <i class="fas fa-eye me-2"></i>view
                                            </a>

                                            <!-- Pause/Resume/Discontinued Buttons -->
                                            @if ($order)
                                                @if ($order->status == 'active')
                                                    <a href="{{ route('subscription.pausepage', $order->id) }}"
                                                        class="btn btn-warning pause-button mt-2 d-flex align-items-center">
                                                        <i class="fas fa-pause-circle me-2"></i> Pause
                                                    </a>
                                                @elseif ($order->status == 'paused')
                                                    <a href="{{ route('subscription.resumepage', $order->id) }}"
                                                        class="btn btn-warning pause-button mt-2 d-flex align-items-center">
                                                        <i class="fas fa-play-circle me-2"></i>Resume
                                                    </a>
                                                @elseif($order->status == 'expired')
                                                    <!-- Discontinued Button -->
                                                    <a href="javascript:void(0);"
                                                        class="btn mt-2 d-flex align-items-center"
                                                        style="background-color: #fe0404; color: white;"
                                                        onclick="confirmDiscontinue('{{ route('admin.subscriptions.discontinue', $order->order->user_id) }}')">
                                                        <i class="fas fa-times-circle me-2"></i> Remove
                                                    </a>
                                                @endif
                                            @else
                                                <span>No subscription available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Add the modal for editing the rider -->
                        @foreach ($orders as $order)
                            <div class="modal fade" id="editRiderModal{{ $order->order->id }}" tabindex="-1"
                                aria-labelledby="editRiderModalLabel{{ $order->order->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRiderModalLabel{{ $order->order->id }}">
                                                Change
                                                Rider for Order #{{ $order->order->order_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admin.orders.updateRider', $order->order->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="rider_id{{ $order->order->id }}"
                                                        class="form-label">Select
                                                        Rider</label>
                                                    <select name="rider_id" id="rider_id{{ $order->order->id }}"
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
    <div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="edit-status-form" method="POST"
                action="{{ route('admin.subscriptions.updateStatus', ['id' => 0]) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Update Subscription Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="subscription_id" id="status-sub-id">
                        <div class="mb-3">
                            <label for="status-select">Status</label>
                            <select class="form-select" name="status" id="status-select" required>
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                                <option value="pending">Pending</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="editPauseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="edit-pause-form" method="POST" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Edit Pause Dates</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="subscription_id" id="pause-sub-id">
                        <div class="mb-3">
                            <label for="pause-start">Pause Start Date</label>
                            <input type="date" name="pause_start_date" id="pause-start" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="pause-end">Pause End Date</label>
                            <input type="date" name="pause_end_date" id="pause-end" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- End Row -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap 5 -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDiscontinue(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will mark all related subscriptions as dead.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, discontinue!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>


    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000
            });
        </script>
    @endif
    <script>
        // Function to set the min attribute of the Pause End Date
        document.getElementById('pause_start_date').addEventListener('change', function() {
            let startDate = this.value;
            document.getElementById('pause_end_date').setAttribute('min', startDate);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Open modal and populate
        $('#file-datatable').on('click', '.edit-status-btn', function() {
            const subId = $(this).data('id');
            const currentStatus = $(this).data('status');

            $('#status-sub-id').val(subId);
            $('#status-select').val(currentStatus);
            $('#edit-status-form').attr('action', `/admin/subscriptions/${subId}/update-status`);
        });
    </script>

    <script>
        // Open modal and populate pause dates
        $('#file-datatable').on('click', '.edit-pause-dates', function() {
            const id = $(this).data('id');
            const start = $(this).data('start');
            const end = $(this).data('end');

            $('#pause-sub-id').val(id);
            $('#pause-start').val(moment(start).format('YYYY-MM-DD'));
            $('#pause-end').val(moment(end).format('YYYY-MM-DD'));
            $('#edit-pause-form').attr('action', `/admin/subscriptions/${id}/update-pause-dates`);
        });
    </script>
@endsection
