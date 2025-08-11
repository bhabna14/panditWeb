@extends('admin.layouts.apps')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PAYMENT MADE</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ route('manageOfficePayments') }}"
                        class="btn btn-warning text-dark">Manage Payment Mode</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('saveOfficeTransaction') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="col-md-4">
                        <label for="categories" class="form-label">Categories</label>
                        <select class="form-select" id="categories" name="categories">
                            <option value="">Select Type</option>
                            <option value="rent">Rent</option>
                            <option value="rider_salary">Rider Salary</option>
                            <option value="vendor_payment">Vendor Payment</option>
                            <option value="fuel">Fuel</option>
                            <option value="package">Package</option>
                            <option value="bus_fare">Bus Fare</option>
                            <option value="miscellaneous">Miscellaneous</option>
                        </select>
                    </div>
                   
                    <div class="col-md-4">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                    </div>

                    <div class="col-md-4">
                        <label for="mode_of_payment" class="form-label">Mode of Payment</label>
                        <select class="form-select" id="mode_of_payment" name="mode_of_payment" required>
                            <option value="">Select Mode</option>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>

                     <div class="col-md-4">
                        <label for="paid_by" class="form-label">Paid By</label>
                        <select class="form-select" id="paid_by" name="paid_by" required>
                            <option value="">Select Person</option>
                            <option value="pankaj">Pankaj</option>
                            <option value="subrat">Subrat</option>
                            <option value="basudha">Basudha</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Offer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
