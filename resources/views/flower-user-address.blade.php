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
    height: 100vh;
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
                
                                    <form action="" method="POST">
                                        @csrf
                                        <!-- Place Category -->
                                        <div class="form-group mb-4">
                                            <label class="form-label">Name</label>
                                           
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
                timer: 5000  // This will automatically close the alert after 3 seconds (optional)
            }).then((result) => {
                // Check if the user clicked the "OK" button (or if it timed out)
                if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                    // Redirect to the Play Store link after the user clicks "OK"
                    window.location.href = '{{ $playStoreLink }}';
                }
            });
        </script>
        @endif
        

    @endsection
