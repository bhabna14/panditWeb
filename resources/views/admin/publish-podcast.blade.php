@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PUBLISH PODCAST</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Publish</li>
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
                            
                                <a class="nav-link mb-2 mt-2" style="padding: 10px;"
                                href="{{ url('admin/podcast-media') }}" onclick="setActive(this)">Podcast
                                Media</a>
                                <a class="nav-link mb-2 mt-2 bg-warning"
                                style="  color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/publish-podcast') }}" onclick="setActive(this)">Publish Podcast</a>
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
                                    <th class="border-bottom-0 bg-info text-white">Festival</th>
                                    <th class="border-bottom-0 bg-info text-white">Scripted By</th>
                                    <th class="border-bottom-0 bg-info text-white">Recorded By</th>
                                    <th class="border-bottom-0 bg-info text-white">Edited by</th>
                                    <th class="border-bottom-0 bg-info text-white">Edit Date</th>
                                    <th class="border-bottom-0 bg-info text-white">Podcast URL</th>
                                    <th class="border-bottom-0 bg-info text-white">Publish</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($podcast_details as $index => $podcast)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $podcast->podcast_name }}</td>
                                        <td>{{ $podcast->festival_name }}</td>
                                        <td>{{ $podcast->script_created_by }}</td>
                                        <td>{{ $podcast->podcast_recording_by }}</td>
                                        <td>{{ $podcast->audio_edited_by }}</td>
                                        <td>{{ $podcast->editing_date }}</td>
                                        <td>
                                            @if ($podcast->editing_complete_url)
                                                <a href="{{ $podcast->editing_complete_url }}" target="_blank"
                                                    class="btn btn-primary btn-sm text-white">
                                                    View Podcast
                                                </a>
                                            @else
                                                <span class="text-muted">Not Available</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($publishedPodcasts->contains($podcast->podcast_id))
                                                <button type="button" class="btn btn-success btn-sm" disabled>
                                                    Published
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-danger publish-btn"
                                                    data-bs-toggle="modal" data-bs-target="#publishModal"
                                                    data-podcast-id="{{ $podcast->podcast_id }}"
                                                    data-podcast-name="{{ $podcast->podcast_name }}"
                                                    data-deity="{{ $podcast->deity_category }}">
                                                    PUBLISH
                                                </button>
                                            @endif

                                        </td>
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

    <div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ url('admin/savePublishPodcast') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="publishModalLabel">Publish Podcast</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="podcast_id" id="podcast_id">
                        <div class="form-group mb-3">
                            <label for="modal_podcast_name" class="fw-bold">Podcast Name</label>
                            <input type="text" class="form-control" id="podcast_name" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="modal_deity" class="fw-bold">Deity</label>
                            <input type="text" class="form-control" id="deity_category" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="podcast_image" class="fw-bold">Podcast Image</label>
                            <input type="file" class="form-control" id="podcast_image" name="podcast_image" required>
                        </div>
                        <div class="form-group mb-3">
                            <label style="font-weight: 600" for="podcast_music">Podcast Music <span style="color: red" class="max-text">(maximum file size
                                    30mb)</span></label>
                            <input type="file" class="form-control" id="podcast_music" name="podcast_music" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="podcast_image" class="fw-bold">Podcast Video Url</label>
                            <input type="text" class="form-control" id="podcast_video_url" name="podcast_video_url" placeholder="Enter Video URL">
                        </div>
                        <div class="form-group mb-3">
                            <label for="publish_date" class="fw-bold">Publish Date</label>
                            <input type="date" class="form-control" id="publish_date" name="publish_date" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter Description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Publish</button>
                    </div>
                </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const publishButtons = document.querySelectorAll('.publish-btn');
            const modalPodcastId = document.getElementById('podcast_id');
            const modalPodcastName = document.getElementById('podcast_name');
            const modalDeity = document.getElementById('deity_category');

            publishButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const podcastId = this.getAttribute('data-podcast-id');
                    const podcastName = this.getAttribute('data-podcast-name');
                    const podcastDeity = this.getAttribute('data-deity');

                    // Populate modal fields
                    modalPodcastId.value = podcastId;
                    modalPodcastName.value = podcastName;
                    modalDeity.value = podcastDeity;
                });
            });
        });
    </script>
@endsection
