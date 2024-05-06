@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
    <!-- Main Section -->
    <section data-anim-wrap class="masthead -type-2 js-mouse-move-container">
        <div class="masthead__bg bg-dark-3">
            <img src="{{ asset('front-assets/img/masthead/2/mayura1.jpg') }}" alt="image">
        </div>

        <div class="container">

            <div class="masthead__content">
                <div class="row y-gap-40">
                    <div class="col-xl-5">
                        <h1 data-anim-child="slide-up delay-2"
                            class="z-2 text-60 lg:text-40 text-white md:text-30 pt-80 xl:pt-0">
                            <span class="text-white-1"> At which location</span><br>
                            Did you call the Pandit??
                        </h1>

                        <p data-anim-child="slide-up delay-3" class="z-2 text-white mt-20">Search Location Here!!</p>

                        <div data-anim-child="slide-up delay-4"
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
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="masthead__images">
                            <div data-anim-child="slide-up delay-6"><img
                                    src="{{ asset('front-assets/img/masthead/2/alati.webp') }}" alt="image"></div>
                            <div data-anim-child="slide-up delay-7"><img
                                    src="{{ asset('front-assets/img/masthead/2/puja2.jpeg') }}" alt="image"></div>
                            <div data-anim-child="slide-up delay-8"><img
                                    src="{{ asset('front-assets/img/masthead/2/puja3.jpeg') }}" alt="image"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section data-anim="slide-up delay-1" class="layout-pt-md layout-pb-md">
        <div class="container">
            <div class="row ml-0 mr-0 items-center justify-between">
                <div class="col-xl-3 px-0" style= "margin-right: 30px">
                    <img class="col-12 h-400" src="{{ asset('front-assets/img/masthead/2/krishna.jpeg') }}" alt="image">
                </div>

                <div class="col px-0">
                    <div class="d-flex justify-center flex-column h-400 px-80 py-40 md:px-30 bg-light-2"
                        style="padding-left: 20px;"> <!-- Added padding-left -->
                        <h2 class="text-30 sm:text-24 lh-15 mt-20">Janmastami Puja Starts from Here</h2>
                        <p class="text-dark-1 mt-5">Select your Pandit who will be with you from start to end of your Puja
                        </p>

                        <div class="row mt-20">
                            <div class="col-2">
                                <a href=""><img src="{{ asset('front-assets/img/avatars/11.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""><img src="{{ asset('front-assets/img/avatars/12.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""><img src="{{ asset('front-assets/img/avatars/13.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""><img src="{{ asset('front-assets/img/avatars/14.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""><img src="{{ asset('front-assets/img/avatars/15.png') }}"
                                        alt="image"></a>
                            </div>
                        </div>

                      

                        <div class="single-field -w-410 d-flex x-gap-10 flex-wrap y-gap-20 pt-30">
             
                          <div class="">
                            <a href="lingaraj-temple-sevayat.html" class="button -md h-60 -blue-1 bg-yellow-1 text-dark-1">Select
                              Pandit for
                              Krishna Puja</a>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section data-anim="slide-up delay-1" class="layout-pt-md layout-pb-md">
        <div class="container">
            <div class="row ml-0 mr-0 items-center justify-between">


                <div class="col px-0" >
                    <div class="d-flex justify-center flex-column h-400 px-80 py-40 md:px-30 bg-light-2">

                        <h2 class="text-30 sm:text-24 lh-15 mt-20">Ganesh Puja Starts from Here</h2>
                        <p class="text-dark-1 mt-5">Select your Pandit who will be with you from start to end of your Puja
                        </p>

                        <div class="row mt-20">
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/11.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/12.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/13.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/14.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/15.png') }}"
                                        alt="image"></a>
                            </div>
                        </div>
                        <div class="single-field -w-410 d-flex x-gap-10 flex-wrap y-gap-20 pt-30">
                            <div class="">
                                <a href="jagannath-temple-sevayat.html"
                                    class="button -md h-60 -blue-1 bg-yellow-1 text-dark-1">Select
                                    Pandit for
                                    Ganesh Puja</a>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="col-xl-3 px-0">
                    <img class="col-12 h-400" src="{{ asset('front-assets/img/masthead/2/ganesh2.webp') }}"
                        alt="image">
                </div>

            </div>
        </div>
    </section>

    <section data-anim="slide-up delay-1" class="layout-pt-md layout-pb-md">
        <div class="container">
            <div class="row ml-0 mr-0 items-center justify-between">
                <div class="col-xl-3 px-0" style = "margin-right: 30px">
                    <img class="col-12 h-400" src="{{ asset('front-assets/img/masthead/2/durga1.webp') }}"
                        alt="image">
                </div>

                <div class="col px-0">
                    <div class="d-flex justify-center flex-column h-400 px-80 py-40 md:px-30 bg-light-2">

                        <h2 class="text-30 sm:text-24 lh-15 mt-20">Durga Puja Starts from Here</h2>
                        <p class="text-dark-1 mt-5">Select your Pandit who will be with you from start to end of your Puja
                        </p>


                        <div class="row mt-20">
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/11.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/12.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/13.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/14.png') }}"
                                        alt="image"></a>
                            </div>
                            <div class="col-2">
                                <a href=""> <img src="{{ asset('front-assets/img/avatars/15.png') }}"
                                        alt="image"></a>
                            </div>
                        </div>
                        <div class="resp">
                            <div class="single-field -w-410 d-flex x-gap-10 flex-wrap y-gap-20 pt-30">
                                <div class="">
                                    <a href="jagannath-temple-sevayat.html"
                                        class="button -md h-60 -blue-1 bg-yellow-1 text-dark-1" >Select
                                        Pandit for
                                        Durga Puja</a>
                                </div>
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

    <section class="layout-pt-md layout-pb-md">
        <div class="container">
            <div data-anim="slide-up delay-1" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Famous Puja</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">These are few famous puja for you to do</p>
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
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Balaram Puja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/puja11.jpeg') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Rudrabhisek Puja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/puja12.jpeg') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>

                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Janmastami Puja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/puja14.jpeg') }}"
                                    alt="image" class="js-lazy">
                            </div>

                            <div
                                class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                                <div class="citiesCard__bg"></div>

                                <div class="citiesCard__top">
                                    <div class="text-14 text-white">Opening Time 6AM , Closing Time 8PM</div>
                                </div>

                                <div class="citiesCard__bottom">
                                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20">Jagannath Puja</h4>
                                    <button class="button col-12 h-60 -blue-1 bg-white text-dark-1">Explore Online</button>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="swiper-slide">

                        <a href="#" class="citiesCard -type-1 d-block rounded-4 ">
                            <div class="citiesCard__image ratio ratio-3:4">
                                <img src="#" data-src="{{ asset('front-assets/img/masthead/2/puja15.webp') }}"
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
    </section>


    <section class="layout-pt-md layout-pb-lg">
        <div data-anim-wrap class="container">
            <div class="tabs -pills-2 js-tabs">
                <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-between items-end">
                    <div class="col-auto">
                        <div class="sectionTitle -md">
                            <h2 class="sectionTitle__title">Famous Pandits</h2>
                            <p class=" sectionTitle__text mt-5 sm:mt-0">Some predefined packages for your Thirt Yatra</p>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="tabs__controls row x-gap-10 justify-center js-tabs-controls">

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button is-tab-el-active"
                                    data-tab-target=".-tab-item-1">Shiva Temple</button>
                            </div>

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button "
                                    data-tab-target=".-tab-item-2">Maa Durga</button>
                            </div>

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button "
                                    data-tab-target=".-tab-item-3">Vishnu Bhagwan</button>
                            </div>

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button "
                                    data-tab-target=".-tab-item-4">Surya Devta</button>
                            </div>

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button "
                                    data-tab-target=".-tab-item-5">Ganesh </button>
                            </div>

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button "
                                    data-tab-target=".-tab-item-6">Kartikayen</button>
                            </div>

                            <div class="col-auto">
                                <button
                                    class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button "
                                    data-tab-target=".-tab-item-7">Hanuman</button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="tabs__content pt-40 js-tabs-content">

                    <div class="tabs__pane -tab-item-1 is-tab-el-active">
                        <div class="row y-gap-30">

                            <div data-anim-child="slide-left delay-4" class="col-xl-3 col-lg-3 col-sm-6">

                                <a href="#" class="hotelsCard -type-1 ">
                                    <div class="hotelsCard__image">

                                        <div class="cardImage ratio ratio-1:1">
                                            <div class="cardImage__content">

                                                <img class="rounded-4 col-12 js-lazy" src="#"
                                                    data-src="{{ asset('front-assets/img/avatars/11.png') }}"
                                                    alt="image">
                                                {{-- <img src="" alt="image"> --}}

                                            </div>

                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>


                                            <div class="cardImage__leftBadge">
                                                <div
                                                    class="py-5 px-15 rounded-right-4 text-12 lh-16 fw-500 uppercase bg-dark-1 text-white">
                                                    Bhog included
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="hotelsCard__content mt-10">
                                        <h4 class="hotelsCard__title text-dark-1 text-18 lh-16 fw-500">
                                            <span>Maa Sarala Temple</span>
                                        </h4>

                                        <p class="text-light-1 lh-14 text-14 mt-5">Jhankada, Jagatsinghpur</p>

                                        <div class="d-flex items-center mt-20">
                                            <div class="flex-center bg-blue-1 rounded-4 size-30 text-12 fw-600 text-white">
                                                4.8</div>
                                            <div class="text-14 text-dark-1 fw-500 ml-10">Exceptional</div>
                                            <div class="text-14 text-light-1 ml-10">3,014 reviews</div>
                                        </div>

                                        <div class="mt-5">
                                            <div class="fw-500">
                                                Starting from <span class="text-blue-1">INR 250</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            </div>

                            <div data-anim-child="slide-left delay-5" class="col-xl-3 col-lg-3 col-sm-6">

                                <a href="#" class="hotelsCard -type-1 ">
                                    <div class="hotelsCard__image">

                                        <div class="cardImage ratio ratio-1:1">
                                            <div class="cardImage__content">


                                                <div
                                                    class="cardImage-slider rounded-4 overflow-hidden js-cardImage-slider">
                                                    <div class="swiper-wrapper">

                                                        <div class="swiper-slide">
                                                            <img class="col-12 js-lazy" src="#"
                                                                data-src="{{ asset('front-assets/img/avatars/12.png') }}"
                                                                alt="image">
                                                        </div>

                                                        <div class="swiper-slide">
                                                            <img class="col-12 js-lazy" src="#"
                                                                data-src="{{ asset('front-assets/img/avatars/12.png') }}"
                                                                alt="image">
                                                        </div>

                                                        <div class="swiper-slide">
                                                            <img class="col-12 js-lazy" src="#"
                                                                data-src="{{ asset('front-assets/img/avatars/12.png') }}"
                                                                alt="image">
                                                        </div>

                                                    </div>

                                                    <div class="cardImage-slider__pagination js-pagination"></div>

                                                    <div class="cardImage-slider__nav -prev">
                                                        <button
                                                            class="button -blue-1 bg-white size-30 rounded-full shadow-2 js-prev">
                                                            <i class="icon-chevron-left text-10"></i>
                                                        </button>
                                                    </div>

                                                    <div class="cardImage-slider__nav -next">
                                                        <button
                                                            class="button -blue-1 bg-white size-30 rounded-full shadow-2 js-next">
                                                            <i class="icon-chevron-right text-10"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>


                                        </div>

                                    </div>

                                    <div class="hotelsCard__content mt-10">
                                        <h4 class="hotelsCard__title text-dark-1 text-18 lh-16 fw-500">
                                            <span>AkhandalaMani</span>
                                        </h4>

                                        <p class="text-light-1 lh-14 text-14 mt-5">Bhadrak, Odisha</p>

                                        <div class="d-flex items-center mt-20">
                                            <div class="flex-center bg-blue-1 rounded-4 size-30 text-12 fw-600 text-white">
                                                4.8</div>
                                            <div class="text-14 text-dark-1 fw-500 ml-10">Exceptional</div>
                                            <div class="text-14 text-light-1 ml-10">3,014 reviews</div>
                                        </div>

                                        <div class="mt-5">
                                            <div class="fw-500">
                                                Starting from <span class="text-blue-1">INR 200</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            </div>

                            <div data-anim-child="slide-left delay-6" class="col-xl-3 col-lg-3 col-sm-6">

                                <a href="#" class="hotelsCard -type-1 ">
                                    <div class="hotelsCard__image">

                                        <div class="cardImage ratio ratio-1:1">
                                            <div class="cardImage__content">

                                                <img class="rounded-4 col-12 js-lazy" src="#"
                                                    data-src="{{ asset('front-assets/img/avatars/15.png') }}"
                                                    alt="image">


                                            </div>

                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>


                                            <div class="cardImage__leftBadge">
                                                <div
                                                    class="py-5 px-15 rounded-right-4 text-12 lh-16 fw-500 uppercase bg-blue-1 text-white">
                                                    Famous for Coconut Bhog
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="hotelsCard__content mt-10">
                                        <h4 class="hotelsCard__title text-dark-1 text-18 lh-16 fw-500">
                                            <span>Maa Tarini Temple</span>
                                        </h4>

                                        <p class="text-light-1 lh-14 text-14 mt-5">Ghata Gaan, Keonjhar</p>

                                        <div class="d-flex items-center mt-20">
                                            <div class="flex-center bg-blue-1 rounded-4 size-30 text-12 fw-600 text-white">
                                                4.8</div>
                                            <div class="text-14 text-dark-1 fw-500 ml-10">Exceptional</div>
                                            <div class="text-14 text-light-1 ml-10">3,014 reviews</div>
                                        </div>

                                        <div class="mt-5">
                                            <div class="fw-500">
                                                Starting from <span class="text-blue-1">INR 700</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            </div>

                            <div data-anim-child="slide-left delay-7" class="col-xl-3 col-lg-3 col-sm-6">

                                <a href="#" class="hotelsCard -type-1 ">
                                    <div class="hotelsCard__image">

                                        <div class="cardImage ratio ratio-1:1">
                                            <div class="cardImage__content">

                                                <img class="rounded-4 col-12 js-lazy" src="#"
                                                    data-src="{{ asset('front-assets/img/avatars/14.png') }}"
                                                    alt="image">


                                            </div>

                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>


                                            <div class="cardImage__leftBadge">
                                                <div
                                                    class="py-5 px-15 rounded-right-4 text-12 lh-16 fw-500 uppercase bg-yellow-1 text-dark-1">
                                                    Hill and Island View
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="hotelsCard__content mt-10">
                                        <h4 class="hotelsCard__title text-dark-1 text-18 lh-16 fw-500">
                                            <span>Baba Dhabaleswar Shiva</span>
                                        </h4>

                                        <p class="text-light-1 lh-14 text-14 mt-5">Dhabaleswar, Athagarh</p>

                                        <div class="d-flex items-center mt-20">
                                            <div class="flex-center bg-blue-1 rounded-4 size-30 text-12 fw-600 text-white">
                                                4.8</div>
                                            <div class="text-14 text-dark-1 fw-500 ml-10">Exceptional</div>
                                            <div class="text-14 text-light-1 ml-10">3,014 reviews</div>
                                        </div>

                                        <div class="mt-5">
                                            <div class="fw-500">
                                                Starting from <span class="text-blue-1">INR 350</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="layout-pt-lg layout-pb-lg bg-dark-3">
        <div class="container">
            <div class="row y-gap-60">
                <div class="col-xl-5 col-lg-6">
                    <h2 class="text-30 text-white">What is our service</h2>
                    <p class="text-white mt-20">We are using Technology to revive the hundreds of years old method of
                        having a peaceful and happy darshan for the religious travellers .</p>

                    <h2 class="text-30 text-white">What is our service</h2>

                    <div class="row y-gap-30 text-white pt-60 lg:pt-40">
                        <div class="col-sm-5 col-6">
                            <div class="text-30 lh-15 fw-600">100000</div>
                            <div class="lh-15">Happy Spiritual Tourist</div>
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

                <div class="col-xl-4 offset-xl-2 col-lg-5 offset-lg-1 col-md-10">


                    <div class="testimonials-slider-2 js-testimonials-slider-2">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40 shadow-2">
                                    <div class="">
                                        <h4 class="text-16 fw-500 text-blue-1 mb-20">Traveller from Karnataka</h4>
                                        <p class="testimonials__text lh-18 fw-500 text-dark-1">&quot;Our family was
                                            traveling to Odisha and we had no idea of getting a good visit to Puri. Then we
                                            came across 33Crores. They made the visit so easy and the religious trip was
                                            fantastic.&quot;</p>

                                        <div class="pt-20 mt-28 border-top-light">
                                            <div class="row x-gap-20 y-gap-20 items-center">
                                                <div class="col-auto">
                                                </div>

                                                <div class="col-auto">
                                                    <div class="text-15 fw-500 lh-14">Venky A</div>
                                                    <div class="text-14 lh-14 text-light-1 mt-5">Software Engineer</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40 shadow-2">
                                    <div class="">
                                        <h4 class="text-16 fw-500 text-blue-1 mb-20">Assamese Traveller</h4>
                                        <p class="testimonials__text lh-18 fw-500 text-dark-1">&quot;We wanted to have a
                                            visit to Puri Temple, some one told us about 33Crores. It was really magical to
                                            know that we had a dedicated Sebayat for our city, who took all of us around the
                                            temple and we were delighted to see our ancestors visits to the Devine
                                            place&quot;</p>

                                        <div class="pt-20 mt-28 border-top-light">
                                            <div class="row x-gap-20 y-gap-20 items-center">
                                                <div class="col-auto">
                                                </div>

                                                <div class="col-auto">
                                                    <div class="text-15 fw-500 lh-14">Milan Kumar</div>
                                                    <div class="text-14 lh-14 text-light-1 mt-5">Business Man</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40 shadow-2">
                                    <div class="">
                                        <h4 class="text-16 fw-500 text-blue-1 mb-20">Tarveller from Madurai</h4>
                                        <p class="testimonials__text lh-18 fw-500 text-dark-1">&quot;We reached Lingaraj
                                            Temple a Priest was ready to guide us and showed us the temple. We had heard
                                            priest of Odisha were harsh but its not true. We felt as if we were the part of
                                            the Culture. Infact the online service of 33 Crores is the best and should be
                                            implemeted in all temples across India &quot;</p>

                                        <div class="pt-20 mt-28 border-top-light">
                                            <div class="row x-gap-20 y-gap-20 items-center">
                                                <div class="col-auto">
                                                </div>

                                                <div class="col-auto">
                                                    <div class="text-15 fw-500 lh-14">R Swamy</div>
                                                    <div class="text-14 lh-14 text-light-1 mt-5">Hotel Owner</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="d-flex x-gap-15 items-center justify-center pt-30">
                            <div class="col-auto">
                                <button class="d-flex items-center text-24 arrow-left-hover text-white js-prev">
                                    <i class="icon icon-arrow-left"></i>
                                </button>
                            </div>

                            <div class="col-auto">
                                <div class="pagination -dots text-white-50 js-pagination"></div>
                            </div>

                            <div class="col-auto">
                                <button class="d-flex items-center text-24 arrow-right-hover text-white js-next">
                                    <i class="icon icon-arrow-right"></i>
                                </button>
                            </div>
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