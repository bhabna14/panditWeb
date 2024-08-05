@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')
<div class="dashboard__main">
    <div class="dashboard__content" style="margin-top: 40px;">
        <div class="container">
            <h2>Pay Remaining Amount</h2>
            <form action="{{ route('processRemainingPayment', $booking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <button id="pay-button" type="button">Pay ₹{{ $remainingAmount }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('pay-button').onclick = function(e) {
        var options = {
            "key": "{{ config('services.razorpay.key') }}",
            "amount": "{{ $remainingAmount * 100 }}", // Amount in paise
            "currency": "INR",
            "name": "33 Pandits",
            "description": "Payment for remaining amount",
            "handler": function (response){
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.forms[0].submit();
            },
            "prefill": {
                "contact": "{{ Auth::guard('users')->user()->mobile_number }}",
            },
            "theme": {
                "color": "#F37254"
            }
        };
        var rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    }
</script>
@endsection
