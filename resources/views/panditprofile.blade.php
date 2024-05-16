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
                                               <div style="text-align: center;border-bottom: 1px solid black">
                                                <h2>PROFILE INFORMATION</h2>
                                                
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
                                                            <div class="text-center col-md-12">

                                                                <a href="{{ url('/pandit/career') }}" class="btn btn-primary" style="width: 150px;">Submit</a>
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
