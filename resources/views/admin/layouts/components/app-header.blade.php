				<!-- main-header -->
				<div class="main-header side-header sticky nav nav-item">
					<div class=" main-container container-fluid">
						<div class="main-header-left ">
							<div class="responsive-logo">
								<a href="{{url('admin.dashboard')}}" class="header-logo">
									<img src="{{asset('assets/img/brand/Logo_Black.png')}}" class="mobile-logo logo-1" alt="logo">
									<img src="{{asset('assets/img/brand/logo-white.png')}}" class="mobile-logo dark-logo-1" alt="logo">
								</a>
							</div>
							<div class="app-sidebar__toggle" data-bs-toggle="sidebar">
								<a class="open-toggle" href="javascript:void(0);"><i class="header-icon fe fe-align-left" ></i></a>
								<a class="close-toggle" href="javascript:void(0);"><i class="header-icon fe fe-x"></i></a>
							</div>
							<div class="logo-horizontal">
								<a href="{{url('admin.dashboard')}}" class="header-logo">
									<img src="{{asset('assets/img/brand/Logo_Black.png')}}" class="mobile-logo logo-1" alt="logo">
									<img src="{{asset('assets/img/brand/logo-white.png')}}" class="mobile-logo dark-logo-1" alt="logo">
								</a>
							</div>
							
							<li class="nav-item ps-1">
								<span id="google_translate_element"></span>
							</li>
						</div>
						<div class="main-header-center">
							<div class="d-flex align-items-center">
								<div class="me-3">
									<span class="fw-bold" id="current-date"></span>
								</div>
								<div>
									<span class="fw-bold" id="current-time"></span>
								</div>
							</div>
						</div>
						<div class="main-header-right">
							<button class="navbar-toggler navresponsive-toggler d-md-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon fe fe-more-vertical "></span>
							</button>
						

							<div class="mb-0 navbar navbar-expand-lg navbar-nav-right responsive-navbar navbar-dark p-0">
								<div class="collapse navbar-collapse" id="navbarSupportedContent-4">
									<ul class="nav nav-item header-icons navbar-nav-right ms-auto">
										<li class="dropdown nav-item main-header-notification d-flex">
											<a class="new nav-link" data-bs-toggle="dropdown" href="javascript:void(0);">
												<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" width="24" height="24" viewBox="0 0 24 24">
													<path d="M19 13.586V10c0-3.217-2.185-5.927-5.145-6.742C13.562 2.52 12.846 2 12 2s-1.562.52-1.855 1.258C7.185 4.074 5 6.783 5 10v3.586l-1.707 1.707A.996.996 0 0 0 3 16v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-2a.996.996 0 0 0-.293-.707L19 13.586zM19 17H5v-.586l1.707-1.707A.996.996 0 0 0 7 14v-4c0-2.757 2.243-5 5-5s5 2.243 5 5v4c0 .266.105.52.293.707L19 16.414V17zm-7 5a2.98 2.98 0 0 0 2.818-2H9.182A2.98 2.98 0 0 0 12 22z"/>
												</svg>
												<span class="pulse"></span>
											</a>
											

											<div class="dropdown-menu">
												<div class="menu-header-content text-start border-bottom">
													<div class="d-flex">
														<h6 class="dropdown-title mb-1 tx-15 font-weight-semibold">Notifications</h6>
													</div>
												</div>
												
												<div class="main-notification-list Notification-scroll">
												</div>
												<div class="dropdown-footer">
													<a class="btn btn-primary btn-sm btn-block" href="#">VIEW ALL</a>
												</div>
											</div>
										</li>
										
										<li class="dropdown nav-item">
											<a href="{{url('/')}}" class="btn btn-primary shadow" target="_blank">Visit Website</i></a>
										</li>
										
										
										<li class="dropdown main-profile-menu nav nav-item nav-link ps-lg-2">
											<a class="new nav-link profile-user d-flex" href="" data-bs-toggle="dropdown"><img alt="" src="{{asset('assets/img/faces/2.jpg')}}" class=""></a>
											<div class="dropdown-menu">
												<div class="menu-header-content p-3 border-bottom">
													<div class="d-flex wd-100p">
														<div class="main-img-user"><img alt="" src="{{asset('assets/img/faces/2.jpg')}}" class=""></div>
														<div class="ms-3 my-auto">
															<h6 class="tx-15 font-weight-semibold mb-0">{{ Auth::guard('admins')->user()->name }}</h6><span class="dropdown-title-text subtext op-6  tx-12">Premium Member</span>
														</div>
													</div>
												</div>
												{{-- <a class="dropdown-item" href="{{url('profile')}}"><i class="far fa-user-circle"></i>Profile</a> --}}
												{{-- <a class="dropdown-item" href="{{url('chat')}}"><i class="far fa-smile"></i> chat</a>
												<a class="dropdown-item" href="{{url('mail-read')}}"><i class="far fa-envelope "></i>Inbox</a>
												<a class="dropdown-item" href="{{url('mail')}}"><i class="far fa-comment-dots"></i>Messages</a> --}}
												{{-- <a class="dropdown-item" href="{{url('mail-settings')}}"><i class="far fa-sun"></i>  Settings</a> --}}
												<form method="POST" action="{{ url('admin/logout') }}">
													@csrf
													<button type="submit" class="dropdown-item"><i class="far  fa-arrow-alt-circle-left"></i> Logout</button>
												</form>

												<!-- <a class="dropdown-item" href="{{url('logout')}}"><i class="far fa-arrow-alt-circle-left"></i> Sign Out</a> -->
											</div>
										</li>
									</ul>
								</div>
                                <!--place for switcher icon-->
							</div>
						</div>
					</div>
				</div>
				<!-- /main-header -->
