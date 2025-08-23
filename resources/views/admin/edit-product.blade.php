@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Edit Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-product') }}"
                        class="btn btn-warning text-dark">Manage Product</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Edit Product</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">
            {{ $errors->first('danger') }}
        </div>
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

            // Prepare package rows from old input OR from controller-provided $packageItems
            $oldItemIds = old('item_id', []);
            $oldQtys = old('quantity', []);
            $oldUnitIds = old('unit_id', []);
            $oldPrices = old('item_price', []);

            $hasOldRows = is_array($oldItemIds) && count($oldItemIds) > 0;

            // Normalize $packageItems to array of rows with item_id, quantity, unit_id, price
            $preparedPackage = [];
            if (!$hasOldRows && isset($packageItems) && count($packageItems)) {
                foreach ($packageItems as $row) {
                    $preparedPackage[] = [
                        'item_id' => $row['item_id'] ?? null,
                        'quantity' => $row['quantity'] ?? null,
                        'unit_id' => $row['unit_id'] ?? null,
                        'price' => $row['price'] ?? null,
                    ];
                }
            }
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

            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control select2" required>
                    <option value="Puja Item" {{ old('category', $product->category) == 'Puja Item' ? 'selected' : '' }}>
                        Puja Item</option>
                    <option value="Subscription"
                        {{ old('category', $product->category) == 'Subscription' ? 'selected' : '' }}>Subscription</option>
                    <option value="Flower" {{ old('category', $product->category) == 'Flower' ? 'selected' : '' }}>Flower
                    </option>
                    <option value="Immediateproduct"
                        {{ old('category', $product->category) == 'Immediateproduct' ? 'selected' : '' }}>Customize Flower
                    </option>
                    <option value="Customizeproduct"
                        {{ old('category', $product->category) == 'Customizeproduct' ? 'selected' : '' }}>Customize Product
                    </option>
                    <option value="Package" {{ old('category', $product->category) == 'Package' ? 'selected' : '' }}>
                        Package</option>
                    <option value="Books" {{ old('category', $product->category) == 'Books' ? 'selected' : '' }}>Books
                    </option>
                </select>
            </div>

            <!-- Flower-only: Mala Provided -->
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

            <!-- Flower-only: Availability (Active/Inactive) -->
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

            <!-- Flower-only: Available From / To -->
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

            <!-- Package fields (Item + Qty + Unit + Item Price) -->
            <div id="packageFields" class="col-md-12 mb-3" style="display:none;">
                <div id="packageItems">
                    @if ($hasOldRows)
                        @foreach ($oldItemIds as $i => $oldItemId)
                            <div class="row mb-3 package-row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Item</label>
                                    <select class="form-control select2 item-select" name="item_id[]" required>
                                        <option value="">Select Puja List</option>
                                        @foreach ($Poojaitemlist as $pujalist)
                                            <option value="{{ $pujalist->id }}"
                                                {{ (int) $oldItemId === (int) $pujalist->id ? 'selected' : '' }}>
                                                {{ $pujalist->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control" name="quantity[]" min="0"
                                        step="any" value="{{ $oldQtys[$i] ?? '' }}" placeholder="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-control select2 unit-select" name="unit_id[]" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($pooja_units as $u)
                                            <option value="{{ $u->id }}"
                                                {{ (int) ($oldUnitIds[$i] ?? 0) === (int) $u->id ? 'selected' : '' }}>
                                                {{ $u->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Item Price (Rs.)</label>
                                    <input type="number" class="form-control" name="item_price[]" min="0"
                                        step="0.01" value="{{ $oldPrices[$i] ?? '' }}" placeholder="0.00" required>
                                </div>
                            </div>
                        @endforeach
                    @elseif (!empty($preparedPackage))
                        @foreach ($preparedPackage as $row)
                            <div class="row mb-3 package-row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Item</label>
                                    <select class="form-control select2 item-select" name="item_id[]" required>
                                        <option value="">Select Puja List</option>
                                        @foreach ($Poojaitemlist as $pujalist)
                                            <option value="{{ $pujalist->id }}"
                                                {{ (int) ($row['item_id'] ?? 0) === (int) $pujalist->id ? 'selected' : '' }}>
                                                {{ $pujalist->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control" name="quantity[]" min="0"
                                        step="any" value="{{ $row['quantity'] ?? '' }}" placeholder="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-control select2 unit-select" name="unit_id[]" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($pooja_units as $u)
                                            <option value="{{ $u->id }}"
                                                {{ (int) ($row['unit_id'] ?? 0) === (int) $u->id ? 'selected' : '' }}>
                                                {{ $u->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Item Price (Rs.)</label>
                                    <input type="number" class="form-control" name="item_price[]" min="0"
                                        step="0.01" value="{{ $row['price'] ?? '' }}" placeholder="0.00" required>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- default one empty row --}}
                        <div class="row mb-3 package-row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Item</label>
                                <select class="form-control select2 item-select" name="item_id[]" required>
                                    <option value="">Select Puja List</option>
                                    @foreach ($Poojaitemlist as $pujalist)
                                        <option value="{{ $pujalist->id }}">{{ $pujalist->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="quantity[]" min="0"
                                    step="any" placeholder="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-control select2 unit-select" name="unit_id[]" required>
                                    <option value="">Select Unit</option>
                                    @foreach ($pooja_units as $u)
                                        <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Item Price (Rs.)</label>
                                <input type="number" class="form-control" name="item_price[]" min="0"
                                    step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                    @endif
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

{{-- Scripts --}}
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        (function() {
            // Ensure Select2 is initialized after the page’s own Select2 bundle loads.
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
            const $pkgFields = $('#packageFields');
            const $malaField = $('#malaProvidedField');
            const $availField = $('#flowerAvailabilityField');
            const $fromField = $('#flowerFromField');
            const $toField = $('#flowerToField');
            const $fromInput = $('#available_from');
            const $toInput = $('#available_to');
            const $flowerActive = $('#flowerActive');
            const $flowerInactive = $('#flowerInactive');

            function applyCategoryUI() {
                const cat = $category.val();

                const isFlower = (cat === 'Flower');
                const isPackage = (cat === 'Package');

                $malaField.toggle(isFlower);
                $availField.toggle(isFlower);
                $fromField.toggle(isFlower);
                $toField.toggle(isFlower);

                $pkgFields.toggle(isPackage);

                updateFlowerDateRequirements();
                initSelect2();
            }

            function updateFlowerDateRequirements() {
                const cat = $category.val();
                if (cat !== 'Flower') {
                    $fromInput.prop({
                        required: false,
                        disabled: false
                    });
                    $toInput.prop({
                        required: false,
                        disabled: false
                    });
                    return;
                }
                const active = $('#flowerActive').is(':checked');
                $fromInput.prop('required', active).prop('disabled', !active);
                $toInput.prop('required', active).prop('disabled', !active);
                if (!active) {
                    $fromInput.val('');
                    $toInput.val('');
                    $fromInput.removeAttr('max');
                    $toInput.removeAttr('min');
                }
            }

            // date bounds
            $fromInput.on('change', function() {
                $toInput.attr('min', this.value || '');
                if ($toInput.val() && this.value && $toInput.val() < this.value) {
                    $toInput.val('');
                }
            });
            $toInput.on('change', function() {
                $fromInput.attr('max', this.value || '');
            });

            // Category change (native + select2)
            $(document).on('change', '#category', applyCategoryUI);
            $('#category').on('select2:select', applyCategoryUI);

            // Availability change
            $('#flowerActive, #flowerInactive').on('change', updateFlowerDateRequirements);

            // Benefits add/remove
            $('#addBenefit').on('click', function() {
                $('#benefitsWrapper').append(`
            <div class="input-group mb-2 benefit-row">
                <input type="text" name="benefits[]" class="form-control" placeholder="Enter benefit">
                <button type="button" class="btn btn-danger removeBenefit">Remove</button>
            </div>
        `);
            });
            $(document).on('click', '.removeBenefit', function() {
                const rows = $('#benefitsWrapper .benefit-row');
                if (rows.length > 1) $(this).closest('.benefit-row').remove();
            });

            // Package rows add/remove
            const $packageItems = $('#packageItems');

            $('#addMore').on('click', function() {
                const rowHtml = `
            <div class="row mb-3 package-row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Item</label>
                    <select class="form-control select2 item-select" name="item_id[]" required>
                        <option value="">Select Puja List</option>
                        @foreach ($Poojaitemlist as $pujalist)
                            <option value="{{ $pujalist->id }}">{{ $pujalist->item_name }}</option>
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
                        <option value="">Select Unit</option>
                        @foreach ($pooja_units as $u)
                            <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Item Price (Rs.)</label>
                    <input type="number" class="form-control" name="item_price[]" min="0" step="0.01" placeholder="0.00" required>
                </div>
            </div>
        `;
                $packageItems.append(rowHtml);
                initSelect2($packageItems.children().last());
            });

            $('#removeLast').on('click', function() {
                const rows = $packageItems.find('.package-row');
                if (rows.length > 1) rows.last().remove();
            });

            // Initial UI state on load
            applyCategoryUI();
        })();
    </script>
@endsection
