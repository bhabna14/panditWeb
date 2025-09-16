@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .stats-card {
            border-radius: 14px;
            padding: 20px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
            border: 1px solid #e0e0e0;
        }

        .stats-card:hover {
            transform: translateY(-4px);
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row mb-4 mt-4">
        <div class="col-md-6">
            <div class="stats-card shadow-sm" style="border: 1px solid rgb(186, 185, 185);">
                <div class="card-body text-center py-2">
                    <h6 class="card-title text-success mb-1">Total Price</h6>
                    <h4 class="fw-bold mb-0" id="totalPrice">₹{{ number_format((float)$total_price, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card shadow-sm" style="border: 1px solid rgb(186, 185, 185);">
                <div class="card-body text-center py-2">
                    <h6 class="card-title text-info mb-1">Today's Price</h6>
                    <h4 class="fw-bold mb-0" id="todayPrice">₹{{ number_format((float)$today_price, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
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

    <!-- Table -->
    <div class="table-responsive export-table">
        <table id="file-datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Pickup Date</th>
                    <th>Vendor Name</th>
                    <th>Rider Name</th>
                    <th>Paid By</th>
                    <th>Flower Details</th>
                    <th>Status</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                @foreach ($reportData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->pickup_date)->format('d M Y') }}</td>
                        <td>{{ $item->vendor->vendor_name ?? '—' }}</td>
                        <td>{{ $item->rider->rider_name ?? '—' }}</td>
                        <td>{{ $item->paid_by ? ucfirst($item->paid_by) : '—' }}</td>
                        <td>
                            @forelse ($item->flowerPickupItems as $f)
                                {{ $f->flower?->name ?? '—' }}
                                ({{ rtrim(rtrim(number_format((float)$f->quantity, 2), '0'), '.') }}
                                {{ $f->unit?->unit_name ?? '—' }})<br>
                            @empty
                                —
                            @endforelse
                        </td>
                        <td>{{ $item->status ? ucfirst($item->status) : '—' }}</td>
                        <td>₹{{ number_format((float)$item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Avoid DataTables alert popups
        $.fn.dataTable.ext.errMode = 'none';

        function capFirst(str) {
            if (!str) return '-';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
        function money(n) {
            n = parseFloat(n || 0);
            return '₹' + n.toFixed(2);
        }

        $(document).ready(function () {
            $('.select2').select2();

            let table = $('#file-datatable').DataTable({
                responsive: true,
                searching: true,
                paging: true,
                info: true
            });

            $('#searchBtn').on('click', function () {
                const fromDate    = $('#from_date').val();
                const toDate      = $('#to_date').val();
                const vendorId    = $('#vendor_id').val();
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
                        table.clear();

                        (response.data || []).forEach(item => {
                            const items = (item.flower_pickup_items || []).map(i => {
                                const flowerName = i.flower?.name || '—';
                                const unitName   = i.unit?.unit_name || '';
                                const qty        = (parseFloat(i.quantity || 0)).toString();
                                return `${flowerName} (${qty} ${unitName})`;
                            }).join('<br>');

                            // IMPORTANT: 7 cells in the same order as <th>
                            table.row.add([
                                moment(item.pickup_date).isValid() ? moment(item.pickup_date).format('DD MMM YYYY') : (item.pickup_date || '-'),
                                item.vendor?.vendor_name || '-',
                                item.rider?.rider_name || '-',
                                capFirst(item.paid_by),
                                items || '—',
                                capFirst(item.status),
                                money(item.total_price)
                            ]);
                        });

                        table.draw(false);

                        $('#totalPrice').text(money(response.total_price));
                        $('#todayPrice').text(money(response.today_price));
                    },
                    error: function () {
                        Swal.fire('Error', 'Unable to fetch data.', 'error');
                    }
                });
            });
        });
    </script>
@endsection
