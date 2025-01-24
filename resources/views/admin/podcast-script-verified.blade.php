@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">SCRIPT VERIFIED</span>
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
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/podcast-create') }}"
                                onclick="setActive(this)">Create Podcast</a>
                            <a class="nav-link mb-2 mt-2"  style="padding: 10px;" href="{{ url('admin/podcast-script') }}"
                                onclick="setActive(this)">Script</a>
                            <a class="nav-link mb-2 mt-2 bg-warning"
                                style="color: white;padding: 10px;box-shadow: 3px 3px 5px rgba(0,0,0,0.2);border-radius: 15px;"
                                href="{{ url('admin/podcast-script-verified') }}" onclick="setActive(this)">
                                Verification</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;"
                                href="{{ url('admin/podcast-recording') }}" onclick="setActive(this)">Recording</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-editing') }}"
                                onclick="setActive(this)">Editing</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-editing-verified') }}" onclick="setActive(this)">
                                Verified</a>
                            <a class="nav-link mb-2 mt-2" style="padding: 10px;" href="{{ url('admin/podcast-media') }}"
                                onclick="setActive(this)">
                                Creatives</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/publish-podcast') }}"
                                onclick="setActive(this)">Publish</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;" href="{{ url('admin/social-media') }}"
                                onclick="setActive(this)">Social Media</a>
                            <a class="nav-link mb-2 mt-2"  style="padding: 10px;" href="{{ url('admin/podcast-report') }}"
                                onclick="setActive(this)">Report</a>
                            <a class="nav-link mb-2 mt-2" style=" padding: 10px;"
                                href="{{ url('admin/podcast-planning') }}" onclick="setActive(this)">Planning</a>
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
                                    <th class="border-bottom-0 bg-info text-white">Script Location</th>
                                    <th class="border-bottom-0 bg-info text-white">Script Verified By</th>
                                    <th class="border-bottom-0 bg-info text-white">Script Verified Date</th>
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

                                        <td>
                                            @if ($podcast->script_location)
                                                <a href="{{ $podcast->script_location }}" target="_blank"
                                                    class="btn btn-primary btn-sm text-white">
                                                    File Location
                                                </a>
                                            @else
                                                <span class="text-muted">Not Available</span>
                                            @endif
                                        </td>
                                        <form action="{{ route('updateScriptVerified', $podcast->podcast_id) }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <td>
                                                <input type="text" class="form-control" id="script_verified_by"
                                                    name="script_verified_by" placeholder="Enter Verified By"
                                                    value="{{ $podcast->script_verified_by }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group me-2" style="flex: 1;">
                                                        <div class="input-group-text">
                                                            <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                        </div>
                                                        <input class="form-control" id="script_verified_date"
                                                            name="script_verified_date" type="date" readonly>
                                                    </div>
                                                    <button type="submit" class="btn btn-warning btn-md">Save</button>
                                                </div>
                                            </td>
                                        </form>

                                        <!-- Approve and Reject Buttons -->
                                        <td>
                                            <div
                                                style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                                                <!-- Approve Button -->
                                                <form action="{{ route('approvePodcastScript', $podcast->podcast_id) }}"
                                                    method="POST" class="approve-form">
                                                    @csrf
                                                    <button type="button" class="btn btn-success btn-md approve-btn">
                                                        <i class="icon ion-ios-checkmark-circle-outline"></i>
                                                    </button>
                                                </form>

                                                <!-- Reject Button -->
                                                <form action="{{ route('rejectPodcastScript', $podcast->podcast_id) }}"
                                                    method="POST" class="reject-form">
                                                    @csrf
                                                    <button type="button" class="btn btn-danger btn-md reject-btn">
                                                        <i class="icon ion-ios-close-circle"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No completed podcasts available for
                                            editing.</td>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Select all elements with the class 'script-created-date'
            document.querySelectorAll('#script_verified_date').forEach(input => {
                input.value = today;
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Approve button click
            document.querySelectorAll('.approve-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent form submission immediately

                    const form = this.closest('form'); // Get the closest form element

                    // Show SweetAlert2 confirmation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to approve this podcast script.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, approve it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });

            // Reject button click
            document.querySelectorAll('.reject-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent form submission immediately

                    const form = this.closest('form'); // Get the closest form element

                    // Show SweetAlert2 confirmation with input field
                    Swal.fire({
                        title: 'Reject Podcast Script',
                        text: "Please provide a reason for rejection.",
                        input: 'text',
                        inputPlaceholder: 'Enter rejection reason',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Reject',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        preConfirm: (reason) => {
                            if (!reason) {
                                Swal.showValidationMessage(
                                    'Rejection reason is required');
                            }
                            return reason;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Add the rejection reason to the form
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'script_reject_reason';
                            input.value = result.value; // Get the entered reason
                            form.appendChild(input);

                            form.submit(); // Submit the form with the reason
                        }
                    });
                });
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
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection
