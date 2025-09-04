@extends('admin.layouts.apps')

@section('styles')
    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <style>
        /* ===== Polished section cards & headings ===== */
        .nu-card {
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, 0.06);
            background: #fff;
            padding: 18px;
            margin-bottom: 18px;
        }

        .nu-hero {
            background: linear-gradient(135deg, #f3f4ff 0%, #e9fbff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
        }

        .nu-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #e9ecf5;
            background: #fff;
            font-weight: 600;
            font-size: .9rem;
        }

        .nu-title {
            margin: 0 0 6px;
            font-weight: 700;
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

        .form-text.muted {
            color: #6b7280;
        }

        .rdiobox,
        .form-check {
            cursor: pointer;
        }

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

        @media (max-width: 575.98px) {
            .nu-hero .d-flex {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page header -->
    <div class="nu-hero d-flex justify-content-between align-items-center">
        <div>
            <h4 class="nu-title">Subscription for New User</h4>
            <div class="nu-chip">
                <span>Quick create • Clean layout</span>
            </div>
        </div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
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

    <form action="{{ route('saveNewUserOrder') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- User Details -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">1</span>
                User Details
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="user_type" class="form-label">User Type</label>
                    <select name="user_type" id="user_type" class="form-control" required>
                        <option value="">Select user type</option>
                        <option value="normal">Normal</option>
                        <option value="vip">VIP</option>
                    </select>
                    <div class="form-text muted">VIP users can be prioritized in service and support.</div>
                </div>
                <div class="col-md-4">
                    <label for="name" class="form-label">User Name</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter full name"
                        required>
                </div>
                <div class="col-md-4">
                    <label for="mobile_number" class="form-label">Phone Number</label>
                    <input type="text" name="mobile_number" class="form-control" id="mobile_number"
                        placeholder="10-digit phone" inputmode="numeric" pattern="[0-9]{10}" required>
                </div>
            </div>
        </div>

        <!-- Address Details -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">2</span>
                Address Details
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label d-block mb-1">Place Category</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="individual" name="place_category"
                                value="Individual" required>
                            <label class="form-check-label" for="individual">Individual</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="apartment" name="place_category"
                                value="Apartment">
                            <label class="form-check-label" for="apartment">Apartment</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="business" name="place_category"
                                value="Business">
                            <label class="form-check-label" for="business">Business</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="temple" name="place_category"
                                value="Temple">
                            <label class="form-check-label" for="temple">Temple</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="apartment_flat_plot" class="form-label">Apartment / Flat / Plot</label>
                    <input type="text" class="form-control" id="apartment_flat_plot" name="apartment_flat_plot"
                        placeholder="e.g., A-302, Lotus Enclave" required>
                </div>
                <div class="col-md-6">
                    <label for="landmark" class="form-label">Landmark</label>
                    <input type="text" class="form-control" id="landmark" name="landmark"
                        placeholder="Nearby landmark" required>
                </div>

                <div class="col-md-4">
                    <label for="locality" class="form-label">Locality</label>
                    <select class="form-control select2" id="locality" name="locality" required>
                        <option value="">Select Locality</option>
                        @foreach ($localities as $locality)
                            <option value="{{ $locality->unique_code }}" data-locality-id="{{ $locality->id }}"
                                data-pincode="{{ $locality->pincode }}">
                                {{ $locality->locality_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="apartment_name" class="form-label">Apartment Name</label>
                    <select class="form-control select2" id="apartment_name" name="apartment_name">
                        <option value="">Select Apartment</option>
                    </select>
                    <div class="form-text muted">Populated automatically from the selected locality.</div>
                </div>

                <div class="col-md-4">
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Auto-filled"
                        readonly required>
                </div>

                <div class="col-md-6">
                    <label for="city" class="form-label">Town / City</label>
                    <input type="text" class="form-control" id="city" name="city"
                        placeholder="Enter town/city" required>
                </div>
                <div class="col-md-6">
                    <label for="state" class="form-label">State</label>
                    <select name="state" class="form-control" id="state" required>
                        <option value="Odisha" selected>Odisha</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label d-block mb-1">Address Type</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input name="address_type" id="addr_home" class="form-check-input" type="radio"
                                value="Home">
                            <label class="form-check-label" for="addr_home">Home</label>
                        </div>
                        <div class="form-check">
                            <input name="address_type" id="addr_work" class="form-check-input" type="radio"
                                value="Work">
                            <label class="form-check-label" for="addr_work">Work</label>
                        </div>
                        <div class="form-check">
                            <input name="address_type" id="addr_other" class="form-check-input" type="radio"
                                value="Other" checked>
                            <label class="form-check-label" for="addr_other">Other</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">3</span>
                Product Details
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="product" class="form-label">Flower</label>
                    <select name="product_id" id="product" class="form-control select2" required>
                        <option value="">Select Flower</option>
                        @foreach ($flowers as $flower)
                            <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" id="start_date" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" id="end_date" required>
                    <div class="form-text muted">Will auto-calculate when Duration is selected (you can still override).
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="nu-card">
            <div class="section-title">
                <span class="badge bg-primary rounded-pill">4</span>
                Payment Details
            </div>
            <div class="row g-3">
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
                    <input type="number" min="0" step="1" name="paid_amount" class="form-control"
                        id="paid_amount" placeholder="Enter amount" required>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Payment Mode</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" selected>Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="nu-card d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        // Apartments grouped by locality_id from the controller
        const apartmentsByLocality = @json($apartmentsByLocality);

        // Helpers
        function setEndDateFromDuration() {
            const start = document.getElementById('start_date').value;
            const dur = parseInt(document.getElementById('duration').value || '0', 10);
            if (!start || !dur) return;

            const d = new Date(start + 'T00:00:00'); // local midnight to avoid TZ drift
            const startDay = d.getDate();
            const target = new Date(d);

            // Add months (handle month overflow)
            target.setMonth(target.getMonth() + dur);

            // We want inclusive period: end = (start + months) - 1 day
            target.setDate(target.getDate() - 1);

            // Format yyyy-mm-dd
            const yyyy = target.getFullYear();
            const mm = String(target.getMonth() + 1).padStart(2, '0');
            const dd = String(target.getDate()).padStart(2, '0');
            document.getElementById('end_date').value = `${yyyy}-${mm}-${dd}`;
        }

        function populateApartmentsFromLocality(selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const localityId = selectedOpt ? selectedOpt.getAttribute('data-locality-id') : null;
            const pincode = selectedOpt ? selectedOpt.getAttribute('data-pincode') : '';
            const apartmentSelect = document.getElementById('apartment_name');

            // Update pincode
            document.getElementById('pincode').value = pincode || '';

            // Reset apartments
            apartmentSelect.innerHTML = '<option value="">Select Apartment</option>';

            if (!localityId) {
                $(apartmentSelect).val('').trigger('change.select2');
                return;
            }

            const list = apartmentsByLocality[localityId] || [];
            if (list.length === 0) {
                apartmentSelect.innerHTML = '<option value="">No Apartments Available</option>';
                $(apartmentSelect).val('').trigger('change.select2');
                return;
            }

            list.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.apartment_name;
                opt.text = item.apartment_name;
                apartmentSelect.appendChild(opt);
            });
            $(apartmentSelect).trigger('change.select2');
        }

        // Init Select2
        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            // Locality -> apartments + pincode
            const localityEl = document.getElementById('locality');
            localityEl.addEventListener('change', function() {
                populateApartmentsFromLocality(this);
            });

            // Duration & Start Date -> auto end date
            document.getElementById('duration').addEventListener('change', setEndDateFromDuration);
            document.getElementById('start_date').addEventListener('change', setEndDateFromDuration);

            // Basic phone sanitation (numbers only)
            const phoneEl = document.getElementById('mobile_number');
            phoneEl.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            });

            // Auto-hide success message
            const msg = document.getElementById('Message');
            if (msg) setTimeout(() => msg.remove(), 3000);
        });
    </script>
@endsection
