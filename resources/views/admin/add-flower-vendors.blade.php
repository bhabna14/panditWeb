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
                    <form method="POST" action="{{ route('admin.saveVendorDetails') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="venodr_name">Vendor Name <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="vendor_name" name="vendor_name"
                                        placeholder="Enter Venoor Name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone_no">Phone Number <span style="color:red">*</span></label>
                                    <input type="number" class="form-control" id="phone_no" name="phone_no" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email_id">Email Id </label>
                                    <input type="email" class="form-control" id="email_id" name="email_id">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_category">Vendor Category <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="vendor_category" name="vendor_category"
                                        placeholder="Enter Vendor Category" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <select class="form-control" id="payment_type" name="payment_type">
                                        <option value="">Select Payment Type</option>
                                        <option value="UPI">UPI</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_gst">GST NO.</label>
                                    <input type="text" class="form-control" id="vendor_gst" name="vendor_gst">
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="vendor_address">Vendor Address</label>
                            <textarea name="vendor_address" class="form-control" id="vendor_address" placeholder="Enter Vendor Address"></textarea>
                        </div>

                        {{-- Flowers Provided (Category = Flower) --}}
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                    <div>
                                        <label class="form-label mb-0">Select Flowers</label>
                                        <small class="text-muted d-block">Choose one or more flowers supplied by this
                                            vendor.</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <input type="text" id="flowerSearch" class="form-control"
                                            placeholder="Search flowers..." style="min-width: 220px;">
                                        <button type="button" class="btn btn-outline-primary" id="selectAllFlowers">Select
                                            all</button>
                                        <button type="button" class="btn btn-outline-secondary"
                                            id="clearAllFlowers">Clear</button>
                                    </div>
                                </div>

                                @if (isset($flowers) && $flowers->count())
                                    <div class="row" id="flowersGrid">
                                        @foreach ($flowers as $flower)
                                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2 flower-item">
                                                <div class="form-check p-2 border rounded">
                                                    <input class="form-check-input flower-checkbox" type="checkbox"
                                                        id="flower_{{ $flower->product_id }}" name="flower_ids[]"
                                                        value="{{ $flower->product_id }}">
                                                    <label class="form-check-label ms-1"
                                                        for="flower_{{ $flower->product_id }}">
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

                        <div id="bank-details-container"
                            style="background-color: rgba(239, 227, 180, 0.5);padding: 20px;border-radius: 15px;margin: 5px">
                            <div class="bank-details">
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_name">Bank Name</label>
                                            <input type="text" class="form-control" name="bank_name[]"
                                                placeholder="Enter Bank Name">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="account_no">Account Number</label>
                                            <input type="number" class="form-control" name="account_no[]"
                                                placeholder="Enter Account Number" maxlength="17">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ifsc_code">IFSC Code</label>
                                            <input type="text" class="form-control" name="ifsc_code[]"
                                                placeholder="Enter IFSC Code" maxlength="15"
                                                oninput="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="upi_id">UPI Number/ID</label>
                                            <input type="text" class="form-control" name="upi_id[]"
                                                placeholder="Enter UPI Number/ID">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-4">
                                        <button type="button" class="btn btn-danger remove-bank-section">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success" id="add-bank-section">Add Bank</button>

                        <br><br>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
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
@endsection

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
