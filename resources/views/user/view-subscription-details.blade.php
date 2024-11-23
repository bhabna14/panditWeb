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
    <div class="row y-gap-20 justify-center items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <h1 class="text-30 lh-14 fw-600">Booking Details</h1>
      </div>
      <div class="col-auto">
        <!-- Additional buttons or actions can go here -->
      </div>
    </div>

    <div class="container">
      <div class="order-details-header">
          <h1 class="text-30 fw-600">Order Details</h1>
          <div class="order-id">
              <span>Order ID: <strong>{{ $order->order_id }}</strong></span>
          </div>
      </div>
  
      <!-- Order Summary Section -->
      <section class="order-summary">
        <div class="section-title">
            <h2>Order Summary</h2>
        </div>
        <div class="order-summary-content">
            <!-- User Name -->
            <div class="order-summary-item">
              <div class="order-summary-label">User:</div>
              <div class="order-summary-value">
                  @if (!empty($order->user->name))
                      {{ $order->user->name }}
                  @else
                      {{ $order->user->mobile_number }}
                  @endif
              </div>
          </div>
          
    
            <!-- Subscription Status -->
            <div class="order-summary-item">
                <div class="order-summary-label">Status:</div>
                <div class="order-summary-value">
                    <span class="badge 
                        {{ $order->subscription->status === 'paused' ? 'badge-warning' : 'badge-success' }}">
                        {{ ucfirst($order->subscription->status) }}
                    </span>
                    <!-- Highlighted text for paused subscriptions -->
                    
                </div>
            </div>
    
            <!-- Subscription Start Date -->
            <div class="order-summary-item">
                <div class="order-summary-label">Subscription Start Date:</div>
                <div class="order-summary-value">{{ $order->subscription->start_date }}</div>
            </div>
    
            <!-- Subscription End Date -->
            <div class="order-summary-item">
                <div class="order-summary-label">Subscription End Date:</div>
                <div class="order-summary-value">
                  @if ($order->subscription->new_date)
                      <span class="text-strike">
                          {{ \Carbon\Carbon::parse($order->subscription->end_date)->format('Y-m-d') }}
                      </span>
                      <span class="text-highlight ms-2">
                          {{ \Carbon\Carbon::parse($order->subscription->new_date)->format('Y-m-d') }}
                      </span>
                  @else
                      {{ \Carbon\Carbon::parse($order->subscription->end_date)->format('Y-m-d') }}
                  @endif
              </div>
              
            </div>
        </div>
        @if ($order->subscription->status === 'paused')
                        <div class="highlighted-text mt-2">
                            <strong>Note:</strong> Your subscription is paused from 
                            <span class="text-highlight">{{ \Carbon\Carbon::parse($order->subscription->pause_start_date)->format('Y-m-d') }}</span> 
                            to 
                            <span class="text-highlight">{{ \Carbon\Carbon::parse($order->subscription->pause_end_date)->format('Y-m-d') }}</span>.
                        </div>
                    @endif
    </section>
    
  
      <!-- Flower Product Section -->
      <section class="flower-product">
          <div class="col-md-12">
            <div class="section-title">
              <h2>Flower Product Details</h2>
          </div>
          </div>
          <div class="product-details">
              <div class="product-image">
                  <img src="{{ $order->flowerProduct->product_image_url }}" alt="Flower Product Image">
              </div>
              <div class="product-info">
                  <div class="product-name">
                      <strong>{{ $order->flowerProduct->name }}</strong>
                  </div>
                  <div class="product-description">
                      <p>{{ $order->flowerProduct->description ?? 'No description available.' }}</p>
                  </div>
              </div>
          </div>
      </section>
  
      <!-- Payment History Section -->
      <section class="payment-history">
          <div class="section-title">
              <h2>Payment History</h2>
          </div>
          <div class="payment-details">
              @foreach($order->flowerPayments as $payment)
                  <div class="payment-item">
                      <div class="payment-label">Payment Date: <span class="payment-value">{{ $payment->created_at }}</span></div>
                      <div class="payment-label">Payment Method: <span class="payment-value">{{ $payment->payment_method }}</span></div>
                      
                      <div class="payment-label">Amount:   <span class="payment-value">₹{{ $payment->paid_amount }}</span></div>
                    
                      <div class="payment-label">Status: <span class="payment-value">{{ $payment->payment_status }}</span></div>
                      
                  </div>
              @endforeach
          </div>
      </section>
  
      <!-- Address Details Section -->
      <section class="address-details">
          <div class="section-title">
              <h2>Address Details</h2>
          </div>
          <div class="address-content">
              <div class="address-item">
                  <div class="address-label">Full Address:</div>
                  <div class="address-value">
                    <td>
                      <strong>Address:</strong> {{ $order->address->apartment_flat_plot ?? "" }}, {{ $order->address->locality ?? "" }}<br>
                      <strong>Landmark:</strong> {{ $order->address->landmark ?? "" }}<br>

                      <strong>City:</strong> {{ $order->address->city ?? ""}}<br>
                      <strong>State:</strong> {{ $order->address->state ?? ""}}<br>
                      <strong>Pin Code:</strong> {{ $order->address->pincode ?? "" }}
                  </td>
                  </div>
              </div>
              
          </div>
      </section>
  </div>
  </div>
</div>

@endsection

@section('scripts')
@endsection
