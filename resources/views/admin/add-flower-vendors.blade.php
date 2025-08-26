@extends('admin.layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Add Vendor Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.managevendor') }}"
                        class="btn btn-info text-white">Manage Vendor</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Vendor</li>
            </ol>
        </div>
    </div>
    <!-- Vendor Form -->
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-body pt-0 pt-4">
                    <form method="POST" action="{{ route('admin.saveVendorDetails') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="vendor_name">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                                       id="vendor_name" name="vendor_name"
                                       placeholder="Enter Vendor Name"
                                       value="{{ old('vendor_name') }}" required>
                                @error('vendor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="phone_no">Phone Number <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('phone_no') is-invalid @enderror"
                                       id="phone_no" name="phone_no"
                                       placeholder="e.g. 9876543210 or +919876543210"
                                       inputmode="numeric"
                                       pattern="^\+?[0-9]{7,15}$"
                                       value="{{ old('phone_no') }}" required>
                                @error('phone_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Digits only, 7–15 chars, optional leading “+”.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="email_id">Email Id</label>
                                <input type="email" class="form-control @error('email_id') is-invalid @enderror"
                                       id="email_id" name="email_id"
                                       value="{{ old('email_id') }}">
                                @error('email_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="vendor_category">Vendor Category <span class="text-danger">*</span></label>
                                <select class="form-control @error('vendor_category') is-invalid @enderror"
                                        id="vendor_category" name="vendor_category" required>
                                    <option value="">Select Vendor Category</option>
                                    <option value="farmer"   {{ old('vendor_category')==='farmer'?'selected':'' }}>Farmer</option>
                                    <option value="retailer" {{ old('vendor_category')==='retailer'?'selected':'' }}>Retailer</option>
                                    <option value="dealer"   {{ old('vendor_category')==='dealer'?'selected':'' }}>Dealer</option>
                                </select>
                                @error('vendor_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="payment_type">Payment Type</label>
                                <select class="form-control @error('payment_type') is-invalid @enderror"
                                        id="payment_type" name="payment_type">
                                    <option value="">Select Payment Type</option>
                                    <option value="UPI"  {{ old('payment_type')==='UPI'?'selected':'' }}>UPI</option>
                                    <option value="Bank" {{ old('payment_type')==='Bank'?'selected':'' }}>Bank</option>
                                    <option value="Cash" {{ old('payment_type')==='Cash'?'selected':'' }}>Cash</option>
                                </select>
                                @error('payment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="vendor_gst">GST No.</label>
                                <input type="text" class="form-control @error('vendor_gst') is-invalid @enderror"
                                       id="vendor_gst" name="vendor_gst"
                                       value="{{ old('vendor_gst') }}"
                                       placeholder="e.g. 22AAAAA0000A1Z5">
                                @error('vendor_gst') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="date_of_joining">Date of Joining <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_joining') is-invalid @enderror"
                                       id="date_of_joining" name="date_of_joining"
                                       max="{{ now()->toDateString() }}"
                                       value="{{ old('date_of_joining') ?? now()->toDateString() }}" required>
                                @error('date_of_joining') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="vendor_document">Vendor Document (PDF)</label>
                                <input type="file" class="form-control @error('vendor_document') is-invalid @enderror"
                                       id="vendor_document" name="vendor_document" accept="application/pdf">
                                <small class="text-muted">Upload vendor document in PDF format.</small>
                                @error('vendor_document') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="vendor_address">Vendor Address</label>
                        <textarea name="vendor_address" class="form-control @error('vendor_address') is-invalid @enderror"
                                  id="vendor_address" placeholder="Enter Vendor Address">{{ old('vendor_address') }}</textarea>
                        @error('vendor_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Flowers Provided (Category = Flower) --}}
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Select Flowers</label>
                                    <small class="text-muted d-block">Choose one or more flowers supplied by this vendor.</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="text" id="flowerSearch" class="form-control" placeholder="Search flowers..." style="min-width: 220px;">
                                    <button type="button" class="btn btn-outline-primary" id="selectAllFlowers">Select all</button>
                                    <button type="button" class="btn btn-outline-secondary" id="clearAllFlowers">Clear</button>
                                </div>
                            </div>

                            @if (isset($flowers) && $flowers->count())
                                <div class="row" id="flowersGrid">
                                    @foreach ($flowers as $flower)
                                        @php $checked = in_array($flower->product_id, old('flower_ids', [])); @endphp
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2 flower-item">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input flower-checkbox"
                                                       type="checkbox"
                                                       id="flower_{{ $flower->product_id }}"
                                                       name="flower_ids[]"
                                                       value="{{ $flower->product_id }}"
                                                       {{ $checked ? 'checked' : '' }}>
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

                    {{-- Bank details (repeatable) --}}
                    <div id="bank-details-container"
                         style="background-color: rgba(239, 227, 180, 0.5); padding: 20px; border-radius: 15px; margin: 5px">
                        <div class="bank-details">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <input type="text" class="form-control" name="bank_name[]" placeholder="Enter Bank Name" value="{{ old('bank_name.0') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <input type="text" class="form-control"
                                               name="account_no[]"
                                               inputmode="numeric"
                                               pattern="^[0-9]{9,20}$"
                                               maxlength="20"
                                               placeholder="Enter Account Number"
                                               value="{{ old('account_no.0') }}">
                                        <small class="text-muted">Digits only, 9–20.</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>IFSC Code</label>
                                        <input type="text" class="form-control"
                                               name="ifsc_code[]"
                                               placeholder="e.g. HDFC0001234"
                                               maxlength="11"
                                               pattern="^[A-Z]{4}0[A-Z0-9]{6}$"
                                               oninput="this.value = this.value.toUpperCase()"
                                               value="{{ old('ifsc_code.0') }}">
                                        <small class="text-muted">Format: AAAA0XXXXXX</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>UPI Number/ID</label>
                                        <input type="text" class="form-control"
                                               name="upi_id[]"
                                               placeholder="e.g. name@bank"
                                               pattern="^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$"
                                               value="{{ old('upi_id.0') }}">
                                    </div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <button type="button" class="btn btn-danger remove-bank-section">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success mt-2" id="add-bank-section">Add Bank</button>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6'
            })
        @elseif(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33'
            })
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bankDetailsContainer = document.getElementById('bank-details-container');
            const addBankSectionButton = document.getElementById('add-bank-section');

            // Add Bank Section
            addBankSectionButton.addEventListener('click', function() {
                const newBankSection = document.querySelector('.bank-details').cloneNode(true);
                newBankSection.querySelectorAll('input').forEach(input => input.value = '');
                bankDetailsContainer.appendChild(newBankSection);

                // Add event listener to the new remove button
                newBankSection.querySelector('.remove-bank-section').addEventListener('click', function() {
                    this.closest('.bank-details').remove();
                });
            });

            // Remove Bank Section
            document.querySelectorAll('.remove-bank-section').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.bank-details').remove();
                });
            });


        });
    </script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Flower search filter ---
            const searchInput = document.getElementById('flowerSearch');
            const grid = document.getElementById('flowersGrid');
            if (searchInput && grid) {
                searchInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    grid.querySelectorAll('.flower-item').forEach(function(item) {
                        const label = item.querySelector('.form-check-label')?.innerText
                            ?.toLowerCase() || '';
                        item.style.display = label.includes(q) ? '' : 'none';
                    });
                });
            }

            // --- Select all / Clear ---
            const selectAllBtn = document.getElementById('selectAllFlowers');
            const clearAllBtn = document.getElementById('clearAllFlowers');

            const getVisibleCheckboxes = () => Array.from(
                    document.querySelectorAll('#flowersGrid .flower-item')
                ).filter(el => el.style.display !== 'none')
                .map(el => el.querySelector('.flower-checkbox'))
                .filter(Boolean);

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    getVisibleCheckboxes().forEach(cb => cb.checked = true);
                });
            }
            if (clearAllBtn) {
                clearAllBtn.addEventListener('click', function() {
                    document.querySelectorAll('.flower-checkbox').forEach(cb => cb.checked = false);
                });
            }
        });
    </script>
@endsection
