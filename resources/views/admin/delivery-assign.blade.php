@extends('admin.layouts.app')

@section('styles')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <style>
        /* Styling */
        .breadcrumb-header {
            background: #0056b3; /* Deep Blue */
            padding: 15px;
            border-radius: 10px;
            color: #fff;
        }
        .table {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background: #003366; /* Dark Navy */
            color: white;
        }
        .table tbody tr:hover {
            background: #f8f9fa; /* Light Gray */
        }
        .badge-active {
            background-color: #007bff !important; /* Professional Blue */
            color: white;
        }
        .badge-inactive {
            background-color: #6c757d !important; /* Soft Gray */
            color: white;
        }
        .card {
            border: none;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-manage {
            background: #007bff; /* Primary Blue */
            color: white;
            border-radius: 5px;
        }
        .btn-manage:hover {
            background: #0056b3; /* Deep Blue */
        }
    </style>
@endsection

@section('content')

    <!-- Breadcrumb Header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1 text-white">
                🚴 Rider Order Assignment
            </span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ url('admin/manage-title') }}" class="btn btn-manage">
                        <i class="fas fa-tasks"></i> Manage Delivery Assign
                    </a>
                </li>
                <li class="breadcrumb-item tx-15">
                    <a href="javascript:void(0);"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="breadcrumb-item active tx-15" aria-current="page">
                    <i class="fas fa-truck"></i> Delivery Assign
                </li>
            </ol>
        </div>
    </div>

    <!-- Rider Details Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <h4 class="mb-1 text-primary">{{ $rider->rider_name }}</h4>
                <p class="mb-0 text-muted"><i class="fas fa-phone"></i> {{ $rider->phone_number }}</p>
            </div>
        </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Show Error Message -->
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    <!-- Orders Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-list-ol"></i> Serial No</th>
                            <th><i class="fas fa-hashtag"></i> Order ID</th>
                            <th><i class="fas fa-user"></i> User</th>
                            <th><i class="fas fa-check-circle"></i> Subscription Status</th>
                            <th><i class="fas fa-calendar"></i> Assigned Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $index => $order)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($order->subscription->status ?? '' == 'active')
                                        <span class="badge badge-active">Active</span>
                                    @else
                                        <span class="badge badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach

                        @if($orders->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-info-circle"></i> No orders assigned to this rider.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfer Order Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <h5 class="text-primary"><i class="fas fa-exchange-alt"></i> Transfer Order to Another Rider</h5>
                <form action="{{ route('admin.transferOrder') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Order Selection -->
                        

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="order_id">Select Order</label>
                                <select class="form-control  select2" name="order_ids[]"  multiple="multiple"  required>
                                    
                                    @foreach ($orders as $order)
                                    <option value="{{ $order->order_id }}">{{ $order->order_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Rider Selection -->
                        <div class="col-md-4">
                            <label for="new_rider_id">Select New Rider</label>
                            <select class="form-control" name="new_rider_id" required>
                                <option value="">-- Select Rider --</option>
                                @foreach ($allRiders as $rider)
                                    <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-exchange-alt"></i> Transfer Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Custom JavaScript if needed
    </script>

<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

<!-- Internal Select2 js-->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

<!--Internal  Form-elements js-->
<script src="{{ asset('assets/js/advanced-form-elements.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
@endsection
