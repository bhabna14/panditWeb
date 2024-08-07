@extends('pandit.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="custom-card main-content-body-profile">
                <div class="tab-content">
                    <div class="main-content-body tab-pane active" id="poojaitemlist">
                        <div class="left-content m-2">
                            <a href="{{ url('/pandit/poojaitemlist') }}" class="btn btn-danger">
                                << Back</a>
                        </div>
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
                    
                        <div class="card">
                            <div class="card-body">
                                <div class="panel-group1" id="accordion11" role="tablist">
                                    <div class="card overflow-hidden">
                                        <a class="accordion-toggle panel-heading1" data-bs-toggle="collapse"
                                            data-bs-parent="#accordion11" href="#collapse{{ $poojaname->id }}"
                                            aria-expanded="true">{{ $poojaname->pooja_name }}</a>
                                        <div id="collapse{{ $poojaname->id }}" class="panel-collapse collapse show"
                                            role="tabpanel" aria-expanded="true">
                                            <div class="panel-body">
                                                <div class="table-responsive export-table">
                                                    @if ($errors->any())
                                                        <div class="alert alert-danger">
                                                            <ul>
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    <form action="{{ url('/pandit/save-poojaitemlist') }}" method="post"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="pooja_id"
                                                            value="{{ $poojaname->pooja_id }}">
                                                        <input type="hidden" name="pooja_name"
                                                            value="{{ $poojaname->pooja_name }}">

                                                        <table id="file-datatable-{{ $poojaname->id }}"
                                                            class="table table-bordered text-nowrap key-buttons border-bottom">
                                                            <thead>
                                                                <tr>
                                                                    <th class="border-bottom-0">Sl</th>
                                                                    <th class="border-bottom-0">Puja Name</th>
                                                                    <th class="border-bottom-0">Item Name</th>
                                                                    <th class="border-bottom-0">Variant</th>
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
                                                                        <select class="form-control select2" name="item_id[]" id="item_id" required>
                                                                            <option value="">Select Puja List</option>
                                                                            @foreach ($Poojaitemlist as $pujalist)
                                                                                <option value="{{ $pujalist->id }}" data-variants="{{ htmlspecialchars(json_encode($pujalist->variants), ENT_QUOTES, 'UTF-8') }}">
                                                                                    {{ $pujalist->item_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select2" name="variant_id[]" id="variant_id" required>
                                                                            <option value="">Select Variant</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-success add_item_btn"
                                                                            onclick="addPujaListSection({{ $poojaname->id }})">Add More</button>
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
    <!-- Internal Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-item.js') }}"></script>
    <script>
        var poojaItemList = @json($Poojaitemlist);
    </script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#item_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var variants = selectedOption.data('variants');
                var $variantSelect = $('#variant_id');

                // Clear previous options
                $variantSelect.empty();
                $variantSelect.append('<option value="">Select Variant</option>');

                if (variants) {
                    try {
                        // Ensure the data is a JSON string and decode HTML entities
                        if (typeof variants === 'string') {
                            variants = variants.replace(/&quot;/g, '"').replace(/&amp;/g, '&');
                            variants = JSON.parse(variants);
                        }

                        // Populate the variant dropdown
                        $.each(variants, function(index, variant) {
                            $variantSelect.append('<option value="' + variant.id + '">' + variant.title + ' - ' + variant.price + '</option>');
                        });
                    } catch (e) {
                        console.error('Error parsing variant data:', e);
                    }
                }
            });
        });
    </script>
@endsection
