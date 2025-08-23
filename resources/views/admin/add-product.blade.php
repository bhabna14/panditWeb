@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">ADD Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-product') }}"
                        class="btn btn-warning text-dark">Manage Product</a></li>

            </ol>
        </div>
    </div>

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

    <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Product Name -->
            <div class="col-md-4 mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter product name"
                    required>
            </div>

            <!-- Odia Product Name -->
            <div class="col-md-4 mb-3">
                <label for="odia_name" class="form-label">Product Name (Odia)</label>
                <input type="text" name="odia_name" class="form-control" id="odia_name"
                    placeholder="Enter product name in Odia">
            </div>

            <!-- Price -->
            <div class="col-md-4 mb-3">
                <label for="mrp" class="form-label">MRP (Rs.)</label>
                <input type="number" name="mrp" class="form-control" id="mrp" placeholder="Enter product mrp"
                    required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="price" class="form-label">Sale Price (Rs.)</label>
                <input type="number" name="price" class="form-control" id="price"
                    placeholder="Enter product sale price" required>
            </div>

            <!-- Category -->
            <div class="col-md-4 mb-3">
                <label for="category" class="form-label">Category</label>
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

            <!-- Mala Provided Field (for Flower category) -->
            <div class="col-md-4 mb-3" id="malaProvidedField" style="display: none;">
                <label class="form-label">Is Mala Provided with this Flower?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaYes" value="yes">
                    <label class="form-check-label" for="malaYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mala_provided" id="malaNo" value="no">
                    <label class="form-check-label" for="malaNo">No</label>
                </div>
            </div>

            <!-- Flower Availability Field (for Flower category) -->
            <div class="col-md-4 mb-3" id="flowerAvailabilityField" style="display: none;">
                <label class="form-label">Is this Flower Available?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerActive" value="yes">
                    <label class="form-check-label" for="flowerActive">Active</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flower_available" id="flowerInactive"
                        value="no">
                    <label class="form-check-label" for="flowerInactive">Inactive</label>
                </div>
            </div>

            <!-- NEW: Flower availability dates (shown only when category=Flower) -->
            <div class="col-md-4 mb-3" id="flowerFromField" style="display:none;">
                <label for="available_from" class="form-label">Available From</label>
                <input type="date" name="available_from" id="available_from" class="form-control">
            </div>

            <div class="col-md-4 mb-3" id="flowerToField" style="display:none;">
                <label for="available_to" class="form-label">Available To</label>
                <input type="date" name="available_to" id="available_to" class="form-control">
            </div>

            <!-- Pooja (for Package) -->
            <div class="col-md-4 mb-3" id="poojafields" style="display: none;">
                <div class="form-group">
                    <label for="pooja_name" class="form-label">Pooja Name</label>
                    <select class="form-control" id="pooja_id" name="pooja_id">
                        <option value="">Select Festival</option>
                        @foreach ($pooja_list as $pooja)
                            <option value="{{ $pooja->id }}">{{ $pooja->pooja_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Package fields -->
            <div id="packageFields" class="col-md-12 mb-3" style="display: none;">
                <div id="packageItems">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <select class="form-control select2 item-select" name="item_id[]">
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
                            <select class="form-control select2 variant-select" name="variant_id[]">
                                <option value="">Select Variant</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" id="addMore" class="btn btn-secondary">Add More</button>
                <button type="button" id="removeLast" class="btn btn-danger">Remove Last</button>
            </div>

            <!-- Subscription Duration -->
            <div class="col-md-4 mb-3">
                <label for="duration" class="form-label">Subscription Duration (Months)</label>
                <select name="duration" id="duration" class="form-control select2">
                    <option value="" disabled selected>Select Package</option>
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                </select>
            </div>

            <!-- Stock -->
            <div class="col-md-4 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" id="stock"
                    placeholder="Enter stock quantity">
            </div>

            <!-- Product Image -->
            <div class="col-md-4 mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" name="product_image" class="form-control" id="product_image" required>
            </div>

            <!-- Benefits -->
            <div class="col-md-4 mb-3">
                <label for="benefit" class="form-label">Benefits</label>
                <div id="benefitFields">
                    <div class="input-group mb-2 benefit-row">
                        <input type="text" name="benefit[]" class="form-control" placeholder="Enter benefit"
                            required>
                        <button type="button" class="btn btn-success add-benefit" title="Add Benefit"><i
                                class="fa fa-plus"></i> Add</button>
                        <button type="button" class="btn btn-danger remove-benefit" title="Remove Benefit"
                            style="display:none;"><i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="3"
                    placeholder="Enter product description" required></textarea>
            </div>

            <!-- Submit Button -->
            <div class="col-md-12 mt-4">
                <button type="submit" class="btn btn-primary">Add Product</button>
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
        setTimeout(function() {
            $('#Message').fadeOut('fast');
        }, 2500);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const packageFields = document.getElementById('packageFields');
            const poojafields = document.getElementById('poojafields');
            const packageItems = document.getElementById('packageItems');
            const addMoreButton = document.getElementById('addMore');
            const removeLastButton = document.getElementById('removeLast');

            // NEW: Duration group wrapper (the .mb-3 around the select)
            const durationSelect = document.getElementById('duration');
            const durationGroup = durationSelect ? durationSelect.closest('.mb-3') : null;

            function updateByCategory() {
                const cat = categorySelect.value;
                const isPackage = (cat === 'Package');
                const isSubscription = (cat === 'Subscription');

                // Show/hide Package-related blocks
                packageFields.style.display = isPackage ? 'block' : 'none';
                poojafields.style.display = isPackage ? 'block' : 'none';

                // Show duration only for Subscription; hide otherwise (including Package)
                if (durationGroup) {
                    durationGroup.style.display = isSubscription ? 'block' : 'none';
                    if (!isSubscription) {
                        durationSelect.selectedIndex = 0; // clear selection when hidden
                    }
                }
            }

            // Handle category changes
            categorySelect.addEventListener('change', updateByCategory);
            updateByCategory(); // run once on load

            // Add more package items
            addMoreButton.addEventListener('click', function() {
                const newItemRow = document.createElement('div');
                newItemRow.classList.add('row', 'mb-3');
                newItemRow.innerHTML = `
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
                    </div>
                `;
                packageItems.appendChild(newItemRow);

                // Reinitialize event listeners for dynamically added items
                initializeItemChangeListener(newItemRow.querySelector('.item-select'));
            });

            // Remove the last added item
            removeLastButton.addEventListener('click', function() {
                const rows = packageItems.querySelectorAll('.row');
                if (rows.length > 1) {
                    rows[rows.length - 1].remove();
                }
            });

            // Variants loader
            function initializeItemChangeListener(itemSelect) {
                itemSelect.addEventListener('change', function() {
                    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                    const variants = selectedOption.getAttribute('data-variants');
                    const variantSelect = itemSelect.closest('.row').querySelector('.variant-select');

                    // Clear previous options
                    variantSelect.innerHTML = '<option value="">Select Variant</option>';

                    if (variants) {
                        try {
                            let parsedVariants = variants;

                            // Decode HTML entities and parse JSON
                            if (typeof parsedVariants === 'string') {
                                parsedVariants = parsedVariants.replace(/&quot;/g, '"').replace(/&amp;/g,
                                    '&');
                                parsedVariants = JSON.parse(parsedVariants);
                            }

                            // Populate the variant dropdown
                            parsedVariants.forEach(function(variant) {
                                const option = document.createElement('option');
                                option.value = variant.id;
                                option.textContent = `${variant.title} - ${variant.price}`;
                                variantSelect.appendChild(option);
                            });
                        } catch (e) {
                            console.error('Error parsing variant data:', e);
                        }
                    }
                });
            }

            // Initialize listeners for the default row
            document.querySelectorAll('.item-select').forEach(initializeItemChangeListener);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const benefitFields = document.getElementById('benefitFields');

            benefitFields.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-benefit')) {
                    const row = e.target.closest('.benefit-row');
                    const newRow = row.cloneNode(true);
                    newRow.querySelector('input').value = '';
                    newRow.querySelector('.remove-benefit').style.display = 'inline-block';
                    benefitFields.appendChild(newRow);
                }
                if (e.target.classList.contains('remove-benefit')) {
                    const row = e.target.closest('.benefit-row');
                    if (benefitFields.querySelectorAll('.benefit-row').length > 1) {
                        row.remove();
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');

            const malaProvidedField = document.getElementById('malaProvidedField');
            const flowerAvailabilityField = document.getElementById('flowerAvailabilityField');

            const flowerFromField = document.getElementById('flowerFromField');
            const flowerToField = document.getElementById('flowerToField');
            const availableFrom = document.getElementById('available_from');
            const availableTo = document.getElementById('available_to');

            // NEW: wrappers for Duration + Stock (their .mb-3 containers)
            const durationSelect = document.getElementById('duration');
            const stockInput = document.getElementById('stock');
            const durationGroup = durationSelect ? durationSelect.closest('.mb-3') : null;
            const stockGroup = stockInput ? stockInput.closest('.mb-3') : null;

            function setRequiredForFlower(on) {
                [availableFrom, availableTo].forEach(el => {
                    if (!el) return;
                    if (on) {
                        el.setAttribute('required', 'required');
                    } else {
                        el.removeAttribute('required');
                    }
                });
            }

            function clearFlowerDates() {
                if (availableFrom) availableFrom.value = '';
                if (availableTo) availableTo.value = '';
                if (availableFrom) availableFrom.removeAttribute('max');
                if (availableTo) availableTo.removeAttribute('min');
            }

            function toggleFlowerFields() {
                const isFlower = categorySelect.value === 'Flower';

                // show/hide standard flower fields
                malaProvidedField.style.display = isFlower ? 'block' : 'none';
                flowerAvailabilityField.style.display = isFlower ? 'block' : 'none';

                // show/hide new date fields
                flowerFromField.style.display = isFlower ? 'block' : 'none';
                flowerToField.style.display = isFlower ? 'block' : 'none';

                // NEW: hide Duration + Stock when Flower selected; show otherwise
                if (durationGroup) durationGroup.style.display = isFlower ? 'none' : '';
                if (stockGroup) stockGroup.style.display = isFlower ? 'none' : '';

                // If hiding, clear their values to avoid unintended submits
                if (isFlower) {
                    if (durationSelect) durationSelect.selectedIndex = 0; // back to "Select Package"
                    if (stockInput) stockInput.value = '';
                }

                // required + cleanup for flower-only fields
                setRequiredForFlower(isFlower);
                if (!isFlower) {
                    malaProvidedField.querySelectorAll('input[type=radio]').forEach(r => r.checked = false);
                    flowerAvailabilityField.querySelectorAll('input[type=radio]').forEach(r => r.checked = false);
                    clearFlowerDates();
                }
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
            }

            // init on load + on change
            categorySelect.addEventListener('change', toggleFlowerFields);
            toggleFlowerFields();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
        });
    </script>
@endsection
