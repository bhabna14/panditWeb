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
        <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE ADDRESS</span>
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
                <div class="main-content-body tab-pane  border-0" id="address">
                    
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
                    <form action="{{url('pandit/saveaddress')}}" method="post" enctype="multipart/form-data">
                         @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                      
                                    <div class="border-0" data-select2-id="12">
                                           
                                            
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="card custom-card">
                                                        <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="preaddress">Present Address</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="preaddress" value="{{$addressdata->preaddress ?? ''}}" id="preaddress"
                                                                                    placeholder="Enter Address">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="prepost">Post</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="prepost" value="{{$addressdata->prepost ?? ''}}" id="prepost"
                                                                                    placeholder="Enter Post">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="predistrict">District</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="predistrict" value="{{$addressdata->predistrict ?? ''}}" id="predistrict"
                                                                                    placeholder="Enter District">
                                                                            </div>
                                                                        </div>
                                                                    </div>
            
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="prestate">State</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="prestate" value="Odisha" id="prestate"
                                                                                    placeholder="Enter State">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="precountry">Country</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="precountry" value="India" id="precountry"
                                                                                    placeholder="Enter Country">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="prepincode">Pincode</label>
                                                                                <input type="text" class="form-control" name="prepincode" value="{{$addressdata->prepincode ?? ''}}" id="prepincode" placeholder="Enter Pincode" maxlength="6" pattern="\d{6}">
                                                                                <small class="text-danger" id="pincode-error" style="display: none;">Pincode must be exactly 6 digits.</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="prelandmark">Landmark</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="prelandmark" value="{{$addressdata->prelandmark ?? ''}}" id="prelandmark"
                                                                                    placeholder="Enter Landmark">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                      
                                    <div class="border-0" data-select2-id="12">
                                            {{-- @csrf --}}
                                            
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="card custom-card">
                                                        <div class="card-body">
                                                            <label class="ckbox" style="margin-bottom: 13px;"><input type="checkbox" id="same"
                                                                onchange="addressFunction()"> <span class="mg-b-10">Same
                                                                as Present Address</span></label>

                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="peraddress">Permanent Address</label>
                                                                            <input type="text" class="form-control"
                                                                                name="peraddress" value="{{$addressdata->peraddress ?? ''}}" id="peraddress"
                                                                                placeholder="Enter Address">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="perpost">Post</label>
                                                                            <input type="text" class="form-control"
                                                                                name="perpost" value="{{$addressdata->perpost ?? ''}}" id="perpost"
                                                                                placeholder="Enter Post">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="perdistri">District</label>
                                                                            <input type="text" class="form-control"
                                                                                name="perdistri" value="{{$addressdata->perdistri ?? ''}}" id="perdistri"
                                                                                placeholder="Enter District">
                                                                        </div>
                                                                    </div>
                                                                </div>
        
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="perstate">State</label>
                                                                            <input type="text" class="form-control"
                                                                                name="perstate" value="{{$addressdata->perstate ?? ''}}" id="perstate"
                                                                                placeholder="Enter State">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="percountry">Country</label>
                                                                            <input type="text" class="form-control"
                                                                                name="percountry" value="{{$addressdata->percountry ?? ''}}" id="percountry"
                                                                                placeholder="Enter Country">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="perpincode">Pincode</label>
                                                                            <input type="text" class="form-control" name="perpincode" value="{{$addressdata->perpincode ?? ''}}" id="perpincode" placeholder="Enter Pincode" maxlength="6" pattern="\d{6}">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="perlandmark">Landmark</label>
                                                                            <input type="text" class="form-control"
                                                                                name="perlandmark" value="{{$addressdata->perlandmark ?? ''}}"
                                                                                id="perlandmark" placeholder="Enter Landmark">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center col-md-12">
                            <button type="submit" class="btn btn-primary"
                                style="width: 150px; margin-bottom:20px;">Submit</button>
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
    <script>
        setTimeout(function(){
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
