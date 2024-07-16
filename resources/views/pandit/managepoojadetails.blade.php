@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE POOJA DETAILS</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        </ol>
    </div>
</div>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive  export-table">
                        <form action="{{ url('/pandit/update-poojadetails') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0">Sl No.</th>
                                        <th class="border-bottom-0">Puja Name</th>
                                        <th class="border-bottom-0">Fee</th>
                                        <th class="border-bottom-0">Duration</th>
                                        <th class="border-bottom-0">Upload Img</th>
                                        <th class="border-bottom-0">View</th>
                                        <th class="border-bottom-0">Upload Video</th>
                                        <th class="border-bottom-0">View</th>
                                        <th class="border-bottom-0">Completed Pooja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($poojaDetails as $index => $poojaDetail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="tb-col">
                                                <div class="media-group">
                                                    <div class="media-text">
                                                        <a href="#" class="title">{{ $poojaDetail->pooja_name }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="fee[{{ $poojaDetail->id }}]"
                                                    class="form-control"
                                                    value="{{ old('fee.' . $poojaDetail->id, $poojaDetail->pooja_fee) }}">
                                            </td>
                                            <td>
                                                <input type="text" name="duration[{{ $poojaDetail->id }}]"
                                                    class="form-control"
                                                    value="{{ old('duration.' . $poojaDetail->id, $poojaDetail->pooja_duration) }}">
                                            </td>
                                            <td>
                                                <input type="file" name="image[{{ $poojaDetail->id }}]"
                                                    class="form-control">
                                            </td>
                                            <td>
                                                <a href="{{ asset($poojaDetail->pooja_photo) }}" target="_blank"
                                                    class="btn btn-success">
                                                    View Image
                                                </a>
                                            </td>
                                            <td>
                                                <input type="file" name="video[{{ $poojaDetail->id }}]"
                                                    class="form-control">
                                            </td>
                                            <td>
                                                @if ($poojaDetail->pooja_video)
                                                    <a href="{{ asset($poojaDetail->pooja_video) }}" target="_blank"
                                                        class="btn btn-danger">Watch Video</a>
                                                @else
                                                    No video uploaded
                                                @endif
                                            </td>
                                            <td>
                                                <input type="text" name="done_count[{{ $poojaDetail->id }}]"
                                                    class="form-control"
                                                    value="{{ old('done_count.' . $poojaDetail->id, $poojaDetail->pooja_done) }}">
                                            </td>

                                            <input type="hidden" name="pooja_id[{{ $poojaDetail->id }}]"
                                                value="{{ $poojaDetail->pooja_id }}">
                                            <input type="hidden" name="pooja_name[{{ $poojaDetail->id }}]"
                                                value="{{ $poojaDetail->pooja_name }}">
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center col-md-12">
                                <button type="submit" class="btn btn-primary" style="width: 150px;">Update</button>
                            </div>
                        </form>
                    </div>

                  
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>
    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
