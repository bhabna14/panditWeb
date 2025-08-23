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
        @endphp

        <div class="row">

            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ $product->name }}"
                    required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="odia_name" class="form-label">Product Name (Odia)</label>
                <input type="text" name="odia_name" class="form-control" id="odia_name"
                    value="{{ old('odia_name', $product->odia_name) }}">
            </div>

            <div class="col-md-3 mb-3">
                <label for="mrp" class="form-label">MRP (Rs.)</label>
                <input type="number" name="mrp" class="form-control" id="mrp"
                    value="{{ old('mrp', $product->mrp) }}" required>
            </div>

            <div class="col-md-3 mb-3">
                <label for="price" class="form-label">Price (Rs.)</label>
                <input type="number" name="price" class="form-control" id="price"
                    value="{{ old('price', $product->price) }}" required>
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
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerActive" value="yes"
                        {{ $availDefault === 'yes' ? 'checked' : '' }}>
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

            <!-- Package fields -->
            <div id="packageFields" class="col-md-12 mb-3" style="display:none;">
                <div id="packageItems">
                    @foreach ($selectedItems as $selected)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select class="form-control select2 item-select" name="item_id[]">
                                    <option value="">Select Puja List</option>
                                    @foreach ($Poojaitemlist as $pujalist)
                                        <option value="{{ $pujalist->id }}"
                                            {{ $pujalist->id == $selected->item_id ? 'selected' : '' }}>
                                            {{ $pujalist->item_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control select2 variant-select" name="variant_id[]">
                                    <option value="">Select Variant</option>
                                    @foreach ($Poojaitemlist->find($selected->item_id)->variants as $variant)
                                        <option value="{{ $variant->id }}"
                                            {{ $variant->id == $selected->variant_id ? 'selected' : '' }}>
                                            {{ $variant->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
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
                        $benefits = !empty($product->benefits) ? explode('#', $product->benefits) : [''];
                    @endphp
                    @foreach ($benefits as $benefit)
                        <div class="input-group mb-2 benefit-row">
                            <input type="text" name="benefits[]" class="form-control" value="{{ trim($benefit) }}"
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

@section('modal')
@endsection

@section('scripts')
    <!-- Form-layouts js -->
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Initialize Select2 for dropdowns
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');

            // Flower-only blocks
            const malaField = document.getElementById('malaProvidedField');
            const flowerField = document.getElementById('flowerAvailabilityField');
            const flowerFromField = document.getElementById('flowerFromField');
            const flowerToField = document.getElementById('flowerToField');
            const availableFrom = document.getElementById('available_from');
            const availableTo = document.getElementById('available_to');

            // Package blocks
            const packageFields = document.getElementById('packageFields');
            const packageItems = document.getElementById('packageItems');
            const addMoreButton = document.getElementById('addMore');
            const removeLastButton = document.getElementById('removeLast');

            // BENEFITS add/remove
            const benefitsWrapper = document.getElementById('benefitsWrapper');
            const addBenefitBtn = document.getElementById('addBenefit');

            function show(el, yes = true) {
                if (!el) return;
                el.style.display = yes ? 'block' : 'none';
            }

            function toggleByCategory() {
                const cat = categorySelect.value;
                const isFlower = cat === 'Flower';
                const isPackage = cat === 'Package';

                // Flower shows: radios + date range; else hide
                show(malaField, isFlower);
                show(flowerField, isFlower);
                show(flowerFromField, isFlower);
                show(flowerToField, isFlower);

                // Package fields
                show(packageFields, isPackage);
            }

            // keep "To" >= "From"
            if (availableFrom && availableTo) {
                availableFrom.addEventListener('change', function() {
                    availableTo.min = availableFrom.value || '';
                    if (availableTo.value && availableFrom.value && availableTo.value < availableFrom
                        .value) {
                        availableTo.value = '';
                    }
                });
                availableTo.addEventListener('change', function() {
                    availableFrom.max = availableTo.value || '';
                });
                // initialize min/max on load
                if (availableFrom.value) availableTo.min = availableFrom.value;
                if (availableTo.value) availableFrom.max = availableTo.value;
            }

            // Package: add/remove rows and load variants for new rows (using data-variants on options)
            if (addMoreButton && removeLastButton && packageItems) {
                addMoreButton.addEventListener('click', function() {
                    const newRow = document.createElement('div');
                    newRow.classList.add('row', 'mb-3');
                    newRow.innerHTML = `
                <div class="col-md-6">
                    <select class="form-control select2 item-select" name="item_id[]" required>
                        <option value="">Select Puja List</option>
                        @foreach ($Poojaitemlist as $pujalist)
                            <option value="{{ $pujalist->id }}"
                                data-variants="{{ htmlspecialchars(json_encode($pujalist->variants), ENT_QUOTES, 'UTF-8') }}">
                                {{ $pujalist->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-control select2 variant-select" name="variant_id[]" required>
                        <option value="">Select Variant</option>
                    </select>
                </div>`;
                    packageItems.appendChild(newRow);

                    // init listener for this row
                    const itemSelect = newRow.querySelector('.item-select');
                    initializeItemChangeListener(itemSelect);
                });

                removeLastButton.addEventListener('click', function() {
                    const rows = packageItems.querySelectorAll('.row');
                    if (rows.length > 0) rows[rows.length - 1].remove();
                });

                function initializeItemChangeListener(itemSelect) {
                    if (!itemSelect) return;
                    itemSelect.addEventListener('change', function() {
                        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                        const variantsAttr = selectedOption.getAttribute('data-variants');
                        const variantSelect = itemSelect.closest('.row').querySelector('.variant-select');
                        variantSelect.innerHTML = '<option value="">Select Variant</option>';
                        if (!variantsAttr) return;

                        try {
                            let variants = variantsAttr.replace(/&quot;/g, '"').replace(/&amp;/g, '&');
                            variants = JSON.parse(variants);
                            variants.forEach(v => {
                                const opt = document.createElement('option');
                                opt.value = v.id;
                                opt.textContent = `${v.title}`;
                                variantSelect.appendChild(opt);
                            });
                        } catch (e) {
                            console.error('Variant parse error:', e);
                        }
                    });
                }

                // bind existing .item-selects (if any)
                document.querySelectorAll('.item-select').forEach(initializeItemChangeListener);
            }

            // Benefits add/remove
            if (addBenefitBtn && benefitsWrapper) {
                addBenefitBtn.addEventListener('click', function() {
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2 benefit-row';
                    div.innerHTML = `
                <input type="text" name="benefits[]" class="form-control" placeholder="Enter benefit">
                <button type="button" class="btn btn-danger removeBenefit">Remove</button>`;
                    benefitsWrapper.appendChild(div);
                });
                benefitsWrapper.addEventListener('click', function(e) {
                    if (e.target.classList.contains('removeBenefit')) {
                        const rows = benefitsWrapper.querySelectorAll('.benefit-row');
                        if (rows.length > 1) e.target.closest('.benefit-row').remove();
                    }
                });
            }

            // init
            categorySelect.addEventListener('change', toggleByCategory);
            toggleByCategory(); // run once on load
        });
    </script>
@endsection
