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
                                    <th class="border-bottom-0">Mobile No.</th>
                                    <th class="border-bottom-0">Adv. Price</th>
                                    <th class="border-bottom-0">Location</th>
                                    <th class="border-bottom-0">Date</th>
                                    <th class="border-bottom-0">Time</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td>{{ $booking->user->name }}</td> --}}
                                        {{-- <td>{{ $booking->user->phonenumber }}</td> --}}
                                        <td>{{ $booking->pooja_fee }}</td>
                                        {{-- <td>{{ $booking->user->address }}</td> --}}
                                        <td>{{ $booking->booking_date }}</td>
                                        <td>{{ $booking->booking_time }}</td>
                                        <td>
                                            @if ($booking->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif ($booking->status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
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
