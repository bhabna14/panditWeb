@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* ===== Dashboard Cards ===== */
        .stats-card {
            border: none;
            border-radius: 14px;
            padding: 20px;
            background: linear-gradient(135deg, #f9f9f9, #ffffff);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        .stats-card:hover {
            transform: translateY(-6px);
        }

        .stats-icon {
            font-size: 36px;
            padding: 12px;
            border-radius: 12px;
            color: #fff;
        }

        .stats-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 6px;
        }

        .stats-value {
            font-size: 22px;
            font-weight: bold;
        }

        /* Different icon colors */
        .bg-users {
            background: #7366ff;
        }

        .bg-subscription {
            background: #28a745;
        }

        .bg-paused {
            background: #ffc107;
        }

        .bg-pending {
            background: #dc3545;
        }

        /* ===== Users Table ===== */
        .custom-card {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table thead {
            background: #f1f1f9;
            font-weight: 600;
        }

        .table td img {
            border-radius: 50%;
            object-fit: cover;
            width: 42px;
            height: 42px;
        }

        .user-name {
            font-weight: 600;
            color: #333;
        }

        .user-email {
            font-size: 12px;
            color: #777;
        }

        .action-icons a {
            margin: 0 8px;
            font-size: 18px;
            transition: all 0.2s;
        }

        .action-icons a.view {
            color: #0d6efd;
        }

        .action-icons a.edit {
            color: #ffc107;
        }

        .action-icons a.delete {
            color: #dc3545;
        }

        .action-icons a:hover {
            transform: scale(1.2);
        }
    </style>
@endsection

@section('content')
    <!-- Dashboard Stats -->
    <div class="row g-3 mb-4 mt-3">
        <div class="col-md-3">
            <div class="stats-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-title">Total Customers</div>
                    <div class="stats-value">{{ $totalCustomer ?? 0 }}</div>
                </div>
                <div class="stats-icon bg-users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-title">Subscriptions Taken</div>
                    <div class="stats-value">{{ $totalSubscriptionTaken ?? 0 }}</div>
                </div>
                <div class="stats-icon bg-subscription">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank"
                class="text-decoration-none">
                <div class="stats-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-title">Paused Subscriptions</div>
                        <div class="stats-value">{{ $pausedSubscriptions ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-paused">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="stats-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-title">Payment Pending</div>
                    <div class="stats-value">{{ $paymentPending ?? 0 }}</div>
                </div>
                <div class="stats-icon bg-pending">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card custom-card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Users</h5>
            <a href="{{ url('admin/add-user') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add User
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive export-table">
                <table id="file-datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Registered</th>
                            <th>Subscription</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($user->userphoto ? 'storage/' . $user->userphoto : 'front-assets/img/images.jfif') }}"
                                            alt="user">
                                        <div class="ms-2">
                                            <div class="user-name">
                                                <a
                                                    href="{{ url('admin/user-profile/' . $user->id) }}">{{ $user->name ?? 'N/A' }}</a>
                                            </div>
                                            <div class="user-email">{{ $user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->mobile_number }}</td>
                                <td>{{ $user->created_at->format('d M, Y') }}</td>
                                <td>
                                    @if ($user->orders()->count() > 0)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">No Subscription</span>
                                    @endif
                                </td>
                                <td class="text-center action-icons">
                                    <a href="{{ url('admin/user-profile/' . $user->id) }}" class="view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('admin/edit-user/' . $user->id) }}" class="edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('admin/delete-user/' . $user->id) }}" class="delete"
                                        onclick="return confirm('Are you sure you want to delete this user?');"
                                        title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
@endsection
