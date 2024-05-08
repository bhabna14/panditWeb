@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <div class="sc-gJqsIT bdDCMj logo" height="6rem" width="30rem">
                    <div class="low-res-container">
                    </div>
                </div>
                <h1 class="sc-7kepeu-0 kYnyFA description">Discover the best pandit in <span class="next-line">country</span></h1>
                <div class="searchContainer">
                    <div class="searchbar">
                        <div class="sc-18n4g8v-0 gAhmYY sc-jqBkfb cOJMkN">
                            <i class="sc-rbbb40-1 iFnyeo" color="#FF7E8B" size="20">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="#FF7E8B" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-" role="img" class="sc-rbbb40-0 iRDDBk">
                                    <title>location-fill</title>
                                    <path d="M10.2 0.42c-4.5 0-8.2 3.7-8.2 8.3 0 6.2 7.5 11.3 7.8 11.6 0.2 0.1 0.3 0.1 0.4 0.1s0.3 0 0.4-0.1c0.3-0.2 7.8-5.3 7.8-11.6 0.1-4.6-3.6-8.3-8.2-8.3zM10.2 11.42c-1.7 0-3-1.3-3-3s1.3-3 3-3c1.7 0 3 1.3 3 3s-1.3 3-3 3z"></path>
                                </svg>
                            </i>
                            <input placeholder="Enter Location" class="sc-cNCRlr bZpjF" value="">
                            <i class="sc-rbbb40-1 iFnyeo sc-iHfyOJ gMzMrK" color="#4F4F4F" size="12">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="#4F4F4F" width="12" height="12" viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-" role="img" class="sc-rbbb40-0 ezrcri">
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="#828282" width="18" height="18" viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-" role="img" class="sc-rbbb40-0 iwHbVQ">
                                    <title>Search</title>
                                    <path d="M19.78 19.12l-3.88-3.9c1.28-1.6 2.080-3.6 2.080-5.8 0-5-3.98-9-8.98-9s-9 4-9 9c0 5 4 9 9 9 2.2 0 4.2-0.8 5.8-2.1l3.88 3.9c0.1 0.1 0.3 0.2 0.5 0.2s0.4-0.1 0.5-0.2c0.4-0.3 0.4-0.8 0.1-1.1zM1.5 9.42c0-4.1 3.4-7.5 7.5-7.5s7.48 3.4 7.48 7.5-3.38 7.5-7.48 7.5c-4.1 0-7.5-3.4-7.5-7.5z"></path>
                                </svg>
                            </i>
                        </div>
                        <input placeholder="Search pandit for all type of pooja" class="sc-gFXMyG fEokXR" value="">
                        <div class="sc-cnTzU cStzIL"></div>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</section>
<div class = "container">
    <div class="text-center" style="margin-top: 50px">
        <h3>Book Your Preferable Pandit</h3>
        <hr>
    </div>
    <div class = "row">
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 pandit-card">
            <div class="card" data-state="#about">
                <div class="card-header">
                    <img class="card-avatar" src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image">
                                                   
                    <h1 class="card-fullname">P.Bibhu Panda</h1>
                </div>
                <div class="card-main">
                    <div class="card-section is-active" id="about">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">Whatever tattooed stumptown art party sriracha gentrify hashtag intelligentsia readymade schlitz brooklyn disrupt.</p>
                        </div>
                        <div class="card-social">
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.997 3.985h2.191V.169C17.81.117 16.51 0 14.996 0c-3.159 0-5.323 1.987-5.323 5.639V9H6.187v4.266h3.486V24h4.274V13.267h3.345l.531-4.266h-3.877V6.062c.001-1.233.333-2.077 2.051-2.077z"/></svg></a>
                            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/></svg></a>
                            <a href="#"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M301 256c0 24.852-20.148 45-45 45s-45-20.148-45-45 20.148-45 45-45 45 20.148 45 45zm0 0"/><path d="M332 120H180c-33.086 0-60 26.914-60 60v152c0 33.086 26.914 60 60 60h152c33.086 0 60-26.914 60-60V180c0-33.086-26.914-60-60-60zm-76 211c-41.355 0-75-33.645-75-75s33.645-75 75-75 75 33.645 75 75-33.645 75-75 75zm86-146c-8.285 0-15-6.715-15-15s6.715-15 15-15 15 6.715 15 15-6.715 15-15 15zm0 0"/></svg></a>
                            <a href="#"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.994 24v-.001H24v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07V7.976H8.489v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243V24zM.396 7.977h4.976V24H.396zM2.882 0C1.291 0 0 1.291 0 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909A2.884 2.884 0 002.882 0z"/></svg></a>
                        </div>
                    </div>
                    <div class="card-section" id="experience">
                        <div class="card-content">
                            <div class="card-subtitle">WORK EXPERIENCE</div>
                            <div class="card-timeline">
                                <div class="card-item" data-year="2014">
                                    <div class="card-item-title">Durga Puja</div>
                                </div>
                                <div class="card-item" data-year="2016">
                                    <div class="card-item-title">Kali Puja</div>
                                    <div class="card-item-desc">Developed new conversion funnels and disrupt.</div>
                                </div>
                                <div class="card-item" data-year="2018">
                                    <div class="card-item-title">Diwali Puja</div>
                                    <div class="card-item-desc">Onboarding illustrations for App.</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-section" id="contact">
                        <div class="card-content">
                            <div class="card-subtitle">CONTACT</div>
                            <div class="card-contact-wrapper">
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Algonquin Rd, Three Oaks Vintage, MI, 49128
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                    </svg>
                                    (269) 756-9809
                                </div>
                                <div class="card-contact">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                    william@rocheald.com
                                </div>
                                <button class="contact-me">Book Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <button data-section="#about" class="is-active">ABOUT</button>
                        <button data-section="#experience">EXPERIENCE</button>
                        <button data-section="#contact">CONTACT</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
 
</div>

<nav data-pagination>
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
