@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">RECORDING OF PODCAST</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Recording</li>
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
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style=" color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-recording') }}" onclick="setActive(this)">Recording Of
                                Podcast</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-editing') }}"
                                onclick="setActive(this)">Editing Of Podcast</a>
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
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
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
                                                        <th class="border-bottom-0 bg-info text-white">View Script</th>
                                                        <th class="border-bottom-0 bg-info text-white">Completed Script
                                                        </th>
                                                        <th class="border-bottom-0 bg-info text-white">Script Created By
                                                        </th>
                                                        <th class="border-bottom-0 bg-info text-white">Action</th>
                                                        <th class="border-bottom-0 bg-info text-white">Recorded File URL
                                                        </th>
                                                        <th class="border-bottom-0 bg-info text-white">Recorded By</th>
                                                        <th class="border-bottom-0 bg-info text-white">Recording Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($podcast_recording as $key => $podcast)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $podcast->podcast_name ?? 'N/A' }}</td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-success view-script-btn"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#scriptEditorModal"
                                                                    data-script-editor="{{ $podcast->script_editor }}">
                                                                    Script
                                                                </button>
                                                            </td>
                                                            <td>
                                                                @if ($podcast->script_location)
                                                                    <a href="{{ $podcast->script_location }}"
                                                                        target="_blank"
                                                                        class="btn btn-danger btn-md text-white">
                                                                        File Location
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $podcast->script_created_by }}</td>
                                                            <td>
                                                                @if ($podcast->podcast_recording_status === 'PENDING')
                                                                    <form
                                                                        action="{{ route('startPodcast', $podcast->podcast_id) }}"
                                                                        method="POST" style="display: inline;"
                                                                        class="start-podcast-form">
                                                                        @csrf
                                                                        <button type="button"
                                                                            class="btn btn-primary btn-md start-podcast-btn">Start
                                                                            Podcast</button>
                                                                    </form>
                                                                @elseif($podcast->podcast_recording_status === 'STARTED')
                                                                    <!-- Cancel Podcast Button -->
                                                                    <form
                                                                        action="{{ route('cancelPodcast', $podcast->podcast_id) }}"
                                                                        method="POST" class="cancel-podcast-form"
                                                                        style="display: inline;">
                                                                        @csrf
                                                                        <button type="button"
                                                                            class="btn btn-warning btn-md cancel-podcast-btn">Cancel</button>
                                                                    </form>

                                                                    <!-- Complete Podcast Button -->
                                                                    <form
                                                                        action="{{ route('completePodcast', $podcast->podcast_id) }}"
                                                                        method="POST" class="complete-podcast-form"
                                                                        style="display: inline;">
                                                                        @csrf
                                                                        <button type="button"
                                                                            class="btn btn-success btn-md complete-podcast-btn">Complete</button>
                                                                    </form>
                                                                @elseif($podcast->podcast_recording_status === 'RECORDING COMPLETED')
                                                                    <button class="btn btn-info">Completed</button>
                                                                @endif
                                                            </td>
                                                            <form
                                                                action="{{ route('saveCompleteUrl', $podcast->podcast_id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            name="recording_complete_url"
                                                                            placeholder="Enter Complete URL" required>
                                                                        <small class="text-danger scriptLocationError"
                                                                            style="display: none;">Please enter a valid
                                                                            URL.</small>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            name="podcast_recording_by"
                                                                            placeholder="Recorded by" required>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="date"
                                                                            class="form-control recording-date"
                                                                            name="recording_date" required readonly>
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm">Save</button>
                                                                    </div>
                                                                </td>


                                                            </form>


                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>


                                            <div class="modal fade" id="scriptEditorModal" tabindex="-1"
                                                aria-labelledby="scriptEditorModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="scriptEditorModalLabel">Script
                                                                Details</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Script editor content will be loaded here -->
                                                            <textarea id="script-editor-content" class="form-control" rows="10"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
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
            // URL validation regex
            const urlPattern = /^(https?:\/\/)?([\w-]+\.{1}[a-zA-Z]{2,63})([\/\w.-]*)*\/?$/;

            // Get all forms on the page
            const forms = document.querySelectorAll('form');

            forms.forEach(function(form) {
                // Get the `recording_complete_url` input field and error element within the form
                const urlInput = form.querySelector('input[name="recording_complete_url"]');
                const errorElement = form.querySelector('.scriptLocationError');

                if (urlInput && errorElement) {
                    // Validate URL on input event
                    urlInput.addEventListener('input', function() {
                        const scriptLocation = this.value;

                        if (scriptLocation && !urlPattern.test(scriptLocation)) {
                            // Show error if URL is invalid
                            errorElement.style.display = 'block';
                            errorElement.textContent = 'Please enter a valid URL.';
                        } else {
                            // Hide error if URL is valid
                            errorElement.style.display = 'none';
                        }
                    });

                    // Validate URL on form submission
                    form.addEventListener('submit', function(e) {
                        const scriptLocation = urlInput.value;

                        if (!urlPattern.test(scriptLocation)) {
                            e.preventDefault(); // Prevent form submission
                            errorElement.style.display = 'block';
                            errorElement.textContent =
                                'Please enter a valid URL before submitting.';
                        }
                    });
                }
            });
        });
    </script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Start Podcast confirmation
            const startPodcastButtons = document.querySelectorAll('.start-podcast-btn');

            startPodcastButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest(
                        '.start-podcast-form'); // Get the form associated with the button

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to start the podcast?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Start it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });

            // Handle Cancel Podcast confirmation
            const cancelPodcastButtons = document.querySelectorAll('.cancel-podcast-btn');

            cancelPodcastButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest(
                        '.cancel-podcast-form'); // Get the form associated with the button

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to cancel the podcast?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Cancel it!',
                        cancelButtonText: 'No, Keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });

            // Handle Complete Podcast confirmation
            const completePodcastButtons = document.querySelectorAll('.complete-podcast-btn');

            completePodcastButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest(
                        '.complete-podcast-form'); // Get the form associated with the button

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to complete the podcast?",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Complete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });
        });
    </script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Select all input elements with the class 'recording-date'
            const recordingDateInputs = document.querySelectorAll('.recording-date');

            // Set the value of each input field with today's date
            recordingDateInputs.forEach(input => {
                input.value = today;
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scriptEditorModal = document.getElementById('scriptEditorModal');
            const scriptEditorContent = document.getElementById('script-editor-content');

            document.querySelectorAll('.view-script-btn').forEach(button => {
                button.addEventListener('click', () => {
                    let scriptEditor = button.getAttribute('data-script-editor');

                    // Use a temporary div to parse and extract text content without HTML tags
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = scriptEditor;
                    scriptEditorContent.value = tempDiv.innerText
                .trim(); // Get clean text and set it
                });
            });
        });
    </script>
@endsection
