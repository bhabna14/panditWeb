@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">EDITING OF PODCAST</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editing</li>
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
                            <a class="nav-link mb-2 mt-2 " style="padding: 10px;" href="{{ url('admin/podcast-create') }}"
                                onclick="setActive(this)">Create Podcast</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px " href="{{ url('admin/podcast-script') }}"
                                onclick="setActive(this)">Script Of Podcast</a>
                          
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-script-verified') }}" onclick="setActive(this)">Script
                                Verified</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-recording') }}"
                                onclick="setActive(this)">Recording Of Podcast</a>
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style=" color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-editing') }}" onclick="setActive(this)">Podcast Editing</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-editing-verified') }}" onclick="setActive(this)">Editing
                                Verified</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/publish-podcast') }}"
                                onclick="setActive(this)">Publish Podcast</a>
                                <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-media') }}"
                                onclick="setActive(this)">Podcast
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
                    
                    @if (session('success'))
                        <div class="alert alert-success" id="Message">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger" id="Message">
                            {{ session('error') }}
                        </div>
                    @endif
                    

                        <div class="row row-sm">
                            <div class="col-lg-12">
                                <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                        <div class="table-responsive export-table">
                                            <table id="file-datatable"
                                                class="table table-bordered text-nowrap key-buttons border-bottom">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0 bg-info text-white">SlNo</th>
                                                        <th class="border-bottom-0 bg-info text-white">Podcast Name</th>
                                                        <th class="border-bottom-0 bg-info text-white">Recording URL</th>
                                                        <th class="border-bottom-0 bg-info text-white">Action</th>
                                                        <th class="border-bottom-0 bg-info text-white">Audio Edited By</th>
                                                        <th class="border-bottom-0 bg-info text-white">Music Source</th>
                                                        <th class="border-bottom-0 bg-info text-white">Editing Date</th>
                                                        <th class="border-bottom-0 bg-info text-white">Enter Complete URL
                                                        </th>
                                                        <th class="border-bottom-0 bg-info text-white">Save</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($podcast_editing as $index => $podcast)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $podcast->podcast_name }}</td>
                                                            <td>
                                                                @if ($podcast->recording_complete_url)
                                                                    <a href="{{ $podcast->recording_complete_url }}"
                                                                        target="_blank"
                                                                        class="btn btn-warning btn-md text-white">
                                                                        File Location
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($podcast->podcast_editing_status === 'PENDING')
                                                                    <form
                                                                        action="{{ route('startPodcastEdit', $podcast->podcast_id) }}"
                                                                        method="POST" class="start-podcast-edit-form"
                                                                        style="display: inline;">
                                                                        @csrf
                                                                        <button type="button"
                                                                            class="btn btn-primary btn-md start-podcast-edit-btn">Start
                                                                            Editing</button>
                                                                    </form>
                                                                @elseif ($podcast->podcast_editing_status === 'STARTED')
                                                                    <form
                                                                        action="{{ route('cancelPodcastEdit', $podcast->podcast_id) }}"
                                                                        method="POST" class="cancel-podcast-edit-form"
                                                                        style="display: inline;">
                                                                        @csrf
                                                                        <button type="button"
                                                                            class="btn btn-warning btn-md cancel-podcast-edit-btn">Cancel</button>
                                                                    </form>
                                                                    <form
                                                                        action="{{ route('completePodcastEdit', $podcast->podcast_id) }}"
                                                                        method="POST" class="complete-podcast-edit-form"
                                                                        style="display: inline;">
                                                                        @csrf
                                                                        <button type="button"
                                                                            class="btn btn-success btn-md complete-podcast-edit-btn">Complete</button>
                                                                    </form>
                                                                @elseif ($podcast->podcast_editing_status === 'EDITING COMPLETED')
                                                                    <button class="btn btn-info btn-md">Completed</button>
                                                                @endif
                                                            </td>
                                                            <td>
                                                        <form action="{{ route('podcast.saveEditing', $podcast->podcast_id) }}"  method="POST">
                                                                @csrf
                                                                <input type="text" class="form-control" name="audio_edited_by"  placeholder="Audio Edited By" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="music_source" placeholder="Enter Music Source">
                                                            </td>
                                                            <td>
                                                                <input type="date" class="form-control editing-date" name="editing_date" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="editing_complete_url"  placeholder="Enter Complete URL">
                                                            </td>
                                                            <td>
                                                                <button type="submit" class="btn btn-success btn-md">Save</button>
                                                            </td>
                                                        </form>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center">No completed podcasts
                                                                available for editing.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
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
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all input fields and forms on the page
            const forms = document.querySelectorAll('form');

            forms.forEach(function(form) {
                // Add input event listener for the `complete_url` field inside the form
                const urlInput = form.querySelector('input[name="complete_url"]');
                const errorElement = form.querySelector('#scriptLocationError');

                if (urlInput) {
                    urlInput.addEventListener('input', function() {
                        const scriptLocation = this.value;

                        // URL validation regex
                        const urlPattern =
                            /^(https?:\/\/)?([\w\-])+\.{1}([a-zA-Z]{2,63})([\/\w\.-]*)*\/?$/;

                        if (scriptLocation && !urlPattern.test(scriptLocation)) {
                            // Show error if URL is invalid
                            errorElement.style.display = 'block';
                        } else {
                            // Hide error if URL is valid
                            errorElement.style.display = 'none';
                        }
                    });
                }

                // Add submit event listener to the form
                form.addEventListener('submit', function(e) {
                    const scriptLocation = urlInput.value;
                    const urlPattern =
                        /^(https?:\/\/)?([\w\-])+\.{1}([a-zA-Z]{2,63})([\/\w\.-]*)*\/?$/;

                    if (!urlPattern.test(scriptLocation)) {
                        e.preventDefault(); // Prevent form submission
                        errorElement.style.display = 'block'; // Show error
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle Start Editing confirmation
            const startEditButtons = document.querySelectorAll(".start-podcast-edit-btn");
            startEditButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const form = this.closest(".start-podcast-edit-form");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to start editing this podcast?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Start!",
                        cancelButtonText: "Cancel"
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Handle Cancel Editing confirmation
            const cancelEditButtons = document.querySelectorAll(".cancel-podcast-edit-btn");
            cancelEditButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const form = this.closest(".cancel-podcast-edit-form");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to cancel editing this podcast?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#ffc107",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Cancel!",
                        cancelButtonText: "No, Keep Editing"
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Handle Complete Editing confirmation
            const completeEditButtons = document.querySelectorAll(".complete-podcast-edit-btn");
            completeEditButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const form = this.closest(".complete-podcast-edit-form");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to mark this podcast editing as complete?",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#28a745",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Complete!",
                        cancelButtonText: "No"
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    <script>
    setTimeout(function() {
        let messageElement = document.getElementById('Message');
        if (messageElement) {
            messageElement.style.display = 'none';
        }
    }, 5000); // Hide after 5 seconds
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Set the value for all inputs with the class 'editing-date'
            document.querySelectorAll('.editing-date').forEach(input => {
                input.value = today;
            });
        });
    </script>
@endsection
