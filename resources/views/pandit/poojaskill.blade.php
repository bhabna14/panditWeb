@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PROFILE</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 full-width-tabs">
                            <a class="nav-link mb-2 mt-2 active"  href="{{url('pandit/poojaskill')}}" onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojadetails')}}" onclick="changeColor(this)">Add Details of Pooja</a>
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojaitemlist')}}"  onclick="changeColor(this)">Pooja Item List</a>
                            <a class="nav-link mb-2 mt-2" href="{{url('pandit/poojaarea')}}" onclick="changeColor(this)">Areas of Service</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="custom-card main-content-body-profile">
                <div class="tab-content">
                    <div class="main-content-body tab-pane border-top-0 active" id="poojaskill">
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
                        <form action="{{ url('/pandit/save-skillpooja') }}" method="post" enctype="multipart/form-data">
                            <div class="row mb-5">
                                @csrf
                                @foreach ($Poojanames as $pooja)
                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="card p-3">
                                            <div class="card-body">
                                                <div class="mb-3 text-center about-team">
                                                    <!-- Wrap the image inside a label -->
                                                    <label for="checkbox{{ $pooja->id }}">
                                                        <img style="border-radius: 10%"
                                                            src="{{ asset('assets/img/' . $pooja->pooja_photo) }}"
                                                            alt="{{ $pooja->pooja_name }}">
                                                    </label>
                                                </div>
                                                <div class="tx-16 text-center font-weight-semibold">
                                                    {{ $pooja->pooja_name }}
                                                </div>
                                                <div class="form-check mt-3 text-center">
                                                    <input style="width: 30px;height:30px" class="form-check-input checks" type="checkbox" id="checkbox{{ $pooja->id }}"
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
                                    <button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
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
