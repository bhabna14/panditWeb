<!-- main-sidebar -->
<div class="sticky">
	<aside class="app-sidebar">
		<div class="main-sidebar-header active">
			<a class="header-logo active" href="{{ url('/') }}">
				<img src="{{ asset('assets/img/brand/Logo_Black.png') }}" class="main-logo desktop-logo" alt="logo">
				<img src="{{ asset('assets/img/brand/logo-white.png') }}" class="main-logo desktop-dark" alt="logo">
				<img src="{{ asset('assets/img/brand/favicon.png') }}" class="main-logo mobile-logo" alt="logo">
				<img src="{{ asset('assets/img/brand/favicon-white.png') }}" class="main-logo mobile-dark" alt="logo">
			</a>
		</div>

		<div class="main-sidemenu">
			<div class="slide-left disabled" id="slide-left">
				<svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
					<path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
				</svg>
			</div>

			<ul class="side-menu">
				<li class="side-item side-item-category">Main</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('flowerDashboard') }}">
						<i class="side-menu__icon fe fe-home"></i>
						<span class="side-menu__label">Flower Dashboard</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ url('admin/manage-users') }}">
						<i class="side-menu__icon fe fe-users"></i>
						<span class="side-menu__label">Manage Users</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('admin.managelocality') }}">
						<i class="side-menu__icon fe fe-map"></i>
						<span class="side-menu__label">Manage Locality</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('admin.orders.index') }}">
						<i class="side-menu__icon fe fe-shopping-cart"></i>
						<span class="side-menu__label">Manage Flower Orders</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('admin.managevendor') }}">
						<i class="side-menu__icon fe fe-briefcase"></i>
						<span class="side-menu__label">Manage Vendors</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('admin.manageRiderDetails') }}">
						<i class="side-menu__icon fe fe-user"></i>
						<span class="side-menu__label">Manage Rider</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('admin.manageOrderAssign') }}">
						<i class="side-menu__icon fe fe-layers"></i>
						<span class="side-menu__label">Apartment Assign</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ route('admin.manageflowerpickupdetails') }}">
						<i class="side-menu__icon fe fe-truck"></i>
						<span class="side-menu__label">Manage Flower Pickup</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" href="{{ url('admin/manage-delivery-history') }}">
						<i class="side-menu__icon fe fe-file-text"></i>
						<span class="side-menu__label">Delivery History</span>
					</a>
				</li>

				<li class="slide">
					<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
						<i class="side-menu__icon fe fe-plus-square"></i>
						<span class="side-menu__label">Order Creation</span>
						<i class="angle fe fe-chevron-right"></i>
					</a>
					<ul class="slide-menu">
						<li><a class="sub-side-menu__item" href="{{ url('admin/existing-user') }}">Subscription Order (Existing User)</a></li>
						<li><a class="sub-side-menu__item" href="{{ url('admin/new-user-order') }}">Subscription Order (New User)</a></li>
						<li><a class="sub-side-menu__item" href="{{ url('admin/demo-customize-order') }}">Demo Customize Order</a></li>
					</ul>
				</li>

				<li class="slide">
					<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
						<i class="side-menu__icon fe fe-message-square"></i>
						<span class="side-menu__label">Marketing</span>
						<i class="angle fe fe-chevron-right"></i>
					</a>
					<ul class="slide-menu">
						<li><a class="sub-side-menu__item" href="{{ route('admin.followUpSubscriptions') }}">Follow Up</a></li>
					</ul>
				</li>

				@if(session('admin_role') === 'admin')
					<li class="slide">
						<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
							<i class="side-menu__icon fe fe-settings"></i>
							<span class="side-menu__label">Product Admin</span>
							<i class="angle fe fe-chevron-right"></i>
						</a>
						<ul class="slide-menu">
							<li><a class="sub-side-menu__item" href="{{ route('admin.productSubscriptionOrder') }}">Manage Order</a></li>
						</ul>
					</li>
				@endif
			</ul>

			<div class="slide-right" id="slide-right">
				<svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
					<path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
				</svg>
			</div>
		</div>
	</aside>
</div>
<!-- main-sidebar -->
