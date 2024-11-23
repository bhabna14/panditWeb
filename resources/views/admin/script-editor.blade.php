@extends('admin.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <a href="{{ route('podcastScript') }}" class="btn btn-danger" style="font-size: 20px"><--</a>
            <span class="main-content-title mg-b-0 mg-b-lg-1">SCRIPT EDITOR</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editor</li>
            </ol>
        </div>
    </div>

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
    <!-- Script Editor Form -->
    <form action="{{ route('saveScriptEditor', $podcast->podcast_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row row-sm">
            <div class="col-lg-12 col-md-12">
                <div class="form-group">
                    <label for="script_editor">Script Editor</label>
                    <textarea name="script_editor" class="form-control" id="script_editor" cols="30" rows="10">{{ old('script_editor', $podcast->script_editor ?? '') }}</textarea>
                    @error('script_editor')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Script</button>
    </form>
    
    
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
    <script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: '#script_editor',
            height: 300,
            plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks fullscreen link media template table charmap anchor',
            menubar: 'file edit view insert format tools',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | numlist bullist | fullscreen save print',
            content_css: '//www.tiny.cloud/css/codepen.min.css',
        });
    </script>
@endsection
