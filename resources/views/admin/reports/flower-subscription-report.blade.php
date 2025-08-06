{{-- Extend your base layout --}}
@extends('admin.layouts.apps')

{{-- SECTION: Styles --}}
@section('styles')
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- DataTables and Bootstrap CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome & Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

{{-- SECTION: Content --}}
@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Subscription Report</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Report</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" id="from_date" name="from_date" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" id="to_date" name="to_date" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
        </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="file-datatable" class="table table-bordered w-100">
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Purchase Date</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

{{-- SECTION: Scripts --}}
@section('scripts')
    <!-- JS Libraries: jQuery, DataTables, Bootstrap, SweetAlert, Moment, Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTable Initialization Script -->
   <script>
    $(document).ready(function () {
        var table = $('#file-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('subscription.report') }}",
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const user = row.user || {};
                        const address = user.address_details || {};
                        const userId = user.userid ?? null;

                        const tooltipContent = `
                            <strong>Name:</strong> ${user.name || 'N/A'}<br>
                            <strong>No:</strong> ${user.mobile_number || 'N/A'}
                        `.trim();

                        const modalId = `addressModal${userId}`;

                        const viewBtn = userId
                            ? `<a href="/admin/show-customer/${userId}/details" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a>`
                            : '';

                        const addressHtml = `
                            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title"><i class="fas fa-home"></i> Address Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Address:</strong> ${address.apartment_flat_plot || ''}, ${address.apartment_name || ''}, ${address.locality || ''}</p>
                                            <p><strong>Landmark:</strong> ${address.landmark || ''}</p>
                                            <p><strong>Pin Code:</strong> ${address.pincode || ''}</p>
                                            <p><strong>City:</strong> ${address.city || ''}</p>
                                            <p><strong>State:</strong> ${address.state || ''}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        return `
                            <div class="order-details" 
                                 data-bs-toggle="tooltip" 
                                 data-bs-html="true" 
                                 title="${tooltipContent}">
                                <strong>Name:</strong> ${user.name || 'N/A'}<br>
                                <strong>No:</strong> ${user.mobile_number || 'N/A'}<br>
                                ${viewBtn}
                                <br>
                                <button class="btn btn-sm btn-warning mt-1" data-bs-toggle="modal" data-bs-target="#${modalId}">
                                    View Address
                                </button>
                            </div>
                            ${addressHtml}
                        `;
                    }
                },
                {
                    data: 'purchase_date',
                    render: function (data) {
                        return moment(data.start).format('DD MMM YYYY') + ' - ' + moment(data.end).format('DD MMM YYYY');
                    }
                },
                {
                    data: 'duration',
                    render: function (data) {
                        return data + ' days';
                    }
                },
                {
                    data: 'price'
                },
                {
                    data: 'status'
                }
            ]
        });

        $('#searchBtn').click(function () {
            table.ajax.reload();
        });

        // Re-enable tooltips after DataTable draw
        $('#file-datatable').on('draw.dt', function () {
            // Manually dispose existing tooltips to avoid duplicates
            $('[data-bs-toggle="tooltip"]').tooltip('dispose');

            // Re-initialize all tooltips
            $('[data-bs-toggle="tooltip"]').tooltip({
                html: true,
                boundary: 'window',
                trigger: 'hover'
            });
        });
    });
</script>

@endsection
