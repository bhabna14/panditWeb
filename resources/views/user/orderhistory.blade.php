
@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content bg-light-2">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">

        <h1 class="text-30 lh-14 fw-600">Booking History</h1>
        {{-- <div class="text-15 text-light-1">Lorem ipsum dolor sit amet, consectetur.</div> --}}

      </div>

      <div class="col-auto">

      </div>
    </div>
    <div class="row">
      @foreach ($bookings as $index => $booking)
      <div class="col-md-12">
        <div class="order-history-sec">
          <div class="order-details">
            <div class="row">
              <div class="col-md-2">
                BOOKING DATE <br>
                {{ $booking->booking_date }},{{ $booking->booking_time }}
              </div>
              <div class="col-md-2">
                TOTAL FEE <br>
                ₹ {{ $booking->pooja_fee }}
              </div>
              <div class="col-md-2">
                TOTAL PAID <br>
                ₹ {{ $booking->paid }}
              </div>
              <div class="col-md-3">
              </div>
              <div class="col-md-3 text-right">
                BOOKING NUMBER <br>
                # {{ $booking->booking_id }}
              </div>
            </div>
          </div>
          <div class="row order-details-booking">
            <div class="col-md-2">
              <img src="{{ asset('assets/img/'.$booking->pooja->poojalist->pooja_photo) }}" alt="">
            </div>
            <div class="col-md-7">
              <h6>{{ $booking->pooja->pooja_name }}</h6>
              <p>{{ $booking->pandit->name }}</p>
              <p>Duration: {{ $booking->pooja->pooja_duration }}</p>
            </div>
            <div class="col-md-3">
              @if (Carbon\Carbon::parse($booking->booking_date)->isPast())
              <span class="status-text"><i class="fa fa-circle comp-dot" aria-hidden="true"></i>Completed on {{ $booking->booking_date }}</span>
              @endif
              @if ($booking->status == "canceled")
              <span class="status-text"><i class="fa fa-circle cancel-dot" aria-hidden="true"></i>Canceled on {{ $booking->canceled_at }}</span>
              @endif
              @if (Carbon\Carbon::parse($booking->booking_date)->isPast() && $booking->status !== 'canceled')
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-bottom: 10px;background-color: #c80100 !important;">Rate the Pooja</a>
              @endif
              @if (Carbon\Carbon::parse($booking->booking_date)->isFuture() && $booking->status !== 'canceled')
                  <a href="{{ route('cancelForm', $booking->id) }}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white cancel-pooja-btn" style="margin-bottom: 10px;width: 100%;">Cancel Pooja</a>
              @endif
              <a href="{{ url('view-ordered-pooja-details/'.$booking->id) }}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white">View Details</a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
      
      
    
     
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