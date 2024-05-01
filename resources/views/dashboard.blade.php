@extends('layouts.app')

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
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12 ">Total Sebayat</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-20 font-weight-semibold mb-2">{{ $userCount}}</h4>
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
								</div>
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12">Total Pending Sebayat</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-20 font-weight-semibold mb-2">{{ $pendinguser }}</h4>
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
								</div>
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12">Total Approved Sebayat</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-20 font-weight-semibold mb-2">{{$approuser}}</h4>
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
								</div>
								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
									<div class="card sales-card">
										<div class="row">
											<div class="col-8">
												<div class="ps-4 pt-4 pe-3 pb-4">
													<div class="">
														<h6 class="mb-2 tx-12">Total Rejected Sebayat</h6>
													</div>
													<div class="pb-0 mt-0">
														<div class="d-flex">
															<h4 class="tx-22 font-weight-semibold mb-2">{{$rejecteduser}}</h4>
														</div>
														{{-- <p class="mb-0 tx-12  text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>
															<span class="text-danger font-weight-semibold"> -693</span>
														</p> --}}
													</div>
												</div>
											</div>
											<div class="col-4">
												<div class="circle-icon bg-warning-transparent text-center align-self-center overflow-hidden">
													<i class="si si-user-unfollow tx-16 text-warning"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
									<div class="card">
										<div class="card-header pb-1">
											<h3 class="card-title mb-2">Browser Usage</h3>
										</div>
										<div class="card-body p-0">
											<div class="browser-stats">
												<div class="d-flex align-items-center item  border-bottom my-2">
													<div class="d-flex">
														<img src="{{asset('assets/img/svgicons/chrome.svg')}}" alt="img" class="ht-30 wd-30 me-2">
														<div class="">
															<h6 class="">Chrome</h6>
															<span class="text-muted tx-12">Google, Inc.</span>
														</div>
													</div>
													<div class="ms-auto my-auto">
														<div class="d-flex">
															<span class="me-4 mt-1 font-weight-semibold tx-16">35,502</span>
															<span class="text-success fs-13 my-auto"><i class="fe fe-trending-up text-success me-2 ms-1 my-auto"></i>12.75%</span>
														</div>
													</div>
												</div>
												<div class="d-flex align-items-center item  border-bottom my-2">
													<div class="d-flex">
														<img src="{{asset('assets/img/svgicons/edge.svg')}}" alt="img" class="ht-30 wd-30 me-2">
														<div class="">
															<h6 class="">Edge</h6>
															<span class="text-muted tx-12">Microsoft Corporation, Inc.</span>
														</div>
													</div>
													<div class="ms-auto my-auto">
														<div class="d-flex">
															<span class="me-4 mt-1 font-weight-semibold tx-16">25,364</span>
															<span class="text-success"><i class="fe fe-trending-down text-danger me-2 ms-1 my-auto"></i>24.37%</span>
														</div>
													</div>
												</div>
												<div class="d-flex align-items-center item  border-bottom my-2">
													<div class="d-flex">
														<img src="{{asset('assets/img/svgicons/firefox.svg')}}" alt="img" class="ht-30 wd-30 me-2">
														<div class="">
															<h6 class="">Firefox</h6>
															<span class="text-muted tx-12">Mozilla Foundation, Inc.</span>
														</div>
													</div>
													<div class="ms-auto my-auto">
														<div class="d-flex">
															<span class="me-4 mt-1 font-weight-semibold tx-16">14,635</span>
															<span class="text-success"><i class="fe fe-trending-up text-success me-2 ms-1 my-auto"></i>15,63%</span>
														</div>
													</div>
												</div>
												<div class="d-flex align-items-center item  border-bottom my-2">
													<div class="d-flex">
														<img src="{{asset('assets/img/svgicons/safari.svg')}}" alt="img" class="ht-30 wd-30 me-2">
														<div class="">
															<h6 class="">Safari</h6>
															<span class="text-muted tx-12">Apple Corporation, Inc.</span>
														</div>
													</div>
													<div class="ms-auto my-auto">
														<div class="d-flex">
															<span class="me-4 mt-1 font-weight-semibold tx-16">35,657</span>
															<span class="text-danger"><i class="fe fe-trending-up text-success me-2 ms-1 my-auto"></i>12.54%</span>
														</div>
													</div>
												</div>
												<div class="d-flex align-items-center item my-2">
													<div class="d-flex">
														<img src="{{asset('assets/img/svgicons/opera.svg')}}" alt="img" class="ht-30 wd-30 me-2">
														<div class="">
															<h6 class="">Opera</h6>
															<span class="text-muted tx-12">Opera, Inc.</span>
														</div>
													</div>
													<div class="ms-auto my-auto">
														<div class="d-flex">
															<span class="me-4 mt-1 font-weight-semibold tx-16">12,563</span>
															<span class="text-danger"><i class="fe fe-trending-down text-danger me-2 ms-1 my-auto"></i>15.12%</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
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
							<div class="row">
								<div class="col-sm-12 col-lg-12 col-xl-6">
									<div class="card overflow-hidden">
										<div class="card-header pb-1">
											<h3 class="card-title mb-2">Recent Customers</h3>
										</div>
										<div class="card-body p-0 customers mt-1">
											<div class="list-group list-lg-group list-group-flush">
												<a href="javascript:void(0);" class="border-0">
												<div class="list-group-item list-group-item-action border-0">
													<div class="media mt-0">
														<img class="avatar-lg rounded-circle me-3 my-auto shadow" src="{{asset('assets/img/faces/2.jpg')}}" alt="Image description">
														<div class="media-body">
															<div class="d-flex align-items-center">
																<div class="mt-0">
																	<h5 class="mb-1 tx-13 font-weight-sembold text-dark">Samantha Melon</h5>
																	<p class="mb-0 tx-12 text-muted">User ID: #1234</p>
																</div>
																<span class="ms-auto wd-45p tx-14">
																	<span class="float-end badge badge-success-transparent">
																	<span class="op-7 text-success font-weight-semibold">paid </span>
																</span>
																</span>
															</div>
														</div>
													</div>
												</div>
												</a>
												<a href="javascript:void(0);" class="border-0">
													<div class="list-group-item list-group-item-action border-0" >
														<div class="media mt-0">
															<img class="avatar-lg rounded-circle me-3 my-auto shadow" src="{{asset('assets/img/faces/1.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-flex align-items-center">
																	<div class="mt-1">
																		<h5 class="mb-1 tx-13 font-weight-sembold text-dark">Allie Grater</h5>
																		<p class="mb-0 tx-12 text-muted">User ID: #1234</p>
																	</div>
																	<span class="ms-auto wd-45p tx-14">
																		<span class="float-end badge badge-danger-transparent ">
																		<span class="op-7 text-danger font-weight-semibold">Pending</span>
																	</span>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</a>
												<a href="javascript:void(0);" class="border-0">
													<div class="list-group-item list-group-item-action border-0" >
														<div class="media mt-0">
															<img class="avatar-lg rounded-circle me-3 my-auto shadow" src="{{asset('assets/img/faces/5.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-flex align-items-center">
																	<div class="mt-1">
																		<h5 class="mb-1 tx-13 font-weight-sembold text-dark">Gabe Lackmen</h5>
																		<p class="mb-0 tx-12 text-muted">User ID: #1234</p>
																	</div>
																	<span class="ms-auto wd-45p  tx-14">
																		<span class="float-end badge badge-danger-transparent ">
																		<span class="op-7 text-danger font-weight-semibold">Pending</span>
																	</span>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</a>
												<a href="javascript:void(0);" class="border-0">
													<div class="list-group-item list-group-item-action border-0" >
														<div class="media mt-0">
															<img class="avatar-lg rounded-circle me-3 my-auto shadow" src="{{asset('assets/img/faces/7.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-flex align-items-center">
																	<div class="mt-1">
																		<h5 class="mb-1 tx-13 font-weight-sembold text-dark">Manuel Labor</h5>
																		<p class="mb-0 tx-12 text-muted">User ID: #1234</p>
																	</div>
																	<span class="ms-auto wd-45p tx-14">
																	<span class="float-end badge badge-success-transparent ">
																	<span class="op-7 text-success font-weight-semibold">Paid</span>
																</span>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</a>
												<a href="javascript:void(0);" class="border-0">
													<div class="list-group-item list-group-item-action border-0" >
														<div class="media mt-0">
															<img class="avatar-lg rounded-circle me-3 my-auto shadow" src="{{asset('assets/img/faces/9.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-flex align-items-center">
																	<div class="mt-1">
																		<h5 class="mb-1 tx-13 font-weight-sembold text-dark">Hercules Bing</h5>
																		<p class="mb-0 tx-12 text-muted">User ID: #1754</p>
																	</div>
																	<span class="ms-auto wd-45p tx-14">
																	<span class="float-end badge badge-success-transparent ">
																	<span class="op-7 text-success font-weight-semibold">Paid</span>
																</span>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</a>
												<a href="javascript:void(0);" class="border-0">
													<div class="list-group-item list-group-item-action border-0" >
														<div class="media mt-0">
															<img class="avatar-lg rounded-circle me-3 my-auto shadow" src="{{asset('assets/img/faces/11.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-flex align-items-center">
																	<div class="mt-1">
																		<h5 class="mb-1 tx-13 font-weight-sembold text-dark">Manuel Labor</h5>
																		<p class="mb-0 tx-12 text-muted">User ID: #1234</p>
																	</div>
																	<span class="ms-auto wd-45p tx-14">
																		<span class="float-end badge badge-danger-transparent ">
																		<span class="op-7 text-danger font-weight-semibold">Pending</span>
																	</span>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-12 col-xl-6">
									<div class="card">
										<div class="card-header pb-3">
											<h3 class="card-title mb-2">MAIN TASKS</h3>
										</div>
										<div class="card-body p-0 customers mt-1">
											<div class="">
												<label class="p-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto">
														accurate information at any given point.
													</span>
													<span class="ms-auto"><span class="badge badge-primary-transparent font-weight-semibold px-2 py-1 tx-11 me-2">Today</span></span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto">
														sharing the information with clients or stakeholders.
													</span>
													<span class="ms-auto"><span class="badge badge-primary-transparent font-weight-semibold px-2 py-1 tx-11 me-2">Today</span></span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto">
														Hearing the information and responding .
													</span>
													<span class="ms-auto"><span class="badge badge-primary-transparent font-weight-semibold px-2 py-1 tx-11 me-2 float-end">22 hrs</span></span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto">
														Setting up and customizing your own sales.
													</span>
													<span class="ms-auto"> <span class="badge badge-light-transparent font-weight-semibold px-2 py-1 tx-11 me-2">1 Day</span></span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input checked="" type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto">
														To have a complete 360° overview of sales information, having.
													</span>
													<span class="ms-auto"> <span class="badge badge-light-transparent font-weight-semibold px-2 py-1 tx-11 me-2">2 Days</span></span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input checked="" type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto">
														New Admin Launched.
													</span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input checked="" type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto mb-4">
														To maximize profits and improve productivity.
													</span>
												</label>
												<label class="p-2 mt-2 d-flex">
													<span class="check-box mb-0 ms-2">
														<span class="ckbox"><input checked="" type="checkbox"><span></span></span>
													</span>
													<span class="ms-3 me-5 my-auto mb-4">
														To improve profits.
													</span>
												</label>
											</div>
										</div>
									</div>
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
									<h4 class="card-title">Sebayat List</h4>
								</div>
								<div class="card-body pt-0 example1-table">
									<div class="table-responsive">
										<table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                                            <thead>
                                                <tr>
                                                <th class="border-bottom-0">#</th>

                                                    <th class="border-bottom-0">Name</th>
                                                    <th class="border-bottom-0">Registered Date</th>
                                                    <th class="border-bottom-0">Approved Date</th>
                                                    <th class="border-bottom-0">Added By</th>
                                                    <th class="border-bottom-0">Application Status</th>
                                                    
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($sebayatlists as $index => $sebayatlist)
                                            
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <a href="{{url('admin/viewsebayat/'.$sebayatlist->userid)}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                @if($sebayatlist->userphoto == '')
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                                @else
                                                                    <img src="{{asset('assets/uploads/userphoto/') }}/{{$sebayatlist->userphoto}}" alt="user">
                                                                @endif
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/viewsebayat/'.$sebayatlist->userid)}}" class="title">{{ $sebayatlist->first_name}} {{ $sebayatlist->last_name }}</a>
                                                                <span class="small text">{{ $sebayatlist->email }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>{{ $sebayatlist->created_at }}</td>
                                                   <td>{{ $sebayatlist->approved_date =="" ? "N/A" :  date_format($sebayatlist->approved_date,"j F Y")}}</td>
                                                   <td>{{  $sebayatlist->added_by }}</td>
                                                    <td>
                                                        @if($sebayatlist->application_status == 'rejected')
                                                            <span class="badge badge-primary">Rejected</span>
                                                        @elseif($sebayatlist->application_status == 'pending')
                                                          <span class="badge badge-orange">Pending</span>
                                                        @else
                                                            <span class="badge badge-success">{{ $sebayatlist->application_status }}</span> 
                                                        @endif
                                                    </td>
                                                    
                                                    <td>
                                                        <a href="{{url('admin/viewsebayat/'.$sebayatlist->userid)}}"><i class="fas fa-eye"></i></a> | 
                                                        <a href="{{url('admin/editsebayat/'.$sebayatlist->userid)}}"><i class="fa fa-edit"></i></a> | 
                                                        <a href="{{url('admin/dltsebayat/'.$sebayatlist->userid)}}" onClick="return confirm('Are you sure to delete ?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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
