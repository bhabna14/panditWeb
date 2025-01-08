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

  <section class="layout-pt-md">
    <div class="container">
      <div class="row y-gap-30 justify-between items-center">
        <div class="col-lg-5">
          <h2 class="text-30 fw-600">What is 33 Crores</h2>

          <p class="text-dark-1 mt-60 lg:mt-40 md:mt-20">
            At 33 Crores, our journey began with a profound realization—just as we strive to provide the best for our kids or our elderly parents, our celestial parents, our creators deserve nothing less. The idea germinated from a deep-seated belief that offering pure and premium puja essentials to the divine is not just a ritual but an expression of profound gratitude and respect. Driven by this guiding principle, we embarked on a mission to create a sanctuary for the gods and goddesses, where every product emanates purity and devotion. The birth of 33 Crores marked the dawn of a unique venture, weaving together tradition
           
          </p>
        </div>

        <div class="col-lg-6">
          <img src="{{ asset('front-assets/img/ppb.png')}}" alt="image" class="rounded-4">
        </div>
      </div>
    </div>
  </section>

@endsection

@section('scripts')
@endsection