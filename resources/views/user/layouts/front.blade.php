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
      
       

        @include('user.layouts.components.front-header')
        </main>
		<!-- Page -->
        @yield('content')
		<!-- End Page -->
        @include('user.layouts.components.front-footer')


    </main>
    @include('user.layouts.components.front-script')

    @yield('scripts')
    </body>
</html>
