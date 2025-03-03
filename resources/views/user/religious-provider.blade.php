@extends('user.layouts.front')

@section('styles')
    <style>
        .service-provider-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }
        .service-provider-wrapper {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
        }
        .service-title {
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
        .service-title i {
            font-size: 32px;
            color: #ff6600;
        }
        .service-list {
            list-style: none;
            padding: 0;
        }
        .service-list li {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .service-list li i {
            font-size: 24px;
            color: #ff6600;
            min-width: 30px;
        }
        .service-list li:last-child {
            border-bottom: none;
        }
        .service-list li strong {
            display: block;
            font-size: 18px;
            color: #444;
            margin-bottom: 5px;
        }
        .service-list li p {
            margin: 0;
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }
        .service-contact {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
        .service-contact a {
            color: #ff6600;
            text-decoration: none;
        }
        .service-contact a:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper text-center">
                <h1 class="sc-7kepeu-0 kYnyFA description">Religious Service Provider</h1>
                <p class="mt-3">At 33 Crores, we honor and support religious service providers who guide devotees in their spiritual journey. We invite qualified priests, astrologers, and spiritual guides to collaborate with us.</p>
            </div>
        </div>
    </div>
</section>

<section class="service-provider-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="service-provider-wrapper">

                    <h2 class="service-title">
                        <i class="fas fa-praying-hands"></i> Partner With 33 Crores
                    </h2>

                    <ul class="service-list">
                        <li>
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Who Can Join?</strong>
                                <p>✔️ Pandits, priests, and religious scholars<br>
                                   ✔️ Astrologers offering Vedic insights<br>
                                   ✔️ Gurus and spiritual counselors</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-hand-holding-heart"></i>
                            <div>
                                <strong>Services We Support</strong>
                                <p>🌟 Online and offline puja services<br>
                                   🌟 Horoscope reading and astrological guidance<br>
                                   🌟 Ritual consultations for special occasions</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-file-signature"></i>
                            <div>
                                <strong>How to Enroll</strong>
                                <p>📝 Submit your credentials and service offerings through our <a href="#" style="color: #ff6600; text-decoration: underline;">Online Registration Form</a>.<br>
                                   ✔️ Our team will review and verify your details.<br>
                                   📞 Upon approval, you can offer services through our platform.</p>
                            </div>
                        </li>
                    </ul>

                    <p class="service-contact">
                        For partnership inquiries, contact us at <a href="mailto:services@33crores.com">services@33crores.com</a>.
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
