<!-- resources/views/otp-login.blade.php -->
@if (session('otp_sent'))
<form action="/verify-otp" method="POST">
    @csrf
    <input type="text" name="order_id" value="{{ session('otp_order_id') }}" required>
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <input type="text" name="phone" value="{{ session('otp_phone') }}" placeholder="Enter your phone number" required>
    <button type="submit">Verify OTP</button>
</form>
@else
    <form action="/send-otp" method="POST">
        @csrf
        <input type="text" name="phone" placeholder="Enter your phone number" required>
        <button type="submit">Send OTP</button>
    </form>
@endif

@if (session('message'))
    <p>{{ session('message') }}</p>
@endif
