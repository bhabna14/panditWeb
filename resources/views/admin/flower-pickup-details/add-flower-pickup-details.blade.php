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
            <li class="breadcrumb-item tx-15"><a href="{{ route('admin.managevendor') }}" class="btn btn-info text-white">Manage  Flower Pickup Details</a></li>
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
        <div class="card">
            <div class="card-body pt-0 pt-4">
                <form method="POST" action="{{ route('admin.saveFlowerPickupDetails') }}">
                    @csrf
                
                    <div id="flower-pickup-container">
                        <div class="flower-pickup-row">
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
                
                           <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_id">Vendor</label>
                                    <select name="vendor_id[]" class="form-control" required>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                           </div>
                
                           <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rider_id">Assign to Rider</label>
                                    <select name="rider_id[]" class="form-control" required>
                                        @foreach($riders as $rider)
                                            <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                           </div>
                
                            <div class="form-group">
                                <label for="pickup_date">Date</label>
                                <input type="date" name="pickup_date[]" class="form-control" required>
                            </div>
                            </div>
                            <button type="button" class="btn btn-danger remove-flower-pickup-row">Remove</button>
                        </div>
                    </div>
                
                    <button type="button" class="btn btn-secondary" id="add-more-flower-pickup">Add More</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                
            </div>
        </div>
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
@endsection

