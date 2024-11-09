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
									
									<p class="text-muted ms-md-4 ms-0 mb-2">
										<span><i class="fa fa-phone me-2"></i></span><span class="font-weight-semibold me-2">Phone:</span><span>{{ $user->mobile_number }}</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2">
										<span><i class="fa fa-envelope me-2"></i></span><span class="font-weight-semibold me-2">Email:</span><span>{{ $user->email }}</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2">
										<span><i class="fa fa-venus-mars me-2" aria-hidden="true"></i></span><span class="font-weight-semibold me-2">Gender:</span><span>{{ $user->gender }}</span>
									</p>
									
								</div>
							</div>
							<div class="card-footer py-0">
								<div class="profile-tab tab-menu-heading border-bottom-0">
									<nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0">
										<a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#about">About</a>
										<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#order">Orders</a>
										<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#address">Login Devices</a>

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
								
								<div class="main-content-body tab-pane  border-top-0 active" id="about">
										<div class="card">
											<div class="card-body p-0 border-0 rounded-10">
												<div class="p-4">
													<h4 class="tx-15 text-uppercase mb-3">About</h4>
													<p class="m-b-5"></p>
													
												</div>
												
												
												<div class="border-top"></div>
												
											</div>
										</div>
								</div>
								
								
								<div class="main-content-body  tab-pane border-top-0" id="order">
									<div class="border-0">
										<div class="main-content-body main-content-body-profile">
											<div class="main-profile-body p-0">
												<div class="row row-sm">
													<div class="col-12">
														<table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
															
														</table>
													</div>
												</div>
											</div>
											<!-- main-profile-body -->
										</div>
									</div>
								</div>

								<div class="main-content-body  tab-pane border-top-0" id="address">
									<div class="border-0">
										<div class="main-content-body main-content-body-profile">
											<div class="main-profile-body p-0">
												<div class="row row-sm">
													<div class="col-12">
														<table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
															
														</table>
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
