@extends('admin.layouts.app')

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
            <a href="{{ url('/admin/manage-pandits') }}" class=" btn btn-danger"><< Back</a>
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
                            <img class="br-5" alt="" src="{{ asset($pandit_profile->profile_photo) }}" alt="profile">
                                
                            <span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
                        </span>
                    </div>
                   
					<div class="my-md-auto mt-4 prof-details" >
                        <h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">
                           {{ $pandit_profile->title . ' ' . $pandit_profile->name }}</h4>
                        <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                            <span class="me-3"><i
                                    class="far fa-address-card me-2"></i>Pandit Id :{{ $pandit_profile->pandit_id }}</span>
                        </p>
                        <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                            <span class="me-3"><i
                                    class="far fa-address-card me-2"></i>Login Number :{{ $pandit_login_detail->mobile_no ?? "null" }}</span>
                        </p>
                        <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                            <span class="me-3"><i
                                    class="far fa-address-card me-2"></i>Whatsapp Number :{{ $pandit_profile->whatsappno }}</span>
                        </p>
                      
                    </div>
                </div>
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
                            <a class="nav-link  mb-2 mt-2 active" data-bs-toggle="tab" href="#profile">About</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#career">career</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#listpooja">List of Pooja</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#logindevice">Login Devices</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#bankdetails">Bank Details</a>

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
                    <div class="main-content-body tab-pane  active" id="profile">
                        <div class="card">
                            <div class="card-body border-0">
                                
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
                                <div class="mb-4 main-content-label">Personal Information</div>
                              
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Title</label>
                                            </div>
                                            <div class="col-md-9">
                                               {{ $pandit_profile->title }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Name</label>
                                            </div>
                                            <div class="col-md-9">
                                              {{ $pandit_profile->name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Email Address</label>
                                            </div>
                                            <div class="col-md-9">
                                               {{ $pandit_profile->email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Whatsapp Number</label>
                                            </div>
                                            <div class="col-md-9">
                                               {{ $pandit_profile->whatsappno }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3"> <label class="form-label" for="blood_group">Blood
                                                    Group</label>
                                            </div>
                                            <div class="col-md-9">
                                               {{ $pandit_profile->bloodgroup }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Marital Status</label>
                                            </div>
                                            <div class="col-md-3">
												{{$pandit_profile->maritalstatus}}
											</div>
                                        </div>
                                    </div>
                                  
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3"> <label class="form-label" for="blood_group">Choose
                                                    Language</label>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="col-md-9">
                                                   {{$pandit_profile->language}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  
                            </div>
                        </div>
                    </div>
                    <div class="main-content-body tab-pane border-top-0" id="career">
                        <div class="card">
                            <div class="card-body border-0">
                                <div class="mb-4 main-content-label">Personal Information</div>
                                <form class="form-horizontal">
                                    @foreach($pandit_careers as $pandit_career)
                                    <div class="form-group">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Qualification</label>
                                            </div>
                                            <div class="col-md-9">
                                                {{ $pandit_career->qualification }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Total Experience</label>
                                            </div>
                                            <div class="col-md-9">
                                                {{ $pandit_career->total_experience }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                    <div class="mb-4 main-content-label">Id Info</div>
                                    
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table mg-b-0 text-md-nowrap">
                                                <thead>
                                                    <tr style="background-color: #b30000;color:white">
                                                        <th style='text-align: center'>SlNo.</th>
                                                        <th style='text-align: center'>Id Type</th>
                                                        <th style='text-align: center'>Photo</th>
                                                        {{-- <th style='text-align: center'>Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pandit_idcards as $index => $pandit_idcard)
                                                    @if($pandit_idcard->upload_id != "")
                                                    <tr>
                                                        <td style='text-align: center;font-weight: bold'>{{ $index + 1 }}</td>
                                                        <td style='text-align: center;font-weight: bold'>{{$pandit_idcard->id_type}}</td>
                                                            <td style='text-align: center;font-weight: bold'>
                                                                <a href="{{ asset('uploads/id_proof/' . $pandit_idcard->upload_id) }}" target="_blank">
                                                                    <img src="{{ asset('uploads/id_proof/' . $pandit_idcard->upload_id) }}" style="width:80px; height:40px" alt="" />
                                                                </a>                                                            </td>
                                                        {{-- <td style='text-align: center;font-weight: bold'>
                                                            <a  style="font-size: 25px;color: red" href="{{url('/admin/deletIdproofs/'.$pandit_idcard->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td> --}}
                                                    </tr>

                                                    @else
                                                    nothing
                                                    @endif
     
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="mb-4 main-content-label">Vedic Info</div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table mg-b-0 text-md-nowrap">
                                                <thead>
                                                    <tr style="background-color: #b30000;color:white">
                                                        <th style='text-align: center'>SlNo.</th>
                                                        <th style='text-align: center'>Id Type</th>
                                                        <th style='text-align: center'>Photo</th>
                                                        {{-- <th style='text-align: center'>Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pandit_vedics as $index => $pandit_vedic)
                                                    @if($pandit_vedic->upload_vedic != "")
                                                    <tr>
                                                        <td style='text-align: center;font-weight: bold'>{{ $index + 1 }}</td>
                                                        <td style='text-align: center;font-weight: bold'>{{$pandit_vedic->vedic_type}}</td>
                                                            <td style='text-align: center;font-weight: bold'>
                                                                <a href="{{ asset('uploads/vedic_details/' . $pandit_vedic->upload_vedic) }}" target="_blank">
                                                                    <img src="{{ asset('uploads/vedic_details/' . $pandit_vedic->upload_vedic) }}" style="width:80px; height:40px" alt="" />
                                                                </a>                                                            </td>
                                                        {{-- <td style='text-align: center;font-weight: bold'>
                                                            <a  style="font-size: 25px;color: red" href="{{url('/admin/deletVedics/'.$pandit_vedic->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td> --}}
                                                    </tr>
                                                    @else
                                                    nothing
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="mb-4 main-content-label">Education Info</div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table mg-b-0 text-md-nowrap">
                                                <thead>
                                                    <tr style="background-color: #b30000;color:white">
                                                        <th style='text-align: center'>SlNo.</th>
                                                        <th style='text-align: center'>Education</th>
                                                        <th style='text-align: center'>Photo</th>
                                                        {{-- <th style='text-align: center'>Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pandit_educations as $index => $pandit_education)
                                                    @if($pandit_education->upload_education != "")
                                                    <tr>
                                                        <td style='text-align: center;font-weight: bold'>{{ $index + 1 }}</td>
                                                        <td style='text-align: center;font-weight: bold'>{{$pandit_education->education_type}}</td>
                                                            <td style='text-align: center;font-weight: bold'>
                                                                <a href="{{ asset('uploads/edu_details/' . $pandit_education->upload_education) }}" target="_blank">
                                                                    <img src="{{ asset('uploads/edu_details/' . $pandit_education->upload_education) }}" style="width:80px; height:40px" alt="" />
                                                                </a>                                                            </td>
                                                        {{-- <td style='text-align: center;font-weight: bold'>
                                                            <a  style="font-size: 25px;color: red" href="{{url('/admin/deletEducations/'.$pandit_education->id)}}" onclick="return confirm('Are you sure to delete?')" ><i class="fa fa-trash" aria-hidden="true"></i></a></td></td> --}}
                                                    </tr>

                                                    @else
                                                    nothing
                                                    @endif
     
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                    </div>
                    <div class="main-content-body  tab-pane border-top-0" id="listpooja">
                        <div class="border-0">
                            <div class="main-content-body main-content-body-profile">
                                <div class="main-profile-body p-0">
                                    <div class="row row-sm">
                                        <div class="col-12">
                                            <table id="file-datatable" class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">#</th>
                                                        <th class="border-bottom-0">Pooja Name</th>
                                                        <th class="border-bottom-0">Pooja Duration</th>
                                                        <th class="border-bottom-0">Pooja Fee</th>
                                                        <th class="border-bottom-0">Pooja Done</th>
                                                        <th class="border-bottom-0">Pooja Photo</th>
                                                        <th class="border-bottom-0">Pooja Video</th>
                                                        <th class="border-bottom-0">List of Samagri</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pandit_pujas as $index => $pandit_puja)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{  $pandit_puja->poojalist->pooja_name }}</td>
                                                        <td>{{  $pandit_puja->pooja_duration}}</td>
                                                        <td>{{ $pandit_puja->pooja_fee}}</td>
                                                        <td>{{  $pandit_puja->pooja_done}}</td>
                                                        <td>{{ $pandit_puja->pooja_photo ? $pandit_puja->pooja_photo : 'No photo' }}</td>
                                                        <td>{{ $pandit_puja->pooja_video ? $pandit_puja->pooja_video : 'No video' }}</td>
                                                        <td>
                                                            @php
                                                                $pooja_samagri = $samagri_items->where('pooja_id', $pandit_puja->poojalist->id);
                                                            @endphp
                                                            @if($pooja_samagri->isNotEmpty())
                                                                <ul>
                                                                    @foreach($pooja_samagri as $item)
                                                                        <li>{{ $item->item->item_name }} ({{ $item->variant->title }}) = {{ $item->variant->price }} </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                No samagri available
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-profile-body -->
                            </div>
                        </div>
                    </div>
                    <div class="main-content-body  tab-pane border-top-0" id="logindevice">
                        <div class="border-0">
                            <div class="main-content-body main-content-body-profile">
                                <div class="main-profile-body p-0">
                                    <div class="row row-sm">
                                        <div class="col-12">
                                            <table id="file-datatable" class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">#</th>
                                                        <th class="border-bottom-0">Device Id</th>
                                                        <th class="border-bottom-0">Device Model</th>
                                                        <th class="border-bottom-0">Platform</th>
                                                        <th class="border-bottom-0">Login Time</th>
                                                      
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pandit_logins as $index => $pandit_login)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ Str::limit($pandit_login->device_id, 15, '...') }}</td>
                                                        <td>{{  $pandit_login->device_model }}</td>
                                                        <td>{{  $pandit_login->platform }}</td>
                                                        <td>{{  $pandit_login->created_at }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-profile-body -->
                            </div>
                        </div>
                    </div>
                    <div class="main-content-body  tab-pane border-top-0" id="bankdetails">
                        <div class="border-0">
                            <div class="main-content-body main-content-body-profile">
                                <div class="main-profile-body p-0">
                                    <div class="row row-sm">
                                        <div class="col-12">
                                            <table id="file-datatable" class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">#</th>
                                                        <th class="border-bottom-0">Bank Name</th>
                                                        <th class="border-bottom-0">Branch Name</th>
                                                        <th class="border-bottom-0">IFSC Code</th>
                                                        <th class="border-bottom-0">Holder Name</th>
                                                        <th class="border-bottom-0">Account Number</th>
                                                        <th class="border-bottom-0">UPI Number</th>
                                                      
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pandit_bankdetails as $index => $pandit_bankdetail)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                       
                                                        <td>{{  $pandit_bankdetail->bankname }}</td>
                                                        <td>{{  $pandit_bankdetail->branchname }}</td>
                                                        <td>{{  $pandit_bankdetail->ifsccode }}</td>
                                                        <td>{{  $pandit_bankdetail->accname }}</td>
                                                        <td>{{  $pandit_bankdetail->accnumber }}</td>
                                                        <td>{{  $pandit_bankdetail->upi_number }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-profile-body -->
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

        <!-- smart photo master js -->
        <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
        <script src="{{ asset('assets/js/gallery.js') }}"></script>
    @endsection
