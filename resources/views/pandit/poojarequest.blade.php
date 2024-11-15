@extends('pandit.layouts.app')

@section('styles')
    <!-- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Row -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">POOJA REQUEST</span>
        </div>
        <div class="justify-content-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </div>
    </div>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sl</th>
                                    <th class="border-bottom-0">View</th>
                                    <th class="border-bottom-0">Pooja Name</th>
                                    <th class="border-bottom-0">Date & Time</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $index => $booking)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a style="color: white" class="btn ripple btn-primary view-booking" data-bs-toggle="modal" data-bs-target="#full-screen" data-booking-id="{{ $booking->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                    <td>{{ $booking->pooja->pooja_name ?? 'N/A' }}</td>
                                    <td>{{ $booking->booking_date }} {{ $booking->booking_time }}</td>
                                    <td>
                                        @if($booking->application_status === 'pending')
                                        <form action="{{ route('pandit.booking.approve', $booking->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>
                                        <form action="{{ route('pandit.booking.reject', $booking->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#exampleModal-{{ $booking->id }}" data-bs-whatever="@mdo">Reject</button>
                                
                                            <div class="modal fade" id="exampleModal-{{ $booking->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Please Select Your Reason</h5>
                                                            <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" value="{{ $booking->booking_id }}" id="booking_id" name="booking_id">
                                                            <div class="mb-3">
                                                                <label for="cancel_reason" class="col-form-label">Cancel Reason:</label>
                                                                <select class="form-control" id="cancel_reason" name="cancel_reason" required>
                                                                    <option value="">Select Reason</option>
                                                                    <option value="I am not free at this time">I am not available at this time</option>
                                                                    <option value="I am not available in this city">I am not available in this city</option>
                                                                    <option value="Personal problem">Personal problem</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-danger">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                
                                        @elseif($booking->application_status === 'approved')
                                        <span class="btn btn-success">Approved</span>
                                        @elseif($booking->application_status === 'paid')
                                        <span class="btn btn-success">Paid</span>
                                        @elseif($booking->application_status === 'rejected')
                                        <span class="btn btn-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->

    {{-- Modal --}}
    <div class="modal fade" id="full-screen" tabindex="-1" aria-labelledby="fullScreenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" role="document" style="width: 1200px">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">Pooja Request</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ">
                       
                        <tbody>
                            <tr>
                                <th>Full Name</th>
                                <td id="modal-full-name">N/A</td>
                            </tr>
                            <tr>
                                <th>Pooja Name</th>
                                <td id="modal-pooja-name">N/A</td>
                            </tr>
                            <tr>
                                <th>Mobile Number</th>
                                <td id="modal-mobile-number">N/A</td>
                            </tr>
                            <tr>
                                <th>Pooja Fee</th>
                                <td>₹ <span id="modal-pooja-fee">N/A</span></td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td><span id="modal-payment-status">N/A</span></td>
                            </tr>
                            <tr>
                                <th>Date and Time</th>
                                <td id="modal-date-time">N/A</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td id="modal-address">N/A</td>
                            </tr>
                            <tr>
                                <th>Pooja Status</th>
                                <td id="modal-pooja-status">N/A</td>
                            </tr>
                            
                           
                        </tbody>
                    </table>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>

    <script>
 document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.view-booking');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-booking-id');
            
            // Fetch booking details using AJAX
            fetch(`/pandit/booking/details/${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    // Mask the mobile number and name
                    const maskedMobileNumber = maskMobileNumber(data.user?.mobile_number);
                    const maskedName = maskName(data.user?.name);
                    
                    // Update modal content
                    document.getElementById('modal-full-name').textContent = maskedName || 'N/A';
                    document.getElementById('modal-pooja-name').textContent = data.pooja?.pooja_name || 'N/A';
                    document.getElementById('modal-mobile-number').textContent = maskedMobileNumber || 'N/A';
                    document.getElementById('modal-pooja-fee').textContent = data.pooja?.pooja_fee || 'N/A';
                    document.getElementById('modal-payment-status').textContent = data.payment_status || 'N/A';
                    document.getElementById('modal-date-time').textContent = data.booking_time || 'N/A';
                    document.getElementById('modal-pooja-status').textContent = data.pooja_status || 'N/A';
                    

                    document.getElementById('modal-address').innerHTML = `
                                Country: ${data.address?.country || 'N/A'}<br>
                                State: ${data.address?.state || 'N/A'}<br>                                    
                                City: ${data.address?.city || 'N/A'}<br>
                                Area: ${data.address?.area || 'N/A'}<br>
                                Pincode: ${data.address?.pincode || 'N/A'}<br>
                                Address Type: ${data.address?.address_type || 'N/A'}<br>
                            `;
                })
                .catch(error => console.error('Error fetching booking details:', error));
        });
    });

    function maskMobileNumber(mobileNumber) {
        if (!mobileNumber) return 'N/A';
        return mobileNumber.slice(0, 5) + '*****';
    }

    function maskName(name) {
        if (!name) return 'N/A';
        
        return name.split(' ').map(word => {
            if (word.length <= 2) return word; // Handle short words
            
            const firstChar = word.charAt(0);
            const lastChar = word.charAt(word.length - 1);
            const maskedMiddle = '*'.repeat(word.length - 2);

            return `${firstChar}${maskedMiddle}${lastChar}`;
        }).join(' ');
    }
});
    </script>
@endsection
