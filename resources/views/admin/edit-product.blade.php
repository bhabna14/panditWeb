@extends('admin.layouts.app')

@section('styles')
    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <style>
        .hidden {
            display: none !important;
        }

        .label-note {
            font-weight: 500;
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
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
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

    <!--
            Expected variables:
            - $product: FlowerProduct with relations ->pooja, ->packageItems
            - $pooja_list: list of Poojalist (id, pooja_name)
            - $flowerlist: FlowerProduct where category='Flower' (id, name)
            - $pooja_units: list of PoojaUnit (id, unit_name)
        -->

    <form action="{{ route('admin.update-product', $product->id) }}" method="post" enctype="multipart/form-data"
        id="categorySmartForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- CATEGORY (always visible) -->
            <div class="col-md-4 mb-3">
                <label for="category" class="form-label"><span id="label-category">Category</span></label>
                <select name="category" id="category" class="form-control select2" required>
                    <option value="" disabled>Select Category</option>
                    @php $cats = ['Puja Item','Subscription','Flower','Immediateproduct','Customizeproduct','Package','Books']; @endphp
                    @foreach ($cats as $cat)
                        <option value="{{ $cat }}" {{ $product->category === $cat ? 'selected' : '' }}>
                            {{ $cat }}</option>
                    @endforeach
                </select>
                <div class="form-text-muted mt-1">Switching category will show/hide relevant fields; ensure you review
                    before saving.</div>
            </div>

            <!-- CORE -->
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="name" class="form-label"><span id="label-name">Product Name</span></label>
                <input type="text" name="name" class="form-control" id="name"
                    value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
            </div>

            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="odia_name" class="form-label"><span id="label-odia-name">Product Name (Odia)</span></label>
                <input type="text" name="odia_name" class="form-control" id="odia_name"
                    value="{{ old('odia_name', $product->odia_name) }}" placeholder="Enter product name in Odia">
            </div>

            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="mrp" class="form-label"><span id="label-mrp">MRP (Rs.)</span></label>
                <input type="number" name="mrp" class="form-control" id="mrp" min="0" step="0.01"
                    value="{{ old('mrp', $product->mrp) }}" placeholder="Enter MRP" required>
            </div>

            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="price" class="form-label"><span id="label-price">Sale Price (Rs.)</span></label>
                <input type="number" name="price" class="form-control" id="price" min="0" step="0.01"
                    value="{{ old('price', $product->price) }}" placeholder="Enter sale price" required>
            </div>

            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="discount" class="form-label"><span id="label-discount">Discount (%)</span></label>
                <input type="number" name="discount" class="form-control" id="discount" min="0" max="100"
                    step="0.01" value="{{ old('discount', $product->discount) }}"
                    placeholder="Enter discount percentage">
            </div>

            <!-- STOCK (not for Flower) -->
            <div class="col-md-4 mb-3 controlled" id="stockGroup" data-block="stock">
                <label for="stock" class="form-label"><span id="label-stock">Stock</span></label>
                <input type="number" name="stock" class="form-control" id="stock" min="0"
                    value="{{ old('stock', $product->stock) }}" placeholder="Enter stock quantity">
            </div>

            <!-- IMAGE -->
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="product_image" class="form-label"><span id="label-image">Product Image</span></label>
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

            <!-- FLOWER -->
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
                <label for="available_from" class="form-label"><span id="label-available-from">Available
                        From</span></label>
                <input type="date" name="available_from" id="available_from" class="form-control"
                    value="{{ old('available_from', $product->available_from ? \Carbon\Carbon::parse($product->available_from)->format('Y-m-d') : null) }}">
            </div>

            <div class="col-md-4 mb-3 controlled" id="flowerToField" data-block="flowerDates">
                <label for="available_to" class="form-label"><span id="label-available-to">Available To</span></label>
                <input type="date" name="available_to" id="available_to" class="form-control"
                    value="{{ old('available_to', $product->available_to ? \Carbon\Carbon::parse($product->available_to)->format('Y-m-d') : null) }}">
            </div>

            <!-- PACKAGE -->
            <div class="col-md-4 mb-3 controlled" id="poojafields" data-block="package">
                <label for="pooja_id" class="form-label"><span id="label-pooja">Pooja (Festival)</span></label>
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
                                $selectedItem = optional($flowerlist->firstWhere('name', $packageItem->item_name))->id;
                                $selectedUnit = optional($pooja_units->firstWhere('unit_name', $packageItem->unit))->id;
                            @endphp
                            <div class="row mb-3 package-row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Item</label>
                                    <select class="form-control select2 item-select" name="item_id[]" required>
                                        <option value="">Select Puja List</option>
                                        @foreach ($flowerlist as $flower)
                                            <option value="{{ $flower->id }}"
                                                {{ (string) old('item_id.' . $idx, $selectedItem) === (string) $flower->id ? 'selected' : '' }}>
                                                {{ $flower->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control" name="quantity[]" min="0"
                                        step="any" value="{{ old('quantity.' . $idx, $packageItem->quantity) }}"
                                        placeholder="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-control select2 unit-select" name="unit_id[]" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($pooja_units as $u)
                                            <option value="{{ $u->id }}"
                                                {{ (string) old('unit_id.' . $idx, $selectedUnit) === (string) $u->id ? 'selected' : '' }}>
                                                {{ $u->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Item Price (Rs.)</label>
                                    <input type="number" class="form-control" name="item_price[]" min="0"
                                        step="0.01" value="{{ old('item_price.' . $idx, $packageItem->price) }}"
                                        placeholder="0.00" required>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback empty row if no items (keeps UI consistent) -->
                        <div class="row mb-3 package-row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Item</label>
                                <select class="form-control select2 item-select" name="item_id[]">
                                    <option value="">Select Puja List</option>
                                    @foreach ($flowerlist as $flower)
                                        <option value="{{ $flower->id }}">{{ $flower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="quantity[]" min="0"
                                    step="any" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-control select2 unit-select" name="unit_id[]">
                                    <option value="">Select Unit</option>
                                    @foreach ($pooja_units as $u)
                                        <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Item Price (Rs.)</label>
                                <input type="number" class="form-control" name="item_price[]" min="0"
                                    step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="addMore" class="btn btn-secondary">Add More</button>
                <button type="button" id="removeLast" class="btn btn-danger">Remove Last</button>
            </div>

            <!-- SUBSCRIPTION -->
            <div class="col-md-4 mb-3 controlled" id="durationGroup" data-block="subscription">
                <label for="duration" class="form-label"><span id="label-duration">Subscription Duration
                        (Months)</span></label>
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
                                $selectedItem = optional($flowerlist->firstWhere('name', $sItem->item_name))->id;
                                $selectedUnit = optional($pooja_units->firstWhere('unit_name', $sItem->unit))->id;
                            @endphp
                            <div class="row mb-3 subscription-item-row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Item</label>
                                    <select class="form-control select2 item-select" name="sub_item_id[]" required>
                                        <option value="">Select Puja List</option>
                                        @foreach ($flowerlist as $flower)
                                            <option value="{{ $flower->id }}"
                                                {{ (string) old('sub_item_id.' . $idx, $selectedItem) === (string) $flower->id ? 'selected' : '' }}>
                                                {{ $flower->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control" name="sub_quantity[]" min="0"
                                        step="any" value="{{ old('sub_quantity.' . $idx, $sItem->quantity) }}"
                                        placeholder="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-control select2 unit-select" name="sub_unit_id[]" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($pooja_units as $u)
                                            <option value="{{ $u->id }}"
                                                {{ (string) old('sub_unit_id.' . $idx, $selectedUnit) === (string) $u->id ? 'selected' : '' }}>
                                                {{ $u->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Item Price (Rs.)</label>
                                    <input type="number" class="form-control" name="sub_item_price[]" min="0"
                                        step="0.01" value="{{ old('sub_item_price.' . $idx, $sItem->price) }}"
                                        placeholder="0.00" required>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback empty row -->
                        <div class="row mb-3 subscription-item-row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Item</label>
                                <select class="form-control select2 item-select" name="sub_item_id[]">
                                    <option value="">Select Puja List</option>
                                    @foreach ($flowerlist as $flower)
                                        <option value="{{ $flower->id }}">{{ $flower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="sub_quantity[]" min="0"
                                    step="any" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-control select2 unit-select" name="sub_unit_id[]">
                                    <option value="">Select Unit</option>
                                    @foreach ($pooja_units as $u)
                                        <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Item Price (Rs.)</label>
                                <input type="number" class="form-control" name="sub_item_price[]" min="0"
                                    step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="addSubscriptionItemRow" class="btn btn-secondary">Add Item</button>
                <button type="button" id="removeSubscriptionItemRow" class="btn btn-danger">Remove Last Item</button>
            </div>

            <!-- BENEFITS -->
            <div class="col-md-4 mb-3 controlled" data-block="core">
                <label for="benefit" class="form-label"><span id="label-benefit">Benefits</span></label>
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
                            <button type="button" class="btn btn-success add-benefit" title="Add Benefit">
                                <i class="fa fa-plus"></i> Add
                            </button>
                            <button type="button" class="btn btn-danger remove-benefit" title="Remove Benefit"
                                style="display: {{ $benefits->count() > 1 ? 'inline-block' : 'none' }};">
                                <i class="fa fa-minus"></i> Remove
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-md-12 mb-3 controlled" data-block="core">
                <label for="description" class="form-label"><span id="label-description">Description</span></label>
                <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter description"
                    required>{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Submit -->
            <div class="col-md-12 mt-4 controlled" data-block="core">
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </div>
    </form>
@endsection

@section('modal')
@endsection

@section('scripts')
    <!-- Load jQuery first, then Select2, then your scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        // Toast fades
        setTimeout(() => $('#Message').fadeOut('fast'), 2500);

        (function() {
            // ---------- Helpers ----------
            function initSelect2(scope) {
                const $scope = scope ? $(scope) : $(document);
                $scope.find('.select2').each(function() {
                    if (!$(this).data('select2')) {
                        $(this).select2({
                            width: '100%'
                        });
                    }
                });
            }

            function setRequired(el, required) {
                if (!el) return;
                el.required = !!required;
                if (!required) el.removeAttribute('required');
            }

            function enableGroup(nodeList, enable) {
                nodeList.forEach(el => {
                    el.classList.toggle('hidden', !enable);
                    el.querySelectorAll('input, select, textarea, button').forEach(i => i.disabled = !enable);

                    if (!enable) {
                        // NOTE: we DO NOT wipe values here on edit; users may switch back
                    }
                });
            }

            // ---------- Cache ----------
            const categorySelect = document.getElementById('category');
            const groups = {
                core: document.querySelectorAll('[data-block="core"]'),
                stock: document.querySelectorAll('[data-block="stock"]'),
                subscription: document.querySelectorAll('[data-block="subscription"]'),
                flower: document.querySelectorAll('[data-block="flower"]'),
                flowerDates: document.querySelectorAll('[data-block="flowerDates"]'),
                package: document.querySelectorAll('[data-block="package"]'),
            };

            const availableFrom = document.getElementById('available_from');
            const availableTo = document.getElementById('available_to');
            const flowerActive = document.getElementById('flowerActive');
            const flowerInactive = document.getElementById('flowerInactive');

            // Labels & inputs we rewrite
            const $labels = {
                name: $('#label-name'),
                odiaName: $('#label-odia-name'),
                mrp: $('#label-mrp'),
                price: $('#label-price'),
                image: $('#label-image'),
                description: $('#label-description'),
                benefit: $('#label-benefit'),
                stock: $('#label-stock'),
                duration: $('#label-duration'),
                mala: $('#label-mala'),
                availability: $('#label-availability'),
                availableFrom: $('#label-available-from'),
                availableTo: $('#label-available-to'),
                pooja: $('#label-pooja'),
                packageItems: $('#label-package-items')
            };
            const $inputs = {
                name: $('#name'),
                odiaName: $('#odia_name'),
                mrp: $('#mrp'),
                price: $('#price'),
                description: $('#description')
            };

            const LABELS = {
                default: {
                    name: 'Product Name',
                    odiaName: 'Product Name (Odia)',
                    mrp: 'MRP (Rs.)',
                    price: 'Sale Price (Rs.)',
                    image: 'Product Image',
                    description: 'Description',
                    benefit: 'Benefits',
                    stock: 'Stock'
                },
                'Flower': {
                    name: 'Flower Name',
                    odiaName: 'Flower Name (Odia)',
                    mrp: 'Flower MRP (Rs.)',
                    price: 'Flower Price (Rs.)',
                    image: 'Flower Image',
                    description: 'Flower Description',
                    benefit: 'Flower Benefits',
                    stock: 'Stock'
                },
                'Package': {
                    name: 'Package Name',
                    odiaName: 'Package Name (Odia)',
                    mrp: 'Package MRP (Rs.)',
                    price: 'Package Price (Rs.)',
                    image: 'Package Image',
                    description: 'Package Description',
                    benefit: 'Package Benefits',
                    stock: 'Stock'
                },
                'Subscription': {
                    name: 'Subscription Name',
                    odiaName: 'Subscription Name (Odia)',
                    mrp: 'Subscription MRP (Rs.)',
                    price: 'Subscription Price (Rs.)',
                    image: 'Subscription Image',
                    description: 'Subscription Description',
                    benefit: 'Subscription Benefits',
                    stock: 'Stock'
                },
                'Puja Item': {
                    name: 'Item Name',
                    odiaName: 'Item Name (Odia)',
                    mrp: 'Item MRP (Rs.)',
                    price: 'Item Price (Rs.)',
                    image: 'Item Image',
                    description: 'Item Description',
                    benefit: 'Item Benefits',
                    stock: 'Stock'
                },
                'Immediateproduct': {
                    name: 'Customized Flower Name',
                    odiaName: 'Customized Flower Name (Odia)',
                    mrp: 'Customized Flower MRP (Rs.)',
                    price: 'Customized Flower Price (Rs.)',
                    image: 'Customized Flower Image',
                    description: 'Customized Flower Description',
                    benefit: 'Benefits',
                    stock: 'Stock'
                },
                'Customizeproduct': {
                    name: 'Customized Product Name',
                    odiaName: 'Customized Product Name (Odia)',
                    mrp: 'Customized Product MRP (Rs.)',
                    price: 'Customized Product Price (Rs.)',
                    image: 'Customized Product Image',
                    description: 'Customized Product Description',
                    benefit: 'Benefits',
                    stock: 'Stock'
                },
                'Books': {
                    name: 'Book Title',
                    odiaName: 'Book Title (Odia)',
                    mrp: 'Book MRP (Rs.)',
                    price: 'Book Price (Rs.)',
                    image: 'Book Cover Image',
                    description: 'Book Description',
                    benefit: 'Key Benefits',
                    stock: 'Stock'
                }
            };

            const PLACEHOLDERS = {
                default: {
                    name: 'Enter product name',
                    odiaName: 'Enter product name in Odia',
                    mrp: 'Enter MRP',
                    price: 'Enter sale price',
                    description: 'Enter description'
                },
                'Flower': {
                    name: 'Enter flower name',
                    odiaName: 'Enter flower name in Odia',
                    mrp: 'Enter flower MRP',
                    price: 'Enter flower price',
                    description: 'Enter flower description'
                },
                'Package': {
                    name: 'Enter package name',
                    odiaName: 'Enter package name in Odia',
                    mrp: 'Enter package MRP',
                    price: 'Enter package price',
                    description: 'Enter package description'
                },
                'Subscription': {
                    name: 'Enter subscription name',
                    odiaName: 'Enter subscription name in Odia',
                    mrp: 'Enter subscription MRP',
                    price: 'Enter subscription price',
                    description: 'Enter subscription description'
                },
                'Puja Item': {
                    name: 'Enter item name',
                    odiaName: 'Enter item name in Odia',
                    mrp: 'Enter item MRP',
                    price: 'Enter item price',
                    description: 'Enter item description'
                },
                'Immediateproduct': {
                    name: 'Enter customized flower name',
                    odiaName: 'Enter customized flower name in Odia',
                    mrp: 'Enter customized flower MRP',
                    price: 'Enter customized flower price',
                    description: 'Enter customized flower description'
                },
                'Customizeproduct': {
                    name: 'Enter customized product name',
                    odiaName: 'Enter customized product name in Odia',
                    mrp: 'Enter customized product MRP',
                    price: 'Enter customized product price',
                    description: 'Enter customized product description'
                },
                'Books': {
                    name: 'Enter book title',
                    odiaName: 'Enter book title in Odia',
                    mrp: 'Enter book MRP',
                    price: 'Enter book price',
                    description: 'Enter book description'
                }
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

            function setLabelsByCategory(cat) {
                const map = LABELS[cat] || LABELS.default;
                $labels.name.text(map.name);
                $labels.odiaName.text(map.odiaName);
                $labels.mrp.text(map.mrp);
                $labels.price.text(map.price);
                $labels.image.text(map.image);
                $labels.description.text(map.description);
                $labels.benefit.text(map.benefit);
                $labels.stock.text(map.stock);

                const ph = PLACEHOLDERS[cat] || PLACEHOLDERS.default;
                $inputs.name.attr('placeholder', ph.name);
                $inputs.odiaName.attr('placeholder', ph.odiaName);
                $inputs.mrp.attr('placeholder', ph.mrp);
                $inputs.price.attr('placeholder', ph.price);
                $inputs.description.attr('placeholder', ph.description);

                if (cat === 'Flower') {
                    $('#label-mala').text('Is mala provided with this flower?');
                    $('#label-availability').text('Is this flower available?');
                    $('#label-available-from').text('Available From');
                    $('#label-available-to').text('Available To');
                } else {
                    $('#label-mala').text('Is mala provided?');
                    $('#label-availability').text('Is this available?');
                    $('#label-available-from').text('Available From');
                    $('#label-available-to').text('Available To');
                }
                if (cat === 'Package') {
                    $('#label-pooja').text('Pooja (Festival)');
                    $('#label-package-items').text('Package Items');
                } else {
                    $('#label-pooja').text('Pooja');
                    $('#label-package-items').text('Items');
                }
            }

            function applyCategoryRules() {
                const cat = categorySelect.value;
                const vis = VISIBILITY[cat] || VISIBILITY.default;

                enableGroup(groups.core, !!vis.core);
                enableGroup(groups.stock, !!vis.stock);
                enableGroup(groups.subscription, !!vis.subscription);
                enableGroup(groups.flower, !!vis.flower);
                enableGroup(groups.flowerDates, !!vis.flowerDates);
                enableGroup(groups.package, !!vis.package);

                setLabelsByCategory(cat);
                updateFlowerDatesRequired();
            }

            // Keep "To" >= "From"
            function wireDateBounds() {
                if (!availableFrom || !availableTo) return;
                availableFrom.addEventListener('change', function() {
                    availableTo.min = availableFrom.value || '';
                    if (availableTo.value && availableFrom.value && availableTo.value < availableFrom.value) {
                        availableTo.value = '';
                    }
                });
                availableTo.addEventListener('change', function() {
                    availableFrom.max = availableTo.value || '';
                });

                // Initialize min/max based on prefilled values
                if (availableFrom.value) availableTo.min = availableFrom.value;
                if (availableTo.value) availableFrom.max = availableTo.value;
            }

            function updateFlowerDatesRequired() {
                const isFlower = categorySelect.value === 'Flower';
                const active = isFlower && (flowerActive?.checked === true);
                [availableFrom, availableTo].forEach(el => {
                    if (!el) return;
                    setRequired(el, active);
                    el.disabled = !
                    isFlower; // only disable when not flower; when flower but not active, keep enabled but not required
                });
            }

            // ---------- Dynamic rows (Package + Subscription Items only) ----------
            const packageItems = document.getElementById('packageItems');
            const addMoreButton = document.getElementById('addMore');
            const removeLastButton = document.getElementById('removeLast');

            const subscriptionItems = document.getElementById('subscriptionItems');
            const addSubscriptionItemRow = document.getElementById('addSubscriptionItemRow');
            const removeSubscriptionItemRow = document.getElementById('removeSubscriptionItemRow');

            function addRow(container, html) {
                const temp = document.createElement('div');
                temp.innerHTML = html.trim();
                const node = temp.firstElementChild;
                container.appendChild(node);
                initSelect2(node);
            }

            // Package row template (inline for perf)
            const tplPackageRow = `
                <div class="row mb-3 package-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Item</label>
                        <select class="form-control select2 item-select" name="item_id[]">
                            <option value="">Select Puja List</option>
                            @foreach ($flowerlist as $flower)
                                <option value="{{ $flower->id }}">{{ $flower->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" name="quantity[]" min="0" step="any" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <select class="form-control select2 unit-select" name="unit_id[]">
                            <option value="">Select Unit</option>
                            @foreach ($pooja_units as $u)
                                <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item Price (Rs.)</label>
                        <input type="number" class="form-control" name="item_price[]" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>`;

            const tplSubscriptionItemRow = `
                <div class="row mb-3 subscription-item-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Item</label>
                        <select class="form-control select2 item-select" name="sub_item_id[]">
                            <option value="">Select Puja List</option>
                            @foreach ($flowerlist as $flower)
                                <option value="{{ $flower->id }}">{{ $flower->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" name="sub_quantity[]" min="0" step="any" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <select class="form-control select2 unit-select" name="sub_unit_id[]">
                            <option value="">Select Unit</option>
                            @foreach ($pooja_units as $u)
                                <option value="{{ $u->id }}">{{ $u->unit_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item Price (Rs.)</label>
                        <input type="number" class="form-control" name="sub_item_price[]" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>`;

            if (addMoreButton) {
                addMoreButton.addEventListener('click', () => addRow(packageItems, tplPackageRow));
            }
            if (removeLastButton) {
                removeLastButton.addEventListener('click', () => {
                    const rows = packageItems.querySelectorAll('.package-row');
                    if (rows.length > 1) rows[rows.length - 1].remove();
                });
            }

            if (addSubscriptionItemRow && removeSubscriptionItemRow) {
                addSubscriptionItemRow.addEventListener('click', () => addRow(subscriptionItems,
                    tplSubscriptionItemRow));
                removeSubscriptionItemRow.addEventListener('click', () => {
                    const rows = subscriptionItems.querySelectorAll('.subscription-item-row');
                    if (rows.length > 1) rows[rows.length - 1].remove();
                });
            }

            // Benefits add/remove via delegation
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

            // Category change (native + Select2)
            $(document).on('change', '#category', function() {
                applyCategoryRules();
            });
            $('#category').on('select2:select', function() {
                applyCategoryRules();
            });

            // Flower availability change
            [flowerActive, flowerInactive].forEach(r => r && r.addEventListener('change', updateFlowerDatesRequired));

            // Image live preview (for newly selected file)
            const imgInput = document.getElementById('product_image');
            const imgPreview = document.getElementById('imagePreview');
            if (imgInput && imgPreview) {
                imgInput.addEventListener('change', function() {
                    const file = this.files && this.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        imgPreview.src = e.target.result;
                        imgPreview.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Init
            initSelect2();
            wireDateBounds();
            applyCategoryRules();
        })();
    </script>
@endsection
