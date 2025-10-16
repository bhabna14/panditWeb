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
            max-height: 70px;
            display: none;
            margin-top: 6px;
            border-radius: 6px;
        }

        .readonly-input {
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">ADD Product</span>
        </div>

        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ url('admin/manage-product') }}" class="btn btn-warning text-dark">Manage Product</a>
                </li>
                <li class="breadcrumb-item tx-15">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        Add Item
                    </button>
                </li>
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

    <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data" id="categorySmartForm">
        @csrf

        <div class="row">
            <!-- CATEGORY (always visible) -->
            <div class="col-md-4 mb-3">
                <label for="category" class="form-label"><span id="label-category">Category</span></label>
                <select name="category" id="category" class="form-control select2" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Puja Item">Puja Item</option>
                    <option value="Subscription">Subscription</option>
                    <option value="Flower">Flower</option>
                    <option value="Immediateproduct">Customize Flower</option>
                    <option value="Customizeproduct">Customize Product</option>
                    <option value="Package">Package</option>
                    <option value="Books">Books</option>
                </select>
            </div>

            <!-- CORE -->
            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="name" class="form-label"><span id="label-name">Product Name</span></label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter product name"
                    required>
            </div>

            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="odia_name" class="form-label"><span id="label-odia-name">Product Name (Odia)</span></label>
                <input type="text" name="odia_name" class="form-control" id="odia_name"
                    placeholder="Enter product name in Odia">
            </div>

            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="mrp" class="form-label"><span id="label-mrp">MRP (Rs.)</span></label>
                <input type="number" name="mrp" class="form-control" id="mrp" min="0" step="0.01"
                    placeholder="Enter MRP" required>
            </div>

            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="price" class="form-label"><span id="label-price">Sale Price (Rs.)</span></label>
                <input type="number" name="price" class="form-control" id="price" min="0" step="0.01"
                    placeholder="Enter sale price" required>
            </div>

            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="discount" class="form-label"><span id="label-discount">Discount (%)</span></label>
                <input type="number" name="discount" class="form-control" id="discount" min="0" max="100"
                    step="0.01" placeholder="Enter discount percentage">
            </div>

            <!-- STOCK (not for Flower) -->
            <div class="col-md-4 mb-3 controlled hidden" id="stockGroup" data-block="stock">
                <label for="stock" class="form-label"><span id="label-stock">Stock</span></label>
                <input type="number" name="stock" class="form-control" id="stock" min="0"
                    placeholder="Enter stock quantity">
            </div>

            <!-- IMAGE -->
            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="product_image" class="form-label"><span id="label-image">Product Image</span></label>
                <input type="file" name="product_image" class="form-control" id="product_image" accept="image/*"
                    required>
                <img id="imagePreview" class="img-preview" alt="Preview">
            </div>

            <!-- FLOWER -->
            <div class="col-md-4 mb-3 controlled hidden" id="malaProvidedField" data-block="flower">
                <label class="form-label"><span id="label-mala">Is Mala provided with this flower?</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaYes" value="yes">
                    <label class="form-check-label" for="malaYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaNo" value="no">
                    <label class="form-check-label" for="malaNo">No</label>
                </div>
            </div>

            <div class="col-md-4 mb-3 controlled hidden" id="flowerAvailabilityField" data-block="flower">
                <label class="form-label"><span id="label-availability">Is this flower available?</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerActive"
                        value="yes">
                    <label class="form-check-label" for="flowerActive">Active</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerInactive"
                        value="no">
                    <label class="form-check-label" for="flowerInactive">Inactive</label>
                </div>
            </div>

            <div class="col-md-4 mb-3 controlled hidden" id="flowerFromField" data-block="flowerDates">
                <label for="available_from" class="form-label"><span id="label-available-from">Available
                        From</span></label>
                <input type="date" name="available_from" id="available_from" class="form-control">
            </div>

            <div class="col-md-4 mb-3 controlled hidden" id="flowerToField" data-block="flowerDates">
                <label for="available_to" class="form-label"><span id="label-available-to">Available To</span></label>
                <input type="date" name="available_to" id="available_to" class="form-control">
            </div>

            <!-- PACKAGE -->
            <div class="col-md-4 mb-3 controlled hidden" id="poojafields" data-block="package">
                <label for="pooja_id" class="form-label"><span id="label-pooja">Pooja (Festival)</span></label>
                <select class="form-control select2" id="pooja_id" name="pooja_id">
                    <option value="">Select Festival</option>
                    @foreach ($pooja_list as $pooja)
                        <option value="{{ $pooja->id }}">{{ $pooja->pooja_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="packageFields" class="col-md-12 mb-3 controlled hidden" data-block="package">
                <label class="form-label d-block mb-2"><span id="label-package-items">Package Items</span></label>

                <div id="packageItems">
                    <div class="row mb-3 package-row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Item</label>
                            <select class="form-control select2 item-select" name="item_id[]" required>
                                <option value="">Select Item</option>
                                @foreach ($flowerDetails as $it)
                                    <option value="{{ $it->id }}" data-unit="{{ $it->unit }}"
                                        data-price="{{ $it->price }}">{{ $it->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Qty</label>
                            <input type="number" class="form-control qty-input" name="quantity[]" min="0"
                                step="any" placeholder="0" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control unit-input readonly-input" name="unit_text[]"
                                placeholder="Auto" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Item Price (Rs.)</label>
                            <input type="number" class="form-control price-input" name="item_price[]" min="0"
                                step="0.01" placeholder="0.00" required readonly>
                        </div>
                    </div>
                </div>

                <button type="button" id="addMore" class="btn btn-secondary">Add More</button>
                <button type="button" id="removeLast" class="btn btn-danger">Remove Last</button>
            </div>

            <!-- SUBSCRIPTION -->
            <div class="col-md-4 mb-3 controlled hidden" id="durationGroup" data-block="subscription">
                <label for="duration" class="form-label"><span id="label-duration">Subscription Duration
                        (Months)</span></label>
                <select name="duration" id="duration" class="form-control select2">
                    <option value="" disabled selected>Select Package</option>
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                </select>
            </div>

            <!-- single per-day price remains -->
            <div id="subscriptionDayFields" class="col-md-12 mb-3 controlled hidden" data-block="subscription">
                <label class="form-label d-block mb-2"><span id="label-subscription-prices">Per-Day Price</span></label>

                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Per-Day Price (Rs.)</label>
                        <input type="number" class="form-control" name="per_day_price" min="0" step="0.01"
                            placeholder="0.00" required>
                    </div>
                </div>
            </div>

            <div id="subscriptionItemFields" class="col-md-12 mb-3 controlled hidden" data-block="subscription">
                <label class="form-label d-block mb-2"><span id="label-subscription-items">Subscription
                        Items</span></label>

                <div id="subscriptionItems">
                    <div class="row mb-3 subscription-item-row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Item</label>
                            <select class="form-control select2 item-select" name="sub_item_id[]" required>
                                <option value="">Select Item</option>
                                @foreach ($flowerDetails as $it)
                                    <option value="{{ $it->id }}" data-unit="{{ $it->unit }}"
                                        data-price="{{ $it->price }}">{{ $it->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Qty</label>
                            <input type="number" class="form-control qty-input" name="sub_quantity[]" min="0"
                                step="any" placeholder="0" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control unit-input readonly-input" name="sub_unit_text[]"
                                placeholder="Auto" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Item Price (Rs.)</label>
                            <input type="number" class="form-control price-input" name="sub_item_price[]"
                                min="0" step="0.01" placeholder="0.00" required readonly>
                        </div>
                    </div>
                </div>

                <button type="button" id="addSubscriptionItemRow" class="btn btn-secondary">Add Item</button>
                <button type="button" id="removeSubscriptionItemRow" class="btn btn-danger">Remove Last Item</button>
            </div>

            <!-- BENEFITS -->
            <div class="col-md-4 mb-3 controlled hidden" data-block="core">
                <label for="benefit" class="form-label"><span id="label-benefit">Benefits</span></label>
                <div id="benefitFields">
                    <div class="input-group mb-2 benefit-row">
                        <input type="text" name="benefit[]" class="form-control" placeholder="Enter benefit"
                            required>
                        <button type="button" class="btn btn-success add-benefit" title="Add Benefit">
                            <i class="fa fa-plus"></i> Add
                        </button>
                        <button type="button" class="btn btn-danger remove-benefit" title="Remove Benefit"
                            style="display:none;">
                            <i class="fa fa-minus"></i> Remove
                        </button>
                    </div>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-md-12 mb-3 controlled hidden" data-block="core">
                <label for="description" class="form-label"><span id="label-description">Description</span></label>
                <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter description"
                    required></textarea>
            </div>

            <!-- Submit -->
            <div class="col-md-12 mt-4 controlled hidden" data-block="core">
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </div>
    </form>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('items.store') }}" method="POST" id="addItemForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Add New Item Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-6">
                            <label for="item_name" class="form-label">Item Name</label>
                            <input type="text" name="item_name" id="item_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Item</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('modal')
@endsection

@section('scripts')
    <!-- Load jQuery first, then Select2, then your scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>

    <script>
        // Toast fades
        setTimeout(() => $('#Message').fadeOut('fast'), 2500);

        (function() {
            // ---------- FlowerDetails dataset (id -> {unit, price}) ----------
            // We'll read from the <option data-unit data-price> attributes on the fly,
            // so no need to inline a big JSON blob.

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
                        el.querySelectorAll(
                                'input[type="text"], input[type="number"], input[type="date"], textarea')
                            .forEach(i => i.value = '');
                        el.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(i => i
                            .checked = false);
                        el.querySelectorAll('select').forEach(s => {
                            $(s).val(null).trigger('change');
                        });
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
                    core: false,
                    stock: false,
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
                } else {
                    $('#label-mala').text('Is mala provided?');
                    $('#label-availability').text('Is this available?');
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

            // Flower date bounds
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
            }

            function updateFlowerDatesRequired() {
                const isFlower = categorySelect.value === 'Flower';
                const active = isFlower && (flowerActive?.checked === true);
                [availableFrom, availableTo].forEach(el => {
                    if (!el) return;
                    setRequired(el, active);
                    el.disabled = !active;
                    if (!active) {
                        el.value = '';
                        el.removeAttribute('min');
                        el.removeAttribute('max');
                    }
                });
            }

            // ---------- Price auto-calc (per row) ----------
            function recalcRow(row) {
                const select = row.querySelector('.item-select');
                const qtyEl = row.querySelector('.qty-input');
                const unitEl = row.querySelector('.unit-input');
                const priceEl = row.querySelector('.price-input');

                if (!select || !qtyEl || !unitEl || !priceEl) return;

                const opt = select.options[select.selectedIndex];
                const unit = opt ? (opt.getAttribute('data-unit') || '') : '';
                const per = opt ? parseFloat(opt.getAttribute('data-price') || '0') : 0;
                const qty = parseFloat(qtyEl.value || '0');

                unitEl.value = unit || '';
                const total = (per * (isNaN(qty) ? 0 : qty));
                priceEl.value = total ? total.toFixed(2) : '';
            }

            function wireRow(row) {
                const select = row.querySelector('.item-select');
                const qtyEl = row.querySelector('.qty-input');

                if (select) {
                    $(select).on('change', () => recalcRow(row));
                    // When Select2 triggers selection:
                    $(select).on('select2:select', () => recalcRow(row));
                }
                if (qtyEl) {
                    qtyEl.addEventListener('input', () => recalcRow(row));
                    qtyEl.addEventListener('change', () => recalcRow(row));
                }
            }

            function addRow(container, html) {
                const temp = document.createElement('div');
                temp.innerHTML = html.trim();
                const node = temp.firstElementChild;
                container.appendChild(node);
                initSelect2(node);
                wireRow(node);
            }

            // Templates (FlowerDetails-based)
            const tplPackageRow = `
                <div class="row mb-3 package-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Item</label>
                        <select class="form-control select2 item-select" name="item_id[]" required>
                            <option value="">Select Item</option>
                            @foreach ($flowerDetails as $it)
                                <option value="{{ $it->id }}" data-unit="{{ $it->unit }}" data-price="{{ $it->price }}">{{ $it->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control qty-input" name="quantity[]" min="0" step="any" placeholder="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control unit-input readonly-input" name="unit_text[]" placeholder="Auto" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item Price (Rs.)</label>
                        <input type="number" class="form-control price-input" name="item_price[]" min="0" step="0.01" placeholder="0.00" required readonly>
                    </div>
                </div>`;

            const tplSubscriptionItemRow = `
                <div class="row mb-3 subscription-item-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Item</label>
                        <select class="form-control select2 item-select" name="sub_item_id[]" required>
                            <option value="">Select Item</option>
                            @foreach ($flowerDetails as $it)
                                <option value="{{ $it->id }}" data-unit="{{ $it->unit }}" data-price="{{ $it->price }}">{{ $it->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control qty-input" name="sub_quantity[]" min="0" step="any" placeholder="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control unit-input readonly-input" name="sub_unit_text[]" placeholder="Auto" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item Price (Rs.)</label>
                        <input type="number" class="form-control price-input" name="sub_item_price[]" min="0" step="0.01" placeholder="0.00" required readonly>
                    </div>
                </div>`;

            // Dynamic row buttons
            const packageItems = document.getElementById('packageItems');
            const addMoreButton = document.getElementById('addMore');
            const removeLastButton = document.getElementById('removeLast');

            const subscriptionItems = document.getElementById('subscriptionItems');
            const addSubscriptionItemRow = document.getElementById('addSubscriptionItemRow');
            const removeSubscriptionItemRow = document.getElementById('removeSubscriptionItemRow');

            if (addMoreButton) addMoreButton.addEventListener('click', () => addRow(packageItems, tplPackageRow));
            if (removeLastButton) removeLastButton.addEventListener('click', () => {
                const rows = packageItems.querySelectorAll('.package-row');
                if (rows.length > 1) rows[rows.length - 1].remove();
            });

            if (addSubscriptionItemRow) addSubscriptionItemRow.addEventListener('click', () => addRow(subscriptionItems,
                tplSubscriptionItemRow));
            if (removeSubscriptionItemRow) removeSubscriptionItemRow.addEventListener('click', () => {
                const rows = subscriptionItems.querySelectorAll('.subscription-item-row');
                if (rows.length > 1) rows[rows.length - 1].remove();
            });

            // Benefits add/remove via delegation
            const benefitFields = document.getElementById('benefitFields');
            benefitFields.addEventListener('click', function(e) {
                const addBtn = e.target.closest('.add-benefit');
                const rmvBtn = e.target.closest('.remove-benefit');
                if (addBtn) {
                    const row = addBtn.closest('.benefit-row');
                    const clone = row.cloneNode(true);
                    clone.querySelector('input').value = '';
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

            // Image preview
            const imgInput = document.getElementById('product_image');
            const imgPreview = document.getElementById('imagePreview');
            if (imgInput && imgPreview) {
                imgInput.addEventListener('change', function() {
                    const file = this.files && this.files[0];
                    if (!file) {
                        imgPreview.style.display = 'none';
                        imgPreview.src = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = e => {
                        imgPreview.src = e.target.result;
                        imgPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Init Select2 on page load
            initSelect2();

            // Wire the very first rows for auto-calc
            document.querySelectorAll('.package-row, .subscription-item-row').forEach(wireRow);

            // Date bounds + rules
            wireDateBounds();
            applyCategoryRules();
        })();
    </script>
@endsection