@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
    <style>
        td {
    vertical-align: top; /* Ensures inputs and form elements are aligned to the top of the cell */
    padding: 10px; /* Adds padding for better spacing inside the table */
}

.input-group {
    display: flex;
    align-items: center;
}

.input-group-text {
    background-color: #f1f1f1; /* Adjust background color if necessary */
    border-right: 0; /* Ensure no border between input and icon */
}

.form-control {
    border-radius: 4px; /* Optional: Rounds the corners of the input fields */
    margin-bottom: 10px; /* Adds spacing between fields */
}

.d-flex {
    display: flex;
    flex-direction: column;
    gap: 10px; /* Adds spacing between stacked elements */
}

    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">SOCIAL MEDIA</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Social Media</li>
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
                            <a class="nav-link mb-2 mt-2 " style="padding: 10px;" href="{{ url('admin/podcast-script') }}"
                                onclick="setActive(this)">Script Of Podcast</a>

                            <a class="nav-link mb-2 mt-2" style="padding: 10px;"
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
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style="color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/social-media') }}" onclick="setActive(this)">Social Media</a>
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
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap border-bottom" id="input-fields">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0 bg-info text-white">SlNo</th>
                                        <th class="border-bottom-0 bg-info text-white">Podcast Name</th>
                                        <th class="border-bottom-0 bg-info text-white">View Image Path</th>
                                        <th class="border-bottom-0 bg-info text-white">View Video Path</th>
                                        <th class="border-bottom-0 bg-info text-white">View Audio Path</th>
                                        <th class="border-bottom-0 bg-info text-white">Final Edit Podcast</th>
                                        <th class="border-bottom-0 bg-info text-white">Completed Podcast</th>
                                        <th class="border-bottom-0 bg-info text-white">Youtube Post Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Instagram Post Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Facebook Post Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($podcast_details as $index => $podcast)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $podcast->podcast_name }}</td>
                                            <td>
                                                @if ($podcast->podcast_image_path)
                                                    <a href="{{ $podcast->podcast_image_path }}" target="_blank"
                                                        class="btn btn-danger btn-sm text-white">
                                                        Image Path
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not Available</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($podcast->podcast_video_path)
                                                    <a href="{{ $podcast->podcast_video_path }}" target="_blank"
                                                        class="btn btn-dark btn-sm text-white">
                                                        Video Path
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not Available</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($podcast->podcast_audio_path)
                                                    <a href="{{ $podcast->podcast_audio_path }}" target="_blank"
                                                        class="btn btn-secondary btn-sm text-white">
                                                        Audio Path
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not Available</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($podcast->editing_complete_url)
                                                    <a href="{{ $podcast->editing_complete_url }}" target="_blank"
                                                        class="btn btn-success btn-sm text-white">
                                                         Open Url
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not Available</span>
                                                @endif
                                            </td>
                                            <form action="{{ route('updatePodcastSocialMedia', $podcast->podcast_id) }}"
                                                method="post" enctype="multipart/form-data">
                                                @csrf
                                                <td>
                                                    <select class="form-control" id="final_podcast_type" name="final_podcast_type">
                                                        <option value="" disabled selected>Select Video Type</option>
                                                        <option value="full">Full Video</option>
                                                        <option value="short">Short Video</option>
                                                    </select>
                                                    
                                                    <input class="form-control mb-2" id="final_podcast_url" name="final_podcast_url" type="text"
                                                           placeholder="Final Podcast URL" onchange="validateURL(this)">
                                                   
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-start">
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                            </div>
                                                            <input class="form-control" id="youtube_post_date" name="youtube_post_date" type="date"
                                                                   placeholder="Select Date">
                                                        </div>
                                                        <input class="form-control" id="youtube_post_link" name="youtube_post_link" type="text"
                                                               placeholder="Youtube Post URL" onchange="validateURL(this)">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-start">
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                            </div>
                                                            <input class="form-control" id="instagram_post_date" name="instagram_post_date" type="date"
                                                                   placeholder="Select Date">
                                                        </div>
                                                        <input class="form-control" id="instagram_post_link" name="instagram_post_link" type="text"
                                                               placeholder="Instagram Post URL" onchange="validateURL(this)">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-start">
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                            </div>
                                                            <input class="form-control" id="facebook_post_date" name="facebook_post_date" type="date"
                                                                   placeholder="Select Date">
                                                        </div>
                                                        <input class="form-control" id="facebook_post_link" name="facebook_post_link" type="text"
                                                               placeholder="Facebook Post URL" onchange="validateURL(this)">
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <button type="submit" class="btn btn-warning btn-md">Save</button>
                                                </td>
                                            </form>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No podcasts available</td>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <script>
        // Show/Hide inputs and update hidden field dynamically
        document.querySelectorAll('.path-type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const pathId = this.getAttribute('data-path-id');
                const audioInput = document.getElementById(`audio-input-${pathId}`);
                const videoInput = document.getElementById(`video-input-${pathId}`);

                if (this.value === 'audio') {
                    audioInput.style.display = 'block';
                    videoInput.style.display = 'none';
                } else if (this.value === 'video') {
                    audioInput.style.display = 'none';
                    videoInput.style.display = 'block';
                }
            });
        });

        function updatePathLocation(inputElement, pathId) {
            const hiddenInput = document.getElementById(`path_location_url-${pathId}`);
            hiddenInput.value = inputElement.value;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in the format yyyy-mm-dd
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0]; // Formats date as yyyy-mm-dd

            // Set today's date to all the input fields
            document.getElementById('youtube_post_date').value = formattedDate;
            document.getElementById('instagram_post_date').value = formattedDate;
            document.getElementById('facebook_post_date').value = formattedDate;
        });
    </script>

    <script>
        function validateURL(input) {
            const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,})(\/\S*)?$/;
            const url = input.value;

            if (url && !urlPattern.test(url)) {
                alert("Invalid URL. Please enter a valid URL starting with http or https.");
                input.value = ''; // Clear the invalid input
                input.focus();
            }
        }
    </script>
@endsection
