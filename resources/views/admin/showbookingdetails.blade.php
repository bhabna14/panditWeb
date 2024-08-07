@extends('admin.layouts.app')

@section('styles')
    <!-- Add any required styles -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .card-header {
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')

<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">Booking Details</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb d-flex justify-content-between align-items-center">
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Booking Details</li>
        </ol>
    </div>
</div>

<!-- Booking Details -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
             <h5 class="card-title">Booking ID: {{ $booking->booking_id }}</h5>
            </div>
        </div>
    </div>
    <!-- Pooja Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">Pooja Details</div>
            <div class="card-body">
                <p><strong>Pooja Name:</strong> {{ $booking->pooja->pooja_name }}</p>
                <p><strong>Pooja Fee:</strong> ₹{{ $booking->pooja_fee }}</p>
                <p><strong>Pooja Date:</strong> {{ $booking->booking_date }}</p>
                <p>
                    @if($booking->payment_status == "paid")
                        <strong>Total Paid:</strong> ₹{{ $booking->payment->paid }}   
                        @if($booking->payment->payment_type == "full")
                        (Full paid with 5% discount)
                        @else
                        (Advanced paid 20%)
                        @endif
                    @else
                        <strong>Payment Not Done Yet</strong>
                    @endif
                </p>
                <p><strong>Payment Details:</strong> {{ $booking->payment->payment_id }} ({{ $booking->payment->payment_method }})</p>
            </div>
        </div>
    </div>

    <!-- Pandit Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">Pandit Details</div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $booking->pandit->title }} {{ $booking->pandit->name }}</p>
                <p><strong>Phone:</strong> {{ $booking->pandit->phone }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm">
    <!-- Address Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">Address Details</div>
            <div class="card-body">
                <p><strong>Address:</strong> {{ $booking->address->area }}</p>
                <p><strong>City:</strong> {{ $booking->address->city }}</p>
                <p><strong>State:</strong> {{ $booking->address->state }}</p>
                <p><strong>Zip Code:</strong> {{ $booking->address->pincode }}</p>
            </div>
        </div>
    </div>

    <!-- User Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">User Details</div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $booking->user->name }}</p>
                <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                <p><strong>Phone:</strong> {{ $booking->user->mobile_number }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm">
    <!-- Pooja Status Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">Pooja Status Details</div>
            <div class="card-body">
                
                    <p><strong>Status:</strong> {{ $booking->status }}</p>
                    <p><strong>Payment Status:</strong> {{ $booking->payment_status }}</p>
              
            </div>
        </div>
    </div>

    <!-- Rating Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">Rating Details</div>
            <div class="card-body">
                @foreach($booking->ratings as $rating)
                    <p><strong>Rating:</strong> {{ $rating->rating }}</p>
                    <p><strong>Comment:</strong> {{ $rating->comment }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/pdfmake/vfs_fonts.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/js/table-data.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
@endsection
