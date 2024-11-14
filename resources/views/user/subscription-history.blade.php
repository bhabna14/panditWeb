@extends('user.layouts.front-dashboard')

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
</style>
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content bg-light-2">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <h1 class="text-30 lh-14 fw-600">Booking History</h1>
      </div>
      <div class="col-auto">
        <div class="filter-buttons">
          {{-- <a href="{{ route('booking.history', ['filter' => 'all']) }}" class="{{ request('filter') == 'all' || !request('filter') ? 'active' : '' }}">All</a>
          <a href="{{ route('booking.history', ['filter' => 'pending']) }}" class="{{ request('filter') == 'pending' ? 'active' : '' }}">Payment Pending</a>
          <a href="{{ route('booking.history', ['filter' => 'confirmed']) }}" class="{{ request('filter') == 'confirmed' ? 'active' : '' }}">Confirmed</a>

          <a href="{{ route('booking.history', ['filter' => 'canceled']) }}" class="{{ request('filter') == 'canceled' ? 'active' : '' }}">Canceled</a>
          <a href="{{ route('booking.history', ['filter' => 'rejected']) }}" class="{{ request('filter') == 'rejected' ? 'active' : '' }}">Rejected By Pandit</a>
          <a href="{{ route('booking.history', ['filter' => 'completed']) }}" class="{{ request('filter') == 'completed' ? 'active' : '' }}">Completed</a> --}}
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

      @forelse ($subscriptionsOrder as $order)

      <div class="col-md-12">
            <div class="order-history-sec">
                <div class="order-details">
                    <div class="row">
                        <div class="col-md-3">
                            SUBSCRIPTION START DATE <br>
                            {{ \Carbon\Carbon::parse($order->subscription->start_date)->format('Y-m-d') }} <!-- Subscription start date -->
                        </div>
                        <div class="col-md-3">
                            SUBSCRIPTION END DATE <br>
                            {{ \Carbon\Carbon::parse($order->subscription->end_date)->format('Y-m-d') }} <!-- Subscription end date -->
                        </div>
                        <div class="col-md-2">
                            TOTAL PAYMENT <br>
                            ₹ {{ number_format($order->total_price), 2 }} <!-- Total payment from flowerPayments -->
                        </div>
                        <div class="col-md-3 text-right">
                            ORDER NUMBER <br>
                            #{{ $order->order_id }} <!-- Order number -->
                        </div>
                    </div>
                </div>
                <div class="row order-details-booking">
                    <div class="col-md-2">
                        <img src="{{ $order->flowerProduct->product_image_url }}" alt="Product Image" /> <!-- Display product image -->
                    </div>
                    <div class="col-md-6">
                        <h6>{{ $order->flowerProduct->name }}</h6> <!-- Subscription name -->
                        <p>{{ $order->flowerProduct->description }}</p> <!-- Subscription description -->
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="button px-10 fw-400 text-14 pay-button-bg h-50 text-white">
                            {{ $order->subscription->status == 'active' ? 'Running' : 'Paused' }}
                        </a>
                        <a href="rate-pooja-url" class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white" style="margin-bottom: 10px; background-color: #c80100 !important;">
                            Pause
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
      var cancelButtons = document.querySelectorAll('.cancel-pooja-btn');
      cancelButtons.forEach(function(button) {
          button.addEventListener('click', function(event) {
              event.preventDefault();
              var userConfirmed = confirm('Are you sure you want to cancel this booking?');
              if (userConfirmed) {
                  window.location.href = this.href;
              }
          });
      });
  });
</script>
@endsection
