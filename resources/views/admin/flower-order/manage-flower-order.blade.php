@extends('admin.layouts.app')

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
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table id="file-datatable" class="table table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>Customer Details</th>
                                        <th>Purchase Date</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Assigned Rider</th>
                                        <th>Subscription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTables will load rows via AJAX --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-home"></i> Address Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" id="addressModalBody">
                                        <!-- Filled dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="edit-address-form" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Address</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" id="edit-address-id" name="id">
                                            <div class="row">
                                                <div class="mb-3 col-6"><label>Flat/Plot</label><input type="text"
                                                        class="form-control" name="apartment_flat_plot" id="edit-flat">
                                                </div>
                                                <div class="mb-3 col-6"><label>Apartment</label><input type="text"
                                                        class="form-control" name="apartment_name" id="edit-apartment">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-6"><label>Locality</label><input type="text"
                                                        class="form-control" name="locality" id="edit-locality"></div>
                                                <div class="mb-3 col-6"><label>Landmark</label><input type="text"
                                                        class="form-control" name="landmark" id="edit-landmark"></div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-6"><label>Pincode</label><input type="text"
                                                        class="form-control" name="pincode" id="edit-pincode"></div>
                                                <div class="mb-3 col-6"><label>City</label><input type="text"
                                                        class="form-control" name="city" id="edit-city"></div>
                                            </div>
                                            <div class="mb-3"><label>State</label><input type="text"
                                                    class="form-control" name="state" id="edit-state"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                                Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                                    class="fas fa-times"></i> Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Global Edit Dates Modal -->
                        <div class="modal fade" id="editDatesModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="edit-dates-form" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Edit Subscription Dates</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="subscription_id" id="sub-id">
                                            <div class="mb-3">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" id="sub-start"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" id="sub-end"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Save</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Global Edit Rider Modal -->
                        <div class="modal fade" id="editRiderModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="edit-rider-form" method="POST">
                                    @csrf @method('POST')
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">Assign/Change Rider</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="subscription_id" id="rider-sub-id">
                                            <div class="mb-3">
                                                <label>Rider</label>
                                                <select name="rider_id" id="rider-select" class="form-control" required>
                                                    <option value="">Choose Rider</option>
                                                    @foreach ($riders as $rider)
                                                        <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Save</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
@endsection
@section('scripts')
<!-- Dependencies -->
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<script>
$(function() {
    const table = $('#file-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.orders.index') }}",
            data: { filter: '{{ request('filter', '') }}' }
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: function(r) {
                    const userId = r.users?.userid;
                    const orderId = r.id;
                    const order = r.order || {};
                    const address = order.address || {};
                    const locality = address.localityDetails?.locality_name || address.locality || '';
                    const apartmentPlot = address.apartment_flat_plot || '';
                    const apartmentName = address.apartment_name || '';
                    const landmark = address.landmark || '';
                    const pincode = address.pincode || '';
                    const city = address.city || '';
                    const state = address.state || '';
                    const addressId = address.id;

                    let html = `
                        <p><strong>Ord No:</strong> ${order.order_id || 'N/A'}</p>
                        <p><strong>Name:</strong> ${r.users?.name || 'N/A'}</p>
                        <p><strong>No:</strong> ${r.users?.mobile_number || 'N/A'}</p>
                    `;

                    if (userId) {
                        html += `
                            <a href="/admin/showCustomerDetails/${userId}" class="btn btn-outline-info btn-sm me-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-outline-success btn-sm me-1 show-address-modal" data-order-id="${orderId}" data-bs-toggle="modal" data-bs-target="#addressModal">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm edit-address-modal" data-order-id="${orderId}"
                                data-address-id="${addressId}"
                                data-flat="${apartmentPlot}" data-name="${apartmentName}" data-locality="${locality}"
                                data-landmark="${landmark}" data-pincode="${pincode}" data-city="${city}" data-state="${state}"
                                data-bs-toggle="modal" data-bs-target="#editAddressModal">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                    }

                    return html;
                }
            },
            {
                data: 'created_at',
                render: d => d ? moment(d).format('DD-MM-YYYY h:mm A') : 'N/A'
            },
            {
                data: null,
                orderable: false,
                render: r => `
                    ${moment(r.start_date).format('MMM D, YYYY')}<br> — <br>
                    ${r.new_date ? moment(r.new_date).format('MMM D, YYYY') : moment(r.end_date).format('MMM D, YYYY')}
                    <br>
                    <button class="btn btn-sm btn-outline-secondary edit-dates" data-id="${r.id}" data-start="${r.start_date}" data-end="${r.new_date || r.end_date}">
                        <i class="fas fa-edit"></i>
                    </button>
                `
            },
            {
                data: 'order.total_price',
                render: p => `₹ ${parseFloat(p).toFixed(2)}`
            },
            {
                data: 'status',
                render: s => {
                    const classes = {
                        active: 'bg-success',
                        paused: 'bg-warning',
                        expired: 'bg-primary',
                        dead: 'bg-danger',
                        pending: 'bg-danger'
                    };
                    return `<span class="badge ${classes[s] || ''}">${s.toUpperCase()}</span>`;
                }
            },
            {
                data: null,
                render: r => `
                    ${r.order?.rider?.rider_name || 'Unassigned'}<br>
                    <button class="btn btn-sm btn-info edit-rider" data-subscription="${r.id}" data-order="${r.order?.id}" data-rider="${r.order?.rider?.rider_id || ''}">
                        <i class="fas fa-edit"></i>
                    </button>
                `
            },
            {
                data: null,
                orderable: false,
                render: r => {
                    let btn = `<a href="/admin/flower-orders/${r.id}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>`;
                    if (r.status === 'active')
                        btn += ` <a href="/admin/subscription/pause-page/${r.id}" class="btn btn-sm btn-warning"><i class="fas fa-pause"></i></a>`;
                    if (r.status === 'paused')
                        btn += ` <a href="/admin/subscription/resume-page/${r.id}" class="btn btn-sm btn-warning"><i class="fas fa-play"></i></a>`;
                    return btn;
                }
            }
        ],
        order: [[1, 'desc']]
    });

    // Show View Address Modal
    $('#file-datatable').on('click', '.show-address-modal', function() {
        const row = table.row($(this).closest('tr')).data();
        const address = row.order?.address || {};
        const body = `
            <p><strong>Address:</strong> ${address.apartment_flat_plot || ''}, ${address.apartment_name || ''}, ${address.localityDetails?.locality_name || ''}</p>
            <p><strong>Landmark:</strong> ${address.landmark || ''}</p>
            <p><strong>Pin Code:</strong> ${address.pincode || ''}</p>
            <p><strong>City:</strong> ${address.city || ''}</p>
            <p><strong>State:</strong> ${address.state || ''}</p>
        `;
        $('#addressModalBody').html(body);
    });

    // Open Edit Address Modal
    $('#file-datatable').on('click', '.edit-address-modal', function() {
        const form = $('#edit-address-form');
        const id = $(this).data('address-id');
        form.attr('action', `/admin/orders/${id}/updateAddress`);

        $('#edit-flat').val($(this).data('flat'));
        $('#edit-apartment').val($(this).data('name'));
        $('#edit-locality').val($(this).data('locality'));
        $('#edit-landmark').val($(this).data('landmark'));
        $('#edit-pincode').val($(this).data('pincode'));
        $('#edit-city').val($(this).data('city'));
        $('#edit-state').val($(this).data('state'));
    });

    // Submit Edit Address
    $('#edit-address-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: this.action,
            type: 'POST',
            data: $(this).serialize(),
            success: () => {
                Swal.fire('Updated', 'Address updated.', 'success');
                $('#editAddressModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: () => {
                Swal.fire('Error', 'Failed to update address.', 'error');
            }
        });
    });

    // Open Edit Dates Modal
    $('#file-datatable').on('click', '.edit-dates', function() {
        const subId = $(this).data('id');
        const startDate = $(this).data('start');
        const endDate = $(this).data('end');

        $('#sub-id').val(subId);
        $('#sub-start').val(moment(startDate).format('YYYY-MM-DD'));
        $('#sub-end').val(moment(endDate).format('YYYY-MM-DD'));
        $('#edit-dates-form').attr('action', `/admin/subscriptions/${subId}/updateDates`);

        new bootstrap.Modal($('#editDatesModal')[0]).show();
    });

    // Submit Edit Dates Form
    $('#edit-dates-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: this.action,
            type: 'POST',
            data: $(this).serialize(),
            success: () => {
                Swal.fire('Updated', 'Subscription dates updated.', 'success');
                $('#editDatesModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: () => {
                Swal.fire('Error', 'Failed to update dates.', 'error');
            }
        });
    });

    // Open Edit Rider Modal
    $('#file-datatable').on('click', '.edit-rider', function() {
        const subId = $(this).data('subscription');
        const orderId = $(this).data('order');
        const riderId = $(this).data('rider');

        $('#rider-sub-id').val(subId);
        $('#rider-select').val(riderId);
        $('#edit-rider-form').attr('action', `/admin/orders/${orderId}/updateRider`);

        new bootstrap.Modal($('#editRiderModal')[0]).show();
    });

    // Submit Edit Rider Form
    $('#edit-rider-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: this.action,
            type: 'POST',
            data: $(this).serialize(),
            success: () => {
                Swal.fire('Updated', 'Rider assigned successfully.', 'success');
                $('#editRiderModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: () => {
                Swal.fire('Error', 'Failed to assign rider.', 'error');
            }
        });
    });
});
</script>
@endsection

