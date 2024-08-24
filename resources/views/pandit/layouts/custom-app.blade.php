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
		<title> Pandit Login</title>
        <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>
    
        <!-- Firebase Messaging -->
        <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>
        
<link rel="shortcut icon" href="//cdn.shopify.com/s/files/1/0654/9789/1030/files/MicrosoftTeams-image_471_32x32.png?v=1660732431" type="image/png">
    </head>
		@include('pandit.layouts.components.custom-styles')

    </head>
	<body class="ltr error-page1">

		@yield('class')

            <!-- Loader -->
            <div id="global-loader">
                <img src="{{asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
            </div>
            <!-- /Loader -->


            <div class="square-box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="page" >

                @yield('content')

            </div>
        </div>

		@include('pandit.layouts.components.custom-scripts')
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            // You can now proceed to subscribe the user to push notifications

            const notification = new Notification(notificationTitle, notificationOptions);

            // Play custom sound
            const audio = new Audio('/var/www/panditWeb/public/Ghanti.mp3');
            audio.play();

            notification.onclick = function(event) {
                event.preventDefault(); // Prevent the browser from focusing the Notification's tab
                window.open(payload.data.url, '_blank'); // Open the URL in a new tab
            };
        } else {
            console.log('Unable to get permission to notify.');
        }
    });
});

</script> --}}
<script src="{{ asset('js/firebase.js') }}"></script>
    </body>

</html>
