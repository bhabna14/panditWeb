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
                    <h1 class="sc-7kepeu-0 kYnyFA description">Discover the best pooja you want to do</h1>
                    <div class="searchContainer">
                        <div class="searchbar">
                            <div class="sc-18n4g8v-0 gAhmYY sc-jqBkfb cOJMkN">
                                <i class="sc-rbbb40-1 iFnyeo" color="#FF7E8B" size="20">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#FF7E8B" width="20" height="20"
                                        viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-" role="img"
                                        class="sc-rbbb40-0 iRDDBk">
                                        <title>location-fill</title>
                                        <path
                                            d="M10.2 0.42c-4.5 0-8.2 3.7-8.2 8.3 0 6.2 7.5 11.3 7.8 11.6 0.2 0.1 0.3 0.1 0.4 0.1s0.3 0 0.4-0.1c0.3-0.2 7.8-5.3 7.8-11.6 0.1-4.6-3.6-8.3-8.2-8.3zM10.2 11.42c-1.7 0-3-1.3-3-3s1.3-3 3-3c1.7 0 3 1.3 3 3s-1.3 3-3 3z">
                                        </path>
                                    </svg>
                                </i>
                                <input placeholder="Enter Location" class="sc-cNCRlr bZpjF" value="">
                                <i class="sc-rbbb40-1 iFnyeo sc-iHfyOJ gMzMrK" color="#4F4F4F" size="12">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#4F4F4F" width="12" height="12"
                                        viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-" role="img"
                                        class="sc-rbbb40-0 ezrcri">
                                        <title>down-triangle</title>
                                        <path d="M20 5.42l-10 10-10-10h20z"></path>
                                    </svg>
                                </i>
                                <div class="sc-imapFV hLKNYi">
                                </div>
                            </div>
                            <div class="sc-iQoMDr cuCypd"></div>
                            <div class="sc-18n4g8v-0 gAhmYY sc-bTiqRo cGUIwG">
                                <div class="sc-fFTYTi kVBZua">
                                    <i class="sc-rbbb40-1 iFnyeo" color="#828282" size="18">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="#828282" width="18" height="18"
                                            viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-"
                                            role="img" class="sc-rbbb40-0 iwHbVQ">
                                            <title>Search</title>
                                            <path
                                                d="M19.78 19.12l-3.88-3.9c1.28-1.6 2.080-3.6 2.080-5.8 0-5-3.98-9-8.98-9s-9 4-9 9c0 5 4 9 9 9 2.2 0 4.2-0.8 5.8-2.1l3.88 3.9c0.1 0.1 0.3 0.2 0.5 0.2s0.4-0.1 0.5-0.2c0.4-0.3 0.4-0.8 0.1-1.1zM1.5 9.42c0-4.1 3.4-7.5 7.5-7.5s7.48 3.4 7.48 7.5-3.38 7.5-7.48 7.5c-4.1 0-7.5-3.4-7.5-7.5z">
                                            </path>
                                        </svg>
                                    </i>
                                </div>
                                <input placeholder="Search pooja here" class="sc-gFXMyG fEokXR" value="">
                                <div class="sc-cnTzU cStzIL"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </section>
    <div class = "container">
        <div class="text-center bookpandit-heading" style="margin-top: 50px">
            <h3>All pooja list available here</h3>
            <img src="{{ asset('front-assets/img/general/hr.png')}}" alt="">
        </div>
        <div class = "row" data-aos="fade-up">
            <div class="col-md-4 pandit-card">
                <a href="{{url('puja-details')}}"> 
                    <div class="card" data-state="#pooja">
                        <div class="card-header">
                            <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/baldev.jpg') }}" alt="image">
                        </div>
                        <div class="pooja-head">
                            <h5>Baladevjew Pooja</h5>
                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                            <div style="text-align: center">
                                {{-- <h6>(12-03-2024)</h6> --}}
                            </div>
                            {{-- <button class="contact-me">Book Now</button> --}}
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/Janmashtami.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Janmasthami Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/ganeshpuja.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Ganesh Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            
        </div>
        <div class = "row" data-aos="fade-up">
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/baldev.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Baladevjew Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/Janmashtami.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Janmasthami Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/ganeshpuja.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Ganesh Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            
        </div>
        <div class = "row" data-aos="fade-up">
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/baldev.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Baladevjew Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/Janmashtami.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Janmasthami Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 pandit-card">
                <div class="card" data-state="#pooja">
                    <div class="card-header">
                        <img class="card-pooja" src="{{ asset('front-assets/img/masthead/2/ganeshpuja.jpg') }}" alt="image">
                    </div>
                    <div class="pooja-head">
                        <h5>Ganesh Pooja</h5>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                        <div style="text-align: center">
                            {{-- <h6>(12-03-2024)</h6> --}}
                        </div>
                        {{-- <button class="contact-me">Book Now</button> --}}
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <nav data-pagination class="pagination-custom">
        <a href=# disabled><i class=ion-chevron-left></i></a>
        <ul>
            <li class=current><a href=#1>1</a>
            <li><a href=#2>2</a>
            <li><a href=#3>3</a>
            <li><a href=#4>4</a>
            <li><a href=#5>5</a>
            <li><a href=#6>6</a>
            <li><a href=#7>7</a>
            <li><a href=#8>8</a>
            <li><a href=#9>9</a>
            <li><a href=#10>…</a>
            <li><a href=#41>41</a>
        </ul>
        <a href=#2><i class=ion-chevron-right></i></a>
    </nav>
@endsection

@section('scripts')
@endsection
