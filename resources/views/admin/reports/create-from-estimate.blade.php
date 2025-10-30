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
            $oldVendors   = old('row_vendor_id', []);
            $oldRiders    = old('row_rider_id', []);

            $rowCountOld = max(
                count($oldFlowerIds), count($oldUnitIds),
                count($oldQtys), count($oldPrices),
                count($oldEstQtys),
                count($oldVendors), count($oldRiders)
            );
            $rows = $rowCountOld > 0 ? range(0, $rowCountOld - 1) : range(0, max(count($prefillRows), 1) - 1);
        @endphp

        <div class="card mb-3">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Flower Items</strong>
                <button type="button" class="btn btn-outline-primary btn-sm ms-auto" id="addRowBtn">Add Row</button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-items align-middle">
                        <thead class="table-light">
                        <tr>
                            <th style="width:18%">Flower</th>

                            {{-- Estimate --}}
                            <th style="width:12%">Est. Unit</th>
                            <th style="width:12%">Est. Qty</th>

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

                                {{-- Est. Unit (mirrors actual; read-only UI) --}}
                                <td>
                                    {{-- Preselect to $unitVal so it shows immediately --}}
                                    <select class="form-control" data-est-unit-display disabled>
                                        <option value="" {{ $unitVal ? '' : 'selected' }}>Choose</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- Hidden field that actually submits the mirrored unit --}}
                                    <input type="hidden" name="est_unit_id[]" value="{{ $unitVal }}">
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

                        {{-- Est. Unit (display only) --}}
                        <td>
                            <select class="form-control" data-est-unit-display disabled>
                                <option value="" selected>Choose</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="est_unit_id[]" value="">
                        </td>

                        <td>
                            <input type="number" name="est_quantity[]" class="form-control" inputmode="decimal" min="0.01" step="0.01">
                        </td>

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
                        <small class="text-muted d-block mt-1">Estimated uses Est. Qty × price-per Actual Unit (from live prices).</small>
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

    function symbolToCategory(sym) {
        if (!sym) return 'count';
        if (sym === 'kg' || sym === 'g') return 'weight';
        if (sym === 'l'  || sym === 'ml') return 'volume';
        return 'count';
    }
    function toBaseFactor(sym) {
        switch (sym) {
            case 'kg': return 1000;
            case 'g':  return 1;
            case 'l':  return 1000;
            case 'ml': return 1;
            case 'pcs': default: return 1;
        }
    }

    function computeEstimatedLineTotal(productId, estQty, actualUnitId) {
        const info = PRICING[String(productId)];
        if (!info || !estQty || !actualUnitId) return 0;

        const actualSym = UNIT_SYMBOL[String(actualUnitId)];
        const fdSym     = info.fd_unit_symbol;

        const catA = symbolToCategory(actualSym);
        const catB = symbolToCategory(fdSym);
        if (catA !== catB) return 0;

        const actualBase = toBaseFactor(actualSym);
        const fdBase     = toBaseFactor(fdSym);

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
            const handler = () => {
                const tr = inp.closest('tr[data-row]');
                syncEstimateUnit(tr);
                computeTotals();
            };
            inp.addEventListener('input', handler);
            inp.addEventListener('change', handler);
        });
    }

    function addRow() {
        const frag = document.importNode(rowTpl.content, true);
        rowsBody.appendChild(frag);
        const tr = rowsBody.querySelector('tr[data-row]:last-child');
        attachRowHandlers(tr);

        if (applyOneRiderCb.checked) {
            const riderSel = tr.querySelector('[data-row-rider]');
            syncRowRider(riderSel);
            riderSel.disabled = true;
        }

        syncEstimateUnit(tr);
        computeTotals();
    }

    function computeTotals() {
        let estTotal = 0;
        rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
            const productId = tr.querySelector('select[name="flower_id[]"]')?.value || '';
            const estQty    = parseFloat(tr.querySelector('input[name="est_quantity[]"]')?.value || '0') || 0;
            const unitId    = tr.querySelector('select[name="unit_id[]"]')?.value || '';
            if (productId && unitId && estQty > 0) {
                estTotal += computeEstimatedLineTotal(productId, estQty, unitId);
            }
        });
        estTotalEl.textContent = estTotal.toFixed(2);

        let actTotal = 0;
        rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
            const qty   = parseFloat(tr.querySelector('input[name="quantity[]"]')?.value || '0') || 0;
            const price = parseFloat(tr.querySelector('input[name="price[]"]')?.value || '0') || 0;
            if (qty > 0 && price >= 0) actTotal += qty * price;
        });
        actTotalEl.textContent = actTotal.toFixed(2);
    }

    // Mirror actual unit → est unit (display + hidden field)
    function syncEstimateUnit(tr) {
        if (!tr) return;
        const unitSel        = tr.querySelector('select[name="unit_id[]"]');
        const estUnitDisplay = tr.querySelector('[data-est-unit-display]');
        const estUnitHidden  = tr.querySelector('input[name="est_unit_id[]"]');
        const value = unitSel?.value || '';
        if (estUnitDisplay) estUnitDisplay.value = value;
        if (estUnitHidden)  estUnitHidden.value  = value;
    }

    // Bulk Rider logic
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

    // Init
    addRowBtn.addEventListener('click', addRow);
    attachRowHandlers(document);

    // First render: ensure Est. Unit shows
    Array.from(rowsBody.querySelectorAll('tr[data-row]')).forEach(tr => syncEstimateUnit(tr));
    computeTotals();

    if (applyOneRiderCb.checked) {
        bulkRiderSel.disabled = false;
        syncAllRiders();
    }
});
</script>
@endsection
