@extends('user.layouts.front')

@section('styles')
    <style>
        .policy-section {
            background-color: #f8f9fa;
            padding: 40px 0;
        }
        .policy-wrapper {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
        }
        .policy-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .policy-title i {
            font-size: 32px;
            color: #ff6600;
        }
        .policy-list {
            list-style: none;
            padding: 0;
        }
        .policy-list li {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .policy-list li i {
            font-size: 24px;
            color: #ff6600;
            min-width: 30px;
        }
        .policy-list li:last-child {
            border-bottom: none;
        }
        .policy-list li strong {
            display: block;
            font-size: 18px;
            color: #444;
            margin-bottom: 5px;
        }
        .policy-list li p {
            margin: 0;
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }
        .policy-contact {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
        .policy-contact a {
            color: #ff6600;
            text-decoration: none;
        }
        .policy-contact a:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <h1 class="sc-7kepeu-0 kYnyFA description text-center">Cancellation & Returns</h1>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="policy-wrapper">

                    <h2 class="policy-title">
                        <i class="fas fa-undo-alt"></i> Cancellation & Returns Policy
                    </h2>

                    <ul class="policy-list">
                        <li>
                            <i class="fas fa-ban"></i>
                            <div>
                                <strong>Order Cancellation</strong>
                                <p>Orders can be canceled within 24 hours of placement. Once dispatched, cancellation requests cannot be accepted. Refunds for canceled orders will be processed within 5-7 business days.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-box-open"></i>
                            <div>
                                <strong>Return Eligibility</strong>
                                <p>Products must be returned within 7 days of delivery. Items should be unused, in original packaging, and accompanied by proof of purchase. Perishable items like fresh flowers and food offerings are non-returnable.</p>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-wallet"></i>
                            <div>
                                <strong>Refund Process</strong>
                                <p>Refunds are processed after a quality check. Refunds will be credited to the original payment method within 7-10 business days. Shipping charges are non-refundable.</p>
                            </div>
                        </li>
                    </ul>

                    <p class="policy-contact">
                        For returns or queries, contact us at <a href="mailto:support@33crores.com">support@33crores.com</a>.
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
