@extends('user.layouts.front')

@section('styles')
    <style>
        .enrollment-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }
        .enrollment-wrapper {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
        }
        .enrollment-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .enrollment-title i {
            font-size: 32px;
            color: #ff6600;
        }
        .enrollment-list {
            list-style: none;
            padding: 0;
        }
        .enrollment-list li {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .enrollment-list li i {
            font-size: 24px;
            color: #ff6600;
            min-width: 30px;
        }
        .enrollment-list li:last-child {
            border-bottom: none;
        }
        .enrollment-list li strong {
            display: block;
            font-size: 18px;
            color: #444;
            margin-bottom: 5px;
        }
        .enrollment-list li p {
            margin: 0;
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }
        .enrollment-contact {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
        .enrollment-contact a {
            color: #ff6600;
            text-decoration: none;
        }
        .enrollment-contact a:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper text-center">
                <h1 class="sc-7kepeu-0 kYnyFA description">Business Enrollment</h1>
                <p class="mt-3">33 Crores invites businesses to collaborate with us in delivering premium puja essentials to devotees worldwide. Partner with us and expand your reach in the spiritual market.</p>
            </div>
        </div>
    </div>
</section>

<section class="enrollment-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="enrollment-wrapper">

                    <h2 class="enrollment-title">
                        <i class="fas fa-handshake"></i> Partner With 33 Crores
                    </h2>

                    <ul class="enrollment-list">
                        <li>
                            <i class="fas fa-store"></i>
                            <div>
                                <strong>Who Can Enroll?</strong>
                                <p>✔️ Manufacturers of puja essentials<br>
                                   ✔️ Retailers and wholesalers of spiritual products<br>
                                   ✔️ Artisans creating handcrafted religious items</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-bullhorn"></i>
                            <div>
                                <strong>Benefits of Partnering with Us</strong>
                                <p>🌟 Increased visibility among a spiritually inclined audience<br>
                                   🌟 Access to an established e-commerce platform<br>
                                   🌟 Seamless logistics and order fulfillment support</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <strong>How to Enroll</strong>
                                <p>📝 Fill out the <a href="#" style="color: #ff6600; text-decoration: underline;">Business Enrollment Form</a> on our website.<br>
                                   📦 Provide product details and business credentials.<br>
                                   ✔️ Our team will review and approve eligible applications.</p>
                            </div>
                        </li>
                    </ul>

                    <p class="enrollment-contact">
                        For inquiries, contact us at <a href="mailto:partners@33crores.com">partners@33crores.com</a>.
                    </p>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
