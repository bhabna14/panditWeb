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
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                    @endif
                    <form action="{{  route('updateUserInfo', $userinfo->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
                                         <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                        </div>
										<div class="row">
                                            <div class="col-md-4">
                                            
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">First Name</label>
                                                    <input type="text" class="form-control" value="{{ $userinfo->first_name ?? "" }}" id="exampleInputEmail1" name="first_name" placeholder="Enter First Name">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email address</label>
                                                    <input type="email" class="form-control" value="{{ $userinfo->email ?? "" }}" id="exampleInputEmail1" name="email" placeholder="Enter email">
                                                </div>
                                            
                                            </div>
                                            <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Last Name</label>
                                                    <input type="text" class="form-control" value="{{ $userinfo->last_name ?? "" }}" id="exampleInputEmail1" name="last_name" placeholder="Enter Last Name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Phone Number</label>
                                                    <input type="text" class="form-control" value="{{ $userinfo->phonenumber ?? "" }}" id="exampleInputPassword1" name="phonenumber" placeholder="Phone Number">
                                                </div>
                                            
                                        
                                             </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">DOB</label>
                                                    <input type="date" class="form-control" value="{{ $userinfo->dob ?? "" }}" id="exampleInputPassword1" name="dob" placeholder="">
                                                </div>
                                               
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Blood Group</label>
                                                    <input type="text" class="form-control" value="{{ $userinfo->bloodgrp ?? "" }}" id="exampleInputPassword1" name="bloodgrp" placeholder="Enter Blood Group">
                                                </div>
                                            </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Educational Qualification</label>
                                                        <input type="text" class="form-control" value="{{ $userinfo->qualification ?? "" }}" id="exampleInputEmail1" name="qualification" placeholder="Enter Educational Qualification">
                                                    </div>
                                                    
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    {{-- <div class="form-group">
                                                        <label for="exampleInputPassword1">Password</label>
                                                        <input type="password" class="form-control" id="exampleInputPassword1" name="passowrd" placeholder="Enter Password">
                                                    </div> --}}
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Photo</label>
                                                        <input type="file" name="userphoto" class="form-control" id="exampleInputPassword1" >
                                                    </div>
                                                    @if ( $userinfo->userphoto != "")
                                                    <div class="form-group">
                                                        <img class="br-5" alt="" src="{{asset('assets/uploads/userphoto/'.$userinfo->userphoto) }}" alt="user" style="width: 399px; height: 300px;">

                                                    </div>
                                                    @else
                                                    <div class="form-group">
                                                        
                                                    </div>
                                                    @endif
                                                    
                                                </div>
                                            </div>

                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mx-4">
                                                        <!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
                                                        <input type="submit" class="btn btn-primary" value="Submit">
                                                </div>
                                                                            
                                            </div>
                                        </div>
									
									</div>
							</div>
                           
                               
                            
                                
						
                        </div>
                        

                    </form>
                        <!-- /row -->

                         <!--family info row -->
                    <form action="{{  route('updateFamilyInfo', $userinfo->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                         <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Family Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                         <input type="text" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Father's Name</label>
                                                        <input type="text" class="form-control" value="{{ $userinfo->fathername ?? "" }}" name="fathername" id="exampleInputEmail1" placeholder="Enter Father's Name">
                                                    </div>
                                                    
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Mother's Name</label>
                                                        <input type="text" class="form-control" name="mothername" value="{{ $userinfo->mothername ?? "" }}" id="exampleInputPassword1" placeholder="Enter Mother's Name">
                                                    </div>
                                                    
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Marital Status</label>
                                                        <div class="row">
                                                        <div class="col-lg-4">
                                                            <label class="rdiobox"><input name="marital" {{$userinfo->marital == "married" ? "checked" : ""}} value="married" type="radio"> <span>Married </span></label>
                                                        </div>
                                                        <div class="col-lg-6 ">
                                                            <label class="rdiobox"><input {{$userinfo->marital == "unmarried" ? "checked" : ""}} name="marital" value="unmarried" type="radio"> <span>Unmarried </span></label>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Spouse Name</label>
                                                        <input type="text" class="form-control" name="spouse" value="{{ $userinfo->spouse ?? "" }}" id="exampleInputPassword1" placeholder="Enter Spouse Name">
                                                    </div>
                                                    
                                                </div>
                                               
                                            

                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                            <!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
                                                            <input type="submit" class="btn btn-primary" value="Submit">
                                                    </div>
                                                                                
                                                </div>
                                            </div>
                    </form>
                                            <form action="{{  url('admin/updatechildInfo') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                {{-- @method('PUT') --}}
                                                <div class="main-content-label mg-b-5 mt-4">
                                                    Children Information
                                                </div>
                                            <div id="show_item">
                                                <div class="row">
                                                    <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                                    <div class="col-md-4" >
                                                        <div class="form-group">
                                                            <label for="">Children name</label>
                                                            <input type="text" class="form-control" name="childrenname[]" id="" placeholder="Enter Children name">
                                                        </div>
                                                      
                                                        
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group">
                                                            <label for="">DOB</label>
                                                            <input type="date" class="form-control" name="dob[]" id="" placeholder="Enter DOB">
                                                        </div>
                                                      
                                                        
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group">
                                                            <label for="">Gender</label>
                                                            <select name="gender[]" class="form-control" id="">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>

                                                            </select>
                                                            {{-- <input type="text" class="form-control" name="childrenname[]" id="" placeholder="Enter Children name"> --}}
                                                        </div>
                                                      
                                                        
                                                    </div>
                                                

                                                    <div class="col-md-2">
                                                        <div class="form-group  mt-4">
                                                            <button type="button" class="btn btn-dark add_item_btn" id="addInput">Add More</button>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                            <!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
                                                            <input type="submit" class="btn btn-primary" value="Submit">
                                                    </div>
                                                                                
                                                </div>
                                            </div>
                                            </form>
                                            <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">

                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">#</th>
                                                        <th class="border-bottom-0">Name</th>
                                                        <th class="border-bottom-0">Dob</th>
                                                        <th class="border-bottom-0">Gender</th>
                                                        <th class="border-bottom-0">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($childinfos as $index => $childinfo)
                                                    @if($childinfo->childrenname != "")
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{$childinfo->childrenname}}</td>
                                                        <td>{{$childinfo->dob}}</td>
                                                        <td>{{$childinfo->gender}}</td>
                                                        <td>
                                                            <a href="{{url('admin/updatechildstatus/'.$childinfo->id)}}"><i class="fa fa-trash" aria-hidden="true"></i></a></td></td>
                                                    </tr>
                                                    
                                                    @else
                                                    nothing
                                                    @endif

                                                @endforeach
                                                </tbody>
                                              </table>

                                      
                                        
                                        
                                        
                                            </div>
                                    </div>
                                </div>
                            
                                
                        </div>
                      
                    
                        <!-- /row -->

                         <!-- id card details row -->
                    <form action="{{  route('updateIdInfo', $userinfo->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                         <div class="row">
                            <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

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
                                                                <button type="button" class="btn btn-dark add_item_btn" id="adddoc">Add More</button>
                                                            </div>
                                                            
                                                    </div>
                                           </div>
                                           <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                        <!-- <button class="btn btn-primary add_item_btn" id="adddoc">Add More</button> -->
                                                        <input type="submit" class="btn btn-primary" value="Submit">
                                                </div>
                                                                            
                                            </div>
                                            </div>

                                           <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">

                                            <thead>
                                                <tr>
                                                    <th class="border-bottom-0">#</th>
                                                    <th class="border-bottom-0">Name</th>
                                                    <th class="border-bottom-0">ID Number</th>
                                                    <th class="border-bottom-0">Doc Image</th>

                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($iddetails as $index => $iddetail)
                                                @if($iddetail->idproof != "")
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{$iddetail->idproof}}</td>
                                                    <td>{{$iddetail->idnumber}}</td>
                                                        <td>
														<img src="{{ asset($iddetail->uploadoc) }}" style="width:150px; height:100px" alt="" />

                                                        </td>

                                                    <td>
                                                        <a href="{{url('admin/updateIdstatus/'.$iddetail->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td>
                                                </tr>
                                                
                                                @else
                                                nothing
                                                @endif

                                            @endforeach
                                            </tbody>
                                          </table>

                                            
                                        
                                        
                                        </div>
                                    </div>
                                </div>
                        </div>
                       
                    </form>
                        <!-- /row -->

                        <!--address information row -->
                        @if($address)
                        <form action="{{  route('updateAddressInfo',$address->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                             <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Address Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                                <input type="text" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                                <div class="col-md-6">
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="preaddress">Present Address</label>
                                                            <input type="text" class="form-control" name="preaddress" value="{{ $address->preaddress ?? "" }}" id="preaddress" placeholder="Enter Address">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="prepost">Post</label>
                                                            <input type="text" class="form-control" name="prepost" value="{{ $address->prepost ?? "" }}" id="prepost" placeholder="Enter Post">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="predistrict">District</label>
                                                            <input type="text" class="form-control" name="predistrict" value="{{ $address->predistrict ?? "" }}" id="predistrict" placeholder="Enter District">
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
                                                            <input type="text" class="form-control" name="prepincode" value="{{ $address->prepincode ?? "" }}" id="prepincode" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="prelandmark">Landmark</label>
                                                            <input type="text" class="form-control" name="prelandmark" value="{{ $address->prelandmark ?? "" }}" id="prelandmark" placeholder="Enter Landmark">
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
                                                            <input type="text" class="form-control" name="peraddress" value="{{ $address->peraddress ?? "" }}" id="peraddress" placeholder="Enter Address">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perpost">Post</label>
                                                            <input type="text" class="form-control" name="perpost" value="{{ $address->perpost ?? "" }}" id="perpost" placeholder="Enter Post">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perdistri">District</label>
                                                            <input type="text" class="form-control" name="perdistri" value="{{ $address->perdistri ?? "" }}" id="perdistri" placeholder="Enter District">
                                                        </div>
                                                    </div>
                                                    </div>

                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perstate">State</label>
                                                            <input type="text" class="form-control" name="perstate" value="{{ $address->perstate ?? "" }}" id="perstate" placeholder="Enter State">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="percountry">Country</label>
                                                            <input type="text" class="form-control" name="percountry" value="{{ $address->percountry ?? "" }}" id="percountry" placeholder="Enter Country">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="perpincode">Pincode</label>
                                                            <input type="text" class="form-control" name="perpincode" value="{{ $address->perpincode ?? "" }}" id="perpincode" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="perlandmark">Landmark</label>
                                                            <input type="text" class="form-control" name="perlandmark" value="{{ $address->perlandmark ?? "" }}" id="perlandmark" placeholder="Enter Landmark">
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
                        @else
                        <form action="{{  url('admin/updatenewAddress') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                             <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Address Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                                <input type="text" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                                <div class="col-md-6">
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="preaddress">Present Address</label>
                                                            <input type="text" class="form-control" name="preaddress" value="{{ $address->preaddress ?? "" }}" id="preaddress" placeholder="Enter Address">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="prepost">Post</label>
                                                            <input type="text" class="form-control" name="prepost" value="{{ $address->prepost ?? "" }}" id="prepost" placeholder="Enter Post">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="predistrict">District</label>
                                                            <input type="text" class="form-control" name="predistrict" value="{{ $address->predistrict ?? "" }}" id="predistrict" placeholder="Enter District">
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
                                                            <input type="text" class="form-control" name="prepincode" value="{{ $address->prepincode ?? "" }}" id="prepincode" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="prelandmark">Landmark</label>
                                                            <input type="text" class="form-control" name="prelandmark" value="{{ $address->prelandmark ?? "" }}" id="prelandmark" placeholder="Enter Landmark">
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
                                                            <input type="text" class="form-control" name="peraddress" value="{{ $address->peraddress ?? "" }}" id="peraddress" placeholder="Enter Address">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perpost">Post</label>
                                                            <input type="text" class="form-control" name="perpost" value="{{ $address->perpost ?? "" }}" id="perpost" placeholder="Enter Post">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perdistri">District</label>
                                                            <input type="text" class="form-control" name="perdistri" value="{{ $address->perdistri ?? "" }}" id="perdistri" placeholder="Enter District">
                                                        </div>
                                                    </div>
                                                    </div>

                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="perstate">State</label>
                                                            <input type="text" class="form-control" name="perstate" value="{{ $address->perstate ?? "" }}" id="perstate" placeholder="Enter State">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="percountry">Country</label>
                                                            <input type="text" class="form-control" name="percountry" value="{{ $address->percountry ?? "" }}" id="percountry" placeholder="Enter Country">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="perpincode">Pincode</label>
                                                            <input type="text" class="form-control" name="perpincode" value="{{ $address->perpincode ?? "" }}" id="perpincode" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="perlandmark">Landmark</label>
                                                            <input type="text" class="form-control" name="perlandmark" value="{{ $address->perlandmark ?? "" }}" id="perlandmark" placeholder="Enter Landmark">
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
                       @endif
                        <!-- /row -->

                        <!--Bank info row -->
                        @if($bankinfo)
                        <form action="{{  route('updateBankInfo', $bankinfo->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                             <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Bank Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                                <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                               <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Bank Name</label>
                                                        <input type="text" class="form-control" name="bankname" value="{{ $bankinfo->bankname ?? "" }}" id="exampleInputEmail1" placeholder="Enter Bank Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Branch Name</label>
                                                        <input type="text" class="form-control" name="branchname" value="{{ $bankinfo->branchname ?? "" }}" id="exampleInputPassword1" placeholder="Enter Branch Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">IFSC Code</label>
                                                        <input type="text" class="form-control" name="ifsccode" value="{{ $bankinfo->ifsccode ?? "" }}" id="exampleInputPassword1" placeholder="Enter IFSC Code">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Account Holder Name</label>
                                                        <input type="text" class="form-control" name="accname" value="{{ $bankinfo->accname ?? "" }}" id="exampleInputEmail1" placeholder="Enter Account Holder Name">
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Account Number</label>
                                                        <input type="text" class="form-control" name="accnumber" value="{{ $bankinfo->accnumber ?? "" }}" id="exampleInputPassword1" placeholder="Enter Account Number">
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
                        @else
                        <form action="{{  url('admin/updateBankInfo') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                             <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="main-content-label mg-b-5">
                                                    Bank Information
                                            </div>
                                            <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                            <div class="row">
                                                <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                               <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Bank Name</label>
                                                        <input type="text" class="form-control" name="bankname" value="{{ $bankinfo->bankname ?? "" }}" id="exampleInputEmail1" placeholder="Enter Bank Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Branch Name</label>
                                                        <input type="text" class="form-control" name="branchname" value="{{ $bankinfo->branchname ?? "" }}" id="exampleInputPassword1" placeholder="Enter Branch Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">IFSC Code</label>
                                                        <input type="text" class="form-control" name="ifsccode" value="{{ $bankinfo->ifsccode ?? "" }}" id="exampleInputPassword1" placeholder="Enter IFSC Code">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Account Holder Name</label>
                                                        <input type="text" class="form-control" name="accname" value="{{ $bankinfo->accname ?? "" }}" id="exampleInputEmail1" placeholder="Enter Account Holder Name">
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Account Number</label>
                                                        <input type="text" class="form-control" name="accnumber" value="{{ $bankinfo->accnumber ?? "" }}" id="exampleInputPassword1" placeholder="Enter Account Number">
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
                        @endif
                        <!-- /row -->
                        <!-- row -->
                        <form action="{{  route('updateOtherInfo', $userinfo->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
					    <div class="row">
						    <div class="col-lg-12 col-md-">
								<div class="card custom-card">
									<div class="card-body">
										<div class="main-content-label mg-b-5">
												Other Information
										</div>
										<!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
										<div class="row">
                                            <input type="hidden" class="form-control" id="exampleInputEmail1" name="userid" value="{{ $userinfo->userid ?? "" }}" placeholder="Enter First Name">

                                            <div class="col-md-4">
                                            
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Date of join in temple seba</label>
                                                    <input type="date" class="form-control"  id="exampleInputEmail1" value="{{ $userinfo->datejoin ?? "" }}" name="datejoin" placeholder="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Bedha Seba</label>
                                                    <input type="tetx" class="form-control" id="exampleInputPassword1" name="bedhaseba" value="{{ $userinfo->bedhaseba ?? "" }}" placeholder="Enter Bedha Seba">
                                                </div>
                                              
                                            
                                            </div>
                                            <div class="col-md-4">
                                                
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Type of Seba</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail1" name="seba" value="{{ $userinfo->seba ?? "" }}" placeholder="Enter Type of Seba">
                                                </div>
                                                
                                                
                                            
                                        
                                             </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Temple Id</label>
                                                    <input type="text" name="templeid" class="form-control" value="{{ $userinfo->templeid ?? "" }}" id="exampleInputEmail1" placeholder="Enter Temple Id">
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
                        </form>
                        <!-- /row -->

                        

                    

                         
							
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
                    <div class="col-md-4" >
                                                        <div class="form-group">
                                                            <label for="">Children name</label>
                                                            <input type="text" class="form-control" name="childrenname[]" id="" placeholder="Enter Children name">
                                                        </div>
                                                      
                                                        
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group">
                                                            <label for="">DOB</label>
                                                            <input type="date" class="form-control" name="dob[]" id="" placeholder="Enter DOB">
                                                        </div>
                                                      
                                                        
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group">
                                                            <label for="">Gender</label>
                                                            <select name="gender[]" class="form-control" id="">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>

                                                            </select>
                                                            {{-- <input type="text" class="form-control" name="childrenname[]" id="" placeholder="Enter Children name"> --}}
                                                        </div>
                                                      
                                                        
                                                    </div>
                                                

                                                    <div class="col-md-2">
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
    @endsection
