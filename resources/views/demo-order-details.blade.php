@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    {{-- <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet"> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />

@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Existing User Order</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/demo-order-details') }}"
                        class="btn btn-warning text-dark">New User</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                {{-- <li class="breadcrumb-item active tx-15" aria-current="page">Add Product</li> --}}
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

    <form action="{{ route('saveDemoOrderDetails') }}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- User Details -->
        <div class="row">
            <div class="col-md-12">
                <label for="userid" class="form-label">User</label>
                <select class="form-control select2" id="userid" name="userid" required>
                    <option value="">Select User</option>
                    @foreach ($user_details as $user)
                        <option value="{{ $user->userid }}">
                            {{ $user->userid }} - ({{ $user->mobile_number }})
                        </option>
                    @endforeach
                </select>
                
            </div>
        </div>
        
         <!-- Address Section -->
         <div class="row">
            <div class="col-md-12 mt-3">
                <h4 class="mb-3">Add Address</h4>
            </div>
            <div class="col-md-12">
                <div class="your-address-list" id="addressContainer">
                    <p>Select a user to load addresses.</p>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="product" class="form-label">Flower</label>
                <select name="product_id" id="product" class="form-control select2" required>
                    <option value="">Select Flower</option>
                    @foreach ($flowers as $flower)
                        <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="duration" class="form-label">duration</label>
                <select name="duration" id="duration" class="form-control" required>
                    <option value="1">1 month</option>
                    <option value="3">3 month</option>
                    <option value="6">6 month</option>

                </select>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="row mt-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" id="start_date"
                    placeholder="Enter Amount" required>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" id="end_date"
                    placeholder="Enter Amount" required>
            </div>
            <div class="col-md-3">
                <label for="paid_amount" class="form-label">Paid Amount</label>
                <input type="number" name="paid_amount" class="form-control" id="paid_amount"
                    placeholder="Enter Amount" required>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active">active</option>
                    <option value="expired">expired</option>

                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    {{-- <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select User",
            allowClear: true
        });
    });
</script>

   
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select User",
            allowClear: true
        });

        // Handle user selection
        $('.select2').on('change', function () {
            const userId = this.value;

            // Clear previous addresses
            const addressContainer = document.getElementById('addressContainer');
            addressContainer.innerHTML = '<p>Loading addresses...</p>';

            if (userId) {
                fetch(`/admin/get-user-addresses/${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.addresses.length > 0) {
                            addressContainer.innerHTML = ''; // Clear loading message
                            let addressHTML = '<div class="row">';
                            
                            data.addresses.forEach((address, index) => {
                                const defaultBadge = address.default 
                                    ? '<span class="badge bg-success">Default</span>' 
                                    : '';
                                
                                // Create a new row every 3 addresses
                                if (index % 3 === 0 && index !== 0) {
                                    addressHTML += '</div><div class="row">';
                                }

                                addressHTML += `
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <input type="radio" name="address_id" id="address${address.id}" value="${address.id}" required>
                                                <label for="address${address.id}">
                                                    <h5 class="card-title">${address.address_type} ${defaultBadge}</h5>
                                                    <p class="card-text">
                                                        ${address.apartment_flat_plot ?? ''},<br>
                                                        ${address.locality_name ?? 'N/A'},<br>
                                                        ${address.landmark ?? ''}<br>
                                                        ${address.city}, ${address.state}, ${address.country}<br>
                                                        ${address.pincode}
                                                    </p>
                                                </label>
                                            </div>
                                        </div>
                                    </div>`;
                            });

                            addressHTML += '</div>'; // Close the last row
                            addressContainer.innerHTML = addressHTML;
                        } else {
                            addressContainer.innerHTML = '<p>No addresses found for the selected user.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching addresses:', error);
                        addressContainer.innerHTML = '<p>Failed to load addresses. Please try again.</p>';
                    });
            } else {
                addressContainer.innerHTML = '<p>Select a user to load addresses.</p>';
            }
        });
    });
</script>

    
@endsection
