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
                                    <th class="border-bottom-0">Joined On</th>
                                    <th class="border-bottom-0">Document Image</th>
                                    <th class="border-bottom-0">Gst</th>
                                    <th class="border-bottom-0">Vendor Address</th>
                                    <th class="border-bottom-0">Bank Details</th>
                                    <th class="border-bottom-0">Flower List</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendor_details as $index => $vendor)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><a class="text-danger"
                                                href="{{ route('admin.vendorAllDetails', $vendor->vendor_id) }}">{{ $vendor->vendor_name }}</a>
                                        </td>
                                        <td>{{ $vendor->phone_no }}</td>
                                        <td>{{ $vendor->email_id }}</td>
                                        <td>{{ $vendor->vendor_category }}</td>
                                        <td>{{ $vendor->payment_type }}</td>
                                        <td>{{ \Carbon\Carbon::parse($vendor->date_of_joining)->format('d-m-Y') }}</td>
                                        <td>
                                            @if ($vendor->vendor_document)
                                                <button type="button" class="btn btn-outline-primary btn-sm view-doc-btn"
                                                    data-bs-toggle="modal" data-bs-target="#vendorDocModal"
                                                    data-doc-url="{{ asset('storage/' . $vendor->vendor_document) }}"
                                                    {{-- if you saved to storage/app/public/vendor_docs/... --}}
                                                    data-doc-name="Document — {{ $vendor->vendor_name }}">
                                                    View Document
                                                </button>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>

                                        <td>{{ $vendor->vendor_gst }}</td>
                                        <td>{{ $vendor->vendor_address }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#vendorModal" data-vendor="{{ $vendor }}">
                                                View Banks
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                                data-bs-target="#flowerModal"
                                                data-flower-ids="{{ json_encode($vendor->flower_ids) }}"
                                                data-flower-list="{{ $flowers->whereIn('product_id', $vendor->flower_ids ?? [])->pluck('name')->implode(', ') }}">
                                                View Flowers
                                            </button>
                                        </td>

                                        <td>
                                            <form action="{{ route('admin.deletevendor', $vendor->vendor_id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                @csrf
                                                <button type="submit" class="btn btn-md btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <a href="{{ route('admin.editVendorDetails', $vendor->vendor_id) }}"
                                                    class="btn btn-md btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
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

    <div class="modal fade" id="vendorDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vendor Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="docContent" class="w-100 d-flex justify-content-center align-items-center"
                        style="min-height:60vh;">
                        <div class="text-muted">Loading…</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="docDownloadBtn" href="#" class="btn btn-secondary" target="_blank" rel="noopener">Open in
                        new tab</a>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flower List Modal -->
    <div class="modal fade" id="flowerModal" tabindex="-1" aria-labelledby="flowerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="flowerModalLabel">Vendor Flower List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="flowerList" class="list-group">
                        <!-- Flower items will be injected here by JS -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    @if (!empty($vendor->vendorBanks) && $vendor->vendorBanks->count())
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
                                @foreach ($vendor->vendorBanks as $bank)
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
    <script>
        const allFlowers = @json($flowers);

        // When modal opens
        var flowerModal = document.getElementById('flowerModal');
        flowerModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var flowerIds = JSON.parse(button.getAttribute('data-flower-ids') || '[]');

            var flowerListContainer = document.getElementById('flowerList');
            flowerListContainer.innerHTML = '';

            if (flowerIds.length > 0) {
                flowerIds.forEach(id => {
                    let flower = allFlowers.find(f => f.product_id == id);
                    if (flower) {
                        let li = document.createElement('li');
                        li.className = 'list-group-item';
                        li.textContent = flower.name + (flower.odia_name ? ` (${flower.odia_name})` : '');
                        flowerListContainer.appendChild(li);
                    }
                });
            } else {
                flowerListContainer.innerHTML = '<li class="list-group-item text-muted">No flowers assigned.</li>';
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('vendorDocModal');

            modalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-doc-url');
                const name = button.getAttribute('data-doc-name') || 'Vendor Document';

                // figure out extension from URL (ignore query string)
                const clean = (url || '').split('?')[0];
                const ext = (clean.split('.').pop() || '').toLowerCase();

                const contentEl = modalEl.querySelector('#docContent');
                const downloadEl = modalEl.querySelector('#docDownloadBtn');
                modalEl.querySelector('.modal-title').textContent = name;
                downloadEl.href = url;

                contentEl.innerHTML = '';

                if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) {
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = name;
                    img.className = 'img-fluid rounded shadow-sm';
                    contentEl.appendChild(img);
                } else if (ext === 'pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = url + '#zoom=page-width';
                    iframe.width = '100%';
                    iframe.height = '600';
                    iframe.style.border = '0';
                    contentEl.appendChild(iframe);
                } else {
                    contentEl.innerHTML =
                        '<p class="text-muted">Preview not available. ' +
                        '<a href="' + url + '" target="_blank" rel="noopener">Click to download</a>.</p>';
                }
            });

            // optional: cleanup so PDFs stop rendering when modal closes
            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.querySelector('#docContent').innerHTML = '';
            });
        });
    </script>
@endsection
