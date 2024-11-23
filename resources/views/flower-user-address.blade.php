<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Flowers for Your Pooja</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Page Background */
        .page-single {
            background-image: url('{{ asset('images/flowerbg.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 40px;
            position: relative;
            min-height: 100vh;
        }

        /* Glassmorphism Card */
        .glassmorphism-card {
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent background */
            border-radius: 15px;
            backdrop-filter: blur(10px); /* Blur effect */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
            position: relative;
        }

        /* Form Header */
        .card-header {
            background: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            padding: 20px;
        }

        .card-header h3 {
    color: #b90b0b;
    font-weight: 600;
    text-transform: uppercase;
    /* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); */
}

        /* Input Fields with Animation */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control.single-line-input {
            background: rgba(255, 255, 255, 0.6); /* Glass effect for input */
            border: none;
            border-radius: 8px;
            padding: 10px 35px 10px 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            color: #000;
            font-size: 1rem;
            position: relative;
        }

        .form-control.single-line-input:focus {
            outline: none;
            box-shadow: 0 4px 10px rgba(229, 140, 133, 0.4);
            border: 1px solid #B90B0B;
        }

        /* Placeholder Text Color */
        .form-control::placeholder {
            color: #000; /* Black placeholder text */
            opacity: 1; /* Ensure visibility */
        }

        /* Animated Placeholder */
        .form-group label {
            position: absolute;
            top: 50%;
            left: 15px;
            color: #000;
            font-size: 1rem;
            font-weight: 600;
            transform: translateY(-50%);
            pointer-events: none;
            transition: 0.3s;
        }

        .form-control:focus + label,
        .form-control:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 0.85rem;
            color: #B90B0B;
        }

        /* Input Icon */
        .input-icon-container {
            position: relative;
        }

        .input-icon-container .icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            color: #B90B0B;
            font-size: 1.2rem;
        }

        /* Button */
        .btn-primary {
            background-color: #B90B0B;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 10px 0;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #a00a0a;
        }
        .form-control::placeholder { /* For most modern browsers */
    color: #000 !important; /* Black placeholder text */
    opacity: 1;
}
.form-control::-webkit-input-placeholder { /* For Chrome, Safari, Edge */
    color: #000 !important;
}
.form-control::-moz-placeholder { /* For Firefox 19+ */
    color: #000 !important;
}
.form-control:-ms-input-placeholder { /* For IE 10+ */
    color: #000 !important;
}
.form-control:-moz-placeholder { /* For older Firefox */
    color: #000 !important;
}

        /* Responsive */
        @media (max-width: 768px) {
            .glassmorphism-card {
                padding: 15px;
            }
            .page-single{
                padding:40px 9px 40px 9px;
            }
        }
    </style>
</head>
<body>
    <div class="page-single">
        <div class="container">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="card border-0 shadow-lg glassmorphism-card">
                            <div class="card-header text-center">
                                <h3 class="mt-2" style="text-shadow: 2px 2px 11px rgb(255 255 255 / 90%);">Fresh Flowers For Pooja</h3>
                            </div>
                            

                            <div class="card-body">
                                <!-- Errors -->
                                <div class="alert alert-danger d-none" id="error-container">
                                    <ul id="error-list"></ul>
                                </div>

                                <form action="" method="POST">
                                    <!-- Name Field -->
                                    <div class="form-group input-container">
                                        <div class="input-icon-container">
                                            <input type="text" name="name" class="form-control single-line-input" placeholder=" " required>
                                            <label for="name">Name</label>
                                            <i class="fas fa-user icon"></i>
                                        </div>
                                    </div>

                                    <!-- Mobile Number Field -->
                                    <div class="form-group input-container">
                                        <div class="input-icon-container">
                                            <input type="tel" name="number" class="form-control single-line-input" placeholder=" " required>
                                            <label for="number">Mobile Number</label>
                                            <i class="fas fa-phone-alt icon"></i>
                                        </div>
                                    </div>

                                    <!-- OTP Field -->
                                    <div class="form-group input-container">
                                        <div class="input-icon-container">
                                            <input type="text" name="otp" class="form-control single-line-input" placeholder=" " required>
                                            <label for="otp">Enter OTP</label>
                                            <i class="fas fa-key icon"></i>
                                        </div>
                                    </div>

                                    <!-- Apartment / Flat No. Field -->
                                    <div class="form-group input-container">
                                        <div class="input-icon-container">
                                            <input type="text" name="apartment" class="form-control single-line-input" placeholder=" " required>
                                            <label for="apartment">Apartment/Flat Number</label>
                                            <i class="fas fa-building icon"></i>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
