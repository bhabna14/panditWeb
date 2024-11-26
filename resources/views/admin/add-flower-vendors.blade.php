@extends('admin.layouts.app')

@section('styles')
<!-- Include SweetAlert CSS -->
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
            <li class="breadcrumb-item tx-15"><a href="{{ route('admin.managevendor') }}" class="btn btn-info text-white">Manage Vendor</a></li>
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Vendor</li>
        </ol>
    </div>
</div>
<!-- /breadcrumb -->
@if (session('success'))
    <div class="alert alert-success" id="Message">
        {{ session('success') }}
    </div>
@endif


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
                                <input type="text" class="form-control" id="vendor_name" name="vendor_name" placeholder="Enter Venoor Name" required>
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
                                <input type="text" class="form-control" id="vendor_category" name="vendor_category" placeholder="Enter Vendor Category" required>
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

                    <div id="bank-details-container" style="background-color: rgba(239, 227, 180, 0.5);padding: 20px;border-radius: 15px;margin: 5px">
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
                                            placeholder="Enter IFSC Code" maxlength="15"  oninput="this.value = this.value.toUpperCase()">
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
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bankDetailsContainer = document.getElementById('bank-details-container');
        const addBankSectionButton = document.getElementById('add-bank-section');

        // Add Bank Section
        addBankSectionButton.addEventListener('click', function () {
            const newBankSection = document.querySelector('.bank-details').cloneNode(true);
            newBankSection.querySelectorAll('input').forEach(input => input.value = '');
            bankDetailsContainer.appendChild(newBankSection);

            // Add event listener to the new remove button
            newBankSection.querySelector('.remove-bank-section').addEventListener('click', function () {
                this.closest('.bank-details').remove();
            });
        });

        // Remove Bank Section
        document.querySelectorAll('.remove-bank-section').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.bank-details').remove();
            });
        });

   
    });

</script>
<script>
    setTimeout(function(){
        document.getElementById('Message').style.display = 'none';
    }, 3000);
</script>
@endsection

