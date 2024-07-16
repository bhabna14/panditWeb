@extends('pandit.layouts.app')

    @section('styles')

	<!--- Internal Select2 css-->
	<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

	<!--  smart photo master css -->
	<link href="{{asset('assets/plugins/SmartPhoto-master/smartphoto.css')}}" rel="stylesheet">
<style>
    .address-text{
    text-transform: uppercase;
    line-height: 1;
    margin-bottom: 10px;
    letter-spacing: 0.2px;
    font-size: 14px;
    font-weight: 600;
    }
</style>
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
                                        @if($userinfo && $userinfo->userphoto)
                                        <img class="br-5" alt="" src="{{asset('assets/uploads/userphoto/'.$userinfo->userphoto) }}" alt="user">

                                         @else
                                         <img class="br-5" alt="" src="{{asset('assets/img/user.jpg') }}">

                                        @endif
									</span>
								</div>
								<div class="my-md-auto mt-4 prof-details">
									<h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">{{$userinfo->first_name}}{{$userinfo->last_name}}</h4>
									
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-phone me-2"></i></span><span
											class="font-weight-semibold me-2">Phone:</span><span>{{$userinfo->phonenumber}}</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-envelope me-2"></i></span><span
											class="font-weight-semibold me-2">Email:</span><span>{{$userinfo->email}}</span>
									</p>
									
								</div>
							</div>
							<div class="card-footer py-0">
								<div class="profile-tab tab-menu-heading border-bottom-0">
									<nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
										
										<a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#edit">Personal Information</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab"
											href="#family">Family</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#gallery">ID card Details</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#friends">Address</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#settings">Bank Details
											</a>

											{{-- <a class="nav-link  mb-2 mt-2 btn btn-primary"  href="{{url('admin/editsebayat/'.$userinfo->userid)}}" style="margin-left: 267px;">Edit Profile
											</a> --}}
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
								
								<div class="main-content-body tab-pane active border-top-0" id="edit">
									<div class="card">
										<div class="card-body border-0">
											<div class="mb-4 main-content-label">Personal Information</div>
											@if(session('success'))
												<div class="alert alert-success">
														{{ session('success') }}
												</div>
												@endif
											
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">First Name</label>
														</div>
														<div class="col-md-9">
                                                            <input type="text" class="form-control"
															 value="{{$userinfo->first_name}}" name="first_name" readonly>
															
														</div>
													</div>
												</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Last Name</label>
														</div>
														<div class="col-md-9">
                                                            <input type="text" class="form-control"
															value="{{$userinfo->last_name}}" name="last_name" readonly>
															
														</div>
													</div>
												</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Email</label>
														</div>
                                                        <div class="col-md-9">
															<input type="text" class="form-control"
															name="email"  value="{{$userinfo->email}}" readonly>
														</div>
													</div>
												</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Phone Number</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control"
															name="phonenumber"	value="{{$userinfo->phonenumber}}" readonly>
														</div>
													</div>
												</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">DOB</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control"
                                                            value="{{$userinfo->dob}}" name="dob">
														</div>
													</div>
												</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Blood Group</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control"
                                                            value="{{$userinfo->bloodgrp}}" name="bloodgrp" readonly>
														</div>
													</div>
												</div>

												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Educational</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" name="qualification" value="{{$userinfo->qualification}}" readonly>
														</div>
													</div>
												</div>
												
												<div class="mb-4 main-content-label">About Temple Details</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Date of join in Temple</label>
														</div>
														<div class="col-md-9">
															<input type="date" name="datejoin" class="form-control" value="{{$userinfo->datejoin}}" readonly>
														</div>
													</div>
                                                </div>
												<div class="form-group ">

                                                    <div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Temple Id</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" name="templeid" value="{{$userinfo->templeid}}" readonly>
														</div>
													</div>
                                                </div>
												<div class="form-group ">
                                                
                                                    <div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Type of Seba</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" name="seba" value="{{$userinfo->seba}}" readonly>
														</div>
													</div>
                                                </div>
												<div class="form-group ">

                                                    <div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Bedha Seba</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" name="bedhaseba" value="{{$userinfo->bedhaseba}}" readonly>
														</div>
													</div>
                                                    
												</div>
											<div class="row">
											@if($userinfo->application_status == "approved")
											<div class="col-md-3">
												<div class="form-group">
														<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
														<button class="btn btn-success"> Approved</button>
												</div>
																			
											</div>
											@elseif($userinfo->application_status == "rejected")
											<div class="col-md-3">
												<div class="form-group">
														<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
														<button class="btn btn-primary"> Rejected</button>
												</div>
																			
											</div>
											
											@else
											<form class="form-horizontal" action="{{ url('/admin/approve/'.$userinfo->id) }}" method="post" enctype="multipart/form-data">
													@csrf
													@method('PUT')
												
													<div class="col-md-3">
														<div class="form-group">
																<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
																<input  type="submit" class="btn btn-primary" value="Approve">
														</div>
																					
													</div>
											</form>
											<form class="form-horizontal" action="{{ url('/admin/reject/'.$userinfo->id) }}" method="post" enctype="multipart/form-data">
												@csrf
												@method('PUT')

													<div class="col-md-3">
														<div class="form-group">
																<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
																<input type="submit" class="btn btn-primary" value="Reject">
														</div>
																					
													</div>
												
											</form>
											@endif
											</div>
										</div>
									</div>
								</div>
								<div class="main-content-body  tab-pane border-top-0" id="family">
									<div class="card">
										<div class="card-body border-0">
										    <div class="mb-4 main-content-label">About Family Details</div>
												<div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Father's Name</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" value="{{$userinfo->fathername}}" >
														</div>
													</div>
                                                </div>
                                                <div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Mother's Name</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" value="{{$userinfo->mothername}}" >
														</div>
													</div>
                                                </div>
                                                <div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Marital Status</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" value="{{$userinfo->marital}}" >
														</div>
													</div>
                                                </div>
                                                <div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Spouse Name</label>
														</div>
														<div class="col-md-9">
															<input type="text" class="form-control" value="{{$userinfo->spouse}}" >
														</div>
													</div>
                                                </div>

                                                <div class="form-group ">
													<div class="row row-sm">
														<div class="col-md-3">
															<label class="form-label">Children Name</label>
														</div>
														<div class="col-md-9">
                                                            @foreach($childinfos as $childinfo)
                                                                <li> {{$childinfo->childrenname}}</li>
																
                                                            @endforeach
                                                        </div>
													</div>
                                                </div>
                                            </div>
									    </div>
								    </div>
								<div class="main-content-body  border tab-pane border-top-0" id="gallery">
									<div class="card-body border">
                                        @foreach($iddetails as $iddetail)
										<div class="masonry row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="form-label">ID Proof Name:</label>
    
                                                    </div>
                                                    <div class="col-md-9">
                                                        <label class="form-label">{{$iddetail->idproof}}</label>
    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="form-label">{{$iddetail->idproof}} Number:</label>
    
                                                    </div>
                                                    <div class="col-md-7">
                                                        <label class="form-label">{{$iddetail->idnumber}}</label>
    
                                                    </div>
                                                </div>
                                            </div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset($iddetail->uploadoc)}}" class="js-img-viewer"
														data-caption="IMAGE-01" data-id="lion" download>
														<img src="{{ asset($iddetail->uploadoc) }}" alt="" />
													</a>
												</div>
											</div>
											
										</div>
                                        @endforeach
                                        
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="friends">
									<div class="card-body border pd-b-10">
										<!-- row -->
										<div class="row row-sm">
											<div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
												<div class="card custom-card border">
													<div class="card-body  user-lock ">
												        <div class="mb-4 main-content-label">Present Address</div>
														<h5 class="address-text">{{$address->preaddress ?? 'N/A'}}</h5>
                                                        <h5 class="address-text">PO : {{$address->prepost ?? 'N/A'}}, District : {{$address->predistrict ?? 'N/A'}} </h5>
                                                        <h5 class="address-text">State : {{$address->prestate ?? 'N/A' }}, Country : {{$address->precountry ?? 'N/A'}} </h5>
                                                        <h5 class="address-text">Pincode : {{$address->prepincode ?? 'N/A'}}</h5>
                                                        <h5 class="address-text">Landmark : {{$address->prelandmark ?? 'N/A'}}</h5>
														
													</div>
												</div>
											</div>

                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
												<div class="card custom-card border">
													<div class="card-body  user-lock ">
												        <div class="mb-4 main-content-label">Permanent Address</div>
														<h5 class="address-text">{{$address->peraddress ?? 'N/A'}}</h5>
                                                        <h5 class="address-text">PO : {{$address->perpost ?? 'N/A' }}, District : {{$address->perdistri ?? 'N/A'}} </h5>
                                                        <h5 class="address-text">State : {{$address->perstate ?? 'N/A'}}, Country : {{$address->percountry ?? 'N/A'}} </h5>
                                                        <h5 class="address-text">Pincode : {{$address->perpincode ?? 'N/A'}}</h5>
                                                        <h5 class="address-text">Landmark : {{$address->perlandmark ?? 'N/A'}}</h5>
														
														
													</div>
												</div>
											</div>
										
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane  border-0" id="settings">
									<div class="card">
										<div class="card-body  border pd-b-10" >
                                            <div class="row row-sm">
                                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                                    <div class="card custom-card border">
                                                        <div class="card-body  user-lock ">

                                                            <div class="mb-4 main-content-label">Bank Details</div>
                                                            <h5 class="address-text">Bank Name : {{$bankinfo->bankname ?? 'N/A' }}</h5>
                                                            <h5 class="address-text">Branch Name : {{$bankinfo->branchname ?? 'N/A'}}</h5>
                                                            <h5 class="address-text">IFSC : {{$bankinfo->ifsccode ?? 'N/A'}}</h5>
                                                            <h5 class="address-text">Account Holder Name : {{$bankinfo->accname ?? 'N/A'}}</h5>
                                                            <h5 class="address-text">Account Number : {{$bankinfo->accnumber ?? 'N/A'}}</h5>
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane  border-0" id="theme">
									<div class="card">
										<div class="card-body border-0" data-select2-id="12">

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
