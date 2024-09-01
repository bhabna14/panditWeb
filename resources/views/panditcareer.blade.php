@extends('pandit.layouts.custom-app')

@section('styles')
    <title>Pandit Login</title>
@endsection

@section('class')
    <div class="bg-primary">
@endsection

@section('content')
    <div class="page-single">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-8 col-xs-10 card-sigin-main mx-auto my-auto py-45 justify-content-center">
                    <div class="card-sigin mt-5 mt-md-0">
                        <div class="main-card-signin d-md-flex">
                            <div class="wd-100p">
                                <div class="d-flex mb-4">
                                    <a href="#"><img src="{{ asset('assets/img/brand/logo.png') }}" class="sign-favicon ht-40" alt="logo"></a>
                                </div>
                                <div class="">
                                    <div class="main-signup-header">
                                        <div class="panel panel-primary">
                                            <div style="text-align: center;">
                                                <h2>CAREER INFORMATION</h2>
                                            </div>
                                            <div class="panel-body tabs-menu-body border-0 p-3">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="career">
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

                                                        @if (session()->has('error'))
                                                            <div class="alert alert-danger" id="Message">
                                                                {{ session()->get('error') }}
                                                            </div>
                                                        @endif

                                                        <form action="{{ url('/pandit/save-career') }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" class="form-control" id="career_id" name="career_id" value="CAREER{{ rand(1000, 9999) }}" placeholder="">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="qualification">Highest Qualification <span style="color:red">*</span></label>
                                                                        <input type="text" class="form-control" name="qualification" id="qualification" placeholder="Enter Heighest Qualification" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="experience">Total Pooja Experience<span style="color:red">*</span></label>
                                                                        <input type="number" class="form-control" name="experience" id="experience" placeholder="Total Experience" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="show_doc_item" style="border-bottom: 1px solid black">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="id_type">ID Proof<span style="color:red">*</span></label>
                                                                            <select name="id_type[]" class="form-control" id="id_type" required>
                                                                                <option value=" ">Select...</option>
                                                                                <option value="adhar">Adhar Card</option>
                                                                                <option value="voter">Voter Card</option>
                                                                                <option value="pan">Pan Card</option>
                                                                                <option value="DL">DL</option>
                                                                                <option value="health_card">Health Card</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="upload_id">Upload(ID) <span style="color:red">*</span></label>
                                                                            <input type="file" class="form-control" name="upload_id[]" id="upload_id" multiple required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-2" style="margin-top: 27px">
                                                                        <div class="form-group">
                                                                            <button type="button" class="btn btn-success add_item_btn" onclick="addIdSection()">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="show_edu_item" style="border-bottom: 1px solid black">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="education_type">Education</label>
                                                                            <select name="education_type[]" class="form-control" id="education_type">
                                                                                <option value=" ">Select..</option>
                                                                                <option value="10th">10th</option>
                                                                                <option value="+2">+2</option>
                                                                                <option value="+3">+3</option>
                                                                                <option value="Master_Degree">Master Degree</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="upload_edu">Upload(Education)</label>
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
                                                            <div id="show_vedic_item" style="border-bottom: 1px solid black">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="vedic_type">Vedic Type</label>
                                                                            <input type="text" class="form-control" name="vedic_type[]" id="vedic_type" placeholder="Enter Vedic">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="upload_vedic">Upload(Vedic)</label>
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
                                                            <div class="text-center col-md-12 mt-4">
                                                                <button type="submit" class="btn btn-primary" style="width: 150px;">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/generate-otp.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-career.js') }}"></script>

    <script>
        document.getElementById('nextBtn').addEventListener('click', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        });

        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
@endsection
