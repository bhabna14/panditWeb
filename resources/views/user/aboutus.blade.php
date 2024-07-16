@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <div class="sc-gJqsIT bdDCMj logo" height="6rem" width="30rem">
                    <div class="low-res-container">
                    </div>
                </div>
                <h1 class="sc-7kepeu-0 kYnyFA description">About Us</h1>
               
            </div>
        </div>
</section>

<section class="layout-pt-lg layout-pb-md">
    <div data-anim-wrap class="container">
      <div data-anim-child="slide-up delay-1" class="row justify-center text-center">
        <div class="col-auto">
          <div class="sectionTitle -md">
            <h2 class="sectionTitle__title">Why Choose Us</h2>
            <p class=" sectionTitle__text mt-5 sm:mt-0">These popular destinations have a lot to offer</p>
          </div>
        </div>
      </div>

      <div class="row y-gap-40 justify-between pt-50">

        <div data-anim-child="slide-up delay-2" class="col-lg-3 col-sm-6">

          <div class="featureIcon -type-1 ">
            <div class="d-flex justify-center">
              <img src="{{ asset('front-assets/img/featureIcons/1/1.svg')}}" alt="image">
            </div>

            <div class="text-center mt-30">
              <h4 class="text-18 fw-500">Best Price Guarantee</h4>
              <p class="text-15 mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
          </div>

        </div>

        <div data-anim-child="slide-up delay-3" class="col-lg-3 col-sm-6">

          <div class="featureIcon -type-1 ">
            <div class="d-flex justify-center">
              <img src="{{ asset('front-assets/img/featureIcons/1/2.svg')}}" alt="image">
            </div>

            <div class="text-center mt-30">
              <h4 class="text-18 fw-500">Easy & Quick Booking</h4>
              <p class="text-15 mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
          </div>

        </div>

        <div data-anim-child="slide-up delay-4" class="col-lg-3 col-sm-6">

          <div class="featureIcon -type-1 ">
            <div class="d-flex justify-center">
              <img src="{{ asset('front-assets/img/featureIcons/1/3.svg')}}" alt="image">
            </div>

            <div class="text-center mt-30">
              <h4 class="text-18 fw-500">Customer Care 24/7</h4>
              <p class="text-15 mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
          </div>

        </div>

      </div>
    </div>
  </section>

  <section class="layout-pt-md">
    <div class="container">
      <div class="row y-gap-30 justify-between items-center">
        <div class="col-lg-5">
          <h2 class="text-30 fw-600">About Us</h2>
          <p class="mt-5">These popular destinations have a lot to offer</p>

          <p class="text-dark-1 mt-60 lg:mt-40 md:mt-20">
            London is a shining example of a metropolis at the highest peak of modernity and boasts an economy and cultural diversity that’s the envy of other global superpowers.
            <br><br>
            Take the opportunity to acquaint yourself with its fascinating history chronicled by institutions like the British Museum as well as see how far it has come by simply riding the Tube and passing by celebrated landmarks like Buckingham Palace, Westminster Abbey, and marvels like Big Ben, the London Eye, and the Tower Bridge.
          </p>
        </div>

        <div class="col-lg-6">
          <img src="{{ asset('front-assets/img/ppb.png')}}" alt="image" class="rounded-4">
        </div>
      </div>
    </div>
  </section>

  <section class="pt-60">
    <div class="container">
      <div class="border-bottom-light pb-40">
        <div class="row y-gap-30 justify-center text-center">

          <div class="col-xl-3 col-6">
            <div class="text-40 lg:text-30 lh-13 fw-600">4,958</div>
            <div class="text-14 lh-14 text-light-1 mt-5">Pandits</div>
          </div>

          <div class="col-xl-3 col-6">
            <div class="text-40 lg:text-30 lh-13 fw-600">2,869</div>
            <div class="text-14 lh-14 text-light-1 mt-5">Total Pooja</div>
          </div>

          <div class="col-xl-3 col-6">
            <div class="text-40 lg:text-30 lh-13 fw-600">2M</div>
            <div class="text-14 lh-14 text-light-1 mt-5">Happy customers</div>
          </div>

          <div class="col-xl-3 col-6">
            <div class="text-40 lg:text-30 lh-13 fw-600">574,974</div>
            <div class="text-14 lh-14 text-light-1 mt-5">Our Volunteers</div>
          </div>

        </div>
      </div>
    </div>
  </section>

@endsection

@section('scripts')
@endsection