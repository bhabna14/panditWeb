@extends('admin.layouts.app')

@section('styles')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
@php
    // Convert +91XXXXXXXXXX to 10 digit number for input
    $phoneDigits = preg_replace('/\D/', '', $rider->phone_number ?? '');
    $phone10 = substr($phoneDigits, -10);
@endphp

<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">EDIT RIDER</span>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
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

@if (session()->has('error'))
    <div class="alert alert-danger" id="Message">
        {{ session()->get('error') }}
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
                        <!-- Rider Name -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rider_name">Rider Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="rider_name"
                                       name="rider_name"
                                       value="{{ old('rider_name', $rider->rider_name) }}"
                                       required>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">+91</span>
                                    <input type="text"
                                           class="form-control"
                                           id="phone_number"
                                           name="phone_number"
                                           value="{{ old('phone_number', $phone10) }}"
                                           placeholder="10-digit number"
                                           maxlength="10"
                                           required>
                                </div>
                                <small class="text-muted">Only 10 digits (without +91).</small>
                            </div>
                        </div>

                        <!-- Salary -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="salary">Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number"
                                           class="form-control"
                                           id="salary"
                                           name="salary"
                                           value="{{ old('salary', $rider->salary) }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                </div>
                                <small class="text-muted">Monthly salary amount.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Rider Image -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rider_img">Rider Image</label>
                                <input type="file" class="form-control" id="rider_img" name="rider_img" accept="image/*">
                                @if($rider->rider_img)
                                    <div class="mt-2">
                                        <a class="btn btn-success btn-sm"
                                           href="{{ Storage::url($rider->rider_img) }}"
                                           target="_blank">
                                            View Current Image
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control"
                                          id="description"
                                          name="description"
                                          rows="3"
                                          placeholder="Optional notes...">{{ old('description', $rider->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Update">
                                <a href="{{ route('admin.manageRiderDetails') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>

                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    $('.select2').select2();

    setTimeout(function() {
        const m = document.getElementById('Message');
        if (m) m.style.display = 'none';
    }, 3000);

    // Force phone to 10 digits only
    const phone = document.getElementById('phone_number');
    if (phone) {
        phone.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }
</script>
@endsection
