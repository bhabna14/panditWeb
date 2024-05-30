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
                   
                    <div class="panel-group1" id="accordion11" role="tablist">
                        <div class="card overflow-hidden">
                            <a class="accordion-toggle panel-heading1" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapse{{ $poojaname->id }}" aria-expanded="true">{{ $poojaname->pooja_name }}</a>
                            <div id="collapse{{ $poojaname->id }}" class="panel-collapse collapse show" role="tabpanel" aria-expanded="true">
                                <div class="panel-body">
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
                                    <div class="alert alert-success" id="Messages">
                                        {{ session()->get('success') }}
                                    </div>
                                    @endif
    
                                    @if ($errors->has('danger'))
                                    <div class="alert alert-danger" id="Messages">
                                        {{ $errors->first('danger') }}
                                    </div>
                                    @endif
                                    <form action="{{ url('/pandit/update-poojadetails') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="table-responsive  export-table">

                                            <table id="file-datatable"
                                                class="table table-bordered text-nowrap key-buttons border-bottom  table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">Slno</th>
                                                        <th class="border-bottom-0">Puja Name</th>
                                                        <th class="border-bottom-0">List Name</th>
                                                        <th class="border-bottom-0">Quantity</th>
                                                        <th class="border-bottom-0">Unit</th>
                                                        <th class="border-bottom-0">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($poojaItems as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $item->pooja_name }}</td>
                                                        <td>{{ $item->pooja_list }}</td>
                                                        <td>{{ $item->list_quantity }}</td>
                                                        <td>{{ $item->list_unit }}</td>
                                                        <td>
                                                            <a href="{{ url('/pandit/delete-poojaitem/' . $item->id) }}" class="btn btn-md btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
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
@endsection
@section('scripts')
    <!-- Internal Select2 js-->

    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
