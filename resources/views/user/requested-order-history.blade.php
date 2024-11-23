@extends('user.layouts.front-flower-dashboard')


@section('styles')
<style>
  .rejected-status{
    margin-bottom: 20px;
  }
  .rejected-status a{
    color: blue;
    font-weight: bold;
    text-decoration: underline;
  }
  .rejected-text{
    margin-bottom: 20px;
  }
  .order-history-sec .status-text a {
    pointer-events: auto;
  }
  .filter-buttons a {
    margin-right: 10px;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: #c80100;
    border: 1px solid;
}
  .filter-buttons a.active {
    background-color: #c80100;
    color:#fff;
  }
  .filter-buttons  a.active a:hover{
    color: #fff !important;
  }
  .refund-details {
    padding: 10px;
    border: 1px solid #ddd;
    margin: 10px 12px 15px 12px;
    font-weight: 500;
}
/* Styling for the 'Active' badge */
.badge-success {
    background-color: #008009 ; /* Green color for active */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

/* Styling for the 'Not Started' badge */
.badge-secondary {
    background-color: #6c757d; /* Gray color for not started */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

/* Optional: Add some spacing between the badge and the content */
.text-right .badge {
    margin-top: 5px;
}
.paid-button {
    margin-bottom: 11px;
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
    background-color: #28a745;
    border: 1px solid #218838;
    border-radius: 4px;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.3s ease;
}

.paid-button:hover {
    background-color: #218838;
    border-color: #1e7e34;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.paid-button i {
    margin-right: 8px;
    font-size: 16px;
}
.order-number label,
.payment-info label {
    font-size: 12px;
    color: #6c757d; /* Subtle grey for labels */
    text-transform: uppercase;
    font-weight: bold;
}

.order-id,
.total-payment {
    display: block;
    font-size: 16px;
    font-weight: bold;
    color: #000; /* Dark text for important details */
    margin-top: 5px;
}

.status-text {
    font-size: 14px;
    color: #ff9800; /* Amber for pending or notifying text */
    font-weight: 600;
}

.payment-status {
    margin-top: 5px;
    font-size: 14px;
    font-weight: 500;
}

.status-paid {
    color: #28a745; /* Green for paid status */
    font-weight: bold;
}

.total-payment {
    font-size: 18px;
    color: #c80100; /* Bright red for price */
    font-weight: 600;

}



</style>
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content bg-light-2">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <h1 class="text-30 lh-14 fw-600">Booking History</h1>
      </div>
      
    </div>
    <div class="row  y-gap-20 justify-between items-end pb-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <div class="filter-buttons">
          <a href="{{ route('subscription.history') }}" 
          class="{{ request()->routeIs('subscription.history') ? 'active' : '' }}">
          Subscription History
       </a>
       
       <a href="{{ route('requested.order.history') }}" 
          class="{{ request()->routeIs('requested.order.history') ? 'active' : '' }}">
          Customized Order History
       </a>
       
        </div>
      </div>
    </div>

    <div class="row">
      @if (session()->has('success'))
      <div class="alert alert-success" id="Message">
          {{ session()->get('success') }}
      </div>
      @endif

      @if ($errors->has('danger'))
          <div class="alert alert-danger" id="Message">
              {{ $errors->first('danger') }}
          </div>
      @endif

      @forelse ($requestedOrders as $request)

      <div class="col-md-12">
            <div class="order-history-sec">
                <div class="order-details">
                  <div class="row">
                    <div class="col-md-3">
                        <div class="order-number">
                            <label>ORDER NUMBER</label>
                            <span class="order-id">#{{ $request->request_id }}</span>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="payment-info">
                            @if ($request->status == "pending")
                                <h6 class="status-text">Order has been placed, Price will notify soon</h6>
                            @elseif ($request->status == "approved" || $request->status == "paid")
                                <label>TOTAL PAYMENT</label>
                                <span class="total-payment">
                                    ₹ {{ number_format($request->order->total_price, 2) }}
                                </span>
                                {{-- @if ($request->status == "paid")
                                    <div class="payment-status">Payment Status: <span class="status-paid">Paid</span></div>
                                @endif --}}
                            @endif
                        </div>
                    </div>
                </div>
                
                </div>
                <div class="row order-details-booking">
                    <div class="col-md-2">
                        <img src="{{asset('storage/'.$request->flowerProduct->product_image) }}" alt="Product Image" /> <!-- Display product image -->
                    </div>
                    <div class="col-md-7">
                        <h6>{{ $request->flowerProduct->name }}</h6> <!-- Subscription name -->
                        <p> <ul>
                          @foreach ($request->flowerRequestItems as $item)
                              <li>
                                  {{ $item->flower_name }} - {{ $item->flower_quantity }} {{ $item->flower_unit }}
                              </li>
                          @endforeach
                      </ul></p> <!-- Subscription description -->
                    </div>
                    <div class="col-md-3">
                        @if ($request->status == "pending")
                          
                          @elseif ($request->status == "approved")
                          <a href="javascript:void(0);" 
                          class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white pay-now-button" 
                          style="margin-bottom: 10px; background-color: #c80100 !important;" 
                          data-request-id="{{ $request->request_id }}" 
                          data-amount="{{ $request->order->total_price * 100 }}" 
                          data-order-id="{{ $request->order->order_id }}">
                          Pay Now
                       </a>
                       
                          @elseif($request->status == "paid")
                          <a href="javascript:void(0);" 
                              class="paid-button px-10 fw-400 text-14 h-50 text-white">
                                <i class="fas fa-check-circle"></i> Paid
                            </a>

                          @endif
                          
                        
                          <a href="{{ route('requested.order.details', ['id' => $request->id]) }}" 
                            class="button px-10 fw-400 text-14 pay-button-bg h-50 text-white">
                            View Details
                         </a>
                         
                    </div>
                </div>
            </div>
       
    </div>
    
    @empty
    <p>No subscription orders found.</p>
@endforelse
    </div>
  </div>
</div>

@endsection

@section('scripts')

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.pay-now-button');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const requestId = this.dataset.requestId;
                const amount = this.dataset.amount;
                const orderId = this.dataset.orderId;

                const options = {
                    key: "{{ config('services.razorpay.key') }}", // Add Razorpay key
                    amount: amount, // Amount in paise
                    currency: "INR",
                    name: "33 Crores",
                    image: "{{ asset('front-assets/img/brand/logo.png') }}",
                    description: "Payment for Order #" + orderId,
                    handler: function (response) {
                        // Submit payment details to your server
                        fetch("{{ route('request.order.payment.callback') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                request_id: requestId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Payment Successful!',
                                    text: 'Thank you for your payment.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload(); // Refresh the page or redirect
                                });
                            } else {
                                Swal.fire({
                                    title: 'Payment Failed',
                                    text: 'Something went wrong. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'Retry'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire({
                                title: 'Error',
                                text: 'An unexpected error occurred. Please try again later.',
                                icon: 'error',
                                confirmButtonText: 'Close'
                            });
                            console.error(err);
                        });
                    },
                    theme: {
                        color: "#F37254"
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
            });
        });
    });
</script>


@endsection
