@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')
<div class="dashboard__main">
    <div class="dashboard__content" style="margin-top: 40px;">
        <div class="container">
            <h2>Pay Remaining Amount for Booking: {{ $booking->booking_id }}</h2>
            <p>Pandit: {{ $booking->pandit->name }}</p>
            <p>Pooja: {{ $booking->pooja->pooja_name }}</p>
            <p>Total Fee: ₹{{ $booking->pooja_fee }}</p>
            <p>Advance Paid: ₹{{ $booking->advance_fee }}</p>
            <p>Remaining Fee: ₹{{ $booking->pooja_fee - $booking->advance_fee }}</p>
            
            <h3>Proceed with the remaining payment</h3>
            <form id="payment-form" action="{{ route('payment.processRemainingPayment', ['booking_id' => $booking->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_type" value="remaining">
                <input type="hidden" name="payment_method" value="razorpay">
                <button type="button" id="pay-now" class="btn btn-primary">Pay Now</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('pay-now').onclick = function(e){
    e.preventDefault();

    var amount = {{ ($booking->pooja_fee - $booking->advance_fee) * 100 }}; // Remaining amount in paise

    var options = {
        "key": "{{ config('services.razorpay.key') }}",
        "amount": amount,
        "name": "33 Pandits",
        "description": "Payment for Booking ID: {{ $booking->booking_id }}",
        "image": "{{ asset('front-assets/img/brand/logo.png') }}",
        "handler": function (response){
            // Add the response to the form and submit it
            var form = document.getElementById('payment-form');
            form.appendChild(createHiddenInput('razorpay_payment_id', response.razorpay_payment_id));
            form.submit();
        },
        "prefill": {
            "name": "{{ $booking->address->fullname }}",
            "contact": "{{ $booking->address->number }}"
        },
        "theme": {
            "color": "#F37254"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
}

function createHiddenInput(name, value) {
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}
</script>

@endsection
