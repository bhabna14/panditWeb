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
                <div class="card-body d-md-flex">
                    <div class="">
                        <span class="profile-image pos-relative">
                            <img class="br-5" alt=""
                                src="{{ asset($pandit_profile->profile_photo) }}" alt="profile">
                            <span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
                        </span>
                    </div>
                    <div class="my-md-auto mt-4 prof-details">
                        <h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">
                            {{ $pandit_profile->title . ' ' . $pandit_profile->name }}</h4>
                        <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                            <span class="me-3"><i
                                    class="far fa-address-card me-2"></i>{{ $pandit_profile->whatsappno }}</span>
                        </p>

                    </div>
                </div>
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
                            <a class="nav-link mb-2 mt-2" href="{{url('pandit/manageprofile')}}">Edit Profile</a>
                            <a class="nav-link mb-2 mt-2 active"  href="{{url('pandit/managecareer')}}">Edit Career</a>
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
                    
                    <div class="main-content-body tab-pane border-top-0 active" id="career">
                        <div class="card">
                            <div class="card-body border-0">
                                <div class="mb-4 main-content-label">Career Information</div>
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
                                <div class="alert alert-success" id="Messages">
                                    {{ session()->get('success') }}
                                </div>
                                @endif

                                @if ($errors->has('danger'))
                                <div class="alert alert-danger" id="Messages">
                                    {{ $errors->first('danger') }}
                                </div>
                                @endif
                                <form class="form-horizontal" action="{{ route('updateCareer', $pandit_career->id) }}" method="post" enctype="multipart/form-data">
                                    
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <input type="hidden" class="form-control"
                                                            id="career_id" name="career_id"
                                                            value="CAREER{{ rand(1000, 9999) }}" placeholder="">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Highest Qualification</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" name="qualification" value="{{  $pandit_career->qualification}}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Total Experience</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="number" class="form-control" name="experience" value="{{ $pandit_career->total_experience}}">
                                            </div>
                                        </div>
                                    </div>
                                        <div id="show_doc_item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="id_type">ID Proof</label>
                                                        <select name="id_type[]" class="form-control" id="id_type">
                                                            <option value=" ">Select...</option>
                                                            <option value="adhar">Adhar Card</option>
                                                            <option value="voter">Voter Card</option>
                                                            <option value="pan">Pan Card</option>
                                                            <option value="DL">DL</option>
                                                            <option value="health card">Health Card</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="upload_id">Upload</label>
                                                        <input type="file" class="form-control" name="upload_id[]" id="upload_id" multiple>
                                                    </div>
                                                </div>

                                               
                                                <div class="col-md-1" style="margin-top: 27px">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-success add_item_btn" onclick="addIdSection()">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style='border-bottom: 1px solid black;width: auto;text-align: center;margin:20px'>
                                            <h4 style="">MANAGE ID CARD</h4>
                                        </div>
                                        <div class="card-body">
                                           
                                            <div class="table-responsive">
                                                <table class="table mg-b-0 text-md-nowrap">
                                                    <thead>
                                                        <tr style="background-color: #b30000;color:white">
                                                            <th style='text-align: center'>SlNo.</th>
                                                            <th style='text-align: center'>Id Type</th>
                                                            <th style='text-align: center'>Photo</th>
                                                            <th style='text-align: center'>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pandit_idcards as $index => $pandit_idcard)
                                                        @if($pandit_idcard->upload_id != "")
                                                        <tr>
                                                            <td style='text-align: center;font-weight: bold'>{{ $index + 1 }}</td>
                                                            <td style='text-align: center;font-weight: bold'>{{$pandit_idcard->id_type}}</td>
                                                                <td style='text-align: center;font-weight: bold'>
                                                                <img src="{{ asset(asset('uploads/id_proof/' . $pandit_idcard->upload_id)) }}" style="width:80px; height:40px" alt="" />
                                                                </td>
                                                            <td style='text-align: center;font-weight: bold'>
                                                                <a  style="font-size: 25px;color: red" href="{{url('/pandit/deletIdproof/'.$pandit_idcard->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td>
                                                        </tr>

                                                        @else
                                                        nothing
                                                        @endif
         
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="show_edu_item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="education_type">Education</label>
                                                        <select name="education_type[]" class="form-control" id="education_type">
                                                            <option value=" ">Select..</option>
                                                            <option value="10th">10th</option>
                                                            <option value="+2">+2</option>
                                                            <option value="+3">+3</option>
                                                            <option value="Master Degree">Master Degree</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="upload_edu">Upload</label>
                                                        <input type="file" class="form-control" name="upload_edu[]" id="upload_edu" multiple>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="margin-top: 27px">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-success add_item_btn" onclick="addEduSection()">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style='border-bottom: 1px solid black;width: auto;text-align: center;margin:20px'>
                                            <h4 style="">MANAGE EDUCATION</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table mg-b-0 text-md-nowrap">
                                                    <thead>
                                                        <tr style="background-color: #b30000;color:white">
                                                            <th style='text-align: center'>SlNo.</th>
                                                            <th style='text-align: center'>Education</th>
                                                            <th style='text-align: center'>Photo</th>
                                                            <th style='text-align: center'>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pandit_educations as $index => $pandit_education)
                                                        @if($pandit_education->upload_education != "")
                                                        <tr>
                                                            <td style='text-align: center;font-weight: bold'>{{ $index + 1 }}</td>
                                                            <td style='text-align: center;font-weight: bold'>{{$pandit_education->education_type}}</td>
                                                                <td style='text-align: center;font-weight: bold'>
                                                                <img src="{{ asset(asset('uploads/edu_details/' . $pandit_education->upload_education)) }}" style="width:80px; height:40px" alt="" />
                                                                </td>
                                                            <td style='text-align: center;font-weight: bold'>
                                                                <a  style="font-size: 25px;color: red" href="{{url('/pandit/deletEducation/'.$pandit_education->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td>
                                                        </tr>

                                                        @else
                                                        nothing
                                                        @endif
         
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <div id="show_vedic_item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vedic_type">Vedic Type</label>
                                                        <input type="text" class="form-control" name="vedic_type[]" id="vedic_type" placeholder="Enter Vedic">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="upload_vedic">Upload</label>
                                                        <input type="file" class="form-control" name="upload_vedic[]" id="upload_vedic" multiple>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="margin-top: 27px">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-success add_item_btn" onclick="addVedicSection()">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style='border-bottom: 1px solid black;width: auto;text-align: center;margin:20px'>
                                            <h4 style="">MANAGE VEDIC DETAILS</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table mg-b-0 text-md-nowrap">
                                                    <thead>
                                                        <tr style="background-color: #b30000;color:white">
                                                            <th style='text-align: center'>SlNo.</th>
                                                            <th style='text-align: center'>Id Type</th>
                                                            <th style='text-align: center'>Photo</th>
                                                            <th style='text-align: center'>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pandit_vedics as $index => $pandit_vedic)
                                                        @if($pandit_vedic->upload_vedic != "")
                                                        <tr>
                                                            <td style='text-align: center;font-weight: bold'>{{ $index + 1 }}</td>
                                                            <td style='text-align: center;font-weight: bold'>{{$pandit_vedic->vedic_type}}</td>
                                                                <td style='text-align: center;font-weight: bold'>
                                                                <img src="{{ asset(asset('uploads/vedic_details/' . $pandit_vedic->upload_vedic)) }}" style="width:80px; height:40px" alt="" />
                                                                </td>
                                                            <td style='text-align: center;font-weight: bold'>
                                                                <a  style="font-size: 25px;color: red" href="{{url('/pandit/deletVedic/'.$pandit_vedic->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td>
                                                        </tr>
                                                        @else
                                                        nothing
                                                        @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <div class="text-center col-md-12">
                                        <button type="submit" class="btn btn-primary"
                                            style="width: 150px;">Update</button>
                                    </div>
                                </form>
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
    <script src="{{ asset('assets/js/pandit-career.js') }}"></script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>
    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
