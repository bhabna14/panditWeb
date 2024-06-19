@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
    

 <section class="banner-bg">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <h1 data-aos="fade-left" data-aos-delay="500">Reliable Pandit Booking For Every <br> Religious Event</h1>
          <p data-aos="fade-right" data-aos-delay="500">Experienced Pandits For Every Occasion, Just a Click Away</p>
          <a href="" class="book-now-btn" data-aos="fade-up" data-aos-delay="500">Book Now</a>
          
        </div>
      </div>
    </div>
 </section>
 <section>
  <div class="container">
      <div class="row" style="margin-top:60px">
          <div class="col-md-6" data-aos="fade-right" data-aos-delay="500">
              
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
          <div class="col-md-6" data-aos="fade-left" data-aos-delay="500">
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

    <section class="upcoming-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="upcoming-main-heading">
                        <h1>Upcoming Events</h1>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="upcoming-event" data-aos="fade-up" data-aos-delay="500">
                        <div class="row">
                            <div class="col-md-3">
                               
                                <div class="upcoming-event-img">
                                    <img src="{{asset('front-assets/img/masthead/post-11-copyright-520x424.jpg')}}" alt="Avatar" class="image">
                                   
                                </div>
                            </div>
                          
                            <div class="col-md-7">
                               <div class="event-text">
                                    <h4>Kundalini Yoga Practice Againts Bad Habits</h4>
                                    <h6>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas consectetur necessitatibus perferendis iusto culpa nemo rem dolorum optio tempore!</h6>
                                    <p><i class="fa fa-calendar-check-o" aria-hidden="true"></i>12/06/2024</p>
                               </div>
                            </div>
                            <div class="col-md-2">
                                <div class="event-info">
                                    <a href="">Info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="upcoming-event" data-aos="fade-up" data-aos-delay="500">
                        <div class="row">
                            <div class="col-md-3">
                               
                                <div class="upcoming-event-img">
                                    <img src="https://vihara.themerex.net/wp-content/uploads/2018/12/post-15-copyright-520x424.jpg" alt="Avatar" class="image">
                                </div>
                            </div>
                          
                            <div class="col-md-7">
                               <div class="event-text">
                                    <h4>Kundalini Yoga Practice Againts Bad Habits</h4>
                                    <h6>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas consectetur necessitatibus perferendis iusto culpa nemo rem dolorum optio tempore!</h6>
                                    <p><i class="fa fa-calendar-check-o" aria-hidden="true"></i>12/06/2024</p>
                               </div>
                            </div>
                            <div class="col-md-2">
                                <div class="event-info">
                                    <a href="">Info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="upcoming-event" data-aos="fade-up" data-aos-delay="500">
                        <div class="row">
                            <div class="col-md-3">
                               
                                <div class="upcoming-event-img">
                                    <img src="{{asset('front-assets/img/masthead/post-11-copyright-520x424.jpg')}}" alt="Avatar" class="image">
                                </div>
                            </div>
                          
                            <div class="col-md-7">
                               <div class="event-text">
                                    <h4>Kundalini Yoga Practice Againts Bad Habits</h4>
                                    <h6>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas consectetur necessitatibus perferendis iusto culpa nemo rem dolorum optio tempore!</h6>
                                    <p><i class="fa fa-calendar-check-o" aria-hidden="true"></i>12/06/2024</p>
                               </div>
                            </div>
                            <div class="col-md-2">
                                <div class="event-info">
                                    <a href="">Info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

                    <a href="{{ url('book-pandit')}}" class="button -md pandit-btn">
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
            
        </div>
    </section>

@endsection

@section('scripts')
@endsection
