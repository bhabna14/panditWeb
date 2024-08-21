@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        /* Custom CSS can be added here */
    </style>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12 mt-4">
            <div class="custom-card main-content-body-profile">
                <div class="main-content-body tab-pane border-top-0">
                    <div style="display: flex;justify-content: space-between;">
                        <h3>PROFILE INFORMATION</h3>
                    </div>
                   <hr>

                    <!-- row -->
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
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
                    <form action="{{ url('/admin/save-profile') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" class="form-control" id="profile_id" name="profile_id"
                                value="PRF{{ rand(1000, 9999) }}" placeholder="">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Enter Title" required>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email
                                        address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Whatsapp
                                        Number</label>
                                    <input type="number" class="form-control" id="whatsappno" name="whatsappno"
                                        placeholder="Whatsapp Number" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="blood_group">Blood Group</label>
                                    <select class="form-control" id="bloodgroup" name="bloodgroup">
                                        <option value=" ">Select...</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Photo</label>
                                    <input type="file" name="profile_photo" class="form-control" id="profile_photo">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="language">Select Language</label>
                                    <select class="form-control select2" id="language" name="language[]"
                                        multiple="multiple" required>
                                        @foreach ($languages as $language)
                                            <option value="{{ $language }}">
                                                {{ $language }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Marital
                                            Status</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="rdiobox"><input name="marital" type="radio" value="Married">
                                        <span>Married</span></label>
                                </div>
                                <div class="col-lg-12">
                                    <label class="rdiobox"><input checked name="marital" type="radio" value="Unmarried">
                                        <span>Unmarried</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">About Pandit</label>
                                    <textarea name="about_pandit" class="form-control" id="" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                      
                      
                        <div style="display: flex;justify-content: space-between;">
                            <h4>CAREER INFORMATION</h4>
                        </div>
                       <hr>
                       <div class="row">
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qualification">Highest Qualification</label>
                                <input type="text" class="form-control" name="qualification" id="qualification" placeholder="Enter Heighest Qualification">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="experience">Total Experience</label>
                                <input type="number" class="form-control" name="experience" id="experience" placeholder="Total Experience">
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
                        <div class="text-center col-md-12">
                            <button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
@section('scripts')
<!-- Internal Select2 js-->

<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/add-profile.js') }}"></script>


<!-- smart photo master js -->
{{-- <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script> --}}
<script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
