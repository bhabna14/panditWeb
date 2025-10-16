@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .hidden {
            display: none !important;
        }

        .img-preview {
            max-height: 90px;
            display: inline-block;
            margin-top: 6px;
            border-radius: 8px;
        }

        .form-text-muted {
            font-size: .85rem;
            color: #6c757d;
        }

        .readonly-input {
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Edit Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ url('admin/manage-product') }}" class="btn btn-warning text-dark">Manage Product</a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Edit Product</li>
            </ol>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">{{ session('success') }}</div>
    @endif
    @if (session()->has('danger'))
        <div class="alert alert-danger" id="Message">{{ session('danger') }}</div>
    @endif

    <form action="{{ route('admin.update-product', $product->id) }}" method="post" enctype="multipart/form-data"
        id="categorySmartForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Category -->
            <div class="col-md-4 mb-3">
                <label class="form-label"><span id="label-category">Category</span></label>
                <select name="category" id="category" class="form-control select2" required>
                    <option value="" disabled>Select Category</option>
                    @php $cats = ['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books']; @endphp
                    @foreach ($cats as $cat)
                        <option value="{{ $cat }}" {{ $product->category === $cat ? 'selected' : '' }}>
                            {{ $cat }}</option>
                    @endforeach
                </select>
                <div class="form-text-muted mt-1">Switching category will show/hide relevant fields; review before saving.
                </div>
            </div>

            <!-- Core -->
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-name">Product Name</span></label>
                <input type="text" name="name" class="form-control" id="name"
                    value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
            </div>
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-odia-name">Product Name (Odia)</span></label>
                <input type="text" name="odia_name" class="form-control" id="odia_name"
                    value="{{ old('odia_name', $product->odia_name) }}" placeholder="Enter product name in Odia">
            </div>
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-mrp">MRP (Rs.)</span></label>
                <input type="number" name="mrp" class="form-control" id="mrp" min="0" step="0.01"
                    value="{{ old('mrp', $product->mrp) }}" required>
            </div>
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-price">Sale Price (Rs.)</span></label>
                <input type="number" name="price" class="form-control" id="price" min="0" step="0.01"
                    value="{{ old('price', $product->price) }}" required>
            </div>
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-discount">Discount (%)</span></label>
                <input type="number" name="discount" class="form-control" id="discount" min="0" max="100"
                    step="0.01" value="{{ old('discount', $product->discount) }}">
            </div>

            <!-- Stock -->
            <div class="col-md-4 mb-3 controlled" id="stockGroup" data-block="stock">
                <label class="form-label"><span id="label-stock">Stock</span></label>
                <input type="number" name="stock" class="form-control" id="stock" min="0"
                    value="{{ old('stock', $product->stock) }}">
            </div>

            <!-- Image -->
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-image">Product Image</span></label>
                <input type="file" name="product_image" class="form-control" id="product_image" accept="image/*">
                @if ($product->product_image)
                    <div class="mt-2">
                        <img id="imagePreview" class="img-preview" src="{{ $product->product_image }}"
                            alt="Current image">
                        <div class="form-text-muted">Current image. Choose a new file to replace.</div>
                    </div>
                @else
                    <img id="imagePreview" class="img-preview" alt="Preview" style="display:none;">
                @endif
            </div>

            <!-- Flower toggles -->
            <div class="col-md-4 mb-3 controlled" id="malaProvidedField" data-block="flower">
                <label class="form-label"><span id="label-mala">Is Mala provided with this flower?</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaYes" value="yes"
                        {{ old('mala_provided', $product->mala_provided === null ? null : ($product->mala_provided ? 'yes' : 'no')) === 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="malaYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaNo" value="no"
                        {{ old('mala_provided', $product->mala_provided === null ? null : ($product->mala_provided ? 'yes' : 'no')) === 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="malaNo">No</label>
                </div>
            </div>

            <div class="col-md-4 mb-3 controlled" id="flowerAvailabilityField" data-block="flower">
                <label class="form-label"><span id="label-availability">Is this flower available?</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerActive"
                        value="yes"
                        {{ old('flower_available', $product->is_flower_available === null ? null : ($product->is_flower_available ? 'yes' : 'no')) === 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="flowerActive">Active</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerInactive"
                        value="no"
                        {{ old('flower_available', $product->is_flower_available === null ? null : ($product->is_flower_available ? 'yes' : 'no')) === 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="flowerInactive">Inactive</label>
                </div>
            </div>

            <div class="col-md-4 mb-3 controlled" id="flowerFromField" data-block="flowerDates">
                <label class="form-label"><span id="label-available-from">Available From</span></label>
                <input type="date" name="available_from" id="available_from" class="form-control"
                    value="{{ old('available_from', $product->available_from ? \Carbon\Carbon::parse($product->available_from)->format('Y-m-d') : null) }}">
            </div>

            <div class="col-md-4 mb-3 controlled" id="flowerToField" data-block="flowerDates">
                <label class="form-label"><span id="label-available-to">Available To</span></label>
                <input type="date" name="available_to" id="available_to" class="form-control"
                    value="{{ old('available_to', $product->available_to ? \Carbon\Carbon::parse($product->available_to)->format('Y-m-d') : null) }}">
            </div>

            <!-- Package -->
            <div class="col-md-4 mb-3 controlled" id="poojafields" data-block="package">
                <label class="form-label"><span id="label-pooja">Pooja (Festival)</span></label>
                <select class="form-control select2" id="pooja_id" name="pooja_id">
                    <option value="">Select Festival</option>
                    @foreach ($pooja_list as $pooja)
                        <option value="{{ $pooja->id }}"
                            {{ (string) old('pooja_id', $product->pooja_id) === (string) $pooja->id ? 'selected' : '' }}>
                            {{ $pooja->pooja_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="packageFields" class="col-md-12 mb-3 controlled" data-block="package">
                <label class="form-label d-block mb-2"><span id="label-package-items">Package Items</span></label>

                <div id="packageItems">
                    @php
                        $pkgItems = $product->category === 'Package' ? $product->packageItems : collect();
                    @endphp

                    @if ($pkgItems->isNotEmpty())
                        @foreach ($pkgItems as $idx => $packageItem)
                            @php
                                // try to preselect by matching name
                                $selected = optional($flowerDetails->firstWhere('name', $packageItem->item_name))->id;
                            @endphp
                            <div class="row mb-3 package-row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Item</label>
                                    <select class="form-control select2 item-select" name="item_id[]" required>
                                        <option value="">Select Item</option>
                                        @foreach ($flowerDetails as $it)
                                            <option value="{{ $it->id }}" data-unit="{{ $it->unit }}"
                                                data-price="{{ $it->price }}"
                                                {{ (string) old('item_id.' . $idx, $selected) === (string) $it->id ? 'selected' : '' }}>
                                                {{ $it->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control qty-input" name="quantity[]"
                                        min="0" step="any"
                                        value="{{ old('quantity.' . $idx, $packageItem->quantity) }}" placeholder="0"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <input type="text" class="form-control unit-input readonly-input"
                                        name="unit_text[]" placeholder="Auto"
                                        value="{{ old('unit_text.' . $idx, $packageItem->unit) }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Item Price (Rs.)</label>
                                    <input type="number" class="form-control price-input" name="item_price[]"
                                        min="0" step="0.01"
                                        value="{{ old('item_price.' . $idx, number_format($packageItem->price, 2, '.', '')) }}"
                                        placeholder="0.00" required readonly>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row mb-3 package-row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Item</label>
                                <select class="form-control select2 item-select" name="item_id[]">
                                    <option value="">Select Item</option>
                                    @foreach ($flowerDetails as $it)
                                        <option value="{{ $it->id }}" data-unit="{{ $it->unit }}"
                                            data-price="{{ $it->price }}">
                                            {{ $it->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control qty-input" name="quantity[]" min="0"
                                    step="any" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <input type="text" class="form-control unit-input readonly-input" name="unit_text[]"
                                    placeholder="Auto" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Item Price (Rs.)</label>
                                <input type="number" class="form-control price-input" name="item_price[]"
                                    min="0" step="0.01" placeholder="0.00" readonly>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="addMore" class="btn btn-secondary">Add More</button>
                <button type="button" id="removeLast" class="btn btn-danger">Remove Last</button>
            </div>

            <!-- Subscription -->
            <div class="col-md-4 mb-3 controlled" id="durationGroup" data-block="subscription">
                <label class="form-label"><span id="label-duration">Subscription Duration (Months)</span></label>
                <select name="duration" id="duration" class="form-control select2">
                    <option value="" disabled>Select Package</option>
                    <option value="1" {{ (string) old('duration', $product->duration) === '1' ? 'selected' : '' }}>1
                        Month</option>
                    <option value="3" {{ (string) old('duration', $product->duration) === '3' ? 'selected' : '' }}>3
                        Months</option>
                    <option value="6" {{ (string) old('duration', $product->duration) === '6' ? 'selected' : '' }}>6
                        Months</option>
                </select>
            </div>

            <div id="subscriptionDayFields" class="col-md-12 mb-3 controlled" data-block="subscription">
                <label class="form-label d-block mb-2"><span id="label-subscription-prices">Per-Day Price</span></label>
                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Per-Day Price (Rs.)</label>
                        <input type="number" class="form-control" name="per_day_price" min="0" step="0.01"
                            value="{{ old('per_day_price', $product->per_day_price) }}" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div id="subscriptionItemFields" class="col-md-12 mb-3 controlled" data-block="subscription">
                <label class="form-label d-block mb-2"><span id="label-subscription-items">Subscription
                        Items</span></label>
                <div id="subscriptionItems">
                    @php
                        $subItems = $product->category === 'Subscription' ? $product->packageItems : collect();
                    @endphp

                    @if ($subItems->isNotEmpty())
                        @foreach ($subItems as $idx => $sItem)
                            @php
                                $selected = optional($flowerDetails->firstWhere('name', $sItem->item_name))->id;
                            @endphp
                            <div class="row mb-3 subscription-item-row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Item</label>
                                    <select class="form-control select2 item-select" name="sub_item_id[]" required>
                                        <option value="">Select Item</option>
                                        @foreach ($flowerDetails as $it)
                                            <option value="{{ $it->id }}" data-unit="{{ $it->unit }}"
                                                data-price="{{ $it->price }}"
                                                {{ (string) old('sub_item_id.' . $idx, $selected) === (string) $it->id ? 'selected' : '' }}>
                                                {{ $it->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control qty-input" name="sub_quantity[]"
                                        min="0" step="any"
                                        value="{{ old('sub_quantity.' . $idx, $sItem->quantity) }}" placeholder="0"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <input type="text" class="form-control unit-input readonly-input"
                                        name="sub_unit_text[]" value="{{ old('sub_unit_text.' . $idx, $sItem->unit) }}"
                                        placeholder="Auto" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Item Price (Rs.)</label>
                                    <input type="number" class="form-control price-input" name="sub_item_price[]"
                                        min="0" step="0.01"
                                        value="{{ old('sub_item_price.' . $idx, number_format($sItem->price, 2, '.', '')) }}"
                                        placeholder="0.00" required readonly>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row mb-3 subscription-item-row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Item</label>
                                <select class="form-control select2 item-select" name="sub_item_id[]">
                                    <option value="">Select Item</option>
                                    @foreach ($flowerDetails as $it)
                                        <option value="{{ $it->id }}" data-unit="{{ $it->unit }}"
                                            data-price="{{ $it->price }}">
                                            {{ $it->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control qty-input" name="sub_quantity[]"
                                    min="0" step="any" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <input type="text" class="form-control unit-input readonly-input"
                                    name="sub_unit_text[]" placeholder="Auto" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Item Price (Rs.)</label>
                                <input type="number" class="form-control price-input" name="sub_item_price[]"
                                    min="0" step="0.01" placeholder="0.00" readonly>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="addSubscriptionItemRow" class="btn btn-secondary">Add Item</button>
                <button type="button" id="removeSubscriptionItemRow" class="btn btn-danger">Remove Last Item</button>
            </div>

            <!-- Benefits -->
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-benefit">Benefits</span></label>
                <div id="benefitFields">
                    @php
                        $benefits = collect(explode('#', (string) $product->benefits))
                            ->map(fn($b) => trim($b))
                            ->filter()
                            ->values();
                        if ($benefits->isEmpty()) {
                            $benefits = collect(['']);
                        }
                    @endphp
                    @foreach ($benefits as $i => $b)
                        <div class="input-group mb-2 benefit-row">
                            <input type="text" name="benefit[]" class="form-control"
                                value="{{ old('benefit.' . $i, $b) }}" placeholder="Enter benefit"
                                {{ $loop->first ? 'required' : '' }}>
                            <button type="button" class="btn btn-success add-benefit"><i class="fa fa-plus"></i>
                                Add</button>
                            <button type="button" class="btn btn-danger remove-benefit"
                                style="display: {{ $benefits->count() > 1 ? 'inline-block' : 'none' }};"><i
                                    class="fa fa-minus"></i> Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Description -->
            <div class="col-md-12 mb-3 controlled" data-block="core">
                <label class="form-label"><span id="label-description">Description</span></label>
                <textarea name="description" class="form-control" id="description" rows="3" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="col-md-12 mt-4 controlled" data-block="core">
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        setTimeout(() => $('#Message').fadeOut('fast'), 2500);

        (function() {
            function initSelect2(scope) {
                const $s = scope ? $(scope) : $(document);
                $s.find('.select2').each(function() {
                    if (!$(this).data('select2')) $(this).select2({
                        width: '100%'
                    });
                });
            }

            function recalcRow(row) {
                const sel = row.querySelector('.item-select');
                const qty = row.querySelector('.qty-input');
                const unitEl = row.querySelector('.unit-input');
                const priceEl = row.querySelector('.price-input');
                if (!sel || !qty || !unitEl || !priceEl) return;

                const opt = sel.options[sel.selectedIndex];
                const unit = opt ? (opt.getAttribute('data-unit') || '') : '';
                const per = opt ? parseFloat(opt.getAttribute('data-price') || '0') : 0;
                const q = parseFloat(qty.value || '0');
                unitEl.value = unit || unitEl.value; // keep existing when switching categories
                priceEl.value = (per * (isNaN(q) ? 0 : q)).toFixed(2);
            }

            function wireRow(row) {
                const sel = row.querySelector('.item-select');
                const qty = row.querySelector('.qty-input');
                if (sel) {
                    $(sel).on('change select2:select', () => recalcRow(row));
                }
                if (qty) {
                    qty.addEventListener('input', () => recalcRow(row));
                    qty.addEventListener('change', () => recalcRow(row));
                }
                // initial pass
                recalcRow(row);
            }

            function addRow(container, html) {
                const t = document.createElement('div');
                t.innerHTML = html.trim();
                const node = t.firstElementChild;
                container.appendChild(node);
                initSelect2(node);
                wireRow(node);
            }

            // Templates using $flowerDetails
            const tplPackageRow = `
    <div class="row mb-3 package-row align-items-end">
        <div class="col-md-4">
            <label class="form-label">Item</label>
            <select class="form-control select2 item-select" name="item_id[]">
                <option value="">Select Item</option>
                @foreach ($flowerDetails as $it)
                    <option value="{{ $it->id }}" data-unit="{{ $it->unit }}" data-price="{{ $it->price }}">{{ $it->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Qty</label>
            <input type="number" class="form-control qty-input" name="quantity[]" min="0" step="any" placeholder="0">
        </div>
        <div class="col-md-3">
            <label class="form-label">Unit</label>
            <input type="text" class="form-control unit-input readonly-input" name="unit_text[]" placeholder="Auto" readonly>
        </div>
        <div class="col-md-3">
            <label class="form-label">Item Price (Rs.)</label>
            <input type="number" class="form-control price-input" name="item_price[]" min="0" step="0.01" placeholder="0.00" readonly>
        </div>
    </div>`;

            const tplSubscriptionItemRow = `
    <div class="row mb-3 subscription-item-row align-items-end">
        <div class="col-md-4">
            <label class="form-label">Item</label>
            <select class="form-control select2 item-select" name="sub_item_id[]">
                <option value="">Select Item</option>
                @foreach ($flowerDetails as $it)
                    <option value="{{ $it->id }}" data-unit="{{ $it->unit }}" data-price="{{ $it->price }}">{{ $it->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Qty</label>
            <input type="number" class="form-control qty-input" name="sub_quantity[]" min="0" step="any" placeholder="0">
        </div>
        <div class="col-md-3">
            <label class="form-label">Unit</label>
            <input type="text" class="form-control unit-input readonly-input" name="sub_unit_text[]" placeholder="Auto" readonly>
        </div>
        <div class="col-md-3">
            <label class="form-label">Item Price (Rs.)</label>
            <input type="number" class="form-control price-input" name="sub_item_price[]" min="0" step="0.01" placeholder="0.00" readonly>
        </div>
    </div>`;

            // Buttons + wiring
            const packageItems = document.getElementById('packageItems');
            const addMore = document.getElementById('addMore');
            const removeLast = document.getElementById('removeLast');
            const subscriptionItems = document.getElementById('subscriptionItems');
            const addSub = document.getElementById('addSubscriptionItemRow');
            const removeSub = document.getElementById('removeSubscriptionItemRow');

            if (addMore) addMore.addEventListener('click', () => addRow(packageItems, tplPackageRow));
            if (removeLast) removeLast.addEventListener('click', () => {
                const rows = packageItems.querySelectorAll('.package-row');
                if (rows.length > 1) rows[rows.length - 1].remove();
            });
            if (addSub) addSub.addEventListener('click', () => addRow(subscriptionItems, tplSubscriptionItemRow));
            if (removeSub) removeSub.addEventListener('click', () => {
                const rows = subscriptionItems.querySelectorAll('.subscription-item-row');
                if (rows.length > 1) rows[rows.length - 1].remove();
            });

            // Benefit add/remove
            const benefitFields = document.getElementById('benefitFields');
            benefitFields.addEventListener('click', function(e) {
                const addBtn = e.target.closest('.add-benefit');
                const rmvBtn = e.target.closest('.remove-benefit');
                if (addBtn) {
                    const row = addBtn.closest('.benefit-row');
                    const clone = row.cloneNode(true);
                    const input = clone.querySelector('input');
                    input.value = '';
                    input.required = false;
                    clone.querySelector('.remove-benefit').style.display = 'inline-block';
                    benefitFields.appendChild(clone);
                }
                if (rmvBtn) {
                    const rows = benefitFields.querySelectorAll('.benefit-row');
                    if (rows.length > 1) rmvBtn.closest('.benefit-row').remove();
                }
            });

            // Select2 init
            initSelect2();

            // Wire existing rows for auto-calc
            document.querySelectorAll('.package-row, .subscription-item-row').forEach(wireRow);

            // Image preview
            const imgInput = document.getElementById('product_image');
            const imgPreview = document.getElementById('imagePreview');
            if (imgInput && imgPreview) {
                imgInput.addEventListener('change', function() {
                    const f = this.files && this.files[0];
                    if (!f) return;
                    const r = new FileReader();
                    r.onload = e => {
                        imgPreview.src = e.target.result;
                        imgPreview.style.display = 'inline-block';
                    };
                    r.readAsDataURL(f);
                });
            }

            // Category show/hide (kept as in your original)
            function enableGroup(list, on) {
                list.forEach(el => {
                    el.classList.toggle('hidden', !on);
                    el.querySelectorAll('input,select,textarea,button').forEach(i => i.disabled = !on);
                });
            }
            const groups = {
                core: document.querySelectorAll('[data-block="core"]'),
                stock: document.querySelectorAll('[data-block="stock"]'),
                subscription: document.querySelectorAll('[data-block="subscription"]'),
                flower: document.querySelectorAll('[data-block="flower"]'),
                flowerDates: document.querySelectorAll('[data-block="flowerDates"]'),
                package: document.querySelectorAll('[data-block="package"]'),
            };
            const VISIBILITY = {
                'Flower': {
                    core: true,
                    stock: false,
                    subscription: false,
                    flower: true,
                    flowerDates: true,
                    package: false
                },
                'Package': {
                    core: true,
                    stock: true,
                    subscription: false,
                    flower: false,
                    flowerDates: false,
                    package: true
                },
                'Subscription': {
                    core: true,
                    stock: true,
                    subscription: true,
                    flower: false,
                    flowerDates: false,
                    package: false
                },
                'Puja Item': {
                    core: true,
                    stock: true,
                    subscription: false,
                    flower: false,
                    flowerDates: false,
                    package: false
                },
                'Immediateproduct': {
                    core: true,
                    stock: true,
                    subscription: false,
                    flower: false,
                    flowerDates: false,
                    package: false
                },
                'Customizeproduct': {
                    core: true,
                    stock: true,
                    subscription: false,
                    flower: false,
                    flowerDates: false,
                    package: false
                },
                'Books': {
                    core: true,
                    stock: true,
                    subscription: false,
                    flower: false,
                    flowerDates: false,
                    package: false
                },
                default: {
                    core: true,
                    stock: true,
                    subscription: false,
                    flower: false,
                    flowerDates: false,
                    package: false
                }
            };
            const categorySelect = document.getElementById('category');

            function applyCategoryRules() {
                const cat = categorySelect.value;
                const v = VISIBILITY[cat] || VISIBILITY.default;
                enableGroup(groups.core, !!v.core);
                enableGroup(groups.stock, !!v.stock);
                enableGroup(groups.subscription, !!v.subscription);
                enableGroup(groups.flower, !!v.flower);
                enableGroup(groups.flowerDates, !!v.flowerDates);
                enableGroup(groups.package, !!v.package);
            }
            $('#category').on('change select2:select', applyCategoryRules);
            applyCategoryRules();
        })();
    </script>
@endsection
