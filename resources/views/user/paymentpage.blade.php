@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')
<div class="dashboard__main">
    <div class="dashboard__content" style="margin-top: 40px;">
        <div class="container">
            <h2>Payment for Booking: {{ $booking->booking_id }}</h2>
            <p>Pandit: {{ $booking->pandit->name }}</p>
            <p>Pooja: {{ $booking->pooja->pooja_name }}</p>
            <p>Total Fee: ₹{{ $booking->pooja_fee }}</p>
            <p>Advance Fee: ₹{{ $booking->advance_fee }}</p>
            
            <h3>Choose a payment option:</h3>
            <form id="payment-form" action="{{ route('payment.process', ['booking_id' => $booking->id]) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 15px;">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_option" id="full_payment" value="full" required>
                        <label class="form-check-label" for="full_payment">
                            Pay Full Amount in Advance (5% discount): ₹{{ sprintf('%.2f',$booking->pooja_fee * 0.95) }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_option" id="partial_payment" value="advance">
                        <label class="form-check-label" for="partial_payment">
                            Pay Partial Amount (Advance Fee): ₹{{ $booking->advance_fee }}
                        </label>
                    </div>
                </div>
                <input type="hidden" name="payment_type" id="payment_type">
                <input type="hidden" name="payment_method" value="razorpay">
                <button type="button" id="pay-now" class="btn btn-primary">Pay Now</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Razorpay Checkout script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('pay-now').onclick = function(e){
    e.preventDefault();

    var paymentOption = document.querySelector('input[name="payment_option"]:checked').value;
    var amount;
    if (paymentOption === 'full') {
        amount = {{ $booking->pooja_fee * 0.95 * 100 }}; // Amount in paise
        document.getElementById('payment_type').value = 'full';
    } else {
        amount = {{ $booking->advance_fee * 100 }}; // Amount in paise
        document.getElementById('payment_type').value = 'advance';
    }

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
