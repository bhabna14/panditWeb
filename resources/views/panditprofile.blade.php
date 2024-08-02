@extends('pandit.layouts.custom-app')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
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
                                    <div class="d-flex mb-4" style="justify-content: center;">
                                        <a href="#"><img src="{{ asset('assets/img/brand/logo.png') }}"
                                                class="sign-favicon ht-40" alt="logo"></a>
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
                                                            <!-- Display Success Message -->


                                                            @if ($errors->any())
                                                                <div class="alert alert-danger">
                                                                    <ul>
                                                                        @foreach ($errors->all() as $error)
                                                                            <li>{{ $error }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif

                                                            @if (session()->has('success'))
                                                                <div class="alert alert-success" id="Message">
                                                                    {{ session()->get('success') }}
                                                                </div>
                                                            @endif

                                                            @if ($errors->has('danger'))
                                                                <div class="alert alert-danger" id="Message">
                                                                    {{ $errors->first('danger') }}
                                                                </div>
                                                            @endif

                                                            {{-- form start --}}

                                                            <form action="{{ url('/pandit/save-profile') }}" method="post"
                                                                enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="row">
                                                                    
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="title">Title</label>
                                                                            <select class="form-control" id="title" name="title" required>
                                                                                @foreach ($pandititle as $title)
                                                                                    <option value="{{ $title }}">{{ $title }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">Full Name <span style="color: red">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="name" name="name"
                                                                                placeholder="Enter Name" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">Email
                                                                                address</label>
                                                                            <input type="email" class="form-control"
                                                                                id="email" name="email"
                                                                                placeholder="Enter email">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputPassword1">Whatsapp
                                                                                Number <span style="color: red">*</span></label>
                                                                            <input type="number" class="form-control"
                                                                                id="whatsappno" name="whatsappno"
                                                                                placeholder="Whatsapp Number" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="blood_group">Blood Group</label>
                                                                            <select class="form-control" id="bloodgroup" name="bloodgroup">
                                                                                <option value=" ">Select...</option>
                                                                                <option value="A+">A+</option>
                                                                                <option value="A-">A-</option>
                                                                                <option value="B+">B+</option>
                                                                                <option value="B-">B-</option>
                                                                                <option value="AB+">AB+</option>
                                                                                <option value="AB-">AB-</option>
                                                                                <option value="O+">O+</option>
                                                                                <option value="O-">O-</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputPassword1">Profile Photo <span style="color: red">*</span></label>
                                                                            <input type="file" name="profile_photo"
                                                                                class="form-control" id="profile_photo" required>
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
                                                                                type="radio" value="Married">
                                                                            <span>Married</span></label>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <label class="rdiobox"><input checked name="marital"
                                                                                type="radio" value="Unmarried">
                                                                            <span>Unmarried</span></label>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="language">Select Language <span style="color: red">*</span></label>
                                                                            <select class="form-control select2"
                                                                                id="language" name="language[]"
                                                                                multiple="multiple" required>
                                                                                @foreach ($languages as $language)
                                                                                    <option value="{{ $language }}">
                                                                                        {{ $language }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="about">About Pandit<span style="color: red">*</span></label>
                                                                            <textarea class="form-control" name="about" id="about"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="text-center col-md-12">
                                                                    <button type="submit" class="btn btn-primary"
                                                                        style="width: 150px;">Submit</button>
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
        <script src="{{ asset('assets/js/generate-otp.js') }}"></script>
        <script src="{{ asset('assets/js/login.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/select2.js') }}"></script>
        <script>
            setTimeout(function(){
                document.getElementById('Message').style.display = 'none';
            }, 3000);
        </script>
    @endsection
