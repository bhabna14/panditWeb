@extends('admin.layouts.apps')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">ADD LOCALITY</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.managelocality') }}" class="btn btn-warning text-dark">Manage locality</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">ADD LOCALITY</li>
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

    <form action="{{ route('savelocality') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="locality_name">Locality Name</label>
                                    <input type="text" class="form-control" id="locality_name" name="locality_name"
                                        placeholder="Enter locality name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" 
                                        placeholder="Enter 6-digit pincode" pattern="\d{6}" title="Pincode must be a 6-digit number">
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-12">
                                <label>Apartment Names</label>
                                <div id="apartment-wrapper">
                                    <div class="d-flex align-items-center mb-3">
                                        <input type="text" class="form-control" name="apartment_name[]" 
                                            placeholder="Enter Apartment name" required>
                                        <button type="button" class="btn btn-success ms-2" id="add-apartment">
                                            Add
                                        </button>
                                    </div>
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
    <script>
        setTimeout(function(){
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script>
      document.getElementById('add-apartment').addEventListener('click', function () {
    const wrapper = document.getElementById('apartment-wrapper');
    const newField = document.createElement('div');
    newField.classList.add('d-flex', 'align-items-center', 'mb-3');
    newField.innerHTML = `
        <input type="text" class="form-control" name="apartment_name[]" placeholder="Enter Apartment name" required>
        <button type="button" class="btn btn-danger ms-2 remove-apartment">Remove</button>
    `;
    wrapper.appendChild(newField);
});

// Event delegation for removing fields
document.getElementById('apartment-wrapper').addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('remove-apartment')) {
        e.target.parentElement.remove();
    }
});

    </script>
@endsection
