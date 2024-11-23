<!DOCTYPE html>
<html lang="en">
	<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google fonts -->
		<!-- Title -->
		<title> Flowers</title>

        @include('user.layouts.components.front-style')
        @yield('styles')
        <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>
    
        <!-- Firebase Messaging -->
        <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>
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
      
       

        @include('user.layouts.components.front-header-flower')
        </main>
		<!-- Page -->
        @yield('content')
		<!-- End Page -->
        @include('user.layouts.components.front-footer')


    </main>
    @include('user.layouts.components.front-script')

    @yield('scripts')
    <script>
      function confirmLogout() {
          if (confirm('Are you sure you want to log out?')) {
              document.getElementById('logout-form').submit();
          }
      }
    </script>
            <script src="{{ asset('js/user_firebase.js') }}"></script>

    </body>
</html>
