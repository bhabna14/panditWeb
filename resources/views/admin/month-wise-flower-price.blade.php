@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .card-shadow {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .06);
            border-radius: 16px;
        }

        .pill {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 999px;
            background: #f1f5f9;
        }

        .flower-chip {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .flower-item.disabled {
            opacity: .35;
            pointer-events: none;
        }

        .required::after {
            content: " *";
            color: #dc2626;
        }

        .floating-total {
            font-weight: 600;
        }

        .grid-gap {
            row-gap: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Flower Registration</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            </ol>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.flowerRegistration.store') }}" id="flowerRegForm">
        @csrf

        <div class="card card-shadow mb-4">
            <div class="card-body">
                <div class="row grid-gap">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label required">Vendor</label>
                        <select id="vendor_id" name="vendor_id" class="form-control select2" required
                            data-placeholder="Select a vendor">
                            <option value=""></option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->vendor_id }}" {{ old('vendor_id') === $v->vendor_id ? 'selected' : '' }}>
                                    {{ $v->vendor_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Only flowers assigned to the selected vendor can be chosen.</small>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="w-100">
                            <label class="form-label mb-1">Quick Search</label>
                            <input type="text" id="flowerSearch" class="form-control"
                                placeholder="Search flowers by name...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flowers -->
        <div class="card card-shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fw-semibold">Flowers</div>
                        <small class="text-muted">Check one or more flowers. For each checked flower, fill details
                            below.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select all</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">Clear</button>
                    </div>
                </div>

                <div class="row" id="flowersGrid">
                    @foreach ($flowers as $f)
                        @php
                            $checked = in_array($f->product_id, old('flower_ids', []));
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-2 flower-item"
                            data-name="{{ strtolower($f->name) }}" data-id="{{ $f->product_id }}">
                            <label class="flower-chip w-100">
                                <input class="form-check-input me-2 flower-checkbox" type="checkbox" name="flower_ids[]"
                                    value="{{ $f->product_id }}" {{ $checked ? 'checked' : '' }}>
                                <span>{{ $f->name }}
                                    @if (!empty($f->odia_name))
                                        <small class="text-muted">({{ $f->odia_name }})</small>
                                    @endif
                                </span>
                                <span class="ms-auto pill">#{{ $f->product_id }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div id="noFlowersMsg" class="alert alert-warning d-none mt-2">
                    No flowers are assigned to this vendor.
                </div>
            </div>
        </div>

        <!-- Per-flower details will appear here -->
        <div id="flowerDetailsContainer"></div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg">Save Registration</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const vendors = @json($vendors);
        const vendorMap = {};
        vendors.forEach(v => vendorMap[v.vendor_id] = (v.flower_ids || []).map(Number));

        const allFlowers = @json($flowers);
        const units = @json($units);

        const oldData = {
            start_date: @json(old('start_date', [])),
            end_date: @json(old('end_date', [])),
            quantity: @json(old('quantity', [])),
            unit_id: @json(old('unit_id', [])),
            price: @json(old('price', [])),
            selected: @json(old('flower_ids', [])),
            vendor_id: @json(old('vendor_id'))
        };

        function buildFlowerDetailCard(flowerId) {
            const f = allFlowers.find(x => Number(x.product_id) === Number(flowerId));
            const name = f ? f.name : `Flower #${flowerId}`;

            const sd = oldData.start_date[flowerId] || '';
            const ed = oldData.end_date[flowerId] || '';
            const qty = oldData.quantity[flowerId] || '';
            const uid = oldData.unit_id[flowerId] || '';
            const pr = oldData.price[flowerId] || '';

            return `
            <div class="card card-shadow mb-3 flower-detail" data-id="${flowerId}">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="h6 mb-0">${name}</div>
                        <div class="floating-total">
                            Total: <span class="total-val" data-id="${flowerId}">₹0.00</span>
                        </div>
                    </div>
                    <div class="row grid-gap">
                        <div class="col-md-3">
                            <label class="form-label required">Start date</label>
                            <input type="date" class="form-control" name="start_date[${flowerId}]" value="${sd}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">End date</label>
                            <input type="date" class="form-control" name="end_date[${flowerId}]" value="${ed}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label required">Qty</label>
                            <input type="number" step="0.01" min="0" class="form-control qty-input"
                                   name="quantity[${flowerId}]" value="${qty}" required data-id="${flowerId}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label required">Unit</label>
                            <select class="form-control" name="unit_id[${flowerId}]" required>
                                <option value="">Select unit</option>
                                ${units.map(u => `<option value="${u.id}" ${Number(uid)===Number(u.id) ? 'selected':''}>${u.unit_name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label required">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control price-input"
                                       name="price[${flowerId}]" value="${pr}" required data-id="${flowerId}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }

        function refreshTotals(flowerId) {
            const qty = parseFloat(document.querySelector(`input.qty-input[data-id="${flowerId}"]`)?.value || 0);
            const price = parseFloat(document.querySelector(`input.price-input[data-id="${flowerId}"]`)?.value || 0);
            const total = isFinite(qty * price) ? (qty * price) : 0;
            const el = document.querySelector(`.total-val[data-id="${flowerId}"]`);
            if (el) el.textContent = `₹${total.toFixed(2)}`;
        }

        function renderDetailsForSelected() {
            const container = document.getElementById('flowerDetailsContainer');
            container.innerHTML = '';
            document.querySelectorAll('.flower-checkbox:checked').forEach(cb => {
                const fid = cb.value;
                container.insertAdjacentHTML('beforeend', buildFlowerDetailCard(fid));
                refreshTotals(fid);
            });
        }

        function filterFlowersByVendor(vendorId) {
            const allowed = vendorMap[vendorId] || [];
            const grid = document.getElementById('flowersGrid');
            let any = false;

            // Uncheck everything if vendor changes
            document.querySelectorAll('.flower-checkbox').forEach(cb => {
                if (!allowed.includes(Number(cb.value))) {
                    cb.checked = false;
                }
            });

            // Show only allowed flowers
            grid.querySelectorAll('.flower-item').forEach(item => {
                const id = Number(item.dataset.id);
                if (allowed.includes(id)) {
                    item.classList.remove('disabled');
                    item.style.display = '';
                    any = true;
                } else {
                    item.classList.add('disabled');
                    item.style.display = 'none';
                }
            });

            document.getElementById('noFlowersMsg').classList.toggle('d-none', any);
            renderDetailsForSelected();
        }

        function searchFlowers(term) {
            const q = term.trim().toLowerCase();
            document.querySelectorAll('#flowersGrid .flower-item:not(.disabled)').forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Select2
            const sel = $('.select2').select2({
                width: '100%',
                allowClear: true,
                placeholder: $(this).data('placeholder')
            });

            // Handle vendor change
            const vendorSel = document.getElementById('vendor_id');
            vendorSel.addEventListener('change', (e) => {
                filterFlowersByVendor(e.target.value);
            });

            // Initial (old input) rehydrate
            if (oldData.vendor_id) {
                filterFlowersByVendor(oldData.vendor_id);
                // ensure old selected details render
                renderDetailsForSelected();
            } else {
                // Hide all until vendor chosen
                document.querySelectorAll('#flowersGrid .flower-item').forEach(i => i.style.display = 'none');
                document.getElementById('noFlowersMsg').classList.add('d-none');
            }

            // Search
            document.getElementById('flowerSearch').addEventListener('input', (e) => searchFlowers(e.target.value));

            // Check/uncheck events
            document.getElementById('flowersGrid').addEventListener('change', (e) => {
                if (e.target.classList.contains('flower-checkbox')) {
                    renderDetailsForSelected();
                }
            });

            // Totals
            document.body.addEventListener('input', (e) => {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains(
                    'price-input')) {
                    const id = e.target.getAttribute('data-id');
                    refreshTotals(id);
                }
            });

            // Select all / Clear — only visible items (allowed + filtered)
            document.getElementById('selectAll').addEventListener('click', () => {
                document.querySelectorAll('#flowersGrid .flower-item:not(.disabled)')
                    .forEach(item => {
                        if (item.style.display !== 'none') {
                            const cb = item.querySelector('.flower-checkbox');
                            if (cb) cb.checked = true;
                        }
                    });
                renderDetailsForSelected();
            });
            document.getElementById('clearAll').addEventListener('click', () => {
                document.querySelectorAll('#flowersGrid .flower-checkbox').forEach(cb => cb.checked =
                false);
                renderDetailsForSelected();
            });
        });

        // SweetAlert on session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Saved',
                text: @json(session('success'))
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error'))
            });
        @endif
    </script>
@endsection
