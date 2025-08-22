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
                                {{ (string)old('vendor_id') === (string)$v->vendor_id ? 'selected' : '' }}>
                                {{ $v->vendor_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Flowers assigned to the selected vendor will appear below.</small>
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <div class="w-100">
                        <label class="form-label mb-1">Quick Search</label>
                        <input type="text" id="flowerSearch" class="form-control" placeholder="Search flowers by name...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details (rendered after vendor is chosen) -->
    <div class="card card-shadow mb-4">
        <div class="card-body">
            <div class="fw-semibold mb-2">Vendor Flowers & Prices</div>
            <small class="text-muted">For each flower below, add one or more rows (date range, qty, unit, price).</small>

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
    // =============== Config / Data ===============
    const vendorFlowersUrl = "{{ route('admin.vendor.flowers') }}"; // GET ?vendor_id=...
    const units = @json($units);

    // Old input rehydration
    const oldData = {
        vendor_id: @json(old('vendor_id')),
        start_date: @json(old('start_date', [])),
        end_date:   @json(old('end_date', [])),
        quantity:   @json(old('quantity', [])),
        unit_id:    @json(old('unit_id', [])),
        price:      @json(old('price', [])),
    };

    // =============== UI Builders ===============
    function buildRowHTML(fid, idx, preset = {}) {
        const sd  = preset.start_date || '';
        const ed  = preset.end_date   || '';
        const q   = preset.quantity   || '';
        const uid = preset.unit_id    || '';
        const pr  = preset.price      || '';

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

    function buildFlowerDetailCard(flower) {
        const fid  = flower.product_id;
        const name = flower.name || `Flower #${fid}`;

        const olds = {
            start_date: (oldData.start_date[String(fid)] || []),
            end_date:   (oldData.end_date[String(fid)]   || []),
            quantity:   (oldData.quantity[String(fid)]   || []),
            unit_id:    (oldData.unit_id[String(fid)]    || []),
            price:      (oldData.price[String(fid)]      || []),
        };
        const maxRows = Math.max(
            olds.start_date.length,
            olds.end_date.length,
            olds.quantity.length,
            olds.unit_id.length,
            olds.price.length,
            1
        );

        let rowsHTML = '';
        for (let i = 0; i < maxRows; i++) {
            rowsHTML += buildRowHTML(fid, i, {
                start_date: olds.start_date[i],
                end_date:   olds.end_date[i],
                quantity:   olds.quantity[i],
                unit_id:    olds.unit_id[i],
                price:      olds.price[i],
            });
        }

        return `
        <div class="card card-shadow mb-3 flower-detail" data-id="${fid}" data-name="${(name || '').toLowerCase()}">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="h6 mb-0">${name}${flower.odia_name ? ` <small class="text-muted">(${flower.odia_name})</small>` : ''}</div>
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

    function renderFlowers(flowers) {
        const container = document.getElementById('flowerDetailsContainer');
        container.innerHTML = '';

        if (!Array.isArray(flowers) || flowers.length === 0) {
            // As requested: no alert — just empty space
            return;
        }

        // Sort by name
        const sorted = [...flowers].sort((a,b) => (a.name||'').localeCompare(b.name||''));
        sorted.forEach(f => {
            container.insertAdjacentHTML('beforeend', buildFlowerDetailCard(f));
        });
    }

    // =============== Search (filters rendered cards) ===============
    function searchInRendered(term) {
        const q = (term || '').trim().toLowerCase();
        document.querySelectorAll('#flowerDetailsContainer .flower-detail').forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            card.style.display = name.includes(q) ? '' : 'none';
        });
    }

    // =============== Fetch vendor flowers ===============
    async function fetchVendorFlowers(vendorId) {
        if (!vendorId) { renderFlowers([]); return; }
        const url = new URL(vendorFlowersUrl, window.location.origin);
        url.searchParams.set('vendor_id', vendorId);

        try {
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Failed to load');
            renderFlowers(data.flowers);
        } catch (err) {
            console.error(err);
            renderFlowers([]);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load vendor flowers.' });
        }
    }

    // =============== Boot ===============
    document.addEventListener('DOMContentLoaded', () => {
        $('.select2').select2({ width: '100%', allowClear: true });

        const vendorSel = document.getElementById('vendor_id');
        vendorSel.addEventListener('change', (e) => {
            // Clear search on vendor change
            const s = document.getElementById('flowerSearch');
            if (s) s.value = '';
            fetchVendorFlowers(e.target.value);
        });

        // Rehydrate vendor on validation error
        if (oldData.vendor_id) {
            fetchVendorFlowers(oldData.vendor_id);
        }

        // Search listener
        document.getElementById('flowerSearch').addEventListener('input', (e) => {
            searchInRendered(e.target.value);
        });

        // Add / remove row (event delegation)
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

    // =============== SweetAlert (flash) ===============
    @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Saved', text: @json(session('success')) });
    @endif
    @if (session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) });
    @endif
</script>
@endsection
