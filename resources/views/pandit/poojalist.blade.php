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
                <h3>Manage Pooja List</h3>
                <hr>
                <div class="panel-group1" id="accordion11" role="tablist">
                    <div class="card overflow-hidden">
                        <a class="accordion-toggle panel-heading1 collapsed " data-bs-toggle="collapse"
                            data-bs-parent="#accordion11" href="#collapseFour1"
                            aria-expanded="false">Ganesh Pooja</a>
                        <div id="collapseFour1" class="panel-collapse collapse" role="tabpanel"
                            aria-expanded="false">
                            <div class="panel-body">
                                <div class="table-responsive  export-table">
                                    <table id="file-datatable"
                                        class="table table-bordered text-nowrap key-buttons border-bottom">
                                        <thead>
                                            <tr>
                                                <th class="border-bottom-0">#</th>
                                                <th class="border-bottom-0">Puja Name</th>
                                                <th class="border-bottom-0">List Name</th>
                                                <th class="border-bottom-0">Quantity</th>
                                                <th class="border-bottom-0">Unit</th>
                                                <th class="border-bottom-0">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="show_puja_item">
                                            <tr>
                                                <td>1</td>
                                                <td class="tb-col">
                                                    <div class="media-group">
                                                        <div
                                                            class="media media-md media-middle media-circle">
                                                            <img src="{{ asset('assets/img/user.jpg') }}"
                                                                alt="user">
                                                        </div>
                                                        <div class="media-text">
                                                            <a href="" class="title">Ganesh
                                                                Puja</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><select class="form-control" name="country"
                                                        id="country">
                                                        <option value=" ">Ghee
                                                        </option>
                                                        
                                                        {{-- @foreach ($PujaLists as $pujalist)
                                                            <option value="{{ $pujalist }}">
                                                                {{ $pujalist }}</option>
                                                        @endforeach --}}
                                                    </select></td>
                                                <td><input type="number" class="form-control"
                                                        name="quantity[]" value="1" id="quantity"
                                                        placeholder="Enter List Quatity"></td>
                                                <td>
                                                    <select class="form-control" id="weight_unit"
                                                        name="weight_unit">
                                                        <option value="kg">Kilogram (kg)</option>
                                                        <option value="g">Gram (g)</option>
                                                        <option value="mg">Milligram (mg)</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-success add_item_btn"
                                                        onclick="addPujaListSection()">Add More</button>
                                                </td>
                                            </tr>
                                            <tr class="remove_puja_item">
                                                <td colspan="2" class="tb-col">
                                                   
                                                </td>
                                                <td><select class="form-control" name="country"
                                                        id="country">
                                                        <option value=" ">Water
                                                        </option>
                                                        {{-- @foreach ($PujaLists as $pujalist)
                                                            <option value="{{ $pujalist }}">
                                                                {{ $pujalist }}</option>
                                                        @endforeach --}}
                                                    </select></td>
                                                <td><input type="number" class="form-control"
                                                        name="quantity[]" value="200" id="quantity"
                                                        placeholder="Enter List Quatity"></td>
                                                <td>
                                                    <select class="form-control" id="weight_unit"
                                                        name="weight_unit">
                                                        <option value="g">Gram (g)</option>
                                                        <option value="mg">Milligram (mg)</option>
                                                    </select>
                                                </td>
                                                
                                               <td><button type="button" class="btn btn-danger" onclick="removePujaListSection(this)">Remove</button></td>
                                               </tr>
                                               <tr class="remove_puja_item">
                                                <td colspan="2" class="tb-col">
                                                   
                                                </td>
                                                <td><select class="form-control" name="country"
                                                        id="country">
                                                        <option value=" ">Dhupa
                                                        </option>
                                                        {{-- @foreach ($PujaLists as $pujalist)
                                                            <option value="{{ $pujalist }}">
                                                                {{ $pujalist }}</option>
                                                        @endforeach --}}
                                                    </select></td>
                                                <td><input type="number" class="form-control"
                                                        name="quantity[]" value="1" id="quantity"
                                                        placeholder="Enter List Quatity"></td>
                                                <td>
                                                    <select class="form-control" id="weight_unit"
                                                        name="weight_unit">
                                                        <option value=" ">Select Unit</option>
                                                        <option value="kg">Kilogram (kg)</option>
                                                        <option value="g">Gram (g)</option>
                                                        <option value="mg">Milligram (mg)</option>
                                                    </select>
                                                </td>
                                                
                                               <td><button type="button" class="btn btn-danger" onclick="removePujaListSection(this)">Remove</button></td>
                                               </tr>
                                               <tr class="remove_puja_item">
                                                <td colspan="2" class="tb-col">
                                                   
                                                </td>
                                                <td><select class="form-control" name="country"
                                                        id="country">
                                                        <option value=" ">Amba Katha
                                                        </option>
                                                        {{-- @foreach ($PujaLists as $pujalist)
                                                            <option value="{{ $pujalist }}">
                                                                {{ $pujalist }}</option>
                                                        @endforeach --}}
                                                    </select></td>
                                                <td><input type="number" class="form-control"
                                                        name="quantity[]" value="10" id="quantity"
                                                        placeholder="Enter List Quatity"></td>
                                                <td>
                                                    <select class="form-control" id="weight_unit"
                                                        name="weight_unit">
                                                        <option value=" ">Select Unit</option>
                                                        <option value="kg">Kilogram (kg)</option>
                                                        <option value="g">Gram (g)</option>
                                                        <option value="mg">Milligram (mg)</option>
                                                    </select>
                                                </td>
                                                
                                               <td><button type="button" class="btn btn-danger" onclick="removePujaListSection(this)">Remove</button></td>
                                               </tr>
                                               <tr class="remove_puja_item">
                                                <td colspan="2" class="tb-col">
                                                   
                                                </td>
                                                <td><select class="form-control" name="country"
                                                        id="country">
                                                        <option value=" ">Aguru
                                                        </option>
                                                        {{-- @foreach ($PujaLists as $pujalist)
                                                            <option value="{{ $pujalist }}">
                                                                {{ $pujalist }}</option>
                                                        @endforeach --}}
                                                    </select></td>
                                                <td><input type="number" class="form-control"
                                                        name="quantity[]" value="10" id="quantity"
                                                        placeholder="Enter List Quatity"></td>
                                                <td>
                                                    <select class="form-control" id="weight_unit"
                                                        name="weight_unit">
                                                     
                                                        <option value="mg">Milligram (mg)</option>
                                                    </select>
                                                </td>
                                                
                                               <td><button type="button" class="btn btn-danger" onclick="removePujaListSection(this)">Remove</button></td>
                                               </tr>
        
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center col-md-12">
                                    <button type="submit" class="btn btn-primary"
                                        style="width: 150px;">Update</button>
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
