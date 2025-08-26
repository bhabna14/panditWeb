@extends('admin.layouts.apps')

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
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.addVendorDetails') }}"
                        class="btn btn-info text-white">Add Vendor</a></li>
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

    @php
        use Illuminate\Support\Facades\Storage;
        use Carbon\Carbon;
    @endphp

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable"
                            class="table table-bordered text-nowrap key-buttons border-bottom align-middle">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Vendor Name</th>
                                    <th>Phone No</th>
                                    <th>Email</th>
                                    <th>Categories</th>
                                    <th>Payment Type</th>
                                    <th>GST</th>
                                    <th>Joined On</th> {{-- NEW --}}
                                    <th>Document</th> {{-- NEW --}}
                                    <th>Vendor Address</th>
                                    <th>Bank Details</th>
                                    <th>Flower List</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($vendor_details as $index => $vendor)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            <a class="text-danger"
                                                href="{{ route('admin.vendorAllDetails', $vendor->vendor_id) }}">
                                                {{ $vendor->vendor_name }}
                                            </a>
                                        </td>

                                        <td>{{ $vendor->phone_no ?? '—' }}</td>
                                        <td>{{ $vendor->email_id ?? '—' }}</td>
                                        <td>{{ $vendor->vendor_category ?? '—' }}</td>
                                        <td>{{ $vendor->payment_type ?? '—' }}</td>
                                        <td>{{ $vendor->vendor_gst ?? '—' }}</td>

                                        {{-- Joined On (formatted) --}}
                                        <td>
                                            @if (!empty($vendor->date_of_joining))
                                                {{ Carbon::parse($vendor->date_of_joining)->format('d M Y') }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- Vendor Document (public disk URL) --}}
                                        <td>
                                            @if (!empty($vendor->vendor_document))
                                                <a class="btn btn-outline-secondary btn-sm"
                                                    href="{{ Storage::url($vendor->vendor_document) }}" target="_blank"
                                                    rel="noopener">
                                                    View
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td style="max-width: 280px; white-space: pre-wrap;">
                                            {{ $vendor->vendor_address ?? '—' }}</td>

                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#vendorModal" data-vendor-name="{{ $vendor->vendor_name }}"
                                                data-banks='@json(
                                                    ($vendor->vendorBanks ?? collect())->map(function ($b) {
                                                            return [
                                                                'bank_name' => $b->bank_name,
                                                                'account_no' => $b->account_no,
                                                                'ifsc_code' => $b->ifsc_code,
                                                                'upi_id' => $b->upi_id,
                                                            ];
                                                        })->values())'>
                                                View Banks
                                            </button>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#flowerModal" data-vendor-name="{{ $vendor->vendor_name }}"
                                                data-flower-ids='@json($vendor->flower_ids ?? [])'>
                                                View Flowers
                                            </button>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.editVendorDetails', $vendor->vendor_id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.deletevendor', $vendor->vendor_id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this vendor?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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

    {{-- Flower List Modal --}}
    <div class="modal fade" id="flowerModal" tabindex="-1" aria-labelledby="flowerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="flowerModalLabel">Vendor Flower List</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="flowerList" class="list-group"></ul>
                    <div id="flowerEmpty" class="text-muted d-none">No flowers assigned.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Vendor Bank Details Modal --}}
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="vendorModalLabel">Vendor Bank Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div id="noBanks" class="text-muted d-none">No bank details available for this vendor.</div>

                    <div class="table-responsive" id="banksTableWrap">
                        <table class="table table-bordered" id="bank-details-table">
                            <thead>
                                <tr>
                                    <th>Bank Name</th>
                                    <th>Account Number</th>
                                    <th>IFSC Code</th>
                                    <th>UPI ID</th>
                                </tr>
                            </thead>
                            <tbody id="bank-details-body">
                                {{-- Rows injected by JS --}}
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script>
        // Full flowers list for client lookup
        const allFlowers = @json(
            ($flowers ?? collect())->map(function ($f) {
                    return [
                        'product_id' => $f->product_id,
                        'name' => $f->name,
                        'odia_name' => $f->odia_name ?? null,
                    ];
                })->values());

        // -------- Flower Modal --------
        const flowerModal = document.getElementById('flowerModal');
        flowerModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const vendorName = button.getAttribute('data-vendor-name') || 'Vendor';
            const flowerIds = JSON.parse(button.getAttribute('data-flower-ids') || '[]');

            document.getElementById('flowerModalLabel').textContent = vendorName + ' — Flower List';

            const list = document.getElementById('flowerList');
            const empty = document.getElementById('flowerEmpty');
            list.innerHTML = '';

            if (flowerIds.length === 0) {
                empty.classList.remove('d-none');
                return;
            }
            empty.classList.add('d-none');

            flowerIds.forEach(function(id) {
                const f = allFlowers.find(x => String(x.product_id) === String(id));
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = f ?
                    (f.name + (f.odia_name ? ` (${f.odia_name})` : '')) :
                    `Unknown (ID: ${id})`;
                list.appendChild(li);
            });
        });

        // -------- Banks Modal --------
        const vendorModal = document.getElementById('vendorModal');
        vendorModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const vendorName = button.getAttribute('data-vendor-name') || 'Vendor';
            const banks = JSON.parse(button.getAttribute('data-banks') || '[]');

            document.getElementById('vendorModalLabel').textContent = vendorName + ' — Bank Details';

            const tbody = document.getElementById('bank-details-body');
            const noBanks = document.getElementById('noBanks');
            const tableWrap = document.getElementById('banksTableWrap');

            tbody.innerHTML = '';

            if (!Array.isArray(banks) || banks.length === 0) {
                tableWrap.classList.add('d-none');
                noBanks.classList.remove('d-none');
                return;
            }

            noBanks.classList.add('d-none');
            tableWrap.classList.remove('d-none');

            banks.forEach(function(b) {
                const tr = document.createElement('tr');

                const tdBank = document.createElement('td');
                tdBank.textContent = b.bank_name || '—';
                tr.appendChild(tdBank);

                const tdAcc = document.createElement('td');
                tdAcc.textContent = b.account_no || '—';
                tr.appendChild(tdAcc);

                const tdIfsc = document.createElement('td');
                tdIfsc.textContent = b.ifsc_code || '—';
                tr.appendChild(tdIfsc);

                const tdUpi = document.createElement('td');
                tdUpi.textContent = b.upi_id || '—';
                tr.appendChild(tdUpi);

                tbody.appendChild(tr);
            });
        });
    </script>
@endsection
