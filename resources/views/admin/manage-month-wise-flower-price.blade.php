@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }
        .badge {
            font-size: 12px;
            padding: 5px 8px;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Flower Price</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.monthWiseFlowerPrice') }}" class="btn btn-info text-white">
                        + Add Flower Price
                    </a>
                </li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive export-table">
                <table id="flower-price-table" class="table table-bordered text-nowrap key-buttons border-bottom">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Flower</th>
                            <th>Date Range</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $index => $t)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $t->vendor?->vendor_name }}</td>
                                <td>{{ $t->product?->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ \Carbon\Carbon::parse($t->start_date)->format('d M Y') }} →
                                        {{ \Carbon\Carbon::parse($t->end_date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>{{ $t->quantity }}</td>
                                <td>{{ $t->unit?->unit_name }}</td>
                                <td><strong>₹{{ number_format($t->price_per_unit,2) }}</strong></td>
                                <td>
                                    <button class="btn btn-sm btn-warning">Edit</button>
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($transactions->isEmpty())
                    <div class="text-center text-muted py-3">No records found</div>
                @endif
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

    <script>
        $(function () {
            $('#flower-price-table').DataTable({
                pageLength: 10,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm btn-secondary' },
                    { extend: 'csv', className: 'btn btn-sm btn-success' },
                    { extend: 'excel', className: 'btn btn-sm btn-info' },
                    { extend: 'pdf', className: 'btn btn-sm btn-danger' },
                    { extend: 'print', className: 'btn btn-sm btn-primary' }
                ]
            });
        });
    </script>
@endsection
