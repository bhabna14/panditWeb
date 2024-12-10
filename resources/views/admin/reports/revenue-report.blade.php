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
            <span class="main-content-title mg-b-0 mg-b-lg-1">Flower Pickup Report</span>
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
            <form action="{{ route('admin.filterRevenueReport') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- From Date -->
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ old('from_date') }}" required>
                        </div>
                    </div>

                    <!-- To Date -->
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ old('to_date') }}" required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>

    <!-- Report Table -->
 <!-- Table to Display Results -->
@if (!empty($orders))
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <h5 class="mb-3">Revenue Details</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User Name</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ $order->user->mobile_number }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>₹ {{ number_format($order->flowerPayments->sum('paid_amount'), 2)  }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No orders found for the selected date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Grand Total</th>
                            <th>Total Revenue: ₹{{ number_format($totalRevenue, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Total Revenue -->
                {{-- <div class="mt-3">
                    <h5>Total Revenue: ₹{{ number_format($totalRevenue, 2) }}</h5>
                </div> --}}
            </div>
        </div>
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
