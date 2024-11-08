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
               
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-podcast') }}"
                        class="btn btn-warning text-dark">Manage Podcast</a></li>
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

    <form action="{{ url('admin/savepodcast') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12 col-md-">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="podcast">Podcast</label>
                                    <select class="form-control" id="podcast_id" name="podcast_id" >
                                        <option value="">Next Part Of Podcast</option>
                                        @foreach ($podcasts as $podcast)
                                            <option value="{{ $podcast->podcast_id }}">{{ $podcast->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Podcast Name</label>
                                    <input type="text" class="form-control" id="puja_name" name="name" placeholder="Enter Podcast Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="language">Language</label>
                                    <select class="form-control" id="language" name="language" required>
                                        <option value="odia">Odia</option>
                                        <option value="english">English</option>
                                        <option value="hindi">Hindi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Podcast Image</label>
                                    <input type="file" class="form-control" id="puja_img" name="image" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Podcast Music <span class="max-text">(maximum file size 30mb)</span></label>
                                    <input type="file" class="form-control" id="puja_music" name="music" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="special_niti">Description</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description" required></textarea>
                                </div>
                            </div>
                        </div>
    
                        <!-- New Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="podcast_category">Podcast Category</label>
                                    <select class="form-control" id="podcast_category" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="youtube_url">YouTube Video URL</label>
                                    <input type="text" class="form-control" id="youtube_url" name="youtube_url" placeholder="Enter YouTube Video URL">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="upload_date">Upload Date</label>
                                    <input type="date" class="form-control" id="upload_date" name="upload_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="publish_date">Publish Date</label>
                                    <input type="date" class="form-control" id="publish_date" name="publish_date" required>
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
