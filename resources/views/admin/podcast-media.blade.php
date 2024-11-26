@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PODCAST MEDIA</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Media</li>
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
                           
                            <a class="nav-link mb-2 mt-2"
                                style="padding: 10px;"
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
                                <a class="nav-link mb-2 mt-2 bg-warning"
                                style="color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
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
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0 bg-info text-white">SlNo</th>
                                    <th class="border-bottom-0 bg-info text-white">Podcast Name</th>
                                    <th class="border-bottom-0 bg-info text-white">View Script</th>
                                    <th class="border-bottom-0 bg-info text-white">All Image File Path</th>
                                    <th class="border-bottom-0 bg-info text-white">All Video File Path</th>
                                    <th class="border-bottom-0 bg-info text-white">All Audio File Path</th>
                                    <th class="border-bottom-0 bg-info text-white">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($podcast_details as $index => $podcast)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $podcast->podcast_name }}</td>
                        
                                        <!-- View Script Button -->
                                        <td>
                                            <button type="button" class="btn btn-success view-script-btn"
                                                data-bs-toggle="modal" data-bs-target="#scriptEditorModal"
                                                data-script-editor="{{ $podcast->script_editor }}">
                                                Script
                                            </button>
                                        </td>
                        
                                        <form action="{{ route('updatePodcastMedia', ['podcast_id' => $podcast->podcast_id]) }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <td>
                                                <input type="text" class="form-control" id="podcast_image_path" name="podcast_image_path" placeholder="Enter Image Path" required onchange="validateUrl(this)">
                                            </td>
                                        
                                            <td>
                                                <input type="text" class="form-control" id="podcast_video_path" name="podcast_video_path" placeholder="Enter Video Path" required onchange="validateUrl(this)">
                                            </td>
                                        
                                            <td>
                                                <input type="text" class="form-control" id="podcast_audio_path" name="podcast_audio_path" placeholder="Enter Audio Path" required onchange="validateUrl(this)">
                                            </td>
                                        
                                            <td>
                                                <button type="submit" class="btn btn-dark">SAVE</button>
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
                        

                        <!-- Modal -->
                        <div class="modal fade" id="scriptEditorModal" tabindex="-1"
                            aria-labelledby="scriptEditorModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="scriptEditorModalLabel">Script Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
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
        radio.addEventListener('change', function () {
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
    function validateUrl(input) {
        // Regex for URL validation
        const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,})(\/[^\s]*)?$/;

        // Get the input value
        const url = input.value.trim();

        // Check if the URL is valid
        if (!urlPattern.test(url)) {
            alert('Invalid URL! Please enter a valid URL.');
            input.value = ''; // Clear the invalid value
            input.focus(); // Focus the field for correction
        }
    }
</script>

@endsection
