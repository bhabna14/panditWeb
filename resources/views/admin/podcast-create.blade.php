@extends('admin.layouts.app')

@section('styles')
    <style>
        /* Styling for the active button */
        .active-btn {
            color: white !important;
            /* Make text color white */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            /* Add subtle shadow */
            font-weight: bold;
            background-color: #4ec2f0;
            /* Bold text for emphasis */
        }
    </style>

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">CREATE PODCAST</span>
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
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 ">
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style=" color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-create') }}" onclick="setActive(this)">Create Podcast</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/podcast-script') }}"
                                onclick="setActive(this)">Script Of Podcast</a>
                           
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-script-verified') }}" onclick="setActive(this)">Script
                                Verified</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-recording') }}"
                                onclick="setActive(this)">Recording Of Podcast</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-editing') }}"
                                onclick="setActive(this)">Editing Of Podcast</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-editing-verified') }}" onclick="setActive(this)">Editing
                                Verified</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/publish-podcast') }}"
                                onclick="setActive(this)">Publish Podcast</a>
                                <a class="nav-link mb-2 mt-2" style="padding: 10px;"
                                href="{{ url('admin/podcast-media') }}" onclick="setActive(this)">Podcast
                                Media</a>
                                <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/social-media') }}"
                                onclick="setActive(this)">Social Media</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/podcast-report') }}"
                                onclick="setActive(this)">Report</a>
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
                    <div class="main-content-body tab-pane border-top-0 active" id="poojaskill">
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

                        <form action="{{ url('admin/save-podcast-create') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 col-md-">
                                    <div class="card custom-card">
                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="main-content-label mg-b-5">Podcast Language</div>

                                                        <select class="form-control" id="language" name="language"
                                                            required>
                                                            <option value=" ">Select Language</option>
                                                            <option value="odia">Odia</option>
                                                            <option value="english">English</option>
                                                            <option value="hindi">Hindi</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="main-content-label mg-b-5">Podcast Name</div>

                                                        <input type="text" class="form-control" id="podcast_name"
                                                            name="podcast_name" placeholder="Enter Podcast Name" required>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="main-content-label mg-b-5">Deity</div>

                                                        <select class="form-control" id="deity_category"
                                                            name="deity_category" required>
                                                            <option value="">Select Deity</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->category_name }}">
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="main-content-label mg-b-5">Festival</div>
                                                        <select class="form-control" id="festival_name"
                                                            name="festival_name" onchange="setFestivalDate()">
                                                            <option value="">Select Festival</option>
                                                            @foreach ($pooja_list as $pooja)
                                                                <option value="{{ $pooja->pooja_name }}"
                                                                    data-date="{{ $pooja->pooja_date }}">
                                                                    {{ $pooja->pooja_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="main-content-label mg-b-5">Publish Date</div>
                                                        <div class="input-group">

                                                            <div class="input-group-text">
                                                                <i
                                                                    class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                            </div>
                                                            <input class="form-control" id="date" name="date"
                                                                type="date">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>



                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" style="padding-top: 27px">
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
    <!-- Internal form-elements js -->
    <script src="{{ asset('assets/js/form-elements.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>

    <script>
        function setFestivalDate() {
            const festivalDropdown = document.getElementById('festival_name');
            const selectedOption = festivalDropdown.options[festivalDropdown.selectedIndex];
            const festivalDate = selectedOption.getAttribute('data-date');

            // Set the festival date in the input field
            document.getElementById('date').value = festivalDate;
        }
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Set the min attribute of the date input
            const dateInput = document.getElementById('date');
            dateInput.setAttribute('min', today);
        });
    </script>
@endsection
