@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
    

 <section class="banner-bg">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <h1 data-aos="fade-up" data-aos-delay="500">Reliable Pandit Booking For Every <br> Religious Event</h1>
          <p data-aos="fade-up" data-aos-delay="500">Experienced Pandits For Every Occasion, Just a Click Away</p>
          <a href="{{url('pandit-list')}}" class="book-now-btn" data-aos="fade-up" data-aos-delay="500">Book Now</a>
          
        </div>
      </div>
    </div>
 </section>
 <section>
  <div class="container">
      <div class="row" style="margin-top:60px;    margin-bottom: 140px;">
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
              
              <div class="img-group">
                  <div class="img-group-inner">
                    <img src="{{asset('front-assets/img/about/about1.png')}}" alt="about">
                    <span></span>
                    <span></span>
                  </div>
                  <img src="{{asset('front-assets/img/about/about2.png')}}" alt="about">
                  <img src="{{asset('front-assets/img/about/about3.png')}}" alt="about">
              </div>
          </div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
              <div class="section-title mb-0 text-start">
                  <p class="subtitle">Bringing Tradition to Your Doorstep with Ease                  </p>
                  <h4 class="title">Your Trusted Source for Online Pandit Booking</h4>
              </div>
              <ul class="sigma_list list-2 mb-0">
                  <li>Pooja At Home</li>
                  <li>Special Poojas</li>
                  <li>Experienced Pandits</li>
                  <li>Pooja At Temple</li>
              </ul>
              <p class="blockquote bg-transparent"> Welcome to 33 Crores Pandit where we make it easy to book experienced and knowledgeable pandits for your spiritual and religious needs. Our mission is to connect you with trusted pandits who can perform rituals and ceremonies with the utmost authenticity and dedication. Whether it's a small puja or a grand celebration, we are here to ensure your religious events are conducted seamlessly and traditionally. Experience the convenience of online booking with the assurance of our commitment to quality and tradition.</p>
          </div>
      </div>
  </div>
</section>

    <section class="upcoming-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="upcoming-main-heading">
                        <h1>Upcoming Pooja Calendar</h1>
                        <p class="text-white">Discover and book upcoming pujas effortlessly with our online pandit booking service. Join us for spiritual ceremonies and secure your pandit today for a seamless experience.
                        </p>
                    </div>
                </div>
                <div class="col-md-12">
                     @foreach ($upcomingPoojas as $upcomingPooja)
                    <div class="upcoming-event" data-aos="fade-up" data-aos-delay="500">
                        <div class="row">
                            <div class="col-md-3">
                               
                                <div class="upcoming-event-img">
                                    <img src="{{asset('assets/img/'.$upcomingPooja->pooja_photo)}}" alt="Avatar" class="image">
                                   
                                </div>
                            </div>
                          
                            <div class="col-md-7">
                               <div class="event-text">
                                    <h4>{{$upcomingPooja->pooja_name}}</h4>
                                    <h6>{{$upcomingPooja->short_description}}</h6>
                                    <p><i class="fa fa-calendar-check-o" aria-hidden="true"></i>{{$upcomingPooja->pooja_date}}</p>
                               </div>
                            </div>
                            <div class="col-md-2">
                                <div class="event-info">
                                    <a href="{{ route('pooja.show', $upcomingPooja->slug) }}">Info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                  
                </div>
            </div>
        </div>
    </section>

 
  
   
    <section class="layout-pt-md layout-pb-md">
        <div class="container">
            <div data-anim="" data-aos="fade-up" class="row y-gap-20 mb-30 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title"> Expert Pandits                        </h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">Connect with our top-rated pandits, celebrated for their wisdom and skill. Enhance your spiritual practices with their guidance.
                        </p>
                    </div>
                </div>

                <div class="col-auto md:d-none">

                    <a href="{{ route('panditlist')}}" class="button -md pandit-btn">
                        View All Pandits <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
            </div>

            <div class="row mb-30" data-aos="fade-up" data-aos-delay="500">
              @foreach ($pandits as $pandit)
             
                <div class="col-md-4 col-12 mb-20">
                  <a href="{{ route('pandit.show', $pandit->slug) }}">
                  <div class="row">
                    <div class="col-md-4 col-4">
                        <div class="pandit-front-sec-img">
                          <img class="rounded-lg" src="{{asset($pandit->profile_photo)}}" alt="">
                        </div>
                      
                    </div>
                    <div class="col-md-8 col-8">
                        <div class="pandit-front-sec-text">
                            <h3>{{$pandit->name}}</h3>
                            <span><i class="fa fa-star-o" aria-hidden="true"></i>4.8</span> Exceptional
                        </div>
                    </div>
                  </div>
                 </a>
                </div>
              
              @endforeach
              

              
            </div>
         
        

        </div>
    </section>

    <section class="special-pooja-bg layout-pt-md layout-pb-md">
        <div class="container">
            <div data-anim="" data-aos="fade-up" data-aos-delay="500" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Customized Pujas</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0 text-white">Elevate your spiritual journey with our special pujas, conducted by experienced pandits for auspicious occasions.</p>
                    </div>
                </div>

                <div class="col-auto md:d-none">

                    <a href="{{ url('pooja-list')}}" class="button -md -blue-1 all-ppja-btn">
                        View All Pooja <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
            </div>

            <div class = "row" data-aos="fade-up" data-aos-delay="500">
                @foreach ($otherpoojas as $otherpooja)
                <div class="col-md-4 pandit-card">
                    <a href="{{ route('pooja.show', $otherpooja->slug) }}"> 
                        <div class="card" data-state="#pooja">
                            <div class="card-header">
                                <img class="card-pooja" src="{{ asset('assets/img/'.$otherpooja->pooja_photo) }}" alt="image">
                            </div>
                            <div class="pooja-head">
                                <h5>{{$otherpooja->pooja_name}}</h5>
                                <div class="pooja-description">
                                  <p class="short-description">{{ Str::limit($otherpooja->short_description, 150, '...') }}</p>
                                  {{-- <p class="full-description" style="display:none;">{{ $otherpooja->short_description }}</p>
                                  <a href="javascript:void(0);" class="read-more-toggle">Read more</a> --}}
                                </div>
                                <div style="text-align: center">
                                    {{-- <h6>(12-03-2024)</h6> --}}
                                </div>
                                {{-- <button class="contact-me">Book Now</button> --}}
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
              
            </div>

           
        </div>
    </section>

 


    <section class="testimonial-bg">
        <div data-anim-wrap class="container">
          <div  class="row justify-center text-center">
            <div class="col-auto">
              <div class="sectionTitle text-white">
                <h2 class="sectionTitle__title">Blessed Reviews</h2>
                <p class=" sectionTitle__text mt-5 sm:mt-0">Hear how our expert pandits have enhanced spiritual experiences for our valued customers. Read their stories and feel the divine connection.</p>
              </div>
            </div>
          </div>
  
          <div  class="overflow-hidden pt-60 lg:pt-40 sm:pt-30 js-section-slider" data-gap="30" data-slider-cols="xl-3 lg-3 md-2 sm-1 base-1">
            <div class="swiper-wrapper">
  
              <div class="swiper-slide">
                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">
                  
                  <p class="testimonials__text lh-18 fw-500 text-dark-1">I recently used 33Crores Pandit to book a pandit for my housewarming ceremony, and the experience was fantastic. The booking process was smooth and hassle-free. The pandit was highly knowledgeable and performed the rituals with great devotion and precision. I appreciated the punctuality and professionalism. Highly recommend this service!</p>
  
                  <div class="pt-20 mt-28 border-top-light">
                    <div class="row x-gap-20 y-gap-20 items-center">
                      {{-- <div class="col-auto">
                        <img class="size-60" src="img/avatars/1.png" alt="image">
                      </div> --}}
  
                      <div class="col-auto">
                        <div class="text-15 fw-500 lh-14">Sidhant Rout</div>
                        {{-- <div class="text-14 lh-14 text-light-1 mt-5">Web Designer</div> --}}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="swiper-slide">
                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">
                  
                  <p class="testimonials__text lh-18 fw-500 text-dark-1">Booking a pandit for my daughter’s wedding through 33Crores Pandit was one of the best decisions. The website is user-friendly, and the customer service team is very responsive. The pandit arrived on time and conducted the ceremony beautifully, explaining the significance of each ritual. It was a truly memorable experience. Five stars!</p>
  
                  <div class="pt-20 mt-28 border-top-light">
                    <div class="row x-gap-20 y-gap-20 items-center">
                      {{-- <div class="col-auto">
                        <img class="size-60" src="img/avatars/1.png" alt="image">
                      </div> --}}
  
                      <div class="col-auto">
                        <div class="text-15 fw-500 lh-14">Soumya Puhan</div>
                        {{-- <div class="text-14 lh-14 text-light-1 mt-5">Web Designer</div> --}}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="swiper-slide">
                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">
                  
                  <p class="testimonials__text lh-18 fw-500 text-dark-1">I used 33Crores Pandit to arrange a Navgraha Puja, and I couldn't be more satisfied. The whole process, from booking to the actual puja, was seamless. The pandit was extremely knowledgeable and performed the puja with utmost dedication. It was a great convenience to book online and have everything taken care of professionally.</p>
  
                  <div class="pt-20 mt-28 border-top-light">
                    <div class="row x-gap-20 y-gap-20 items-center">
                      {{-- <div class="col-auto">
                        <img class="size-60" src="img/avatars/1.png" alt="image">
                      </div> --}}
  
                      <div class="col-auto">
                        <div class="text-15 fw-500 lh-14">Barsa Das</div>
                        {{-- <div class="text-14 lh-14 text-light-1 mt-5">Web Designer</div> --}}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
            
  
            </div>
          </div>
  
         
        </div>
    </section>

  {{--cta---}}
  <div class="section-cta-custom pt-0">
    <div class="container">
    <div class="section-title text-center">
    <p class="subtitle">How We Can Assist</p>
    <h4 class="title">We Are Ready To Assist    </h4>
    </div>
    <div class="row align-items-center position-relative">
      <div class="col-md-6">
        <div class="sigma_cta primary-bg">
          {{-- <img class="cta-left-img" src="{{ asset('front-assets/img/4ebcc66a-bcc1-429f-8ef1-6040ae9f369d-removebg-preview.png')}}" alt="cta"> --}}
          <div class="sigma_cta-content" style="    padding: 40px 40px 40px 148px;">
           
            <h4 class="text-white">+91 9776888887</h4>
          </div>
        </div>
      </div>
      <span class="sigma_cta-sperator d-lg-flex">or</span>
      <div class="col-md-6">
        <div class="sigma_cta primary-bg1">
          <div class="sigma_cta-content" style="padding: 40px 40px 40px 90px;">
            <h4 class="text-white">contact@33crores.com</h4>
          </div>
          {{-- <img class="cta-left-img" src="{{ asset('front-assets/img/4ebcc66a-bcc1-429f-8ef1-6040ae9f369d-removebg-preview.png')}}" alt="cta"> --}}

        </div>
      </div>
    </div>

  </div>
</div>


  
  <section class="pt-60 custmer-count">
    <div class="container">
        <div class="border-bottom-light pb-40">
            <div class="row y-gap-30 justify-center text-center">

                <div class="col-xl-3 col-6">
                    <img src="{{ asset('front-assets/img/919f2cab-3b5c-46df-8121-1dbbea546f1e.png') }}" alt="image" width="50%">
                    <div class="text-40 lg:text-30 lh-13 fw-600 counter">101</div>
                    <div class="text-14 lh-14 text-light-1 mt-5 " style="text-transform: capitalize; ">Type of Pooja Listed</div>
                </div>

                <div class="col-xl-3 col-6">
                  <img src="{{ asset('front-assets/img/customer.png') }}" alt="image" width="50%">
                    <div class="text-40 lg:text-30 lh-13 fw-600 counter">791</div>
                    <div class="text-14 lh-14 text-light-1 mt-5" style="text-transform: capitalize; ">Happy customers</div>
                </div>

                <div class="col-xl-3 col-6">
                  <img src="{{ asset('front-assets/img/PANDIT_JEE_LISTED-removebg-preview.png') }}" alt="image" width="50%">
                    <div class="text-40 lg:text-30 lh-13 fw-600 counter">121</div>
                    <div class="text-14 lh-14 text-light-1 mt-5" style="text-transform: capitalize; ">Pandti Jee Listed</div>
                </div>

                <div class="col-xl-3 col-6">
                  <img src="{{ asset('front-assets/img/POOJA PERFORMED.png') }}" alt="image" width="50%">
                    <div class="text-40 lg:text-30 lh-13 fw-600 counter">1491</div>
                    <div class="text-14 lh-14 text-light-1 mt-5" style="text-transform: capitalize; ">Pooja performed</div>
                </div>

            </div>
        </div>
    </div>
</section>
    <section class="section-bg pt-80 pb-80 md:pt-40 md:pb-40">


        <div class="container">
            <div class="row y-gap-30 items-center justify-between">
                <div  class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-delay="500">
                    <h2 class="text-30 lh-15">Download the App</h2>
                    <p class="text-dark-1 pr-40 lg:pr-0 mt-15 sm:mt-5">Stay connected and make your spiritual journey easier with our app. Book pandits, schedule pujas, and get updates on upcoming events all at your fingertips. Download now for a seamless and convenient experience!</p>

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

                        <div class="col-auto" >
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

                <div  class="col-lg-6" data-aos="fade-up" data-aos-delay="500">
                    <img src="{{ asset('front-assets/img/Beige &amp; White Special Offer Discount Instagram Post.png') }}" alt="image">
                </div>
            </div>
        </div>
    </section>


   
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/counterup2@1.0.4/dist/index.min.js"></script>
<script>
    $(document).ready(function(){
        $('.counter').counterUp({
            delay: 20, // increased delay
            time: 2000 // increased time
        });
    });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const readMoreToggle = document.querySelector('.read-more-toggle');
      const shortDescription = document.querySelector('.short-description');
      const fullDescription = document.querySelector('.full-description');
      
      readMoreToggle.addEventListener('click', function() {
          if (fullDescription.style.display === 'none') {
              fullDescription.style.display = 'block';
              readMoreToggle.textContent = 'Read less';
          } else {
              fullDescription.style.display = 'none';
              readMoreToggle.textContent = 'Read more';
          }
      });
  });
</script>
@endsection