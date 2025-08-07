@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Customize Order Report</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Report</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter -->
    <div class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label fw-semibold">From Date</label>
            <input type="date" id="from_date" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label fw-semibold">To Date</label>
            <input type="date" id="to_date" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button id="searchBtn" class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i> Search
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive export-table">
        <table id="file-datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Purchase Date</th>
                    <th>Delivery Date</th>
                    <th>Flower Items</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <!-- Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTable Script -->
<script>
    $(document).ready(function () {
        var table = $('#file-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('report.customize') }}",
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                { data: 'request_id', name: 'request_id' }, // real column
                {
                    data: 'purchase_date',
                    name: 'purchase_date',
                    orderable: false, // virtual column
                    searchable: false
                },
                {
                    data: 'delivery_date',
                    name: 'delivery_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'flower_items',
                    name: 'flower_items',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-capitalize',
                    orderable: false
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#searchBtn').click(function () {
            table.ajax.reload();
        });
    });
</script>

@endsection
