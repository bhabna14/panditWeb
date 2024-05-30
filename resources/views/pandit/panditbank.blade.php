@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="row row-sm">
    <div class="col-lg-12 col-md-12 mt-4">
        <div class="custom-card main-content-body-profile">
            <div class="main-content-body tab-pane border-top-0" id="bank">
                <!-- row -->
                @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @if(session('success'))
                    <div style="color: green;">
                        {{ session('success') }}
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
                                <div style="border-bottom:1px solid red;text-align:center;margin: 20px">
                                    <h3>BANK INFORMATION</h3>
                                </div>
                                <div class="card-body">
                                   
                                    <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                    <div class="row">
                                       

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Bank Name</label>
                                                <input type="text" class="form-control" name="bankname"
                                                    value="{{$bankdata->bankname ?? ''}}" id="exampleInputEmail1"
                                                    placeholder="Enter Bank Name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Branch Name</label>
                                                <input type="text" class="form-control" name="branchname"
                                                    value="{{$bankdata->branchname ?? ''}}" id="exampleInputPassword1"
                                                    placeholder="Enter Branch Name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">IFSC Code</label>
                                                <input type="text" class="form-control" name="ifsccode"
                                                    value="{{$bankdata->ifsccode ?? ''}}" id="exampleInputPassword1"
                                                    placeholder="Enter IFSC Code">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Account Holder Name</label>
                                                <input type="text" class="form-control" name="accname"
                                                    value="{{$bankdata->accname ?? ''}}" id="exampleInputEmail1"
                                                    placeholder="Enter Account Holder Name">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Account Number</label>
                                                <input type="text" class="form-control" name="accnumber"
                                                    value="{{$bankdata->accnumber ?? ''}}" id="exampleInputPassword1"
                                                    placeholder="Enter Account Number">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">UPI Number/ID</label>
                                                <input type="text" class="form-control" name="upi_number"
                                                    value="{{$bankdata->upi_number ?? ''}}" id="exampleInputPassword1"
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
    <script>
        function addressFunction() {
            if (document.getElementById("same").checked) {
                document.getElementById("peraddress").value = document.getElementById("preaddress").value;
                document.getElementById("perpost").value = document.getElementById("prepost").value;
                document.getElementById("perdistri").value = document.getElementById("predistrict").value;
                document.getElementById("perstate").value = document.getElementById("prestate").value;
                document.getElementById("percountry").value = document.getElementById("precountry").value;
                document.getElementById("perpincode").value = document.getElementById("prepincode").value;
                document.getElementById("perlandmark").value = document.getElementById("prelandmark").value;

            } else {
                document.getElementById("peraddress").value = "";
                document.getElementById("perpost").value = "";
                document.getElementById("perdistri").value = "";
                document.getElementById("perstate").value = "";
                document.getElementById("percountry").value = "";
                document.getElementById("perpincode").value = "";
                document.getElementById("perlandmark").value = "";
            }
        }
    </script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>


    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection