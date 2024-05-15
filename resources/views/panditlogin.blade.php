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
                                                            <li class="me-2"><a href="#tab5" class="active" data-bs-toggle="tab">Login</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="panel-body tabs-menu-body border-0 p-3">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="tab5">
                                                            @if ($errors->any())
                                                                <div class="alert alert-danger">
                                                                    <ul>
                                                                        @foreach ($errors->all() as $error)
                                                                            <li>{{ $error }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                            <form id="loginForm" action="{{ url('/pandit/career') }}">
                                                                {{-- @csrf --}}

                                                                <div id="step1">
                                                                    <div class="form-group">
                                                                        <label>Phone Number</label> <input
                                                                            class="form-control"
                                                                            placeholder="Enter your Phone Number"
                                                                            name="phonenumber" value="+91"
                                                                            type="text">
                                                                    </div>
                                                                    <button type="button" id="nextBtn"
                                                                        class="btn btn-primary">
                                                                        {{ __('Generate Otp') }}
                                                                    </button>
                                                                </div>

                                                                <div id="step2" style="display: none;">
                                                                    <div class="form-group">
                                                                        <label>otp</label> <input class="form-control"
                                                                            placeholder="Enter your otp" name="otp"
                                                                            type="text">
                                                                    </div>


                                                                    <input type="submit" class="btn btn-primary"
                                                                        value="Login">

                                                                </div>
                                                            </form>
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
