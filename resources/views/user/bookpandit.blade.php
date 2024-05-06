@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
    <section class="pt-40 pb-40 bg-light-2">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <h1 class="text-30 fw-600">Find Your Pandit By Location And Puja</h1>
                        <br>
                        <!-------------------------------Filter Code--------------------------------->

                        <form>
                            <div class="row text-center">
                                <div class="form-group col-4  mb-3">
                                    <select
                                        class="form-control  form-input lh-1 text-18 text-center -outline-blue-1 h-50 ml-20"
                                        id="inputState"
                                        style=" border-color: var(--color-blue-1);border: 1px solid var(--color-border);
      border-radius: 4px;
      padding: 0;
      min-height: 70px;
      transition: all .2s cubic-bezier(.165,.84,.44,1);">
                                        <option value="SelectState">Select Location</option>
                                        {{-- <option value="Andra Pradesh">Andra Pradesh</option>
                          <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                          <option value="Assam">Assam</option>
                          <option value="Bihar">Bihar</option>
                          <option value="Chhattisgarh">Chhattisgarh</option>
                          <option value="Goa">Goa</option>
                          <option value="Gujarat">Gujarat</option>
                          <option value="Haryana">Haryana</option>
                          <option value="Himachal Pradesh">Himachal Pradesh</option>
                          <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                          <option value="Jharkhand">Jharkhand</option>
                          <option value="Karnataka">Karnataka</option>
                          <option value="Kerala">Kerala</option>
                          <option value="Madya Pradesh">Madya Pradesh</option>
                          <option value="Maharashtra">Maharashtra</option>
                          <option value="Manipur">Manipur</option>
                          <option value="Meghalaya">Meghalaya</option>
                          <option value="Mizoram">Mizoram</option>
                          <option value="Nagaland">Nagaland</option>
                          <option value="Odisha">Odisha</option>
                          <option value="Punjab">Punjab</option>
                          <option value="Rajasthan">Rajasthan</option>
                          <option value="Sikkim">Sikkim</option>
                          <option value="Tamil Nadu">Tamil Nadu</option>
                          <option value="Telangana">Telangana</option>
                          <option value="Tripura">Tripura</option>
                          <option value="Uttaranchal">Uttaranchal</option>
                          <option value="Uttar Pradesh">Uttar Pradesh</option>
                          <option value="West Bengal">West Bengal</option>
                          <option disabled style="background-color:#aaa; color:#fff">UNION Territories</option>
                          <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                          <option value="Chandigarh">Chandigarh</option>
                          <option value="Dadar and Nagar Haveli">Dadar and Nagar Haveli</option>
                          <option value="Daman and Diu">Daman and Diu</option>
                          <option value="Delhi">Delhi</option>
                          <option value="Lakshadeep">Lakshadeep</option>
                          <option value="Pondicherry">Pondicherry</option> --}}
                                    </select>
                                </div>
                                <div class="form-group col-4  mb-3 form-input">
                                    <select class="form-control lh-1 text-18 text-center -outline-blue-1 h-50 ml-20"
                                        id="inputDistrict"
                                        style=" border-color: var(--color-blue-1);border: 1px solid var(--color-border);
      border-radius: 4px;
      padding: 0;
      min-height: 70px;
      transition: all .2s cubic-bezier(.165,.84,.44,1);">
                                        <option value="">--Select Pandit -- </option>
                                        <option value=" Mettupalayam"> P.Bibhu ranjan Nanda</option>
                                        <option value=" Mettupalayam"> P.Jyoti ranjan Dash</option>
                                        <option value=" Mettupalayam"> P.Hare krushna Nanda</option>
                                        <option value=" Mettupalayam"> P.Prabhakar</option>
                                        <option value=" Mettupalayam"> P.Ullas</option>

                                    </select>
                                </div>
                                <div class="form-group col-4  mb-3 form-input">
                                    <select class="form-control lh-1 text-18 text-center -outline-blue-1 h-50 ml-20"
                                        id="inputCity"
                                        style=" border-color: var(--color-blue-1);border: 1px solid var(--color-border);
      border-radius: 4px;
      padding: 0;
      min-height: 70px;
      transition: all .2s cubic-bezier(.165,.84,.44,1);">
                                        <option value="">--Select Puja -- </option>
                                        <option value=" Mettupalayam"> Ganesh Puja</option>
                                        <option value=" Mettupalayam"> Kali Puja</option>
                                        <option value=" Mettupalayam"> Durga Puja</option>
                                        <option value=" Mettupalayam"> Shiva Puja</option>
                                        <option value=" Mettupalayam"> Saraswati Puja</option>
                                        <option value=" Mettupalayam"> Hanuman Puja</option>



                                    </select>
                                </div>
                            </div>
                        </form>

                        <!-------------------------------End---------------------------------------->

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="layout-pt-md layout-pb-lg">
        <div class="container">
            <div class="row y-gap-30">

                <div class="col-xl-12 col-lg-12">
                    <div class="row y-gap-30">

                        <div class="col-12">

                            <div class="border-top-light pt-30">
                                <div class="row x-gap-20 y-gap-20">
                                    <div class="col-md-auto">

                                        <div class="cardImage ratio ratio-1:1 w-250 md:w-1/1 rounded-4">
                                            <div class="cardImage__content">

                                                <img class="rounded-4 col-12 js-lazy" src="#"
                                                    data-src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}"
                                                    alt="image">


                                            </div>

                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>


                                        </div>

                                    </div>

                                    <div class="col-md">
                                        <h3 class="text-18 lh-16 fw-500">
                                            Krupasindhu<br class="lg:d-none"> Jagannatha Temple, Puri

                                            <div class="d-inline-block ml-10">
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                            </div>
                                        </h3>
                                        <div class="text-14 text-green-2 lh-15 mt-10">
                                            <div class="fw-500">Free cancellation</div>
                                            <div class="">You can cancel later, so lock in this great price today.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-auto text-right md:text-left">
                                        <div class="row x-gap-10 y-gap-10 justify-end items-center md:justify-start">
                                            <div class="col-auto">
                                                <div class="text-14 lh-14 fw-500">Exceptional</div>
                                                <div class="text-14 lh-14 text-light-1">3,014 reviews</div>
                                            </div>
                                            <div class="col-auto">
                                                <div
                                                    class="flex-center text-white fw-600 text-14 size-40 rounded-4 bg-blue-1">
                                                    4.8</div>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="text-14 text-light-1 mt-50 md:mt-20"></div>
                                            <div class="text-22 lh-12 fw-600 mt-5">INR 300</div>
                                            <div class="text-14 text-light-1 mt-5"></div>


                                            <a href="https://rzp.io/l/sebayat"
                                                class="button -md -dark-1 bg-blue-1 text-white mt-24">
                                                Book Now <div class="icon-arrow-top-right ml-15"></div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>



                        <div class="col-12">

                            <div class="border-top-light pt-30">
                                <div class="row x-gap-20 y-gap-20">
                                    <div class="col-md-auto">

                                        <div class="cardImage ratio ratio-1:1 w-250 md:w-1/1 rounded-4">
                                            <div class="cardImage__content">

                                                <img class="rounded-4 col-12 js-lazy" src="#"
                                                    data-src="{{ asset('front-assets/img/avatars/pandit1.jpeg') }}"
                                                    alt="image">


                                            </div>

                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>


                                        </div>

                                    </div>

                                    <div class="col-md">
                                        <h3 class="text-18 lh-16 fw-500">
                                            Gangadhar Pratihari<br class="lg:d-none">Jagannatha Temple, Puri

                                            <div class="d-inline-block ml-10">
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                            </div>
                                        </h3>
                                        <div class="text-14 text-green-2 lh-15 mt-10">
                                            <div class="fw-500">Free cancellation</div>
                                            <div class="">You can cancel later, so lock in this great price today.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-auto text-right md:text-left">
                                        <div class="row x-gap-10 y-gap-10 justify-end items-center md:justify-start">
                                            <div class="col-auto">
                                                <div class="text-14 lh-14 fw-500">Exceptional</div>
                                                <div class="text-14 lh-14 text-light-1">3,014 reviews</div>
                                            </div>
                                            <div class="col-auto">
                                                <div
                                                    class="flex-center text-white fw-600 text-14 size-40 rounded-4 bg-blue-1">
                                                    4.8</div>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="text-14 text-light-1 mt-50 md:mt-20"></div>
                                            <div class="text-22 lh-12 fw-600 mt-5">INR 300</div>
                                            <div class="text-14 text-light-1 mt-5"></div>


                                            <a href="https://rzp.io/l/sebayat"
                                                class="button -md -dark-1 bg-blue-1 text-white mt-24">
                                                Book Now <div class="icon-arrow-top-right ml-15"></div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">

                            <div class="border-top-light pt-30">
                                <div class="row x-gap-20 y-gap-20">
                                    <div class="col-md-auto">

                                        <div class="cardImage ratio ratio-1:1 w-250 md:w-1/1 rounded-4">
                                            <div class="cardImage__content">


                                                <div class=" rounded-4 overflow-hidden js-cardImage-slider">
                                                    <div class="cardImage__content">

                                                        <img class="rounded-4 col-12 js-lazy" src="#"
                                                            data-src="{{ asset('front-assets/img/avatars/pandit2.jpeg') }}"
                                                            alt="image">


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

                                    <div class="col-md">
                                        <h3 class="text-18 lh-16 fw-500">
                                            Raju<br class="lg:d-none"> Lingaraj Temple, Bhubaneswar

                                            <div class="d-inline-block ml-10">
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                            </div>
                                        </h3>
                                        <div class="text-14 text-green-2 lh-15 mt-10">
                                            <div class="fw-500">Free cancellation</div>
                                            <div class="">You can cancel later, so lock in this great price today.
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-auto text-right md:text-left">
                                        <div class="row x-gap-10 y-gap-10 justify-end items-center md:justify-start">
                                            <div class="col-auto">
                                                <div class="text-14 lh-14 fw-500">Exceptional</div>
                                                <div class="text-14 lh-14 text-light-1">3,014 reviews</div>
                                            </div>
                                            <div class="col-auto">
                                                <div
                                                    class="flex-center text-white fw-600 text-14 size-40 rounded-4 bg-blue-1">
                                                    4.8</div>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="text-14 text-light-1 mt-50 md:mt-20"></div>
                                            <div class="text-22 lh-12 fw-600 mt-5">INR 300</div>
                                            <div class="text-14 text-light-1 mt-5"></div>


                                            <a href="https://rzp.io/l/sebayat"
                                                class="button -md -dark-1 bg-blue-1 text-white mt-24">
                                                Book Now <div class="icon-arrow-top-right ml-15"></div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-12">

                            <div class="border-top-light pt-30">
                                <div class="row x-gap-20 y-gap-20">
                                    <div class="col-md-auto">
                                        <div class="cardImage ratio ratio-1:1 w-250 md:w-1/1 rounded-4">
                                            <div class="cardImage__content">
                                                <img class="rounded-4 col-12 js-lazy" src="#"
                                                    data-src="{{ asset('front-assets/img/avatars/pandit4.jpeg') }}"
                                                    alt="image">
                                            </div>
                                            <div class="cardImage__wishlist">
                                                <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                                                    <i class="icon-heart text-12"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <h3 class="text-18 lh-16 fw-500">
                                            Subharaj Batu<br class="lg:d-none"> Lingaraj Temple, Bhubaneswar
                                            <div class="d-inline-block ml-10">
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                            </div>
                                        </h3>
                                        <div class="text-14 text-green-2 lh-15 mt-10">
                                            <div class="fw-500">Free cancellation</div>
                                            <div class="">You can cancel later, so lock in this great price today.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-auto text-right md:text-left">
                                        <div class="row x-gap-10 y-gap-10 justify-end items-center md:justify-start">
                                            <div class="col-auto">
                                                <div class="text-14 lh-14 fw-500">Exceptional</div>
                                                <div class="text-14 lh-14 text-light-1">3,014 reviews</div>
                                            </div>
                                            <div class="col-auto">
                                                <div
                                                    class="flex-center text-white fw-600 text-14 size-40 rounded-4 bg-blue-1">
                                                    4.8</div>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="text-14 text-light-1 mt-50 md:mt-20"></div>
                                            <div class="text-22 lh-12 fw-600 mt-5">INR 300</div>
                                            <div class="text-14 text-light-1 mt-5"></div>

                                            <a href="https://rzp.io/l/sebayat"
                                                class="button -md -dark-1 bg-blue-1 text-white mt-24">
                                                Book Now <div class="icon-arrow-top-right ml-15"></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="border-top-light pt-30">
                                <div class="row x-gap-20 y-gap-20">
                                    <div class="col-md-auto">
                                        <div class="cardImage ratio ratio-1:1 w-250 md:w-1/1 rounded-4">
                                            <div class="cardImage__content">
                                                <div class=" rounded-4 overflow-hidden js-cardImage-slider">
                                                    <div class="cardImage__content">
                                                        <img class="rounded-4 col-12 js-lazy" src="#"
                                                            data-src="{{ asset('front-assets/img/avatars/pandit5.jpeg') }}"
                                                            alt="image">
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

                                    <div class="col-md">
                                        <h3 class="text-18 lh-16 fw-500">
                                            Raju<br class="lg:d-none"> Biraja Temple, Jajpur

                                            <div class="d-inline-block ml-10">
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                                <i class="icon-star text-10 text-yellow-2"></i>
                                            </div>
                                        </h3>

                                        <div class="text-14 text-green-2 lh-15 mt-10">
                                            <div class="fw-500">Free cancellation</div>
                                            <div class="">You can cancel later, so lock in this great price today.
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-auto text-right md:text-left">
                                        <div class="row x-gap-10 y-gap-10 justify-end items-center md:justify-start">
                                            <div class="col-auto">
                                                <div class="text-14 lh-14 fw-500">Exceptional</div>
                                                <div class="text-14 lh-14 text-light-1">3,014 reviews</div>
                                            </div>
                                            <div class="col-auto">
                                                <div
                                                    class="flex-center text-white fw-600 text-14 size-40 rounded-4 bg-blue-1">
                                                    4.8</div>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="text-14 text-light-1 mt-50 md:mt-20"></div>
                                            <div class="text-22 lh-12 fw-600 mt-5">INR 300</div>
                                            <div class="text-14 text-light-1 mt-5"></div>


                                            <a href="https://rzp.io/l/sebayat"
                                                class="button -md -dark-1 bg-blue-1 text-white mt-24">
                                                Book Now <div class="icon-arrow-top-right ml-15"></div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="border-top-light mt-30 pt-30">
                        <div class="row x-gap-10 y-gap-20 justify-between md:justify-center">
                            <div class="col-auto md:order-1">
                                <button class="button -blue-1 size-40 rounded-full border-light">
                                    <i class="icon-chevron-left text-12"></i>
                                </button>
                            </div>

                            <div class="col-md-auto md:order-3">
                                <div class="row x-gap-20 y-gap-20 items-center md:d-none">

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">1</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full bg-dark-1 text-white">2</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">3</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full bg-light-2">4</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">5</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">...</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">20</div>

                                    </div>

                                </div>

                                <div class="row x-gap-10 y-gap-20 justify-center items-center d-none md:d-flex">

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">1</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full bg-dark-1 text-white">2</div>

                                    </div>

                                    <div class="col-auto">

                                        <div class="size-40 flex-center rounded-full">3</div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-auto md:order-2">
                                <button class="button -blue-1 size-40 rounded-full border-light">
                                    <i class="icon-chevron-right text-12"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection
