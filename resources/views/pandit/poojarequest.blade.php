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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Pandits</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Pandits</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   

                  

                    <!-- Row -->
                    <div class="row row-sm">
                        <div class="col-lg-12">
                            <div class="card custom-card overflow-hidden">
                                <div class="card-body">
                                    <!-- <div>
                                        <h6 class="main-content-label mb-1">File export Datatables</h6>
                                        <p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
                                    </div> -->
                                    <div class="table-responsive  export-table">
                                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                                            <thead>
                                                <tr>
                                                <th class="border-bottom-0">Sl</th>
                                                    <th class="border-bottom-0">Name</th>
                                                    <th class="border-bottom-0">Mobile No.</th>
                                                    <th class="border-bottom-0">Adv. Price</th>
                                                    <th class="border-bottom-0">Location</th>
                                                    <th class="border-bottom-0">Date</th>
                                                    <th class="border-bottom-0">Time</th>
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                           
                                            
                                               
                                                <tr>
                                                    <td>1</td>
                                                    <a href="{{url('admin/pandit-profile/')}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/pandit-profile/')}}" class="title">K_I_A_H S_H_O</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>77499*****</td>
                                                   <td>500</td>
                                                   <td>Bhubaneswar</td>
                                                   <td>12-05-2005</td>
                                                   <td>02:30</td>
                                                    <td>
                                                            <span class="badge badge-success">Approved</span> 
                                                            <span class="badge badge-danger">Reject</span> 
                                                       
                                                    </td>
                                                    
                                                  
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <a href="{{url('admin/pandit-profile/')}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/pandit-profile/')}}" class="title">S_O_M P_H_O</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>89655*****</td>
                                                   <td>500</td>
                                                   <td>Khurda</td>
                                                   <td>12-05-2012</td>
                                                   <td>02:30</td>
                                                    <td>
                                                            <span class="badge badge-success">Approved</span> 
                                                            <span class="badge badge-danger">Reject</span> 
                                                       
                                                    </td>
                                                    
                                                  
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <a href="{{url('admin/pandit-profile/')}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/pandit-profile/')}}" class="title">J_H_D U_H_O</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>75866*****</td>
                                                   <td>100</td>
                                                   <td>Bhubaneswar</td>
                                                   <td>12-05-2005</td>
                                                   <td>02:30</td>
                                                    <td>
                                                            <span class="badge badge-success">Approved</span> 
                                                            <span class="badge badge-danger">Reject</span> 
                                                       
                                                    </td>
                                                    
                                                  
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <a href="{{url('admin/pandit-profile/')}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/pandit-profile/')}}" class="title">J_F_A_H S_J_O</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>99865*****</td>
                                                   <td>200</td>
                                                   <td>JAJPUR</td>
                                                   <td>12-05-2005</td>
                                                   <td>02:30</td>
                                                    <td>
                                                            <span class="badge badge-success">Approved</span> 
                                                            <span class="badge badge-danger">Reject</span> 
                                                       
                                                    </td>
                                                    
                                                  
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <a href="{{url('admin/pandit-profile/')}}" class="title">
                                                    <td class="tb-col">
                                                        <div class="media-group">
                                                            <div class="media media-md media-middle media-circle">
                                                                    <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                            </div>
                                                            <div class="media-text">
                                                                <a href="{{url('admin/pandit-profile/')}}" class="title">T_A_R I_H_O</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </a>
                                                    
                                                   <td>68984*****</td>
                                                   <td>4000</td>
                                                   <td>Bhubaneswar</td>
                                                   <td>12-05-2005</td>
                                                   <td>02:30</td>
                                                    <td>
                                                            <span class="badge badge-success">Approved</span> 
                                                            <span class="badge badge-danger">Reject</span> 
                                                       
                                                    </td>
                                                    
                                                  
                                                </tr>
                                             
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Row -->

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
