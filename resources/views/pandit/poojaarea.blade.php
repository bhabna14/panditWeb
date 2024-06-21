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
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojaskill')}}" onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojadetails')}}" onclick="changeColor(this)">Add Details of Puja</a>
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojalist')}}"  onclick="changeColor(this)">Puja Item List</a>
                            <a class="nav-link mb-2 mt-2 active" href="{{url('pandit/poojaarea')}}" onclick="changeColor(this)">Areas of Service</a>
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
                        <!-- row -->
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="row">
                                                <input type="hidden" class="form-control" id="exampleInputEmail1"
                                                    name="userid" value="" placeholder="Enter First Name">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="country">Country Name</label>
                                                        <select class="form-control" name="country" id="country"
                                                            onchange="getStates(this.value)">
                                                            <option value=" ">Select Your Country</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}">{{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="state">State Name</label>
                                                        <select class="form-control" name="state" id="state"
                                                            onchange="getCity(this.value)">
                                                            <option value=" ">Select Your State</option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->id }}">{{ $state->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="city">City Name</label>
                                                        <select class="form-control" name="city" id="city">
                                                            <option value=" ">Select Your City</option>
                                                            @foreach ($citys as $city)
                                                                <option value="{{ $city->id }}">{{ $city->city }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="city">Location Name</label>
                                                        <select class="form-control select2" name="location[]"
                                                            id="location" multiple="multiple">
                                                            <option value=" ">Select Your Location</option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ $location }}">{{ $location }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label style="font-weight: bold;margin-top: 30px">
                                                            <input type="checkbox" id="across_bhubaneswar_checkbox"
                                                                name="across_bhubaneswar" value="1"
                                                                style="width: 30px; height: 30px; vertical-align: middle;"
                                                                onchange="toggleLocationDropdown()">
                                                            <span style="vertical-align: middle;">Across This City</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="main-content-label mg-b-5 mt-10">
                                                Temple Association
                                            </div>
                                            <hr>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="city">Temple Name</label>
                                                    <select class="form-control select2" name="temple[]" id="temple"
                                                        multiple="multiple">
                                                        <option value=" ">Select Your Temple</option>
                                                        @foreach ($temples as $temple)
                                                            <option value="{{ $temple }}">{{ $temple }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="city">Temple Address</label>

                                                <div class="form-group">
                                                    <textarea class="form-control" id="templeaddress" name="templeaddress" placeholder="Enter Temple Address"></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <div class="form-group">
                                                        <input type="submit" class="btn btn-primary" value="Submit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
