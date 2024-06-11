@extends('user.layouts.front')

@section('styles')

@endsection

@section('content')
    <!-- Main Section -->
    {{-- <section data-anim-wrap class="masthead -type-7" style="margin-top: -104px;">
        <div class="masthead-slider js-masthead-slider-7">
          <div class="swiper-wrapper">
  
            <div class="swiper-slide">
              <div class="row justify-center text-center">
                <div class="col-auto">
                  <div class="masthead__content">
                    <div class="masthead__bg">
                      <img src="{{url('front-assets/img/masthead/banner1.png')}}" alt="image">
                    </div>
  
                    <div data-anim-child="slide-up delay-1" class="text-white">
                        Experienced Pandits for Every Occasion, Just a Click Away
                    </div>
  
                    <h1 data-anim-child="slide-up delay-2" class="text-60 lg:text-40 md:text-30 text-white">
                        Find the Perfect Pandit for<br class="lg:d-none"> Your Spiritual Journey 
                    </h1>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="swiper-slide">
              <div class="row justify-center text-center">
                <div class="col-auto">
                  <div class="masthead__content">
                    <div class="masthead__bg">
                      <img src="{{url('front-assets/img/masthead/banner2.png')}}" alt="image">
                    </div>
  
                    <div data-anim-child="slide-up delay-1" class="text-white">
                        Bringing Traditional Expertise to Your Doorstep

                    </div>
  
                    <h1 data-anim-child="slide-up delay-2" class="text-60 lg:text-40 md:text-30 text-white">
                        Convenient and Reliable <br class="lg:d-none"> Online Pandit
                    </h1>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="swiper-slide">
              <div class="row justify-center text-center">
                <div class="col-auto">
                  <div class="masthead__content">
                    <div class="masthead__bg">
                      <img src="{{url('front-assets/img/masthead/banner3.png')}}" alt="image">
                    </div>
  
                    <div data-anim-child="slide-up delay-1" class="text-white">
                        Reliable Pandit Booking for Every Religious Event
                    </div>
  
                    <h1 data-anim-child="slide-up delay-2" class="text-60 lg:text-40 md:text-30 text-white">
                        Effortless Booking, Expert Guidance, <br class="lg:d-none"> Memorable Rituals
                    </h1>
                  </div>
                </div>
              </div>
            </div>
  
          </div>
  
          <div class="masthead-slider__nav -prev js-prev">
            <button class="button -outline-white text-white size-50 rounded-full">
              <i class="icon-arrow-left"></i>
            </button>
          </div>
  
          <div class="masthead-slider__nav -next js-next">
            <button class="button -outline-white text-white size-50 rounded-full">
              <i class="icon-arrow-right"></i>
            </button>
          </div>
        </div>
  
       
    </section> --}}
    <section data-anim-wrap class="masthead -type-2 js-mouse-move-container" style="margin-top: -17px;">
        <div class="masthead__bg">
            <img src="{{ asset('front-assets/img/masthead/home.png') }}" alt="image">
        </div>

        <div class="container">

            <div class="masthead__content">
                <div class="row y-gap-40">
                    <div class="col-xl-5" style="margin-top: 33px;">
                        <h1 data-anim-child="slide-up delay-2"
                            class="z-2 lg:text-40 text-dark md:text-30  xl:pt-0 text-respn" style="    font-size: 44px; line-height:49px !important">
                            <span class="" style="color: #c80100;font-size: 72px;">N</span>EW <span class="" style="color:#c80100">O</span>FFERS: <br> <span class="" style="color:#c80100"> B</span>OOK <span class="" style="color:#c80100">P</span>ANDIT <span class="" style="color:#c80100">O</span>NLINE<br>
                           
                        </h1>
                        {{-- <p style="
                            font-size: 23px;
                            color: #000;
                            font-weight: 500;
                            margin-top: 20px;
                        ">Up to 100% discount on your first booking and flat 50% off in all Pooja Packages.</p> --}}
                        {{-- <p data-anim-child="slide-up delay-3" class="z-2 text-white mt-20">Search Location Here!!</p> --}}

                        {{-- <div data-anim-child="slide-up delay-4"
                            class="mainSearch z-2 bg-white pr-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 shadow-1">
                            <div class="button-grid items-center">

                                <div class="searchMenu-loc px-30 lg:py-20 lg:px-0 js-form-dd js-liverSearch">

                                    <div data-x-dd-click="searchMenu-loc">
                                        <h4 class="text-15 fw-500 ls-2 lh-16">Location</h4>

                                        <div class="text-15 text-light-1 ls-2 lh-16">
                                            <input autocomplete="off" type="search" placeholder="Select Location?"
                                                class="js-search js-dd-focus" />
                                        </div>
                                    </div>


                                    <div class="searchMenu-loc__field shadow-2 js-popup-window" data-x-dd="searchMenu-loc"
                                        data-x-dd-toggle="-is-active">
                                        <div class="bg-white px-30 py-30 sm:px-0 sm:py-15 rounded-4 text-center">
                                            <div class="y-gap-5 js-results">

                                                <div>
                                                    <button
                                                        class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                                                        <div class="d-flex">
                                                            <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                                                            <div class="ml-10">
                                                                <a href="jagannath-temple-sevayat.html">
                                                                    <div
                                                                        class="text-15 lh-12 fw-500 js-search-option-target">
                                                                        Khordha</div>
                                                                    <div class="text-14 lh-12 text-light-1 mt-5">Bhubaneswar
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>

                                                <div>
                                                    <button
                                                        class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                                                        <div class="d-flex">
                                                            <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                                                            <div class="ml-10">
                                                                <a href="lingaraj-temple-sevayat.html">
                                                                    <div
                                                                        class="text-15 lh-12 fw-500 js-search-option-target">
                                                                        Cuttack</div>
                                                                    <div class="text-14 lh-12 text-light-1 mt-5">Cuttack
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>

                                                <div>
                                                    <button
                                                        class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                                                        <div class="d-flex">
                                                            <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                                                            <div class="ml-10">
                                                                <a href="biraja-temple-sevayat.html">
                                                                    <div
                                                                        class="text-15 lh-12 fw-500 js-search-option-target">
                                                                        Jagatsinghpur</div>
                                                                    <div class="text-14 lh-12 text-light-1 mt-5">Paradeep
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="col-xl-7">
                        <div class="masthead__images">
                            <div data-anim-child="slide-up delay-6"><img
                                    src="{{ asset('front-assets/img/masthead/home1.png') }}" alt="image"></div>
                            <div data-anim-child="slide-up delay-7"><img
                                    src="{{ asset('front-assets/img/masthead/b2.jpeg') }}" alt="image"></div>
                            <div data-anim-child="slide-up delay-8"><img
                                    src="{{ asset('front-assets/img/masthead/b1.jpeg') }}" alt="image"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row" style="margin-top:60px">
                <div class="col-md-6">
                    
                    <div class="img-group">
                        <div class="img-group-inner">
                          <img src="https://metropolitanhost.com/themes/themeforest/html/maharatri/assets/img/about-group1/1.jpg" alt="about">
                          <span></span>
                          <span></span>
                        </div>
                        <img src="https://metropolitanhost.com/themes/themeforest/html/maharatri/assets/img/about-group1/2.jpg" alt="about">
                        <img src="https://metropolitanhost.com/themes/themeforest/html/maharatri/assets/img/about-group1/3.jpg" alt="about">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-title mb-0 text-start">
                        <p class="subtitle">EDUCATION FOR ALL RURAL CHILDREN</p>
                        <h4 class="title">We are a Hindu that believe in Ram</h4>
                    </div>
                    <ul class="sigma_list list-2 mb-0">
                        <li>Peace of Mind</li>
                        <li>Set For Pastor</li>
                        <li>100% Satisfaction</li>
                        <li>Trusted Company</li>
                    </ul>
                    <p class="blockquote bg-transparent"> We are a Hindu that belives in Lord Rama and Vishnu Deva the followers and We are a Hindu that belives in Lord Rama and Vishnu Deva. </p>
                </div>
            </div>
        </div>
    </section>

  
    <section class="layout-pt-md layout-pb-md upcoming-pooja" style="overflow-y: hidden;overflow-x: hidden">
        <div class="container">
            <div data-anim="slide-up delay-1" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Upcoming Pooja</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">These are few upcoming Pooja for you to do</p>
                    </div>
                </div>

                {{-- <div class="col-auto md:d-none">

                    <a href="#" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                        View All Pooja <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div> --}}
            </div>

            <div class="relative pt-40 sm:pt-20 js-section-slider" data-gap="30" data-scrollbar
                data-slider-cols="base-2 xl-4 lg-3 md-2 sm-2 base-1" data-anim="" data-aos="fade-up" data-aos-delay="500">
                <div class="swiper-wrapper">
                    @foreach ($upcomingPoojas as $upcomingPooja)
                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:2">
                                <img src="#" data-src="img/destinations/1/1.png" alt="image" class="js-lazy">
                                <img src="{{ asset('assets/img/'.$upcomingPooja->pooja_photo) }}" alt="image"
                                    class="js-lazy">

                            </div>

                            {{-- <div class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                              
                            </div> --}}
                        </a>
                        <div class="citiesCard__bottom">
                            <h4 class="text-18 md:text-20 lh-13 text-center text-dark mb-10 mt-10">{{$upcomingPooja->pooja_name}}</h4>
                        </div>

                    </div>
                    @endforeach

                
                  

                </div>


                <button
                    class="section-slider-nav -prev flex-center button -blue-1 bg-white shadow-1 size-40 rounded-full sm:d-none js-prev">
                    <i class="icon icon-chevron-left text-12"></i>
                </button>

                <button
                    class="section-slider-nav -next flex-center button -blue-1 bg-white shadow-1 size-40 rounded-full sm:d-none js-next">
                    <i class="icon icon-chevron-right text-12"></i>
                </button>


                <div class="slider-scrollbar bg-light-2 mt-40 sm:d-none js-scrollbar"></div>

                <div class="row pt-20 d-none md:d-block">
                    <div class="col-auto">
                        <div class="d-inline-block">

                            <a href="{{ url('pooja-list')}}" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                                View All Temples <div class="icon-arrow-top-right ml-15"></div>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> 

    <section class="special-pooja-bg layout-pt-md layout-pb-md">
        <div class="container">
            <div data-anim="" data-aos="fade-up" data-aos-delay="500" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Special Pooja</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">These are few upcoming Pooja for you to do</p>
                    </div>
                </div>

                <div class="col-auto md:d-none">

                    <a href="{{ url('pooja-list')}}" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                        View All Pooja <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
            </div>

            <div class = "row" data-aos="fade-up" data-aos-delay="500">
                @foreach ($otherpoojas as $otherpooja)
                <div class="col-md-3 pandit-card">
                    <div class="card" data-state="#pooja">
                        <div class="card-header">
                            <img class="card-pooja" src="{{ asset('assets/img/'.$otherpooja->pooja_photo) }}" alt="image">
                        </div>
                        <div class="pooja-head">
                            <h5>{{$otherpooja->pooja_name}}</h5>
                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                            <div style="text-align: center">
                                {{-- <h6>(12-03-2024)</h6> --}}
                            </div>
                            {{-- <button class="contact-me">Book Now</button> --}}
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>

           
        </div>
    </section>

    <section class="layout-pt-md layout-pb-md">
        <div class="container">
            <div data-anim="" data-aos="fade-up" class="row y-gap-20 mb-30 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Famous Pandits</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">These are few upcoming Pooja for you to do</p>
                    </div>
                </div>

                <div class="col-auto md:d-none">

                    <a href="{{ url('book-pandit')}}" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                        View All Pandits <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
            </div>

            <div class="row mb-30" data-aos="fade-up" data-aos-delay="500">
                <div class="col-md-4 col-12 mb-20">
                   <div class="row">
                     <div class="col-md-4 col-4">
                        <div class="pandit-front-sec-img">
                          <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                        </div>
                       
                     </div>
                     <div class="col-md-8 col-8">
                        <div class="pandit-front-sec-text">
                            <h3>P.Bibhu Panda</h3>
                            <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                        </div>
                     </div>
                   </div>
                  
                </div>
                <div class="col-md-4 col-12 mb-20">
                    <div class="row">
                      <div class="col-md-4 col-4">
                         <div class="pandit-front-sec-img">
                           <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                         </div>
                        
                      </div>
                      <div class="col-md-8 col-8">
                         <div class="pandit-front-sec-text">
                             <h3>P.Bibhu Panda</h3>
                             <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                         </div>
                      </div>
                    </div>
                   
                 </div>
                 <div class="col-md-4 col-12 mb-20">
                    <div class="row">
                      <div class="col-md-4 col-4">
                         <div class="pandit-front-sec-img">
                           <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                         </div>
                        
                      </div>
                      <div class="col-md-8 col-8">
                         <div class="pandit-front-sec-text">
                             <h3>P.Bibhu Panda</h3>
                             <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                         </div>
                      </div>
                    </div>
                   
                 </div>

               
            </div>
            <div class="row mb-30" data-aos="fade-up" data-aos-delay="500">
                <div class="col-md-4 col-12 mb-20">
                   <div class="row">
                     <div class="col-md-4 col-4">
                        <div class="pandit-front-sec-img">
                          <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                        </div>
                       
                     </div>
                     <div class="col-md-8 col-8">
                        <div class="pandit-front-sec-text">
                            <h3>P.Bibhu Panda</h3>
                            <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                        </div>
                     </div>
                   </div>
                  
                </div>
                <div class="col-md-4 col-12 mb-20">
                    <div class="row">
                      <div class="col-md-4 col-4">
                         <div class="pandit-front-sec-img">
                           <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                         </div>
                        
                      </div>
                      <div class="col-md-8 col-8">
                         <div class="pandit-front-sec-text">
                             <h3>P.Bibhu Panda</h3>
                             <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                         </div>
                      </div>
                    </div>
                   
                 </div>
                 <div class="col-md-4 col-12 mb-20">
                    <div class="row">
                      <div class="col-md-4 col-4">
                         <div class="pandit-front-sec-img">
                           <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                         </div>
                        
                      </div>
                      <div class="col-md-8 col-8">
                         <div class="pandit-front-sec-text">
                             <h3>P.Bibhu Panda</h3>
                             <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                         </div>
                      </div>
                    </div>
                   
                 </div>

               
            </div>
            <div class="row mb-30" data-aos="fade-up" data-aos-delay="500">
                <div class="col-md-4 col-12 mb-20">
                   <div class="row">
                     <div class="col-md-4 col-4">
                        <div class="pandit-front-sec-img">
                          <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                        </div>
                       
                     </div>
                     <div class="col-md-8 col-8">
                        <div class="pandit-front-sec-text">
                            <h3>P.Bibhu Panda</h3>
                            <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                        </div>
                     </div>
                   </div>
                  
                </div>
                <div class="col-md-4 col-12 mb-20">
                    <div class="row">
                      <div class="col-md-4 col-4">
                         <div class="pandit-front-sec-img">
                           <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                         </div>
                        
                      </div>
                      <div class="col-md-8 col-8">
                         <div class="pandit-front-sec-text">
                             <h3>P.Bibhu Panda</h3>
                             <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                         </div>
                      </div>
                    </div>
                   
                 </div>
                 <div class="col-md-4 col-12 mb-20">
                    <div class="row">
                      <div class="col-md-4 col-4">
                         <div class="pandit-front-sec-img">
                           <img class="rounded-lg" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=900&h=900&q=80" alt="">
                         </div>
                        
                      </div>
                      <div class="col-md-8 col-8">
                         <div class="pandit-front-sec-text">
                             <h3>P.Bibhu Panda</h3>
                             <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                         </div>
                      </div>
                    </div>
                   
                 </div>

               
            </div>

        </div>
    </section>



    <section class="layout-pt-lg layout-pb-lg bg-blue-2">
        <div class="container">
            <div class="row y-gap-20 justify-center text-center">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">How does it work?</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">Use Traditional Ways For Your Spiritual Path With The
                            Help Of Technology</p>
                    </div>
                </div>
            </div>

            <div class="row y-gap-30 justify-between pt-40">

                <div class="col-xl-2 col-lg-1 col-md-6">
                    <div class="d-flex flex-column items-center text-center">
                        <div class="relative size-120 flex-center rounded-full bg-green-1">
                            <img src="{{ asset('front-assets/img/pages/become-expert/icons/1.png') }}" alt="image">

                            <div class="side-badge">
                                <div class="size-40 flex-center rounded-full bg-yellow-1">
                                    <span class="text-15 fw-500">01</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-18 fw-500 mt-30 sm:mt-20">Select Temple To Visit</div>
                    </div>
                </div>


                <div class="col-1 xl:d-none">
                    <div class="pt-30">
                        <img src="{{ asset('front-assets/img/pages/become-expert/lines/1.svg') }}" alt="icon">
                    </div>
                </div>


                <div class="col-xl-2 col-lg-1 col-md-6">
                    <div class="d-flex flex-column items-center text-center">
                        <div class="relative size-120 flex-center rounded-full bg-green-1">
                            <img src="{{ asset('front-assets/img/pages/become-expert/icons/2.png') }}" alt="image">

                            <div class="side-badge">
                                <div class="size-40 flex-center rounded-full bg-yellow-1">
                                    <span class="text-15 fw-500">02</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-18 fw-500 mt-30 sm:mt-20">Share Your Origin</div>
                    </div>
                </div>


                <div class="col-1 xl:d-none">
                    <div class="pt-30">
                        <img src="{{ asset('front-assets/img/pages/become-expert/lines/2.svg') }}" alt="icon">
                    </div>
                </div>


                <div class="col-xl-2 col-lg-1 col-md-6">
                    <div class="d-flex flex-column items-center text-center">
                        <div class="relative size-120 flex-center rounded-full bg-green-1">
                            <img src="{{ asset('front-assets/img/pages/become-expert/icons/3.png') }}" alt="image">

                            <div class="side-badge">
                                <div class="size-40 flex-center rounded-full bg-yellow-1">
                                    <span class="text-15 fw-500">03</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-18 fw-500 mt-30 sm:mt-20">Your Temple Sevayat</div>
                    </div>
                </div>


                <div class="col-1 xl:d-none">
                    <div class="pt-30">
                        <img src="{{ asset('front-assets/img/pages/become-expert/lines/1.svg') }}" alt="icon">
                    </div>
                </div>


                <div class="col-xl-2 col-lg-1 col-md-6">
                    <div class="d-flex flex-column items-center text-center">
                        <div class="relative size-120 flex-center rounded-full bg-green-1">
                            <img src="{{ asset('front-assets/img/pages/become-expert/icons/4.png') }}" alt="image">

                            <div class="side-badge">
                                <div class="size-40 flex-center rounded-full bg-yellow-1">
                                    <span class="text-15 fw-500">04</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-18 fw-500 mt-30 sm:mt-20">Book Your Visit</div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    {{-- <section class="layout-pt-md layout-pb-md">
        <div class="container">
            <div data-anim="slide-up delay-1" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Famous Pooja</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">These are few famous Pooja for you to do</p>
                    </div>
                </div>

                <div class="col-auto md:d-none">

                    <a href="#" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                        View All Temples <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
            </div>

            <div class="relative pt-40 sm:pt-20 js-section-slider" data-gap="30" data-scrollbar
                data-slider-cols="base-2 xl-4 lg-3 md-2 sm-2 base-1" data-anim="slide-up delay-2">
                <div class="swiper-wrapper">

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="img/destinations/1/1.png" alt="image" class="js-lazy">
                                <img src="{{ asset('front-assets/img/masthead/2/puja10.jpeg') }}" alt="image"
                                    class="js-lazy">

                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Balaram Pooja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/Pooja11.jpeg') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Rudrabhisek Pooja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/Pooja12.jpeg') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>

                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Janmastami Pooja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/Pooja14.jpeg') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Jagannath Pooja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/Pooja15.webp') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Hanuman Mela</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                </div>


                <button
                    class="section-slider-nav -prev flex-center button -blue-1 bg-white shadow-1 size-40 rounded-full sm:d-none js-prev">
                    <i class="icon icon-chevron-left text-12"></i>
                </button>

                <button
                    class="section-slider-nav -next flex-center button -blue-1 bg-white shadow-1 size-40 rounded-full sm:d-none js-next">
                    <i class="icon icon-chevron-right text-12"></i>
                </button>


                <div class="slider-scrollbar bg-light-2 mt-40 sm:d-none js-scrollbar"></div>

                <div class="row pt-20 d-none md:d-block">
                    <div class="col-auto">
                        <div class="d-inline-block">

                            <a href="#" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                                View All Temples <div class="icon-arrow-top-right ml-15"></div>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}


  
    <section class="layout-pt-lg layout-pb-lg bg-dark-3">
        <div class="container">
          <div class="row y-gap-40 justify-between text-white">
            <div class="col-xl-5 col-lg-6">
              <h2 class="text-30">What our customers are<br> saying us?</h2>
              <p class="text-white mt-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas varius tortor nibh, sit amet tempor nibh finibus et. Aenean eu enim justo.</p>
  
              <div class="row y-gap-30 pt-60 lg:pt-40">
                <div class="col-sm-5 col-6">
                  <div class="text-30 lh-15 fw-600">13m+</div>
                  <div class="lh-15">Happy People</div>
                </div>
  
                <div class="col-sm-5 col-6">
                  <div class="text-30 lh-15 fw-600">4.88</div>
                  <div class="lh-15">Overall rating</div>
  
                  <div class="d-flex x-gap-5 items-center pt-10">
  
                    <div class="icon-star text-white text-10"></div>
  
                    <div class="icon-star text-white text-10"></div>
  
                    <div class="icon-star text-white text-10"></div>
  
                    <div class="icon-star text-white text-10"></div>
  
                    <div class="icon-star text-white text-10"></div>
  
                  </div>
                </div>
              </div>
            </div>
  
            <div data-anim="slide-up delay-2" class="col-lg-6">
              <div class="overflow-hidden js-testimonials-slider-3" data-scrollbar>
                <div class="swiper-wrapper">
  
                  <div class="swiper-slide">
                    <div class="row items-center x-gap-30 y-gap-20">
                      <div class="col-auto">
                        <img src="img/avatars/1.png" alt="image">
                      </div>
  
                      <div class="col-auto">
                        <h5 class="text-16 text-white fw-500">Annette Black</h5>
                        <div class="text-15 text-white lh-15">UX / UI Designer</div>
                      </div>
                    </div>
  
                    <p class="text-18 fw-500 text-white mt-30 sm:mt-20">The place is in a great location in Gumbet. The area is safe and beautiful. The apartment was comfortable and the host was kind and responsive to our requests.</p>
                  </div>
  
                  <div class="swiper-slide">
                    <div class="row items-center x-gap-30 y-gap-20">
                      <div class="col-auto">
                        <img src="img/avatars/1.png" alt="image">
                      </div>
  
                      <div class="col-auto">
                        <h5 class="text-16 text-white fw-500">Annette Black</h5>
                        <div class="text-15 text-white lh-15">UX / UI Designer</div>
                      </div>
                    </div>
  
                    <p class="text-18 fw-500 text-white mt-30 sm:mt-20">The place is in a great location in Gumbet. The area is safe and beautiful. The apartment was comfortable and the host was kind and responsive to our requests.</p>
                  </div>
  
                  <div class="swiper-slide">
                    <div class="row items-center x-gap-30 y-gap-20">
                      <div class="col-auto">
                        <img src="img/avatars/1.png" alt="image">
                      </div>
  
                      <div class="col-auto">
                        <h5 class="text-16 text-white fw-500">Annette Black</h5>
                        <div class="text-15 text-white lh-15">UX / UI Designer</div>
                      </div>
                    </div>
  
                    <p class="text-18 fw-500 text-white mt-30 sm:mt-20">The place is in a great location in Gumbet. The area is safe and beautiful. The apartment was comfortable and the host was kind and responsive to our requests.</p>
                  </div>
  
                </div>
  
                <div class="d-flex items-center mt-60 sm:mt-20 js-testimonials-slider-pag">
                  <div class="text-white fw-500 js-current">01</div>
                  <div class="slider-scrollbar -light bg-white-10 ml-20 mr-20 w-max-300 js-scrollbar"></div>
                  <div class="text-white fw-500 js-all">05</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

    <section data-anim="slide-up delay-1" class="layout-pt-md layout-pb-md">
        <div class="container">
            <div class="row ml-0 mr-0 items-center justify-between">
                <div class="col-xl-5 px-0">
                    <img class="col-12 h-400" src="{{ asset('front-assets/img/newsletter/4.png') }}" alt="image">
                </div>

                <div class="col px-0">
                    <div class="d-flex justify-center flex-column h-400 px-80 py-40 md:px-30 bg-light-2">
                        <div class="icon-newsletter text-60 sm:text-40 text-dark-1"></div>
                        <h2 class="text-30 sm:text-24 lh-15 mt-20">Your Path to Spirituality Starts Here</h2>
                        <p class="text-dark-1 mt-5">Sign up and you will get info of famous temples</p>

                        <div class="row single-field -w-410 d-flex x-gap-10 flex-wrap y-gap-20 pt-30">
                            <div class="col-auto" style="width: 100%">
                                <input class="col-12 bg-white h-60" type="text" placeholder="Your Email">
                            </div>

                            <div class="col-auto">
                                <button class="button -md h-60 -blue-1 bg-yellow-1 text-dark-1">Subscribe</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section data-anim-wrap class="section-bg pt-80 pb-80 md:pt-40 md:pb-40">


        <div class="container">
            <div class="row y-gap-30 items-center justify-between">
                <div data-anim-child="slide-up delay-2" class="col-xl-5 col-lg-6">
                    <h2 class="text-30 lh-15">Download the App</h2>
                    <p class="text-dark-1 pr-40 lg:pr-0 mt-15 sm:mt-5">Book in advance or last-minute with 33Crores.
                        Receive instant confirmation. Access your booking info offline.</p>

                    <div class="row y-gap-20 items-center pt-30 sm:pt-10">
                        <div class="col-auto">
                            <div class="d-flex items-center px-20 py-10 rounded-8 border-white-15 text-white bg-dark-3">
                                <div class="icon-apple text-24"></div>
                                <div class="ml-20">
                                    <div class="text-14">Download on the</div>
                                    <div class="text-15 lh-1 fw-500">Apple Store</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto">
                            <a href="https://play.google.com/store/apps/details?id=com.croresadmin.shopifyapp"
                                target="_blank"
                                class="d-flex items-center px-20 py-10 rounded-8 border-white-15 text-white bg-dark-3">
                                <div class="icon-play-market text-24"></div>
                                <div class="ml-20">

                                    <div class="text-14">Get in on</div>
                                    <div class="text-15 lh-1 fw-500">Google Play</div>

                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div data-anim-child="slide-up delay-3" class="col-lg-6">
                    <img src="{{ asset('front-assets/img/app/1.png') }}" alt="image">
                </div>
            </div>
        </div>
    </section>


    <section class="layout-pt-md layout-pb-md bg-blue-2">
        <div class="container">
            <div class="row justify-between">

                <div class="col-lg-3 col-sm-6">

                    <div class="featureIcon -type-1 ">
                        <div class="d-flex justify-center">
                            <img src="{{ asset('front-assets/img/featureIcons/1/1.svg') }}" alt="image">
                        </div>

                        <div class="text-center mt-30">
                            <h4 class="text-18 fw-500">Best Service Guarantee</h4>
                        </div>
                    </div>

                </div>

                <div class="col-lg-3 col-sm-6">

                    <div class="featureIcon -type-1 ">
                        <div class="d-flex justify-center">
                            <img src="{{ asset('front-assets/img/featureIcons/1/2.svg') }}" alt="image">
                        </div>

                        <div class="text-center mt-30">
                            <h4 class="text-18 fw-500">Easy & Quick Booking</h4>

                        </div>
                    </div>

                </div>

                <div class="col-lg-3 col-sm-6">

                    <div class="featureIcon -type-1 ">
                        <div class="d-flex justify-center">
                            <img src="{{ asset('front-assets/img/featureIcons/1/3.svg') }}" alt="image">
                        </div>
                        <div class="text-center mt-30">
                            <h4 class="text-18 fw-500">24/7 Guide on Call</h4>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
@endsection

@section('scripts')

@endsection