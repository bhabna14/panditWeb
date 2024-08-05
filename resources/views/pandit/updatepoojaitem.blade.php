@extends('pandit.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- Include Chosen CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <a href="{{ url('/pandit/poojaitemlist') }}" class=" btn btn-danger">
            << Back</a>
        <span class="main-content-title mg-b-0 mg-b-lg-1">UPDATE POOJA ITEM</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        </ol>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="custom-card main-content-body-profile">
            <div class="main-content-body tab-pane border-top-0" id="bank">
                <!-- Display validation errors and success messages -->
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

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

                <!-- Form for updating pooja item -->
                <form action="{{ route('updatePoojaItem', $poojaItem->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="item_id">Puja List</label>
                                                <select class="form-control chosen-select" name="item_id" id="item_id" required disabled>
                                                    <option value="">Select Puja List</option>
                                                    @foreach ($poojaItemLists as $list)
                                                        <option value="{{ $list->id }}" {{ $poojaItem->item_id == $list->id ? 'selected' : '' }}>
                                                            {{ $list->item_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="variant_id">Variant</label>
                                                <select class="form-control chosen-select" name="variant_id" id="variant_id" required>
                                                    <option value="">Select Variant</option>
                                                    @foreach ($variants as $variant)
                                                        <option value="{{ $variant->id }}" {{ $poojaItem->variant_id == $variant->id ? 'selected' : '' }}>
                                                            {{ $variant->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="text-center col-md-12">
                                            <button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Internal Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-item.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
