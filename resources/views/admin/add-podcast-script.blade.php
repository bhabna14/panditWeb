@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        h3 {
    font-size: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 20px 0;
    padding: 10px;
    border-radius: 5px;
}
.table {
    border-collapse: collapse;
    width: 100%;
}
.table th, .table td {
    text-align: center;
    padding: 10px;
}
.table tbody tr:hover {
    background-color: #f1f1f1;
}
.btn {
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 4px;
}

    </style>
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
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-create') }}" onclick="setActive(this)">Create Podcast</a>
                                <a class="nav-link mb-2 mt-2 bg-warning"
                                style="  color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-script') }}" onclick="setActive(this)">Script</a>
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
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"  href="{{ url('admin/podcast-planning') }}"
                                onclick="setActive(this)">Planning</a>
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
                        <div class="accordion" id="podcastAccordion">
                            @forelse ($podcastDetails as $month => $podcasts)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"  style="background: linear-gradient(120deg, #87f0cf, #f866b8);" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
                                            {{ $month }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#podcastAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                               

                                                <table class="table table-bordered table-striped text-nowrap key-buttons border-bottom">
                                                    <thead class="bg-info text-dark">
                                                        <tr>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">SlNo</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Podcast Name</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Add Script</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Script File Location</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Script Create Date</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Source Of The Story</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Script Created By</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Save</th>
                                                            <th class="text-white" style="background-color: rgb(20,30,200)">Reject Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($podcasts as $index => $podcast)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    {{ $podcast->podcast_name }}
                                                                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $podcast->id }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('scriptEditor', ['podcast_id' => $podcast->podcast_id]) }}" class="btn btn-success" style="font-size: 16px">+</a>
                                                                </td>
                                                                <form action="{{ route('updatePodcastScript', $podcast->podcast_id) }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <td>
                                                                        <input type="text" class="form-control" name="script_location" placeholder="Enter Script File Location" value="{{ $podcast->script_location }}" required>
                                                                    </td>
                                                                    <td>
                                                                        {{ $podcast->podcast_create_date }}
                                                                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $podcast->id }}">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="story_source" placeholder="Enter Source Of The Story" value="{{ $podcast->story_source }}" required>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="script_created_by" placeholder="Enter Script Created By" value="{{ $podcast->script_created_by }}" required>
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
                                                
                                                            <!-- Modal for Editing Podcast Name and Script Create Date -->
                                                            <div class="modal fade" id="editModal{{ $podcast->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <form action="{{ route('updatePodcastDetails', $podcast->id) }}" method="POST">
                                                                            @csrf
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel">Edit Podcast Details</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="mb-3">
                                                                                    <label for="podcast_name" class="form-label">Podcast Name</label>
                                                                                    <input type="text" class="form-control" id="podcast_name" name="podcast_name" value="{{ $podcast->podcast_name }}" required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="podcast_create_date" class="form-label">Script Create Date</label>
                                                                                    <input type="date" class="form-control" id="podcast_create_date" name="podcast_create_date" value="{{ $podcast->podcast_create_date }}" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
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

    

    
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
@endsection
