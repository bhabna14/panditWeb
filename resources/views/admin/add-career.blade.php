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
                <div class="left-content">
                    <a href="{{ url('/admin/manage-pandits') }}" class=" btn btn-danger"><< Back</a>
                </div>
                <div class="main-content-body tab-pane border-top-0">
                    <h3 style='margin:25px 0'>Profile Information</h3>
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
                    <form action="{{ url('/admin/save-career') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" class="form-control"
                            id="career_id" name="career_id"
                            value="CAREER{{ rand(1000, 9999) }}" placeholder="">
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

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="upload_id">Upload</label>
                                        <input type="file" class="form-control" name="upload_id[]" id="upload_id" multiple>
                                    </div>
                                </div>

                                <div class="col-md-2" style="margin-top: 27px">
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

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="upload_edu">Upload</label>
                                        <input type="file" class="form-control" name="upload_edu[]" id="upload_edu" multiple>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 27px">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="upload_vedic">Upload</label>
                                        <input type="file" class="form-control" name="upload_vedic[]" id="upload_vedic" multiple>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 27px">
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
        <!-- Internal Data tables -->
        <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/js/table-data.js') }}"></script>
        <script src="{{ asset('assets/js/manage-profile.js') }}"></script>
        <script>
            document.getElementById('nextBtn').addEventListener('click', function() {
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
            });
        </script>
        <script>
            setTimeout(function() {
                document.getElementById('Message').style.display = 'none';
            }, 3000);
        </script>
        <!-- INTERNAL Select2 js -->
        <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    @endsection
    