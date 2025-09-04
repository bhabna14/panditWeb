@extends('admin.layouts.apps')

@section('styles')
    <!-- Select2 (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />

    <style>
        /* ====== Layout polish ====== */
        .nu-hero {
            background: linear-gradient(135deg, #f3f4ff 0%, #e9fbff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nu-hero h4 {
            margin: 0;
            font-weight: 700;
        }

        .nu-card {
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, .06);
            background: #fff;
            padding: 18px;
            margin-bottom: 18px;
        }

        .section-title {
            font-weight: 700;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .section-title .badge {
            font-size: .75rem;
            padding: .35rem .5rem;
        }

        /* ====== Address cards ====== */
        .address-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 12px;
        }

        @media (max-width: 575.98px) {
            .address-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        @media (min-width: 576px) and (max-width: 991.98px) {
            .address-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        @media (min-width: 992px) {
            .address-grid {
                grid-template-columns: repeat(12, 1fr);
            }
        }

        .address-col {
            grid-column: span 12;
        }

        @media (min-width: 576px) {
            .address-col {
                grid-column: span 6;
            }
        }

        @media (min-width: 992px) {
            .address-col {
                grid-column: span 4;
            }
        }

        .address-card {
            border: 1px solid #e9ecf5;
            border-radius: 12px;
            height: 100%;
            transition: box-shadow .2s ease, border-color .2s ease;
            padding: 14px;
            position: relative;
        }

        .address-card:hover {
            box-shadow: 0 10px 22px rgba(0, 0, 0, .06);
            border-color: #d7def0;
        }

        .address-radio {
            position: absolute;
            top: 12px;
            right: 12px;
            transform: scale(1.1);
        }

        .address-type {
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .badge-default {
            background: #e8f7ee;
            color: #0f7a3a;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: .75rem;
        }

        .address-text {
            margin: 0;
            color: #374151;
            line-height: 1.4;
        }

        /* ====== Select2 polish ====== */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: .375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        /* ====== Skeleton loader for addresses ====== */
        .skeleton {
            background: linear-gradient(90deg, #f2f4f8 25%, #e9edf5 37%, #f2f4f8 63%);
            background-size: 400% 100%;
            animation: shimmer 1.2s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .sk-line {
            height: 12px;
            margin-bottom: 8px;
        }

        .sk-line.lg {
            height: 18px;
            width: 60%;
        }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="nu-hero">
        <div>
            <h4>Existing User Order</h4>
            <div class="text-muted small">Create a subscription for an existing user</div>
        </div>
        <ol class="breadcrumb mb-0 d-flex align-items-center gap-2">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
        </ol>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger nu-card">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="mb-1">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success nu-card" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger nu-card">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('saveDemoOrderDetails') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- 1) User -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">1</span>
                Select User
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label for="userid" class="form-label">User</label>
                    <select class="form-control select2" id="userid" name="userid" required>
                        <option value="">Search by User ID or phone</option>
                        @foreach ($user_details as $user)
                            <option value="{{ $user->userid }}">
                                {{ $user->userid }} — ({{ $user->mobile_number }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">Start typing to quickly find a user.</div>
                </div>
            </div>
        </div>

        <!-- 2) Address -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">2</span>
                Address
            </div>
            <div id="addressContainer" class="your-address-list">
                <p class="text-muted mb-0">Select a user to load addresses.</p>
            </div>
        </div>

        <!-- 3) Subscription & Payment -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">3</span>
                Subscription & Payment
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" id="start_date" required>
                </div>

                <div class="col-md-3">
                    <label for="duration" class="form-label">Duration</label>
                    <select name="duration" id="duration" class="form-control" required>
                        <option value="1">1 month</option>
                        <option value="3">3 months</option>
                        <option value="6">6 months</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="paid_amount" class="form-label">Paid Amount</label>
                    <input type="number" name="paid_amount" class="form-control" id="paid_amount" min="0"
                        step="1" placeholder="0" required>
                </div>

                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Payment Mode</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="form-control" required>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Subscription Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="nu-card d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">Submit</button>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Select2 (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

    <script>
        // ===== Helpers =====
        function addressSkeleton(count = 3) {
            let grid = '<div class="address-grid">';
            for (let i = 0; i < count; i++) {
                grid += `
                    <div class="address-col">
                        <div class="address-card">
                            <div class="skeleton sk-line lg" style="width: 40%;"></div>
                            <div class="skeleton sk-line" style="width: 100%;"></div>
                            <div class="skeleton sk-line" style="width: 90%;"></div>
                            <div class="skeleton sk-line" style="width: 80%;"></div>
                        </div>
                    </div>`;
            }
            grid += `</div>`;
            return grid;
        }

        function renderAddresses(addresses) {
            if (!addresses || addresses.length === 0) {
                return '<p class="text-muted mb-0">No addresses found for the selected user.</p>';
            }

            let html = '<div class="address-grid">';
            addresses.forEach((a) => {
                const badge = a.default ? '<span class="badge-default">Default</span>' : '';
                html += `
                <div class="address-col">
                    <label class="address-card w-100">
                        <input type="radio" class="address-radio" name="address_id" value="${a.id}" required>
                        <div class="address-type">
                            <span>${a.address_type || 'Address'}</span>${badge}
                        </div>
                        <p class="address-text">
                            ${a.apartment_flat_plot ?? ''}${a.apartment_flat_plot ? ',<br>' : ''}
                            ${a.locality_name ?? ''}${a.locality_name ? ',<br>' : ''}
                            ${a.landmark ?? ''}${a.landmark ? '<br>' : ''}
                            ${a.city ?? ''}${a.city ? ', ' : ''}${a.state ?? ''}${(a.city || a.state) ? ', ' : ''}${a.country ?? ''}<br>
                            ${a.pincode ?? ''}
                        </p>
                    </label>
                </div>`;
            });
            html += '</div>';
            return html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Init Select2
            const $user = $('#userid').select2({
                placeholder: 'Search by User ID or phone',
                allowClear: true,
                width: '100%'
            });

            // Auto dismiss success message
            const msg = document.getElementById('Message');
            if (msg) setTimeout(() => msg.remove(), 3000);

            // Input sanitation for amount
            const amt = document.getElementById('paid_amount');
            amt.addEventListener('input', function() {
                const v = this.value.replace(/[^\d]/g, '');
                this.value = v;
            });

            // Load addresses when user changes
            const addressContainer = document.getElementById('addressContainer');

            $user.on('change', function() {
                const userId = this.value;

                if (!userId) {
                    addressContainer.innerHTML =
                        '<p class="text-muted mb-0">Select a user to load addresses.</p>';
                    return;
                }

                addressContainer.innerHTML = addressSkeleton(3);

                fetch(`/admin/get-user-addresses/${userId}`)
                    .then(res => res.json())
                    .then(data => {
                        const content = renderAddresses(data.addresses || []);
                        addressContainer.innerHTML = content;
                    })
                    .catch(err => {
                        console.error('Error fetching addresses:', err);
                        addressContainer.innerHTML =
                            '<p class="text-danger mb-0">Failed to load addresses. Please try again.</p>';
                    });
            });
        });
    </script>
@endsection
