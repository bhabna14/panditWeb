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
                <div class="main-content-body tab-pane  border-0" id="address">
                    <div class="card">
                      
                        <div class="border-0" data-select2-id="12">
                            <div style="border-bottom:1px solid red;text-align:center;margin: 20px">
                                <h3>ADDRESS INFORMATION</h3>
                            </div>
                            <form action="" method="post" enctype="multipart/form-data">
                                {{-- @csrf --}}
                                {{-- @method('PUT') --}}
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="card custom-card">
                                            <div class="card-body">
                                               
                                                <!-- <p class="mg-b-20">A form control layout using basic layout.</p> -->
                                                <div class="row">
                                                    <input type="hidden" class="form-control" id="exampleInputEmail1"
                                                        name="userid" value="" placeholder="Enter First Name">

                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="preaddress">Present Address</label>
                                                                    <input type="text" class="form-control"
                                                                        name="preaddress" value="" id="preaddress"
                                                                        placeholder="Enter Address">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="prepost">Post</label>
                                                                    <input type="text" class="form-control"
                                                                        name="prepost" value="" id="prepost"
                                                                        placeholder="Enter Post">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="predistrict">District</label>
                                                                    <input type="text" class="form-control"
                                                                        name="predistrict" value="" id="predistrict"
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
                                                                    <input type="text" class="form-control"
                                                                        name="prepincode" value="" id="prepincode"
                                                                        placeholder="Enter Pincode">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="prelandmark">Landmark</label>
                                                                    <input type="text" class="form-control"
                                                                        name="prelandmark" value="" id="prelandmark"
                                                                        placeholder="Enter Landmark">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <label class="ckbox"><input type="checkbox" id="same"
                                                                onchange="addressFunction()"> <span class="mg-b-10">Same
                                                                as Present Address</span></label>
                                                    </div>
                                                    <div class="col-md-6">

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="peraddress">Permanent Address</label>
                                                                    <input type="text" class="form-control"
                                                                        name="peraddress" value="" id="peraddress"
                                                                        placeholder="Enter Address">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="perpost">Post</label>
                                                                    <input type="text" class="form-control"
                                                                        name="perpost" value="" id="perpost"
                                                                        placeholder="Enter Post">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="perdistri">District</label>
                                                                    <input type="text" class="form-control"
                                                                        name="perdistri" value="" id="perdistri"
                                                                        placeholder="Enter District">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="perstate">State</label>
                                                                    <input type="text" class="form-control"
                                                                        name="perstate" value="" id="perstate"
                                                                        placeholder="Enter State">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="percountry">Country</label>
                                                                    <input type="text" class="form-control"
                                                                        name="percountry" value="" id="percountry"
                                                                        placeholder="Enter Country">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="perpincode">Pincode</label>
                                                                    <input type="text" class="form-control"
                                                                        name="perpincode" value="" id="perpincode"
                                                                        placeholder="Enter Pincode">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="perlandmark">Landmark</label>
                                                                    <input type="text" class="form-control"
                                                                        name="perlandmark" value=""
                                                                        id="perlandmark" placeholder="Enter Landmark">
                                                                </div>
                                                            </div>
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