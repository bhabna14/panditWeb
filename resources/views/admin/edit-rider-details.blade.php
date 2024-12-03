@extends('admin.layouts.app')

@section('styles')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">EDIT RIDER</span>
    </div>
</div>

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

<form action="{{ route('admin.updateRiderDetails', $rider->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rider_name">Rider Name</label>
                                <input type="text" class="form-control" id="rider_name" name="rider_name"
                                    value="{{ old('rider_name', $rider->rider_name) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <input type="number" class="form-control" id="phone_number" name="phone_number"
                                    value="{{ $rider->phone_number }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rider_img">Rider Image</label>
                                <input type="file" class="form-control" id="rider_img" name="rider_img">
                                @if($rider->rider_img)
                                    <a class="btn btn-success" href="{{ Storage::url($rider->rider_img) }}" target="_blank">View Current Image</a>
                                @endif
                            </div>
                        </div>
                       
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description">{{ old('description', $rider->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Update">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    $('.select2').select2();
    setTimeout(function() {
        document.getElementById('Message').style.display = 'none';
    }, 3000);
</script>
@endsection
