@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-items th, .table-items td { vertical-align: middle; }
        .table-items select, .table-items input { min-width: 120px; }
        .totals-bar { background:#f8fafc; border:1px solid #e5e7eb; border-radius:.5rem; padding:.75rem 1rem; }
        .totals-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
        @media (max-width:768px){ .totals-grid { grid-template-columns:1fr; } }
        .form-check-align { display:flex; align-items:center; gap:.5rem; }
        .small-muted { font-size:.875rem; color:#6b7280; }
        .subhead { font-size:.85rem; color:#6c757d; }
        .readonly-input { background:#f3f4f6; }
    </style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Assign Vendor — {{ \Carbon\Carbon::parse($prefillDate)->toFormattedDateString() }}</h4>
        <a href="{{ route('admin.flowerEstimate', ['preset' => 'tomorrow']) }}" class="btn btn-outline-secondary">Back to Estimate</a>
    </div>

    <form method="POST" action="{{ route('admin.saveFlowerPickupAssignRider') }}" novalidate>
        @csrf

        {{-- Pickup & Delivery --}}
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>Pickup & Delivery</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="pickup_date" class="form-label required">Pickup Date</label>
                        <input type="date" id="pickup_date" name="pickup_date"
                               class="form-control @error('pickup_date') is-invalid @enderror"
                               value="{{ old('pickup_date', $todayDate) }}"
                               min="{{ now()->toDateString() }}" required>
                        @error('pickup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="delivery_date" class="form-label required">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date"
                               class="form-control @error('delivery_date') is-invalid @enderror"
                               value="{{ old('delivery_date', $prefillDate) }}"
                               min="{{ now()->toDateString() }}" required>
                        @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        @php
            $oldFlowerIds = old('flower_id', []);
            $oldUnitIds   = old('unit_id', []);
            $oldQtys      = old('quantity', []);
            $oldPrices    = old('price', []);

            $oldEstQtys   = old('est_quantity', []);
            $oldEstPrices = old('est_price', []);

            $oldVendors   = old('row_vendor_id', []);
            $oldRiders    = old('row_rider_id', []);

            $rowCountOld = max(
                count($oldFlowerIds), count($oldUnitIds),
                count($oldQtys), count($oldPrices),
                count($oldEstQtys), count($oldEstPrices),
                count($oldVendors), count($oldRiders)
            );
            $rows = $rowCountOld > 0 ? range(0, $rowCountOld - 1) : range(0, max(count($prefillRows), 1) - 1);
        @endphp

        <div class="card mb-3">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <strong>Items</strong>
                    <div class="subhead">Estimate unit is always the same as Actual unit. Estimate price is auto-calculated.</div>
                </div>

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
                            <th style="width:18%">Flower</th>

                            {{-- Estimate (unit mirrors actual) --}}
                            <th style="width:12%">Est. Qty</th>
                            <th style="width:12%">Est. Price (₹)</th>

                            {{-- Actual --}}
                            <th style="width:13%">Actual Unit</th>
                            <th style="width:11%">Actual Qty</th>
                            <th style="width:11%">Actual Price (₹)</th>

                            <th style="width:12%">Vendor</th>
                            <th style="width:12%">Rider</th>
                            <th style="width:0%"></th>
                        </tr>
                        </thead>
                        <tbody id="rowsBody">
                        @foreach ($rows as $i)
                            @php
                                $default = array_merge(
                                    [
                                        'flower_id'    => null,
                                        'est_quantity' => null,
                                        'unit_id'      => null,
                                        'quantity'     => null,
                                        'price'        => null,
                                        'flower_name'  => null,
                                        'unit_label'   => null,
                                    ],
                                    $prefillRows[$i] ?? [],
                                );

                                $flowerVal  = $oldFlowerIds[$i] ?? $default['flower_id'];
                                $estQtyVal  = $oldEstQtys[$i]   ?? $default['est_quantity'];
                                $estPriceVal= $oldEstPrices[$i] ?? null;

                                $unitVal    = $oldUnitIds[$i]   ?? $default['unit_id'];
                                $qtyVal     = $oldQtys[$i]      ?? $default['quantity'];
                                $priceVal   = $oldPrices[$i]    ?? $default['price'];

                                $vendorVal  = $oldVendors[$i]   ?? null;
                                $riderVal   = $oldRiders[$i]    ?? null;
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

                                {{-- Est. Qty (editable) --}}
                                <td>
                                    <input type="number" name="est_quantity[]"
                                           class="form-control @error('est_quantity.' . $i) is-invalid @enderror"
                                           inputmode="decimal" min="0.01" step="0.01"
                                           value="{{ $estQtyVal }}">
                                    @error('est_quantity.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>

                                {{-- Est. Price (auto, readonly) --}}
                                <td>
                                    <input type="number" name="est_price[]"
                                           class="form-control readonly-input"
                                           inputmode="decimal" min="0" step="0.01"
                                           value="{{ $estPriceVal }}" readonly>
                                </td>

                                {{-- Hidden est_unit_id that mirrors actual unit --}}
                                <input type="hidden" name="est_unit_id[]" value="{{ $unitVal }}"/>

                                {{-- Actual Unit --}}
                                <td>
                                    <select name="unit_id[]"
                                            class="form-control @error('unit_id.' . $i) is-invalid @enderror"
                                            data-actual-unit>
                                        <option value="" selected>Choose</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit_id.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>

                                {{-- Actual Qty --}}
                                <td>
                                    <input type="number" name="quantity[]"
                                           class="form-control @error('quantity.' . $i) is-invalid @enderror"
                                           inputmode="decimal" min="0.01" step="0.01"
                                           value="{{ $qtyVal }}">
                                    @error('quantity.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>

                                {{-- Actual Price --}}
                                <td>
                                    <input type="number" name="price[]"
                                           class="form-control @error('price.' . $i) is-invalid @enderror"
                                           inputmode="decimal" min="0" step="0.01"
                                           value="{{ $priceVal }}">
                                    @error('price.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>

                                {{-- Vendor --}}
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

                                {{-- Rider --}}
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
                            <input type="number" name="est_quantity[]" class="form-control" inputmode="decimal" min="0.01" step="0.01">
                        </td>
                        <td>
                            <input type="number" name="est_price[]" class="form-control readonly-input" inputmode="decimal" min="0" step="0.01" readonly>
                        </td>

                        <input type="hidden" name="est_unit_id[]" value=""/>

                        <td>
                            <select name="unit_id[]" class="form-control" data-actual-unit>
                                <option value="" selected>Choose</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control" inputmode="decimal" min="0.01" step="0.01">
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
                        <div class="totals-grid">
                            <div><strong>Estimated Total (₹):</strong> <span id="estTotal">0.00</span></div>
                            <div><strong>Actual Total (₹):</strong> <span id="actTotal">0.00</span></div>
                        </div>
                        <small class="text-muted d-block mt-1">Totals are Σ(price × qty) where both values exist.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.flowerEstimate', ['preset' => 'tomorrow']) }}" class="btn btn-outline-secondary">Cancel</a>
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
    const actTotalEl  = document.getElementById('actTotal');

    const applyOneRiderCb = document.getElementById('applyOneRider');
    const bulkRiderSel    = document.getElementById('bulkRider');

    // Pricing map from server: product_id -> {fd_unit_symbol, fd_unit_id, fd_price}
    const PRICING = @json($fdProductPricing);
    // Unit id -> canonical symbol ('kg','g','l','ml','pcs')
    const UNIT_SYMBOL = @json($unitIdToSymbol);

    function qsa(root, sel) { return Array.from((root || document).querySelectorAll(sel)); }

    // ----- Unit conversions (to base) -----
    // Base: weight → g, volume → ml, count → pcs
    function symbolToCategory(sym) {
        if (!sym) return 'count';
        if (sym === 'kg' || sym === 'g') return 'weight';
        if (sym === 'l'  || sym === 'ml') return 'volume';
        return 'count';
    }
    function toBaseFactor(sym) {
        switch (sym) {
            case 'kg': return 1000; // kg → g
            case 'g':  return 1;
            case 'l':  return 1000; // L → ml
            case 'ml': return 1;
            case 'pcs': default: return 1;
        }
    }

    // Convert quantity from actual unit to FD unit, then multiply by fd_price
    function computeEstimatedPrice(productId, estQty, actualUnitId) {
        const info = PRICING[String(productId)];
        if (!info || !estQty || !actualUnitId) return 0;

        const actualSym = UNIT_SYMBOL[String(actualUnitId)];
        const fdSym     = info.fd_unit_symbol;

        const catA = symbolToCategory(actualSym);
        const catB = symbolToCategory(fdSym);
        if (catA !== catB) {
            // Different categories: cannot price; return 0
            return 0;
        }

        const actualBase = toBaseFactor(actualSym);
        const fdBase     = toBaseFactor(fdSym);

        // quantity in FD units
        const fdUnitsCount = (estQty * actualBase) / fdBase;

        return fdUnitsCount * (parseFloat(info.fd_price) || 0);
    }

    function attachRowHandlers(root) {
        (root || document).querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.currentTarget.closest('[data-row]').remove();
                computeTotals();
            });
        });

        (root || document).querySelectorAll(
            'input[name="quantity[]"], input[name="price[]"], input[name="est_quantity[]"], select[name="unit_id[]"], select[name="flower_id[]"]'
        ).forEach(inp => {
            inp.addEventListener('input', () => {
                const tr = inp.closest('tr[data-row]');
                recalcEstimateForRow(tr);
                computeTotals();
            });
            inp.addEventListener('change', () => {
                const tr = inp.closest('tr[data-row]');
                recalcEstimateForRow(tr);
                computeTotals();
            });
        });
    }

    function addRow() {
        const frag = document.importNode(rowTpl.content, true);
        rowsBody.appendChild(frag);
        const tr = rowsBody.querySelector('tr[data-row]:last-child');
        attachRowHandlers(tr);

        // Bulk rider?
        if (applyOneRiderCb.checked) {
            const riderSel = tr.querySelector('[data-row-rider]');
            syncRowRider(riderSel);
            riderSel.disabled = true;
        }

        // Initial estimate calc
        recalcEstimateForRow(tr);
        computeTotals();
    }

    function sumProduct(priceSelector, qtySelector) {
        let total = 0;
        rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
            const qtyEl   = tr.querySelector(qtySelector);
            const priceEl = tr.querySelector(priceSelector);
            const qty     = qtyEl ? parseFloat(qtyEl.value) || 0 : 0;
            const price   = priceEl ? parseFloat(priceEl.value) || 0 : 0;
            if (qty > 0 && price >= 0) total += qty * price;
        });
        return total;
    }

    function computeTotals() {
        // Estimated total adds up est_price × est_quantity
        let estTotal = 0;
        rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
            const q = parseFloat(tr.querySelector('input[name="est_quantity[]"]')?.value || '0') || 0;
            const p = parseFloat(tr.querySelector('input[name="est_price[]"]')?.value || '0') || 0;
            if (q > 0 && p >= 0) estTotal += q * p;
        });
        estTotalEl.textContent = estTotal.toFixed(2);

        // Actual total
        actTotalEl.textContent = sumProduct('input[name="price[]"]', 'input[name="quantity[]"]').toFixed(2);
    }

    // Keep est_unit_id in sync with actual unit and recalc est_price
    function recalcEstimateForRow(tr) {
        if (!tr) return;
        const flowerSel = tr.querySelector('select[name="flower_id[]"]');
        const estQtyEl  = tr.querySelector('input[name="est_quantity[]"]');
        const estPriceEl= tr.querySelector('input[name="est_price[]"]');
        const unitSel   = tr.querySelector('select[name="unit_id[]"]');
        const estUnitHidden = tr.querySelector('input[name="est_unit_id[]"]');

        const productId = flowerSel ? flowerSel.value : '';
        const estQty    = estQtyEl ? parseFloat(estQtyEl.value) || 0 : 0;
        const unitId    = unitSel ? unitSel.value : '';

        // Mirror est_unit_id to actual unit
        if (estUnitHidden) estUnitHidden.value = unitId || '';

        const pricePerUnit = computeEstimatedPrice(productId, 1, unitId); // price for 1 actual-unit
        const lineEstPrice = computeEstimatedPrice(productId, estQty, unitId);

        // We store per-unit price (est_price) as the price for the chosen unit,
        // so the "Estimated Total" uses (est_qty × est_price)
        // If you prefer est_price to be the line total, set it directly to lineEstPrice and adjust totals accordingly.
        if (estPriceEl) estPriceEl.value = (pricePerUnit || 0).toFixed(2);
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

    // Initial calc for existing rows
    qsa(rowsBody, 'tr[data-row]').forEach(tr => recalcEstimateForRow(tr));
    computeTotals();

    // Rehydrate bulk rider state
    if (applyOneRiderCb.checked) {
        bulkRiderSel.disabled = false;
        syncAllRiders();
    }
});
</script>
@endsection
