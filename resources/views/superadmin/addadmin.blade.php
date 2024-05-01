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
                            <a href="{{url('superadmin/adminlist')}}" class="btn btn-primary btn-style">Admin List</a>

								<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">MANAGE ADMIN</li>
							</ol>
						</div>
					</div>
					<!-- /breadcrumb -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if(session()->has('success'))
                    <div class="alert alert-success"  id="Message">
                        {{ session()->get('success') }}
                    </div>
                    @endif
                    @if ($errors->has('danger'))
                    <div class="alert alert-danger" id="Message">
                        {{ $errors->first('danger') }}
                    </div>
                    @endif
                    <form action="{{ url('superadmin/saveadmin') }}" method="post" enctype="multipart/form-data">
                        @csrf
					    <!-- row -->
					    <div class="row">
						    <div class="col-lg-12 col-md-">
								<div class="card custom-card">
									<div class="card-body">
										
                                        <div class="row">
                                         <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="JA{{ rand(1000, 9999) }}">

                                        </div>
										<div class="row">
                                            <div class="col-md-4">
                                            
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Name</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Name">
                                                </div>
                                                
                                               
                                            
                                            </div>
                                            <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email</label>
                                                    <input type="email" class="form-control" id="exampleInputEmail1" value="" name="email" placeholder="Enter Email">
                                                </div>
                                                
                                        
                                             </div>
                                             <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Phone Number</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" value="+91" name="phonenumber" placeholder="Enter Phone Number">
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
<script>

        $(document).ready(function() {
            $("#addInput").click(function() {
                $("#show_item").append(` <div class="row input-wrapper">
                                                    <div class="col-md-6" >
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Children name</label>
                                                            <input type="text" class="form-control" name="childrenname[]" id="exampleInputPassword1" placeholder="Enter Children name">
                                                        </div>
                                                        
                                                    </div>
                                                

                                                    <div class="col-md-6">
                                                        <div class="form-group mt-4">
                                                            <button class="btn btn-danger removeInput" id="addInput">Remove</button>
                                                        </div>
                                                        
                                                    </div>
                                                </div>`);
            });

            $(document).on('click', '.removeInput', function() {
                $(this).closest('.input-wrapper').remove(); // Use closest() to find the closest parent div with class input-wrapper and remove it
            });
        });


        $(document).ready(function() {
            $("#adddoc").click(function() {
                $("#show_doc_item").append(` <div class="row input-wrapper_doc">
                                                    <div class="col-md-4">
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
                                                
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Number</label>
                                                            <input type="text" name="idnumber[]" class="form-control" id="exampleInputPassword1" placeholder="Enter Number">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Upload Document</label>
                                                            <input type="file" name="uploadoc[]" class="form-control" id="exampleInputPassword1" placeholder="">
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-6">
                                                            <div class="form-group">
                                                                <button class="btn btn-danger remove_doc" >Remove</button>
                                                            </div>
                                                            
                                                    </div>
                                                </div>`);
            });

            $(document).on('click', '.remove_doc', function() {
                $(this).closest('.input-wrapper_doc').remove(); // Use closest() to find the closest parent div with class input-wrapper and remove it
            });
        });
</script>


    @endsection
