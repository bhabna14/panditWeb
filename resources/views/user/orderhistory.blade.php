
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
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              {{-- <img src="{{ asset('assets/img/'.$booking->pooja->pooja_photo)}}" alt=""> --}}
              <img src="{{ asset('assets/img/'.$booking->pooja->poojalist->pooja_photo) }}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>{{ $booking->pooja->pooja_name }}</h6>
              <p>{{ $booking->pandit->name }}</p>
              <p>Duration: {{ $booking->pooja->pooja_duration }}</p>
              <p>Date : {{ $booking->booking_date }}</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a href="{{ url('view-ordered-pooja-details/'.$booking->id) }}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;">View Details</a>
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
@endsection