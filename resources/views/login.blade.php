@extends('layouts.custom-app')

    @section('styles')
    <title> User Login</title>
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
                                                            <li class="me-2"><a href="#tab5" class="active"
                                                                    data-bs-toggle="tab">Login</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="panel-body tabs-menu-body border-0 p-3">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="tab5">
                                                            @if (session('success'))
                                                                <div class="alert alert-success">
                                                                    {{ session('success') }}
                                                                </div>
                                                            @elseif (session('error'))
                                                                <div class="alert alert-danger">
                                                                    {{ session('error') }}
                                                                </div>
                                                            @endif
                                                            @if (session('otp_sent'))
                                                            {{-- <form action="/verify-otp" method="POST">
                                                                @csrf
                                                                <input type="text" name="order_id" value="{{ session('otp_order_id') }}" required>
                                                                <input type="text" name="otp" placeholder="Enter OTP" required>
                                                                <input type="text" name="phone" value="{{ session('otp_phone') }}" placeholder="Enter your phone number" required>
                                                                <button type="submit">Verify OTP</button>
                                                            </form> --}}
                                                            <form id="loginForm" action="/verify-otp" method="POST">
                                                                @csrf
                                                               
                                                                <div id="step1">
                                                                    <div class="form-group">
                                                                        {{-- <label for="mobile_no">Orderid</label> --}}
                                                                        <div style="display: flex; align-items: center;">
                                                                            {{-- <input type="text" class="form-control" id="country_code" name="country_code" value="+91" readonly style="background-color: #f1f1f1; width: 60px; text-align: center;"> --}}
                                                                            <input type="hidden" class="form-control" id="phone" value="{{ session('otp_order_id') }}" name="order_id" style="margin-left: 5px; flex: 1;" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="mobile_no">Enter OTP</label>
                                                                        <div style="display: flex; align-items: center;">
                                                                            {{-- <input type="text" class="form-control" id="country_code" name="country_code" value="+91" readonly style="background-color: #f1f1f1; width: 60px; text-align: center;"> --}}
                                                                            <input type="text" class="form-control" id="phone" placeholder="Enter OTP" name="otp" style="margin-left: 5px; flex: 1;" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        {{-- <label for="mobile_no">Phone Number</label> --}}
                                                                        <div style="display: flex; align-items: center;">
                                                                            {{-- <input type="text" class="form-control" id="country_code" name="country_code" value="+91" readonly style="background-color: #f1f1f1; width: 60px; text-align: center;"> --}}
                                                                            <input type="hidden" class="form-control" id="phone"  value="{{ session('otp_phone') }}" name="phone" style="margin-left: 5px; flex: 1;" required>
                                                                        </div>
                                                                    </div>
                                                                    {{-- <input type="submit" class="btn btn-primary" value="Generate OTP"> --}}
                                                                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                                                                </div>
                                                            </form>
                                                            @else
                                                            <form id="loginForm" action="/send-otp" method="POST">
                                                                @csrf
                                                               
                                                                <div id="step1">
                                                                    <div class="form-group">
                                                                        <label for="mobile_no">Phone Number</label>
                                                                        <div style="display: flex; align-items: center;">
                                                                            <input type="text" class="form-control" id="country_code" name="country_code" value="+91" readonly style="background-color: #f1f1f1; width: 60px; text-align: center;">
                                                                            <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" style="margin-left: 5px; flex: 1;">
                                                                        </div>
                                                                    </div>
                                                                    <input type="submit" class="btn btn-primary" value="Generate OTP">
                                                                </div>
                                                            </form>
                                                            @endif
                                                            
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
		<script src="{{asset('assets/js/generate-otp.js')}}"></script>
        <script src="{{asset('assets/js/pandit-career.js')}}"></script>

        <script>
            document.getElementById('nextBtn').addEventListener('click', function() {
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
            });
        </script>

    @endsection
