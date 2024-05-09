@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')

<div class="row row-sm pt-4">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="card">
                    <div class="card-body p-2">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search ...">
                            <span class="input-group-append">
                                <button class="btn btn-primary" type="button">Search</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-xl-12 p-0">
    <div class="row">
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 alert">
            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/jagannath.jpeg') }}" alt="img"></a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i
                                                class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">Rath Pooja</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">1,967</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">(12-02-2001)</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -<span>3hr</span></h6>
                               
                            </div>
                        </div>
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
