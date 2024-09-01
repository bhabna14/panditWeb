@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
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
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 full-width-tabs">
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaskill') }}"
                                onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojadetails') }}"
                                onclick="changeColor(this)">Add Details of Pooja</a>
                                <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaitemlist') }}"
                                onclick="changeColor(this)">Pooja Item List</a>
                            <a class="nav-link mb-2 mt-2 active" href="{{ url('pandit/poojaarea') }}"
                                onclick="changeColor(this)">Areas of Service</a>
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
                    <div class="main-content-body tab-pane border-top-0 active" id="poojaarea">
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
                        <!-- row -->
                        <form action="{{ route('save.form') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="state">State Name</label>
                                                        <select class="form-control" name="state" id="state" onchange="getDistrict(this.value)">
                                                            <option value="">Select Your State</option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->stateCode }}">{{ $state->stateName }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="district">District Name</label>
                                                        <select class="form-control" name="district" id="district" onchange="getSubdistrict(this.value)">
                                                            <option value="">Select Your District</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="city">City Name</label>
                                                        <select class="form-control" name="city" id="city" onchange="getVillage(this.value)">
                                                            <option value="">Select Your City</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="village">Village Name</label>
                                                        <select class="form-control select2" id="village" name="village[]" multiple="multiple">
                                                            @foreach ($villages as $village)
                                                            <option value="{{ $village->villageCode }}">{{ $village->villageName }} {{ $village->pincode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('scripts')
    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-area.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
