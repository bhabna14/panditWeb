@extends('admin.layouts.apps')

@section('styles')
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .table-items th, .table-items td { vertical-align: middle; }
  .table-items select, .table-items input { min-width: 140px; }
  .totals-bar { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: .5rem; padding: .75rem 1rem; }
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
          <div class="col-md-4">
            <label for="vendor_id" class="form-label required">Vendor</label>
            <select id="vendor_id" name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
              <option value="" selected>Choose</option>
              @foreach ($vendors as $vendor)
                <option value="{{ $vendor->vendor_id }}" @selected(old('vendor_id') == $vendor->vendor_id)>{{ $vendor->vendor_name }}</option>
              @endforeach
            </select>
            @error('vendor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4">
            <label for="pickup_date" class="form-label required">Pickup Date</label>
            <input type="date" id="pickup_date" name="pickup_date"
                   class="form-control @error('pickup_date') is-invalid @enderror"
                   value="{{ old('pickup_date', $prefillDate) }}" min="{{ now()->toDateString() }}" required>
            @error('pickup_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4">
            <label for="delivery_date" class="form-label required">Delivery Date</label>
            <input type="date" id="delivery_date" name="delivery_date"
                   class="form-control @error('delivery_date') is-invalid @enderror"
                   value="{{ old('delivery_date', $prefillDate) }}" min="{{ now()->toDateString() }}" required>
            @error('delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
      $rowCountOld  = max(count($oldFlowerIds), count($oldUnitIds), count($oldQtys), count($oldPrices));
      $rows         = $rowCountOld > 0 ? range(0, $rowCountOld-1) : range(0, max(count($prefillRows),1)-1);
    @endphp

    <div class="card mb-3">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Items</strong>
        <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">Add Row</button>
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
                <th style="width:15%"></th>
              </tr>
            </thead>
            <tbody id="rowsBody">
              @foreach ($rows as $i)
                @php
                  $default   = $prefillRows[$i] ?? ['flower_id'=>null,'unit_id'=>null,'quantity'=>null,'price'=>null,'flower_name'=>null,'unit_label'=>null];
                  $flowerVal = $oldFlowerIds[$i] ?? $default['flower_id'];
                  $unitVal   = $oldUnitIds[$i]   ?? $default['unit_id'];
                  $qtyVal    = $oldQtys[$i]      ?? $default['quantity'];
                  $priceVal  = $oldPrices[$i]    ?? $default['price'];
                @endphp
                <tr data-row>
                  <td>
                    <select name="flower_id[]" class="form-control @error('flower_id.'.$i) is-invalid @enderror" required>
                      <option value="" selected>Choose flower</option>
                      @foreach ($flowers as $flower)
                        <option value="{{ $flower->product_id }}" @selected($flowerVal == $flower->product_id)>{{ $flower->name }}</option>
                      @endforeach
                    </select>
                    @if(!$flowerVal && ($default['flower_name'] ?? null))
                      <small class="text-muted">Unmatched: {{ $default['flower_name'] }}</small>
                    @endif
                    @error('flower_id.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </td>
                  <td>
                    <select name="unit_id[]" class="form-control @error('unit_id.'.$i) is-invalid @enderror" required>
                      <option value="" selected>Choose unit</option>
                      @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                      @endforeach
                    </select>
                    @if(!$unitVal && ($default['unit_label'] ?? null))
                      <small class="text-muted">Unmatched: {{ $default['unit_label'] }}</small>
                    @endif
                    @error('unit_id.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </td>
                  <td>
                    <input type="number" name="quantity[]" class="form-control @error('quantity.'.$i) is-invalid @enderror"
                           inputmode="decimal" min="0.01" step="0.01" value="{{ $qtyVal }}" required>
                    @error('quantity.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </td>
                  <td>
                    <input type="number" name="price[]" class="form-control @error('price.'.$i) is-invalid @enderror"
                           inputmode="decimal" min="0" step="0.01" value="{{ $priceVal }}">
                    @error('price.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </td>
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
            <td><input type="number" name="quantity[]" class="form-control" inputmode="decimal" min="0.01" step="0.01" required></td>
            <td><input type="number" name="price[]" class="form-control" inputmode="decimal" min="0" step="0.01"></td>
            <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">Remove</button></td>
          </tr>
        </template>

        <div class="mt-3 d-flex justify-content-end">
          <div class="totals-bar">
            <div><strong>Estimated Total (₹):</strong> <span id="estTotal">0.00</span></div>
            <small class="text-muted">Calculated as Σ(price × qty) of rows with both values.</small>
          </div>
        </div>
      </div>
    </div>

    {{-- Rider --}}
    <div class="card mb-3">
      <div class="card-header bg-white"><strong>Assign Rider</strong></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="rider_id" class="form-label required">Rider</label>
            <select id="rider_id" name="rider_id" class="form-control @error('rider_id') is-invalid @enderror" required>
              <option value="" selected>Choose</option>
              @foreach ($riders as $rider)
                <option value="{{ $rider->rider_id }}" @selected(old('rider_id') == $rider->rider_id)>{{ $rider->rider_name }}</option>
              @endforeach
            </select>
            @error('rider_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
    rowsBody.appendChild(frag);
    attachRowHandlers(rowsBody);
  }

  function computeTotal() {
    let total = 0;
    rowsBody.querySelectorAll('tr[data-row]').forEach(tr => {
      const qty   = parseFloat(tr.querySelector('input[name="quantity[]"]').value) || 0;
      const price = parseFloat(tr.querySelector('input[name="price[]"]').value) || 0;
      if (qty > 0 && price >= 0) total += qty * price;
    });
    estTotalEl.textContent = total.toFixed(2);
  }

  addRowBtn.addEventListener('click', addRow);
  attachRowHandlers(document);
  computeTotal();
});
</script>
@endsection
