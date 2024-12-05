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
                
                <li class="breadcrumb-item active" aria-current="page">Flower Pickup Report</li>
            </ol>
        </div>
    </div>

    <!-- Form for Date Range -->
    <div class="card custom-card">
        <div class="card-body">
            <form action="{{ route('admin.generateFlowerPickupReport') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="vendor_id">Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="rider_id">Rider</label>
                        <select name="rider_id" id="rider_id" class="form-control">
                            <option value="">All Riders</option>
                            @foreach($riders as $rider)
                                <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>

    <!-- Report Table -->
    @if(isset($reportData))
        <div class="card custom-card mt-4">
            <div class="card-body">
                <h5>Flower Pickup Report</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Pickup Date</th>
                            <th>Vendor Name</th>
                            <th>Rider Name</th>
                            <th>Flower Details</th>
                            <th>Status</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($reportData as $pickup)
                            @php $grandTotal += $pickup->total_price; @endphp
                            <tr>
                                <td>{{ $pickup->pickup_date }}</td>
                                <td>{{ $pickup->vendor->vendor_name }}</td>
                                <td>{{ $pickup->rider->rider_name }}</td>
                                <td>
                                    @foreach($pickup->flowerPickupItems as $item)
                                        <div>{{ $item->flower->name }} - {{ $item->quantity }} {{ $item->unit->name }} ({{$item->price}})</div>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($pickup->payment_status === 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>&#8377;{{ $pickup->total_price }}</td> <!-- For INR -->

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Grand Total</th>
                            <th>&#8377;{{ number_format($grandTotal, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
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
