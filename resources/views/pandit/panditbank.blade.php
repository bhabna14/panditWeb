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
        <span class="main-content-title mg-b-0 mg-b-lg-1">BANK INFORMATION</span>
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
                <!-- row -->
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
                <form action="{{ url('pandit/savebankdetails')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- @method('PUT') --}}
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                    <div class="row">
                                       
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="bankname">Bank Name</label>
                                                <input type="text" class="form-control" name="bankname"
                                                    value="{{$bankdata->bankname ?? ''}}" id="bankname"
                                                    placeholder="Enter Bank Name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="branchname">Branch Name</label>
                                                <input type="text" class="form-control" name="branchname"
                                                    value="{{$bankdata->branchname ?? ''}}" id="branchname"
                                                    placeholder="Enter Branch Name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="ifsccode">IFSC Code</label>
                                                <input type="text" class="form-control" name="ifsccode"
                                                       value="{{$bankdata->ifsccode ?? ''}}" id="ifsccode"
                                                       placeholder="Enter IFSC Code" maxlength="15" oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="accname">Account Holder Name</label>
                                                <input type="text" class="form-control" name="accname"
                                                    value="{{$bankdata->accname ?? ''}}" id="accname"
                                                    placeholder="Enter Account Holder Name">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="accnumber">Account Number</label>
                                                <input type="text" class="form-control" name="accnumber"
                                                       value="{{$bankdata->accnumber ?? ''}}" id="accnumber"
                                                       placeholder="Enter Account Number" maxlength="17" oninput="validateAccountNumber(this)">
                                                <small id="accNumberError" class="text-danger"></small>
                                            </div>
                                        </div>
                                        

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="upi_number">UPI Number/ID</label>
                                                <input type="text" class="form-control" name="upi_number"
                                                    value="{{$bankdata->upi_number ?? ''}}" id="upi_number"
                                                    placeholder="Enter Account Number">
                                            </div>
                                        </div>


                                    </div>
                                    <div class="text-center col-md-12">
                                        <button type="submit" class="btn btn-primary"
                                            style="width: 150px;">Submit</button>
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
    <!-- Internal Select2 js-->
   
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>

    <script>

        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection