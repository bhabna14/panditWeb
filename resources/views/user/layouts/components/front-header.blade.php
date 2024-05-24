<div class="header-margin"></div>
<header data-add-bg="" class="header bg-white shadow-3 js-header" data-x="header" data-x-toggle="is-menu-opened">
  <div data-anim="" class="header__container header__container-1500 mx-auto px-30 sm:px-20">
    <div class="row justify-between items-center">

      <div class="col-auto">
        <div class="d-flex items-center">
          <a href="{{url('/')}}" class="header-logo mr-50" data-x="header-logo" data-x-toggle="is-logo-dark">
            <img src="{{asset('front-assets/img/brand/logo.png')}}" alt="image" >
            <img src="{{asset('front-assets/img/brand/logo.png')}}" alt="image" >
          </a>


          <div class="header-menu " data-x="mobile-menu" data-x-toggle="is-menu-active">
            <div class="mobile-overlay"></div>

            <div class="header-menu__content">
              <div class="mobile-bg js-mobile-bg"></div>

              <div class="menu js-navList">
                <ul class="menu__nav text-dark-1 -is-active">
                 
                  <li>
                    <a data-barba href="{{url('/')}}">
                      <span class="mr-10">Home</span>
                    </a>
                  </li>


                  <li>
                    <a data-barba href="{{url('book-pandit')}}">
                      <span class="mr-10">Book a Pandit</span>
                    </a>
                  </li>
                     <li>
                    <a data-barba href="{{url('pooja-list')}}">
                       List of Pooja
                    </a>
                  </li>
                  <li>
                    <a href="book-temple-sevayat-name.html">
                       Panji
                    </a>
                  </li>
          
                  <li>
                    <a href="{{url('about-us')}}">About Us</a>
                  </li>
                  
                  <li>
                    <a href="{{url('contact')}}">Contact</a>
                  </li>
                  <li class="menu-item-has-children">
                    <a data-barba href="#">
                      <i class="d-flex items-center icon-user text-inherit text-18 mx-10" style="margin-right: 7px;"></i>
                      Dashboard
                      <i class="icon icon-chevron-sm-down" style="margin-left: 7px;"></i>
                    </a>

                    <ul class="subnav">
                      <li class="subnav__backBtn js-nav-list-back">
                        <a href="#"><i class="icon icon-chevron-sm-down"></i></a>
                      </li>

                      <li><a href="{{url('my-profile')}}">My profile</a> </li>

                      <li><a href="{{url('order-history')}}">Manage Address</a></li>

                      <li><a href="{{url('manage-address')}}">Orders</a></li>

                      <li><a href="{{url('coupons')}}">Coupons</a></li>

                      <li><a href="#">Logout</a></li>

                      

                    </ul>
                    

                  </li>

                 
                </ul>

              </div>

              <div class="mobile-footer px-20 py-20 border-top-light js-mobile-footer">
              </div>
            </div>
          </div>

          



        </div>
      </div>


      <div class="col-auto" >
        <div class="d-flex items-center">
          <div class="d-flex items-center ml-20 is-menu-opened-hide md:d-none">
            
              {{-- <a href="{{url('/my-profile')}}" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style = 'margin-left: 20px;width: 100px;background-color: #c80100 !important;'>My Profile</a> --}}
              <a href="http://127.0.0.1:8000/login" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;width: 100px;background-color: #c80100 !important;justify-content: left;align-items: left;/* align-items: end; */"><i class="d-flex items-center icon-user text-inherit text-18 mx-10" style="margin-right: 7px;"></i> Login</a>
                <a href="http://127.0.0.1:8000/panditlogin" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-left: 20px;width: 100px;background-color: #c80100 !important;justify-content: left;align-items: left;/* align-items: end; */"><i class="d-flex items-center icon-user text-inherit text-18 mx-10" style="margin-right: 7px;"></i> Pandit Login</a>
                  
          
            <div class="searchMenu-loc px-10 lg:py-20 lg:px-0 js-form-dd js-liverSearch">
                
                <div data-x-dd-click="searchMenu-loc">
                  <div class="button px-10 fw-400 text-14 -outline-blue-1 h-50 ml-20">
                    <h4 class="text-15 fw-500 ls-2 lh-16">Select Language</h4><i class="icon-chevron-sm-down text-7 ml-15"></i>
                  </div>
                </div>
                <div class="searchMenu-loc__field shadow-2 js-popup-window" data-x-dd="searchMenu-loc" data-x-dd-toggle="-is-active">
                  <div class="bg-white sm:px-0 sm:py-15 rounded-4 text-center">
                    <div class="y-gap-5 js-results">

                    <div class="text-center  js-search-option">
                            <a href="../EN/index.html">English </a>
                    </div>
                     <div class="text-center  js-search-option">
                            <a href="../OD/index.html">Odia </a>
                        </div>
                         <div class="text-center  js-search-option">
                            <a href="../HD/index.html">Hindi </a>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
        
             
            </div>

          <div class="d-none xl:d-flex x-gap-20 items-center pl-30" data-x="header-mobile-icons" data-x-toggle="text-white">
            <div>
              
              <div class="dropdown">
                <button class="dropbtn"><a href="#" class="d-flex items-center icon-user text-inherit text-22"></a></button>
                <div class="dropdown-content">
                  <a href="#">Link 1</a>
                  <a href="#">Link 2</a>
                  <a href="#">Link 3</a>
                </div>
              </div>
            </div>
            <div><button class="d-flex items-center icon-menu text-inherit text-20" data-x-click="header, header-logo, header-mobile-icons, mobile-menu"></button></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</header>