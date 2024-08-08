@extends('admin.layouts.app')

    @section('styles')

	<!--- Internal Select2 css-->
	<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

	<!--  smart photo master css -->
	<link href="{{asset('assets/plugins/SmartPhoto-master/smartphoto.css')}}" rel="stylesheet">

    @endsection

    @section('content')

				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<span class="main-content-title mg-b-0 mg-b-lg-1">PROFILE</span>
					</div>
					<div class="justify-content-center mt-2">
						<ol class="breadcrumb">
							<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
							<li class="breadcrumb-item active" aria-current="page">Profile</li>
						</ol>
					</div>
				</div>
				<!-- /breadcrumb -->

				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card custom-card">
							<div class="card-body d-md-flex">
								<div class="">
									<span class="profile-image pos-relative">
										<img class="br-5" alt="" src="{{ asset($user->userphoto ? 'storage/' . $user->userphoto : 'front-assets/img/images.jfif') }}">
										<span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
									</span>
								</div>
								<div class="my-md-auto mt-4 prof-details">
									<h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">{{ $user->name }}</h4>
									<p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2">
										<span class="me-3"><i class="far fa-address-card me-2"></i>{{ $user->role }}</span>
										<span class="me-3"><i class="fa fa-taxi me-2"></i>{{ $user->address }}</span>
										<span><i class="far fa-flag me-2"></i>{{ $user->city }}</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2">
										<span><i class="fa fa-phone me-2"></i></span><span class="font-weight-semibold me-2">Phone:</span><span>{{ $user->phone_number }}</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2">
										<span><i class="fa fa-envelope me-2"></i></span><span class="font-weight-semibold me-2">Email:</span><span>{{ $user->email }}</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2">
										<span><i class="fa fa-globe me-2"></i></span><span class="font-weight-semibold me-2">Website:</span><span>{{ $user->website }}</span>
									</p>
								</div>
							</div>
							<div class="card-footer py-0">
								<div class="profile-tab tab-menu-heading border-bottom-0">
									<nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0">
										<a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#about">About</a>
										<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#timeline">Bookings</a>
									</nav>
								</div>
							</div>
						</div>
					</div>
				</div>
				

				<!-- Row -->
				<div class="row row-sm">
					<div class="col-lg-12 col-md-12">
						<div class="custom-card main-content-body-profile">
							<div class="tab-content">
								<div class="main-content-body tab-pane  active" id="about">
									<div class="card">
										<div class="card-body p-0 border-0 p-0 rounded-10">
											<div class="p-4">
												<h4 class="tx-15 text-uppercase mb-3">BIOdata</h4>
												<p class="m-b-5">Hi I'm Teri Dactyl,has been the industry's standard
													dummy text ever since the 1500s, when an unknown printer took a
													galley of type. Donec pede justo, fringilla vel, aliquet nec,
													vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a,
													venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.
													Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi.
													Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu,
													consequat vitae, eleifend ac, enim.</p>
												<div class="m-t-30">
													<div class=" p-t-10">
														<h5 class="text-primary m-b-5 tx-14">Lead designer / Developer
														</h5>
														<p class="">websitename.com</p>
														<p><b>2010-2015</b></p>
														<p class="text-muted tx-13 m-b-0">Lorem Ipsum is simply dummy
															text of the printing and typesetting industry. Lorem Ipsum
															has been the industry's standard dummy text ever since the
															1500s, when an unknown printer took a galley of type and
															scrambled it to make a type specimen book.</p>
													</div>

													<div class="">
														<h5 class="text-primary m-b-5 tx-14">Senior Graphic Designer
														</h5>
														<p class="">coderthemes.com</p>
														<p><b>2007-2009</b></p>
														<p class="text-muted tx-13 mb-0">Lorem Ipsum is simply dummy
															text of the printing and typesetting industry. Lorem Ipsum
															has been the industry's standard dummy text ever since the
															1500s, when an unknown printer took a galley of type and
															scrambled it to make a type specimen book.</p>
													</div>
												</div>
											</div>
											<div class="border-top"></div>
											<div class="p-4">
												<label class="main-content-label tx-13 mg-b-20">Statistics</label>
												<div class="profile-cover__info ms-4 ms-auto p-0">
													<ul class="nav p-0 border-bottom-0 mb-0">
														<li class="border p-2 br-5 bg-light wd-100 ht-70"><span
																class="border-0 mb-0 pb-0">113</span>Projects</li>
														<li class="border p-2 br-5 bg-light wd-100 ht-70"><span
																class="border-0 mb-0 pb-0">245</span>Followers</li>
														<li class="border p-2 br-5 bg-light wd-100 ht-70"><span
																class="border-0 mb-0 pb-0">128</span>Following</li>
													</ul>
												</div>
											</div>
											<div class="border-top"></div>
											<div class="p-4">
												<label class="main-content-label tx-13 mg-b-20">Contact</label>
												<div class="d-sm-flex">
													<div class="mg-sm-r-20 mg-b-10">
														<div class="main-profile-contact-list">
															<div class="media">
																<div
																	class="media-icon bg-primary-transparent text-primary">
																	<i class="icon ion-md-phone-portrait"></i>
																</div>
																<div class="media-body"> <span>Mobile</span>
																	<div> +245 354 654 </div>
																</div>
															</div>
														</div>
													</div>
													<div class="mg-sm-r-20 mg-b-10">
														<div class="main-profile-contact-list">
															<div class="media">
																<div
																	class="media-icon bg-success-transparent text-success">
																	<i class="icon ion-logo-slack"></i>
																</div>
																<div class="media-body"> <span>Slack</span>
																	<div> @spruko.w </div>
																</div>
															</div>
														</div>
													</div>
													<div class="">
														<div class="main-profile-contact-list">
															<div class="media">
																<div class="media-icon bg-info-transparent text-info">
																	<i class="icon ion-md-locate"></i>
																</div>
																<div class="media-body"> <span>Current Address</span>
																	<div> San Francisco, CA </div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="border-top"></div>
											<div class="p-4">
												<label class="main-content-label tx-13 mg-b-20">Social</label>
												<div class="d-lg-flex">
													<div class="mg-md-r-20 mg-b-10">
														<div class="main-profile-social-list">
															<div class="media">
																<div
																	class="media-icon bg-primary-transparent text-primary">
																	<i class="icon ion-logo-github"></i>
																</div>
																<div class="media-body"> <span>Github</span> <a
																		href="">github.com/spruko</a> </div>
															</div>
														</div>
													</div>
													<div class="mg-md-r-20 mg-b-10">
														<div class="main-profile-social-list">
															<div class="media">
																<div
																	class="media-icon bg-success-transparent text-success">
																	<i class="icon ion-logo-twitter"></i>
																</div>
																<div class="media-body"> <span>Twitter</span> <a
																		href="">twitter.com/spruko.me</a> </div>
															</div>
														</div>
													</div>
													<div class="mg-md-r-20 mg-b-10">
														<div class="main-profile-social-list">
															<div class="media">
																<div class="media-icon bg-info-transparent text-info">
																	<i class="icon ion-logo-linkedin"></i>
																</div>
																<div class="media-body"> <span>Linkedin</span> <a
																		href="">linkedin.com/in/spruko</a> </div>
															</div>
														</div>
													</div>
													<div class="mg-md-r-20 mg-b-10">
														<div class="main-profile-social-list">
															<div class="media">
																<div
																	class="media-icon bg-danger-transparent text-danger">
																	<i class="icon ion-md-link"></i>
																</div>
																<div class="media-body"> <span>My Portfolio</span> <a
																		href="">spruko.com/</a> </div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="main-content-body  tab-pane border-top-0" id="timeline">
									<div class="border-0">
										<div class="main-content-body main-content-body-profile">
											<div class="main-profile-body p-0">
												<div class="row row-sm">
													<div class="col-12">
														<div class="card mg-b-20 border">
															<div class="card-header p-4">
																<div class="media">
																	<div class="media-user me-2">
																		<div class="main-img-user avatar-md"><img alt=""
																				class="rounded-circle"
																				src="{{asset('assets/img/faces/6.jpg')}}"></div>
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 mg-t-2 ms-2">Mintrona Pechon
																			Pechon</h6><span
																			class="text-primary ms-2">just now</span>
																	</div>
																	<div class="ms-auto">
																		<div class="dropdown show main-contact-star"> <a
																				class="new option-dots2"
																				data-bs-toggle="dropdown"
																				href="JavaScript:void(0);"><i
																					class="fe fe-more-vertical  tx-18"></i></a>
																			<div class="dropdown-menu shadow"> <a
																					class="dropdown-item"
																					href="javascript:void(0);">Edit
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Delete
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Personal
																					Settings</a> </div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="card-body">
																<p class="mg-t-0">There are many variations of passages
																	of Lorem Ipsum available, but the majority have
																	suffered alteration in some form, by injected
																	humour, or randomised words which don't look even
																	slightly believable.</p>
																<div class="row row-sm">
																	<div class="col">
																		<a href="{{url('gallery')}}"><img alt="img" class="wd-200 br-5 mb-2 mt-2 me-4"
																			src="{{asset('assets/img/media/1.jpg')}}"></a>
																		<a href="{{url('gallery')}}"><img alt="img" class="wd-200 br-5"
																			src="{{asset('assets/img/media/2.jpg')}}"></a>
																	</div>
																</div>
																<div class="media mg-t-15 profile-footer">
																	<div class="media-user me-2">
																		<div class="demo-avatar-group">
																			<div
																				class="demo-avatar-group main-avatar-list-stacked">
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/12.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/12.jpg')}}">
																				</div>
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/5.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/6.jpg')}}">
																				</div>
																				<div class="main-avatar"> +23 </div>
																			</div>
																			<!-- demo-avatar-group -->
																		</div>
																		<!-- demo-avatar-group -->
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 mg-t-10">28 people like your
																			photo</h6>
																	</div>

																</div>
															</div>
														</div>
														<div class="card mg-b-20 border">
															<div class="card-header p-4">
																<div class="media">
																	<div class="media-user me-2">
																		<div class="main-img-user avatar-md">
																			<img alt="" class="rounded-circle"
																				src="{{asset('assets/img/faces/6.jpg')}}">
																		</div>
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 ms-2 mg-t-3">Mintrona Pechon
																			Pechon</h6><span class="text-muted ms-2">Sep
																			26 2019, 10:14am</span>
																	</div>
																	<div class="ms-auto">
																		<div class="dropdown show main-contact-star"> <a
																				class="new option-dots2"
																				data-bs-toggle="dropdown"
																				href="JavaScript:void(0);"><i
																					class="fe fe-more-vertical  tx-18"></i></a>
																			<div class="dropdown-menu shadow"> <a
																					class="dropdown-item"
																					href="javascript:void(0);">Edit
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Delete
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Personal
																					Settings</a> </div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="card-body h-100">
																<p class="mg-t-0">There are many variations of passages
																	of Lorem Ipsum available, but the majority have
																	suffered alteration in some form, by injected
																	humour, or randomised words which don't look even
																	slightly believable.</p>
																<div class="row row-sm">
																	<div class="col">
																		<a href="{{url('gallery')}}"><img alt="img" class="wd-200 br-5 mb-2 mt-2 me-4"
																			src="{{asset('assets/img/media/4.jpg')}}"></a>
																		<a href="{{url('gallery')}}"><img alt="img" class="wd-200 br-5 mb-2 mt-2"
																			src="{{asset('assets/img/media/1.jpg')}}"></a>
																	</div>
																</div>
																<div class="media mg-t-15 profile-footer">
																	<div class="media-user me-2">
																		<div class="demo-avatar-group">
																			<div
																				class="demo-avatar-group main-avatar-list-stacked">
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/12.jpg')}}">
																				</div>
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/7.jpg')}}">
																				</div>
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/5.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/6.jpg')}}">
																				</div>
																				<div class="main-avatar"> +23 </div>
																			</div>
																			<!-- demo-avatar-group -->
																		</div>
																		<!-- demo-avatar-group -->
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 mg-t-10">28 people like your
																			photo</h6>
																	</div>

																</div>
															</div>
														</div>
														<div class="card mg-b-20 border">
															<div class="card-header p-4">
																<div class="media">
																	<div class="media-user me-2">
																		<div class="main-img-user avatar-md"><img alt=""
																				class="rounded-circle"
																				src="{{asset('assets/img/faces/6.jpg')}}"></div>
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 ms-2 mg-t-3">Mintrona Pechon
																			Pechon</h6><span class="text-muted ms-2">Sep
																			26 2019, 10:14am</span>
																	</div>
																	<div class="ms-auto">
																		<div class="dropdown show main-contact-star"> <a
																				class="new option-dots2"
																				data-bs-toggle="dropdown"
																				href="JavaScript:void(0);"><i
																					class="fe fe-more-vertical  tx-18"></i></a>
																			<div class="dropdown-menu shadow"> <a
																					class="dropdown-item"
																					href="javascript:void(0);">Edit
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Delete
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Personal
																					Settings</a> </div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="card-body h-100">
																<p class="mg-t-0">There are many variations of passages
																	of Lorem Ipsum available, but the majority have
																	suffered alteration in some form, by injected
																	humour, or randomised words which don't look even
																	slightly believable.</p>
																<div class="media mg-t-15 profile-footer">
																	<div class="media-user me-2">
																		<div class="demo-avatar-group">
																			<div
																				class="demo-avatar-group main-avatar-list-stacked">
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/12.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/3.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/4.jpg')}}">
																				</div>
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/10.jpg')}}">
																				</div>
																				<div class="main-avatar"> +23 </div>
																			</div>
																			<!-- demo-avatar-group -->
																		</div>
																		<!-- demo-avatar-group -->
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 mg-t-10">28 people like your
																			photo</h6>
																	</div>

																</div>
															</div>
														</div>
														<div class="card border">
															<div class="card-header p-4">
																<div class="media">
																	<div class="media-user me-2">
																		<div class="main-img-user avatar-md"><img alt=""
																				class="rounded-circle"
																				src="{{asset('assets/img/faces/2.jpg')}}"></div>
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 ms-2 mg-t-3">Mintrona Pechon
																			Pechon</h6><span class="text-muted ms-2">Sep
																			26 2019, 10:14am</span>
																	</div>
																	<div class="ms-auto">
																		<div class="dropdown show main-contact-star"> <a
																				class="new option-dots2"
																				data-bs-toggle="dropdown"
																				href="JavaScript:void(0);"><i
																					class="fe fe-more-vertical  tx-18"></i></a>
																			<div class="dropdown-menu shadow"> <a
																					class="dropdown-item"
																					href="javascript:void(0);">Edit
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Delete
																					Post</a> <a class="dropdown-item"
																					href="javascript:void(0);">Personal
																					Settings</a> </div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="card-body h-100">
																<p class="mg-t-0">There are many variations of passages
																	of Lorem Ipsum available, but the majority have
																	suffered alteration in some form, by injected
																	humour, or randomised words which don't look even
																	slightly believable.</p>
																<div class="row row-sm">
																	<div class="col">
																		<a href="{{url('gallery')}}"><img alt="img" class="wd-200 br-5 mb-2 mt-2 me-3"
																			src="{{asset('assets/img/media/4.jpg')}}"></a>
																		<a href="{{url('gallery')}}"><img alt="img" class="wd-200 br-5 mb-2 mt-2"
																			src="{{asset('assets/img/media/3.jpg')}}"></a>
																	</div>
																</div>
																<div class="media mg-t-15 profile-footer">
																	<div class="media-user me-2">
																		<div class="demo-avatar-group">
																			<div
																				class="demo-avatar-group main-avatar-list-stacked">
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/11.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/12.jpg')}}">
																				</div>
																				<div class="main-img-user"><img alt=""
																						class="rounded-circle"
																						src="{{asset('assets/img/faces/3.jpg')}}">
																				</div>
																				<div class="main-img-user online"><img
																						alt="" class="rounded-circle"
																						src="{{asset('assets/img/faces/5.jpg')}}">
																				</div>
																				<div class="main-avatar"> +23 </div>
																			</div>
																			<!-- demo-avatar-group -->
																		</div>
																		<!-- demo-avatar-group -->
																	</div>
																	<div class="media-body">
																		<h6 class="mb-0 mg-t-10">28 people like your
																			photo</h6>
																	</div>

																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- main-profile-body -->
										</div>
									</div>
								</div>
							
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->

    @endsection

    @section('scripts')

        <!-- Internal Select2 js-->
        <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
        <script src="{{asset('assets/js/select2.js')}}"></script>

        <!-- smart photo master js -->
        <script src="{{asset('assets/plugins/SmartPhoto-master/smartphoto.js')}}"></script>
        <script src="{{asset('assets/js/gallery.js')}}"></script>

    @endsection
