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
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojaskill')}}" onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2"  href="{{url('pandit/poojadetails')}}" onclick="changeColor(this)">Add Details of Puja</a>
                            <a class="nav-link mb-2 mt-2 active"  href="{{url('pandit/poojalist')}}"  onclick="changeColor(this)">Puja Item List</a>
                            <a class="nav-link mb-2 mt-2" href="{{url('pandit/poojaarea')}}" onclick="changeColor(this)">Areas of Service</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row -->
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
                  
    <div class="row mb-5">
        @foreach ($Poojaskills as $pooja)
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card p-3">
                <div class="card-body">
                    <div class="mb-3 text-center about-team">
                        <!-- Wrap the image inside a label -->
                        <label for="checkbox{{ $pooja->id }}">
                            <img class="rounded-pill" src="{{ asset('assets/img/' . $pooja->pooja_photo) }}" alt="{{ $pooja->pooja_name }}">
                        </label>
                    </div>
                    <div class="tx-16 text-center font-weight-semibold">
                        {{$pooja->pooja_name}}
                    </div>
                    <div class="form-check mt-3 text-center">
                        <a href="{{ url('pandit/poojaitem?pooja_id=' . $pooja->id) }}" class="btn btn-primary" data-toggle="tooltip" title="Add Pooja List">+</a>
                        <a href="{{ url('pandit/managepoojaitem?pooja_id=' . $pooja->pooja_id) }}" class="btn btn-success"  data-toggle="tooltip" title="Manage Pooja List"><i class="fas fa-eye"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
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
<script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
       </script>
    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
