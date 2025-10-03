@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .nu-card {
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, .06);
            background: #fff;
            padding: 18px;
            margin-bottom: 18px
        }

        .nu-hero {
            background: linear-gradient(135deg, #f3f4ff 0%, #e9fbff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px
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
            font-size: .9rem
        }

        .nu-title {
            margin: 0 0 6px;
            font-weight: 700
        }

        .section-title {
            font-weight: 700;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px
        }

        .section-title .badge {
            font-size: .75rem;
            padding: .35rem .5rem
        }

        .form-text.muted {
            color: #6b7280
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: .375rem
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 10px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px
        }

        @media (max-width:575.98px) {
            .nu-hero .d-flex {
                flex-direction: column;
                gap: 8px
            }
        }

        .hidden {
            display: none !important
        }

        /* Address cards */
        .addr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px
        }

        @media (max-width:1200px) {
            .addr-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media (max-width:576px) {
            .addr-grid {
                grid-template-columns: 1fr
            }
        }

        .addr-card {
            position: relative;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
            transition: box-shadow .2s, border-color .2s, transform .05s ease-in-out;
            background: #fff;
            min-height: 110px
        }

        .addr-card:hover {
            box-shadow: 0 8px 18px rgba(0, 0, 0, .06)
        }

        .addr-card.selected {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15)
        }

        .addr-card .addr-check {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: #fff;
            background: #fff
        }

        .addr-card.selected .addr-check {
            border-color: #4f46e5;
            background: #4f46e5
        }

        .addr-card .addr-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .72rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f1f5ff;
            color: #4f46e5;
            margin-bottom: 8px
        }

        .addr-card .addr-type {
            font-size: .78rem;
            color: #64748b;
            margin-top: 6px
        }

        .addr-card .addr-default {
            font-size: .72rem;
            color: #059669;
            margin-left: 8px
        }

        .addr-add {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #4f46e5;
            background: #f8fafc;
            border-style: dashed
        }

        .addr-add:hover {
            border-color: #4f46e5
        }

        .addr-text {
            font-size: .92rem;
            line-height: 1.3
        }
    </style>
@endsection

@section('content')
    <div class="nu-hero d-flex justify-content-between align-items-center mt-4">
        <div>
            <h4 class="nu-title">Subscription for New User</h4>
            <div class="nu-chip"><span>Quick create • Clean layout</span></div>
        </div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
        </ol>
    </div>

    <form action="{{ route('admin.saveNewUserOrder') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- USER PICKER --}}
        <div class="nu-card">
            <div class="section-title"><span class="badge bg-primary rounded-pill">1</span> User</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="user_select" class="form-label">Search User (mobile or name)</label>
                    <select id="user_select" name="user_select" class="form-control"></select>
                    <div class="form-text muted">Choose an existing user or select “➕ New user…”</div>
                    <input type="hidden" name="existing_user_id" id="existing_user_id" value="">
                </div>

                <div id="new_user_fields" class="col-12 hidden">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="new_user_name" class="form-label">New User Name</label>
                            <input type="text" class="form-control" id="new_user_name" name="new_user_name"
                                placeholder="Full name">
                        </div>
                        <div class="col-md-6">
                            <label for="new_user_mobile" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="new_user_mobile" name="new_user_mobile"
                                placeholder="10-digit phone" inputmode="numeric" pattern="[0-9]{10}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ADDRESS --}}
        <div class="nu-card">
            <div class="section-title"><span class="badge bg-primary rounded-pill">2</span> Address</div>

            <input type="hidden" name="address_mode" id="address_mode" value="new">

            {{-- Existing address card grid --}}
            <div id="existing_address_block" class="hidden">
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="form-text muted">Tap an address to use it, or add a new one.</div>
                    <button class="btn btn-outline-primary btn-sm" type="button" id="btn_add_new_address">➕ Add new
                        address</button>
                </div>
                <div class="addr-grid" id="address_cards">
                    {{-- Cards injected by JS --}}
                </div>
                <input type="hidden" name="existing_address_id" id="existing_address_id" value="">
            </div>

            {{-- New address form --}}
            <div id="address_form_block" class="row g-3">
                <div class="col-12">
                    <label class="form-label d-block mb-1">Place Category</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach (['Individual', 'Apartment', 'Business', 'Temple'] as $pc)
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="pc_{{ $pc }}"
                                    name="place_category" value="{{ $pc }}"
                                    {{ $pc === 'Individual' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pc_{{ $pc }}">{{ $pc }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="apartment_flat_plot" class="form-label">Apartment / Flat / Plot</label>
                    <input type="text" class="form-control" id="apartment_flat_plot" name="apartment_flat_plot"
                        placeholder="e.g., A-302, Lotus Enclave">
                </div>
                <div class="col-md-6">
                    <label for="landmark" class="form-label">Landmark</label>
                    <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Nearby landmark">
                </div>

                <div class="col-md-4">
                    <label for="locality" class="form-label">Locality</label>
                    <select class="form-control select2" id="locality" name="locality">
                        <option value="">Select Locality</option>
                        @foreach ($localities as $locality)
                            <option value="{{ $locality->unique_code }}" data-locality-key="{{ $locality->unique_code }}"
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
                        readonly>
                </div>

                <div class="col-md-6">
                    <label for="city" class="form-label">Town / City</label>
                    <input type="text" class="form-control" id="city" name="city">
                </div>
                <div class="col-md-6">
                    <label for="state" class="form-label">State</label>
                    <select name="state" class="form-control" id="state">
                        <option value="Odisha" selected>Odisha</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label d-block mb-1">Address Type</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach (['Home', 'Work', 'Other'] as $at)
                            <div class="form-check">
                                <input name="address_type" id="addr_{{ strtolower($at) }}" class="form-check-input"
                                    type="radio" value="{{ $at }}" {{ $at === 'Other' ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="addr_{{ strtolower($at) }}">{{ $at }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="btn_use_existing_addresses">⬅︎ Use
                        existing addresses</button>
                </div>
            </div>
        </div>

        {{-- PRODUCT --}}
        <div class="nu-card">
            <div class="section-title"><span class="badge bg-primary rounded-pill">3</span> Product Details</div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="product" class="form-label">Select Product</label>
                    <select name="product_id" id="product" class="form-control select2" required>
                        @foreach ($flowers as $flower)
                            <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="duration" class="form-label">Subscription Duration</label>
                    <select name="duration" id="duration" class="form-control">
                        <option value="1">1 month</option>
                        <option value="3">3 months</option>
                        <option value="6">6 months</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" id="start_date">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" id="end_date">
                    <div class="form-text muted">Auto-calculates from Duration (you can override).</div>
                </div>
            </div>
        </div>

        {{-- PAYMENT --}}
        <div class="nu-card">
            <div class="section-title"><span class="badge bg-primary rounded-pill">4</span> Payment Details</div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="paid_amount" class="form-label">Amount</label>
                    <input type="number" min="0" step="1" name="paid_amount" class="form-control"
                        id="paid_amount" placeholder="Enter amount">
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Payment Mode</label>
                    <select name="payment_method" id="payment_method" class="form-control">
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="Razorpay">Razorpay</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="active" selected>Active</option>
                        <option value="pending">Pending</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="paid" selected>Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="nu-card d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true
        });
        const showToast = (icon, title) => Toast.fire({
            icon,
            title
        });

        function showValidationErrors(errorsArray) {
            if (!errorsArray?.length) return;
            const html = '<ul style="text-align:left;margin:0;padding-left:18px;">' + errorsArray.map(e => `<li>${e}</li>`)
                .join('') + '</ul>';
            Swal.fire({
                icon: 'error',
                title: 'Please fix the following',
                html,
                confirmButtonText: 'OK'
            });
        }

        function setEndDateFromDuration() {
            const startStr = document.getElementById('start_date').value;
            const dur = (document.getElementById('duration').value || '').trim();

            if (!startStr || !dur) return;

            // Map duration (months) to exact days
            const daysMap = {
                '1': 30,
                '3': 90,
                '6': 180
            };
            const totalDays = daysMap[dur];
            if (!totalDays) return;

            // Inclusive range: end = start + (days - 1)
            const d = new Date(startStr + 'T00:00:00');
            if (isNaN(d)) return;

            const target = new Date(d);
            target.setDate(target.getDate() + (totalDays - 1));

            const yyyy = target.getFullYear();
            const mm = String(target.getMonth() + 1).padStart(2, '0');
            const dd = String(target.getDate()).padStart(2, '0');
            document.getElementById('end_date').value = `${yyyy}-${mm}-${dd}`;
        }

        async function populateApartmentsFromLocality(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            const localityKey = opt ? opt.getAttribute('data-locality-key') : null;
            const pincode = opt ? opt.getAttribute('data-pincode') : '';
            const $apartmentSelect = $('#apartment_name');

            document.getElementById('pincode').value = pincode || '';
            $apartmentSelect.empty().append(new Option('Select Apartment', '', true, false)).trigger('change');

            if (!localityKey) return;

            try {
                const urlTemplate = @json(route('admin.apartments.byLocality', ['uniqueCode' => '___CODE___']));
                const url = urlTemplate.replace('___CODE___', encodeURIComponent(localityKey));
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();

                if (data.ok && Array.isArray(data.data) && data.data.length) {
                    data.data.forEach(name => {
                        const clean = String(name).trim();
                        if (!clean || clean.toUpperCase() === 'NULL') return;
                        $apartmentSelect.append(new Option(clean, clean, false, false));
                    });
                } else {
                    $apartmentSelect.append(new Option('No Apartments Available', '', false, false));
                }
                $apartmentSelect.trigger('change');
            } catch (e) {
                $apartmentSelect.append(new Option('Failed to load apartments', '', false, false)).trigger('change');
                showToast('error', 'Failed to load apartments');
            }
        }

        /* ------- NEW: Address cards ------- */
        function addressCardHtml(a) {
            // a = {id,label,is_default?,type?}
            const parts = a.label ? a.label.split(',').map(s => s.trim()) : [];
            const type = a.type || '';
            const isDefault = !!a.is_default;
            const id = a.id;

            return `
            <div class="addr-card" data-id="${id}" role="button" tabindex="0" aria-label="Select address">
                <div class="addr-check">✓</div>
                <div class="addr-badge">${type || 'Address'}</div>
                <div class="addr-text">${parts.join(', ') || '—'}</div>
                <div class="addr-type">${type || ''}${isDefault ? '<span class="addr-default">• Default</span>' : ''}</div>
            </div>`;
        }

        function addNewAddressCardHtml() {
            return `
            <div class="addr-card addr-add" id="addr_add_card" role="button" tabindex="0" aria-label="Add new address">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span><strong>Add new address</strong></span>
            </div>`;
        }

        function setSelectedAddressCard(id) {
            document.querySelectorAll('.addr-card').forEach(el => {
                el.classList.toggle('selected', String(el.dataset.id) === String(id));
            });
        }

        function showExistingAddressBlock(show) {
            document.getElementById('existing_address_block').classList.toggle('hidden', !show);
            document.getElementById('address_form_block').classList.toggle('hidden', show);
            document.getElementById('address_mode').value = show ? 'existing' : 'new';
        }

        // User mode
        function switchToNewUser() {
            $('#existing_user_id').val('');
            $('#new_user_fields').removeClass('hidden');
            showExistingAddressBlock(false); // show form by default for new user
        }

        function switchToExistingUser(userid) {
            $('#existing_user_id').val(userid);
            $('#new_user_fields').addClass('hidden');
            loadUserAddresses(userid);
        }

        async function loadUserAddresses(userid) {
            const url = @json(route('admin.users.addresses', ['userid' => '___ID___'])).replace('___ID___', encodeURIComponent(userid));
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            let ok = false,
                data = [];
            if (res.ok) {
                const json = await res.json();
                ok = !!json.ok;
                data = json.data || [];
            }

            const grid = document.getElementById('address_cards');
            grid.innerHTML = '';

            if (ok && data.length) {
                // Map labels into nicer structure (optional type detection)
                data.forEach((a, idx) => {
                    // try to guess "type" and default flag from the label suffix
                    const isDefault = (a.label || '').includes('(Default)');
                    const cleanLabel = (a.label || '').replace('(Default)', '').trim();
                    grid.insertAdjacentHTML('beforeend', addressCardHtml({
                        id: a.id,
                        label: cleanLabel,
                        is_default: isDefault,
                        type: 'Saved'
                    }));
                });
                grid.insertAdjacentHTML('beforeend', addNewAddressCardHtml());
                showExistingAddressBlock(true);

                // Select first by default
                const firstId = data[0]?.id;
                if (firstId) {
                    document.getElementById('existing_address_id').value = firstId;
                    setSelectedAddressCard(firstId);
                }

                // Click handlers
                grid.querySelectorAll('.addr-card').forEach(card => {
                    card.addEventListener('click', () => {
                        if (card.id === 'addr_add_card') {
                            // Switch to new address form
                            showExistingAddressBlock(false);
                            return;
                        }
                        const id = card.dataset.id;
                        document.getElementById('existing_address_id').value = id;
                        setSelectedAddressCard(id);
                    });
                    card.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            card.click();
                        }
                    });
                });

            } else {
                // No saved addresses → show form
                showExistingAddressBlock(false);
            }
        }

        $(function() {
            // Select2
            $('.select2').select2({
                width: '100%'
            });

            // USER SELECT2 (remote)
            $('#user_select').select2({
                width: '100%',
                placeholder: 'Search by mobile or name',
                ajax: {
                    delay: 200,
                    url: @json(route('admin.users.search')),
                    dataType: 'json',
                    data: params => ({
                        q: params.term || ''
                    }),
                    processResults: data => data
                },
                minimumInputLength: 0
            });

            // user picked
            $('#user_select').on('select2:select', function(e) {
                const sel = e.params.data;
                if (!sel) return;
                if (sel.id === 'NEW') {
                    switchToNewUser();
                } else {
                    switchToExistingUser(sel.id);
                }
            });

            // “Add new address” (from existing block)
            $('#btn_add_new_address').on('click', () => showExistingAddressBlock(false));

            // “Use existing addresses” (from form)
            $('#btn_use_existing_addresses').on('click', () => {
                const uid = $('#existing_user_id').val();
                if (uid) loadUserAddresses(uid);
            });

            // Locality → apartments + pincode
            $('#locality').on('change select2:select', function() {
                populateApartmentsFromLocality(this);
            });
            if (document.getElementById('locality').value) {
                populateApartmentsFromLocality(document.getElementById('locality'));
            }

            // Duration → end date
            document.getElementById('duration').addEventListener('change', setEndDateFromDuration);
            document.getElementById('start_date').addEventListener('change', setEndDateFromDuration);

            // New user phone mask
            const mask10 = el => el && el.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            });
            mask10(document.getElementById('new_user_mobile'));

            // Session toasts & errors
            @if ($errors->any())
                showValidationErrors([
                    @foreach ($errors->all() as $e)
                        @json($e),
                    @endforeach
                ]);
            @endif
            @if (session('success'))
                showToast('success', @json(session('success')));
            @endif
            @if (session('error'))
                showToast('error', @json(session('error')));
            @endif
            @if (session('warning'))
                showToast('warning', @json(session('warning')));
            @endif
            @if (session('info'))
                showToast('info', @json(session('info')));
            @endif
        });
    </script>
@endsection
