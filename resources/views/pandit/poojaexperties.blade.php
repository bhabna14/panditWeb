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
<div class="row mb-5">

    <div class="col-lg-3 col-md-6 col-sm-12">

        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox1">
                        <img class="rounded-pill " src="{{ asset('assets/img/jagannath.jpeg') }}"
                            alt="Shree Jagannath">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Shree Jagannath
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox10">
                </div>
            </div>
        </div>
    </div>
    <!-- Repeat the structure for other images -->
    <!-- Shree Ram -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox2">
                        <img class="rounded-pill" src="{{ asset('assets/img/rams.jpeg') }}"
                            alt="Shree Ram">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Shree Ram
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox20">
                </div>
            </div>
        </div>
    </div>
    <!-- Hanuman -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox3">
                        <img class="rounded-pill" src="{{ asset('assets/img/hanuman1.jpeg') }}"
                            alt="Hanuman" style="height: 100px;width: 100px;">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Hanuman
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox30">
                </div>
            </div>
        </div>
    </div>
    <!-- Shree Krishna -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox4">
                        <img class="rounded-pill" src="{{ asset('assets/img/krishna1.jpeg') }}"
                            alt="Shree Krishna" style="height: 100px;width: 100px;">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Shree Krishna
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox4">
                </div>
            </div>
        </div>
    </div>
    <!-- Lord Shiv -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox5">
                        <img class="rounded-pill" src="{{ asset('assets/img/shiva.jpeg') }}"
                            alt="Lord Shiv">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Lord Shiv
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox5">
                </div>
            </div>
        </div>
    </div>
    <!-- Maa Mangala -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox6">
                        <img class="rounded-pill" src="{{ asset('assets/img/durga.jpeg') }}"
                            alt="Maa Mangala" style="height: 100px;width: 100px;">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Maa Durga
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox6">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox7">
                        <img class="rounded-pill" src="{{ asset('assets/img/saraswati.jpeg') }}"
                            alt="Maa Mangala" style="height: 100px;width: 100px;">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Maa Saraswati
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox70">
                </div>
            </div>
        </div>
    </div>
    <!-- Shree Ganesh -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
                    <!-- Wrap the image inside a label -->
                    <label for="checkbox8">
                        <img class="rounded-pill" src="{{ asset('assets/img/ganeshs.jpeg') }}"
                            alt="Shree Ganesh">
                    </label>
                </div>
                <div class="tx-16 text-center font-weight-semibold">
                    Shree Ganesh
                </div>
                <div class="form-check mt-3 text-center">
                    <input class="form-check-input checks" type="checkbox" id="checkbox80">
                </div>
            </div>
        </div>
    </div>
    <div class="text-center col-md-12">
        <button type="submit" class="btn btn-primary" style="width: 150px;">Update</button>
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
