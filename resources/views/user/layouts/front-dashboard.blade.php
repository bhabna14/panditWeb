<!DOCTYPE html>
<html lang="en">
	<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google fonts -->
		<!-- Title -->
		<title> Pandit Web</title>

        @include('user.layouts.components.front-style')
        
<style>
  .active {
    /* Your active link styles */
    background-color: #f0f0f0;
}
</style>
	</head>

	<body>


        <div class="preloader js-preloader">
          <div class="preloader__wrap">
            <div class="preloader__icon">
             <img src="{{asset('front-assets/img/icons/1.png')}}" alt="splash icon">
            </div>
          </div>
      
          <div class="preloader__title">33Crores</div>
        </div>
       
        <main>
      
       

        @include('user.layouts.components.front-dashboard-header')

		<!-- Page -->
        <div class="dashboard" data-x="dashboard" data-x-toggle="-is-sidebar-open">
            <div class="dashboard__sidebar bg-white scroll-bar-1">
        
        
              <div class="sidebar -dashboard">
        
                <div class="sidebar__item">
                  <div class="sidebar__button {{ Request::is('my-profile') ? 'active' : '' }}">
                    <a href="{{url('my-profile')}}" class="d-flex items-center text-15 lh-1 fw-500">
                      <img src="{{ asset('front-assets/img/dashboard/sidebar/compass.svg')}}" alt="image" class="mr-15">
                      Dashboard
                    </a>
                  </div>
                </div>
        
                <div class="sidebar__item">
                  <div class="sidebar__button {{ Request::is('order-history') ? 'active' : '' }}">
                    <a href="{{url('order-history')}}" class="d-flex items-center text-15 lh-1 fw-500">
                      <img src="{{ asset('front-assets/img/dashboard/sidebar/booking.svg')}}" alt="image" class="mr-15">
                      Order History
                    </a>
                  </div>
                </div>
        
               
        
                <div class="sidebar__item">
                  <div class="sidebar__button {{ Request::is('userprofile') ? 'active' : '' }}">
                    <a href="{{url('userprofile')}}" class="d-flex items-center text-15 lh-1 fw-500">
                      <img src="{{ asset('front-assets/img/dashboard/sidebar/gear.svg')}}" alt="image" class="mr-15">
                      Profile
                    </a>
                  </div>
                </div>

                <div class="sidebar__item">
                  <div class="sidebar__button {{ Request::is('manage-address') ? 'active' : '' }}">
                    <a href="{{url('manage-address')}}" class="d-flex items-center text-15 lh-1 fw-500">
                      <img src="{{ asset('front-assets/img/dashboard/sidebar/gear.svg')}}" alt="image" class="mr-15">
                      Manage Address
                    </a>
                  </div>
                </div>
        
                <div class="sidebar__item">
                  <div class="sidebar__button ">
                    <a href="#" class="d-flex items-center text-15 lh-1 fw-500">
                      <img src="{{ asset('front-assets/img/dashboard/sidebar/log-out.svg')}}" alt="image" class="mr-15">
                      Logout
                    </a>
                  </div>
                </div>
        
              </div>
        
        
            </div>
        @yield('content')
		<!-- End Page -->
        </div>
        {{-- @include('user.layouts.components.front-footer') --}}


    </main>
    @include('user.layouts.components.front-script')


    </body>
</html>
