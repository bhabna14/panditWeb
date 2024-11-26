@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">SCRIPT OF PODCAST</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Script</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 ">
                            <a class="nav-link mb-2 mt-2 " style="padding: 10px;" href="{{ url('admin/podcast-create') }}"
                                onclick="setActive(this)">Create Podcast</a>
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style="  color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-script') }}" onclick="setActive(this)">Script Of Podcast</a>

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
                            
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-media') }}"
                                onclick="setActive(this)">Podcast
                                Media</a>
                                <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/publish-podcast') }}"
                                onclick="setActive(this)">Publish Podcast</a>
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
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0 bg-info text-white">SlNo</th>
                                    <th class="border-bottom-0 bg-info text-white">Podcast Name</th>
                                    <th class="border-bottom-0 bg-info text-white">Add Script</th>
                                    <th class="border-bottom-0 bg-info text-white">Script File Location</th>
                                    <th class="border-bottom-0 bg-info text-white">Script Create Date</th>
                                    <th class="border-bottom-0 bg-info text-white">Source Of The Story</th>
                                    <th class="border-bottom-0 bg-info text-white">Script Created By</th>
                                    <th class="border-bottom-0 bg-info text-white">Save</th>
                                    <th class="border-bottom-0 bg-info text-white">Reject Reason</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($podcast_details as $index => $podcast)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $podcast->podcast_name }}</td>

                                        <td>
                                            <a href="{{ route('scriptEditor', ['podcast_id' => $podcast->podcast_id]) }}"
                                                class="btn btn-success" style="font-size: 16px">+</a>
                                        </td>

                                        <!-- Form to Update Script -->
                                        <form action="{{ route('updatePodcastScript', $podcast->podcast_id) }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <td>
                                                <input type="text" class="form-control" id="script_location"
                                                    name="script_location" placeholder="Enter Script File Location" value="{{ $podcast->script_location}}"
                                                    required>
                                            </td>

                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                    </div>
                                                    <input class="form-control script-created-date"
                                                        name="script_created_date" type="date" readonly>
                                                </div>
                                            </td>


                                            <td>
                                                <input type="text" class="form-control" id="story_source"
                                                    name="story_source" placeholder="Enter Source Of The Story" value="{{$podcast->story_source}}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="script_created_by"
                                                    name="script_created_by" placeholder="Enter Script Created By"  value="{{$podcast->script_created_by}}" 
                                                    required>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-success btn-md">Save</button>
                                            </td>
                                            <td>
                                                @if ($podcast->script_reject_reason)
                                                    {{ $podcast->script_reject_reason }}
                                                @else
                                                    Not Verified
                                                @endif
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No completed podcasts available for
                                            editing.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('scripts')
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>

    <script>
            document.getElementById('script_location').addEventListener('input', function() {
            const scriptLocation = this.value;
            const errorElement = document.getElementById('scriptLocationError');

            // URL validation regex
            const urlPattern = /^(https?:\/\/)?([\w\-])+\.{1}([a-zA-Z]{2,63})([\/\w\.-]*)*\/?$/;

            if (scriptLocation && !urlPattern.test(scriptLocation)) {
                // Show error if URL is invalid
                errorElement.style.display = 'block';
            } else {
                // Hide error if URL is valid
                errorElement.style.display = 'none';
            }
        });

        // Optional: Prevent form submission if the URL is invalid
        document.querySelector('form').addEventListener('submit', function(e) {
            const scriptLocation = document.getElementById('script_location').value;
            const errorElement = document.getElementById('scriptLocationError');

            if (!urlPattern.test(scriptLocation)) {
                e.preventDefault(); // Prevent form submission
                errorElement.style.display = 'block'; // Show error
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Select all elements with the class 'script-created-date'
            document.querySelectorAll('.script-created-date').forEach(input => {
                input.value = today;
            });
        });
    </script>
    
@endsection
