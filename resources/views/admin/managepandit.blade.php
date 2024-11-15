@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        /* Custom CSS can be added here */
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Pandits</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ url('admin/add-panditProfile') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Pandits</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
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
                    <!-- <div>
                        <h6 class="main-content-label mb-1">File export Datatables</h6>
                        <p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
                    </div> -->
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Slno</th>
                                    <th class="border-bottom-0">Name</th>
                                    <th class="border-bottom-0">Registered Date</th>
                                    <th class="border-bottom-0">Mobile No</th>
                                    <th class="border-bottom-0">Blood Group</th>
                                    <th class="border-bottom-0">Application Status</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pandit_profiles as $index => $profile)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    
                                    <td class="tb-col">
                                        <div class="media-group">
                                            <div class="media media-md media-middle media-circle" >
                                                <img src="{{ asset( $profile->profile_photo) }}" alt="user">
                                            </div>
                                            <div class="media-text"  style="color: blue">
                                                <a  style="color: blue" href="{{ url('admin/pandit-profile/' . $profile->id) }}" class="title">{{ $profile->name }}</a>
                                                <span class="small text">{{$profile->email}}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($profile->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ $profile->whatsappno }}</td>
                                    <td>{{ $profile->bloodgroup }}</td>
                                    <td>{{ $profile->pandit_status }}</td>
                                    <td>
                                        @if($profile->pandit_status == 'accepted')
                                            <form action="{{ route('rejectPandit', $profile->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </form>
                                        @elseif($profile->pandit_status == 'rejected')
                                            <form action="{{ route('acceptPandit', $profile->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Accept</button>
                                            </form>
                                        @elseif($profile->pandit_status == 'pending')
                                            <form action="{{ route('acceptPandit', $profile->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Accept</button>
                                            </form>
                                            <form action="{{ route('rejectPandit', $profile->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->


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
    <script src="{{ asset('assets/js/manage-profile.js') }}"></script>
    <script>
        document.getElementById('nextBtn').addEventListener('click', function() {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        });
    </script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
@endsection
