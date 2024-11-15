@extends('pandit.layouts.app')
@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE POOJA AREA</span>
    </div>
    <div class="justify-content-center">
       <a href="{{url('pandit/poojaarea')}}" class="btn btn-success">ADD AREA</a>
    </div>
</div>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

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

                    <div class="table-responsive  export-table">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sl No.</th>
                                    <th class="border-bottom-0">Country</th>
                                    <th class="border-bottom-0">State</th>
                                    <th class="border-bottom-0">District</th>
                                    <th class="border-bottom-0">City</th>
                                    <th class="border-bottom-0">Village</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($poojaAreas as $index => $poojaArea)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>INDIA</td>
                                    <td>{{ $poojaArea->stateName }}</td>
                                    <td>{{ $poojaArea->districtName }}</td>
                                    <td>{{ $poojaArea->subdistrictName }}</td>
                                    <td>{{ $poojaArea->villageNames }}</td>
                                    <td>
                                        <form action="{{ route('delete.poojaarea', $poojaArea->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-md btn-danger"><i class="fa fa-trash"></i></button>
                                            <a href="{{ route('edit.poojaarea', $poojaArea->id) }}" class="btn btn-md btn-primary"><i class="fa fa-edit"></i></a>
                                        </form>
                                       

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    <!-- smart photo master js -->
    <script>
        setTimeout(function(){
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
