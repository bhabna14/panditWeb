@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="row row-sm pt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive  export-table">
                    <h3>Manage Pooja Details</h3>
                    <hr>
                    <table id="file-datatable"
                        class="table table-bordered text-nowrap key-buttons border-bottom">
                        <thead>
                            <tr>
                                <th class="border-bottom-0">Sl No.</th>
        
                                <th class="border-bottom-0">Puja Name</th>
                                <th class="border-bottom-0">Fee</th>
                                <th class="border-bottom-0">Duration</th>
                                <th class="border-bottom-0">Image</th>
                                <th class="border-bottom-0">Video</th>
                                <th class="border-bottom-0">How Many Pooja you have done</th>
        
                            </tr>
                        </thead>
                        <tbody>
        
                            <tr>
                                <td>1</td>
                                <td class="tb-col">
                                    <div class="media-group">
                                        <div class="media media-md media-middle media-circle">
                                            <img src="{{ asset('assets/img/user.jpg') }}" alt="user">
        
                                        </div>
                                        <div class="media-text">
                                            <a href="" class="title">Ganesh Puja</a>
        
                                        </div>
                                    </div>
                                </td>
        
                                <td><input type="text" name="fee" value="5000" class="form-control"
                                        id=""></td>
                                <td><input type="text" name="duration" value="2-3Hr" class="form-control"
                                        id=""></td>
        
                                <td>
                                    <input type="file" name="fee" class="form-control"
                                        id="">
                                </td>
                                <td>
                                    <input type="file" name="fee" class="form-control"
                                        id="">
                                </td>
                                <td>
                                    <input type="text" name="fee" class="form-control"
                                       value="9" id="">
                                </td>
        
        
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="tb-col">
                                    <div class="media-group">
                                        <div class="media media-md media-middle media-circle">
                                            <img src="{{ asset('assets/img/user.jpg') }}" alt="user">
        
                                        </div>
                                        <div class="media-text">
                                            <a href="" class="title">Saraswati Puja</a>
        
                                        </div>
                                    </div>
                                </td>
        
                                <td><input type="text" name="fee" value="10000"  class="form-control"
                                        id=""></td>
                                <td><input type="text" name="duration" class="form-control"
                                        id=""></td>
        
                                <td>
                                    <input type="file" name="fee" class="form-control"
                                        id="">
                                </td>
                                <td>
                                    <input type="file" name="fee" class="form-control"
                                        id="">
                                </td>
                                <td>
                                    <input type="text" name="fee" class="form-control"
                                     value="20"   id="">
                                </td>
        
        
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="tb-col">
                                    <div class="media-group">
                                        <div class="media media-md media-middle media-circle">
                                            <img src="{{ asset('assets/img/user.jpg') }}" alt="user">
        
                                        </div>
                                        <div class="media-text">
                                            <a href="" class="title">Durga Puja</a>
        
                                        </div>
                                    </div>
                                </td>
        
                                <td><input type="text" name="fee" value="3000"  class="form-control"
                                        id=""></td>
                                <td><input type="text" name="duration" value="3-4Hr" class="form-control"
                                        id=""></td>
        
                                <td>
                                    <input type="file" name="fee" class="form-control"
                                        id="">
                                </td>
                                <td>
                                    <input type="file" name="fee" class="form-control"
                                        id="">
                                </td>
                                <td>
                                    <input type="text" name="fee" class="form-control" value="5"
                                        id="">
                                </td>
        
        
                            </tr>
        
        
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center col-md-12">
                    <button type="submit" class="btn btn-primary" style="width: 150px;">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <!-- Internal Select2 js-->


    <script>
        function addressFunction() {
            if (document.getElementById("same").checked) {
                document.getElementById("peraddress").value = document.getElementById("preaddress").value;
                document.getElementById("perpost").value = document.getElementById("prepost").value;
                document.getElementById("perdistri").value = document.getElementById("predistrict").value;
                document.getElementById("perstate").value = document.getElementById("prestate").value;
                document.getElementById("percountry").value = document.getElementById("precountry").value;
                document.getElementById("perpincode").value = document.getElementById("prepincode").value;
                document.getElementById("perlandmark").value = document.getElementById("prelandmark").value;

            } else {
                document.getElementById("peraddress").value = "";
                document.getElementById("perpost").value = "";
                document.getElementById("perdistri").value = "";
                document.getElementById("perstate").value = "";
                document.getElementById("percountry").value = "";
                document.getElementById("perpincode").value = "";
                document.getElementById("perlandmark").value = "";
            }
        }
    </script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>


    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
