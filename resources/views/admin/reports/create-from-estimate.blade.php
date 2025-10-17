@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-items th,
        .table-items td { vertical-align: middle; }
        .table-items select,
        .table-items input { min-width: 140px; }
        .totals-bar {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .75rem 1rem;
        }
        .form-check-align { display:flex; align-items:center; gap:.5rem; }
        .small-muted { font-size: .875rem; color: #6b7280; }
    </style>
@endsection

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Assign Vendor — {{ \Carbon\Carbon::parse($prefillDate)->toFormattedDateString() }}</h4>
            <a href="{{ route('admin.flowerEstimate', ['preset' => 'tomorrow']) }}"
               class="btn btn-outline-secondary">Back to Estimate</a>
        </div>

        <form method="POST" action="{{ route('admin.saveFlowerPickupAssignRider') }}" novalidate>
            @csrf

            {{-- Pickup & Delivery (kept) --}}
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Pickup & Delivery</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pickup_date" class="form-label required">Pickup Date</label>
                            <input type="date" id="pickup_date" name="pickup_date"
                                   class="form-control @error('pickup_date') is-invalid @enderror"
                                   value="{{ old('pickup_date', $prefillDate) }}"
                                   min="{{ now()->toDateString() }}" required>
                            @error('pickup_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="delivery_date" class="form-label required">Delivery Date</label>
                            <input type="date" id="delivery_date" name="delivery_date"
                                   class="form-control @error('delivery_date') is-invalid @enderror"
                                   value="{{ old('delivery_date', $prefillDate) }}"
                                   min="{{ now()->toDateString() }}" required>
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items (tabular) --}}
            @php
                $oldFlowerIds = old('flower_id', []);
                $oldUnitIds   = old('unit_id', []);
                $oldQtys      = old('quantity', []);
                $oldPrices    = old('price', []);
                $oldVendors   = old('row_vendor_id', []);
                $oldRiders    = old('row_rider_id', []);

                $rowCountOld = max(
                    count($oldFlowerIds), count($oldUnitIds),
                    count($oldQtys), count($oldPrices),
                    count($oldVendors), count($oldRiders)
                );
                $rows = $rowCountOld > 0 ? range(0, $rowCountOld - 1) : range(0, max(count($prefillRows), 1) - 1);
            @endphp

            <div class="card mb-3">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <strong>Items</strong>

                    {{-- Bulk rider controls --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-check-align">
                            <input class="form-check-input" type="checkbox" value="1" id="applyOneRider" name="apply_one_rider"
                                   @checked(old('apply_one_rider') == '1')>
                            <label class="form-check-label" for="applyOneRider">Use one rider for all items</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="bulkRider" class="small-muted">Rider:</label>
                            <select id="bulkRider" name="bulk_rider_id" class="form-control form-control-sm" {{ old('apply_one_rider') == '1' ? '' : 'disabled' }}>
                                <option value="" selected>Choose</option>
                                @foreach ($riders as $r)
                                    <option value="{{ $r->rider_id }}" @selected(old('bulk_rider_id') == $r->rider_id)>{{ $r->rider_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm ms-auto" id="addRowBtn">Add Row</button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-items align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:22%">Flower</th>
                                    <th style="width:14%">Unit</th>
                                    <th style="width:12%">Quantity</th>
                                    <th style="width:12%">Price (₹)</th>
                                    <th style="width:20%">Vendor (item)</th>
                                    <th style="width:20%">Rider (item)</th>
                                    <th style="width:0%"></th>
                                </tr>
                            </thead>
                            <tbody id="rowsBody">
                                @foreach ($rows as $i)
                                    @php
                                        $default = array_merge(
                                            [
                                                'flower_id'   => null,
                                                'unit_id'     => null,
                                                'quantity'    => null,
                                                'price'       => null,
                                                'flower_name' => null,
                                                'unit_label'  => null,
                                            ],
                                            $prefillRows[$i] ?? [],
                                        );

                                        $flowerVal = $oldFlowerIds[$i] ?? $default['flower_id'];
                                        $unitVal   = $oldUnitIds[$i]   ?? $default['unit_id'];
                                        $qtyVal    = $oldQtys[$i]      ?? $default['quantity'];
                                        $priceVal  = $oldPrices[$i]    ?? $default['price'];
                                        $vendorVal = $oldVendors[$i]   ?? null;
                                        $riderVal  = $oldRiders[$i]    ?? null;
                                    @endphp
                                    <tr data-row>
                                        {{-- Flower --}}
                                        <td>
                                            <select name="flower_id[]"
                                                    class="form-control @error('flower_id.' . $i) is-invalid @enderror" required>
                                                <option value="" selected>Choose flower</option>
                                                @foreach ($flowers as $flower)
                                                    <option value="{{ $flower->product_id }}" @selected($flowerVal == $flower->product_id)>{{ $flower->name }}</option>
                                                @endforeach
                                            </select>
                                            @if (!$flowerVal && ($default['flower_name'] ?? null))
                                                <small class="text-muted">Unmatched: {{ $default['flower_name'] }}</small>
                                            @endif
                                            @error('flower_id.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- Unit --}}
                                        <td>
                                            <select name="unit_id[]"
                                                    class="form-control @error('unit_id.' . $i) is-invalid @enderror" required>
                                                <option value="" selected>Choose unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                                                @endforeach
                                            </select>
                                            @if (!$unitVal && ($default['unit_label'] ?? null))
                                                <small class="text-muted">Unmatched: {{ $default['unit_label'] }}</small>
                                            @endif
                                            @error('unit_id.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- Qty --}}
                                        <td>
                                            <input type="number" name="quantity[]"
                                                   class="form-control @error('quantity.' . $i) is-invalid @enderror"
                                                   inputmode="decimal" min="0.01" step="0.01"
                                                   value="{{ $qtyVal }}" required>
                                            @error('quantity.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- Price --}}
                                        <td>
                                            <input type="number" name="price[]"
                                                   class="form-control @error('price.' . $i) is-invalid @enderror"
                                                   inputmode="decimal" min="0" step="0.01"
                                                   value="{{ $priceVal }}">
                                            @error('price.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- Vendor (item wise) --}}
                                        <td>
                                            <select name="row_vendor_id[]"
                                                    class="form-control @error('row_vendor_id.' . $i) is-invalid @enderror">
                                                <option value="" selected>Choose vendor</option>
                                                @foreach ($vendors as $v)
                                                    <option value="{{ $v->vendor_id }}" @selected($vendorVal == $v->vendor_id)>{{ $v->vendor_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('row_vendor_id.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- Rider (item wise) --}}
                                        <td>
                                            <select name="row_rider_id[]"
                                                    class="form-control row-rider @error('row_rider_id.' . $i) is-invalid @enderror"
                                                    data-row-rider>
                                                <option value="" selected>Choose rider</option>
                                                @foreach ($riders as $r)
                                                    <option value="{{ $r->rider_id }}" @selected($riderVal == $r->rider_id)>{{ $r->rider_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('row_rider_id.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- Remove --}}
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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
                            <td>
                                <input type="number" name="quantity[]" class="form-control" inputmode="decimal" min="0.01" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" name="price[]" class="form-control" inputmode="decimal" min="0" step="0.01">
                            </td>
                            <td>
                                <select name="row_vendor_id[]" class="form-control">
                                    <option value="" selected>Choose vendor</option>
                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->vendor_id }}">{{ $v->vendor_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="row_rider_id[]" class="form-control row-rider" data-row-rider>
                                    <option value="" selected>Choose rider</option>
                                    @foreach ($riders as $r)
                                        <option value="{{ $r->rider_id }}">{{ $r->rider_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">Remove</button>
                            </td>
                        </tr>
                    </template>

                    <div class="mt-3 d-flex justify-content-between flex-wrap gap-2">
                        <div class="small-muted">Tip: leave Vendor/Rider blank on a row if not applicable.</div>
                        <div class="totals-bar ms-auto">
                            <div><strong>Estimated Total (₹):</strong> <span id="estTotal">0.00</span></div>
                            <small class="text-muted">Calculated as Σ(price × qty) of rows with both values.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.flowerEstimate', ['preset' => 'tomorrow']) }}"
                   class="btn btn-outline-secondary">Cancel</a>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-light">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit Pickup</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rowsBody    = document.getElementById('rowsBody');
            const rowTpl      = document.getElementById('rowTemplate');
            const addRowBtn   = document.getElementById('addRowBtn');
            const estTotalEl  = document.getElementById('estTotal');

            const applyOneRiderCb = document.getElementById('applyOneRider');
            const bulkRiderSel    = document.getElementById('bulkRider');

            function qsa(root, sel) { return Array.from((root || document).querySelectorAll(sel)); }

            function attachRowHandlers(root) {
                (root || document).querySelectorAll('.remove-row-btn').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.currentTarget.closest('[data-row]').remove();
                        computeTotal();
                    });
                });

                (root || document).querySelectorAll('input[name="quantity[]"], input[name="price[]"]').forEach(inp => {
                    inp.addEventListener('input', computeTotal);
                });
            }

            function addRow() {
                const frag = document.importNode(rowTpl.content, true);
                const tr = frag.querySelector('tr[data-row]');
                rowsBody.appendChild(frag);
                attachRowHandlers(rowsBody);
                // If bulk rider is active, lock+apply to the new row
                if (applyOneRiderCb.checked) {
                    const lastRow = rowsBody.querySelector('tr[data-row]:last-child');
                    const riderSel = lastRow.querySelector('[data-row-rider]');
                    syncRowRider(riderSel);
                    riderSel.disabled = true;
                }
            }

            function computeTotal() {
                let total = 0;
                rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
                    const qtyEl   = tr.querySelector('input[name="quantity[]"]');
                    const priceEl = tr.querySelector('input[name="price[]"]');
                    const qty     = qtyEl ? parseFloat(qtyEl.value) || 0 : 0;
                    const price   = priceEl ? parseFloat(priceEl.value) || 0 : 0;
                    if (qty > 0 && price >= 0) total += qty * price;
                });
                estTotalEl.textContent = total.toFixed(2);
            }

            // ---- Bulk Rider logic --------------------------------------------
            function syncAllRiders() {
                const val = bulkRiderSel.value || '';
                qsa(rowsBody, '[data-row-rider]').forEach(sel => {
                    sel.value = val;
                    sel.disabled = true;
                });
            }
            function unlockAllRiders() {
                qsa(rowsBody, '[data-row-rider]').forEach(sel => sel.disabled = false);
            }
            function syncRowRider(sel) {
                sel.value = bulkRiderSel.value || '';
            }

            applyOneRiderCb.addEventListener('change', () => {
                if (applyOneRiderCb.checked) {
                    bulkRiderSel.disabled = false;
                    syncAllRiders();
                } else {
                    bulkRiderSel.disabled = true;
                    unlockAllRiders();
                }
            });

            bulkRiderSel.addEventListener('change', () => {
                if (applyOneRiderCb.checked) syncAllRiders();
            });

            // Init bindings
            addRowBtn.addEventListener('click', addRow);
            attachRowHandlers(document);
            computeTotal();

            // Rehydrate state from old() if needed
            if (applyOneRiderCb.checked) {
                bulkRiderSel.disabled = false;
                syncAllRiders();
            }
        });
    </script>
@endsection
