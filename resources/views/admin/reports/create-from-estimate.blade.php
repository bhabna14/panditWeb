@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-items th,.table-items td{vertical-align:middle}
        .table-items select,.table-items input{min-width:120px}
        .totals-bar{background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;padding:.75rem 1rem}
        .totals-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
        @media (max-width:768px){.totals-grid{grid-template-columns:1fr}}
        .small-muted{font-size:.875rem;color:#6b7280}
        .readonly-input{background:#f3f4f6}
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
                                   value="{{ old('pickup_date', $todayDate) }}" min="{{ now()->toDateString() }}" required>
                            @error('pickup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="delivery_date" class="form-label required">Delivery Date</label>
                            <input type="date" id="delivery_date" name="delivery_date"
                                   class="form-control @error('delivery_date') is-invalid @enderror"
                                   value="{{ old('delivery_date', $prefillDate) }}" min="{{ now()->toDateString() }}" required>
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

                $rowCountOld = max(count($oldFlowerIds),count($oldUnitIds),count($oldQtys),count($oldPrices),count($oldEstQtys),count($oldVendors),count($oldRiders));
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

                                {{-- Estimate (READ-ONLY) --}}
                                <th style="width:12%">Est. Unit</th>
                                <th style="width:12%">Est. Qty</th>

                                {{-- Actual (editable) --}}
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
                                            'est_quantity' => null,   // server prefill qty (from estimate)
                                            'unit_id'      => null,   // server prefill unit id (from estimate)
                                            'quantity'     => null,
                                            'price'        => null,
                                            'flower_name'  => null,
                                            'unit_label'   => null,
                                        ],
                                        $prefillRows[$i] ?? [],
                                    );

                                    $flowerVal = $oldFlowerIds[$i] ?? $default['flower_id'];
                                    $estQtyVal = $oldEstQtys[$i]   ?? $default['est_quantity'];

                                    // If Actual Qty is empty, default it to the estimated qty so they start equal.
                                    $qtyVal   = $oldQtys[$i] ?? ($default['quantity'] ?? $estQtyVal);
                                    $unitVal  = $oldUnitIds[$i] ?? $default['unit_id'];
                                    $priceVal = $oldPrices[$i]  ?? $default['price'];
                                    $vendorVal= $oldVendors[$i] ?? null;
                                    $riderVal = $oldRiders[$i]  ?? null;
                                @endphp
                                <tr data-row>
                                    {{-- Flower --}}
                                    <td>
                                        <select name="flower_id[]" class="form-control @error('flower_id.' . $i) is-invalid @enderror" required>
                                            <option value="" selected>Choose flower</option>
                                            @foreach ($flowers as $flower)
                                                <option value="{{ $flower->product_id }}" @selected($flowerVal == $flower->product_id)>{{ $flower->name }}</option>
                                            @endforeach
                                        </select>
                                        @if (!$flowerVal && ($default['flower_name'] ?? null))
                                            <small class="text-muted">Unmatched: {{ $default['flower_name'] }}</small>
                                        @endif
                                        @error('flower_id.' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </td>

                                    {{-- Est. Unit (disabled display + hidden submit) --}}
                                    <td>
                                        <select class="form-control readonly-input" data-est-unit-display disabled>
                                            <option value="" {{ $unitVal ? '' : 'selected' }}>Choose</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="est_unit_id[]" value="{{ $unitVal }}">
                                    </td>

                                    {{-- Est. Qty (disabled display + hidden submit; MIRRORS Actual Qty) --}}
                                    <td>
                                        <input type="number" class="form-control readonly-input"
                                               data-est-qty-display disabled
                                               value="{{ $qtyVal ?? $estQtyVal }}">
                                        <input type="hidden" name="est_quantity[]" value="{{ $qtyVal ?? $estQtyVal }}">
                                    </td>

                                    {{-- Actual Unit (editable) --}}
                                    <td>
                                        <select name="unit_id[]" class="form-control @error('unit_id.' . $i) is-invalid @enderror" data-actual-unit>
                                            <option value="" selected>Choose</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('unit_id.' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </td>

                                    {{-- Actual Qty (editable) --}}
                                    <td>
                                        <input type="number" name="quantity[]"
                                               class="form-control @error('quantity.' . $i) is-invalid @enderror"
                                               inputmode="decimal" min="0.01" step="0.01"
                                               value="{{ $qtyVal }}">
                                        @error('quantity.' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </td>

                                    {{-- Actual Price (editable) --}}
                                    <td>
                                        <input type="number" name="price[]"
                                               class="form-control @error('price.' . $i) is-invalid @enderror"
                                               inputmode="decimal" min="0" step="0.01"
                                               value="{{ $priceVal }}">
                                        @error('price.' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </td>

                                    {{-- Vendor --}}
                                    <td>
                                        <select name="row_vendor_id[]" class="form-control @error('row_vendor_id.' . $i) is-invalid @enderror">
                                            <option value="" selected>Choose vendor</option>
                                            @foreach ($vendors as $v)
                                                <option value="{{ $v->vendor_id }}" @selected($vendorVal == $v->vendor_id)>{{ $v->vendor_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('row_vendor_id.' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </td>

                                    {{-- Rider --}}
                                    <td>
                                        <select name="row_rider_id[]" class="form-control row-rider @error('row_rider_id.' . $i) is-invalid @enderror" data-row-rider>
                                            <option value="" selected>Choose rider</option>
                                            @foreach ($riders as $r)
                                                <option value="{{ $r->rider_id }}" @selected($riderVal == $r->rider_id)>{{ $r->rider_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('row_rider_id.' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
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

                    {{-- New row template --}}
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

                            {{-- Est. Unit (disabled display + hidden submit) --}}
                            <td>
                                <select class="form-control readonly-input" data-est-unit-display disabled>
                                    <option value="" selected>Choose</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="est_unit_id[]" value="">
                            </td>

                            {{-- Est. Qty (disabled display + hidden submit; mirrors Actual Qty) --}}
                            <td>
                                <input type="number" class="form-control readonly-input" data-est-qty-display disabled>
                                <input type="hidden" name="est_quantity[]" value="">
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
                        <div class="small-muted">Estimate Unit & Qty are read-only and mirror the Actual fields.</div>
                        <div class="totals-bar ms-auto">
                            <div class="totals-grid">
                                <div><strong>Estimated Total (₹):</strong> <span id="estTotal">0.00</span></div>
                                <div><strong>Actual Total (₹):</strong> <span id="actTotal">0.00</span></div>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Estimated uses Est. Qty × price-per Actual Unit (live prices).
                            </small>
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
        document.addEventListener('DOMContentLoaded', function () {
            const rowsBody   = document.getElementById('rowsBody');
            const rowTpl     = document.getElementById('rowTemplate');
            const addRowBtn  = document.getElementById('addRowBtn');
            const estTotalEl = document.getElementById('estTotal');
            const actTotalEl = document.getElementById('actTotal');

            // Pricing map from server: product_id -> {fd_unit_symbol, fd_unit_id, fd_price}
            const PRICING      = @json($fdProductPricing);
            // Unit id -> canonical symbol ('kg','g','l','ml','pcs')
            const UNIT_SYMBOL  = @json($unitIdToSymbol);

            function symbolToCategory(sym){
                if (!sym) return 'count';
                if (sym==='kg'||sym==='g') return 'weight';
                if (sym==='l'||sym==='ml') return 'volume';
                return 'count';
            }
            function toBaseFactor(sym){
                switch(sym){
                    case 'kg': return 1000;
                    case 'g' : return 1;
                    case 'l' : return 1000;
                    case 'ml': return 1;
                    case 'pcs':
                    default:   return 1;
                }
            }

            // === MIRROR HELPERS ===
            function syncEstimateUnit(tr){
                const unitSel        = tr.querySelector('select[name="unit_id[]"]');
                const estUnitDisplay = tr.querySelector('[data-est-unit-display]');
                const estUnitHidden  = tr.querySelector('input[name="est_unit_id[]"]');
                const val = unitSel?.value || '';
                if (estUnitDisplay) estUnitDisplay.value = val;
                if (estUnitHidden)  estUnitHidden.value  = val;
            }
            function syncEstimateQty(tr){
                const actQty         = tr.querySelector('input[name="quantity[]"]');
                const estQtyDisplay  = tr.querySelector('[data-est-qty-display]');
                const estQtyHidden   = tr.querySelector('input[name="est_quantity[]"]');
                const val = actQty && actQty.value !== '' ? actQty.value : '';
                if (estQtyDisplay) estQtyDisplay.value = val;
                if (estQtyHidden)  estQtyHidden.value  = val;
            }

            // === TOTALS ===
            function computeEstimatedLineTotal(productId, estQty, actualUnitId){
                const info = PRICING[String(productId)];
                if (!info || !estQty || !actualUnitId) return 0;

                const actualSym = UNIT_SYMBOL[String(actualUnitId)];
                const fdSym     = info.fd_unit_symbol;

                if (symbolToCategory(actualSym) !== symbolToCategory(fdSym)) return 0;

                const actualBase = toBaseFactor(actualSym);
                const fdBase     = toBaseFactor(fdSym);

                const fdUnitsCount = (parseFloat(estQty) * actualBase) / fdBase;
                return fdUnitsCount * (parseFloat(info.fd_price) || 0);
            }
            function computeTotals(){
                let estTotal = 0, actTotal = 0;
                rowsBody.querySelectorAll('tr[data-row]').forEach(tr=>{
                    const productId = tr.querySelector('select[name="flower_id[]"]')?.value || '';
                    const estQty    = tr.querySelector('input[name="est_quantity[]"]')?.value || '';
                    const unitId    = tr.querySelector('select[name="unit_id[]"]')?.value || '';

                    if (productId && unitId && estQty){
                        estTotal += computeEstimatedLineTotal(productId, estQty, unitId);
                    }
                    const qty   = parseFloat(tr.querySelector('input[name="quantity[]"]')?.value || '0') || 0;
                    const price = parseFloat(tr.querySelector('input[name="price[]"]')?.value || '0') || 0;
                    if (qty > 0 && price >= 0) actTotal += qty * price;
                });
                estTotalEl.textContent = estTotal.toFixed(2);
                actTotalEl.textContent = actTotal.toFixed(2);
            }

            // === EVENTING ===
            // Add Row
            addRowBtn.addEventListener('click', function(){
                const frag = document.importNode(rowTpl.content, true);
                rowsBody.appendChild(frag);
                const tr = rowsBody.querySelector('tr[data-row]:last-of-type');

                // Start with empty Actual Qty; mirrors to empty Est Qty
                syncEstimateUnit(tr);
                syncEstimateQty(tr);
                computeTotals();
            });

            // Remove Row (delegated)
            rowsBody.addEventListener('click', function(e){
                const btn = e.target.closest('.remove-row-btn');
                if (!btn) return;
                const tr = btn.closest('tr[data-row]');
                if (tr) tr.remove();
                computeTotals();
            });

            // Any change affecting mirroring or totals (delegated)
            rowsBody.addEventListener('input', onRowFieldChange, true);
            rowsBody.addEventListener('change', onRowFieldChange, true);
            function onRowFieldChange(e){
                const tr = e.target.closest('tr[data-row]');
                if (!tr) return;

                // Always mirror unit & qty
                if (e.target.name === 'quantity[]'){ syncEstimateQty(tr); }
                if (e.target.name === 'unit_id[]'){  syncEstimateUnit(tr); }

                // Recompute totals on relevant changes
                const watched = ['quantity[]','price[]','est_quantity[]','unit_id[]','flower_id[]'];
                if (watched.includes(e.target.name)) computeTotals();
            }

            // === INITIAL HYDRATION ===
            // 1) Ensure Est Unit & Qty mirror the initial Actual values
            rowsBody.querySelectorAll('tr[data-row]').forEach(tr=>{
                // If actual qty empty but we rendered a prefill in Est display/hidden,
                // we already defaulted Actual = Estimate on the server side.
                // Mirror again in JS to be safe:
                syncEstimateUnit(tr);
                syncEstimateQty(tr);
            });
            // 2) Compute initial totals
            computeTotals();
        });
    </script>
@endsection
