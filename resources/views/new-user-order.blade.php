@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Add Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/new-user-order') }}"
                        class="btn btn-warning text-dark">Existing User</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Add Product</li>
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

    <form action="{{ route('saveNewUserOrder') }}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- User Details -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">User Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter User Name">
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="mobile_number" class="form-control" id="mobile_number" placeholder="Enter Phone Number" required>
            </div>
        </div>

        <!-- Address Details -->
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3">Add Address</h4>
            </div>
            
            <div class="col-md-3">
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="individual" name="place_category" value="Individual"
                        required>
                    <label class="form-check-label" for="individual">Individual</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="apartment" name="place_category" value="Apartment">
                    <label class="form-check-label" for="apartment">Apartment</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="business" name="place_category" value="Business">
                    <label class="form-check-label" for="business">Business</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="temple" name="place_category" value="Temple">
                    <label class="form-check-label" for="temple">Temple</label>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label for="apartment_flat_plot" class="form-label">Apartment/Flat/Plot</label>
                <input type="text" class="form-control" id="apartment_flat_plot" name="apartment_flat_plot"
                    placeholder="Enter details" required>
            </div>
            <div class="col-md-6">
                <label for="landmark" class="form-label">Landmark</label>
                <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Enter landmark" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <label for="locality" class="form-label">Locality</label>
                <select class="form-control" id="locality" name="locality" required>
                    <option value="">Select Locality</option>
                    @foreach ($localities as $locality)
                        <option value="{{ $locality->unique_code }}" data-pincode="{{ $locality->pincode }}">
                            {{ $locality->locality_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <div class="col-md-4">
                <label for="apartment_name" class="form-label">Apartment Name</label>
                <select class="form-control" id="apartment_name" name="apartment_name">
                    <option value="">Select Apartment</option>
                </select>
            </div>
        
            <div class="col-md-4">
                <label for="pincode" class="form-label">Pincode</label>
                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter pincode" readonly required>
            </div>
        </div>
        
       
        
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="city" class="form-label">Town/City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Enter Town/City" required>
            </div>
            <div class="col-md-6">
                <label for="state" class="form-label">State</label>
                <select name="state" class="form-control" id="state" required>
                    <option value="Odisha">Odisha</option>
                </select>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="exampleInputEmail1">Address Type</label>
                </div>
            </div>
            <div class="col-md-2">
                <label class="rdiobox"><input name="address_type" type="radio" value="Home"> <span>Home</span></label>
            </div>
            <div class="col-lg-2">
                <label class="rdiobox"><input name="address_type" type="radio" value="Work"> <span>Work</span></label>
            </div>
            <div class="col-lg-2">
              <label class="rdiobox"><input checked name="address_type" type="radio" value="Other"> <span>Other</span></label>
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
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        // Get the apartment data from the Blade variable
        const apartmentsByLocality = @json($apartmentsByLocality);
    
        document.getElementById('locality').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const uniqueCode = this.value;  // Get selected locality's unique_code
            const pincode = selectedOption.getAttribute('data-pincode');  // Get pincode for selected locality
            const apartmentSelect = document.getElementById('apartment_name');
    
            // Update the pincode input field
            document.getElementById('pincode').value = pincode;
    
            // Clear previous apartment options
            apartmentSelect.innerHTML = '<option value="">Select Apartment</option>';
    
            // If a locality is selected
            if (uniqueCode) {
                // Get apartments for the selected locality
                const apartments = apartmentsByLocality[uniqueCode] || [];
    
                // Populate the apartment dropdown
                if (apartments.length > 0) {
                    apartments.forEach(apartment => {
                        apartmentSelect.innerHTML += `<option value="${apartment.apartment_name}">${apartment.apartment_name}</option>`;
                    });
                } else {
                    apartmentSelect.innerHTML = '<option value="">No Apartments Available</option>';
                }
            }
        });
    </script>
    
@endsection
