@extends('user.layouts.front-flower')

@section('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
</style>
<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        margin: 5px 0 10px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    /* .button {
        background-color: blue;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .button:hover {
        background-color: darkblue;
    } */

    .mt-10 {
        margin-top: 10px;
    }

    .pt-30 {
        padding-top: 30px;
    }
</style>  
@endsection

@section('content')
<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <div class="sc-gJqsIT bdDCMj logo" height="6rem" width="30rem">
                    <div class="low-res-container">
                    </div>
                </div>
                <h1 class="sc-7kepeu-0 kYnyFA description">BOOK NOW</h1>
            </div>
        </div>
</section>


<section class="booking-form mt-30 mb-30">
    <div class="container">
        <div class="row">
            <h4 class="mb-20">Book Now</h4>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '{{ session('error') }}',
                        confirmButtonText: 'Okay'
                    });
                </script>
            @endif

            @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Subscription Activated Successfully',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'Okay'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the booking history page
                        window.location.href = '{{ route('subscription.history') }}'; // Make sure to use the correct route name
                    }
                });
            </script>
             @endif

            <div class="col-md-7">
                <form action="{{ route('booking.flower.subscription') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="duration" value="{{ $product->duration }}">
                    <input type="hidden" name="price" value="{{ $product->price }}">
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                    @if(!$addresses->isEmpty())
                    <div class="row">
                        <div class="form-input mt-20 col-md-12">
                            <label for="">Please Select the Date</label>
                            <input type="text" name="start_date" required class="form-control" id="date" placeholder="Select a date">
                        </div>

                        <div class="form-input mt-20 col-md-12">
                            <label for="">Suggestions</label>
                            <textarea name="suggestion" id="suggestion"  class="form-control"  rows="3"></textarea>
                        </div>
                    </div>

                     @endif
                
                    <div class="row">
                        <div class="col-md-12">
                            @foreach ($addresses as $address)
                            <div class="your-address">
                                <input type="radio" name="address_id" id="address{{ $address->id }}" value="{{ $address->id }}" required>
                                <label for="address{{ $address->id }}">
                                    <div class="address-type">{{ $address->address_type }}</div>
                                    {{ $address->apartment_flat_plot ?? "" }}, {{ $address->localityDetails->locality_name ?? 'N/A' }},
                                    {{ $address->landmark ?? "" }}<br>
                                    {{ $address->city }}, {{ $address->state }}, {{ $address->country }}, {{ $address->pincode }}
                                    @if($address->default == 1)
                                        <div class="default-badge">Default</div>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                        </div>
                    </div>
  
                    <div class="row" style="margin-top:20px; margin-bottom: 24px;">
                        <div class="col-md-4">
                            <a href="#" class="add-address-btn" id="addAddressBtn"><i class="fa fa-plus"></i> Add Address</a>
                        </div>
                    </div>
  
                    <button type="button" id="payButton" class="button -md -blue-1 bg-dark-3 text-white mt-20">Pay with Razorpay</button>
                   
                </form>
            </div>
            <div class="col-xl-5 col-lg-5">
                <div class="md:ml-0">
                    <div class="px-30 py-30 border-light rounded-4">
                        <div class="text-20 fw-500 mb-30">Your Subscription Details</div>
                        <div class="row x-gap-15 y-gap-20">
                            <div class="col-auto">
                                <!-- Display the product or pandit's photo -->
                                <img src="{{ asset('storage/'.$product->product_image ?? 'default-image.jpg') }}" alt="Subscription Image" class="size-140 rounded-4 object-cover">
                            </div>
                            <div class="col">
                                <div class="lh-17 fw-500">{{ $product->name }}</div>
                                {{-- <input type="hidden" class="form-control" name="product_id" value="{{ $product_id }}"> --}}
                                
                                <div class="text-16 lh-15 mt-5 fw-600">
                                    Total Fee: ₹{{ sprintf('%.2f', $product->price) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

  
<div id="addressModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('savefrontaddress') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="fullname" placeholder="Enter Your Full Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="number" placeholder="Enter Mobile number">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="country" class="form-control">
                                    <option value="India">India</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="state" class="form-control">
                                    <option value="Odisha">Odisha</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="city" placeholder="Enter Town/City *" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="pincode" placeholder="Enter Pincode *" required pattern="\d{6}" maxlength="6" title="Pincode should be exactly 6 digits">

                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea name="area" class="form-control" rows="5" placeholder="Enter Area, Street, Sector, Village *" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address_type">Address Type *</label>
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
                    <div class="d-inline-block pt-30">
                        <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script>
    // Get the modal
    var modal = document.getElementById("addressModal");

    // Get the button that opens the modal
    var btn = document.getElementById("addAddressBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementById("closeModal");

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<!-- jQuery UI library for datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- jQuery Timepicker plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>

<script>
   
        // Initialize datepicker
        $(document).ready(function() {
    // Initialize datepicker
    $("#date").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0,
        onSelect: function(dateText) {
            console.log("Date selected: " + dateText);
            // Update the time picker when a date is selected
            $("#time").timepicker('option', 'minTime', getMinTime());
        }
    });

});
</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('payButton').onclick = function(e) {
        e.preventDefault();
    
        console.log('Pay button clicked. Initializing Razorpay...');
        var amount = {{ ($product->price) * 100 }}; // Amount in paise
    
        var options = {
            "key": "{{ config('services.razorpay.key') }}",
            "amount": amount,
            "name": "33 Crores",
            "description": "",
            "image": "{{ asset('front-assets/img/brand/logo.png') }}",
            "handler": function(response) {
                console.log('Payment handler triggered.');
                console.log('Payment ID:', response.razorpay_payment_id);
    
                // Check if the form exists and add hidden input for payment ID
                var form = document.getElementById('bookingForm');
                if (form) {
                    console.log('Form found. Appending payment ID...');
                    form.appendChild(createHiddenInput('razorpay_payment_id', response.razorpay_payment_id));
                    console.log('Submitting form...');
                    form.submit();
                } else {
                    console.error('Form not found!');
                }
            },
            "prefill": {
                "name": "{{ $user->name }}",
                "contact": "{{ $user->phone_number }}"
            },
            "theme": {
                "color": "#F37254"
            }
        };
    
        var rzp1 = new Razorpay(options);
        console.log('Opening Razorpay checkout...');
        rzp1.open();
    };
    
    function createHiddenInput(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }
    </script>
@endsection
