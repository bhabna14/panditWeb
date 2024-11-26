@extends('admin.layouts.app')

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">{{ isset($vendordetails) ? 'Edit Vendor Details' : 'Add Vendor Details' }}</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.managevendor') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ isset($vendordetails) ? 'Edit Vendor' : 'Add Vendor' }}</li>
            </ol>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.updateVendorDetails', $vendordetails->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Vendor Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_name">Vendor Name <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="vendor_name" name="vendor_name"
                                        value="{{ old('vendor_name', $vendordetails->vendor_name ?? '') }}" required>
                                    @error('vendor_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone_no">Phone Number <span style="color:red">*</span></label>
                                    <input type="number" class="form-control" id="phone_no" name="phone_no"
                                        value="{{ old('phone_no', $vendordetails->phone_no ?? '') }}" required>
                                    @error('phone_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email_id">Email ID</label>
                                    <input type="email" class="form-control" id="email_id" name="email_id"
                                        value="{{ old('email_id', $vendordetails->email_id ?? '') }}">
                                    @error('email_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- More Vendor Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_category">Vendor Category <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="vendor_category" name="vendor_category"
                                        value="{{ old('vendor_category', $vendordetails->vendor_category ?? '') }}" required>
                                    @error('vendor_category')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <select class="form-control" id="payment_type" name="payment_type">
                                        <option value="">Select Payment Type</option>
                                        <option value="UPI"
                                            {{ old('payment_type', $vendordetails->payment_type ?? '') == 'UPI' ? 'selected' : '' }}>
                                            UPI</option>
                                        <option value="Bank"
                                            {{ old('payment_type', $vendordetails->payment_type ?? '') == 'Bank' ? 'selected' : '' }}>
                                            Bank</option>
                                        <option value="Cash"
                                            {{ old('payment_type', $vendordetails->payment_type ?? '') == 'Cash' ? 'selected' : '' }}>
                                            Cash</option>
                                    </select>
                                    @error('payment_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_gst">GST Number</label>
                                    <input type="text" class="form-control" id="vendor_gst" name="vendor_gst"
                                        value="{{ old('vendor_gst', $vendordetails->vendor_gst ?? '') }}">
                                    @error('vendor_gst')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        

                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="vendor_address">Vendor Address</label>
                                <textarea name="vendor_address" class="form-control" id="vendor_address">{{$vendordetails->vendor_address}}</textarea>
                            </div>
                        </div>
                    

                        <!-- Bank Details -->
                        <div class="row">
                            <div class="col-md-12 mt-4">
                                <h4>Bank Details</h4>
                               
                            </div>
                        </div>

                        <div id="bank-details-container">
                            @if (isset($vendordetails) && $vendordetails->vendorBanks->isNotEmpty())
                                @foreach ($vendordetails->vendorBanks as $index => $bank)
                                    <div class="bank-details" style="background-color: rgba(239, 227, 180, 0.5);padding: 20px;border-radius: 15px;margin: 5px">
                                        <input type="hidden" name="bank_id[]" value="{{ $bank->id }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bank_name_{{ $index }}">Bank Name</label>
                                                    <input type="text" class="form-control" id="bank_name_{{ $index }}" name="bank_name[]"
                                                        value="{{ old('bank_name.' . $index, $bank->bank_name ?? '') }}">
                                                    @error('bank_name.' . $index)
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="account_no_{{ $index }}">Account Number</label>
                                                    <input type="number" class="form-control" id="account_no_{{ $index }}" name="account_no[]"
                                                        value="{{ old('account_no.' . $index, $bank->account_no ?? '') }}">
                                                    @error('account_no.' . $index)
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="ifsc_code_{{ $index }}">IFSC Code</label>
                                                    <input type="text" class="form-control" id="ifsc_code_{{ $index }}" name="ifsc_code[]"
                                                        value="{{ old('ifsc_code.' . $index, $bank->ifsc_code ?? '') }}">
                                                    @error('ifsc_code.' . $index)
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="upi_id_{{ $index }}">UPI Number/ID</label>
                                                    <input type="text" class="form-control" id="upi_id_{{ $index }}" name="upi_id[]"
                                                        value="{{ old('upi_id.' . $index, $bank->upi_id ?? '') }}">
                                                    @error('upi_id.' . $index)
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                           
                                        </div>
                                        <button type="button" class="btn btn-danger remove-bank-section">Remove Bank Section</button>
                                    </div>
                                @endforeach
                            @else
                                <p>No bank details available. Please add a bank.</p>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center">
                            <button type="button" id="add-bank-section" class="btn btn-success">Add Bank Section</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
   $(document).ready(function() {
    $('#add-bank-section').on('click', function() {
        const index = $('#bank-details-container .bank-details').length;
        const bankSection = `
            <div class="bank-details" style="background-color: rgba(239, 227, 180, 0.5); padding: 20px; border-radius: 15px; margin: 5px">
                <input type="hidden" name="bank_id[]" value="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bank_name_${index}">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name_${index}" name="bank_name[]">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="account_no_${index}">Account Number</label>
                            <input type="number" class="form-control" id="account_no_${index}" name="account_no[]">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ifsc_code_${index}">IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code_${index}" name="ifsc_code[]">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="upi_id_${index}">UPI ID</label>
                            <input type="text" class="form-control" id="upi_id_${index}" name="upi_id[]">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-bank-section">Remove Bank Section</button>
            </div>`;
        $('#bank-details-container').append(bankSection);
    });

    // Remove bank section
    $('#bank-details-container').on('click', '.remove-bank-section', function() {
        $(this).closest('.bank-details').remove();
    });
});

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endsection

