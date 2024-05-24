@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="card p-3">
        <h4>MANAGE YOUR POOJA EXPERTIES</h4>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
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

@if ($errors->has('danger'))
    <div class="alert alert-danger" id="Message">
        {{ $errors->first('danger') }}
    </div>
@endif
    <form action="{{ url('pandit/update-skillpooja') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mb-5">
            @foreach ($Poojanames as $pooja)
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card p-3">
                        <div class="card-body">
                            <div class="mb-3 text-center about-team">
                                <label for="checkbox{{ $pooja->id }}">
                                    <img class="rounded-pill" src="{{ asset('assets/img/' . $pooja->pooja_photo) }}"
                                        alt="{{ $pooja->pooja_name }}">
                                </label>
                            </div>
                            <div class="tx-16 text-center font-weight-semibold">
                                {{ $pooja->pooja_name }}
                            </div>
                            <div class="form-check mt-3 text-center">
                                <input class="form-check-input checks" type="checkbox" id="checkbox{{ $pooja->id }}"
                                    name="poojas[{{ $pooja->id }}][id]" value="{{ $pooja->id }}"
                                    @if (in_array($pooja->id, $selectedPoojas)) checked @endif>
                                <input type="hidden" name="poojas[{{ $pooja->id }}][name]"
                                    value="{{ $pooja->pooja_name }}">
                                <input type="hidden" name="poojas[{{ $pooja->id }}][image]"
                                    value="{{ $pooja->pooja_photo }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="text-center col-md-12">
                <button type="submit" class="btn btn-primary" style="width: 150px;">Update</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Internal Select2 js-->


   
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
