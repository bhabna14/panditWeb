@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')

<section class="pandit-single-profile">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="pandit-profile">
                    <img src="{{asset('front-assets/img/avatars/pandit4.jpeg')}}" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="pandit-desc">
                    <h5>P.Bibhu Panda</h5>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Blanditiis quisquam minima sequi numquam culpa facilis labore odit ipsam optio, repudiandae iusto, sapiente harum consequuntur magni consequatur aperiam quibusdam quam laboriosam.</p>
                </div>
                <div class="pandit-price">
                    <h5>Total Amount : 3000</h5>
                    <h5>Advance Amount : 600</h5> 
                    <a href="{{url('book-now')}}"><button class="button -md -blue-1 bg-dark-3 text-white mt-20">Book Now</button></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="tabs -underline-2 pt-20 lg:pt-40 sm:pt-30 js-tabs">
                <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">
  
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">Description</button>
                  </div>
  
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button " data-tab-target=".-tab-item-2">Reviews</button>
                  </div>
  
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button " data-tab-target=".-tab-item-3">Photos</button>
                  </div>
                  {{-- <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button " data-tab-target=".-tab-item-3">Terms & Conditions</button>
                  </div> --}}
  
                </div>
  
                <div class="tabs__content pt-20 js-tabs-content">
  
                  <div class="tabs__pane -tab-item-1 is-tab-el-active">
                    <p class="text-15 text-dark-1 mb-30">
                      Pharetra nulla ullamcorper sit lectus. Fermentum mauris pellentesque nec nibh sed et, vel diam, massa. Placerat quis vel fames interdum urna lobortis sagittis sed pretium. Morbi sed arcu proin quis tortor non risus.
                      <br>
                      Elementum lectus a porta commodo suspendisse arcu, aliquam lectus faucibus.
                    </p>
                  </div>
  
                  <div class="tabs__pane -tab-item-2 mb-30">
                    <p class="text-15 text-dark-1">
                      Pharetra nulla ullamcorper sit lectus. Fermentum mauris pellentesque nec nibh sed et, vel diam, massa. Placerat quis vel fames interdum urna lobortis sagittis sed pretium. Morbi sed arcu proin quis tortor non risus.
                      <br>
                      Elementum lectus a porta commodo suspendisse arcu, aliquam lectus faucibus.
                    </p>
                  </div>
  
                  <div class="tabs__pane -tab-item-3 mb-30">
                    <p class="text-15 text-dark-1">
                      Pharetra nulla ullamcorper sit lectus. Fermentum mauris pellentesque nec nibh sed et, vel diam, massa. Placerat quis vel fames interdum urna lobortis sagittis sed pretium. Morbi sed arcu proin quis tortor non risus.
                      <br>
                      Elementum lectus a porta commodo suspendisse arcu, aliquam lectus faucibus.
                    </p>
                  </div>
  
                </div>
              </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
@endsection