		<!-- BACK-TO-TOP -->
		<a href="#top" id="back-to-top"><i class="las la-arrow-up"></i></a>

		<!-- JQUERY JS -->
		<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>

		<!-- BOOTSTRAP JS -->
		<script src="{{asset('assets/plugins/bootstrap/js/popper.min.js')}}"></script>
		<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

		<!-- IONICONS JS -->
		<script src="{{asset('assets/plugins/ionicons/ionicons.js')}}"></script>

		<!-- MOMENT JS -->
		<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>

		<!-- P-SCROLL JS -->
		<script src="{{asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
		<script src="{{asset('assets/plugins/perfect-scrollbar/p-scroll.js')}}"></script>

		<!-- SIDEBAR JS -->
		<script src="{{asset('assets/plugins/side-menu/sidemenu.js')}}"></script>

		<!-- STICKY JS -->
		<script src="{{asset('assets/js/sticky.js')}}"></script>

		<!-- Chart-circle js -->
		<script src="{{asset('assets/plugins/circle-progress/circle-progress.min.js')}}"></script>

		<!-- RIGHT-SIDEBAR JS -->
		<script src="{{asset('assets/plugins/sidebar/sidebar.js')}}"></script>
		<script src="{{asset('assets/plugins/sidebar/sidebar-custom.js')}}"></script>

        @yield('scripts')

		<!-- EVA-ICONS JS -->
		<script src="{{asset('assets/plugins/eva-icons/eva-icons.min.js')}}"></script>

		<!-- THEME-COLOR JS -->
		<script src="{{asset('assets/js/themecolor.js')}}"></script>

		<!-- CUSTOM JS -->
		<script src="{{asset('assets/js/custom.js')}}"></script>

		<!-- exported JS -->
		<script src="{{asset('assets/js/exported.js')}}"></script>
		<script type="text/javascript"
		src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
		<script>
			function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en'
        }, 'google_translate_element');
    }

		</script>

<script>
	setTimeout(function(){
		document.getElementById('Message').style.display = 'none';
	}, 3000);
</script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		// Check for new notifications every 5 seconds
		setInterval(checkNewNotifications, 5000);

		function checkNewNotifications() {
			// Make an AJAX request to check for new notifications
			fetch('/api/check-new-notifications') // Change this to your API endpoint
				.then(response => response.json())
				.then(data => {
					if (data.new_order) {
						playSound(); // Play the sound
						showNotification(data.notification); // Show the notification
					}
				})
				.catch(error => console.log('Error:', error));
		}

		// Function to play the sound each time a new order is received
		function playSound() {
			var audio = new Audio('{{ asset('sound/flowersound.mp3') }}'); // Ensure this path is correct
			audio.play();
		}

		// Function to show the notification in the dropdown
		function showNotification(notification) {
			let notificationList = document.querySelector('.main-notification-list');
			let newNotification = document.createElement('a');
			newNotification.classList.add('d-flex', 'p-3', 'border-bottom');
			newNotification.href = notification.url;

			newNotification.innerHTML = `
				<div class="notifyimg bg-pink">
					<i class="far fa-folder-open text-white"></i>
				</div>
				<div class="ms-3">
					<h5 class="notification-label mb-1">${notification.message}</h5>
					<div class="notification-subtext">${notification.time}</div>
				</div>
				<div class="ms-auto">
					<i class="las la-angle-right text-end text-muted"></i>
				</div>
			`;

			// Prepend the new notification to the list (to show the most recent on top)
			notificationList.prepend(newNotification);
		}
	});
</script>

