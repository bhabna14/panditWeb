@extends('user.layouts.front')

@section('styles')
    <style>
        .terms-section {
            background-color: #f9f9f9;
            padding: 40px 0;
        }
        .terms-wrapper {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        .terms-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #444;
        }
        .terms-title i {
            font-size: 32px;
            color: #ff6600;
        }
        .terms-list {
            list-style-type: none;
            padding-left: 0;
        }
        .terms-list li {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        .terms-list li i {
            font-size: 22px;
            color: #ff6600;
            min-width: 24px;
        }
        .terms-list li:last-child {
            border-bottom: none;
        }
        .terms-contact {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <h1 class="sc-7kepeu-0 kYnyFA description text-center">Terms & Conditions</h1>
            </div>
        </div>
    </div>
</section>

<section class="terms-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="terms-wrapper">

                    <h2 class="terms-title">
                        <i class="fas fa-file-contract"></i> Terms & Conditions
                    </h2>

                    <ul class="terms-list">
                        <li>
                            <i class="fas fa-globe"></i>
                            <div>
                                <strong>1. Use of Website</strong>
                                <p>You must be at least 18 years old to make a purchase. You agree to provide accurate and complete information when registering or placing an order.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-box"></i>
                            <div>
                                <strong>2. Product Information</strong>
                                <p>We strive to provide accurate product descriptions, but variations in color, size, and texture may occur. 33 Crores is not responsible for minor discrepancies.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-rupee-sign"></i>
                            <div>
                                <strong>3. Pricing & Payments</strong>
                                <p>Prices listed are inclusive of applicable taxes. We accept payments via secure gateways. Your financial details are protected.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-shipping-fast"></i>
                            <div>
                                <strong>4. Shipping & Delivery</strong>
                                <p>Orders are processed within 1-3 business days. Delivery timelines vary based on location. Delays due to unforeseen circumstances are not the responsibility of 33 Crores.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-undo-alt"></i>
                            <div>
                                <strong>5. Returns & Refunds</strong>
                                <p>Refer to our <a href="{{ url('/cancellation-returns-policy') }}">Cancellation & Returns Policy</a> for details on refunds and exchanges.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-copyright"></i>
                            <div>
                                <strong>6. Intellectual Property</strong>
                                <p>All content, including images, text, and designs, is the property of 33 Crores. Unauthorized use or reproduction is prohibited.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>7. Limitation of Liability</strong>
                                <p>33 Crores is not responsible for any indirect damages resulting from the use of our products.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-sync-alt"></i>
                            <div>
                                <strong>8. Changes to Terms</strong>
                                <p>We may update our Terms & Conditions at any time. Continued use of our website implies acceptance of revised terms.</p>
                            </div>
                        </li>
                    </ul>

                    <p class="terms-contact">
                        For inquiries, contact us at <a href="mailto:support@33crores.com">support@33crores.com</a>.
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
