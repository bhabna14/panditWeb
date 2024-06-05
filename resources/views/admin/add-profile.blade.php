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
                        <a href="{{ url('admin/add-panditCareer') }}" class="btn btn-warning">Add Career</a>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Marital
                                        Status</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="rdiobox"><input name="marital" type="radio" value="Married">
                                    <span>Married</span></label>
                            </div>
                            <div class="col-lg-4">
                                <label class="rdiobox"><input checked name="marital" type="radio" value="Unmarried">
                                    <span>Unmarried</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
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
    