<!DOCTYPE html>
<html>
<head>
    <title>Subscription Confirmation</title>
</head>
<body>
    <h1>Subscription Confirmation</h1>

    <h3>Order Information:</h3>
    <ul>
        <li><strong>Order ID:</strong> {{ $order->order_id }}</li>
        <li><strong>Product Name:</strong> {{ $order->flowerProduct->name ?? 'N/A' }}</li>
       
        {{-- <li><strong>Total Price:</strong> ₹{{ $order->total_price }}</li> --}}
        {{-- <li><strong>Status:</strong> ₹{{ $order->flowerPayments->payment_status }}</li> --}}

    </ul>

    <h3>Payment Information:</h3>
    <ul>
        @forelse ($order->flowerPayments as $payment)
            <li><strong>Payment ID:</strong> {{ $payment->payment_id }}</li>
            <li><strong>Status:</strong> {{ $payment->payment_status }}</li>
            <li><strong>Amount:</strong> ₹{{ $payment->paid_amount }}</li>
        @empty
            <li>No payment records available</li>
        @endforelse
    </ul>

    <h3>Subscription Information:</h3>
    <ul>
        <li><strong>Purchase Date:</strong> {{ $order->created_at ?? "NA" }}</li>
        <li><strong>Start Date:</strong> {{ $order->subscription->start_date }}</li>
        <li><strong>End Date:</strong> {{ $order->subscription->end_date }}</li>
    </ul>

    <h3>User Information:</h3>
    <ul>
        <li><strong>Name:</strong> {{ $order->user->name ?? "NA" }}</li>
        {{-- <li><strong>Email:</strong> {{ $order->user->email }}</li> --}}
        <li><strong>Phone:</strong> {{ $order->user->mobile_number }}</li>
    </ul>

    <h3>Address:</h3>
    <ul>
        <li><strong>Address:</strong> {{ $order->address->apartment_flat_plot ?? 'N/A' }}, {{ $order->address->localityDetails->locality_name ?? '' }}</li>
        <li><strong>City:</strong> {{ $order->address->city ?? 'N/A' }}</li>
        <li><strong>State:</strong> {{ $order->address->state ?? 'N/A' }}</li>
        <li><strong>Pin Code:</strong> {{ $order->address->pincode ?? 'N/A' }}</li>
    </ul>
</body>
</html>
