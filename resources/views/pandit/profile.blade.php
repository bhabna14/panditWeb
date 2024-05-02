@extends('pandit.layouts.app')

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
							{{-- <div class="card-body d-md-flex">
								<div class="">
									<span class="profile-image pos-relative">
										<img class="br-5" alt="" src="{{asset('assets/img/faces/profile.jpg')}}">
										<span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
									</span>
								</div>
								<div class="my-md-auto mt-4 prof-details">
									<h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">Sonya Taylor</h4>
									<p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
										<span class="me-3"><i class="far fa-address-card me-2"></i>Ui/Ux
											Developer</span>
										<span class="me-3"><i class="fa fa-taxi me-2"></i>West fransisco,Alabama</span>
										<span><i class="far fa-flag me-2"></i>New Jersey</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-phone me-2"></i></span><span
											class="font-weight-semibold me-2">Phone:</span><span>+94 12345 6789</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-envelope me-2"></i></span><span
											class="font-weight-semibold me-2">Email:</span><span>spruko.space@gmail.com</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-globe me-2"></i></span><span
											class="font-weight-semibold me-2">Website</span><span>sprukotechnologies</span>
									</p>
								</div>
							</div> --}}
							<div class="card-footer py-0">
								<div class="profile-tab tab-menu-heading border-bottom-0">
									<nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
										<a class="nav-link  mb-2 mt-2 active" data-bs-toggle="tab"
											href="#profile">Profile</a>
										<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#career">Career</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#skill">Skills & Expertise</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#gallery">Add Details of Puja</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#bank">Bank Details</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#settings">Address Details
											</a>
                                            <a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#service">Areas of Service
											</a>
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
								<div class="main-content-body tab-pane  active" id="profile">
									<div class="card">
										<div class="card-body p-0 border-0 p-0 rounded-10">
											<div class="">
												<form action="" method="post" enctype="multipart/form-data">
													{{-- @csrf --}}
													{{-- @method('PUT') --}}
													<!-- row -->
													<div class="row">
														<div class="col-lg-12 col-md-">
															<div class="card custom-card">
																<div class="card-body">
																	<div class="main-content-label mg-b-5">
																			Profile Information
																	</div>
																	<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
																	<div class="row">
																	 <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="" placeholder="Enter First Name">
							
																	</div>
																	<div class="row">
																		<div class="col-md-4">
																		
																			<div class="form-group">
																				<label for="exampleInputEmail1">Name</label>
																				<input type="text" class="form-control" value="" id="exampleInputEmail1" name="first_name" placeholder="Enter First Name">
																			</div>
																			
																			<div class="form-group">
																				<label for="exampleInputEmail1">Email address</label>
																				<input type="email" class="form-control" value="" id="exampleInputEmail1" name="email" placeholder="Enter email">
																			</div>
																		
																		</div>
																		<div class="col-md-4">
																			
																			{{-- <div class="form-group">
																				<label for="exampleInputEmail1">Last Name</label>
																				<input type="text" class="form-control" value="" id="exampleInputEmail1" name="last_name" placeholder="Enter Last Name">
																			</div> --}}
																			
																			<div class="form-group">
																				<label for="exampleInputPassword1">Phone Number</label>
																				<input type="text" class="form-control" value="" id="exampleInputPassword1" name="phonenumber" placeholder="Phone Number">
																			</div>
																			<div class="form-group">
																				<label for="exampleInputPassword1">DOB</label>
																				<input type="date" class="form-control" value="" id="exampleInputPassword1" name="dob" placeholder="">
																			</div>
																		
																	
																		 </div>
																		<div class="col-md-4">
																			{{-- <div class="form-group">
																				<label for="exampleInputPassword1">DOB</label>
																				<input type="date" class="form-control" value="" id="exampleInputPassword1" name="dob" placeholder="">
																			</div> --}}
																			<div class="form-group">
																				<label for="exampleInputPassword1">Whatsapp Number</label>
																				<input type="text" class="form-control" value="" id="exampleInputPassword1" name="phonenumber" placeholder="Phone Number">
																			</div>
																		   
																			<div class="form-group">
																				<label for="exampleInputPassword1">Blood Group</label>
																				<input type="text" class="form-control" value="" id="exampleInputPassword1" name="bloodgrp" placeholder="Enter Blood Group">
																			</div>
																		</div>
							
																			<div class="col-md-4">
																				<div class="form-group">
																					<label for="exampleInputEmail1">Community</label>
																					<input type="text" class="form-control" value="" id="exampleInputEmail1" name="qualification" placeholder="Enter Community">
																				</div>
																				
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label for="exampleInputEmail1">Gotra</label>
																					<input type="text" class="form-control" value="" id="exampleInputEmail1" name="qualification" placeholder="Enter Gotra">
																				</div>
																				
																			</div>
																		
																			<div class="col-md-4">
																				<div class="form-group">
																					<label for="exampleInputEmail1">Marital Status</label>
																					<div class="row">
																					<div class="col-lg-4">
																						<label class="rdiobox"><input name="marital" type="radio"> <span>Married </span></label>
																					</div>
																					<div class="col-lg-6 ">
																						<label class="rdiobox"><input checked name="marital" type="radio"> <span>Unmarried </span></label>
																					</div>
																					</div>
																				</div>
																				
																			</div>
																			<div class="col-md-6">
																				<div class="form-group">
																					<label for="exampleInputPassword1">Languages Know</label>
																					<textarea name="" id="" class="form-control" rows="3"></textarea>
																					{{-- <input type="file" name="userphoto" class="form-control" id="exampleInputPassword1" > --}}
																				</div>
																				
																				
																			</div>
																			
																			<div class="col-md-6">
																				<div class="form-group">
																					<label for="exampleInputPassword1">Photo</label>
																					<input type="file" name="userphoto" class="form-control" id="exampleInputPassword1" >
																				</div>
																				
																				
																			</div>
																			
																		</div>
							
																		
																	</div>
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group mx-4">
																					<input type="submit" class="btn btn-primary" value="Submit">
																			</div>
																										
																		</div>
																	</div>
																
																</div>
														</div>
													   
														   
														
															
													
													</div>
													
							
												</form>
											</div>
											
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="career">
									<div class="card">
										<div class="card-body border-0">
											<div class="mb-4 main-content-label">Personal Information</div>
											<form class="form-horizontal">
												<div class="form-group">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="exampleInputEmail1">Highest Qualification</label>
																<input type="text" class="form-control" name="qualification" id="qualification" placeholder="Enter Heighest Qualification">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="exampleInputPassword1">Total Experience</label>
																<input type="text" class="form-control" name="experience" id="experience" placeholder="Total Experience">
															</div>
														</div>
													</div>
												</div>
											
												<div class="mb-4 main-content-label">Documentation</div>
												<div class="row">
													<div class="col-lg-12 col-md-12">
														<div class="card custom-card">
															<div class="card-body">
																<div id="show_doc_item">
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputEmail1">Select ID Proof</label>
																				<select name="idproof[]" class="form-control" id="">
																					<option value="adhar">Adhar Card</option>
																					<option value="voter">Voter Card</option>
																					<option value="pan">Pan Card</option>
																					<option value="DL">DL</option>
																					<option value="health card">Health Card</option>
																				</select>
																			</div>
																		</div>
											
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputPassword1">Upload Document</label>
																				<input type="file" class="form-control" name="uploadoc[]" id="exampleInputPassword1" placeholder="">
																			</div>
																		</div>
																	</div>
																</div>
											
																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<button type="button" class="btn btn-success add_item_btn" onclick="addIdSection()">Add More</button>
																		</div>
																	</div>
																</div>

															</div>
														</div>
													</div>
												</div>
											
												<div class="mb-4 main-content-label">Certification</div>
												<div class="row">
													<div class="col-lg-12 col-md-12">
														<div class="card custom-card">
															<div class="card-body">
																<div id="show_edu_item">
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputEmail1">Select Educational Qualification</label>
																				<select name="education[]" class="form-control" id="">
																					<option value="10th">10th</option>
																					<option value="+2">+2</option>
																					<option value="+3">+3</option>
																					<option value="Master Degree">Master Degree</option>
																				</select>
																			</div>
																		</div>
											
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputPassword1">Upload Document</label>
																				<input type="file" class="form-control" name="uploadEducation[]" id="exampleInputPassword1" placeholder="">
																			</div>
																		</div>
																	</div>
																</div>
											
																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<button type="button" class="btn btn-success add_item_btn" onclick="addEduSection()">Add More</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="text-center">
													<button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
												</div>	
											</form>
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="skill">
									<div class="row mb-5">
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox1">
															<img class="rounded-pill" src="{{asset('assets/img/jagannath.jpeg')}}" alt="Shree Jagannath">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Jagannath
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox1">
													</div>
												</div>
											</div>
										</div>
										<!-- Repeat the structure for other images -->
										<!-- Shree Ram -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox2">
															<img class="rounded-pill" src="{{asset('assets/img/rams.jpeg')}}" alt="Shree Ram">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Ram
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox2">
													</div>
												</div>
											</div>
										</div>
										<!-- Hanuman -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox3">
															<img class="rounded-pill" src="{{asset('assets/img/hanuman1.jpeg')}}" alt="Hanuman" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Hanuman
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox3">
													</div>
												</div>
											</div>
										</div>
										<!-- Shree Krishna -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox4">
															<img class="rounded-pill" src="{{asset('assets/img/krishna1.jpeg')}}" alt="Shree Krishna" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Krishna
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox4">
													</div>
												</div>
											</div>
										</div>
										<!-- Lord Shiv -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox5">
															<img class="rounded-pill" src="{{asset('assets/img/shiva.jpeg')}}" alt="Lord Shiv">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Lord Shiv
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox5">
													</div>
												</div>
											</div>
										</div>
										<!-- Maa Mangala -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox6">
															<img class="rounded-pill" src="{{asset('assets/img/durga.jpeg')}}" alt="Maa Mangala" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Maa Durga
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox6">
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox6">
															<img class="rounded-pill" src="{{asset('assets/img/saraswati.jpeg')}}" alt="Maa Mangala" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
												Maa Saraswati
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox6">
													</div>
												</div>
											</div>
										</div>
										<!-- Shree Ganesh -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox7">
															<img class="rounded-pill" src="{{asset('assets/img/ganeshs.jpeg')}}" alt="Shree Ganesh" >
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Ganesh
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox7">
													</div>
												</div>
											</div>
										</div>
										<div class="text-center col-md-12">
											<button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
										</div>
									</div>
								</div>
								

								<div class="main-content-body  border tab-pane border-top-0" id="gallery">
									<div class="card-body border">
										<div class="masonry row">
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/1.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-01" data-id="lion">
														<img src="{{asset('assets/img/photos/1.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/2.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-02" data-id="camel">
														<img src="{{asset('assets/img/photos/2.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/3.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-03" data-id="hippo">
														<img src="{{asset('assets/img/photos/3.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/4.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-04" data-id="koala">
														<img src="{{asset('assets/img/photos/4.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/5.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-05" data-id=" bear">
														<img src="{{asset('assets/img/photos/5.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/6.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-06" data-id=" rhino">
														<img src="{{asset('assets/img/photos/6.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/7.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-07" data-id=" rhino">
														<img src="{{asset('assets/img/photos/7.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/8.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-08" data-id=" rhino">
														<img src=" {{asset('assets/img/photos/8.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/9.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-09" data-id=" rhino">
														<img src="{{asset('assets/img/photos/9.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/10.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-10" data-id=" rhino">
														<img src="{{asset('assets/img/photos/10.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/11.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-11" data-id=" rhino">
														<img src="{{asset('assets/img/photos/11.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/12.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-11" data-id=" rhino">
														<img src="{{asset('assets/img/photos/12.jpg')}}" alt="" />
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="bank">
									<div class="card-body">
										<!-- row -->
										<form action="" method="post" enctype="multipart/form-data">
											{{-- @csrf --}}
											{{-- @method('PUT') --}}
											 <div class="row">
												<div class="col-lg-12 col-md-12">
													<div class="card custom-card">
														<div class="card-body">
															<div class="main-content-label mg-b-5">
																	Bank Information
															</div>
															<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
															<div class="row">
																<input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="" placeholder="Enter First Name">
				
															   <div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputEmail1">Bank Name</label>
																		<input type="text" class="form-control" name="bankname" value="" id="exampleInputEmail1" placeholder="Enter Bank Name">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">Branch Name</label>
																		<input type="text" class="form-control" name="branchname" value="" id="exampleInputPassword1" placeholder="Enter Branch Name">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">IFSC Code</label>
																		<input type="text" class="form-control" name="ifsccode" value="" id="exampleInputPassword1" placeholder="Enter IFSC Code">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputEmail1">Account Holder Name</label>
																		<input type="text" class="form-control" name="accname" value="" id="exampleInputEmail1" placeholder="Enter Account Holder Name">
																	</div>
																</div>
															
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">Account Number</label>
																		<input type="text" class="form-control" name="accnumber" value="" id="exampleInputPassword1" placeholder="Enter Account Number">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">UPI Number/ID</label>
																		<input type="text" class="form-control" name="accnumber" value="" id="exampleInputPassword1" placeholder="Enter Account Number">
																	</div>
																</div>
				
																
															</div>
															<div class="row">
																<div class="col-md-6 mt-3">
																	<div class="form-group">
																			<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
																			<input type="submit" class="btn btn-primary" value="Submit">
																	</div>
																								
																</div>
															</div>
														</div>
													</div>
												</div>
											
												
											</div>
										</form>
									</div>
								</div>
								<div class="main-content-body tab-pane  border-0" id="settings">
									<div class="card">
										<div class="card-body border-0" data-select2-id="12">
											<form action="" method="post" enctype="multipart/form-data">
												{{-- @csrf --}}
												{{-- @method('PUT') --}}
												 <div class="row">
													<div class="col-lg-12 col-md-12">
														<div class="card custom-card">
															<div class="card-body">
																<div class="main-content-label mg-b-5">
																		Address Information
																</div>
																<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
																<div class="row">
																	<input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="" placeholder="Enter First Name">
					
																	<div class="col-md-6">
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="preaddress">Present Address</label>
																				<input type="text" class="form-control" name="preaddress" value="" id="preaddress" placeholder="Enter Address">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="prepost">Post</label>
																				<input type="text" class="form-control" name="prepost" value="" id="prepost" placeholder="Enter Post">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="predistrict">District</label>
																				<input type="text" class="form-control" name="predistrict" value="" id="predistrict" placeholder="Enter District">
																			</div>
																		</div>
																		</div>
					
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="prestate">State</label>
																				<input type="text" class="form-control" name="prestate" value="Odisha" id="prestate" placeholder="Enter State">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="precountry">Country</label>
																				<input type="text" class="form-control" name="precountry" value="India" id="precountry" placeholder="Enter Country">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="prepincode">Pincode</label>
																				<input type="text" class="form-control" name="prepincode" value="" id="prepincode" placeholder="Enter Pincode">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="prelandmark">Landmark</label>
																				<input type="text" class="form-control" name="prelandmark" value="" id="prelandmark" placeholder="Enter Landmark">
																			</div>
																		</div>
																		</div>
																		<label class="ckbox"><input type="checkbox" id="same" onchange="addressFunction()"> <span class="mg-b-10">Same as Present Address</span></label>
																	</div>
																	<div class="col-md-6">
																	
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="peraddress">Permanent Address</label>
																				<input type="text" class="form-control" name="peraddress" value="" id="peraddress" placeholder="Enter Address">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="perpost">Post</label>
																				<input type="text" class="form-control" name="perpost" value="" id="perpost" placeholder="Enter Post">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="perdistri">District</label>
																				<input type="text" class="form-control" name="perdistri" value="" id="perdistri" placeholder="Enter District">
																			</div>
																		</div>
																		</div>
					
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="perstate">State</label>
																				<input type="text" class="form-control" name="perstate" value="" id="perstate" placeholder="Enter State">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="percountry">Country</label>
																				<input type="text" class="form-control" name="percountry" value="" id="percountry" placeholder="Enter Country">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="perpincode">Pincode</label>
																				<input type="text" class="form-control" name="perpincode" value="" id="perpincode" placeholder="Enter Pincode">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="perlandmark">Landmark</label>
																				<input type="text" class="form-control" name="perlandmark" value="" id="perlandmark" placeholder="Enter Landmark">
																			</div>
																		</div>
																		</div>
																	</div>
																</div>
																<div class="row">
																	<div class="col-md-6 mt-3">
																		<div class="form-group">
																				<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
																				<input type="submit" class="btn btn-primary" value="Submit">
																		</div>
																									
																	</div>
																</div>
															
															
															</div>
														</div>
													</div>
												</div>
											</form>
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
		
<script> 
    function addressFunction() { 
        if (document.getElementById( "same").checked) { 
            document.getElementById( "peraddress").value = document.getElementById( "preaddress").value; 
            document.getElementById("perpost").value = document.getElementById( "prepost").value; 
            document.getElementById( "perdistri").value = document.getElementById( "predistrict").value; 
            document.getElementById("perstate").value = document.getElementById( "prestate").value; 
            document.getElementById( "percountry").value = document.getElementById( "precountry").value; 
            document.getElementById("perpincode").value = document.getElementById( "prepincode").value;
            document.getElementById("perlandmark").value = document.getElementById( "prelandmark").value; 

        } else { 
            document.getElementById( "peraddress").value = ""; 
            document.getElementById("perpost").value = ""; 
            document.getElementById( "perdistri").value = ""; 
            document.getElementById("perstate").value = ""; 
            document.getElementById( "percountry").value = ""; 
            document.getElementById("perpincode").value = "";
            document.getElementById("perlandmark").value = ""; 
        } 
    } 
</script>
        <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
        <script src="{{asset('assets/js/select2.js')}}"></script>
		<script src="{{asset('assets/js/pandit-profile.js')}}"></script>


        <!-- smart photo master js -->
        <script src="{{asset('assets/plugins/SmartPhoto-master/smartphoto.js')}}"></script>
        <script src="{{asset('assets/js/gallery.js')}}"></script>

    @endsection
	@extends('pandit.layouts.app')

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
							{{-- <div class="card-body d-md-flex">
								<div class="">
									<span class="profile-image pos-relative">
										<img class="br-5" alt="" src="{{asset('assets/img/faces/profile.jpg')}}">
										<span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
									</span>
								</div>
								<div class="my-md-auto mt-4 prof-details">
									<h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">Sonya Taylor</h4>
									<p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
										<span class="me-3"><i class="far fa-address-card me-2"></i>Ui/Ux
											Developer</span>
										<span class="me-3"><i class="fa fa-taxi me-2"></i>West fransisco,Alabama</span>
										<span><i class="far fa-flag me-2"></i>New Jersey</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-phone me-2"></i></span><span
											class="font-weight-semibold me-2">Phone:</span><span>+94 12345 6789</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-envelope me-2"></i></span><span
											class="font-weight-semibold me-2">Email:</span><span>spruko.space@gmail.com</span>
									</p>
									<p class="text-muted ms-md-4 ms-0 mb-2"><span><i
												class="fa fa-globe me-2"></i></span><span
											class="font-weight-semibold me-2">Website</span><span>sprukotechnologies</span>
									</p>
								</div>
							</div> --}}
							<div class="card-footer py-0">
								<div class="profile-tab tab-menu-heading border-bottom-0">
									<nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
										<a class="nav-link  mb-2 mt-2 active" data-bs-toggle="tab"
											href="#profile">Profile</a>
										<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#career">Career</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#skill">Skills & Expertise</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#gallery">Add Details of Puja</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#bank">Bank Details</a>
										<a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#settings">Address Details
											</a>
                                            <a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#service">Areas of Service
											</a>
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
								<div class="main-content-body tab-pane  active" id="profile">
									<div class="card">
										<div class="card-body p-0 border-0 p-0 rounded-10">
											<div class="">
												<form action="" method="post" enctype="multipart/form-data">
													{{-- @csrf --}}
													{{-- @method('PUT') --}}
													<!-- row -->
													<div class="row">
														<div class="col-lg-12 col-md-">
															<div class="card custom-card">
																<div class="card-body">
																	<div class="main-content-label mg-b-5">
																			Profile Information
																	</div>
																	<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
																	<div class="row">
																	 <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="" placeholder="Enter First Name">
							
																	</div>
																	<div class="row">
																		<div class="col-md-4">
																		
																			<div class="form-group">
																				<label for="exampleInputEmail1">Name</label>
																				<input type="text" class="form-control" value="" id="exampleInputEmail1" name="first_name" placeholder="Enter First Name">
																			</div>
																			
																			<div class="form-group">
																				<label for="exampleInputEmail1">Email address</label>
																				<input type="email" class="form-control" value="" id="exampleInputEmail1" name="email" placeholder="Enter email">
																			</div>
																		
																		</div>
																		<div class="col-md-4">
																			
																			{{-- <div class="form-group">
																				<label for="exampleInputEmail1">Last Name</label>
																				<input type="text" class="form-control" value="" id="exampleInputEmail1" name="last_name" placeholder="Enter Last Name">
																			</div> --}}
																			
																			<div class="form-group">
																				<label for="exampleInputPassword1">Phone Number</label>
																				<input type="text" class="form-control" value="" id="exampleInputPassword1" name="phonenumber" placeholder="Phone Number">
																			</div>
																			<div class="form-group">
																				<label for="exampleInputPassword1">DOB</label>
																				<input type="date" class="form-control" value="" id="exampleInputPassword1" name="dob" placeholder="">
																			</div>
																		
																	
																		 </div>
																		<div class="col-md-4">
																			{{-- <div class="form-group">
																				<label for="exampleInputPassword1">DOB</label>
																				<input type="date" class="form-control" value="" id="exampleInputPassword1" name="dob" placeholder="">
																			</div> --}}
																			<div class="form-group">
																				<label for="exampleInputPassword1">Whatsapp Number</label>
																				<input type="text" class="form-control" value="" id="exampleInputPassword1" name="phonenumber" placeholder="Phone Number">
																			</div>
																		   
																			<div class="form-group">
																				<label for="exampleInputPassword1">Blood Group</label>
																				<input type="text" class="form-control" value="" id="exampleInputPassword1" name="bloodgrp" placeholder="Enter Blood Group">
																			</div>
																		</div>
							
																			<div class="col-md-4">
																				<div class="form-group">
																					<label for="exampleInputEmail1">Community</label>
																					<input type="text" class="form-control" value="" id="exampleInputEmail1" name="qualification" placeholder="Enter Community">
																				</div>
																				
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label for="exampleInputEmail1">Gotra</label>
																					<input type="text" class="form-control" value="" id="exampleInputEmail1" name="qualification" placeholder="Enter Gotra">
																				</div>
																				
																			</div>
																		
																			<div class="col-md-4">
																				<div class="form-group">
																					<label for="exampleInputEmail1">Marital Status</label>
																					<div class="row">
																					<div class="col-lg-4">
																						<label class="rdiobox"><input name="marital" type="radio"> <span>Married </span></label>
																					</div>
																					<div class="col-lg-6 ">
																						<label class="rdiobox"><input checked name="marital" type="radio"> <span>Unmarried </span></label>
																					</div>
																					</div>
																				</div>
																				
																			</div>
																			<div class="col-md-6">
																				<div class="form-group">
																					<label for="exampleInputPassword1">Languages Know</label>
																					<textarea name="" id="" class="form-control" rows="3"></textarea>
																					{{-- <input type="file" name="userphoto" class="form-control" id="exampleInputPassword1" > --}}
																				</div>
																				
																				
																			</div>
																			
																			<div class="col-md-6">
																				<div class="form-group">
																					<label for="exampleInputPassword1">Photo</label>
																					<input type="file" name="userphoto" class="form-control" id="exampleInputPassword1" >
																				</div>
																				
																				
																			</div>
																			
																		</div>
							
																		
																	</div>
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group mx-4">
																					<input type="submit" class="btn btn-primary" value="Submit">
																			</div>
																										
																		</div>
																	</div>
																
																</div>
														</div>
													   
														   
														
															
													
													</div>
													
							
												</form>
											</div>
											
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="career">
									<div class="card">
										<div class="card-body border-0">
											<div class="mb-4 main-content-label">Personal Information</div>
											<form class="form-horizontal">
												<div class="form-group">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="exampleInputEmail1">Highest Qualification</label>
																<input type="text" class="form-control" name="qualification" id="qualification" placeholder="Enter Heighest Qualification">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="exampleInputPassword1">Total Experience</label>
																<input type="text" class="form-control" name="experience" id="experience" placeholder="Total Experience">
															</div>
														</div>
													</div>
												</div>
											
												<div class="mb-4 main-content-label">Documentation</div>
												<div class="row">
													<div class="col-lg-12 col-md-12">
														<div class="card custom-card">
															<div class="card-body">
																<div id="show_doc_item">
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputEmail1">Select ID Proof</label>
																				<select name="idproof[]" class="form-control" id="">
																					<option value="adhar">Adhar Card</option>
																					<option value="voter">Voter Card</option>
																					<option value="pan">Pan Card</option>
																					<option value="DL">DL</option>
																					<option value="health card">Health Card</option>
																				</select>
																			</div>
																		</div>
											
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputPassword1">Upload Document</label>
																				<input type="file" class="form-control" name="uploadoc[]" id="exampleInputPassword1" placeholder="">
																			</div>
																		</div>
																	</div>
																</div>
											
																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<button type="button" class="btn btn-success add_item_btn" onclick="addIdSection()">Add More</button>
																		</div>
																	</div>
																</div>

															</div>
														</div>
													</div>
												</div>
											
												<div class="mb-4 main-content-label">Certification</div>
												<div class="row">
													<div class="col-lg-12 col-md-12">
														<div class="card custom-card">
															<div class="card-body">
																<div id="show_edu_item">
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputEmail1">Select Educational Qualification</label>
																				<select name="education[]" class="form-control" id="">
																					<option value="10th">10th</option>
																					<option value="+2">+2</option>
																					<option value="+3">+3</option>
																					<option value="Master Degree">Master Degree</option>
																				</select>
																			</div>
																		</div>
											
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="exampleInputPassword1">Upload Document</label>
																				<input type="file" class="form-control" name="uploadEducation[]" id="exampleInputPassword1" placeholder="">
																			</div>
																		</div>
																	</div>
																</div>
											
																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<button type="button" class="btn btn-success add_item_btn" onclick="addEduSection()">Add More</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="text-center">
													<button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
												</div>	
											</form>
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="skill">
									<div class="row mb-5">
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox1">
															<img class="rounded-pill" src="{{asset('assets/img/jagannath.jpeg')}}" alt="Shree Jagannath">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Jagannath
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox1">
													</div>
												</div>
											</div>
										</div>
										<!-- Repeat the structure for other images -->
										<!-- Shree Ram -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox2">
															<img class="rounded-pill" src="{{asset('assets/img/rams.jpeg')}}" alt="Shree Ram">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Ram
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox2">
													</div>
												</div>
											</div>
										</div>
										<!-- Hanuman -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox3">
															<img class="rounded-pill" src="{{asset('assets/img/hanuman1.jpeg')}}" alt="Hanuman" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Hanuman
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox3">
													</div>
												</div>
											</div>
										</div>
										<!-- Shree Krishna -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox4">
															<img class="rounded-pill" src="{{asset('assets/img/krishna1.jpeg')}}" alt="Shree Krishna" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Krishna
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox4">
													</div>
												</div>
											</div>
										</div>
										<!-- Lord Shiv -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox5">
															<img class="rounded-pill" src="{{asset('assets/img/shiva.jpeg')}}" alt="Lord Shiv">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Lord Shiv
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox5">
													</div>
												</div>
											</div>
										</div>
										<!-- Maa Mangala -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox6">
															<img class="rounded-pill" src="{{asset('assets/img/durga.jpeg')}}" alt="Maa Mangala" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Maa Durga
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox6">
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox6">
															<img class="rounded-pill" src="{{asset('assets/img/saraswati.jpeg')}}" alt="Maa Mangala" style="height: 100px;width: 100px;">
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
												Maa Saraswati
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox6">
													</div>
												</div>
											</div>
										</div>
										<!-- Shree Ganesh -->
										<div class="col-lg-3 col-md-6 col-sm-12">
											<div class="card p-3">
												<div class="card-body">
													<div class="mb-3 text-center about-team">
														<!-- Wrap the image inside a label -->
														<label for="checkbox7">
															<img class="rounded-pill" src="{{asset('assets/img/ganeshs.jpeg')}}" alt="Shree Ganesh" >
														</label>
													</div>
													<div class="tx-16 text-center font-weight-semibold">
														Shree Ganesh
													</div>
													<div class="form-check mt-3 text-center">
														<input class="form-check-input" type="checkbox" id="checkbox7">
													</div>
												</div>
											</div>
										</div>
										<div class="text-center col-md-12">
											<button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
										</div>
									</div>
								</div>
								

								<div class="main-content-body  border tab-pane border-top-0" id="gallery">
									<div class="card-body border">
										<div class="masonry row">
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/1.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-01" data-id="lion">
														<img src="{{asset('assets/img/photos/1.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/2.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-02" data-id="camel">
														<img src="{{asset('assets/img/photos/2.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/3.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-03" data-id="hippo">
														<img src="{{asset('assets/img/photos/3.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/4.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-04" data-id="koala">
														<img src="{{asset('assets/img/photos/4.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/5.jpg')}}" class="js-img-viewer"
														data-caption="IMAGE-05" data-id=" bear">
														<img src="{{asset('assets/img/photos/5.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/6.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-06" data-id=" rhino">
														<img src="{{asset('assets/img/photos/6.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/7.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-07" data-id=" rhino">
														<img src="{{asset('assets/img/photos/7.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/8.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-08" data-id=" rhino">
														<img src=" {{asset('assets/img/photos/8.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/9.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-09" data-id=" rhino">
														<img src="{{asset('assets/img/photos/9.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/10.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-10" data-id=" rhino">
														<img src="{{asset('assets/img/photos/10.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/11.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-11" data-id=" rhino">
														<img src="{{asset('assets/img/photos/11.jpg')}}" alt="" />
													</a>
												</div>
											</div>
											<div class="col-xl-3 col-lg-4 col-sm-6">
												<div class="brick">
													<a href="{{asset('assets/img/photos/12.jpg')}}" class=" js-img-viewer"
														data-caption="IMAGE-11" data-id=" rhino">
														<img src="{{asset('assets/img/photos/12.jpg')}}" alt="" />
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="main-content-body tab-pane border-top-0" id="bank">
									<div class="card-body">
										<!-- row -->
										<form action="" method="post" enctype="multipart/form-data">
											{{-- @csrf --}}
											{{-- @method('PUT') --}}
											 <div class="row">
												<div class="col-lg-12 col-md-12">
													<div class="card custom-card">
														<div class="card-body">
															<div class="main-content-label mg-b-5">
																	Bank Information
															</div>
															<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
															<div class="row">
																<input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="" placeholder="Enter First Name">
				
															   <div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputEmail1">Bank Name</label>
																		<input type="text" class="form-control" name="bankname" value="" id="exampleInputEmail1" placeholder="Enter Bank Name">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">Branch Name</label>
																		<input type="text" class="form-control" name="branchname" value="" id="exampleInputPassword1" placeholder="Enter Branch Name">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">IFSC Code</label>
																		<input type="text" class="form-control" name="ifsccode" value="" id="exampleInputPassword1" placeholder="Enter IFSC Code">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputEmail1">Account Holder Name</label>
																		<input type="text" class="form-control" name="accname" value="" id="exampleInputEmail1" placeholder="Enter Account Holder Name">
																	</div>
																</div>
															
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">Account Number</label>
																		<input type="text" class="form-control" name="accnumber" value="" id="exampleInputPassword1" placeholder="Enter Account Number">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label for="exampleInputPassword1">UPI Number/ID</label>
																		<input type="text" class="form-control" name="accnumber" value="" id="exampleInputPassword1" placeholder="Enter Account Number">
																	</div>
																</div>
				
																
															</div>
															<div class="row">
																<div class="col-md-6 mt-3">
																	<div class="form-group">
																			<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
																			<input type="submit" class="btn btn-primary" value="Submit">
																	</div>
																								
																</div>
															</div>
														</div>
													</div>
												</div>
											
												
											</div>
										</form>
									</div>
								</div>
								<div class="main-content-body tab-pane  border-0" id="settings">
									<div class="card">
										<div class="card-body border-0" data-select2-id="12">
											<form action="" method="post" enctype="multipart/form-data">
												{{-- @csrf --}}
												{{-- @method('PUT') --}}
												 <div class="row">
													<div class="col-lg-12 col-md-12">
														<div class="card custom-card">
															<div class="card-body">
																<div class="main-content-label mg-b-5">
																		Address Information
																</div>
																<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
																<div class="row">
																	<input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="" placeholder="Enter First Name">
					
																	<div class="col-md-6">
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="preaddress">Present Address</label>
																				<input type="text" class="form-control" name="preaddress" value="" id="preaddress" placeholder="Enter Address">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="prepost">Post</label>
																				<input type="text" class="form-control" name="prepost" value="" id="prepost" placeholder="Enter Post">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="predistrict">District</label>
																				<input type="text" class="form-control" name="predistrict" value="" id="predistrict" placeholder="Enter District">
																			</div>
																		</div>
																		</div>
					
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="prestate">State</label>
																				<input type="text" class="form-control" name="prestate" value="Odisha" id="prestate" placeholder="Enter State">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="precountry">Country</label>
																				<input type="text" class="form-control" name="precountry" value="India" id="precountry" placeholder="Enter Country">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="prepincode">Pincode</label>
																				<input type="text" class="form-control" name="prepincode" value="" id="prepincode" placeholder="Enter Pincode">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="prelandmark">Landmark</label>
																				<input type="text" class="form-control" name="prelandmark" value="" id="prelandmark" placeholder="Enter Landmark">
																			</div>
																		</div>
																		</div>
																		<label class="ckbox"><input type="checkbox" id="same" onchange="addressFunction()"> <span class="mg-b-10">Same as Present Address</span></label>
																	</div>
																	<div class="col-md-6">
																	
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="peraddress">Permanent Address</label>
																				<input type="text" class="form-control" name="peraddress" value="" id="peraddress" placeholder="Enter Address">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="perpost">Post</label>
																				<input type="text" class="form-control" name="perpost" value="" id="perpost" placeholder="Enter Post">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="perdistri">District</label>
																				<input type="text" class="form-control" name="perdistri" value="" id="perdistri" placeholder="Enter District">
																			</div>
																		</div>
																		</div>
					
																		<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="perstate">State</label>
																				<input type="text" class="form-control" name="perstate" value="" id="perstate" placeholder="Enter State">
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<label for="percountry">Country</label>
																				<input type="text" class="form-control" name="percountry" value="" id="percountry" placeholder="Enter Country">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="perpincode">Pincode</label>
																				<input type="text" class="form-control" name="perpincode" value="" id="perpincode" placeholder="Enter Pincode">
																			</div>
																		</div>
																		</div>
																		<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label for="perlandmark">Landmark</label>
																				<input type="text" class="form-control" name="perlandmark" value="" id="perlandmark" placeholder="Enter Landmark">
																			</div>
																		</div>
																		</div>
																	</div>
																</div>
																<div class="row">
																	<div class="col-md-6 mt-3">
																		<div class="form-group">
																				<!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
																				<input type="submit" class="btn btn-primary" value="Submit">
																		</div>
																									
																	</div>
																</div>
															
															
															</div>
														</div>
													</div>
												</div>
											</form>
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
		
<script> 
    function addressFunction() { 
        if (document.getElementById( "same").checked) { 
            document.getElementById( "peraddress").value = document.getElementById( "preaddress").value; 
            document.getElementById("perpost").value = document.getElementById( "prepost").value; 
            document.getElementById( "perdistri").value = document.getElementById( "predistrict").value; 
            document.getElementById("perstate").value = document.getElementById( "prestate").value; 
            document.getElementById( "percountry").value = document.getElementById( "precountry").value; 
            document.getElementById("perpincode").value = document.getElementById( "prepincode").value;
            document.getElementById("perlandmark").value = document.getElementById( "prelandmark").value; 

        } else { 
            document.getElementById( "peraddress").value = ""; 
            document.getElementById("perpost").value = ""; 
            document.getElementById( "perdistri").value = ""; 
            document.getElementById("perstate").value = ""; 
            document.getElementById( "percountry").value = ""; 
            document.getElementById("perpincode").value = "";
            document.getElementById("perlandmark").value = ""; 
        } 
    } 
</script>
        <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
        <script src="{{asset('assets/js/select2.js')}}"></script>
		<script src="{{asset('assets/js/pandit-profile.js')}}"></script>


        <!-- smart photo master js -->
        <script src="{{asset('assets/plugins/SmartPhoto-master/smartphoto.js')}}"></script>
        <script src="{{asset('assets/js/gallery.js')}}"></script>

    @endsection
