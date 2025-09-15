@extends('admin.layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* ----- Layout & Cards ----- */
        .section-card {
            border: 1px solid #e7ebf0;
            border-radius: 16px;
            overflow: hidden;
        }

        .section-card+.section-card {
            margin-top: 1rem;
        }

        .section-card .card-header {
            background: #fbfcfe;
            border-bottom: 1px solid #e7ebf0;
            padding: .9rem 1rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .icon-info {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .icon-flower {
            background: #eaf7ef;
            color: #198754;
        }

        .icon-bank {
            background: #e6f7ff;
            color: #0dcaf0;
        }

        .icon-doc {
            background: #fff3cd;
            color: #f59f00;
        }

        .icon-cta {
            background: #f1f3f5;
            color: #6c757d;
        }

        .section-title {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
        }

        .section-subtitle {
            font-size: .9rem;
            color: #6c757d;
        }

        /* ----- Helpers ----- */
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

        /* ----- Sticky Actions ----- */
        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px;
            border-top: 1px solid #e7ebf0;
            z-index: 2;
        }

        /* ----- Flowers Provided ----- */
        .flower-tools .form-control {
            min-width: 220px;
        }

        .flower-badge {
            font-size: .8rem;
        }

        .tool-chip {
            border: 1px solid #e7ebf0;
            border-radius: 999px;
            padding: 4px 10px;
            background: #fff;
            font-size: .8rem;
        }

        /* ----- Payment mini-cards ----- */
        .mini-card {
            border: 1px dashed #ced4da;
            border-radius: 12px;
            padding: 14px;
            background: #fff;
        }

        .mini-card+.mini-card {
            margin-top: .75rem;
        }

        .bank-chip {
            font-size: .8rem;
            background: #fff7e6;
            border: 1px dashed #f0ad4e;
            border-radius: 999px;
            padding: 4px 10px;
        }

        .bank-row,
        .upi-row {
            background-color: #fbfcfe;
            border: 1px solid #eef1f4;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .remove-row-btn {
            min-width: 110px;
        }

        /* ----- Inputs ----- */
        .form-floating>label>small {
            font-weight: 400;
            color: #6c757d;
        }

        /* Make invalid feedback show with custom scripts too */
        input:invalid,
        select:invalid,
        textarea:invalid {
            /* optional visual hint */
        }
    </style>
@endsection

@section('content')

    <form method="POST" action="{{ route('admin.saveVendorDetails') }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- VENDOR INFORMATION --}}
        <div class="card section-card mt-4">
            <div class="card-header">
                <div class="section-header">
                    <span class="header-icon icon-info"><i class="fa fa-user"></i></span>
                    <div>
                        <h6 class="section-title">Vendor Information</h6>
                        <div class="section-subtitle">Basic details about the vendor</div>
                    </div>
                </div>
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
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-12">
                <div class="section-header">
                    <span class="header-icon icon-flower"><i class="fa fa-seedling"></i></span>
                    <div>
                        <h6 class="section-title m-0">Flowers Provided</h6>
                        <div class="section-subtitle">Select the flowers this vendor can supply</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-12 flower-tools">
                    <input type="text" id="flowerSearch" class="form-control" placeholder="Search flowers...">
                    <button type="button" class="btn btn-outline-primary" id="selectAllFlowers">Select all</button>
                    <button type="button" class="btn btn-outline-secondary" id="clearAllFlowers">Clear</button>
                    <button type="button" class="btn btn-outline-dark" id="invertSelection">Invert</button>
                    <span class="tool-chip">
                        Selected: <strong id="selectedCount">0</strong>
                        &nbsp;|&nbsp; Total: <strong id="totalCount">{{ isset($flowers) ? $flowers->count() : 0 }}</strong>
                        &nbsp;|&nbsp; Visible: <strong
                            id="visibleCount">{{ isset($flowers) ? $flowers->count() : 0 }}</strong>
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
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="section-header">
                    <span class="header-icon icon-bank"><i class="fa fa-credit-card"></i></span>
                    <div>
                        <h6 class="section-title m-0">Payment & Bank</h6>
                        <div class="section-subtitle">Provide details for the selected payment method</div>
                    </div>
                </div>
                <span class="bank-chip">Visible when Payment Type is <strong>Bank</strong> or <strong>UPI</strong></span>
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

                {{-- @php
                    $bankNames = old('bank_name', ['']);
                    $acctNos = old('account_no', ['']);
                    $ifscCodes = old('ifsc_code', ['']);
                    $upiIds = old('upi_id', ['']);
                    $bankRows = max(count($bankNames), count($acctNos), count($ifscCodes));
                    $upiRows = max(count($upiIds));
                @endphp --}}
@php
    $bankNames = is_array(old('bank_name')) ? old('bank_name') : (old('bank_name') ? [old('bank_name')] : ['']);
    $acctNos   = is_array(old('account_no')) ? old('account_no') : (old('account_no') ? [old('account_no')] : ['']);
    $ifscCodes = is_array(old('ifsc_code')) ? old('ifsc_code') : (old('ifsc_code') ? [old('ifsc_code')] : ['']);
    $upiIds    = is_array(old('upi_id')) ? old('upi_id') : (old('upi_id') ? [old('upi_id')] : ['']);
@endphp

                {{-- BANK DETAILS WRAPPER --}}
                <div id="bank-details-wrapper" class="mt-3" style="display:none;">
                    <div class="mini-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong>Bank Details</strong>
                            <button type="button" class="btn btn-success btn-sm" id="add-bank-section">
                                <i class="fa fa-plus me-1"></i> Add another
                            </button>
                        </div>

                        <div id="bank-details-container">
                            @for ($i = 0; $i < max(1, $bankRows); $i++)
                                <div class="bank-row" data-bank-row>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Bank Name</label>
                                            <input type="text" class="form-control" name="bank_name[]"
                                                placeholder="Enter Bank Name" value="{{ $bankNames[$i] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Account Number</label>
                                            <input type="text" class="form-control only-digits" name="account_no[]"
                                                inputmode="numeric" pattern="[0-9]{9,18}" maxlength="18"
                                                placeholder="9–18 digits" value="{{ $acctNos[$i] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">IFSC Code</label>
                                            <input type="text" class="form-control" name="ifsc_code[]"
                                                placeholder="e.g. HDFC0001234" maxlength="11"
                                                oninput="this.value=this.value.toUpperCase()"
                                                pattern="^[A-Z]{4}0[A-Z0-9]{6}$" value="{{ $ifscCodes[$i] ?? '' }}">
                                        </div>

                                        <div class="col-md-4">
                                            <button type="button"
                                                class="btn btn-outline-danger remove-row-btn remove-bank-section">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- UPI DETAILS WRAPPER --}}
                <div id="upi-details-wrapper" class="mt-3" style="display:none;">
                    <div class="mini-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong>UPI Details</strong>
                            <button type="button" class="btn btn-success btn-sm" id="add-upi-section">
                                <i class="fa fa-plus me-1"></i> Add another
                            </button>
                        </div>

                        <div id="upi-details-container">
                            @for ($i = 0; $i < max(1, $upiRows); $i++)
                                <div class="upi-row" data-upi-row>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label">UPI Number/ID</label>
                                            <input type="text" class="form-control" name="upi_id[]"
                                                placeholder="username@bank" value="{{ $upiIds[$i] ?? '' }}">
                                            <div class="help-text">Provide the UPI ID used for payments</div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button"
                                                class="btn btn-outline-danger remove-row-btn remove-upi-section">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DOCUMENTS --}}
        <div class="card section-card">
            <div class="card-header">
                <div class="section-header">
                    <span class="header-icon icon-doc"><i class="fa fa-file"></i></span>
                    <div>
                        <h6 class="section-title m-0">Documents</h6>
                        <div class="section-subtitle">Upload any supporting vendor documents</div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vendor_document" class="form-label">Vendor Document</label>
                        <input type="file" class="form-control @error('vendor_document') is-invalid @enderror"
                            id="vendor_document" name="vendor_document" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="help-text">Accepted: PDF, JPG, PNG. Max 5MB (validated server-side).</div>
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
                <div class="section-header">
                    <span class="header-icon icon-cta"><i class="fa fa-info"></i></span>
                    <div class="text-muted">Review details before submitting.</div>
                </div>
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
            const invertBtn = document.getElementById('invertSelection');
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
                selectedCount.textContent = String(selected);
                visibleCount.textContent = String(getVisibleCheckboxes().length);
            }

            function applyFilter(q) {
                if (!grid) return;
                const query = (q || '').toLowerCase().trim();
                getItems().forEach(item => {
                    const label = item.querySelector('.form-check-label')?.innerText?.toLowerCase() ?? '';
                    item.style.display = label.includes(query) ? '' : 'none';
                });
                refreshCounts();
            }

            if (grid) {
                refreshCounts();

                searchInput?.addEventListener('input', function() {
                    applyFilter(this.value);
                });

                selectAllBtn?.addEventListener('click', function() {
                    getVisibleCheckboxes().forEach(cb => cb.checked = true);
                    refreshCounts();
                });

                clearAllBtn?.addEventListener('click', function() {
                    document.querySelectorAll('.flower-checkbox').forEach(cb => cb.checked = false);
                    refreshCounts();
                });

                invertBtn?.addEventListener('click', function() {
                    getVisibleCheckboxes().forEach(cb => cb.checked = !cb.checked);
                    refreshCounts();
                });

                grid.addEventListener('change', e => {
                    if (e.target && e.target.classList.contains('flower-checkbox')) refreshCounts();
                });
            }
        });
    </script>

    {{-- Payment: conditional BANK / UPI sections + dynamic rows + digit-only helpers --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentType = document.getElementById('payment_type');

            const bankWrap = document.getElementById('bank-details-wrapper');
            const bankCont = document.getElementById('bank-details-container');
            const addBankBtn = document.getElementById('add-bank-section');

            const upiWrap = document.getElementById('upi-details-wrapper');
            const upiCont = document.getElementById('upi-details-container');
            const addUpiBtn = document.getElementById('add-upi-section');

            function toggleSections() {
                const val = paymentType?.value || '';
                const showBank = (val === 'Bank');
                const showUPI = (val === 'UPI');

                bankWrap.style.display = showBank ? '' : 'none';
                upiWrap.style.display = showUPI ? '' : 'none';
            }

            function bindRemoveButtons(scope = document) {
                scope.querySelectorAll('.remove-bank-section').forEach(btn => {
                    btn.onclick = () => {
                        const rows = bankCont.querySelectorAll('[data-bank-row]');
                        if (rows.length > 1) {
                            btn.closest('[data-bank-row]').remove();
                        } else {
                            btn.closest('[data-bank-row]').querySelectorAll('input').forEach(i => i
                                .value = '');
                        }
                    }
                });

                scope.querySelectorAll('.remove-upi-section').forEach(btn => {
                    btn.onclick = () => {
                        const rows = upiCont.querySelectorAll('[data-upi-row]');
                        if (rows.length > 1) {
                            btn.closest('[data-upi-row]').remove();
                        } else {
                            btn.closest('[data-upi-row]').querySelectorAll('input').forEach(i => i
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
                            <input type="text" class="form-control only-digits" name="account_no[]"
                                   inputmode="numeric" pattern="[0-9]{9,18}" maxlength="18" placeholder="9–18 digits">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc_code[]" maxlength="11"
                                   oninput="this.value=this.value.toUpperCase()"
                                   pattern="^[A-Z]{4}0[A-Z0-9]{6}$" placeholder="e.g. HDFC0001234">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-danger remove-row-btn remove-bank-section">Remove</button>
                        </div>
                    </div>
                </div>`;
                const node = tmpl.content.firstElementChild;
                bankCont.appendChild(node);
                bindRemoveButtons(node);
                bindDigitOnly(node);
            }

            function addUpiRow() {
                const tmpl = document.createElement('template');
                tmpl.innerHTML = `
                <div class="upi-row" data-upi-row>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">UPI Number/ID</label>
                            <input type="text" class="form-control" name="upi_id[]" placeholder="username@bank">
                            <div class="help-text">Provide the UPI ID used for payments</div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-danger remove-row-btn remove-upi-section">Remove</button>
                        </div>
                    </div>
                </div>`;
                const node = tmpl.content.firstElementChild;
                upiCont.appendChild(node);
                bindRemoveButtons(node);
            }

            // Allow only digits in inputs with .only-digits
            function bindDigitOnly(scope = document) {
                scope.querySelectorAll('.only-digits').forEach(inp => {
                    inp.addEventListener('input', function() {
                        this.value = this.value.replace(/\D+/g, '');
                    });
                });
            }

            // Restrict phone digits
            const phone = document.getElementById('phone_no');
            phone?.addEventListener('input', function() {
                this.value = this.value.replace(/\D+/g, '');
            });

            // Init
            if (paymentType && bankWrap && bankCont && upiWrap && upiCont) {
                toggleSections();
                paymentType.addEventListener('change', toggleSections);
                bindRemoveButtons(document);
                bindDigitOnly(document);
                addBankBtn?.addEventListener('click', addBankRow);
                addUpiBtn?.addEventListener('click', addUpiRow);
            }
        });
    </script>
@endsection
