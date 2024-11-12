@extends('layouts.custom-app')

    @section('styles')
    <title>Flower Registration</title>
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>
    
    <!-- Firebase Messaging -->
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>
    <style>
/* General Styling for Labels */
.form-check-label {
    font-size: 16px;
    font-weight: 500;
    color: #333;
    padding-left: 35px; /* Space for custom radio */
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Container for Custom Radio Buttons */
.custom-radio-button {
    display: flex;
    align-items: center;
    position: relative;
    margin-bottom: 15px;
}

/* Hide the default radio button */
.custom-radio-button input[type="radio"] {
    opacity: 0;
    position: absolute;
}

/* Custom Styled Radio Button */
.custom-radio {
    width: 20px;
    height: 20px;
    border: 2px solid #ddd;
    border-radius: 50%;
    background-color: #fff;
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Hover Effect for Custom Radio */
.custom-radio-button:hover .custom-radio {
    border-color: #3f51b5; /* Swiggy or Flipkart Blue */
}

/* When Radio is Checked */
.custom-radio-button input[type="radio"]:checked + .form-check-label .custom-radio {
    background-color: #3f51b5;
    border-color: #3f51b5;
}

.custom-radio-button input[type="radio"]:checked + .form-check-label .custom-radio::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background-color: #fff;
    border-radius: 50%;
}

/* Smooth Focus Effect */
.custom-radio-button input[type="radio"]:focus + .form-check-label .custom-radio {
    box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.4); /* Light blue glow on focus */
}

/* Smooth Transition for Label */
.custom-radio-button:hover .form-check-label {
    color: #3f51b5; /* Text color change on hover */
}

/* Active Effect for Button (Clicked) */
.custom-radio-button input[type="radio"]:active + .form-check-label .custom-radio {
    transform: scale(1.1); /* Slightly scale up the radio button when clicked */
}
.square-box {
    display: none;
}
.page-single {
    background-image: url('{{ asset('images/i_Stock_1436339978_min_3dc7188b09.jpg') }}'); /* Replace with your image path */
    background-size: cover;  /* Ensure the image covers the whole container */
    background-position: center center; /* Center the image */
    background-repeat: no-repeat;  /* Prevent repeating the image */
    padding: 40px;  /* Optional, for padding around the form */
    border-radius: 10px;  /* Optional, to make the container edges rounded */
}

.page-single:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);  /* Semi-transparent overlay */
    border-radius: 10px;  /* Match container's border radius */
    z-index: -1;  /* Place overlay behind the form */
}
    </style>
    @endsection

    @section('class')

	    <div class="bg-primary">

    @endsection

    @section('content')
        <div class="page-single">
            <div class="container">
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-white text-white text-center">
                                    <img src="{{ asset('assets/img/brand/logo.png') }}" class="" alt="logo" style="height: 50px;">

                                 
                                </div>
                                <div class="card-body p-4">
                                    {{-- @if (session('message'))
                                        <div class="alert alert-success">{{ session('message') }}</div>
                                    @elseif (session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif --}}
                
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                
                                    <form action="{{route('saveaddress')}}" method="POST">
                                        @csrf
                                        <!-- Place Category -->
                                        <div class="form-group mb-4">
                                            <label class="form-label">Type</label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check custom-radio-button">
                                                    <input type="radio" class="form-check-input" id="individual" name="place_category" value="Indivisual" required>
                                                    <label class="form-check-label" for="individual">
                                                        <span class="custom-radio"></span>
                                                        Individual
                                                    </label>
                                                </div>
                                                <div class="form-check custom-radio-button">
                                                    <input type="radio" class="form-check-input" id="apartment" name="place_category" value="Apartment">
                                                    <label class="form-check-label" for="apartment">
                                                        <span class="custom-radio"></span>
                                                        Apartment
                                                    </label>
                                                </div>
                                                <div class="form-check custom-radio-button">
                                                    <input type="radio" class="form-check-input" id="business" name="place_category" value="Business">
                                                    <label class="form-check-label" for="business">
                                                        <span class="custom-radio"></span>
                                                        Business
                                                    </label>
                                                </div>
                                                <div class="form-check custom-radio-button">
                                                    <input type="radio" class="form-check-input" id="temple" name="place_category" value="Temple">
                                                    <label class="form-check-label" for="temple">
                                                        <span class="custom-radio"></span>
                                                        Temple
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <!-- Grouped Address Fields -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="apartment_flat_plot" class="form-label">Apartment/Flat/Plot</label>
                                                <input type="text" class="form-control" id="apartment_flat_plot" name="apartment_flat_plot" placeholder="Enter details" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="landmark" class="form-label">Landmark</label>
                                                <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Enter landmark" required>
                                            </div>
                                        </div>
                
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="locality" class="form-label">Locality</label>
                                                <input type="text" class="form-control" id="locality" name="locality" placeholder="Enter locality" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="pincode" class="form-label">Pincode</label>
                                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter pincode" required pattern="\d{6}">
                                            </div>
                                        </div>
                
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="state" class="form-label">State</label>
                                                <input type="text" class="form-control" id="state" name="state" placeholder="Enter state" required>
                                            </div>
                                        </div>
                
                                        <button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </div>
        </div>
    @endsection

    @section('scripts')

		<!-- generate-otp js -->
		<script src="{{asset('assets/js/generate-otp.js')}}"></script>
        <script src="{{asset('assets/js/pandit-career.js')}}"></script>

  


@if(isset($successMessage))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ $successMessage }}',
        showConfirmButton: true,
        timer: 3000
    }).then(() => {
        // Redirect to the Play Store after the success message
        window.location.href = '{{ $playStoreLink }}';
    });
</script>
@endif

    @endsection
