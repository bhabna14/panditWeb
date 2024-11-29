@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE Flower Pickup Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.addflowerpickupdetails') }}" class="btn btn-info text-white">Add Flower Pickup Details</a></li>
                
                <li class="breadcrumb-item active" aria-current="page">Flower Pickup Details</li>
            </ol>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <div class="row">
                            @foreach ($pickupDetails as $date => $details)
                            <div class="card">
                                <div class="card-header">
                                    <h5>Pickup Date: {{ $date }}</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($details as $detail)
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <strong>Flower:</strong> {{ $detail->flower?->name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Unit:</strong> {{ $detail->unit?->name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Quantity:</strong> {{ $detail->quantity ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Vendor:</strong> {{ $detail->vendor?->vendor_name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Rider:</strong> {{ $detail->rider?->rider_name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Price:</strong> ₹{{ $detail->price ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Status:</strong> ₹{{ $detail->status ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>
@endsection
