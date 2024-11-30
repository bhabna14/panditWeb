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
            <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE VENDORS DETAILS</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.addVendorDetails') }}" class="btn btn-info text-white">Add Vendor</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Temple</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vendor</li>
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
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sl No.</th>
                                    <th class="border-bottom-0">Vendor Name</th>
                                    <th class="border-bottom-0">Phone No</th>
                                    <th class="border-bottom-0">Email</th>
                                    <th class="border-bottom-0">Categories</th>
                                    <th class="border-bottom-0">Payment Type</th>
                                    <th class="border-bottom-0">Gst</th>
                                    <th class="border-bottom-0">Vendor Address</th>
                                    <th class="border-bottom-0">Bank Details</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendor_details as $index => $vendor)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $vendor->vendor_name }}</td>
                                        <td>{{ $vendor->phone_no }}</td>
                                        <td>{{ $vendor->email_id }}</td>
                                        <td>{{ $vendor->vendor_category }}</td>
                                        <td>{{ $vendor->payment_type }}</td>
                                        <td>{{ $vendor->vendor_gst }}</td>
                                        <td>{{ $vendor->vendor_address }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" 
                                            data-bs-target="#vendorModal" 
                                            data-vendor="{{ $vendor }}">
                                            View Banks
                                        </button>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.deletevendor', $vendor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                @csrf
                                                <button type="submit" class="btn btn-md btn-danger"><i class="fa fa-trash"></i></button>
                                                <a href="{{ url('admin/edit-vendor-details', $vendor->id) }}" class="btn btn-md btn-primary"><i class="fa fa-edit"></i></a>
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

    <!-- Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vendorModalLabel">Vendor Bank Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(!empty($vendor->vendorBanks) && $vendor->vendorBanks->count())
                      
                        <table class="table table-bordered" id="bank-details-table">
                            <thead>
                                <tr>
                                    <th>Bank Name</th>
                                    <th>Account Number</th>
                                    <th>IFSC Code</th>
                                    <th>Upi Id</th>

                                </tr>
                            </thead>
                            <tbody id="bank-details-body">
                                @foreach($vendor->vendorBanks as $bank)
                                <tr>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->account_no }}</td>
                                <td>{{ $bank->ifsc_code }}</td>
                                <td>{{ $bank->upi_id }}</td>
                            </tr>
                                @endforeach


                            </tbody>
                        </table>
                    @else
                        <p>No bank details available for this vendor.</p>
                    @endif
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
