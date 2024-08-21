@extends('user.layouts.front-dashboard')

@section('styles')
<style>
  /* .dashboard__main {
    padding: 20px;
  } */

  .booking-details img {
    width: 100%;
    border-radius: 8px;
  }

  .order-inner-details, .booking-details {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
  }

  .order-inner-details h4, .booking-details h4, .booking-details h6 {
    margin-bottom: 10px;
    font-weight: bold;
  }

  .booking-details p {
    margin-bottom: 5px;
  }

  .details-container {
    margin-bottom: 20px;
  }
</style>
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <h1 class="text-30 lh-14 fw-600">Booking Details</h1>
      </div>
      <div class="col-auto">
        <!-- Additional buttons or actions can go here -->
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="order-inner-details">
          <div class="row">
            <div class="col-md-4 details-container">
              <h4>Address</h4>
              <p>{{ $booking->address->area ?? 'N/A' }}, {{ $booking->address->city ?? 'N/A' }}, {{ $booking->address->state ?? 'N/A' }}, {{ $booking->address->country ?? 'N/A' }}</p>
              <p>Pincode: {{ $booking->address->pincode ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4 details-container">
              <h4>Payment Method</h4>
              @if($booking->payment_status == 'paid')
                <p>By {{ $booking->payment->payment_method ?? 'N/A' }}</p>
              @elseif($booking->payment_status == 'refundprocess')
               
                <p>Process for Refund</p>
            
              @elseif($booking->payment_status == 'paid')
                <p>By {{ $booking->payment->refund_method ?? 'N/A' }}</p>
              @else
                <p>Payment Not Done Yet</p>
              @endif
            </div>
            <div class="col-md-4 details-container">
              <h4>Booking Summary</h4>
              <p>Total Fee: ₹ {{ $booking->pooja_fee ?? 'N/A' }}</p>
              <p>Advance Fee: ₹ {{ $booking->advance_fee ?? 'N/A' }}</p>
              <p>Paid Amount: ₹ {{ $booking->payment->paid ?? 'N/A' }}</p>
              <p>Refund Details: ₹{{ $booking->payment->refund_amount ?? 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="booking-details" style="margin-top:20px">
          <div class="row">
            <div class="col-md-2">
              <img src="{{ asset('assets/img/'.$booking->pooja->poojalist->pooja_photo) }}" alt="Pooja Photo">
            </div>
            <div class="col-md-6">
              <h6>{{ $booking->booking_id }}</h6>
              <h6>{{ $booking->pooja->pooja_name }}</h6>
              <p>{{ $booking->pandit->title }} {{ $booking->pandit->name }}</p>
              <p>Duration: {{ $booking->pooja->pooja_duration }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
@endsection
