@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
      <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-32">
        <div class="col-auto">

          <h1 class="text-30 lh-14 fw-600">Coupons</h1>
         
        </div>

        <div class="col-auto">

        </div>
      </div>


     

          <div class="tabs__content pt-10 js-tabs-content">
            <div class="container">
                <div class="card_coupon">
                  <div class="main">
                    <div class="co-img">
                      <img
                        src="{{url('front-assets/img/flower.png')}}"
                        alt=""
                      />
                    </div>
                    <div class="vertical"></div>
                    <div class="content">
                      <h2>Flower Package</h2>
                      <h1>10% <span>Coupon</span></h1>
                      <p>Valid till 30 Aug 2024</p>
                    </div>
                  </div>
                  <div class="copy-button">
                    <input id="copyvalue" type="text" readonly value="GOFREE" />
                    <button onclick="copyIt()" class="copybtn">COPY</button>
                  </div>
                </div>
              </div>

           
           
          </div>
        


     
    </div>
  </div>

@endsection

@section('scripts')

@endsection