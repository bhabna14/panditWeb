@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Assign Vendor for Items — {{ \Carbon\Carbon::parse($prefillDate)->toFormattedDateString() }}</h4>
        <a href="{{ route('admin.flowerEstimate', ['preset' => 'tomorrow']) }}" class="btn btn-outline-secondary">Back to Estimate</a>
    </div>

    <form method="POST" action="{{ route('admin.saveFlowerPickupAssignRider') }}" novalidate>
        @csrf

        {{-- Pickup Information --}}
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>Pickup Information</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label required">Vendor</label>
                        <select id="vendor_id" name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
                            <option value="" selected>Choose</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->vendor_id }}" @selected(old('vendor_id') == $vendor->vendor_id)>{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="pickup_date" class="form-label required">Pickup Date</label>
                        <input type="date" id="pickup_date" name="pickup_date" class="form-control @error('pickup_date') is-invalid @enderror"
                               value="{{ old('pickup_date', $prefillDate) }}" min="{{ now()->toDateString() }}" required>
                        @error('pickup_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Flower Items --}}
        @php
            // Rebuild rows from old() upon validation error, else from prefill
            $oldFlowerIds = old('flower_id', []);
            $oldUnitIds   = old('unit_id', []);
            $oldQtys      = old('quantity', []);
            $rowCountOld  = max(count($oldFlowerIds), count($oldUnitIds), count($oldQtys));
            $rows = $rowCountOld > 0 ? range(0, $rowCountOld-1) : range(0, max(count($prefillRows),1)-1);
        @endphp

        <div class="card mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Flower Items</strong>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">Add Row</button>
            </div>
            <div class="card-body">
                <div id="rowsContainer">
                    @foreach ($rows as $i)
                        @php
                            $default = $prefillRows[$i] ?? ['flower_id'=>null,'unit_id'=>null,'quantity'=>null,'flower_name'=>null,'unit_label'=>null];
                            $flowerVal = $oldFlowerIds[$i] ?? $default['flower_id'];
                            $unitVal   = $oldUnitIds[$i]   ?? $default['unit_id'];
                            $qtyVal    = $oldQtys[$i]      ?? $default['quantity'];
                        @endphp
                        <div class="border rounded p-3 mb-3" data-row>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label required">Flower</label>
                                    <select name="flower_id[]" class="form-control @error('flower_id.'.$i) is-invalid @enderror" required>
                                        <option value="" selected>Choose flower</option>
                                        @foreach ($flowers as $flower)
                                            <option value="{{ $flower->product_id }}" @selected($flowerVal == $flower->product_id)>{{ $flower->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(!$flowerVal && $default['flower_name'])
                                        <small class="text-muted">Unmatched item name: {{ $default['flower_name'] }}</small>
                                    @endif
                                    @error('flower_id.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Unit</label>
                                    <select name="unit_id[]" class="form-control @error('unit_id.'.$i) is-invalid @enderror" required>
                                        <option value="" selected>Choose unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected($unitVal == $unit->id)>{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                    @if(!$unitVal && $default['unit_label'])
                                        <small class="text-muted">Unmatched unit: {{ $default['unit_label'] }}</small>
                                    @endif
                                    @error('unit_id.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Quantity</label>
                                    <input type="number" name="quantity[]" class="form-control @error('quantity.'.$i) is-invalid @enderror"
                                           placeholder="e.g. 10" inputmode="decimal" min="0.01" step="0.01"
                                           value="{{ $qtyVal }}" required>
                                    @error('quantity.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button type="button" class="btn btn-outline-danger remove-row-btn">Remove</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <template id="rowTemplate">
                    <div class="border rounded p-3 mb-3" data-row>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label required">Flower</label>
                                <select name="flower_id[]" class="form-control" required>
                                    <option value="" selected>Choose flower</option>
                                    @foreach ($flowers as $flower)
                                        <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Unit</label>
                                <select name="unit_id[]" class="form-control" required>
                                    <option value="" selected>Choose unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Quantity</label>
                                <input type="number" name="quantity[]" class="form-control" placeholder="e.g. 10"
                                       inputmode="decimal" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-md-1 d-grid">
                                <button type="button" class="btn btn-outline-danger remove-row-btn">Remove</button>
                            </div>
                        </div>
                    </div>
                </template>
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
    const rowsContainer = document.getElementById('rowsContainer');
    const rowTemplate   = document.getElementById('rowTemplate');
    const addRowBtn     = document.getElementById('addRowBtn');

    function attachRemoveHandlers(root) {
        (root || document).querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.currentTarget.closest('[data-row]').remove();
            });
        });
    }

    addRowBtn.addEventListener('click', () => {
        const frag = document.importNode(rowTemplate.content, true);
        rowsContainer.appendChild(frag);
        attachRemoveHandlers(rowsContainer);
    });

    attachRemoveHandlers(document);
});
</script>
@endsection
