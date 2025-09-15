@extends('admin.layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .section-card {
            border: 1px solid #eef1f4;
            border-radius: 14px;
        }

        .section-card .card-header {
            background: #fbfcfe;
            border-bottom: 1px solid #eef1f4;
        }

        .section-title {
            font-weight: 600;
            margin: 0;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .help-text {
            font-size: .825rem;
            color: #6c757d;
        }

        .gap-12 {
            gap: 12px;
        }

        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px;
            border-top: 1px solid #eef1f4;
            z-index: 2;
        }

        .form-floating>label>small {
            font-weight: 400;
            color: #6c757d;
        }

        .flower-tools .form-control {
            min-width: 220px;
        }

        .flower-badge {
            font-size: .8rem;
        }

        .bank-chip {
            font-size: .8rem;
            background: #fff7e6;
            border: 1px dashed #f0ad4e;
            border-radius: 999px;
            padding: 4px 10px;
        }

        .bank-row {
            background-color: rgba(239, 227, 180, 0.28);
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Add Vendor Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.managevendor') }}" class="btn btn-info text-white">Manage Vendor</a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Vendor</li>
            </ol>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.saveVendorDetails') }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- VENDOR INFO --}}
        <div class="card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="section-title">Vendor Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="vendor_name" class="form-label required">Vendor Name</label>
                        <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                            id="vendor_name" name="vendor_name" placeholder="Enter Vendor Name"
                            value="{{ old('vendor_name') }}" required>
                        @error('vendor_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="phone_no" class="form-label required">Phone Number</label>
                        <input type="tel" inputmode="numeric" pattern="[0-9]{10,12}"
                            class="form-control @error('phone_no') is-invalid @enderror" id="phone_no" name="phone_no"
                            placeholder="e.g. 9876543210" value="{{ old('phone_no') }}" required>
                        <div class="help-text">Digits only; include STD/mobile without spaces.</div>
                        @error('phone_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="email_id" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email_id') is-invalid @enderror" id="email_id"
                            name="email_id" placeholder="name@example.com" value="{{ old('email_id') }}">
                        @error('email_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="vendor_category" class="form-label required">Vendor Category</label>
                        <select class="form-control @error('vendor_category') is-invalid @enderror" id="vendor_category"
                            name="vendor_category" required>
                            <option value="">Select Vendor Category</option>
                            @foreach (['Farmer', 'Retailer', 'Dealer'] as $opt)
                                <option value="{{ $opt }}" @selected(old('vendor_category') === $opt)>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="date_of_joining" class="form-label required">Date of Joining</label>
                        <input type="date" class="form-control @error('date_of_joining') is-invalid @enderror"
                            id="date_of_joining" name="date_of_joining" max="{{ now()->toDateString() }}"
                            value="{{ old('date_of_joining') }}" required>
                        @error('date_of_joining')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="vendor_gst" class="form-label">GST No.</label>
                        <input type="text" class="form-control @error('vendor_gst') is-invalid @enderror" id="vendor_gst"
                            name="vendor_gst" maxlength="15" value="{{ old('vendor_gst') }}" placeholder="15-char GSTIN"
                            oninput="this.value=this.value.toUpperCase()"
                            pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$">
                        <div class="help-text">Format: 22ABCDE1234F1Z5</div>
                        @error('vendor_gst')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="vendor_address" class="form-label">Vendor Address</label>
                        <textarea name="vendor_address" id="vendor_address" rows="3"
                            class="form-control @error('vendor_address') is-invalid @enderror" placeholder="Full address with PIN">{{ old('vendor_address') }}</textarea>
                        @error('vendor_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- FLOWERS PROVIDED --}}
        <div class="card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-12">
                <h6 class="section-title m-0">Flowers Provided</h6>
                <div class="d-flex align-items-center gap-12 flower-tools">
                    <input type="text" id="flowerSearch" class="form-control" placeholder="Search flowers...">
                    <button type="button" class="btn btn-outline-primary" id="selectAllFlowers">Select all</button>
                    <button type="button" class="btn btn-outline-secondary" id="clearAllFlowers">Clear</button>
                    <span class="badge bg-light text-dark flower-badge">
                        Selected: <span id="selectedCount">0</span>
                        &nbsp;|&nbsp; Total: <span id="totalCount">{{ isset($flowers) ? $flowers->count() : 0 }}</span>
                        &nbsp;|&nbsp; Visible: <span
                            id="visibleCount">{{ isset($flowers) ? $flowers->count() : 0 }}</span>
                    </span>
                </div>
            </div>

            <div class="card-body">
                @if (isset($flowers) && $flowers->count())
                    <div class="row" id="flowersGrid">
                        @foreach ($flowers as $flower)
                            @php $checked = in_array($flower->product_id, old('flower_ids', [])); @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2 flower-item">
                                <div class="form-check p-2 border rounded">
                                    <input class="form-check-input flower-checkbox" type="checkbox"
                                        id="flower_{{ $flower->product_id }}" name="flower_ids[]"
                                        value="{{ $flower->product_id }}" @checked($checked)>
                                    <label class="form-check-label ms-1" for="flower_{{ $flower->product_id }}">
                                        {{ $flower->name }}
                                        @if (!empty($flower->odia_name))
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
        </div>

        {{-- PAYMENT & BANK --}}
        <div class="card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="section-title m-0">Payment & Bank</h6>
                <span class="bank-chip">Only visible if Payment Type is <strong>Bank</strong> or
                    <strong>UPI</strong></span>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select class="form-control @error('payment_type') is-invalid @enderror" id="payment_type"
                            name="payment_type">
                            <option value="">Select Payment Type</option>
                            @foreach (['UPI', 'Bank', 'Cash'] as $opt)
                                <option value="{{ $opt }}" @selected(old('payment_type') === $opt)>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @php
                    $bankNames = old('bank_name', ['']);
                    $acctNos = old('account_no', ['']);
                    $ifscCodes = old('ifsc_code', ['']);
                    $upiIds = old('upi_id', ['']);
                    $bankRows = max(count($bankNames), count($acctNos), count($ifscCodes), count($upiIds));
                @endphp

                <div id="bank-details-wrapper" class="mt-3" style="display:none;">
                    <div id="bank-details-container">
                        @for ($i = 0; $i < $bankRows; $i++)
                            <div class="bank-row" data-bank-row>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" name="bank_name[]"
                                            placeholder="Enter Bank Name" value="{{ $bankNames[$i] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Account Number</label>
                                        <input type="text" class="form-control" name="account_no[]"
                                            inputmode="numeric" pattern="[0-9]{9,18}" placeholder="9–18 digits"
                                            maxlength="18" value="{{ $acctNos[$i] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">IFSC Code</label>
                                        <input type="text" class="form-control" name="ifsc_code[]"
                                            placeholder="e.g. HDFC0001234" maxlength="11"
                                            oninput="this.value=this.value.toUpperCase()" pattern="^[A-Z]{4}0[A-Z0-9]{6}$"
                                            value="{{ $ifscCodes[$i] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">UPI Number/ID</label>
                                        <input type="text" class="form-control" name="upi_id[]"
                                            placeholder="username@bank" value="{{ $upiIds[$i] ?? '' }}">
                                        <div class="help-text">If using UPI, provide the UPI ID here.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button"
                                            class="btn btn-outline-danger remove-bank-section">Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <button type="button" class="btn btn-success mt-2" id="add-bank-section">Add another</button>
                </div>
            </div>
        </div>

        {{-- DOCUMENTS --}}
        <div class="card section-card mb-3">
            <div class="card-header">
                <h6 class="section-title m-0">Documents</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vendor_document" class="form-label">Vendor Document</label>
                        <input type="file" class="form-control @error('vendor_document') is-invalid @enderror"
                            id="vendor_document" name="vendor_document" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="help-text">Accepted: PDF, JPG, PNG. Max 5MB (enforce on server).</div>
                        @error('vendor_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="card section-card">
            <div class="sticky-actions d-flex justify-content-between align-items-center">
                <div class="text-muted">Review details before submitting.</div>
                <div class="d-flex gap-12">
                    <a href="{{ route('admin.managevendor') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="reset" class="btn btn-light">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit Vendor</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SweetAlert flash --}}
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                confirmButtonColor: '#3085d6'
            })
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonColor: '#d33'
            })
        @endif
    </script>

    {{-- Flowers: search/filter and counts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('flowerSearch');
            const grid = document.getElementById('flowersGrid');
            const selectAllBtn = document.getElementById('selectAllFlowers');
            const clearAllBtn = document.getElementById('clearAllFlowers');
            const selectedCount = document.getElementById('selectedCount');
            const totalCount = document.getElementById('totalCount');
            const visibleCount = document.getElementById('visibleCount');

            function getItems() {
                return Array.from(document.querySelectorAll('#flowersGrid .flower-item'));
            }

            function getVisibleCheckboxes() {
                return getItems()
                    .filter(el => el.style.display !== 'none')
                    .map(el => el.querySelector('.flower-checkbox'))
                    .filter(Boolean);
            }

            function refreshCounts() {
                const allCbs = Array.from(document.querySelectorAll('.flower-checkbox'));
                const selected = allCbs.filter(cb => cb.checked).length;
                selectedCount.textContent = selected.toString();
                visibleCount.textContent = getVisibleCheckboxes().length.toString();
            }

            function applyFilter(q) {
                if (!grid) return;
                const query = q.toLowerCase().trim();
                getItems().forEach(item => {
                    const label = item.querySelector('.form-check-label')?.innerText?.toLowerCase() ?? '';
                    item.style.display = label.includes(query) ? '' : 'none';
                });
                refreshCounts();
            }

            if (grid) {
                // initial counts
                refreshCounts();
                // typing filter
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        applyFilter(this.value);
                    });
                }
                // select all visible
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function() {
                        getVisibleCheckboxes().forEach(cb => cb.checked = true);
                        refreshCounts();
                    });
                }
                // clear all
                if (clearAllBtn) {
                    clearAllBtn.addEventListener('click', function() {
                        document.querySelectorAll('.flower-checkbox').forEach(cb => cb.checked = false);
                        refreshCounts();
                    });
                }
                // update counts on any change
                grid.addEventListener('change', e => {
                    if (e.target && e.target.classList.contains('flower-checkbox')) refreshCounts();
                });
            }
        });
    </script>

    {{-- Payment: conditional bank/upi section + dynamic rows --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentType = document.getElementById('payment_type');
            const wrapper = document.getElementById('bank-details-wrapper');
            const container = document.getElementById('bank-details-container');
            const addBtn = document.getElementById('add-bank-section');

            function toggleBankSection() {
                const val = paymentType?.value || '';
                const show = (val === 'Bank' || val === 'UPI');
                wrapper.style.display = show ? '' : 'none';
            }

            function bindRemoveButtons(scope = document) {
                scope.querySelectorAll('.remove-bank-section').forEach(btn => {
                    btn.onclick = () => {
                        const rows = container.querySelectorAll('[data-bank-row]');
                        if (rows.length > 1) {
                            btn.closest('[data-bank-row]').remove();
                        } else {
                            // just clear inputs if only one row left
                            btn.closest('[data-bank-row]').querySelectorAll('input').forEach(i => i
                                .value = '');
                        }
                    }
                });
            }

            function addBankRow() {
                const tmpl = document.createElement('template');
                tmpl.innerHTML = `
                <div class="bank-row" data-bank-row>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name[]" placeholder="Enter Bank Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-control" name="account_no[]" inputmode="numeric"
                                   pattern="[0-9]{9,18}" maxlength="18" placeholder="9–18 digits">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc_code[]" maxlength="11"
                                   oninput="this.value=this.value.toUpperCase()"
                                   pattern="^[A-Z]{4}0[A-Z0-9]{6}$" placeholder="e.g. HDFC0001234">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">UPI Number/ID</label>
                            <input type="text" class="form-control" name="upi_id[]" placeholder="username@bank">
                            <div class="help-text">If using UPI, provide the UPI ID here.</div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-danger remove-bank-section">Remove</button>
                        </div>
                    </div>
                </div>`;
                const node = tmpl.content.firstElementChild;
                container.appendChild(node);
                bindRemoveButtons(node);
            }

            // init
            if (paymentType && wrapper && container) {
                toggleBankSection();
                paymentType.addEventListener('change', toggleBankSection);
                bindRemoveButtons(container);
                if (addBtn) addBtn.addEventListener('click', addBankRow);
            }
        });
    </script>
@endsection
