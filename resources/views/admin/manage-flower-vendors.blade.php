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
                                <th class="border-bottom-0">Flower List</th>
                                <th class="border-bottom-0">Bank Details</th>
                                <th class="border-bottom-0">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vendor_details as $index => $vendor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a class="text-danger" href="{{ route('admin.vendorAllDetails', $vendor->vendor_id) }}">
                                            {{ $vendor->vendor_name }}
                                        </a>
                                    </td>
                                    <td>{{ $vendor->phone_no }}</td>
                                    <td>{{ $vendor->email_id }}</td>
                                    <td>{{ $vendor->vendor_category }}</td>
                                    <td>{{ $vendor->payment_type }}</td>
                                    <td>{{ $vendor->vendor_gst }}</td>
                                    <td>{{ $vendor->vendor_address }}</td>

                                    {{-- Flower List (open modal) --}}
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-primary open-flower-modal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#flowerModal"
                                            data-vendor-id="{{ $vendor->vendor_id }}"
                                            data-vendor-name="{{ $vendor->vendor_name }}"
                                            data-flower-ids='@json($vendor->flower_ids ?? [])'>
                                            View / Update Flowers
                                        </button>
                                    </td>

                                    {{-- Bank Details (existing button) --}}
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#vendorBankModal_{{ $vendor->vendor_id }}">
                                            View Banks
                                        </button>

                                        {{-- (Optional) You can keep a per-vendor bank modal here if you already have it --}}
                                        {{-- ... --}}
                                    </td>

                                    <td>
                                        <form action="{{ route('admin.deletevendor', $vendor->id) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            @csrf
                                            <button type="submit" class="btn btn-md btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <a href="{{ url('admin/edit-vendor-details', $vendor->id) }}"
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

                {{-- Global flash alerts --}}
                @if (session('success'))
                    <div class="alert alert-success mt-3" id="Message">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-3" id="Message">{{ session('error') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- FLOWER MODAL (single reusable) --}}
<div class="modal fade" id="flowerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.vendor.updateFlowers') }}" id="flowerForm" class="modal-content">
            @csrf
            <input type="hidden" name="vendor_id" id="modal_vendor_id">

            <div class="modal-header">
                <h5 class="modal-title">
                    Update Flowers for: <span id="modal_vendor_name" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <div>
                        <label class="form-label mb-0">Select Flowers</label>
                        <small class="text-muted d-block">Choose one or more flowers supplied by this vendor.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="text" id="flowerSearch" class="form-control"
                               placeholder="Search flowers..." style="min-width: 220px;">
                        <button type="button" class="btn btn-outline-primary" id="selectAllFlowers">Select all</button>
                        <button type="button" class="btn btn-outline-secondary" id="clearAllFlowers">Clear</button>
                    </div>
                </div>

                @if(isset($flowers) && $flowers->count())
                    <div class="row" id="flowersGrid">
                        @foreach($flowers as $flower)
                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12 mb-2 flower-item">
                                <div class="form-check p-2 border rounded">
                                    <input
                                        class="form-check-input flower-checkbox"
                                        type="checkbox"
                                        id="modal_flower_{{ $flower->product_id }}"
                                        name="flower_ids[]"
                                        value="{{ $flower->product_id }}"
                                    >
                                    <label class="form-check-label ms-1" for="modal_flower_{{ $flower->product_id }}">
                                        {{ $flower->name }}
                                        @if(!empty($flower->odia_name))
                                            <small class="text-muted">({{ $flower->odia_name }})</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        No flower products found for category <strong>Flower</strong>.
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
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
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    setTimeout(function() {
        const msg = document.getElementById('Message');
        if (msg) msg.style.display = 'none';
    }, 3000);

    // Open modal - prefill vendor info + checkboxes
    const modalEl = document.getElementById('flowerModal');
    const vendorNameSpan = document.getElementById('modal_vendor_name');
    const vendorIdInput  = document.getElementById('modal_vendor_id');

    // Use Bootstrap show event to populate data
    modalEl.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const vendorId   = button.getAttribute('data-vendor-id');
        const vendorName = button.getAttribute('data-vendor-name');
        const raw        = button.getAttribute('data-flower-ids') || '[]';

        let selectedIds = [];
        try {
            selectedIds = JSON.parse(raw) || [];
        } catch (e) {
            selectedIds = [];
        }

        // Set header + hidden input
        vendorNameSpan.textContent = vendorName || '';
        vendorIdInput.value = vendorId || '';

        // Uncheck all first
        document.querySelectorAll('#flowersGrid .flower-checkbox').forEach(cb => cb.checked = false);

        // Check selected ones
        const setSelected = new Set(selectedIds.map(Number));
        document.querySelectorAll('#flowersGrid .flower-checkbox').forEach(cb => {
            const val = Number(cb.value);
            cb.checked = setSelected.has(val);
        });

        // Reset search box
        const searchInput = document.getElementById('flowerSearch');
        if (searchInput) {
            searchInput.value = '';
            document.querySelectorAll('#flowersGrid .flower-item').forEach(function (item) {
                item.style.display = '';
            });
        }
    });

    // Search filter
    const searchInput = document.getElementById('flowerSearch');
    const grid = document.getElementById('flowersGrid');
    if (searchInput && grid) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            grid.querySelectorAll('.flower-item').forEach(function (item) {
                const label = item.querySelector('.form-check-label')?.innerText?.toLowerCase() || '';
                item.style.display = label.includes(q) ? '' : 'none';
            });
        });
    }

    // Select all visible / clear all
    const selectAllBtn = document.getElementById('selectAllFlowers');
    const clearAllBtn  = document.getElementById('clearAllFlowers');

    const getVisibleCheckboxes = () => Array.from(
        document.querySelectorAll('#flowersGrid .flower-item')
    ).filter(el => el.style.display !== 'none')
     .map(el => el.querySelector('.flower-checkbox'))
     .filter(Boolean);

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function () {
            getVisibleCheckboxes().forEach(cb => cb.checked = true);
        });
    }
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function () {
            document.querySelectorAll('#flowersGrid .flower-checkbox').forEach(cb => cb.checked = false);
        });
    }
});
</script>
@endsection
