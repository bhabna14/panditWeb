@extends('admin.layouts.app')

@section('styles')
<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">Add Flower Pickup Details</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item tx-15"><a href="{{ route('admin.manageflowerpickupdetails') }}" class="btn btn-info text-white">Manage  Flower Pickup Details</a></li>
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Flower Pickup Details</li>
        </ol>
    </div>
</div>
<!-- /breadcrumb -->
@if (session('success'))
    <div class="alert alert-success" id="Message">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-12 col-sm-12">
        <form action="{{ route('flower-pickup.update', $pickupDetail->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Vendor -->
            <div class="mb-3">
                <label for="vendor_id" class="form-label">Vendor</label>
                <input type="text" id="vendor_id" name="vendor_id" value="{{ $pickupDetail->vendor_id }}" class="form-control">
            </div>
    
            <!-- Rider -->
            <div class="mb-3">
                <label for="rider_id" class="form-label">Rider</label>
                <input type="text" id="rider_id" name="rider_id" value="{{ $pickupDetail->rider_id }}" class="form-control">
            </div>
    
            <!-- Pickup Date -->
            <div class="mb-3">
                <label for="pickup_date" class="form-label">Pickup Date</label>
                <input type="date" id="pickup_date" name="pickup_date" value="{{ $pickupDetail->pickup_date }}" class="form-control">
            </div>
    
            <!-- Flower Pickup Items -->
            <h5>Flower Pickup Items</h5>
            @foreach ($pickupDetail->flowerPickupItems as $item)
            <div class="mb-3">
                <label for="flower_{{ $item->id }}" class="form-label">Flower</label>
                <input type="text" id="flower_{{ $item->id }}" name="flowers[{{ $item->id }}][flower]" value="{{ $item->flower?->name }}" class="form-control">
    
                <label for="quantity_{{ $item->id }}" class="form-label">Quantity</label>
                <input type="number" id="quantity_{{ $item->id }}" name="flowers[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" class="form-control">
    
                <label for="price_{{ $item->id }}" class="form-label">Price</label>
                <input type="text" id="price_{{ $item->id }}" name="flowers[{{ $item->id }}][price]" value="{{ $item->price }}" class="form-control">
            </div>
            @endforeach
    
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
       
                
    </div>
</div>
@endsection
@section('scripts')
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    setTimeout(function(){
        document.getElementById('Message').style.display = 'none';
    }, 3000);
</script>
<script>
    document.getElementById('add-more-flower-pickup').addEventListener('click', function() {
    const container = document.getElementById('flower-pickup-container');
    const row = container.querySelector('.flower-pickup-row').cloneNode(true);
    row.querySelectorAll('input, select').forEach(field => field.value = '');
    container.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-flower-pickup-row')) {
        e.target.closest('.flower-pickup-row').remove();
    }
});

</script>
 <!-- Add Flower Script -->
 <script>
    $(document).ready(function() {
        // Add More Flowers
        $("#addflower").click(function() {
            $("#add-flower-wrapper").append(`
                <div class="remove-flower-wrapper">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="flower_id">Flower</label>
                                <select name="flower_id[]" class="form-control" required>
                                    @foreach($flowers as $flower)
                                        <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_id">Unit</label>
                                <select name="unit_id[]" class="form-control" required>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" name="quantity[]" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger mt-2 remove_flower">Remove</button>
                </div>
            `);
        });

        // Remove Flower
        $(document).on('click', '.remove_flower', function() {
            $(this).closest('.remove-flower-wrapper').remove();
        });
    });
</script>


@endsection
