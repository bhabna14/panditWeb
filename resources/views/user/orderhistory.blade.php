@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content bg-light-2">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">

        <h1 class="text-30 lh-14 fw-600">Booking History</h1>
        <div class="text-15 text-light-1">Lorem ipsum dolor sit amet, consectetur.</div>

      </div>

      <div class="col-auto">

      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a  href="{{ url('view-ordered-pooja-details')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;" >View Details</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a  href="{{ url('view-ordered-pooja-details')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;" >View Details</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;" >View Details</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;" >View Details</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;" >View Details</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="order-card">
          <div class="row">
            <div class="col-md-5 order-img">
              <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg')}}" alt="">
            </div>
            <div class="col-md-6 mt-15">
              <h6>Baladevjew Pooja</h6>
              <p>P.Bibhu Panda</p>
              <p>Duration: 3hr</p>
              <p>Date : 14/05/2024</p>
            </div>
          </div>
          <div class="row mt-20 mb-20">
            <div class="col-md-6">
              <a href="{{ url('rate-pooja')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;background-color: #c80100 !important;" >Rate the Pooja</a>
            </div>
            <div class="col-md-6">
              <a href="{{ url('view-ordered-pooja-details')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-right: 20px;" >View Details</a>
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