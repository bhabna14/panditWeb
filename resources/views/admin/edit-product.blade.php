@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
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
        <div class="alert alert-success" id="Message">{{ session()->get('success') }}</div>
    @endif
    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">{{ $errors->first('danger') }}</div>
    @endif

    <form action="{{ route('admin.update-product', $product->id) }}" method="post" enctype="multipart/form-data">
        @csrf

        @php
            $malaDefault = old(
                'mala_provided',
                is_null($product->mala_provided) ? '' : ($product->mala_provided ? 'yes' : 'no'),
            );
            $availDefault = old(
                'flower_available',
                is_null($product->is_flower_available) ? '' : ($product->is_flower_available ? 'yes' : 'no'),
            );

            // Old inputs if validation failed
            $oldItemIds = old('item_id', []);
            $oldQtys = old('quantity', []);
            $oldUnitIds = old('unit_id', []);
            $oldPrices = old('item_price', []);
            $hasOldRows = is_array($oldItemIds) && count($oldItemIds) > 0;

            // Prefill rows from controller
            $prefill = [];
            if (!$hasOldRows && !empty($packageItems) && is_array($packageItems)) {
                foreach ($packageItems as $row) {
                    $prefill[] = [
                        'item_id' => $row['item_id'] ?? null,
                        'quantity' => $row['quantity'] ?? null,
                        'unit_id' => $row['unit_id'] ?? null,
                        'price' => $row['price'] ?? null,
                        'item_label' => $row['item_label'] ?? null,
                        'unit_label' => $row['unit_label'] ?? null,
                        'item_not_found' => $row['item_not_found'] ?? false,
                        'unit_not_found' => $row['unit_not_found'] ?? false,
                    ];
                }
            }

            $rowsCount = max(count($oldItemIds), count($prefill), 1);

            $currentCat = old('category', $product->category);
            $isPackage = $currentCat === 'Package';
            $isSubscription = $currentCat === 'Subscription';
            $showLineRows = $isPackage || $isSubscription; // show for both
        @endphp

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" id="name"
                    value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="odia_name" class="form-label">Product Name (Odia)</label>
                <input type="text" name="odia_name" class="form-control" id="odia_name"
                    value="{{ old('odia_name', $product->odia_name) }}">
            </div>

            <div class="col-md-3 mb-3">
                <label for="mrp" class="form-label">MRP (Rs.)</label>
                <input type="number" name="mrp" class="form-control" id="mrp"
                    value="{{ old('mrp', $product->mrp) }}" min="0" step="0.01" required>
            </div>

            <div class="col-md-3 mb-3">
                <label for="price" class="form-label">Price (Rs.)</label>
                <input type="number" name="price" class="form-control" id="price"
                    value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
            </div>

            <div class="col-md-3 mb-3">
                <label for="discount" class="form-label">Discount (%)</label>
                <input type="number" name="discount" class="form-control" id="discount"
                    value="{{ old('discount', $product->discount) }}" min="0" max="100" step="0.01">
            </div>

            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control select2" required>
                    <option value="Puja Item" {{ $currentCat == 'Puja Item' ? 'selected' : '' }}>Puja Item</option>
                    <option value="Subscription" {{ $currentCat == 'Subscription' ? 'selected' : '' }}>Subscription
                    </option>
                    <option value="Flower" {{ $currentCat == 'Flower' ? 'selected' : '' }}>Flower</option>
                    <option value="Immediateproduct" {{ $currentCat == 'Immediateproduct' ? 'selected' : '' }}>Customize
                        Flower</option>
                    <option value="Customizeproduct" {{ $currentCat == 'Customizeproduct' ? 'selected' : '' }}>Customize
                        Product</option>
                    <option value="Package" {{ $currentCat == 'Package' ? 'selected' : '' }}>Package</option>
                    <option value="Books" {{ $currentCat == 'Books' ? 'selected' : '' }}>Books</option>
                </select>
            </div>

            {{-- Flower-only --}}
            <div class="col-md-4 mb-3" id="malaProvidedField" style="display:none;">
                <label class="form-label">Is Mala Provided with this Flower?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaYes" value="yes"
                        {{ $malaDefault === 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="malaYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaNo" value="no"
                        {{ $malaDefault === 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="malaNo">No</label>
                </div>
            </div>

            <div class="col-md-4 mb-3" id="flowerAvailabilityField" style="display:none;">
                <label class="form-label">Is this Flower Available?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerActive"
                        value="yes" {{ $availDefault === 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="flowerActive">Active</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerInactive"
                        value="no" {{ $availDefault === 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="flowerInactive">Inactive</label>
                </div>
            </div>

            <div class="col-md-4 mb-3" id="flowerFromField" style="display:none;">
                <label for="available_from" class="form-label">Available From</label>
                <input type="date" name="available_from" id="available_from" class="form-control"
                    value="{{ old('available_from', $product->available_from) }}">
            </div>

            <div class="col-md-4 mb-3" id="flowerToField" style="display:none;">
                <label for="available_to" class="form-label">Available To</label>
                <input type="date" name="available_to" id="available_to" class="form-control"
                    value="{{ old('available_to', $product->available_to) }}">
            </div>

            {{-- Subscription-only: Duration --}}
            <div class="col-md-4 mb-3" id="durationField" style="{{ $isSubscription ? '' : 'display:none;' }}">
                <label for="duration" class="form-label">Subscription Duration (Months)</label>
                <select name="duration" id="duration" class="form-control select2">
                    <option value="" {{ old('duration', $product->duration) === null ? 'selected' : '' }}>— Select —
                    </option>
                    <option value="1" {{ (string) old('duration', $product->duration) === '1' ? 'selected' : '' }}>1
                    </option>
                    <option value="3" {{ (string) old('duration', $product->duration) === '3' ? 'selected' : '' }}>3
                    </option>
                    <option value="6" {{ (string) old('duration', $product->duration) === '6' ? 'selected' : '' }}>6
                    </option>
                </select>
            </div>

            {{-- Subscription-only: Per-Day Price --}}
            <div class="col-md-4 mb-3" id="perDayPriceField" style="{{ $isSubscription ? '' : 'display:none;' }}">
                <label for="per_day_price" class="form-label">Per-Day Price (Rs.)</label>
                <input type="number" name="per_day_price" id="per_day_price" class="form-control"
                    value="{{ old('per_day_price', $product->per_day_price) }}" min="0" step="0.01">
            </div>

            {{-- Line item fields (Item + Qty + Unit + Item Price) for Package & Subscription --}}
            <div id="packageFields" class="col-md-12 mb-3" style="{{ $showLineRows ? '' : 'display:none;' }}">
                <div id="packageItems">
                    @for ($i = 0; $i < $rowsCount; $i++)
                        @php
                            $itemId = $oldItemIds[$i] ?? ($prefill[$i]['item_id'] ?? null);
                            $qty = $oldQtys[$i] ?? ($prefill[$i]['quantity'] ?? null);
                            $unitId = $oldUnitIds[$i] ?? ($prefill[$i]['unit_id'] ?? null);
                            $price = $oldPrices[$i] ?? ($prefill[$i]['price'] ?? null);
                            $itemLabel = $prefill[$i]['item_label'] ?? null;
                            $unitLabel = $prefill[$i]['unit_label'] ?? null;
                            $itemNF = $prefill[$i]['item_not_found'] ?? false;
                            $unitNF = $prefill[$i]['unit_not_found'] ?? false;
                        @endphp

                        <div class="row mb-3 package-row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Item</label>
                                <select class="form-control select2 item-select" name="item_id[]" required>
                                    {{-- Optional: show the raw stored name if it doesn't exist in the list (not selected) --}}
                                    @if (!empty($itemNF) && !empty($itemLabel))
                                        <option value="" disabled>{{ $itemLabel }} (not in list)</option>
                                    @endif
                                    <option value="">— Select Item —</option>

                                    @foreach ($Poojaitemlist as $pujalist)
                                        @php
                                            // Prefer id match (old input / mapped id), else fallback to name match
                                            $matchById = isset($itemId) && (int) $pujalist->id === (int) $itemId;
                                            $matchByName =
                                                !empty($itemLabel) &&
                                                mb_strtolower(trim($pujalist->name)) ===
                                                    mb_strtolower(trim($itemLabel));
                                        @endphp
                                        <option value="{{ $pujalist->id }}"
                                            {{ $matchById || $matchByName ? 'selected' : '' }}>
                                            {{ $pujalist->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="quantity[]" min="0"
                                    step="any" value="{{ $qty }}" placeholder="0" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-control select2 unit-select" name="unit_id[]" required>
                                    {{-- Optional: show the raw stored name if it doesn't exist in the list (not selected) --}}
                                    @if (!empty($unitNF) && !empty($unitLabel))
                                        <option value="" disabled>{{ $unitLabel }} (not in list)</option>
                                    @endif
                                    <option value="">— Select Unit —</option>

                                    @foreach ($pooja_units as $u)
                                        @php
                                            // Prefer id match, else fallback to name match
                                            $uMatchById = isset($unitId) && (int) $u->id === (int) $unitId;
                                            $uMatchByName =
                                                !empty($unitLabel) &&
                                                mb_strtolower(trim($u->unit_name)) === mb_strtolower(trim($unitLabel));
                                        @endphp
                                        <option value="{{ $u->id }}"
                                            {{ $uMatchById || $uMatchByName ? 'selected' : '' }}>
                                            {{ $u->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Item Price (Rs.)</label>
                                <input type="number" class="form-control" name="item_price[]" min="0"
                                    step="0.01" value="{{ $price }}" placeholder="0.00" required>
                            </div>
                        </div>
                    @endfor
                </div>

                <button type="button" id="addMore" class="btn btn-secondary">Add More</button>
                <button type="button" id="removeLast" class="btn btn-danger">Remove Last</button>
            </div>

            <div class="col-md-6 mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" name="product_image" class="form-control" id="product_image">
                @if ($product->product_image)
                    <img src="{{ $product->product_image }}" alt="Product Image" width="100" class="mt-2">
                @endif
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Benefits</label>
                <div id="benefitsWrapper">
                    @php
                        $benefits = old(
                            'benefits',
                            !empty($product->benefits) ? explode('#', $product->benefits) : [''],
                        );
                    @endphp
                    @foreach ($benefits as $b)
                        <div class="input-group mb-2 benefit-row">
                            <input type="text" name="benefits[]" class="form-control" value="{{ trim($b) }}"
                                placeholder="Enter benefit">
                            <button type="button" class="btn btn-danger removeBenefit">Remove</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary" id="addBenefit">Add Benefit</button>
            </div>

            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="col-md-12 mt-4">
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        (function() {
            function initSelect2(scope) {
                $(scope || document).find('.select2').each(function() {
                    if (!$(this).data('select2')) {
                        $(this).select2({
                            width: '100%'
                        });
                    }
                });
            }
            initSelect2();

            const $category = $('#category');
            const $lineFields = $('#packageFields'); // both Package & Subscription
            const $packageItems = $('#packageItems');
            const $fromInput = $('#available_from');
            const $toInput = $('#available_to');
            const $perDayField = $('#perDayPriceField');
            const $perDayInput = $('#per_day_price');
            const $durationField = $('#durationField');
            const $durationSelect = $('#duration');

            function buildRowHtml() {
                return `
                <div class="row mb-3 package-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Item</label>
                        <select class="form-control select2 item-select" name="item_id[]" required>
                            <option value="">— Select Item —</option>
                            @foreach ($Poojaitemlist as $pujalist)
                                <option value="{{ $pujalist->id }}">{{ $pujalist->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" name="quantity[]" min="0" step="any" placeholder="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <select class="form-control select2 unit-select" name="unit_id[]" required>
                            <option value="">— Select Unit —</option>
                            @foreach ($pooja_units as $u)
                                <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item Price (Rs.)</label>
                        <input type="number" class="form-control" name="item_price[]" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                </div>`;
            }

            function ensureAtLeastOneRow() {
                if ($packageItems.find('.package-row').length === 0) {
                    $packageItems.append(buildRowHtml());
                    initSelect2($packageItems.children().last());
                }
            }

            function applyCategoryUI() {
                const cat = $category.val();
                const isFlower = (cat === 'Flower');
                const isPackage = (cat === 'Package');
                const isSub = (cat === 'Subscription');
                const showLines = isPackage || isSub;

                $('#malaProvidedField, #flowerAvailabilityField, #flowerFromField, #flowerToField').toggle(isFlower);

                $lineFields.toggle(showLines);
                if (showLines) ensureAtLeastOneRow();

                // Subscription-only fields
                $perDayField.toggle(isSub);
                $perDayInput.prop('required', isSub);
                if (!isSub) $perDayInput.val('');

                $durationField.toggle(isSub);
                $durationSelect.prop('required', isSub);
                if (!isSub) {
                    $durationSelect.val('').trigger('change');
                }

                updateFlowerDateRequirements();
                initSelect2();
            }

            function updateFlowerDateRequirements() {
                const isFlower = ($category.val() === 'Flower');
                const active = $('#flowerActive').is(':checked');

                $fromInput.prop('required', isFlower && active).prop('disabled', isFlower && !active);
                $toInput.prop('required', isFlower && active).prop('disabled', isFlower && !active);

                if (!isFlower || !active) {
                    $fromInput.val('');
                    $toInput.val('');
                    $fromInput.removeAttr('max');
                    $toInput.removeAttr('min');
                }
            }

            // Date constraints
            $fromInput.on('change', function() {
                $toInput.attr('min', this.value || '');
                if ($toInput.val() && this.value && $toInput.val() < this.value) $toInput.val('');
            });
            $toInput.on('change', function() {
                $fromInput.attr('max', this.value || '');
            });

            // Benefits add/remove
            $('#addBenefit').on('click', function() {
                $('#benefitsWrapper').append(`
                <div class="input-group mb-2 benefit-row">
                    <input type="text" name="benefits[]" class="form-control" placeholder="Enter benefit">
                    <button type="button" class="btn btn-danger removeBenefit">Remove</button>
                </div>`);
            });
            $(document).on('click', '.removeBenefit', function() {
                const rows = $('#benefitsWrapper .benefit-row');
                if (rows.length > 1) $(this).closest('.benefit-row').remove();
            });

            // Add/Remove line rows
            $('#addMore').on('click', function() {
                $packageItems.append(buildRowHtml());
                initSelect2($packageItems.children().last());
            });
            $('#removeLast').on('click', function() {
                const rows = $packageItems.find('.package-row');
                if (rows.length > 1) rows.last().remove();
            });

            // Category change
            $(document).on('change', '#category', applyCategoryUI);
            $('#category').on('select2:select', applyCategoryUI);

            // Initial UI
            applyCategoryUI();
        })();
    </script>
@endsection
