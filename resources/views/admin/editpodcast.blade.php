@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">ADD PODCAST</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-podcast') }}" class="btn btn-warning text-dark">Manage Podcast</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">ADD PODCAST</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">
            {{ $errors->first('danger') }}
        </div>
    @endif

    <form action="{{ url('admin/updatepodcast/'.$podcast->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12 col-md-">
                <div class="card custom-card">
                    <div class="card-body">
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Podcast Name</label>
                                    <input type="text" value="{{$podcast->name}}" class="form-control" id="puja_name" name="name" placeholder="Enter Podcast Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="language">Language</label>
                                    <select class="form-control" id="language" name="language" required>
                                        <option value="odia" {{ $podcast->language == 'odia' ? 'selected' : '' }}>Odia</option>
                                        <option value="english" {{ $podcast->language == 'english' ? 'selected' : '' }}>English</option>
                                        <option value="hindi" {{ $podcast->language == 'hindi' ? 'selected' : '' }}>Hindi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Podcast Imgae</label>
                                    <input type="file" class="form-control" id="puja_img" name="image" >
                                    <img src="{{ asset('storage/' . $podcast->image) }}" alt="{{ $podcast->name }} Image" width="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Podcast Music</label>
                                    <input type="file" class="form-control" id="puja_img" name="music" >
                                    <audio controls>
                                        <source src="{{ asset('storage/' . $podcast->music) }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                        
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="special_niti">Description</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{ $podcast->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" style="padding-top: 27px">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('modal')
@endsection

@section('scripts')
    <!-- Form-layouts js -->
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
@endsection
