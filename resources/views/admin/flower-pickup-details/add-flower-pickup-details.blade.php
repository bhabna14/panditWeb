@extends('admin.layouts.apps')

@section('styles')
    <!-- SweetAlert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nu-card { border:1px solid #e9ecf5; border-radius:16px; box-shadow:0 6px 18px rgba(25,42,70,.06); background:#fff; padding:18px; margin-bottom:18px }
        .nu-hero { background:linear-gradient(135deg,#f3f4ff 0%,#e9fbff 100%); border:1px solid #e9ecf5; border-radius:16px; padding:18px; margin-bottom:18px }
        .nu-chip { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; border:1px solid #e9ecf5; background:#fff; font-weight:600; font-size:.9rem }
        .section-card { border:1px solid #eef1f4; border-radius:14px }
        .section-card .card-header { background:#fbfcfe; border-bottom:1px solid #eef1f4 }
        .section-title { font-weight:600; margin:0 }
        .required::after { content:" *"; color:#dc3545 }
        .gap-12 { gap:12px }
        .sticky-actions { position:sticky; bottom:0; background:#fff; padding:12px; border-top:1px solid #eef1f4; z-index:2 }
        .row-badge { font-size:.8rem }
        .is-invalid+.invalid-feedback { display:block }
        .table-items th, .table-items td { vertical-align:middle }
        .table-items select, .table-items input { min-width:140px }
        .totals-bar { background:#f8fafc; border:1px solid #e5e7eb; border-radius:.5rem; padding:.75rem 1rem }
    </style>
@endsection

@section('content')
    <div class="nu-hero d-flex justify-content-between align-items-center mt-4">
        <div>
            <h4 class="m-0">Add Flower Pickup Details</h4>
            <div class="nu-chip"><span>Quick create • Clean layout</span></div>
        </div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.manageflowerpickupdetails') }}" class="btn btn-info text-white">
                    Manage Flower Pickup Details
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
        </ol>
    </div>

    {{-- Inline flash (SweetAlert will also show) --}}
    @if (session('success'))
        <div class="alert alert-success" id="Message">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><strong>Please fix the errors below.</strong></div>
    @endif
    @error('general')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('admin.saveFlowerPickupAssignRider') }}" novalidate>
        @csrf

        {{-- PICKUP & DELIVERY --}}
        <div class="nu-card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="section-title">Pickup & Delivery</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="vendor_id" class="form-label required">Vendor</label>
                        <select id="vendor_id" name="vendor_id"
                                class="form-control @error('vendor_id') is-invalid @enderror" required autofocus>
                            <option value="" selected>Choose</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->vendor_id }}" @selected(old('vendor_id') == $vendor->vendor_id)>
                                    {{ $vendor->vendor_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @php
                        $today = now()->toDateString();
                    @endphp

                    <div class="col-md-4">
                        <label for="pickup_date" class="form-label required">Pickup Date</label>
                        <input type="date" id="pickup_date" name="pickup_date"
                               class="form-control @error('pickup_date') is-invalid @enderror"
                               value="{{ old('pickup_date', $today) }}" min="{{ $today }}" required>
                        @error('pickup_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="delivery_date" class="form-label required">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date"
                               class="form-control @error('delivery_date') is-invalid @enderror"
                               value="{{ old('delivery_date', $today) }}" min="{{ old('pickup_date', $today) }}" required>
                        @error('delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ITEMS (TABULAR) --}}
        @php
            // Rebuild old values to persist rows after validation errors
            $oldFlowerIds = old('flower_id', ['']);
            $oldUnitIds   = old('unit_id',   ['']);
            $oldQtys      = old('quantity',  ['']);
            $oldPrices    = old('price',     ['']); // optional array
            $rowCount     = max(count($oldFlowerIds), count($oldUnitIds), count($oldQtys), count($oldPrices), 1);
        @endphp

        <div class="nu-card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-12">
                <h6 class="section-title m-0">Items</h6>
                <div class="d-flex align-items-center gap-12">
                    <span class="badge bg-light text-dark row-badge">
                        Rows: <span id="rowCount">{{ $rowCount }}</span>
                    </span>
                    <button type="button" class="btn btn-outline-primary" id="addRowBtn">Add Row</button>
                    <button type="button" class="btn btn-outline-secondary" id="clearAllBtn">Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-items align-middle">
                        <thead class="table-light">
                        <tr>
                            <th style="width:35%">Flower</th>
                            <th style="width:20%">Unit</th>
                            <th style="width:15%">Quantity</th>
                            <th style="width:15%">Price (₹)</th>
                            <th style="width:15%" class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody id="rowsBody">
                        @for ($i = 0; $i < $rowCount; $i++)
                            <tr data-row>
                                <td>
                                    <select name="flower_id[]"
                                            class="form-control @error('flower_id.' . $i) is-invalid @enderror" required>
                                        <option value="" selected>Choose flower</option>
                                        @foreach ($flowers as $flower)
                                            <option value="{{ $flower->product_id }}" @selected(($oldFlowerIds[$i] ?? '') == $flower->product_id)>
                                                {{ $flower->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('flower_id.' . $i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <select name="unit_id[]"
                                            class="form-control @error('unit_id.' . $i) is-invalid @enderror" required>
                                        <option value="" selected>Choose unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected(($oldUnitIds[$i] ?? '') == $unit->id)>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id.' . $i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <input type="number" name="quantity[]"
                                           class="form-control @error('quantity.' . $i) is-invalid @enderror"
                                           inputmode="decimal" min="0.01" step="0.01"
                                           value="{{ $oldQtys[$i] ?? '' }}" required>
                                    @error('quantity.' . $i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <input type="number" name="price[]"
                                           class="form-control @error('price.' . $i) is-invalid @enderror"
                                           inputmode="decimal" min="0" step="0.01"
                                           value="{{ $oldPrices[$i] ?? '' }}">
                                    @error('price.' . $i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">Remove</button>
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Hidden template for new rows --}}
                <template id="rowTemplate">
                    <tr data-row>
                        <td>
                            <select name="flower_id[]" class="form-control" required>
                                <option value="" selected>Choose flower</option>
                                @foreach ($flowers as $flower)
                                    <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="unit_id[]" class="form-control" required>
                                <option value="" selected>Choose unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control" inputmode="decimal" min="0.01" step="0.01" required></td>
                        <td><input type="number" name="price[]" class="form-control" inputmode="decimal" min="0" step="0.01"></td>
                        <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">Remove</button></td>
                    </tr>
                </template>

                <div class="mt-3 d-flex justify-content-end">
                    <div class="totals-bar">
                        <div><strong>Estimated Total (₹):</strong> <span id="estTotal">0.00</span></div>
                        <small class="text-muted">Calculated as Σ(price × qty) where both are provided.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIDER --}}
        <div class="nu-card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="section-title m-0">Assign Rider</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="rider_id" class="form-label required">Rider</label>
                        <select id="rider_id" name="rider_id"
                                class="form-control @error('rider_id') is-invalid @enderror" required>
                            <option value="" selected>Choose</option>
                            @foreach ($riders as $rider)
                                <option value="{{ $rider->rider_id }}" @selected(old('rider_id') == $rider->rider_id)>
                                    {{ $rider->rider_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('rider_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="nu-card section-card">
            <div class="sticky-actions d-flex justify-content-between align-items-center">
                <div class="text-muted">Review details before submitting.</div>
                <div class="d-flex gap-12">
                    <a href="{{ route('admin.manageflowerpickupdetails') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="reset" class="btn btn-light">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit Pickup</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), confirmButtonColor: '#3085d6' });
        @endif
        @if ($errors->any())
        Swal.fire({ icon: 'error', title: 'Validation error', text: 'Please review the highlighted fields.' });
        @endif
        setTimeout(function(){ const msg=document.getElementById('Message'); if(msg) msg.style.display='none'; },3000);
    </script>

    {{-- Dynamic rows, totals, and date constraints --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rowsBody   = document.getElementById('rowsBody');
            const rowTpl     = document.getElementById('rowTemplate');
            const addRowBtn  = document.getElementById('addRowBtn');
            const clearAllBtn= document.getElementById('clearAllBtn');
            const rowCountEl = document.getElementById('rowCount');
            const estTotalEl = document.getElementById('estTotal');

            const pickupDate  = document.getElementById('pickup_date');
            const deliveryDate= document.getElementById('delivery_date');

            function syncDeliveryMin() {
                if (!pickupDate || !deliveryDate) return;
                deliveryDate.min = pickupDate.value || deliveryDate.min;
                if (deliveryDate.value < deliveryDate.min) {
                    deliveryDate.value = deliveryDate.min;
                }
            }
            pickupDate?.addEventListener('change', syncDeliveryMin);
            syncDeliveryMin();

            function updateRowCount() {
                const count = rowsBody.querySelectorAll('tr[data-row]').length;
                rowCountEl.textContent = String(count);
                rowsBody.querySelectorAll('.remove-row-btn').forEach(btn => btn.disabled = (count <= 1));
            }

            function computeTotal() {
                let total = 0;
                rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
                    const qtyEl   = tr.querySelector('input[name="quantity[]"]');
                    const priceEl = tr.querySelector('input[name="price[]"]');
                    const qty     = parseFloat(qtyEl?.value)   || 0;
                    const price   = parseFloat(priceEl?.value);
                    if (!isNaN(price) && qty > 0) total += qty * price;
                });
                estTotalEl.textContent = total.toFixed(2);
            }

            function bindRow(tr) {
                tr.querySelector('.remove-row-btn')?.addEventListener('click', () => {
                    const n = rowsBody.querySelectorAll('tr[data-row]').length;
                    if (n > 1) {
                        tr.remove();
                        updateRowCount();
                        computeTotal();
                    }
                });
                tr.querySelectorAll('input[name="quantity[]"], input[name="price[]"]').forEach(inp => {
                    inp.addEventListener('input', computeTotal);
                });
            }

            function addRow(focus = true) {
                const frag = document.importNode(rowTpl.content, true);
                const tr = frag.querySelector('tr[data-row]');
                rowsBody.appendChild(tr);
                bindRow(tr);
                updateRowCount();
                if (focus) tr.querySelector('select[name="flower_id[]"]').focus();
            }

            // initial bindings
            rowsBody.querySelectorAll('tr[data-row]').forEach(bindRow);
            updateRowCount();
            computeTotal();

            addRowBtn?.addEventListener('click', () => addRow(true));
            clearAllBtn?.addEventListener('click', () => {
                const rows = Array.from(rowsBody.querySelectorAll('tr[data-row]'));
                rows.forEach((tr, idx) => {
                    tr.querySelectorAll('input').forEach(i => i.value = '');
                    tr.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                    if (idx > 0) tr.remove();
                });
                updateRowCount();
                computeTotal();
            });
        });
    </script>
@endsection
