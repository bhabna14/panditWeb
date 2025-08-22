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

        .grid-gap {
            row-gap: 10px;
        }

        .row-table thead th {
            white-space: nowrap;
        }

        .row-table td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Month-wise Flower Price</span>
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

    <form method="POST" action="{{ route('admin.monthWiseFlowerPrice.store') }}" id="mwfpForm">
        @csrf

        <!-- Vendor + Search -->
        <div class="card card-shadow mb-4">
            <div class="card-body">
                <div class="row grid-gap">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label required">Vendor</label>
                        <select id="vendor_id" name="vendor_id" class="form-control select2" required
                            data-placeholder="Select a vendor">
                            <option value=""></option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->vendor_id }}"
                                    {{ (string) old('vendor_id') === (string) $v->vendor_id ? 'selected' : '' }}>
                                    {{ $v->vendor_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Flowers assigned to the selected vendor will appear below.</small>
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

        <!-- Details (no chips / no checkboxes) -->
        <div class="card card-shadow mb-4">
            <div class="card-body">
                <div class="fw-semibold mb-2">Vendor Flowers & Prices</div>
                <small class="text-muted">For each flower below, add one or more rows (date range, qty, unit,
                    price).</small>

                <!-- Detail containers -->
                <div id="flowerDetailsContainer" class="mt-3"></div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg">Save</button>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        /** ========================
         * Data from server
         * ====================== */
        const vendors = @json($vendors);
        const allFlowers = @json($flowers);
        const units = @json($units);

        /** ========================
         * Build lookups
         * ====================== */
        // all valid numeric product_ids
        const allFlowerIdsSet = new Set((allFlowers || [])
            .map(f => Number(f.product_id))
            .filter(Number.isFinite)
        );

        // CODE -> product_id (fallback to "FLOW{product_id}")
        const codeToId = {};
        (allFlowers || []).forEach(f => {
            const pid = Number(f.product_id);
            if (!Number.isFinite(pid)) return;
            const fallbackCode = 'FLOW' + String(pid);
            const code = String((f.product_code ?? fallbackCode)).toUpperCase();
            codeToId[code] = pid;
        });

        // resolve vendor’s stored id (e.g., "FLOW3419542" or "3419542") to numeric product_id
        const resolveToPid = (value) => {
            if (value == null) return null;
            const raw = String(value).trim().toUpperCase();

            // exact code like FLOW123
            if (codeToId[raw]) return codeToId[raw];

            // extract digits and try
            const digits = raw.match(/\d+/);
            if (digits) {
                const pid = Number(digits[0]);
                if (allFlowerIdsSet.has(pid)) return pid;
            }

            // pure numeric?
            const num = Number(value);
            if (Number.isFinite(num) && allFlowerIdsSet.has(num)) return num;

            return null;
        };

        // vendor_id -> [product_id,...]
        const vendorMap = {};
        (vendors || []).forEach(v => {
            const rawList = Array.isArray(v.flower_ids) ? v.flower_ids : [];
            const resolved = rawList.map(resolveToPid).filter(pid => pid !== null);
            vendorMap[v.vendor_id] = Array.from(new Set(resolved));
        });

        /** ========================
         * Old input (rehydrate)
         * ====================== */
        const oldData = {
            vendor_id: @json(old('vendor_id')),
            start_date: @json(old('start_date', [])),
            end_date: @json(old('end_date', [])),
            quantity: @json(old('quantity', [])),
            unit_id: @json(old('unit_id', [])),
            price: @json(old('price', [])),
        };

        /** ========================
         * Builders
         * ====================== */
        function buildRowHTML(fid, idx, preset = {}) {
            const sd = preset.start_date || '';
            const ed = preset.end_date || '';
            const q = preset.quantity || '';
            const uid = preset.unit_id || '';
            const pr = preset.price || '';

            return `
    <tr data-idx="${idx}">
        <td><input type="date" class="form-control" name="start_date[${fid}][]" value="${sd}" required></td>
        <td><input type="date" class="form-control" name="end_date[${fid}][]" value="${ed}" required></td>
        <td><input type="number" step="0.01" min="0" class="form-control" name="quantity[${fid}][]" value="${q}" required></td>
        <td>
            <select class="form-control" name="unit_id[${fid}][]" required>
                <option value="">Select unit</option>
                ${units.map(u => `<option value="${u.id}" ${Number(uid)===Number(u.id) ? 'selected':''}>${u.unit_name}</option>`).join('')}
            </select>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" min="0" class="form-control" name="price[${fid}][]" value="${pr}" required>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
        </td>
    </tr>`;
        }

        function buildFlowerDetailCard(fid) {
            const f = allFlowers.find(x => Number(x.product_id) === Number(fid));
            const name = f ? f.name : `Flower #${fid}`;

            const olds = {
                start_date: (oldData.start_date[String(fid)] || []),
                end_date: (oldData.end_date[String(fid)] || []),
                quantity: (oldData.quantity[String(fid)] || []),
                unit_id: (oldData.unit_id[String(fid)] || []),
                price: (oldData.price[String(fid)] || []),
            };
            const maxRows = Math.max(
                olds.start_date.length,
                olds.end_date.length,
                olds.quantity.length,
                olds.unit_id.length,
                olds.price.length,
                1 // at least one row
            );

            let rowsHTML = '';
            for (let i = 0; i < maxRows; i++) {
                rowsHTML += buildRowHTML(fid, i, {
                    start_date: olds.start_date[i],
                    end_date: olds.end_date[i],
                    quantity: olds.quantity[i],
                    unit_id: olds.unit_id[i],
                    price: olds.price[i],
                });
            }

            return `
    <div class="card card-shadow mb-3 flower-detail" data-id="${fid}" data-name="${(name || '').toLowerCase()}">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="h6 mb-0">${name}</div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success add-row" data-flower="${fid}">
                        + Add Row
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered row-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="rows" data-flower="${fid}">
                        ${rowsHTML}
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;
        }

        /** ========================
         * Rendering
         * ====================== */
        function renderAllDetailsForVendor(vendorId) {
            const container = document.getElementById('flowerDetailsContainer');
            container.innerHTML = '';

            const allowed = vendorMap[vendorId] || [];
            if (!allowed.length) return;

            // Sort flowers by name for nicer UX
            const allowedWithNames = allowed
                .map(fid => {
                    const f = allFlowers.find(x => Number(x.product_id) === Number(fid));
                    return {
                        fid,
                        name: f ? (f.name || '') : ''
                    };
                })
                .sort((a, b) => a.name.localeCompare(b.name));

            allowedWithNames.forEach(({
                fid
            }) => {
                container.insertAdjacentHTML('beforeend', buildFlowerDetailCard(fid));
            });
        }

        function searchInRendered(term) {
            const q = (term || '').trim().toLowerCase();
            document.querySelectorAll('#flowerDetailsContainer .flower-detail').forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                card.style.display = name.includes(q) ? '' : 'none';
            });
        }

        /** ========================
         * Boot
         * ====================== */
        document.addEventListener('DOMContentLoaded', () => {
            // Select2
            $('.select2').select2({
                width: '100%',
                allowClear: true
            });

            const vendorSel = document.getElementById('vendor_id');
            vendorSel.addEventListener('change', (e) => {
                // Clear stale search
                const s = document.getElementById('flowerSearch');
                if (s) s.value = '';
                renderAllDetailsForVendor(e.target.value);
            });

            // Initialize with old vendor (rehydrate)
            if (oldData.vendor_id) {
                renderAllDetailsForVendor(oldData.vendor_id);
            }

            // Search
            document.getElementById('flowerSearch').addEventListener('input', (e) => {
                searchInRendered(e.target.value);
            });

            // Add / remove rows (event delegation)
            document.body.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-row')) {
                    const fid = e.target.getAttribute('data-flower');
                    const tbody = document.querySelector(`tbody.rows[data-flower="${fid}"]`);
                    const nextIdx = tbody.querySelectorAll('tr').length;
                    tbody.insertAdjacentHTML('beforeend', buildRowHTML(fid, nextIdx));
                }
                if (e.target.classList.contains('remove-row')) {
                    const tr = e.target.closest('tr');
                    tr?.remove();
                }
            });
        });

        /** ========================
         * SweetAlert session
         * ====================== */
        @{{ 'if' }}(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Saved',
            text: @json(session('success'))
        });
        @{{ 'endif' }}
        @{{ 'if' }}(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error'))
        });
        @{{ 'endif' }}
    </script>
@endsection
