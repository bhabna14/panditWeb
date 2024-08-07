@extends('admin.layouts.app')

    @section('styles')

		<!-- INTERNAL Select2 css -->
		<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />

		<!-- INTERNAL Data table css -->
		<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
		<link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
		<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    @endsection

    @section('content')

					<!-- breadcrumb -->
					<div class="breadcrumb-header justify-content-between">
						<div class="left-content">
						<span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span>
						</div>
						<div class="justify-content-center mt-2">
							<ol class="breadcrumb">
								<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Sales</li>
							</ol>
						</div>
					</div>
					<!-- /breadcrumb -->

					<!-- row -->
					<div class="row">
						<div class="col-xl-5 col-lg-12 col-md-12 col-sm-12">
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
									<div class="card">
										<div class="card-body">
											<div class="row">
												<div class="col-xl-9 col-lg-7 col-md-6 col-sm-12">
													<div class="text-justified align-items-center">
														<h3 class="text-dark font-weight-semibold mb-2 mt-0">Hi, Welcome Back <span class="text-primary">{{ Auth::guard('admins')->user()->name }}!</span></h3>
														{{-- <p class="text-dark tx-14 mb-3 lh-3"> You have used the 85% of free plan storage. Please upgrade your plan to get unlimited storage.</p>
														<button class="btn btn-primary shadow">Upgrade Now</button> --}}
													</div>
												</div>
												{{-- <div class="col-xl-3 col-lg-5 col-md-6 col-sm-12 d-flex align-items-center justify-content-center">
													<div class="chart-circle float-md-end mt-4 mt-md-0" data-value="0.85" data-thickness="8" data-color=""><canvas width="100" height="100"></canvas>
														<div class="chart-circle-value circle-style"><div class="tx-18 font-weight-semibold">85%</div></div>
													</div>
												</div> --}}
											</div>
										</div>
									</div>
								</div>
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<a href="{{url('admin/manage-pandits')}}" target="_blank">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12 ">Total Pandits</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-20 font-weight-semibold mb-2">{{ $totalPandit }}</h4>
														</div>
														{{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class="text-success font-weight-semibold"> +427</span>
														</p> --}}
													</div>
												</div>
											</div>
											<div class="col-4">
												<div class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
													<i class="fa fa-user tx-16 text-primary"></i>
												</div>
											</div>
										</div>
									</div>
									</a>
								</div>
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<a href="{{url('admin/manage-pandits')}}" target="_blank">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12">Total Pending Pandits</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-20 font-weight-semibold mb-2">{{$pendingPandit}}</h4>
														</div>
														{{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>
															<span class="font-weight-semibold text-danger"> -453</span>
														</p> --}}
													</div>
												</div>
											</div>
											<div class="col-4">
												<div class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">
													<i class="si si-user-follow tx-16 text-info"></i>
												</div>
											</div>
										</div>
									</div>
									</a>
								</div>
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<a href="{{url('admin/manage-orders')}}" target="_blank">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12">Total Orders</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-20 font-weight-semibold mb-2">{{ $totalOrder}}</h4>
														</div>
														{{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
															<span class=" text-success font-weight-semibold"> +788</span>
														</p> --}}
													</div>
												</div>
											</div>
											<div class="col-4">
												<div class="circle-icon bg-secondary-transparent text-center align-self-center overflow-hidden">
													<i class="si si-user-following tx-16 text-secondary"></i>
												</div>
											</div>
										</div>
									</div>
									</a>
								</div>
								
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<a href="{{url('admin/manage-users')}}" target="_blank">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12">Total Users</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-22 font-weight-semibold mb-2">{{$totalUser}}</h4>
														</div>
														{{-- <p class="mb-0 tx-12  text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>
															<span class="text-danger font-weight-semibold"> -693</span>
														</p> --}}
													</div>
												</div>
											</div>
											<div class="col-4">
												<div class="circle-icon bg-warning-transparent text-center align-self-center overflow-hidden">
													<i class="fa fa-user tx-16 text-primary"></i>
												</div>
											</div>
										</div>
									</div>
									</a>
								</div>
								
							</div>
						</div>
						<div class="col-xl-7 col-lg-12 col-md-12 col-sm-12">
							<div class="card custom-card overflow-hidden">
								<div class="card-header border-bottom-0">
									<div>
										<h3 class="card-title mb-2 ">Project Budget</h3> <span class="d-block tx-12 mb-0 text-muted"></span>
									</div>
								</div>
								<div class="card-body">
									<div id="statistics1"></div>
								</div>
							</div>
						
						</div>
						<!-- </div> -->
					</div>
					<!-- row closed -->

					

					<!-- row  -->
					<div class="row">
						<div class="col-12 col-sm-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Pandit List</h4>
								</div>
								<div class="card-body pt-0 example1-table">
									<div class="table-responsive">
										<table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
											<thead>
												<tr>
													<th class="border-bottom-0">Slno</th>
													<th class="border-bottom-0">Name</th>
													<th class="border-bottom-0">Registered Date</th>
													<th class="border-bottom-0">Mobile No</th>
													<th class="border-bottom-0">Blood Group</th>
													<th class="border-bottom-0">Application Status</th>
													<th class="border-bottom-0">Action</th>
												</tr>
											</thead>
                                            <tbody>
												@foreach ($pandit_profiles as $index => $profile)
												<tr>
													<td>{{ $index + 1 }}</td>
													
													<td class="tb-col">
														<div class="media-group">
															<div class="media media-md media-middle media-circle" >
																<img src="{{ asset( $profile->profile_photo) }}" alt="user">
															</div>
															<div class="media-text"  style="color: blue">
																<a  style="color: blue" href="{{ url('admin/pandit-profile/' . $profile->id) }}" class="title">{{ $profile->name }}</a>
																<span class="small text">{{$profile->email}}</span>
															</div>
														</div>
													</td>
													<td>{{ \Carbon\Carbon::parse($profile->created_at)->format('Y-m-d') }}</td>
													<td>{{ $profile->whatsappno }}</td>
													<td>{{ $profile->bloodgroup }}</td>
													<td>{{ $profile->pandit_status }}</td>
													<td>
														@if($profile->pandit_status == 'accepted')
															<form action="{{ route('rejectPandit', $profile->id) }}" method="POST" style="display:inline;">
																@csrf
																<button type="submit" class="btn btn-danger">Reject</button>
															</form>
														@elseif($profile->pandit_status == 'rejected')
															<form action="{{ route('acceptPandit', $profile->id) }}" method="POST" style="display:inline;">
																@csrf
																<button type="submit" class="btn btn-success">Accept</button>
															</form>
														@elseif($profile->pandit_status == 'pending')
															<form action="{{ route('acceptPandit', $profile->id) }}" method="POST" style="display:inline;">
																@csrf
																<button type="submit" class="btn btn-success">Accept</button>
															</form>
															<form action="{{ route('rejectPandit', $profile->id) }}" method="POST" style="display:inline;">
																@csrf
																<button type="submit" class="btn btn-danger">Reject</button>
															</form>
														@endif
													</td>
												</tr>
												@endforeach
											</tbody>
                                        </table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /row closed -->

    @endsection

    @section('scripts')

		<!-- Internal Chart.Bundle js-->
		<script src="{{asset('assets/plugins/chartjs/Chart.bundle.min.js')}}"></script>

		<!-- Moment js -->
		<script src="{{asset('assets/plugins/raphael/raphael.min.js')}}"></script>

		<!-- INTERNAL Apexchart js -->
		<script src="{{asset('assets/js/apexcharts.js')}}"></script>

		<!--Internal Sparkline js -->
		<script src="{{asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>

		<!--Internal  index js -->
		<script src="{{asset('assets/js/index.js')}}"></script>

        <!-- Chart-circle js -->
		<script src="{{asset('assets/js/chart-circle.js')}}"></script>

		<!-- Internal Data tables -->
		<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
		<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
		<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
		<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>

		<!-- INTERNAL Select2 js -->
		<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
		<script src="{{asset('assets/js/select2.js')}}"></script>

    @endsection
