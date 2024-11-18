@extends('layouts.custom-app')

    @section('styles')
    <title>Flower Registration</title>
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>
    
    <!-- Firebase Messaging -->
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>
              <!-- Additional CSS for custom styling -->
              <style>
             
                .page-single {
    background-image: url('{{ asset('images/flowerbg.jpeg') }}'); /* Replace with your image path */
    background-size: cover;  /* Ensure the image covers the whole container */
    background-position: center center; /* Center the image */
    background-repeat: no-repeat;  /* Prevent repeating the image */
    padding: 40px;  /* Optional, for padding around the form */
    border-radius: 10px;  /* Optional, to make the container edges rounded */
    /* height: 100vh; */
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
                    <div class="row justify-content-center" >
                        <div class="col-lg-8 col-md-10">
                            <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden; background: #ffffff; position: relative;">
                                <!-- Decorative flower images for corners -->
                                {{-- <img src="{{asset('images/f1.png')}}" alt="Flower" class="corner-flower" style="
    position: absolute;
    top: -8px;
    left: -15px;
    height: 80px;
    z-index: 1;
    opacity: 0.7;">
                                <img src="{{asset('images/f2.png')}}" alt="Flower" class="corner-flower" style="position: absolute; top: -20px; right: -20px; height: 80px; z-index: 1; opacity: 0.7;">
                                <img src="{{asset('images/f3.png')}}" alt="Flower" class="corner-flower" style="position: absolute; bottom: -20px; left: -20px; height: 80px; z-index: 1; opacity: 0.7;">
                                <img src="{{asset('images/f4.png')}}" alt="Flower" class="corner-flower" style="position: absolute; bottom: -20px; right: -20px; height: 80px; z-index: 1; opacity: 0.7;">
                     --}}
                                <div class="card-header text-center" style="background-color: #fff; padding: 20px; border-bottom: 1px solid #000;">
                                    {{-- <img src="{{ asset('assets/img/brand/logo.png') }}" alt="logo" style="height: 50px; margin-bottom: 10px;"> --}}
                                    <h3 class="mt-2" style="color: #000; font-weight: 600; ">Get Flower For Your Pooja</h3>
                                </div>
                    
                                <div class="card-body" style="padding-top: 2rem; padding-right: 3rem; padding-bottom: 3rem; padding-left: 3rem;background: #ffffff;">
                                    @if ($errors->any())
                                        <div class="alert alert-danger" style="font-size: 0.9rem; margin-bottom: 20px;">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                    
                                    <form action="" method="POST">
                                        @csrf
                                         <!-- Name Field -->
                                      <!-- Input Field Template -->
                                        <div class="form-group mb-3 input-container">
                                            <div class="input-icon-container">
                                                <i class="fas fa-user icon"></i>
                                                <input type="text" name="name" class="form-control single-line-input animated-input" placeholder="Name" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 input-container">
                                            <div class="input-icon-container">
                                                <i class="fas fa-phone-alt icon"></i>
                                                <input type="tel" name="number" class="form-control single-line-input animated-input" placeholder="Mobile Number" required>
                                            </div>
                                        </div>

                    
                                      <!-- OTP Field -->
                                        <div class="form-group mb-4">
                                            <div class="input-icon-container">
                                                <i class="fas fa-key icon"></i>
                                                <input type="text" name="otp" class="form-control single-line-input animated-input" placeholder="Enter OTP" required>
                                            </div>
                                        </div>

                                        <!-- Apartment / Flat No. Field -->
                                        <div class="form-group mb-4">
                                            <div class="input-icon-container">
                                                <i class="fas fa-building icon"></i>
                                                <input type="text" name="apartment" class="form-control single-line-input animated-input" placeholder="Enter your apartment or flat number" required>
                                            </div>
                                        </div>

                    
                                        <button type="submit" class="btn btn-primary w-100 mt-3" style="background-color: #f28d89; border: none; border-radius: 8px; font-weight: 600; font-size: 1.1rem; padding: 10px 0;">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional CSS -->
                    <style>
                        .form-control.single-line-input {
        border: none;
        border-bottom: 2px solid #e9b1b1;
        border-radius: 0;
        padding-left: 35px;
        box-shadow: none;
        transition: border-bottom 0.3s;
    }

    .form-control.single-line-input::placeholder {
        color: #aaa;
        transition: transform 0.3s, opacity 0.3s;
        position: absolute;
        top: 50%;
        left: 35px;
        transform: translateY(-50%);
    }
    .form-control.single-line-input:focus::placeholder {
    transform: translateY(-1.5em);
    opacity: 0;
}
    .input-icon-container {
        position: relative;
    }
    .input-icon-container .icon {
        position: absolute;
        top: 50%;
        left: 5px;
        transform: translateY(-50%);
        color: #e58c85;
        font-size: 1rem;
    }
    .input-container {
        position: relative;
    }
     /* Keyframe animation for glowing border */
     @keyframes glowEffect {
        0% {
            box-shadow: 0px 0px 0px #e58c85;
        }
        100% {
            box-shadow: 0px 4px 10px rgba(229, 140, 133, 0.4);
        }
    }


                        .form-control {
                            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
                            transition: all 0.3s;
                        }
                    
                   
                    
                        button.btn-primary {
                            transition: background-color 0.3s;
                        }
                    
                        button.btn-primary:hover {
                            background-color: #e5726a;
                        }
                    
                        .corner-flower {
                            animation: floatAnimation 8s ease-in-out infinite;
                        }
                    
                        @keyframes floatAnimation {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(-5px); }
                        }
                    </style>
                    
                    
                    <!-- Additional CSS for styling and animations -->
            
                    
                    
          
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
