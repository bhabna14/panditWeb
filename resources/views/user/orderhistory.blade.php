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
          <a href="{{ route('booking.history', ['filter' => 'all']) }}" class="{{ request('filter') == 'all' || !request('filter') ? 'active' : '' }}">All</a>
          <a href="{{ route('booking.history', ['filter' => 'pending']) }}" class="{{ request('filter') == 'pending' ? 'active' : '' }}">Payment Pending</a>
          <a href="{{ route('booking.history', ['filter' => 'confirmed']) }}" class="{{ request('filter') == 'confirmed' ? 'active' : '' }}">Confirmed</a>

          <a href="{{ route('booking.history', ['filter' => 'canceled']) }}" class="{{ request('filter') == 'canceled' ? 'active' : '' }}">Canceled</a>
          <a href="{{ route('booking.history', ['filter' => 'rejected']) }}" class="{{ request('filter') == 'rejected' ? 'active' : '' }}">Rejected By Pandit</a>
          <a href="{{ route('booking.history', ['filter' => 'completed']) }}" class="{{ request('filter') == 'completed' ? 'active' : '' }}">Completed</a>
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

      @foreach ($bookings as $index => $booking)
      <div class="col-md-12">
          <div class="order-history-sec">
              <div class="order-details">
                  <div class="row">
                      <div class="col-md-2">
                          BOOKING DATE <br>
                          {{ $booking->booking_date }}
                      </div>
                      <div class="col-md-2">
                          TOTAL FEE <br>
                          ₹ {{ $booking->pooja_fee }}
                      </div>
                      
                      @php
                          // Fetch the latest payment record for the booking
                          $latestPayment = \App\Models\Payment::where('booking_id', $booking->booking_id)
                                              ->orderBy('created_at', 'desc')
                                              ->first();
                          
                          // Initialize total paid and remaining amounts
                          $totalPaid = $latestPayment ? $latestPayment->paid : 0;
                          $remainingAmount = $booking->pooja_fee - $totalPaid;

                          $payments = \App\Models\Payment::where('booking_id', $booking->booking_id)
                                   ->where('user_id', $booking->user_id)
                                   ->get();

                          // Calculate the total paid amount
                          $totalPaidam = $payments->sum('paid');
                      @endphp

                      @if($booking->status != 'rejected')
                          <div class="col-md-2">
                              TOTAL PAID <br>
                              ₹ {{ sprintf('%.2f', $totalPaidam) }}
                          </div>
                          
                          @if($latestPayment && $latestPayment->payment_type != 'full')
                              <div class="col-md-3">
                                  REMAINING <br>
                                  ₹ {{ sprintf('%.2f', $remainingAmount) }}
                              </div>
                          @else
                              <div class="col-md-3">
                              </div>
                          @endif
                      @else
                          <div class="col-md-5">
                          </div>
                      @endif

                  
      
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
                  <div class="col-md-6">
                      <h6>{{ $booking->pooja->pooja_name }}</h6>
                      <p>{{ $booking->pandit->title }} {{ $booking->pandit->name }}</p>
                      <p>Duration: {{ $booking->pooja->pooja_duration }}</p>
                  </div>
                  <div class="col-md-4">
                        @if ($booking->status == "pending" && $booking->payment_status == "pending" && $booking->application_status == "approved"  && $booking->pooja_status == "pending" )
                        <a href="{{ route('payment.page', ['booking_id' => $booking->id]) }}" class="button px-10 fw-400 text-14 -blue-1 pay-button-bg h-50 text-white">Pay</a>

                        @endif
                    
                      @if ($booking->status == "paid" && $booking->payment_status == "paid" && $booking->application_status == "approved"  && $booking->pooja_status == "completed" )
                      <span class="status-text"><i class="fa fa-circle comp-dot" aria-hidden="true"></i>Completed on {{ $booking->booking_date }}</span>
                      @endif
                      @if ($booking->status == "canceled" && $booking->payment_status == "refundprocess" && $booking->application_status == "approved"  && $booking->pooja_status == "canceled")
                      <span class="status-text"><i class="fa fa-circle cancel-dot" aria-hidden="true"></i>Canceled on {{ $booking->payment->canceled_at }}</span>
                      @endif
                      @if ($booking->status == "rejected")
                          <a class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white rejected-text" href="{{ route('pandit.list', ['pooja_id' => $booking->pooja_id ,'pandit_id' =>  $booking->pandit->pandit_id]) }}" target="_blank" style="margin-bottom: 10px;background-color: #c80100 !important;">The pandit is booked. Please choose another pandit.View available pandits</a>
                      @endif
                      @if ($booking->status == "paid" && $booking->payment_status == "paid" && $booking->application_status == "approved"  && $booking->pooja_status == "completed")
                      <a href="{{ route('rate.pooja', ['id' => $booking->id]) }}" class="button px-10 fw-400 text-14  bg-dark-4 h-50 text-white" style="margin-bottom: 10px;background-color: #c80100 !important;">Rate the Pooja</a>
                      @endif
                      @if (Carbon\Carbon::parse($booking->booking_date)->subDay()->isFuture() && $booking->status == 'paid' && $booking->payment_status == 'paid' && $booking->application_status == 'approved'  && $booking->pooja_status == "pending")
                      <a href="{{ route('cancelForm', $booking->id) }}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white cancel-pooja-btn" style="margin-bottom: 10px;width: 100%;">Cancel Pooja</a>
                      @endif
                      @php
                      // Fetch the latest payment record for the booking
                      $latestPayment = \App\Models\Payment::where('booking_id', $booking->booking_id)
                                          ->orderBy('created_at', 'desc')
                                          ->first();
                                          
                      @endphp
                      {{-- @dd($latestPayment) --}}
                      @if ($booking->status != 'rejected')
                          @if ($latestPayment && $latestPayment->payment_type != 'full' && 
                              $booking->status == 'paid' && 
                              $booking->payment_status == 'paid' && 
                              $booking->application_status == 'approved' )
                              <a href="{{ route('payRemainingAmount', $booking->id) }}" class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white" style="margin-bottom: 10px;">Pay the remaining amount</a>
                          @endif
                      @endif
                  
                  

                      <a href="{{ url('view-ordered-pooja-details/'.$booking->id) }}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white">View Details</a>
                  </div>
              </div>
              @if ($booking->status == "canceled" && $booking->payment_status == "refundprocess" && $booking->application_status == "approved" && $booking->pooja_status == "canceled")
                <div class="refund-details">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                $currentDate = \Carbon\Carbon::now();
                                $daysBeforePooja = $currentDate->diffInDays($bookingDate);
                                $refundAmount = $booking->payment->refund_amount; // Assuming refund_amount is already calculated and stored in the booking
                            @endphp

                            @if ($booking->payment->payment_type == "advance")
                                <p>
                                    You paid an advance payment for this pooja and you canceled before {{ $daysBeforePooja }} days from the pooja so the refund amount is {{ $refundAmount }}.
                                    For any query call us at +919090808080.
                                </p>
                            @elseif ($booking->payment->payment_type == "full")
                                
                                <p>
                                    You paid a full payment for this pooja and you canceled before {{ $daysBeforePooja }} days from the pooja so the refund amount is {{ $booking->payment->refund_amount }} (with the cancellation charge of 20%).
                                    For any query call us at +919090808080.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

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
