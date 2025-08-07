@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Pick Up Report</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Report</a></li>
            </ol>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center py-2">
                    <h6 class="card-title text-success mb-1">Total Price</h6>
                    <h4 class="fw-bold mb-0" id="totalPrice">₹{{ $total_price }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center py-2">
                    <h6 class="card-title text-info mb-1">Today's Price</h6>
                    <h4 class="fw-bold mb-0" id="todayPrice">₹{{ $today_price }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label fw-semibold">From Date</label>
            <input type="date" id="from_date" class="form-control" value="{{ $fromDate }}">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label fw-semibold">To Date</label>
            <input type="date" id="to_date" class="form-control" value="{{ $toDate }}">
        </div>
        <div class="col-md-3">
            <label for="vendor_id" class="form-label fw-semibold">Vendor Name</label>
            <select id="vendor_id" class="form-select select2">
                <option value="">All Vendors</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="payment_mode" class="form-label fw-semibold">Mode of Payment</label>
            <select id="payment_mode" class="form-select">
                <option value="">All</option>
                <option value="Cash">Cash</option>
                <option value="Upi">UPI</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button id="searchBtn" class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i> Search
            </button>
        </div>
    </div>


    <!-- Data Table -->
    <div class="table-responsive export-table">
        <table id="file-datatable" class="table table-bordered">
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
            <tbody id="reportTableBody">
                @foreach ($reportData as $item)
                    <tr>
                        <td>{{ $item->pickup_date }}</td>
                        <td>{{ $item->vendor->vendor_name ?? '-' }}</td>
                        <td>{{ $item->rider->rider_name ?? '-' }}</td>
                        <td>
                            @foreach ($item->flowerPickupItems as $f)
                                {{ $f->flower->name }} ({{ $f->quantity }} {{ $f->unit->unit_name }})<br>
                            @endforeach
                        </td>
                        <td>{{ $item->status }}</td>
                        <td>₹{{ $item->total_price }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <!-- Scripts & Plugins -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

   <script>
    $(document).ready(function () {
        let table = $('#file-datatable').DataTable({
            responsive: true,
            destroy: true,
            searching: true,
            paging: true,
            info: true,
        });

        $('#searchBtn').on('click', function () {
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();
            const vendorId = $('#vendor_id').val();
            const paymentMode = $('#payment_mode').val();

            if (!fromDate || !toDate) {
                Swal.fire('Warning', 'Please select both from and to dates.', 'warning');
                return;
            }

            $.ajax({
                url: '{{ route('report.flower.pickup') }}',
                type: 'POST',
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    vendor_id: vendorId,
                    payment_mode: paymentMode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    table.clear().draw();

                    response.data.forEach(item => {
                        const flowerDetails = item.flower_pickup_items.map(i =>
                            `${i.flower.name} (${i.quantity} ${i.unit.unit_name})`
                        ).join('<br>');

                        table.row.add([
                            item.pickup_date,
                            item.vendor?.vendor_name || '-',
                            item.rider?.rider_name || '-',
                            flowerDetails,
                            item.status,
                            '₹' + item.total_price
                        ]).draw(false);
                    });

                    $('#totalPrice').text('₹' + response.total_price);
                    $('#todayPrice').text('₹' + response.today_price);
                },
                error: function () {
                    Swal.fire('Error', 'Unable to fetch data.', 'error');
                }
            });
        });

        // Optional: auto-trigger search on load if needed
        // $('#searchBtn').trigger('click');
    });
</script>

@endsection
