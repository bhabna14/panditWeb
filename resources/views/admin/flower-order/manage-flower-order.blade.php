@extends('admin.layouts.app')

@section('styles')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">




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
            color: #fff;
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

        .btn {
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 4px;
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
                                    <th>Order ID</th>
                                    <th>Purchase Date</th>
                                    <th>Product Details</th>
                                    <th>Address Details</th>
                                    <th>Total Price</th>
                                    {{-- <th>Payment Status</th> --}}
                                    <th>Status</th>
                                    <th>Assigned Rider</th>
                                    <th>Reffered By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_id }} <br>
                                            Name: {{ $order->user->name }} <br>
                                            Number : {{ $order->user->mobile_number }} <br>
                                            <a href="{{ route('showCustomerDetails', $order->user->userid) }}"
                                                class="btn btn-sm btn-warning">View Customer</a>
                                        </td>
                                        <td>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d-m-Y h:i A') : 'N/A' }}
                                        </td>

                                        <td>
                                            {{ $order->flowerProduct->name }} <br>
                                            @if ($order->subscription)
                                                ({{ \Carbon\Carbon::parse($order->subscription->start_date)->format('F j, Y') }}
                                                -
                                                {{ $order->subscription->new_date ? \Carbon\Carbon::parse($order->subscription->new_date)->format('F j, Y') : \Carbon\Carbon::parse($order->subscription->end_date)->format('F j, Y') }})
                                            @else
                                                <span>No subscription data</span>
                                            @endif
                                        </td>

                                        <td>
                                            <!-- Button to Open Modal -->
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#addressModal{{ $order->id }}">
                                                <i class="fas fa-map-marker-alt"></i> View Address
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="addressModal{{ $order->id }}" tabindex="-1"
                                                aria-labelledby="addressModalLabel{{ $order->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <!-- Modal Header -->
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title"
                                                                id="addressModalLabel{{ $order->id }}">
                                                                <i class="fas fa-home"></i> Address Details
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>

                                                        <!-- Modal Body -->
                                                        <div class="modal-body">
                                                            <p><i class="fas fa-map-marker-alt text-primary"></i>
                                                                <strong>Address:</strong>
                                                                {{ $order->address->apartment_flat_plot ?? '' }},
                                                                {{ $order->address->apartment_name ?? '' }},
                                                                {{ $order->address->localityDetails->locality_name ?? '' }}
                                                            </p>
                                                            <p><i class="fas fa-landmark text-primary"></i>
                                                                <strong>Landmark:</strong>
                                                                {{ $order->address->landmark ?? '' }}
                                                            </p>
                                                            <p><i class="fas fa-envelope text-primary"></i> <strong>Pin
                                                                    Code:</strong> {{ $order->address->pincode ?? '' }}</p>
                                                            <p><i class="fas fa-city text-primary"></i>
                                                                <strong>City:</strong> {{ $order->address->city ?? '' }}
                                                            </p>
                                                            <p><i class="fas fa-flag text-primary"></i>
                                                                <strong>State:</strong> {{ $order->address->state ?? '' }}
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
                                                <i class="fas fa-edit"></i> Edit Address
                                            </a>

                                            <!-- Edit Address Modal -->
                                            <div class="modal fade" id="editAddressModal{{ $order->id }}"
                                                tabindex="-1" aria-labelledby="editAddressModalLabel{{ $order->id }}"
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
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Modal Body -->
                                                        <div class="modal-body">
                                                            <form
                                                                action="{{ route('admin.orders.updateAddress', $order->address->id) }}"
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
                                                                            value="{{ $order->address->apartment_flat_plot }}">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="apartment_name"
                                                                            class="form-label">Apartment Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="apartment_name" name="apartment_name"
                                                                            value="{{ $order->address->apartment_name }}">
                                                                    </div>

                                                                </div>
                                                                <div class="row">
                                                                    <div class="mb-3">
                                                                        <label for="locality_name"
                                                                            class="form-label">Locality</label>
                                                                        <input type="text" class="form-control"
                                                                            id="locality_name" name="locality_name"
                                                                            value="{{ $order->address->locality }}">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="landmark"
                                                                            class="form-label">Landmark</label>
                                                                        <input type="text" class="form-control"
                                                                            id="landmark" name="landmark"
                                                                            value="{{ $order->address->landmark }}">
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="mb-3">
                                                                        <label for="pincode" class="form-label">Pin
                                                                            Code</label>
                                                                        <input type="text" class="form-control"
                                                                            id="pincode" name="pincode"
                                                                            value="{{ $order->address->pincode }}">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="city"
                                                                            class="form-label">City</label>
                                                                        <input type="text" class="form-control"
                                                                            id="city" name="city"
                                                                            value="{{ $order->address->city }}">
                                                                    </div>
                                                                </div>


                                                                <div class="mb-3">
                                                                    <label for="state" class="form-label">State</label>
                                                                    <input type="text" class="form-control"
                                                                        id="state" name="state"
                                                                        value="{{ $order->address->state }}">
                                                                </div>

                                                                <!-- Modal Footer -->
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="fas fa-save"></i> Save Changes
                                                                    </button>
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        <i class="fas fa-times"></i> Close
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>

                                        <td>
                                            {{ number_format($order->total_price, 2) }}
                                            <!-- Edit Price Button -->
                                            <a href="#" class="btn btn-outline-secondary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editPriceModal{{ $order->id }}">
                                                <i class="fas fa-edit"></i> Edit Price
                                            </a>

                                            <!-- Edit Price Modal -->
                                            <div class="modal fade" id="editPriceModal{{ $order->id }}"
                                                tabindex="-1" aria-labelledby="editPriceModalLabel{{ $order->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <!-- Modal Header -->
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title"
                                                                id="editPriceModalLabel{{ $order->id }}">
                                                                <i class="fas fa-edit"></i> Edit Total Price
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Modal Body -->
                                                        <div class="modal-body">
                                                            <form
                                                                action="{{ route('admin.orders.updatePrice', $order->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="mb-3">
                                                                    <label for="total_price" class="form-label">Total
                                                                        Price</label>
                                                                    <input type="number" class="form-control"
                                                                        id="total_price" name="total_price"
                                                                        value="{{ $order->total_price }}" step="0.01"
                                                                        required>
                                                                </div>

                                                                <!-- Modal Footer -->
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="fas fa-save"></i> Save Changes
                                                                    </button>
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        <i class="fas fa-times"></i> Close
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span
                                                class="status-badge
                                                {{ optional($order->subscription)->status === 'active' ? 'status-running bg-success' : '' }}
                                                {{ optional($order->subscription)->status === 'paused' ? 'status-paused bg-warning' : '' }}
                                                {{ optional($order->subscription)->status === 'expired' ? 'status-expired bg-danger' : '' }}
                                                {{ optional($order->subscription)->status === 'pending' ? 'status-expired bg-danger' : '' }}">
                                                {{ ucfirst(optional($order->subscription)->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($order->rider_id)
                                                <span>{{ $order->rider->rider_name }}</span>
                                                <a href="#editRiderModal{{ $order->id }}" class="btn btn-sm btn-info"
                                                    data-bs-toggle="modal">Edit Rider</a>
                                            @else
                                                <form action="{{ route('admin.orders.assignRider', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <select name="rider_id" class="form-control" required>
                                                        <option value="" selected>Choose</option>
                                                        @foreach ($riders as $rider)
                                                            <option value="{{ $rider->rider_id }}"
                                                                {{ $order->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                                {{ $rider->rider_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-success mt-2">Assign
                                                        Rider</button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->referral_id)
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
                                                <form action="{{ route('admin.orders.refferRider', $order->id) }}"
                                                    method="POST">
                                                    @csrf
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
                                                </form>
                                            @endif
                                        </td>

                                        <td style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                            <!-- View Button -->
                                            <!-- View Button -->
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-success d-flex align-items-center">
                                                <i class="fas fa-eye me-2"></i>view
                                            </a>

                                            <!-- Pause/Resume/Discontinued Buttons -->
                                            @if ($order->subscription)
                                                @if ($order->subscription->status == 'active')
                                                    <!-- Pause Button -->
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-info pause-button mt-2 d-flex align-items-center"
                                                        onclick="openPauseModal('{{ $order->subscription->start_date }}', '{{ $order->subscription->end_date }}')">
                                                        <i class="fas fa-pause-circle me-2"></i>pause
                                                    </a>
                                                @elseif ($order->subscription->status == 'paused')
                                                    <!-- Resume Button -->
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-warning resume-button mt-2 d-flex align-items-center"
                                                        onclick="openResumeModal('{{ $order->subscription->pause_start_date }}', '{{ $order->subscription->pause_end_date }}')">
                                                        <i class="fas fa-play-circle me-2"></i>resume
                                                    </a>
                                                    @elseif($order->subscription->status == 'expired')
                                                    <!-- Discontinued Button -->
                                                    <a href="javascript:void(0);"
                                                       class="btn mt-2 d-flex align-items-center"
                                                       style="background-color: #fe0404; color: white;"
                                                       onclick="confirmDiscontinue('{{ route('admin.subscriptions.discontinue', $order->user_id) }}')">
                                                       <i class="fas fa-times-circle me-2"></i> Remove
                                                    </a>
                                                @endif
                                            @else
                                                <span>No subscription available</span>
                                            @endif


                                            <!-- Pause Modal -->
                                            <div class="modal fade" id="pauseModal" tabindex="-1"
                                                aria-labelledby="pauseModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="pauseModalLabel">Pause
                                                                Subscription</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Pause Form -->
                                                            <form id="pauseForm"
                                                                action="{{ route('subscription.pause', $order->order_id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="pause_start_date" class="form-label">Pause
                                                                        Start Date</label>
                                                                    <input type="date" id="pause_start_date"
                                                                        name="pause_start_date" class="form-control"
                                                                        required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="pause_end_date" class="form-label">Pause
                                                                        End Date</label>
                                                                    <input type="date" id="pause_end_date"
                                                                        name="pause_end_date" class="form-control"
                                                                        required>
                                                                </div>

                                                                <p class="text-muted">
                                                                    Dates must be between <span
                                                                        id="subscriptionStart"></span> and <span
                                                                        id="subscriptionEnd"></span>.
                                                                </p>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="resumeModal" tabindex="-1"
                                                aria-labelledby="resumeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="resumeModalLabel">Resume
                                                                Subscription</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Form to resume subscription -->
                                                            <form id="resumeForm"
                                                                action="{{ route('subscription.resume', $order->order_id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="resume_date" class="form-label">Select
                                                                        Resume Date</label>
                                                                    <input type="date" id="resume_date"
                                                                        name="resume_date" class="form-control" required>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success">Resume</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>



                        <!-- Add the modal for editing the rider -->
                        @foreach ($orders as $order)
                            <div class="modal fade" id="editRiderModal{{ $order->id }}" tabindex="-1"
                                aria-labelledby="editRiderModalLabel{{ $order->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRiderModalLabel{{ $order->id }}">Change
                                                Rider for Order #{{ $order->order_id }}</h5>
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

    <script>
        // Open the Pause Modal and set the date range
        function openPauseModal(startDate, endDate) {
            const startDateField = document.getElementById('pause_start_date');
            const endDateField = document.getElementById('pause_end_date');
            const subscriptionStartText = document.getElementById('subscriptionStart');
            const subscriptionEndText = document.getElementById('subscriptionEnd');

            // Set the date range text
            subscriptionStartText.textContent = startDate;
            subscriptionEndText.textContent = endDate;

            // Set the min and max attributes for the date fields
            startDateField.setAttribute('min', startDate);
            endDateField.setAttribute('min', startDate);
            endDateField.setAttribute('max', endDate);

            // Open the modal using Bootstrap
            new bootstrap.Modal(document.getElementById('pauseModal')).show();
        }

        // Open the Resume Modal
        function openResumeModal(pauseStartDate, pauseEndDate) {
            const resumeDateField = document.getElementById('resume_date');

            // Set the min and max for the resume date field
            resumeDateField.setAttribute('min', pauseStartDate);
            resumeDateField.setAttribute('max', pauseEndDate);

            // Set the default date to the pause start date
            resumeDateField.value = pauseStartDate;

            // Open the modal using Bootstrap
            new bootstrap.Modal(document.getElementById('resumeModal')).show();
        }
    </script>


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

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000
        });
    </script>
@endif



    <!-- Updated JavaScript -->
@endsection
