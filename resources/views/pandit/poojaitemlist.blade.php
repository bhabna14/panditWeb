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
                            <a class="nav-link mb-2 mt-2 active"  href="{{url('pandit/poojalist')}}"  onclick="changeColor(this)">Puja Item List</a>
                            <a class="nav-link mb-2 mt-2" href="{{url('pandit/poojaarea')}}" onclick="changeColor(this)">Areas of Service</a>
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
                    <div class="main-content-body   tab-pane active" id="poojaitemlist">
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
