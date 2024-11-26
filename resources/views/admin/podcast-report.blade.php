@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
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
                           
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-media') }}"
                                onclick="setActive(this)">Podcast
                                Media</a>
                                <a class="nav-link mb-2 mt-2 " style="padding: 10px" href="{{ url('admin/publish-podcast') }}"
                                onclick="setActive(this)">Publish Podcast</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/social-media') }}"
                                onclick="setActive(this)">Social Media</a>
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style="  color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-report') }}" onclick="setActive(this)">Report</a>
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
                                        <th class="border-bottom-0 bg-info text-white">Language</th>
                                        <th class="border-bottom-0 bg-info text-white">Deity</th>
                                        <th class="border-bottom-0 bg-info text-white">Script Verified Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Script Status</th>
                                        <th class="border-bottom-0 bg-info text-white">Recording Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Recording Status</th>
                                        <th class="border-bottom-0 bg-info text-white">Editing Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Editing Status</th>
                                        <th class="border-bottom-0 bg-info text-white">Publish Date</th>
                                        <th class="border-bottom-0 bg-info text-white">Podcast Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($podcast_details as $index => $podcast)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $podcast->podcast_name }}</td>
                                            <td>{{ $podcast->language }}</td>
                                            <td>{{ $podcast->deity_category }}</td>
                                            <td>{{ $podcast->script_verified_date }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge bg-primary p-2">{{ $podcast->podcast_script_status }}</span>
                                                    <button type="button" class="btn btn-success btn-sm script-details"
                                                        data-id="{{ $podcast->podcast_id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>{{ $podcast->recording_date }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge bg-success text-white p-2">{{ $podcast->podcast_recording_status }}</span>
                                                    <button type="button" class="btn btn-warning btn-sm recording-details"
                                                        data-id="{{ $podcast->podcast_id }}"><i
                                                            class="fa fa-eye"></i></button>
                                                </div>
                                            </td>
                                            <td>{{ $podcast->editing_date }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge bg-secondary p-2">{{ $podcast->podcast_editing_status }}</span>

                                                    <button type="button" class="btn btn-warning btn-sm editing-details"
                                                        data-id="{{ $podcast->podcast_id }}"><i
                                                            class="fa fa-eye"></i></button>
                                                </div>
                                            </td>
                                            <td>{{ $publish_data[$podcast->podcast_id] ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-dark p-2">{{ $podcast->podcast_status }}</span>
                                                    <button type="button" class="btn btn-primary btn-sm publish-details"
                                                        data-id="{{ $podcast->podcast_id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" class="text-center">No podcasts available</td>
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


    <!-- Script Details Modal -->
    <div class="modal fade" id="scriptDetailsModal" tabindex="-1" role="dialog" aria-labelledby="scriptDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="scriptDetailsLabel">Script Details</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Script Details Table -->
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th class="bg-light">Script Location</th>
                                <td>
                                    <a id="scriptLocation" href="" target="_blank"
                                        class="btn btn-primary btn-sm text-white">
                                        View Location
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <th class="bg-light">Story Source</th>
                                <td id="storySource"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Verified By</th>
                                <td id="scriptVerifiedBy"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Script Created By</th>
                                <td id="scriptCreatedBy"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Script Created Date</th>
                                <td id="scriptCreatedDate"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Verified Date</th>
                                <td id="scriptVerifiedDate"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Reject Reason</th>
                                <td id="scriptRejectReason"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Script Status</th>
                                <td id="scriptStatus"></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Script Editor Section -->
                    <div class="mt-4">
                        <h5 class="text-secondary">Script Details</h5>
                        <p id="scriptEditor" class="text-dark fw-bold"></p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- recording modal --}}
    <div class="modal fade" id="recordingDetailsModal" tabindex="-1" aria-labelledby="recordingDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="recordingDetailsLabel">Recording Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Recorded By</th>
                                <td id="podcastRecordingBy">N/A</td>
                            </tr>
                           

                            <tr>
                                <th>Recording Date</th>
                                <td id="recordingDate">N/A</td>
                            </tr>
                            <tr>
                                <th>Recording Complete URL</th>
                                <td>
                                    <a id="recordingCompleteUrl" href="#" target="_blank"
                                        class="btn btn-warning">Open URL</a>
                                </td>
                            </tr>
                            <tr>
                                <th>Recording Status</th>
                                <td id="podcastRecordingStatus">N/A</td>
                            </tr>

                            <tr>
                                <th colspan="2">PODCAST MEDIA</th>
                            </tr>

                            <tr>
                                <th>Image Path</th>
                                <td>
                                    <a id="podcastImagePath" href="#" target="_blank" class="btn btn-danger">View
                                        Image</a>
                                </td>
                            </tr>
                            <tr>
                                <th>Video Path</th>
                                <td>
                                    <a id="podcastVideoPath" href="#" target="_blank" class="btn btn-primary">View
                                        Video</a>
                                </td>
                            </tr>
                            <tr>
                                <th>Audio Path</th>
                                <td>
                                    <a id="podcastAudioPath" href="#" target="_blank" class="btn btn-dark">Listen
                                        Audio</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <!-- Modal Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Editing Modal --}}

    <div class="modal fade" id="editingDetailsModal" tabindex="-1" aria-labelledby="editingDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="editingDetailsLabel">Editing Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Editing Date</th>
                                <td id="editingDate">N/A</td>
                            </tr>
                            <tr>
                                <th>Music Source</th>
                                <td id="musicSource">N/A</td>
                            </tr>
                            <tr>
                                <th>Audio Edited By</th>
                                <td id="audioEditedBy">N/A</td>
                            </tr>
                            <tr>
                                <th>Editing Verified By</th>
                                <td id="editingVerifiedBy">N/A</td>
                            </tr>
                            <tr>
                                <th>Editing Verified Date</th>
                                <td id="editingVerifiedDate">N/A</td>
                            </tr>
                            <tr>
                                <th>Editing Status</th>
                                <td id="podcastEditingStatus">N/A</td>
                            </tr>
                            <tr>
                                <th>Editing Complete URL</th>
                                <td>
                                    <a id="editingCompleteUrl" href="#" target="_blank" class="btn btn-warning">Open URL</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
{{-- publish podcast modal --}}

<div class="modal fade" id="publishDetailsModal" tabindex="-1" aria-labelledby="publishDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="publishDetailsLabel">Publish Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Podcast Image</th>
                            <td>
                                <img id="podcastImage" src="" alt="Podcast Image" width="100">
                            </td>
                        </tr>
                        <tr>
                            <th>Podcast Music</th>
                            <td>
                                <audio id="podcastMusic" controls>
                                    <source src="" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>Podcast Video URL</th>
                            <td><a id="podcastVideoUrl" href="#" target="_blank"  class="btn btn-warning">Open URL</a></td>
                        </tr>
                        <tr>
                            <th>Publish Date</th>
                            <td id="publishDate">N/A</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td id="description">N/A</td>
                        </tr>
                        <tr>
                            <th>YouTube Post Date</th>
                            <td id="youtubePostDate">N/A</td>
                        </tr>
                        <tr>
                            <th>YouTube Post Link</th>
                            <td><a id="youtubePostLink" href="#" target="_blank"  class="btn btn-primary">Open URL</a></td>
                        </tr>
                        <tr>
                            <th>Facebook Post Date</th>
                            <td id="facebookPostDate">N/A</td>
                        </tr>
                        <tr>
                            <th>Facebook Post Link</th>
                            <td><a id="facebookPostLink" href="#" target="_blank"  class="btn btn-success">Open URL</a></td>
                        </tr>
                        <tr>
                            <th>Instagram Post Date</th>
                            <td id="instagramPostDate">N/A</td>
                        </tr>
                        <tr>
                            <th>Instagram Post Link</th>
                            <td><a id="instagramPostLink" href="#" target="_blank"  class="btn btn-danger">Open URL</a></td>
                        </tr>
                        <tr>
                            <th>Final Podcast Type</th>
                            <td id="finalPodcastType">N/A</td>
                        </tr>
                        <tr>
                            <th>Final Podcast URL</th>

                            <td><a id="finalPodcastUrl" href="#" target="_blank"  class="btn btn-info">Open URL</a></td>
                        </tr>
                        <tr>
                            <th>Podcast Status</th>
                            <td id="podcastStatus">N/A</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>

    {{-- script modal javascript code --}}
    <script>
        $(document).on('click', '.script-details', function() {
            const podcastId = $(this).data('id');

            $.ajax({
                url: '{{ route('admin.podcast.scriptDetails') }}',
                method: 'GET',
                data: {
                    podcast_id: podcastId
                },
                success: function(response) {
                    // Helper function to remove HTML tags
                    function stripHtmlTags(html) {
                        return html ? html.replace(/<[^>]+>/g, '') : 'N/A';
                    }

                    // Populate modal fields
                    const scriptLocation = response.script_location || '#';
                    $('#scriptLocation')
                        .attr('href', scriptLocation) // Set href dynamically
                        .text('View Location') // Update button text (optional)
                        .toggleClass('disabled', scriptLocation === '#'); // Disable if no URL

                    $('#storySource').text(response.story_source || 'N/A');
                    $('#scriptVerifiedBy').text(response.script_verified_by || 'N/A');
                    $('#scriptCreatedBy').text(response.script_created_by || 'N/A');
                    $('#scriptCreatedDate').text(response.script_created_date || 'N/A');
                    $('#scriptVerifiedDate').text(response.script_verified_date || 'N/A');
                    $('#scriptRejectReason').text(response.script_reject_reason || 'N/A');
                    $('#scriptEditor').text(stripHtmlTags(response
                    .script_editor)); // Remove <p> or other tags
                    $('#scriptStatus').text(response.podcast_script_status || 'N/A');

                    // Show modal
                    $('#scriptDetailsModal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch script details. Please try again.');
                }
            });
        });
    </script>
    {{-- recording modal --}}
    <script>
        $(document).on('click', '.recording-details', function() {
            const podcastId = $(this).data('id');

            $.ajax({
                url: '{{ route('admin.podcast.recordingDetails') }}', // Update this route as per your backend
                method: 'GET',
                data: {
                    podcast_id: podcastId
                },
                success: function(response) {
                    // Helper function to handle links
                    function setLinkOrDisable(id, link) {
                        if (link) {
                            $(id).attr('href', link).removeClass('disabled').text(
                            'View'); // Enable and update text
                        } else {
                            $(id).attr('href', '#').addClass('disabled').text(
                            'N/A'); // Disable if no URL
                        }
                    }

                    // Populate modal fields
                    $('#podcastRecordingBy').text(response.podcast_recording_by || 'N/A');
                    setLinkOrDisable('#podcastImagePath', response.podcast_image_path);
                    setLinkOrDisable('#podcastVideoPath', response.podcast_video_path);
                    setLinkOrDisable('#podcastAudioPath', response.podcast_audio_path);
                    setLinkOrDisable('#recordingCompleteUrl', response.recording_complete_url);
                    $('#recordingDate').text(response.recording_date || 'N/A');
                    $('#podcastRecordingStatus').text(response.podcast_recording_status || 'N/A');

                    // Show the modal
                    $('#recordingDetailsModal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch recording details. Please try again.');
                }
            });
        });
    </script>
    {{-- editing modal --}}
    <script>
 $(document).on('click', '.editing-details', function () {
    const podcastId = $(this).data('id');

    $.ajax({
        url: '{{ route("admin.podcast.editingDetails") }}', // Update this route as per your backend
        method: 'GET',
        data: { podcast_id: podcastId },
        success: function (response) {
            // Populate modal fields
            $('#editingDate').text(response.editing_date || 'N/A');
            $('#audioEditedBy').text(response.audio_edited_by || 'N/A');
            $('#editingVerifiedBy').text(response.editing_verified_by || 'N/A');
            $('#editingVerifiedDate').text(response.editing_verified_date || 'N/A');
            $('#podcastEditingStatus').text(response.podcast_editing_status || 'N/A');

            // Handle Music Source field
            const musicSourceContainer = $('#musicSource');
            musicSourceContainer.empty(); // Clear existing content before appending new links

            if (response.music_source) {
                const urls = response.music_source.split(','); // Split URLs by commas
                urls.forEach((url, index) => {
                    const trimmedUrl = url.trim(); // Remove extra spaces
                    if (trimmedUrl) {
                        musicSourceContainer.append(
                            `<a href="${trimmedUrl}" target="_blank" class="btn btn-dark btn-sm text-white mb-1">Music Source ${index + 1}</a><br>`
                        );
                    }
                });
            } else {
                musicSourceContainer.text('N/A');
            }

            // Handle Editing Complete URL field
            if (response.editing_complete_url) {
                $('#editingCompleteUrl')
                    .attr('href', response.editing_complete_url)
                    .removeClass('disabled')
                    .text('Open URL');
            } else {
                $('#editingCompleteUrl')
                    .attr('href', '#')
                    .addClass('disabled')
                    .text('N/A');
            }

            // Show the modal
            $('#editingDetailsModal').modal('show');
        },
        error: function () {
            alert('Failed to fetch editing details. Please try again.');
        }
    });
});

    </script>
    {{-- publish podcast  --}}
    <script>
    $(document).on('click', '.publish-details', function () {
        const podcastId = $(this).data('id');

        $.ajax({
            url: '{{ route("admin.podcast.publishDetails") }}',
            method: 'GET',
            data: { podcast_id: podcastId },
            success: function (response) {
                // Podcast Image
                if (response.podcast_image) {
                    $('#podcastImage')
                        .attr('src', `/storage/${response.podcast_image}`)
                        .attr('alt', 'Podcast Image');
                } else {
                    $('#podcastImage')
                        .attr('src', '')
                        .attr('alt', 'No Image Available');
                }

                // Set podcast music
                if (response.podcast_music) {
                    $('#podcastMusic source')
                        .attr('src', `/storage/${response.podcast_music}`);
                    $('#podcastMusic')[0].load(); // Reload the audio element
                } else {
                    $('#podcastMusic source')
                        .attr('src', '');
                    $('#podcastMusic')[0].load(); // Clear the audio element
                }

                // Podcast Video URL
                if (response.podcast_video_url) {
                    $('#podcastVideoUrl').attr('href', response.podcast_video_url)
                        .removeClass('disabled')
                        .attr('target', '_blank')
                        .text('Open URL');
                } else {
                    $('#podcastVideoUrl').attr('href', '#')
                        .addClass('disabled')
                        .text('N/A');
                }

                // Publish Date
                $('#publishDate').text(response.publish_date || 'N/A');

                // Description
                $('#description').text(response.description || 'N/A');

                // YouTube Post Date and Link
                $('#youtubePostDate').text(response.youtube_post_date || 'N/A');
                if (response.youtube_post_link) {
                    $('#youtubePostLink').attr('href', response.youtube_post_link)
                        .removeClass('disabled')
                        .attr('target', '_blank')
                        .text('Open URL');
                } else {
                    $('#youtubePostLink').attr('href', '#')
                        .addClass('disabled')
                        .text('N/A');
                }

                // Facebook Post Date and Link
                $('#facebookPostDate').text(response.facebook_post_date || 'N/A');
                if (response.facebook_post_link) {
                    $('#facebookPostLink').attr('href', response.facebook_post_link)
                        .removeClass('disabled')
                        .attr('target', '_blank')
                        .text('Open URL');
                } else {
                    $('#facebookPostLink').attr('href', '#')
                        .addClass('disabled')
                        .text('N/A');
                }

                // Instagram Post Date and Link
                $('#instagramPostDate').text(response.instagram_post_date || 'N/A');
                if (response.instagram_post_link) {
                    $('#instagramPostLink').attr('href', response.instagram_post_link)
                        .removeClass('disabled')
                        .attr('target', '_blank')
                        .text('Open URL');
                } else {
                    $('#instagramPostLink').attr('href', '#')
                        .addClass('disabled')
                        .text('N/A');
                }

                // Final Podcast Type
                $('#finalPodcastType').text(response.final_podcast_type || 'N/A');

                // Final Podcast URL
                if (response.final_podcast_url) {
                    $('#finalPodcastUrl').attr('href', response.final_podcast_url)
                        .removeClass('disabled')
                        .attr('target', '_blank')
                        .text('Open URL');
                } else {
                    $('#finalPodcastUrl').attr('href', '#')
                        .addClass('disabled')
                        .text('N/A');
                }

                // Podcast Status
                $('#podcastStatus').text(response.podcast_status || 'N/A');

                // Show the modal
                $('#publishDetailsModal').modal('show');
            },
            error: function () {
                alert('Failed to fetch podcast publish details.');
            }
        });
    });
    </script>

@endsection
