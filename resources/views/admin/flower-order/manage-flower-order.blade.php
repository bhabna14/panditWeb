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
    <table id="file-datatable" class="table table-bordered">
        <thead>
            <tr>
                <th>Customer Details</th>
                <th>Purchase Date</th>
                <th>Duration</th>
                <th>Price</th>
                <th>Status</th>
                <th>Assigned Rider</th>
                <th>Referred By</th>
                <th>Subscription</th>
            </tr>
        </thead>
        <tbody>
            <!-- Handled by DataTable AJAX -->
        </tbody>
    </table>
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

    <!-- Updated JavaScript -->
@endsection
