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
<div class="row mb-5">
    <!-- Status Card -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card {{ 
            $booking->status == 'paid' ? 'bg-info-transparent' :
            ($booking->status == 'rejected' ? 'bg-danger-transparent' :
            ($booking->status == 'approved' ? 'bg-warning-transparent' :
            ($booking->status == 'pending' ? 'bg-secondary-transparent' : 'bg-primary-transparent'))) 
        }}">
            <div class="card-body">
                <div class="counter-status md-mb-0">
                    <div class="text-center mb-1">
                        <svg class="about-icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            @if($booking->status == 'paid')
                                <!-- Completed SVG Icon -->
                                <path fill="#b8e6f9" d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm5.207,7.61328-6.1875,6.1875a.99963.99963,0,0,1-1.41406,0L6.793,12.98828A.99989.99989,0,0,1,8.207,11.57422l2.10547,2.10547L15.793,8.19922A.99989.99989,0,0,1,17.207,9.61328Z"/>
                            @elseif($booking->status == 'approved')
                                <!-- Approved SVG Icon -->
                                <path fill="#ffd79c" d="M12,14.5c-3.26461,0.00094-6.4876-0.73267-9.43018-2.14648C2.22156,12.18802,1.99974,11.83676,2,11.45117V9.5c0.00181-1.65611,1.34389-2.99819,3-3h14c1.65611,0.00181,2.99819,1.34389,3,3v1.95215c0.00003,0.3859-0.22189,0.73741-0.57031,0.90332C18.48677,13.76762,15.26418,14.50051,12,14.5z"/>
                            @elseif($booking->status == 'pending')
                                <!-- Pending SVG Icon -->
                                <circle cx="10" cy="8.5" r="5" fill="#fbb8c7"/>
                            @else
                                <!-- Default Status SVG Icon -->
                                <path fill="#38cab3" d="M10.3125,16.09375a.99676.99676,0,0,1-.707-.293L6.793,12.98828A.99989.99989,0,0,1,8.207,11.57422l2.10547,2.10547L15.793,8.19922A.99989.99989,0,0,1,17.207,9.61328l-6.1875,6.1875A.99676.99676,0,0,1,10.3125,16.09375Z"/>
                            @endif
                        </svg>
                    </div>
                    <div class="text-center">
                        <h2 class="counter mb-2">{{ $booking->status }}</h2>
                        <h6 class="mb-0 text-muted">Status</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Status Card -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card {{ 
            $booking->payment_status == 'paid' ? 'bg-success-transparent' :
            ($booking->payment_status == 'pending' ? 'bg-secondary-transparent' :
            ($booking->payment_status == 'rejected' ? 'bg-danger-transparent' :
            ($booking->payment_status == 'refundprocess' ? 'bg-warning-transparent' : 'bg-light'))) 
        }}">
            <div class="card-body">
                <div class="counter-status md-mb-0">
                    <div class="text-center mb-1">
                        <svg class="about-icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            @if($booking->payment_status == 'paid')
                                <!-- Paid SVG Icon -->
                                <path fill="#d4edda" d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm5.207 7.613l-6.187 6.187a.999.999 0 0 1-1.414 0l-2.105-2.105a.999.999 0 0 1 1.414-1.414l1.793 1.793L16.793 8.199a.999.999 0 0 1 1.414 1.414z"/>
                            @elseif($booking->payment_status == 'pending')
                                <!-- Pending SVG Icon -->
                                <circle cx="12" cy="12" r="10" fill="#fbb8c7"/>
                            @elseif($booking->payment_status == 'rejected')
                                <!-- Rejected SVG Icon -->
                                <path fill="#f8d7da" d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm-1.707 13.293a1 1 0 0 1 1.414 0l2.293-2.293 2.293 2.293a1 1 0 1 1-1.414 1.414L12 14.414l-2.293 2.293a1 1 0 0 1-1.414-1.414z"/>
                            @elseif($booking->payment_status == 'refundprocess')
                                <!-- Refund Process SVG Icon -->
                                <path fill="#fff3cd" d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm-1.707 13.293a1 1 0 0 1 1.414 0l2.293-2.293 2.293 2.293a1 1 0 0 1-1.414 1.414L12 14.414l-2.293 2.293a1 1 0 0 1-1.414-1.414z"/>
                            @endif
                        </svg>
                    </div>
                    <div class="text-center mb-1">
                        <h2 class="counter mb-2">{{ $booking->payment_status == 'paid' ? 'Paid' : ($booking->payment_status == 'pending' ? 'Pending' : ($booking->payment_status == 'rejected' ? 'Rejected' : 'Refund Process')) }}</h2>
                        <h6 class="mb-0 text-muted">Payment Status</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pooja Status Card -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card {{ 
            $booking->pooja_status == 'completed' ? 'bg-info-transparent' : 
           ($booking->pooja_status == 'rejected' ? 'bg-danger-transparent' : 
           ($booking->pooja_status == 'canceled' ? 'bg-warning-transparent' : 
           ($booking->pooja_status == 'started' ? 'bg-success-transparent' : 
           ($booking->pooja_status == 'pending' ? 'bg-secondary-transparent' : 'bg-primary-transparent')))) 
        }}">
        <div class="card-body">
            <div class="counter-status md-mb-0">
                <div class="text-center mb-1">
                    <svg class="about-icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        @if($booking->pooja_status == 'completed')
                            <!-- completed SVG Icon -->
                            <path fill="#b8e6f9" d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm5.207,7.61328-6.1875,6.1875a.99963.99963,0,0,1-1.41406,0L6.793,12.98828A.99989.99989,0,0,1,8.207,11.57422l2.10547,2.10547L15.793,8.19922A.99989.99989,0,0,1,17.207,9.61328Z"/>
                        @elseif($booking->pooja_status == 'rejected')
                            <!-- Rejected SVG Icon -->
                            <path fill="#f8d7da" d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm0,18a8.00008,8.00008,0,1,1,8-8A8.00846,8.00846,0,0,1,12,20Zm0-6.5h0V7.5H13v6Zm0,0H12v-1h0Z"/>
                        @elseif($booking->pooja_status == 'canceled')
                            <!-- Canceled SVG Icon -->
                            <path fill="#ffcc00" d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm0,18a8.00008,8.00008,0,1,1,8-8A8.00846,8.00846,0,0,1,12,20Zm0-6.5h0V7.5H13v6Zm0,0H12v-1h0Z"/>
                        @elseif($booking->pooja_status == 'started')
                            <!-- started SVG Icon -->
                            <path fill="#d4edda" d="M12,14.5c-3.26461,0.00094-6.4876-0.73267-9.43018-2.14648C2.22156,12.18802,1.99974,11.83676,2,11.45117V9.5c0.00181-1.65611,1.34389-2.99819,3-3h14c1.65611,0.00181,2.99819,1.34389,3,3v1.95215c0.00003,0.3859-0.22189,0.73741-0.57031,0.90332C18.48677,13.76762,15.26418,14.50051,12,14.5z"/>
                        @elseif($booking->pooja_status == 'pending')
                            <!-- Pending SVG Icon -->
                            <circle cx="10" cy="8.5" r="5" fill="#fbb8c7"/>
                        @else
                            <!-- Default Status SVG Icon -->
                            <path fill="#38cab3" d="M10.3125,16.09375a.99676.99676,0,0,1-.707-.293L6.793,12.98828A.99989.99989,0,0,1,8.207,11.57422l2.10547,2.10547L15.793,8.19922A.99989.99989,0,0,1,17.207,9.61328l-6.1875,6.1875A.99676.99676,0,0,1,10.3125,16.09375Z"/>
                        @endif
                    </svg>
                </div>
                <div class="text-center">
                    <h2 class="counter mb-2">{{ ucfirst($booking->pooja_status) }}</h2>
                    <h6 class="mb-0 text-muted">Pooja Status</h6>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    </div>
</div>


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
                        <strong>Payment Details:</strong>Payment Not Done Yet
                    @endif
                </p>
                <p>
                    @if($booking->payment_status == "paid")
                        <strong>Payment Details:</strong> 
                        {{ $booking->payment->payment_id }} ({{ $booking->payment->payment_method }})
                    @else
                        <strong>Payment Details:</strong>Payment Not Done Yet
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Pandit Details Card -->
    <div class="col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">Pandit Details</div>
            <div class="card-body">
                {{-- <p><strong>Pandit Id:</strong> {{ $pandit_login->pandit_id }}</p> --}}
                <p><strong>Name:</strong> {{ $booking->pandit->title }} {{ $booking->pandit->name }}</p>
                <p><strong>Phone:</strong> {{ $pandit_login->mobile_no }}</p>
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
                @if($booking->ratings && $booking->ratings->isNotEmpty())
                    @foreach($booking->ratings as $rating)
                        <p><strong>Rating:</strong> {{ $rating->rating }}</p>
                        <p><strong>Comment:</strong> {{ $rating->comment }}</p>
                    @endforeach
                @else
                    <p>No ratings available for this booking.</p>
                @endif
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
