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
            <p><i class="fa fa-map-pin map-icon"></i>4th floor, o-hub, sez road, chandaka industrial estate, bhubaneswar, odisha 751024</p>
        </div>
      <div class="col-md-12">
        <div class="">
          <div class="row">
            <div class="col-md-3 mt-15">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
              <p>Advance Payment: 600</p>
              <p>Total Payment: 3000</p>
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