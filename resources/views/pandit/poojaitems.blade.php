@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- Include Chosen CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="custom-card main-content-body-profile">
            <div class="tab-content">
                <div class="main-content-body   tab-pane active" id="poojaitemlist">
                    <div class="card">
                        <div class="card-body">
                            <div class="panel-group1" id="accordion11" role="tablist">
                                <div class="card overflow-hidden">
                                    <a class="accordion-toggle panel-heading1" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapse{{ $poojaname->id }}" aria-expanded="true">{{ $poojaname->pooja_name }}</a>
                                    <div id="collapse{{ $poojaname->id }}" class="panel-collapse collapse show" role="tabpanel" aria-expanded="true">
                                        <div class="panel-body">
                                            <div class="table-responsive export-table">
                                                <form action="{{ url('/pandit/save-poojaitemlist') }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="pooja_id" value="{{ $poojaname->id }}">
                                                    <input type="hidden" name="pooja_name" value="{{ $poojaname->pooja_name }}">

                                                    <table id="file-datatable-{{ $poojaname->id }}" class="table table-bordered text-nowrap key-buttons border-bottom">
                                                        <thead>
                                                            <tr>
                                                                <th class="border-bottom-0">Sl</th>
                                                                <th class="border-bottom-0">Puja Name</th>
                                                                <th class="border-bottom-0">List Name</th>
                                                                <th class="border-bottom-0">Quantity</th>
                                                                <th class="border-bottom-0">Unit</th>
                                                                <th class="border-bottom-0">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="show_puja_item_{{ $poojaname->id }}">
                                                            <tr>
                                                                <td>1</td>
                                                                <td class="tb-col">
                                                                    <div class="media-group">
                                                                        <div class="media media-md media-middle media-circle">
                                                                            <img src="{{ asset('assets/img/' . $poojaname->pooja_photo) }}" alt="user">
                                                                        </div>
                                                                        <div class="media-text">
                                                                            <a href="" class="title">{{ $poojaname->pooja_name }}</a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control chosen-select" name="list_name[]" id="list_name" required>
                                                                        <option value="">Select Puja List</option>
                                                                        @foreach ($Poojaitemlist as $pujalist)
                                                                            <option value="{{ $pujalist }}">{{ $pujalist }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control" name="quantity[]" value="" id="quantity" placeholder="Enter List Quantity" required>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" id="weight_unit" name="unit[]" required>
                                                                        <option value="">Select Unit</option>
                                                                        <option value="kg">Kilogram (kg)</option>
                                                                        <option value="gm">Gram (gm)</option>
                                                                        <option value="mg">Milligram (mg)</option>
                                                                        <option value="mg">Piece (psc)</option>
                                                                        <option value="mg">Liter (ltr)</option>
                                                                        <option value="mg">Mili Liter (ml)</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-success add_item_btn" onclick="addPujaListSection({{ $poojaname->id }})">Add More</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="text-center col-md-12">
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

@endsection

@section('scripts')
    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-item.js') }}"></script>
    <script>
        var poojaItemList = @json($Poojaitemlist);
    </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
       

@endsection
