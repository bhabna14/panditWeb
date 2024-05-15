@extends('pandit.layouts.custom-app')

@section('styles')
@endsection

@section('class')
    <div class="bg-primary">
    @endsection

    @section('content')

        <div class="page-single">
            <div class="container">
                <div class="row">
                    <div
                         class="col-xl-5 col-lg-6 col-md-8 col-sm-8 col-xs-10 card-sigin-main mx-auto my-auto py-45 justify-content-center">
                        <div class="card-sigin mt-5 mt-md-0">
                            <!-- Demo content-->
                            <div class="main-card-signin d-md-flex">
                                <div class="wd-100p">
                                    <div class="d-flex mb-4">
                                        <a href="#"><img src="{{ asset('assets/img/brand/logo.png') }}" class="sign-favicon ht-40" alt="logo"></a>
                                    </div>
                                    <div class="">
                                        <div class="main-signup-header">
                                            <div class="panel panel-primary">
                                                <div class=" tab-menu-heading mb-2 border-bottom-0">
                                                    <div class="tabs-menu1">
                                                        <ul class="nav panel-tabs"
                                                            style="display: flex;justify-content: space-between">
                                                            <li><a href="#profile" data-bs-toggle="tab"
                                                                class="active" >Profile</a></li>
                                                            <li><a href="#career" data-bs-toggle="tab"
                                                                    class="">Career</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="panel-body tabs-menu-body border-0 p-3">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="profile">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">Title</label>
                                                                        <input type="text" class="form-control"
                                                                            value="" id="title" name="title"
                                                                            placeholder="Enter Title">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">Name</label>
                                                                        <input type="email" class="form-control"
                                                                            value="" id="exampleInputEmail1"
                                                                            name="email" placeholder="Enter Name">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">Email
                                                                            address</label>
                                                                        <input type="email" class="form-control"
                                                                            value="" id="exampleInputEmail1"
                                                                            name="email" placeholder="Enter email">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputPassword1">Whatsapp
                                                                            Number</label>
                                                                        <input type="text" class="form-control"
                                                                            value="" id="exampleInputPassword1"
                                                                            name="phonenumber" placeholder="Phone Number">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputPassword1">Blood
                                                                            Group</label>
                                                                        <input type="text" class="form-control"
                                                                            value="" id="exampleInputPassword1"
                                                                            name="bloodgrp"
                                                                            placeholder="Enter Blood Group">
                                                                    </div>
                                                                </div>


                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputPassword1">Photo</label>
                                                                        <input type="file" name="userphoto"
                                                                            class="form-control"
                                                                            id="exampleInputPassword1">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">Marital
                                                                            Status</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="rdiobox"><input name="marital"
                                                                            type="radio"> <span>Married </span></label>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <label class="rdiobox"><input checked name="marital"
                                                                            type="radio"> <span>Unmarried </span></label>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="language">Select Language</label>
                                                                        <select class="form-control select2"
                                                                            id="language" name="language[]"
                                                                            multiple="multiple">
                                                                            {{-- @foreach ($languages as $language)
                                                                                <option value="{{ $language }}">
                                                                                    {{ $language }}</option>
                                                                            @endforeach --}}
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                          
                                                        </div>
                                                        <div class="tab-pane" id="career">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">Highest
                                                                            Qualification</label>
                                                                        <input type="text" class="form-control"
                                                                            name="qualification" id="qualification"
                                                                            placeholder="Enter Heighest Qualification">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputPassword1">Total
                                                                            Experience</label>
                                                                        <input type="text" class="form-control"
                                                                            name="experience" id="experience"
                                                                            placeholder="Total Experience">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="show_doc_item">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">ID
                                                                                Proof</label>
                                                                            <select name="idproof[]" class="form-control"
                                                                                id="">
                                                                                <option value=" ">Select...</option>
                                                                                <option value="adhar">Adhar Card</option>
                                                                                <option value="voter">Voter Card</option>
                                                                                <option value="pan">Pan Card</option>
                                                                                <option value="DL">DL</option>
                                                                                <option value="health card">Health Card
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputPassword1">Upload
                                                                            </label>
                                                                            <input type="file" class="form-control"
                                                                                name="uploadDocument[]"
                                                                                id="uploadDocument" placeholder="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2" style="margin-top: 27px">
                                                                        <div class="form-group">
                                                                            <button type="button"
                                                                                class="btn btn-success add_item_btn"
                                                                                onclick="addIdSection()">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="show_edu_item">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">
                                                                                Qualification</label>
                                                                            <select name="education[]"
                                                                                class="form-control" id="">
                                                                                <option value=" ">Select..</option>
                                                                                <option value="10th">10th</option>
                                                                                <option value="+2">+2</option>
                                                                                <option value="+3">+3</option>
                                                                                <option value="Master Degree">Master Degree
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputPassword1">Upload
                                                                            </label>
                                                                            <input type="file" class="form-control"
                                                                                name="uploadEducation[]"
                                                                                id="uploadEducation" placeholder="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2" style="margin-top: 27px">
                                                                        <div class="form-group">
                                                                            <button type="button"
                                                                                class="btn btn-success add_item_btn"
                                                                                onclick="addEduSection()">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="show_vedic_item">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">Vedic Type
                                                                            </label>
                                                                            <select name="idproof[]" class="form-control"
                                                                                id="">
                                                                                <option value=" ">Select..</option>

                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputPassword1">Upload
                                                                            </label>
                                                                            <input type="file" class="form-control"
                                                                                name="uploadDocument[]"
                                                                                id="uploadDocument" placeholder="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2" style="margin-top: 27px">
                                                                        <div class="form-group">
                                                                            <button type="button"
                                                                                class="btn btn-success add_item_btn"
                                                                                onclick="addVedicSection()">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-center col-md-12">
                                                                <a href="{{ url('/pandit/dashboard') }}" class="btn btn-primary" style="width: 150px;">Submit</a>
                                                            </div>
                                                        </div>
                                                        <div class=" tab-menu-heading mb-2 border-bottom-0">
                                                            <div class="tabs-menu1">
                                                                <ul class="nav panel-tabs"
                                                                    style="display: flex;justify-content: space-between">
                                                                    <li style="background-color: rgb(97, 211, 243);color: black;border-radius:15px"><a href="#profile" data-bs-toggle="tab"
                                                                        class="active" >Prev</a></li>
                                                                    <li style="background-color:  rgb(97, 211, 243);color: black;border-radius:15px"><a href="#career" data-bs-toggle="tab"
                                                                            class="">Next</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('scripts')
        <!-- generate-otp js -->
        <script src="{{ asset('assets/js/generate-otp.js') }}"></script>
        <script src="{{ asset('assets/js/login.js') }}"></script>

        <script>
            document.getElementById('nextBtn').addEventListener('click', function() {
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
            });
        </script>
    @endsection
