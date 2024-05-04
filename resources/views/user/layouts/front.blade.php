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
		<title> Pandit Web</title>

        @include('user.layouts.components.front-style')

	</head>

	<body class="ltr main-body app sidebar-mini">

        @include('user.layouts.components.front-header')

		<!-- Page -->
        @yield('content')
		<!-- End Page -->
        @include('user.layouts.components.front-footer')


        @include('user.layouts.components.front-script')

    </body>
</html>
