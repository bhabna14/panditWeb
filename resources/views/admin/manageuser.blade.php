@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .stats-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        .stats-card:hover {
            transform: translateY(-4px);
        }

        .stats-icon {
            font-size: 32px;
            color: #7366ff;
        }

        .table td img {
            border-radius: 50%;
            object-fit: cover;
        }

        .action-icons a {
            margin: 0 6px;
            font-size: 16px;
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
    </style>
@endsection

@section('content')
    <!-- Dashboard Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stats-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Customers</h6>
                        {{-- <h4 class="fw-bold">{{ $totalCustomer }}</h4> --}}
                    </div>
                    <i class="fas fa-users stats-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Subscriptions Taken</h6>
                        {{-- <h4 class="fw-bold">{{ $totalSubscriptionTaken }}</h4> --}}
                    </div>
                    <i class="fas fa-box-open stats-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank">
                <div class="card stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Paused Subscriptions</h6>
                            {{-- <h4 class="fw-bold">{{ $pausedSubscriptions }}</h4> --}}
                        </div>
                        <i class="fas fa-pause-circle stats-icon"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card stats-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Payment Pending</h6>
                        {{-- <h4 class="fw-bold">{{ $paymentPending }}</h4> --}}
                    </div>
                    <i class="fas fa-wallet stats-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card custom-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Users</h5>
            <a href="{{ url('admin/add-user') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add User
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive export-table">
                <table id="file-datatable" class="table table-hover align-middle">
                    <thead class="table-light">
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
                                            alt="user" width="40" height="40">
                                        <div class="ms-2">
                                            <a href="{{ url('admin/user-profile/' . $user->id) }}" class="fw-bold">
                                                {{ $user->name ?? 'N/A' }}
                                            </a>
                                            <div class="text-muted small">{{ $user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->mobile_number }}</td>
                                <td>{{ $user->created_at->format('d M, Y') }}</td>
                                <td>
                                    {{-- Example placeholder --}}
                                    {{ $user->orders()->count() > 0 ? 'Active' : 'No Subscription' }}
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
