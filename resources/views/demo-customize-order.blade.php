@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
    <style>
        .ui-datepicker {
            font-size: 16px;
        }

        .form-control {
            cursor: pointer;
        }

        .ui-datepicker td a {
            cursor: pointer;
        }

        .card {
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card h5 {
    font-size: 1.2em;
    font-weight: bold;
}

.card-body {
    padding: 15px;
}

.badge {
    font-size: 0.8em;
}

    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Add Customize Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-product') }}"
                        class="btn btn-warning text-dark">Manage Product</a></li>
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

    <form action="{{ route('saveCustomizeOrder') }}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- User Details -->
        <div class="row">
            <div class="col-md-12">
                <label for="userid" class="form-label">User</label>
                <select class="form-control" id="userid" name="userid" required>
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
            <div class="col-md-12">
                <h4 class="mb-3">Add Address</h4>
            </div>
            <div class="col-md-12">
                <div class="your-address-list" id="addressContainer">
                    <p>Select a user to load addresses.</p>
                </div>
            </div>
        </div>
    
        <!-- Remaining Fields -->
        <div id="flower-container">
            <div class="row mb-3 flower-group">
                    <div class="col-3">
                        <label for="flower_name">Flower <span style="color:red">*</span></label>
                        <select name="flower_name[]" class="form-control" required>
                            @foreach ($singleflowers as $singleflower)
                                <option value="{{ $singleflower->name }}">
                                    {{ $singleflower->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="flower_quantity">Quantity <span style="color:red">*</span></label>
                        <input type="text" name="flower_quantity[]" required class="form-control" placeholder="Enter quantity">
                    </div>
                    <div class="col-3">
                        <label for="flower_unit">Unit <span style="color:red">*</span></label>
                        <select name="flower_unit[]" class="form-control" required>
                            @foreach ($Poojaunits as $Poojaunit)
                                <option value="{{ $Poojaunit->unit_name }}">
                                    {{ $Poojaunit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                <div class="col-3 mt-4">
                    <button type="button" class="btn btn-success" id="addFlower" style="font-weight: bold; padding: 10px 20px;">
                        <i class="fas fa-plus-circle" style="margin-right:5px"></i> Add More
                    </button>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="form-input mt-20 col-md-6">
                <label for="date">Please Select the Date <span style="color:red">*</span></label>
                <input type="text" name="date" required placeholder="Please Select The Date" class="form-control"
                    id="date">
            </div>

            <div class="form-input mt-20 col-md-6">
                <label for="time">Please Select the Time <span style="color:red">*</span></label>
                <input type="text" name="time" required placeholder="Please Select The Time" class="form-control"
                    id="time">
            </div>

        </div>

    
        <!-- Submit Button -->
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
        $(document).ready(function() {
            // Add new flower group
            $("#addFlower").click(function() {
                $("#flower-container").append(`
                <div class="row mb-3 input-wrapper">
                    <div class="col-3">
                        <label for="">Flower</label>
                        <select name="flower_name[]" class="form-control" required>
                            
                            @foreach ($singleflowers as $singleflower)
                            <option value="{{ $singleflower->name }}">
                                {{ $singleflower->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="">Quantity</label>
                        <input type="text" name="flower_quantity[]" required class="form-control" placeholder="Enter quantity">
                    </div>
                    <div class="col-3">
                        <label for="">Unit</label>
                        <select name="flower_unit[]" class="form-control" required>
                           
                            @foreach ($Poojaunits as $Poojaunit)
                            <option value="{{ $Poojaunit->unit_name }}">
                                {{ $Poojaunit->unit_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-4 text-right">
                        <button type="button" class="btn btn-danger removeChild" style="font-weight: bold; padding: 10px 20px;">
                            <i class="fas fa-minus-circle" style="margin-right:5px"></i> Remove
                        </button>
                    </div>
                </div>
            `);
            });

            // Remove a flower group
            $(document).on('click', '.removeChild', function() {
                $(this).closest('.input-wrapper')
                    .remove(); // Remove the parent div with class input-wrapper
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize datepicker and timepicker
            $("#date").datepicker({
                dateFormat: "yy-mm-dd",
                minDate: 0,
                onSelect: function(dateText) {
                    console.log("Date selected: " + dateText);
                    // Update the timepicker when a date is selected
                    $("#time").timepicker('option', 'minTime', getMinTime());
                }
            });

            // Function to calculate current time + 2 hours and format it for the timepicker
            function getMinTime() {
                const now = new Date();
                now.setHours(now.getHours() + 2);
                now.setMinutes(0); // Round minutes to the nearest 15 if needed

                let hours = now.getHours();
                let minutes = now.getMinutes();
                if (minutes < 10) minutes = '0' + minutes;
                if (hours < 10) hours = '0' + hours;

                return `${hours}:${minutes}`;
            }

            // Initialize timepicker
            $("#time").timepicker({
                timeFormat: 'h:i A', // 12-hour format with AM/PM
                step: 15, // Interval for selectable times
                minTime: getMinTime(), // Start time is 2 hours from current time
                maxTime: '23:59',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        });
    </script>


    <!-- jQuery UI library for datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- jQuery Timepicker plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>

    <script>
       document.getElementById('userid').addEventListener('change', function () {
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
                        const defaultBadge = address.default ? 
                            '<span class="badge bg-success">Default</span>' : '';
                        
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

    </script>
@endsection
