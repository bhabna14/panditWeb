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
                            <a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#skill"
                                onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#addpuja"
                                onclick="changeColor(this)">Add Details of Puja</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#pujalist"
                                onclick="changeColor(this)">Puja List</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#area"
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
                    <div class="main-content-body tab-pane border-top-0 active" id="skill">
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

                        <form action="{{ url('/pandit/save-skillpooja') }}" method="post" enctype="multipart/form-data">
                            <div class="row mb-5">
                                @csrf
                                @foreach ($Poojanames as $pooja)
                                    <div class="col-lg-3 col-md-6 col-sm-12">

                                        <div class="card p-3">
                                            <div class="card-body">
                                                <div class="mb-3 text-center about-team">
                                                    <!-- Wrap the image inside a label -->
                                                    <label for="checkbox{{ $pooja->id }}">
                                                        <img class="rounded-pill"
                                                            src="{{ asset('assets/img/' . $pooja->pooja_photo) }}"
                                                            alt="{{ $pooja->pooja_name }}">
                                                    </label>
                                                </div>
                                                <div class="tx-16 text-center font-weight-semibold">
                                                    {{ $pooja->pooja_name }}
                                                </div>
                                                <div class="form-check mt-3 text-center">
                                                    <input class="form-check-input checks" type="checkbox"
                                                        id="checkbox{{ $pooja->id }}"
                                                        name="poojas[{{ $pooja->id }}][id]"
                                                        value="{{ $pooja->id }}">
                                                    <input type="hidden" name="poojas[{{ $pooja->id }}][name]"
                                                        value="{{ $pooja->pooja_name }}">
                                                    <input type="hidden" name="poojas[{{ $pooja->id }}][image]"
                                                        value="{{ $pooja->pooja_photo }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="text-center col-md-12">
                                    <button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="main-content-body   tab-pane " id="addpuja">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive  export-table">
                                    <form action="{{ url('/pandit/save-poojadetails') }}" method="post" enctype="multipart/form-data">
                                        @csrf

                                    <table id="file-datatable"
                                        class="table table-bordered text-nowrap key-buttons border-bottom">
                                        <thead>
                                            <tr>
                                                <th class="border-bottom-0">Slno</th>
                                                <th class="border-bottom-0">Puja Name</th>
                                                <th class="border-bottom-0">Fee</th>
                                                <th class="border-bottom-0">Duration</th>
                                                <th class="border-bottom-0">Image</th>
                                                <th class="border-bottom-0">Video</th>
                                                <th class="border-bottom-0">How Many Pooja you have done</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                               
                                                @foreach ($Poojaskills as $index => $poojaSkill)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td class="tb-col">
                                                            <div class="media-group">
                                                                <div class="media media-md media-middle media-circle">
                                                                    <img src="{{ asset('assets/img/' . $poojaSkill->pooja_photo) }}"
                                                                        alt="{{ $poojaSkill->pooja_name }}">
                                                                </div>
                                                                <div class="media-text">
                                                                    <a href=""
                                                                        class="title">{{ $poojaSkill->pooja_name }}</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="fee[{{ $poojaSkill->id }}]"
                                                                class="form-control"
                                                                value="{{ old('fee.' . $poojaSkill->id) }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="duration[{{ $poojaSkill->id }}]"
                                                                class="form-control"
                                                                value="{{ old('duration.' . $poojaSkill->id) }}">
                                                        </td>
                                                        <td>
                                                            <input type="file" name="image[{{ $poojaSkill->id }}]"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="file" name="video[{{ $poojaSkill->id }}]"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="done_count[{{ $poojaSkill->id }}]"
                                                                class="form-control"
                                                                value="{{ old('done_count.' . $poojaSkill->id) }}">
                                                        </td>
                                                        <input type="hidden" name="pooja_id[{{ $poojaSkill->id }}]"
                                                            value="{{ $poojaSkill->pooja_id }}">
                                                        <input type="hidden" name="pooja_name[{{ $poojaSkill->id }}]"
                                                            value="{{ $poojaSkill->pooja_name }}">
                                                             <div class="text-center col-md-12">
                                                    </div>
                                                    </tr>
                                                @endforeach
                                               
                                                  
                                        </tbody>
                                    </table>
                                        <div class="text-center col-md-12">
                                            <button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
                                        </div>
                                </form>

                                </div>
                             
                            </div>
                        </div>
                    </div>
                    <div class="main-content-body   tab-pane " id="pujalist">
                        <div class="card">
                            <div class="card-body">
                                <div class="panel-group1" id="accordion11" role="tablist">
                                    <div class="card overflow-hidden">
                                        <a class="accordion-toggle panel-heading1 collapsed " data-bs-toggle="collapse"
                                            data-bs-parent="#accordion11" href="#collapseFour1"
                                            aria-expanded="false">Ganesh Pooja</a>
                                        <div id="collapseFour1" class="panel-collapse collapse" role="tabpanel"
                                            aria-expanded="false">
                                            <div class="panel-body">
                                                <div class="table-responsive  export-table">
                                                    <table id="file-datatable"
                                                        class="table table-bordered text-nowrap key-buttons border-bottom">
                                                        <thead>
                                                            <tr>
                                                                <th class="border-bottom-0">#</th>
                                                                <th class="border-bottom-0">Puja Name</th>
                                                                <th class="border-bottom-0">List Name</th>
                                                                <th class="border-bottom-0">Quantity</th>
                                                                <th class="border-bottom-0">Unit</th>
                                                                <th class="border-bottom-0">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="show_puja_item">
                                                            <tr>
                                                                <td>1</td>
                                                                <td class="tb-col">
                                                                    <div class="media-group">
                                                                        <div
                                                                            class="media media-md media-middle media-circle">
                                                                            <img src="{{ asset('assets/img/user.jpg') }}"
                                                                                alt="user">
                                                                        </div>
                                                                        <div class="media-text">
                                                                            <a href="" class="title">Ganesh
                                                                                Puja</a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td><select class="form-control" name="country"
                                                                        id="country">
                                                                        <option value=" ">Select Your Puja List
                                                                        </option>
                                                                        @foreach ($PujaLists as $pujalist)
                                                                            <option value="{{ $pujalist }}">
                                                                                {{ $pujalist }}</option>
                                                                        @endforeach
                                                                    </select></td>
                                                                <td><input type="number" class="form-control"
                                                                        name="quantity[]" value="" id="quantity"
                                                                        placeholder="Enter List Quatity"></td>
                                                                <td>
                                                                    <select class="form-control" id="weight_unit"
                                                                        name="weight_unit">
                                                                        <option value=" ">Select Unit</option>
                                                                        <option value="kg">Kilogram (kg)</option>
                                                                        <option value="g">Gram (g)</option>
                                                                        <option value="mg">Milligram (mg)</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-success add_item_btn"
                                                                        onclick="addPujaListSection()">Add More</button>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="text-center col-md-12">
                                                    <button type="submit" class="btn btn-primary"
                                                        style="width: 150px;">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="main-content-body tab-pane border-top-0" id="area">
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
