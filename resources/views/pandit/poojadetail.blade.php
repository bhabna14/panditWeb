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
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaskill') }}"
                                onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2 active" href="{{ url('pandit/poojadetails') }}"
                                onclick="changeColor(this)">Add Details of Pooja</a>
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaitemlist') }}"
                                onclick="changeColor(this)">Pooja Item List</a>
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaarea') }}"
                                onclick="changeColor(this)">Areas of Service</a>
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
                    <div class="main-content-body tab-pane active" id="poojadetails">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive  export-table">
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
                                    <form action="{{ url('/pandit/save-poojadetails') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf

                                        @if ($Poojaskills->isEmpty())
                                            <div class="text-center">
                                                <h4>No Pooja list available.</h4>
                                            </div>
                                        @else
                                            <table id="file-datatable"
                                                class="table table-bordered text-nowrap key-buttons border-bottom">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">Slno</th>
                                                        <th class="border-bottom-0">Pooja Name</th>
                                                        <th class="border-bottom-0">Fee</th>
                                                        <th class="border-bottom-0">Duration</th>
                                                        <th class="border-bottom-0">Image</th>
                                                        <th class="border-bottom-0">Video</th>
                                                        <th class="border-bottom-0">How Many Pooja you have done</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($Poojaskills as $index => $poojaSkill)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td class="tb-col">
                                                            <div class="media-group">
                                                                <div class="media media-md media-middle media-circle">
                                                                    <img src="{{ asset('assets/img/' . $poojaSkill->pooja_photo) }}" alt="{{ $poojaSkill->pooja_name }}">
                                                                </div>
                                                                <div class="media-text">
                                                                    <a href="" class="title">{{ $poojaSkill->pooja_name }}</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input style="width: 100px" type="number" name="fee[{{ $poojaSkill->id }}]" class="form-control" value="{{ old('fee.' . $poojaSkill->id) }}" required pattern="[0-9]*" 
                                                            title="Only numbers are allowed">
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <input type="number" name="duration_value[{{ $poojaSkill->id }}]" class="form-control" value="{{ old('duration_value.' . $poojaSkill->id) }}" required>
                                                                <select class="form-control" name="duration_unit[{{ $poojaSkill->id }}]">
                                                                    <option value=" ">Select..</option>
                                                                    <option value="Day">Day</option>
                                                                    <option value="Hour">Hour</option>
                                                                    <option value="Minute">Minute</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="file" name="image[{{ $poojaSkill->id }}]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="file" name="video[{{ $poojaSkill->id }}]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="done_count[{{ $poojaSkill->id }}]" class="form-control" value="{{ old('done_count.' . $poojaSkill->id) }}">
                                                        </td>
                                                        <input type="hidden" name="pooja_id[{{ $poojaSkill->id }}]" value="{{ $poojaSkill->pooja_id }}">
                                                        <input type="hidden" name="pooja_name[{{ $poojaSkill->id }}]" value="{{ $poojaSkill->pooja_name }}">
                                                    </tr>
                                                    @endforeach
                                                    
                                                </tbody>
                                            </table>
                                            <div class="text-center col-md-12">
                                                <button type="submit" class="btn btn-primary"
                                                    style="width: 150px;">Submit</button>
                                            </div>
                                        @endif
                                    </form>

                                </div>
                            </div>
                        </div>
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
