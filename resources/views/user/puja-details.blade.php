@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
<section class="puja-details-sec">
    <div class="container">
        <div class="row">
           <div class="col-md-6">
              <div class="poja-details-img text-center">
                <img src="{{ asset('assets/img/' . $pooja->pooja_photo) }}" alt="">
              </div>
           </div>
           <div class="col-md-6">
            <div class="puja-heading-sec">
                <h5>{{ $pooja->pooja_name }}</h5>
                <p>{{ $pooja->short_description }}</p>
               
                <span><i class="fa fa-calendar-check-o" aria-hidden="true"></i>{{ $pooja->pooja_date }}</span>
            </div>
           </div>
        </div>
        <div class="row">
            <div class="tabs -underline-2 pt-20 lg:pt-40 sm:pt-30 js-tabs">
                <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">List Of Pandits</button>
                  </div>
                  <div class="col-auto">
                    <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button" data-tab-target=".-tab-item-2">About Pooja</button>
                  </div>
                </div>
                <div class="tabs__content js-tabs-content">
                  <div class="tabs__pane -tab-item-1 is-tab-el-active">
                    <div class="row" data-aos="fade-up">
                        @foreach ($pandit_pujas as $pandit_puja)
                        @if($pandit_puja->profile)
                            <div class="col-md-4 pandit-card">
                                <a href="{{ route('pandit.details', ['poojaSlug' => $pooja->slug, 'panditSlug' => $pandit_puja->profile->slug]) }}">
                                    <div class="card" data-state="#pooja">
                                        <div class="card-header">
                                            <img class="card-pooja" src="{{asset($pandit_puja->profile->profile_photo)}}" alt="image">
                                        </div>
                                        <div class="pooja-head row">
                                            <div class="col-md-8 col-7">
                                                <h5>{{ $pandit_puja->profile->name }}</h5>
                                                
                                            </div>
                                            <div class="col-md-4 col-5 text-right">
                                                <span class="rating">4.4</span>
                                                <p>₹{{ $pandit_puja->pooja_fee }}</p>
                                                <p>{{ $pandit_puja->pooja_duration }} hrs</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="col-md-4 pandit-card">
                                <p>No profile info available for this pandit.</p>
                            </div>
                        @endif
                    @endforeach
                    </div>
                  </div>
                  <div class="tabs__pane -tab-item-2">
                    <p class="text-15 text-dark-1">
                      Pharetra nulla ullamcorper sit lectus. Fermentum mauris pellentesque nec nibh sed et, vel diam, massa. Placerat quis vel fames interdum urna lobortis sagittis sed pretium. Morbi sed arcu proin quis tortor non risus.
                      <br><br>
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
