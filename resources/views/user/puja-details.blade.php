@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')

<section class="puja-details-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="puja-images">
                    <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg') }}" alt="">
                </div>
            </div>
            <div class="col-md-4">
                <div class="puja-images">
                    <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg') }}" alt="">
                    <img src="{{ asset('front-assets/img/masthead/2/baldev.jpg') }}" alt="" class="mt-20">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="puja-heading-sec">
                <h5>Baladevjew Pooja</h5>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellat cum reiciendis ab ipsum omnis non repudiandae quo sequi unde dicta accusantium impedit praesentium, sint doloremque? Sit reprehenderit sequi nihil in.</p>
            </div>
        </div>
        <div class="row">
            <div class="tabs -underline-2 pt-20 lg:pt-40 sm:pt-30 js-tabs">
                <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">
  
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">List Of Pandits</button>
                  </div>
  
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button " data-tab-target=".-tab-item-2">About Pooja</button>
                  </div>
  
                  {{-- <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button " data-tab-target=".-tab-item-3">items 3</button>
                  </div> --}}
  
                </div>
  
                <div class="tabs__content js-tabs-content">
  
                  <div class="tabs__pane -tab-item-1 is-tab-el-active">
                    <div class = "row" data-aos="fade-up">
                        <div class="col-md-4 pandit-card">
                            <a href="{{url('pandit-details')}}"> 
                                <div class="card" data-state="#pooja">
                                    <div class="card-header">
                                        <img class="card-pooja" src="{{ asset('front-assets/img/avatars/pandit4.jpeg') }}" alt="image">
                                    </div>
                                    <div class="pooja-head row">
                                        <div class="col-md-8">
                                            <h5>P.Bibhu Panda</h5>
                                            <p>Lorem ipsum dolor sit amet consectetur.</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span class="rating">4.4</span>
                                            <p>₹3000</p>
                                            <p>2 hrs</p>
                                        </div>
                                        
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 pandit-card">
                            <a href="{{url('pandit-details')}}"> 
                                <div class="card" data-state="#pooja">
                                    <div class="card-header">
                                        <img class="card-pooja" src="{{ asset('front-assets/img/avatars/pandit4.jpeg') }}" alt="image">
                                    </div>
                                    <div class="pooja-head row">
                                        <div class="col-md-8">
                                            <h5>P.Bibhu Panda</h5>
                                            <p>Lorem ipsum dolor sit amet consectetur.</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span class="rating">4.4</span>
                                            <p>₹3000</p>
                                            <p>2 hrs</p>
                                        </div>
                                        
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 pandit-card">
                            <a href="{{url('pandit-details')}}"> 
                                <div class="card" data-state="#pooja">
                                    <div class="card-header">
                                        <img class="card-pooja" src="{{ asset('front-assets/img/avatars/pandit4.jpeg') }}" alt="image">
                                    </div>
                                    <div class="pooja-head row">
                                        <div class="col-md-8">
                                            <h5>P.Bibhu Panda</h5>
                                            <p>Lorem ipsum dolor sit amet consectetur.</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span class="rating">4.4</span>
                                            <p>₹3000</p>
                                            <p>2 hrs</p>
                                        </div>
                                        
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                    </div>
                  </div>
  
                  <div class="tabs__pane -tab-item-2 ">
                    <p class="text-15 text-dark-1">
                      Pharetra nulla ullamcorper sit lectus. Fermentum mauris pellentesque nec nibh sed et, vel diam, massa. Placerat quis vel fames interdum urna lobortis sagittis sed pretium. Morbi sed arcu proin quis tortor non risus.
                      <br><br>
                      Elementum lectus a porta commodo suspendisse arcu, aliquam lectus faucibus.
                    </p>
                  </div>
  
                  {{-- <div class="tabs__pane -tab-item-3 ">
                    <p class="text-15 text-dark-1">
                      Pharetra nulla ullamcorper sit lectus. Fermentum mauris pellentesque nec nibh sed et, vel diam, massa. Placerat quis vel fames interdum urna lobortis sagittis sed pretium. Morbi sed arcu proin quis tortor non risus.
                      <br><br>
                      Elementum lectus a porta commodo suspendisse arcu, aliquam lectus faucibus.
                    </p>
                  </div> --}}
  
                </div>
              </div>
            
           
        </div>
    </div>
</section>

@endsection

@section('scripts')
@endsection