@extends('superadmin.layouts.app')

    @section('styles')

		<!--- Internal Select2 css-->
		<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

    @endsection

    @section('content')

					<!-- breadcrumb -->
					<div class="breadcrumb-header justify-content-between">
						<div class="left-content">
						  <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE ADMIN</span>
						</div>
						<div class="justify-content-center mt-2">
							<ol class="breadcrumb">
                            <a href="{{url('superadmin/adminlist')}}" class="btn btn-primary">Admin List</a>

								<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">MANAGE ADMIN</li>
							</ol>
						</div>
					</div>
					<!-- /breadcrumb -->
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                    @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                    @endif
                    <form action="{{ url('superadmin/update/'.$adminlists->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
					    <!-- row -->
					    <div class="row">
						    <div class="col-lg-12 col-md-">
								<div class="card custom-card">
									<div class="card-body">
										
                                        <div class="row">

                                        </div>
										<div class="row">
                                            <div class="col-md-4">
                                            
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Name</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" value="{{$adminlists->name}}" name="name" placeholder="Enter Name">
                                                </div>
                                                
                                                
                                            
                                            </div>
                                            <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email</label>
                                                    <input type="email" class="form-control" id="exampleInputEmail1" value="{{$adminlists->email}}" name="email" placeholder="Enter Email">
                                                </div>
                                               
                                        
                                             </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Phone Number</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" value="{{$adminlists->phonenumber}}" name="phonenumber" placeholder="Enter Phone Number">
                                                </div>
                                               
                                                
                                            </div>

                                                
                                            </div>

                                            
                                        </div>
                                      
									
									</div>
								</div>
						</div>
                        <!-- /row -->



                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                        <!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                </div>
                                                            
                            </div>
                        </div>

                    </form>

                    

                         
							
					</div>
					

                    
					

                    @endsection

                    @section('modal')
                  

                    @endsection

    @section('scripts')

		<!-- Form-layouts js -->
		<script src="{{asset('assets/js/form-layouts.js')}}"></script>

		<!--Internal  Select2 js -->
		<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>

        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>



    @endsection
