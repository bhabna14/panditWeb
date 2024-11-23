@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">ADD Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-product') }}"
                        class="btn btn-warning text-dark">Manage Product</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">ADD Product</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">
            {{ $errors->first('danger') }}
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Product Name -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter product name" required>
            </div>

            <!-- Price -->
            <div class="col-md-3 mb-3">
                <label for="price" class="form-label">MRP (Rs.)</label>
                <input type="number" name="mrp" class="form-control" id="mrp" placeholder="Enter product mrp"  required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="price" class="form-label">Sale Price (Rs.)</label>
                <input type="number" name="price" class="form-control" id="price" placeholder="Enter product sale price" required>
            </div>

            <!-- Description -->
            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="4" placeholder="Enter product description" required></textarea>
            </div>

            <!-- Category -->
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control select2" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Puja Item">Puja Item</option>
                    <option value="Subscription">Subscription</option>
                    <option value="Flower">Flower</option>
                    <option value="Immediateproduct">Immediate Product</option>

                    <!-- Add other categories as needed -->
                </select>
            </div>

            <!-- Stock -->
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" id="stock" placeholder="Enter stock quantity" >
            </div>

            <!-- Subscription Duration -->
            <div class="col-md-6 mb-3">
                <label for="duration" class="form-label">Subscription Duration (Months)</label>
                <select name="duration" id="duration" class="form-control select2" >
                    <option value="" disabled selected>Select Package</option>
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                   
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" name="product_image" class="form-control" id="product_image" placeholder="Enter stock quantity" required>
            </div>

            <!-- Submit Button -->
            <div class="col-md-12 mt-4">
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </div>
    </form>
@endsection

@section('modal')
@endsection

@section('scripts')
    <!-- Form-layouts js -->
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Initialize Select2 for dropdowns
        });
    </script>
@endsection
