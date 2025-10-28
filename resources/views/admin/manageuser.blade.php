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
            border: 1px solid rgb(186, 185, 185);
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
            <div class="stats-card d-flex justify-content-between align-items-center border">
                <div>
                    <div class="stats-title">Total Customers</div>
                    <div class="stats-value">{{ $totalCustomer }}</div>
                </div>
                <div class="stats-icon bg-users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card d-flex justify-content-between align-items-center border">
                <div>
                    <div class="stats-title">Subscriptions Taken</div>
                    <div class="stats-value">{{ $totalSubscriptionTaken }}</div>
                </div>
                <div class="stats-icon bg-subscription">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index', ['filter' => 'discontinued']) }}" target="_blank">
                <div class="stats-card d-flex justify-content-between align-items-center border">
                    <div>
                        <div class="stats-title">Discontinued Customers</div>
                        <div class="stats-value">{{ $discontinuedCustomer }}</div>
                    </div>
                    <div class="stats-icon bg-paused">
                        <i class="fas fa-user-slash"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('payment.collection.index') }}" target="_blank">

                <div class="stats-card d-flex justify-content-between align-items-center border">
                    <div>
                        <div class="stats-title">Payment Pending</div>
                        <div class="stats-value">{{ $paymentPending }}</div>
                    </div>
                    <div class="stats-icon bg-pending">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card custom-card mt-4">

        <div class="card-body">
            <div class="table-responsive export-table">
                <table id="file-datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Registered</th>
                            <th>Last Login</th> {{-- NEW --}}
                            <th>Device</th> {{-- NEW --}}
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
                                <td>{{ optional($user->created_at)->format('d M, Y') }}</td>

                                {{-- NEW: Last Login --}}
                                <td>
                                    @php
                                        $tz = config('app.timezone', 'Asia/Kolkata');
                                        $ll = $user->last_login_time
                                            ? \Carbon\Carbon::parse($user->last_login_time)->timezone($tz)
                                            : null;
                                    @endphp
                                    {{ $ll ? $ll->format('d M, Y h:i A') : '—' }}
                                </td>

                                {{-- NEW: Device Model --}}
                                <td>{{ $user->last_device_model ?: '—' }}</td>

                                <td>
                                    @if ($user->subscriptions->count() > 0)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>

                                <td class="text-center action-icons">
                                    <a class="btn btn-warning btn-sm text-center"
                                        href="{{ route('showCustomerDetails', $user->userid) }}">
                                        View Details
                                    </a>

                                    <button type="button" class="btn btn-sm btn-primary editUserBtn" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}" data-phone="{{ $user->mobile_number }}"
                                        data-user_type="{{ $user->user_type }}"
                                        data-userphoto="{{ $user->userphoto ? asset('storage/' . $user->userphoto) : asset('front-assets/img/images.jfif') }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editUserForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="id">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="editName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="editEmail">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="mobile_number" id="editPhone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">User Type</label>
                                <input type="text" class="form-control" name="user_type" id="editUserType"
                                    placeholder="e.g. customer, admin">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" name="userphoto" id="editPhoto"
                                    accept="image/*">
                                <small class="text-muted">JPG/PNG/WebP up to 2MB.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label d-block">Preview</label>
                            <img id="editPhotoPreview" src="" alt="Preview"
                                class="img-fluid rounded shadow-sm border"
                                style="max-height: 220px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="me-2" id="editSubmitSpinner" style="display:none;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            // route template: we'll replace :id at runtime
            const updateRouteTemplate = @json(route('admin.users.update', ':id'));

            // Open modal and populate
            document.querySelectorAll('.editUserBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name || '';
                    const email = this.dataset.email || '';
                    const phone = this.dataset.phone || '';
                    const user_type = this.dataset.user_type || '';
                    const photoUrl = this.dataset.userphoto || '';

                    // Fill fields
                    document.getElementById('editUserId').value = id;
                    document.getElementById('editName').value = name;
                    document.getElementById('editEmail').value = email;
                    document.getElementById('editPhone').value = phone;
                    document.getElementById('editUserType').value = user_type;
                    document.getElementById('editPhotoPreview').src = photoUrl;

                    // Set form action
                    const action = updateRouteTemplate.replace(':id', id);
                    document.getElementById('editUserForm').setAttribute('action', action);
                });
            });

            // Live preview on file change
            const photoInput = document.getElementById('editPhoto');
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        document.getElementById('editPhotoPreview').src = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Show spinner on submit
            const form = document.getElementById('editUserForm');
            form.addEventListener('submit', function() {
                document.getElementById('editSubmitSpinner').style.display = 'inline-block';
            });

            // Flash messages (success/error) -> SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    timer: 1800,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation error',
                    html: `{!! implode('<br>', $errors->all()) !!}`
                });
            @endif
        })();
    </script>
@endsection
