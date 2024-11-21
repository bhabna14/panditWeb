@extends('user.layouts.front-flower')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<style>
.product-card {
    border-radius: 15px;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff, #f9f9f9);
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    height: 735px;

}
.product-car_cust {
    border-radius: 15px;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff, #f9f9f9);
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    /* height: 600px; */

}

.text-decoration-line-through {
    text-decoration: line-through; /* Strikethrough for the MRP */
    color: #aaa; /* Light grey for the crossed-out MRP */
}

/* 
.product-card:hover {
    transform: translateY(-10px) scale(1.03);
} */

.product-image-container {
    position: relative;
    overflow: hidden;
}

.product-image {
    width: 100%;
    /* height: 250px; */
    object-fit: cover;
    transition: transform 0.3s;
}

.product-image-container:hover .product-image {
    transform: scale(1.1);
}

.badge-sale {
    top: 10px;
    left: 10px;
    background-color: #ff5c5c;
    color: #fff;
    padding: 5px 10px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: bold;
}

.card-body {
    padding: 20px;
}

.product-title {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.product-description {
    color: #777;
    font-size: 14px;
    margin-bottom: 15px;
}

.product-price {
    font-size: 20px;
    color: #B90B0B;
    font-weight: bold;
    margin-bottom: 10px;
}

.btn-gradient {
    background: linear-gradient(90deg, #B90B0B, #feb47b);
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 30px;
    transition: background 0.3s;
    letter-spacing: 1px
}
.flower-package-heading h1{
  color: #000;
  text-align: center;
  margin-bottom: 38px;
  letter-spacing: 1px;
}

/* .btn-gradient:hover {
    background: linear-gradient(90deg, #ff5c5c, #ff7e5f);
} */

</style>
@endsection

@section('content')
    

<section class="" style="margin-top: -17px;">
  <div class="">
    <!-- Home Banner Section -->
    <div class="home-banner-section">
      <div id="homeBannerCarousel" class="owl-carousel owl-theme">
        @foreach ($banners as $banner)
          <div class="item">
            <img src="{{ $banner['banner_img_url'] }}" alt="{{ $banner['alt_text'] ?? 'Home Banner' }}" class="img-fluid d-block w-100">
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="row" style="margin-top: 30px;">
      <div class="col-md-12">
        <div class="flower-package-heading">
          <h1>Customized Flower</h1>
        </div>
      </div>
    </div>
  
    <div class="row" style="margin-top: 20px; margin-bottom: 140px;">
      @if($customizedpps->isNotEmpty())
          @foreach($customizedpps as $customizedpp)
            <div class="product-car_cust row shadow-lg position-relative">
              <div class="col-md-3 mb-4">
                
                      <div class="product-image-container">
                          <img src="{{ asset('storage/'.$customizedpp->product_image) }}" alt="{{ $customizedpp->name }}" class="product-image">
                         
                      </div>
                    
                 
              </div>
              <div class="col-md-9">
                <div class="card-body ">
                  <h5 class="product-title">{{ $customizedpp->name }}</h5>
                  <p class="product-description">{{ $customizedpp->description }}</p>
                  <p class="product-price">
                    {{ $customizedpp->immediate_price }}
                  </p>
                
                  @if(Auth::guard('users')->check())
                    <!-- User is logged in -->
                    <a href="{{ route('cutsomized-checkout', ['product_id' => $customizedpp->product_id]) }}" class="btn btn-gradient w-100 mt-2">
                        Order Now
                    </a>
                  @else
                      <!-- User is not logged in -->
                      <a href="{{route('userlogin', ['referer' => urlencode(url()->current())]) }}" class="btn btn-gradient w-100 mt-2">
                          Order Now
                      </a>
                  @endif
              
                </div>
              </div>
             </div>
          @endforeach
      @else
          <p class="text-center">No products available at the moment.</p>
      @endif
  </div>
  </div>
</section>

<section style="margin-top: -60px;">
  <div class="container">
    <div class="row" style="margin-top: 30px;">
      <div class="col-md-12">
        <div class="flower-package-heading">
          <h1>Flower Package</h1>
        </div>
      </div>
    </div>
  
    <div class="row" style="margin-top: 20px; margin-bottom: 140px;">
      @if($products->isNotEmpty())
          @foreach($products as $product)
              <div class="col-md-4 mb-4">
                  <div class="product-card shadow-lg position-relative">
                      <div class="product-image-container">
                          <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}" class="product-image">
                         
                      </div>
                      <div class="card-body text-center">
                          <h5 class="product-title">{{ $product->name }}</h5>
                          <p class="product-description">{{ $product->description }}</p>
                          <p class="product-price">
                            <span class="text-decoration-line-through">₹ {{ number_format($product['mrp'], 2) }}</span> 
                            ₹ {{ number_format($product['price'], 2) }}
                          </p>
                        
                          @if(Auth::guard('users')->check())
                            <!-- User is logged in -->
                            <a href="{{ route('checkout', ['product_id' => $product->product_id]) }}" class="btn btn-gradient w-100 mt-2">
                                Order Now
                            </a>
                          @else
                              <!-- User is not logged in -->
                              <a href="{{route('userlogin', ['referer' => urlencode(url()->current())]) }}" class="btn btn-gradient w-100 mt-2">
                                  Order Now
                              </a>
                          @endif
                      
                      </div>
                  </div>
              </div>
          @endforeach
      @else
          <p class="text-center">No products available at the moment.</p>
      @endif
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
                        <div class="text-15 fw-500 lh-14">Swati Das</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
  
$(document).ready(function(){
  // Initialize Owl Carousel for Home Banner
  $('#homeBannerCarousel').owlCarousel({
    loop: true,           // Enable looping
    margin: 10,           // Add margin between slides
    nav: false,            // Enable next/prev buttons
    autoplay: true,       // Enable auto slide
    autoplayTimeout: 2000, // Auto slide timeout (5 seconds)
    autoplayHoverPause: true, // Pause on hover
    items: 1,             // Display one item per slide
    dots: false,           // Enable dots navigation
    animateOut: 'fadeOut', // Add fade out effect when transitioning between slides
    animateIn: 'fadeIn'   // Add fade in effect for incoming slides
  });
});

</script>
@endsection