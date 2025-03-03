@extends('user.layouts.front')

@section('styles')
    <style>
        .policy-section {
            background-color: #f9f9f9;
            padding: 60px 0;
        }
        .policy-title {
            font-size: 36px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .policy-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .policy-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .policy-item i {
            font-size: 24px;
            color: #f57c00;
            margin-top: 5px;
        }
        .policy-content {
            flex: 1;
        }
        .policy-content h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .policy-content p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin: 0;
        }
    </style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper text-center">
                <h1 class="sc-7kepeu-0 kYnyFA description">Privacy & Data Policy</h1>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="policy-title">Our Commitment to Your Privacy</div>

        <div class="policy-item">
            <i class="fas fa-user-shield"></i>
            <div class="policy-content">
                <h5>1. Information We Collect</h5>
                <p>We collect personal information such as your name, email address, phone number, and shipping details when you place an order or sign up for our services. We also gather non-personal data like browser type, IP address, and device information to enhance your experience.</p>
            </div>
        </div>

        <div class="policy-item">
            <i class="fas fa-tasks"></i>
            <div class="policy-content">
                <h5>2. How We Use Your Data</h5>
                <p>
                    - To process your orders and ensure timely delivery.<br>
                    - To send updates on new products, offers, and spiritual content.<br>
                    - To improve our website experience and customer support.<br>
                    - To comply with legal requirements and prevent fraud.
                </p>
            </div>
        </div>

        <div class="policy-item">
            <i class="fas fa-lock"></i>
            <div class="policy-content">
                <h5>3. Data Protection Measures</h5>
                <p>We implement industry-standard security measures to protect your data from unauthorized access, loss, or misuse. Your payment details are encrypted and processed through secure gateways.</p>
            </div>
        </div>

        <div class="policy-item">
            <i class="fas fa-share-alt"></i>
            <div class="policy-content">
                <h5>4. Sharing of Information</h5>
                <p>We do not sell or trade your personal information. However, we may share it with trusted third-party partners for logistics, payment processing, and customer support.</p>
            </div>
        </div>

        <div class="policy-item">
            <i class="fas fa-user-cog"></i>
            <div class="policy-content">
                <h5>5. Your Rights</h5>
                <p>You have the right to request access, modification, or deletion of your personal data. If you wish to opt out of promotional communications, you can do so anytime.</p>
            </div>
        </div>

        <div class="policy-item">
            <i class="fas fa-history"></i>
            <div class="policy-content">
                <h5>6. Updates to Privacy Policy</h5>
                <p>We may update this policy periodically. Any changes will be communicated through our website and email notifications.</p>
            </div>
        </div>

        <div class="policy-item">
            <i class="fas fa-envelope"></i>
            <div class="policy-content">
                <h5>For Queries</h5>
                <p>If you have any questions or concerns about your privacy, contact us at <a href="mailto:support@33crores.com">support@33crores.com</a>.</p>
            </div>
        </div>

    </div>
</section>

@endsection

@section('scripts')
<!-- FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
