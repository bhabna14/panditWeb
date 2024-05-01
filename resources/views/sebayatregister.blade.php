@extends('layouts.app')

    @section('styles')

		<!--- Internal Select2 css-->
		<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

    @endsection

    @section('content')

					<!-- breadcrumb -->
					<div class="breadcrumb-header justify-content-between">
						<div class="left-content">
						  <span class="main-content-title mg-b-0 mg-b-lg-1">SEBAYAT REGISTRATION</span>
						</div>
						<div class="justify-content-center mt-2">
							<ol class="breadcrumb">
								<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">SEBAYAT REGISTRATION</li>
							</ol>
						</div>
					</div>
					<!-- /breadcrumb -->
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @if(session()->has('success'))
                    <div class="alert alert-success" id="Message">
                        {{ session()->get('success') }}
                    </div>
                    @endif
                    <form action="{{ route('saveregister') }}" method="post" enctype="multipart/form-data">
                        @csrf
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
                                         <input type="hidden" class="form-control" id="" name="userid" value="JA{{ rand(1000, 9999) }}" placeholder="Enter First Name">

                                        </div>
										<div class="row">
                                            <div class="col-md-4">
                                            
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">First Name</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" name="first_name" placeholder="Enter First Name">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email address</label>
                                                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" placeholder="Enter email">
                                                </div>
                                            
                                            </div>
                                            <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Last Name</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" name="last_name" placeholder="Enter Last Name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Phone Number</label>
                                                    <input type="text" class="form-control" id="exampleInputPassword1" name="phonenumber" value="+91" placeholder="Phone Number">
                                                </div>
                                            
                                        
                                             </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">DOB</label>
                                                    <input type="date" class="form-control" id="exampleInputPassword1" name="dob" placeholder="">
                                                </div>
                                               
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Blood Group</label>
                                                    <input type="text" class="form-control" id="exampleInputPassword1" name="bloodgrp" placeholder="Enter Blood Group">
                                                </div>
                                            </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Educational Qualification</label>
                                                        <input type="text" class="form-control" id="exampleInputEmail1" name="qualification" placeholder="Enter Educational Qualification">
                                                    </div>
                                                    
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Password</label>
                                                        <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Enter Password">
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
                                      
									
									</div>
								</div>
						</div>
                        <!-- /row -->

                         <!--family info row -->
                         <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Family Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Father's Name</label>
                                                        <input type="text" class="form-control" name="fathername" id="exampleInputEmail1" placeholder="Enter Father's Name">
                                                    </div>
                                                    
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Mother's Name</label>
                                                        <input type="text" class="form-control" name="mothername" id="exampleInputPassword1" placeholder="Enter Mother's Name">
                                                    </div>
                                                    
                                                </div>

                                                <div class="col-md-6">
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
                                                        <label for="exampleInputPassword1">Spouse Name</label>
                                                        <input type="text" class="form-control" name="spouse" id="exampleInputPassword1" placeholder="Enter Spouse Name">
                                                    </div>
                                                    
                                                </div>
                                               
                                            

                                                
                                            </div>
                                            <div id="show_item">
                                                <div class="row">
                                                    <div class="col-md-6" >
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Children name</label>
                                                            <input type="text" class="form-control" name="childrenname[]" id="exampleInputPassword1" placeholder="Enter Children name">
                                                        </div>
                                                        
                                                    </div>
                                                

                                                    <div class="col-md-6">
                                                        <div class="form-group  mt-4">
                                                            <button type="button" class="btn btn-primary add_item_btn" id="addInput">Add More</button>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        
                                        </div>
                                    </div>
                                </div>
                            
                                
                        </div>
                        <!-- /row -->

                         <!-- id card details row -->
                         <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Id Card Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div id="show_doc_item">
                                                <div class="row">
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
                                                            <input type="text"  class="form-control" name="idnumber[]" id="exampleInputPassword1" placeholder="Enter Number">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
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
                                                                <button type="button" class="btn btn-primary add_item_btn" id="adddoc">Add More</button>
                                                            </div>
                                                            
                                                    </div>
                                           </div>

                                            
                                        
                                        
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- /row -->

                        <!--address information row -->
                        <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Address Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="preaddress">Present Address</label>
                                                            <input type="text" class="form-control" name="preaddress" id="preaddress" placeholder="Enter Address">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="prepost">Post</label>
                                                            <input type="text" class="form-control" name="prepost" id="prepost" placeholder="Enter Post">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="predistrict">District</label>
                                                            <input type="text" class="form-control" name="predistrict" id="predistrict" placeholder="Enter District">
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
                                                            <input type="text" class="form-control" name="prepincode" id="prepincode" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="prelandmark">Landmark</label>
                                                            <input type="text" class="form-control" name="prelandmark" id="prelandmark" placeholder="Enter Landmark">
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
                                                            <input type="text" class="form-control" name="peraddress" id="peraddress" placeholder="Enter Address">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perpost">Post</label>
                                                            <input type="text" class="form-control" name="perpost" id="perpost" placeholder="Enter Post">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perdistri">District</label>
                                                            <input type="text" class="form-control" name="perdistri" id="perdistri" placeholder="Enter District">
                                                        </div>
                                                    </div>
                                                    </div>

                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perstate">State</label>
                                                            <input type="text" class="form-control" name="perstate" id="perstate" placeholder="Enter State">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="percountry">Country</label>
                                                            <input type="text" class="form-control" name="percountry"  id="percountry" placeholder="Enter Country">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="perpincode">Pincode</label>
                                                            <input type="text" class="form-control" name="perpincode" id="perpincode" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="perlandmark">Landmark</label>
                                                            <input type="text" class="form-control" name="perlandmark" id="perlandmark" placeholder="Enter Landmark">
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- /row -->

                        <!--Bank info row -->
                        <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Bank Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                               <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Bank Name</label>
                                                        <input type="text" class="form-control" name="bankname" id="exampleInputEmail1" placeholder="Enter Bank Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Branch Name</label>
                                                        <input type="text" class="form-control" name="branchname" id="exampleInputPassword1" placeholder="Enter Branch Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">IFSC Code</label>
                                                        <input type="text" class="form-control" name="ifsccode" id="exampleInputPassword1" placeholder="Enter IFSC Code">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Account Holder Name</label>
                                                        <input type="text" class="form-control" name="accname" id="exampleInputEmail1" placeholder="Enter Account Holder Name">
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Account Number</label>
                                                        <input type="text" class="form-control" name="accnumber" id="exampleInputPassword1" placeholder="Enter Account Number">
                                                    </div>
                                                </div>

                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                
                        </div>
                        <!-- /row -->
                        <!-- row -->
					    <div class="row">
						    <div class="col-lg-12 col-md-">
								<div class="card custom-card">
									<div class="card-body">
										<div class="main-content-label mg-b-5">
												Other Information
										</div>
										<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
										<div class="row">
                                            <div class="col-md-4">
                                            
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Date of join in temple seba</label>
                                                    <input type="date" class="form-control"  id="exampleInputEmail1" name="datejoin" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Bedha Seba</label>
                                                    <input type="tetx" class="form-control" id="exampleInputPassword1" name="bedhaseba" placeholder="Enter Bedha Seba">
                                                </div>
                                              
                                            
                                            </div>
                                            <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Type of Seba</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" name="seba" placeholder="Enter Type of Seba">
                                                </div>
                                                {{-- <div class="form-group">
                                                    <label for="exampleInputEmail1">Status</label>
                                                    <select name="status" class="form-control" id="">
                                                        <option value="active">Active</option>
                                                        <option value="deactive">Deactive</option>
                                                    </select>
                                                    <!-- <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Temple Id"> -->
                                                </div> --}}
                                                
                                            
                                        
                                             </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Temple Id</label>
                                                    <input type="text" name="templeid" class="form-control" id="exampleInputEmail1" placeholder="Enter Temple Id">
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
<script>
    setTimeout(function(){
        document.getElementById('Message').style.display = 'none';
    }, 3000);
</script>
    @endsection
