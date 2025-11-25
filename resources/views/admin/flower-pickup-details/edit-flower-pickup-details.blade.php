@extends('admin.layouts.apps')

@section('styles')
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .flower-row + .flower-row {
            border-top: 1px dashed #ddd;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
        }

        .remove_flower {
            margin-top: 1.9rem;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Edit Flower Pickup Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.manageflowerpickupdetails') }}" class="btn btn-info text-white">
                        Manage Flower Pickup Details
                    </a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Flower Pickup Details</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if (session('success'))
        <div class="alert alert-success" id="Message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-sm-12">
            <form method="POST" action="{{ route('flower-pickup.update', $detail->id) }}">
                @csrf
                @method('PUT')

                <div id="show_doc_item">
                    <div class="card">
                        <div class="card-body pt-4">
                            <!-- Header section -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vendor_id" class="form-label">Vendor</label>
                                        <select name="vendor_id" class="form-control" required>
                                            <option value="" disabled {{ !$detail->vendor_id ? 'selected' : '' }}>
                                                Choose
                                            </option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->vendor_id }}"
                                                    {{ $detail->vendor_id == $vendor->vendor_id ? 'selected' : '' }}>
                                                    {{ $vendor->vendor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pickup_date" class="form-label">Pickup Date</label>
                                        <input type="date"
                                               name="pickup_date"
                                               class="form-control"
                                               value="{{ optional($detail->pickup_date)->format('Y-m-d') }}"
                                               required>
                                    </div>
                                </div>

                                <div class="col-md-4 d-flex align-items-end justify-content-md-end">
                                    <h4 id="total_price" class="mb-0">
                                        Total Price: ₹{{ number_format((float) $detail->total_price, 2) }}
                                    </h4>
                                </div>
                            </div>

                            <hr>

                            <!-- Flower Details -->
                            <h5 class="mb-2">Flower Items</h5>
                            <div id="add-flower-wrapper">
                                @forelse ($detail->flowerPickupItems as $item)
                                    <div class="row g-2 align-items-end flower-row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Flower</label>
                                                <select name="flower_id[]" class="form-control" required>
                                                    @foreach ($flowers as $flower)
                                                        <option value="{{ $flower->product_id }}"
                                                            {{ $item->flower_id == $flower->product_id ? 'selected' : '' }}>
                                                            {{ $flower->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Unit</label>
                                                <select name="unit_id[]" class="form-control" required>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}"
                                                            {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->unit_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Quantity</label>
                                                <input type="number"
                                                       name="quantity[]"
                                                       class="form-control quantity-input"
                                                       step="0.01"
                                                       min="0"
                                                       value="{{ $item->quantity }}"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Price (₹)</label>
                                                <input type="number"
                                                       name="price[]"
                                                       class="form-control price-input"
                                                       step="0.01"
                                                       min="0"
                                                       value="{{ $item->price }}">
                                            </div>
                                        </div>

                                        <div class="col-md-1 text-md-start">
                                            <button type="button" class="btn btn-danger remove_flower">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    {{-- Fallback: at least one blank row --}}
                                    <div class="row g-2 align-items-end flower-row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Flower</label>
                                                <select name="flower_id[]" class="form-control" required>
                                                    @foreach ($flowers as $flower)
                                                        <option value="{{ $flower->product_id }}">
                                                            {{ $flower->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Unit</label>
                                                <select name="unit_id[]" class="form-control" required>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">
                                                            {{ $unit->unit_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Quantity</label>
                                                <input type="number"
                                                       name="quantity[]"
                                                       class="form-control quantity-input"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Price (₹)</label>
                                                <input type="number"
                                                       name="price[]"
                                                       class="form-control price-input"
                                                       step="0.01"
                                                       min="0">
                                            </div>
                                        </div>

                                        <div class="col-md-1 text-md-start">
                                            <button type="button" class="btn btn-danger remove_flower">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <button type="button" class="btn btn-success mt-3" id="addflower">
                                <i class="fa fa-plus"></i> Add More Flowers
                            </button>

                            <!-- Rider Assignment -->
                            <hr class="mt-4">
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rider_id" class="form-label">Assign to Rider</label>
                                        <select name="rider_id" class="form-control" required>
                                            <option value="" disabled {{ !$detail->rider_id ? 'selected' : '' }}>
                                                Choose
                                            </option>
                                            @foreach ($riders as $rider)
                                                <option value="{{ $rider->rider_id }}"
                                                    {{ $detail->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                    {{ $rider->rider_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div> <!-- show_doc_item -->

                <button type="submit" class="btn btn-primary mt-3">
                    Update Pickup
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Include SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Hide success message after 3s
        setTimeout(function () {
            const msg = document.getElementById('Message');
            if (msg) msg.style.display = 'none';
        }, 3000);
    </script>

    <!-- Dynamic flower rows + total price -->
    <script>
        $(document).ready(function () {

            function recalcTotal() {
                let total = 0;
                $('.price-input').each(function () {
                    const v = parseFloat($(this).val());
                    if (!isNaN(v)) {
                        total += v;
                    }
                });
                $('#total_price').text('Total Price: ₹' + total.toFixed(2));
            }

            // Initial calc (in case of existing values)
            recalcTotal();

            // Add More Flowers
            $('#addflower').on('click', function () {
                const tpl = `
                    <div class="row g-2 align-items-end flower-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Flower</label>
                                <select name="flower_id[]" class="form-control" required>
                                    @foreach ($flowers as $flower)
                                        <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Unit</label>
                                <select name="unit_id[]" class="form-control" required>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Quantity</label>
                                <input type="number"
                                       name="quantity[]"
                                       class="form-control quantity-input"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Price (₹)</label>
                                <input type="number"
                                       name="price[]"
                                       class="form-control price-input"
                                       step="0.01"
                                       min="0">
                            </div>
                        </div>

                        <div class="col-md-1 text-md-start">
                            <button type="button" class="btn btn-danger remove_flower">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#add-flower-wrapper').append(tpl);
            });

            // Remove Flower row
            $(document).on('click', '.remove_flower', function () {
                const rows = $('.flower-row');
                if (rows.length <= 1) {
                    // If only one row, just clear it instead of removing
                    const row = rows.eq(0);
                    row.find('select').val('');
                    row.find('input[type="number"]').val('');
                } else {
                    $(this).closest('.flower-row').remove();
                }
                recalcTotal();
            });

            // Recalculate total on price input change
            $(document).on('input', '.price-input', function () {
                recalcTotal();
            });
        });
    </script>
@endsection
