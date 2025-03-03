@extends('user.layouts.front')

@section('styles')
<style>
    .contact-us-section {
        background-color: #f9f9f9;
        padding: 60px 0;
    }
    .contact-item {
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .contact-item i {
        font-size: 30px;
        color: #f57c00;
        margin-bottom: 15px;
    }
    .contact-item:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }
    .contact-header {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
    }
    .contact-icon {
        font-size: 28px;
        color: #f57c00;
        margin-right: 10px;
    }
    .social-icons a {
        font-size: 18px;
        color: #555;
        transition: color 0.3s ease;
        margin-right: 10px;
    }
    .social-icons a:hover {
        color: #f57c00;
    }
</style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper text-center">
                <h1 class="sc-7kepeu-0 kYnyFA description">Contact Us</h1>
            </div>
        </div>
    </div>
</section>

<section class="contact-us-section">
    <div class="container">
        <div class="contact-header">Get in Touch with Us</div>

        <div class="row gy-4 justify-content-center">

            <div class="col-lg-4 col-md-6">
                <div class="contact-item">
                    <i class="fa fa-map-marker-alt"></i>
                    <h5 class="fw-600">Address</h5>
                    <p>33Crores Pooja Products Pvt Ltd<br>
                        403, 4th Floor, O-Hub IDCO Sez Infocity,<br>
                        Bhubaneswar 751024, Odisha, Bharat
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="contact-item">
                    <i class="fa fa-phone-alt"></i>
                    <h5 class="fw-600">Toll Free Customer Care</h5>
                    <p>(91)-9776-88888-7</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="contact-item">
                    <i class="fa fa-envelope"></i>
                    <h5 class="fw-600">Email Us</h5>
                    <p>contact@33crores.com</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="contact-item">
                    <i class="fa fa-share-alt"></i>
                    <h5 class="fw-600">Follow Us</h5>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/33crores" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/33crores" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/33crores" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<!-- Make sure you include FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
