@extends('user.layouts.front-dashboard')

@section('styles')
<style>
 /* General Container */
.container {
    width: 85%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
}

/* Header Section */
.order-details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.order-details-header h1 {
    font-size: 30px;
    font-weight: 600;
}

.order-id span {
    font-size: 16px;
    color: #777;
}

/* Section Titles */
.section-title {
    margin-bottom: 20px;
}

.section-title h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #f1f1f1;
    padding-bottom: 10px;
}

/* Order Summary */
.order-summary {
    background-color: #fff;
    padding: 23px;
    border-radius: 9px;
    margin-bottom: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.order-summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.order-summary-label {
    font-weight: 500;
    color: #555;
}

.order-summary-value {
    font-weight: 600;
    color: #333;
}

/* Flower Product */
.flower-product {
  border-radius: 9px;
    margin-bottom: 30px;
    background: #fff !important;
    padding: 18px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.product-details {
    display: flex;
    gap: 20px;
}

.product-image img {
    max-width: 250px;
    border-radius: 8px;
}

.product-info {
    max-width: 650px;
}

.product-name {
    font-size: 22px;
    font-weight: 600;
}

.product-description {
    font-size: 16px;
    color: #777;
}

/* Payment History */
.payment-history {
   
    border-radius: 9px;
    margin-bottom: 30px;
    background: #fff !important;
    padding: 18px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.payment-item {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.payment-label {
    font-weight: 500;
    color: #555;
}

.payment-value {
    font-weight: 600;
    color: #333;
}

/* Address Details */
.address-details {
  border-radius: 9px;
    margin-bottom: 30px;
    background: #fff !important;
    padding: 18px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.address-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.address-label {
    font-weight: 500;
    color: #555;
}

.address-value {
    font-weight: 600;
    color: #333;
}

.badge-success {
    background-color: #008009 ; /* Green color for active */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}
.badge-warning {
    background-color: #c80100 ; /* Green color for active */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}
.highlighted-text.mt-2 {
    background-color: #ffb837;
    padding: 6px 10px;
    border-radius: 9px;
}
.text-strike {
    text-decoration: line-through;
    color: #a5a5a5; /* Optional: Grey color for struck-through text */
    margin-right: 10px;
}

.text-highlight {
    font-weight: bold;
    color: #2e7d32; /* Optional: Green for emphasis */
}

</style>
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content">
        <!-- Page Header -->
        <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
            <div class="col-auto">
                <h1 class="text-30 lh-14 fw-600">Requested Order Details</h1>
            </div>
        </div>

        <!-- Card Container -->
        <div class="order-summary">
            <div class="row y-gap-20">
                <!-- Left Section: Product Details -->
                <div class="col-lg-4 col-md-5">
                    <div class="product-image-wrapper">
                        <img src="{{ asset('storage/' . $requestedOrder->flowerProduct->product_image) }}" alt="Product Image" class="rounded-8 w-100">
                    </div>
                </div>

                <!-- Right Section: Order Summary -->
                <div class="col-lg-8 col-md-7">
                    <div class="details-header mb-20">
                        <h3 class="fw-600 text-20">{{ $requestedOrder->flowerProduct->name }}</h3>
                        <p class="fw-400 text-14">Request ID: {{ $requestedOrder->request_id }}</p>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items mb-20">
                        <h4 class="fw-500 text-16 mb-10">Flowers in Request</h4>
                        <ul class="list-group list-group-borderless">
                            @foreach ($requestedOrder->flowerRequestItems as $item)
                                <li class="list-group-item">
                                    <span class="fw-400 text-14">{{ $item->flower_name }}</span>
                                    <span class="fw-400 text-14">- {{ $item->flower_quantity }} {{ $item->flower_unit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Order Status -->
                    {{-- <div class="order-status mb-20">
                        <h4 class="fw-500 text-16 mb-10">Request Status</h4>
                        <span class="badge {{ $requestedOrder->status == 'pending' ? 'badge-warning' : ($requestedOrder->status == 'approved' ? 'badge-info' : 'badge-success') }}">
                            {{ ucfirst($requestedOrder->status) }}
                        </span>
                    </div> --}}

                    <!-- Actions -->
                    <div class="actions mt-20">
                        @if ($requestedOrder->status == 'pending')
                        <span class="badge badge-warning"><i class="fas fa-check-circle"></i> Price will be notify in few minutes</span>

                        @elseif ($requestedOrder->status == 'paid')
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="order-summary">
            <div class="row y-gap-20">
                <!-- Order Details -->
                @if($requestedOrder->order)
                <div class="col-lg-6">
                    <h4 class="fw-500 text-16 mb-20">Order Summary</h4>
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item d-flex justify-between">
                            <span class="fw-400 text-14">Order ID:</span>
                            <span class="fw-500 text-14">{{ $requestedOrder->order->order_id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-between">
                            <span class="fw-400 text-14">Total Price:</span>
                            <span class="fw-500 text-14">₹{{ $requestedOrder->order->total_price }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-between">
                            <span class="fw-400 text-14">Payment Status:</span>
                            <span class="badge {{ $requestedOrder->order->flowerPayments->isEmpty() ? 'badge-danger' : 'badge-success' }}">
                                {{ $requestedOrder->order->flowerPayments->isEmpty() ? 'Unpaid' : 'Paid' }}
                            </span>
                        </li>
                    </ul>
                </div>
                @endif
                

                <!-- Delivery Details -->
                <div class="col-lg-6">
                    <h4 class="fw-500 text-16 mb-20">Delivery Details</h4>
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item d-flex justify-between">
                            <span class="fw-400 text-14">Recipient:</span>
                            <span class="fw-500 text-14">
                                {{ $requestedOrder->user->name ?? $requestedOrder->user->mobile_number }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-between">
                            <span class="fw-400 text-14">Address:</span>
                            <span class="fw-500 text-14">

                                <strong>Address:</strong> {{ $requestedOrder->address->apartment_flat_plot ?? "" }}, {{ $requestedOrder->address->locality ?? "" }}<br>
                                <strong>Landmark:</strong> {{ $requestedOrder->address->landmark ?? "" }}<br>

                                <strong>City:</strong> {{ $requestedOrder->address->city ?? ""}}<br>
                                <strong>State:</strong> {{ $requestedOrder->address->state ?? ""}}<br>
                                <strong>Pin Code:</strong> {{ $requestedOrder->address->pincode ?? "" }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection
