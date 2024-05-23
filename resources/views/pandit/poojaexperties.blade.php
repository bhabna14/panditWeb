@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')


<div class="row mb-5">
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card p-3">
            <div class="card-body">
                <div class="mb-3 text-center about-team">
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
