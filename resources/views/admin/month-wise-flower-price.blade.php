@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .card-shadow { box-shadow: 0 10px 24px rgba(0,0,0,.06); border-radius: 16px; }
        .pill { font-size: 12px; padding: 2px 8px; border-radius: 999px; background: #f1f5f9; }
        .flower-chip { border: 1px solid #e5e7eb; border-radius: 12px; padding: 8px 10px; display:flex; align-items:center; gap:8px; }
        .flower-item.disabled { opacity: .35; pointer-events: none; }
        .required::after { content:" *"; color:#dc2626; }
        .grid-gap { row-gap: 10px; }
        .row-table thead th { white-space: nowrap; }
        .row-table td { vertical-align: middle; }
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
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.monthWiseFlowerPrice.store') }}" id="mwfpForm">
        @csrf

        <!-- Vendor -->
        <div class="card card-shadow mb-4">
            <div class="card-body">
                <div class="row grid-gap">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label required">Vendor</label>
                        <select id="vendor_id" name="vendor_id" class="form-control select2" required data-placeholder="Select a vendor">
                            <option value=""></option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->vendor_id }}" {{ old('vendor_id')===$v->vendor_id?'selected':'' }}>
                                    {{ $v->vendor_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Only flowers assigned to the selected vendor are shown.</small>
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

        <!-- Flowers -->
        <div class="card card-shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fw-semibold">Flowers</div>
                        <small class="text-muted">Check flowers. For each checked flower, you can add multiple rows (date range, qty, unit, price).</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select all</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">Clear</button>
                    </div>
                </div>

                <div class="row" id="flowersGrid">
                    @foreach($flowers as $f)
                        @php $checked = in_array($f->product_id, old('flower_ids', [])); @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-2 flower-item"
                             data-name="{{ strtolower($f->name) }}" data-id="{{ $f->product_id }}">
                            <label class="flower-chip w-100">
                                <input class="form-check-input me-2 flower-checkbox"
                                       type="checkbox" name="flower_ids[]"
                                       value="{{ $f->product_id }}" {{ $checked ? 'checked' : '' }}>
                                <span>{{ $f->name }}
                                    @if(!empty($f->odia_name))
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

        <!-- Detail containers -->
        <div id="flowerDetailsContainer"></div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg">Save</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Data from server
        const vendors = @json($vendors);
        const allFlowers = @json($flowers);
        const units = @json($units);

        // Map vendor -> allowed flower IDs
        const vendorMap = {};
        vendors.forEach(v => vendorMap[v.vendor_id] = (v.flower_ids || []).map(Number));

        // Old input (rehydrate on validation error)
        const oldData = {
            vendor_id:  @json(old('vendor_id')),
            selected:   @json(old('flower_ids', [])),
            start_date: @json(old('start_date', [])),
            end_date:   @json(old('end_date', [])),
            quantity:   @json(old('quantity', [])),
            unit_id:    @json(old('unit_id', [])),
            price:      @json(old('price', [])),
        };

        // Build a table row for one entry
        function buildRowHTML(fid, idx, preset={}) {
            const sd = preset.start_date || '';
            const ed = preset.end_date || '';
            const q  = preset.quantity   || '';
            const uid= preset.unit_id    || '';
            const pr = preset.price      || '';

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

        // Build the detail card for a selected flower
        function buildFlowerDetailCard(fid) {
            const f = allFlowers.find(x => Number(x.product_id) === Number(fid));
            const name = f ? f.name : `Flower #${fid}`;

            // Old values for this flower (arrays)
            const olds = {
                start_date: (oldData.start_date[String(fid)] || []),
                end_date:   (oldData.end_date[String(fid)]   || []),
                quantity:   (oldData.quantity[String(fid)]   || []),
                unit_id:    (oldData.unit_id[String(fid)]    || []),
                price:      (oldData.price[String(fid)]      || []),
            };
            const maxRows = Math.max(olds.start_date.length, olds.end_date.length, olds.quantity.length, olds.unit_id.length, olds.price.length, 1);

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
            <div class="card card-shadow mb-3 flower-detail" data-id="${fid}">
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

        function renderDetailsForSelected() {
            const container = document.getElementById('flowerDetailsContainer');
            container.innerHTML = '';
            document.querySelectorAll('.flower-checkbox:checked').forEach(cb => {
                container.insertAdjacentHTML('beforeend', buildFlowerDetailCard(cb.value));
            });
        }

        function filterFlowersByVendor(vendorId) {
            const allowed = vendorMap[vendorId] || [];
            let any = false;

            // Uncheck all not allowed
            document.querySelectorAll('.flower-checkbox').forEach(cb => {
                if (!allowed.includes(Number(cb.value))) cb.checked = false;
            });

            // Show allowed
            document.querySelectorAll('#flowersGrid .flower-item').forEach(item => {
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
            $('.select2').select2({ width: '100%', allowClear: true });

            const vendorSel = document.getElementById('vendor_id');
            vendorSel.addEventListener('change', (e) => filterFlowersByVendor(e.target.value));

            // Initialize
            if (oldData.vendor_id) {
                filterFlowersByVendor(oldData.vendor_id);
            } else {
                document.querySelectorAll('#flowersGrid .flower-item').forEach(i => i.style.display = 'none');
                document.getElementById('noFlowersMsg').classList.add('d-none');
            }

            // Search
            document.getElementById('flowerSearch').addEventListener('input', (e) => searchFlowers(e.target.value));

            // Checkbox toggle -> render detail cards
            document.getElementById('flowersGrid').addEventListener('change', (e) => {
                if (e.target.classList.contains('flower-checkbox')) renderDetailsForSelected();
            });

            // Add row
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

            // Select all / clear (only visible+allowed)
            document.getElementById('selectAll').addEventListener('click', () => {
                document.querySelectorAll('#flowersGrid .flower-item:not(.disabled)').forEach(item => {
                    if (item.style.display !== 'none') {
                        const cb = item.querySelector('.flower-checkbox');
                        if (cb) cb.checked = true;
                    }
                });
                renderDetailsForSelected();
            });
            document.getElementById('clearAll').addEventListener('click', () => {
                document.querySelectorAll('#flowersGrid .flower-checkbox').forEach(cb => cb.checked = false);
                renderDetailsForSelected();
            });

            // Rehydrate details if old selected exists
            if (oldData.selected && oldData.selected.length) {
                renderDetailsForSelected();
            }
        });

        // SweetAlert session
        @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Saved', text: @json(session('success')) });
        @endif
        @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) });
        @endif
    </script>
@endsection
