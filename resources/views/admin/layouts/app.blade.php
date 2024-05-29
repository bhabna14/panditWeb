<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Nowa – Laravel Bootstrap 5 Admin & Dashboard Template">
		<meta name="Author" content="Spruko Technologies Private Limited">
		<meta name="Keywords" content="admin dashboard, admin dashboard laravel, admin panel template, blade template, blade template laravel, bootstrap template, dashboard laravel, laravel admin, laravel admin dashboard, laravel admin panel, laravel admin template, laravel bootstrap admin template, laravel bootstrap template, laravel template"/>

		<!-- Title -->
		<title> Pandit Admin Dashboard</title>

        @include('admin.layouts.components.styles')

	</head>

	<body class="ltr main-body app sidebar-mini">

		<!-- Loader -->
		{{-- <div id="global-loader">
			<img src="{{asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
		</div> --}}
		<!-- /Loader -->

		<!-- Page -->
		<div class="page">

			<div>

                @include('admin.layouts.components.app-header')

                @include('admin.layouts.components.app-sidebar')

			</div>

			<!-- main-content -->
			<div class="main-content app-content">

				<!-- container -->
				<div class="main-container container-fluid">

                    @yield('content')

				</div>
				<!-- Container closed -->
			</div>
			<!-- main-content closed -->

            @include('admin.layouts.components.sidebar-right')

            @include('admin.layouts.components.modal')

            @yield('modal')

            @include('admin.layouts.components.footer')

		</div>
		<!-- End Page -->

        @include('admin.layouts.components.scripts')

    </body>
</html>
