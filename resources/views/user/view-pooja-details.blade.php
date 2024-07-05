@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">

        <h1 class="text-30 lh-14 fw-600">Booking History</h1>
       

      </div>

      <div class="col-auto">

      </div>
    </div>
    <div class="row">
        
        <div class="col-md-12">
            <p><i class="fa fa-map-pin map-icon"></i>{{ $booking->address->area ?? 'N/A' }},{{ $booking->address->city ?? 'N/A' }},{{ $booking->address->state ?? 'N/A' }}
              {{ $booking->address->country ?? 'N/A' }}<br>
              Pincode : {{ $booking->address->pincode ?? 'N/A' }}<br>
              Landmark : {{ $booking->address->landmark ?? 'N/A' }}</p>
        </div>
      <div class="col-md-12">
        <div class="">
          <div class="row">
            <div class="col-md-3 mt-15">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>{{ $booking->pooja->pooja_name }}</h6>
              <p>{{ $booking->pandit->name }}</p>
              <p>Duration: {{ $booking->pooja->pooja_duration }}</p>
              <p>Date : {{ $booking->booking_date }}</p>
             
              <p>Total Payment: {{ $booking->pooja_fee }}</p>
              <p>Paid: {{ $booking->paid }}</p>
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