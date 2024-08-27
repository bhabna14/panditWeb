<!DOCTYPE html>
<html lang="en">
	<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google fonts -->
		<!-- Title -->
		<title> Pandit Web</title>
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>
    
    <!-- Firebase Messaging -->
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>
        @include('user.layouts.components.front-style')
        
        @yield('styles')
	</head>

	<body>


        {{-- <div class="preloader js-preloader">
          <div class="preloader__wrap">
            <div class="preloader__icon">
             <img src="{{asset('front-assets/img/icons/1.png')}}" alt="splash icon">
            </div>
          </div>
      
          <div class="preloader__title">33Crores</div>
        </div> --}}
       
        <main>
      
       

        @include('user.layouts.components.front-dashboard-header')

		<!-- Page -->
        <div class="dashboard" data-x="dashboard" data-x-toggle="-is-sidebar-open">
            <div class="dashboard__sidebar bg-white scroll-bar-1">
        
        
              <div class="sidebar -dashboard">

                <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('/') ? 'active' : '' }}">
                    <a class="side-menu__item" href="{{url('/')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z"></path></svg><span class="side-menu__label">Home</span></a>
                  </div>
                </div>
        
                <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('user-dashboard') ? 'active' : '' }}">
                    <a class="side-menu__item" href="{{url('user-dashboard')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z"></path></svg><span class="side-menu__label">Dashboards</span></a>
                  </div>
                </div>
        
                <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('booking-history') ? 'active' : '' }}">
                 
                    <a class="side-menu__item" href="{{url('booking-history')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm11 4h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zm-1 6h-4v-4h4v4zM17 3c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zM7 13c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z"></path></svg><span class="side-menu__label">Booking History</span></a>
                  </div>
                </div>

                <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('userprofile') ? 'active' : '' }}">
                 
                    <a class="side-menu__item" href="{{url('userprofile')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm11 4h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zm-1 6h-4v-4h4v4zM17 3c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zM7 13c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z"></path></svg><span class="side-menu__label">Profile</span></a>
                  </div>
                </div>

                <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('manage-address') ? 'active' : '' }}">
                 
                    <a class="side-menu__item" href="{{url('manage-address')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm11 4h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zm-1 6h-4v-4h4v4zM17 3c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zM7 13c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z"></path></svg><span class="side-menu__label"> Manage Address</span></a>
                  </div>
                </div>

                {{-- <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('coupons') ? 'active' : '' }}">
                 
                    <a class="side-menu__item" href="{{url('coupons')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm11 4h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zm-1 6h-4v-4h4v4zM17 3c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zM7 13c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z"></path></svg><span class="side-menu__label">Manage Coupons</span></a>
                  </div>
                </div> --}}

                <div class="sidebar__item side-menu">
                  <div class="sidebar__button {{ Request::is('') ? 'active' : '' }}">
                 
                      <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm11 4h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zm-1 6h-4v-4h4v4zM17 3c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zM7 13c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z"></path>
                      </svg>
                      <a class="side-menu__label" href="{{ route('userlogout') }}" onclick="event.preventDefault(); confirmLogout();">Logout</a>
                      <form id="logout-form" action="{{ route('userlogout') }}" method="POST" style="display: none;">
                          @csrf
                      </form>
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
    <script>
      function confirmLogout() {
          if (confirm('Are you sure you want to log out?')) {
              document.getElementById('logout-form').submit();
          }
      }
    </script>
    @yield('scripts')
    <script src="{{ asset('js/user_firebase.js') }}"></script>

    </body>
</html>
