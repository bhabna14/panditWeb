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
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-create') }}" onclick="setActive(this)">Create Podcast</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/podcast-script') }}"
                                onclick="setActive(this)">Script</a>
                           
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-script-verified') }}" onclick="setActive(this)">
                                Verification</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-recording') }}"
                                onclick="setActive(this)">Recording</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-editing') }}"
                                onclick="setActive(this)">Editing</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-editing-verified') }}" onclick="setActive(this)">
                                Verified</a>
                           
                                <a class="nav-link mb-2 mt-2" style="padding: 10px;"
                                href="{{ url('admin/podcast-media') }}" onclick="setActive(this)">
                                Creatives</a>
                                <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/publish-podcast') }}"
                                onclick="setActive(this)">Publish</a>
                                <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/social-media') }}"
                                onclick="setActive(this)">Social Media</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/podcast-report') }}"
                                onclick="setActive(this)">Report</a>
                            <a class="nav-link mb-2 mt-2  bg-warning"  style=" color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;" href="{{ url('admin/podcast-planning') }}"
                                onclick="setActive(this)">Planning</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion" id="podcastAccordion">
        @forelse ($sortedPodcastDetails as $month => $podcasts)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                    <button 
                        class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"  
                        style="background: linear-gradient(120deg, #eba4e5, #4a3c44);" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse{{ $loop->index }}" 
                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                        aria-controls="collapse{{ $loop->index }}">
                        {{ $month }}
                    </button>
                </h2>
                <div id="collapse{{ $loop->index }}" 
                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                     aria-labelledby="heading{{ $loop->index }}" 
                     data-bs-parent="#podcastAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap key-buttons border-bottom">
                                <thead class="bg-info text-dark">
                                    <tr>
                                        <th class="text-white" style="background-color: rgb(20,30,200)">SlNo</th>
                                        <th class="text-white" style="background-color: rgb(20,30,200)">Date</th>
                                        <th class="text-white" style="background-color: rgb(20,30,200)">Podcast Name</th>
                                        <th class="text-white" style="background-color: rgb(20,30,200)">Podcast Type</th>
                                        <th class="text-white" style="background-color: rgb(20,30,200)">Publish Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($podcasts as $index => $podcast) {{-- Use $podcasts for this month --}}
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $podcast->podcast_create_date }}</td>
                                            <td>{{ $podcast->podcast_name }}</td>
                                            <td>{{ $podcast->podcast_type }}</td>
                                            <td>{{ $podcast->estimate_publish_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center">No podcasts available for the selected months.</div>
        @endforelse
    </div>
    

    @endsection

    @section('scripts')

    @endsection
