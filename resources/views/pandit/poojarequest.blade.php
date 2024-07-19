@extends('pandit.layouts.app')

@section('styles')
    <!-- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12 mt-4">
            <div class="card custom-card overflow-hidden">
                <div style="border-bottom:1px solid rgb(219, 30, 30);text-align:center;margin: 15px">
                    <h3>POOJA REQUEST</h3>
                </div> 
                <div class="card-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sl</th>
                                    <th class="border-bottom-0">Name</th>
                                    <th class="border-bottom-0">Pooja Name</th>
                                    <th class="border-bottom-0">Mobile No.</th>
                                    <th class="border-bottom-0">Total Price</th>
                                    <th class="border-bottom-0">Total Paid</th>
                                    <th class="border-bottom-0">Location</th>
                                    <th class="border-bottom-0">Date & Time</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $index => $booking)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $booking->address->fullname ?? 'N/A' }}</td>
                                    <td>{{ $booking->pooja->pooja_name }}</td>
                                    <td>{{ $booking->address->number ?? 'N/A' }}</td>
                                    <td>₹ {{ $booking->pooja->pooja_fee ?? 'N/A' }}</td>
                                    <td>₹ {{ $booking->paid ?? 'N/A' }}</td>
                                    <td> {{ $booking->address->area ?? 'N/A' }},{{ $booking->address->city ?? 'N/A' }},{{ $booking->address->state ?? 'N/A' }}
                                        {{ $booking->address->country ?? 'N/A' }}<br>
                                        Pincode : {{ $booking->address->pincode ?? 'N/A' }}<br>
                                        Landmark : {{ $booking->address->landmark ?? 'N/A' }}</td>
                                    <td>{{ $booking->booking_date }} {{ $booking->booking_time }}</td>
                                    <td>
                                        @if($booking->application_status === 'pending')
                                        <form action="{{ route('pandit.booking.approve', $booking->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>
                                        <form action="{{ route('pandit.booking.reject', $booking->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </form>
                                        @elseif($booking->application_status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                        @elseif($booking->application_status === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                       
                                        @elseif($booking->application_status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
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

@endsection

@section('scripts')
    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>

    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
