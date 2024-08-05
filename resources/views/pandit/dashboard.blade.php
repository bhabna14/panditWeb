@extends('pandit.layouts.app')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sales</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->
    @if (session('success'))
        <div class="alert alert-success" id ="Message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" id ="Message">
            {{ session('error') }}
        </div>
    @endif

    <!-- row -->
    <div class="row">
        <!-- Bookings Section -->
        <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
                    <div class="card">
                        <div class="text-center pt-4">
                            <h3
                                style="text-shadow: 8px 8px 20px rgba(0,0,0,0.7);font-weight: bold; font-family: Copperplate, Papyrus, fantasy; font-size: 40px">
                                {{ $today }}
                            </h3>
                            <h3 style="text-shadow: 3px 3px 10px rgba(0,0,0,0.4);color: #B7070A;font-family: 'Trebuchet MS', sans-serif; font-size: 20px;"
                                id="liveTime"></h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($bookings as $booking)
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                    <div class="card text-center p-3">
                                        <h5 class="text-dark font-weight-semibold mb-2">
                                            {{ $booking->pooja_name }}
                                            ({{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i') }})
                                        </h5>
                                        <div class="d-flex justify-content-center">
                                            @if ($booking->status)
                                                @if ($booking->status->start_time && !$booking->status->end_time)
                                                    <!-- If started but not ended, show End button -->
                                                    <form action="{{ route('pooja.end') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                                                        <input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
                                                        <button type="submit" class="btn btn-success mb-2">End</button>
                                                    </form>
                                                @elseif (!$booking->status->start_time)
                                                    <!-- If not started, show Start button -->
                                                    <form action="{{ route('pooja.start') }}" method="POST" class="mr-2">
                                                        @csrf
                                                        <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                                                        <input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
                                                        <button type="submit" class="btn btn-primary mb-2">Start</button>
                                                    </form>
                                                @else
                                                    <!-- If started and ended, show Completed button -->
                                                    <button class="btn btn-secondary mb-2" disabled>Pooja Completed</button>
                                                @endif
                                            @else
                                                <!-- If no status record, show both Start and End buttons -->
                                                <form action="{{ route('pooja.start') }}" method="POST" class="mr-2">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                                                    <input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
                                                    <button type="submit" class="btn btn-primary mb-2">Start</button>
                                                </form>
                                                <form action="{{ route('pooja.end') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                                                    <input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
                                                    <button type="submit" class="btn btn-success mb-2" style="margin-left: 10px">End</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Profile Section -->

        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
                    <div class="card">
                        <div class="text-center pt-4">
                            <h3>POOJA REQUEST</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($pooja_request->isEmpty())
                                    <div class="col-12">
                                        <div class="alert alert-warning text-center">
                                            No pooja request found.
                                        </div>
                                    </div>
                                @else
                                    @foreach ($pooja_request as $request)
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                            <div class="card text-center p-3">
                                                @if ($request->application_status === 'pending')
                                                <h5 class="text-dark font-weight-semibold mb-2">
                                                    {{ $request->pooja->pooja_name }}
                                                </h5>
                                                @endif

                                                <div class="d-flex justify-content-center">
                                                    @if ($request->application_status === 'pending')
                                                        <div class="btn-group" role="group" aria-label="Action Buttons">
                                                            <form action="{{ route('pandit.booking.approve', $request->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-info">Approve</button>
                                                            </form>
                                                            <form action="{{ route('pandit.booking.reject', $request->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="button" class="btn btn-warning me-3" data-bs-toggle="modal" data-bs-target="#exampleModal-{{ $request->id }}" data-bs-whatever="@mdo">Reject</button>
                                                                <div class="modal fade" id="exampleModal-{{ $request->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="exampleModalLabel">Please Select Your Reason</h5>
                                                                                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <input type="hidden" value="{{ $request->id }}" id="booking_id" name="booking_id">
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
                                                        </div>
                                                    @elseif($request->application_status === 'approved')
                                                        <span class="btn btn-success">Approved</span>
                                                    @elseif($request->application_status === 'paid')
                                                        <span class="btn btn-success">Paid</span>
                                                    @elseif($request->application_status === 'rejected')
                                                        <span class="btn btn-danger">Rejected</span>
                                                    @endif

                                                    @if ($request->application_status === 'pending')

                                                    <a style="color: white; margin-left: 8px" class="btn ripple btn-success view-booking" data-bs-toggle="modal" data-bs-target="#full-screen" data-booking-id="{{ $request->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- </div> -->
    </div>
    <!-- row closed -->

    <!-- row  -->
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product Summary</h4>
                </div>
                <div class="card-body pt-0 example1-table">
                    <div class="table-responsive">
                        <table class="table  table-bordered text-nowrap mb-0" id="example1">
                            <thead>
                                <tr>
                                    <th>Slno</th>
                                    <th>Pooja Name</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Pooja Duration</th>
                                    <th>Pooja Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pooja_status as $index => $status)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $status->pooja_name }}</td>
                                        <td>{{ $status->start_time ? \Carbon\Carbon::parse($status->start_time)->format('Y-m-d H:i:s') : 'Not Started' }}
                                        </td>
                                        <td>{{ $status->end_time ? \Carbon\Carbon::parse($status->end_time)->format('Y-m-d H:i:s') : 'Not Ended' }}
                                        </td>
                                        <td>{{ $status->pooja_duration }}</td>
                                        <td>{{ $status->pooja_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


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
                                <th>Paid Amount</th>
                                <td>₹ <span id="modal-paid-amount">N/A</span></td>
                            </tr>
                            <tr>
                                <th>Date and Time</th>
                                <td id="modal-date-time">N/A</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td id="modal-address">N/A</td>
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
    <!-- /row closed -->
@endsection

@section('scripts')
    <script>
        function updateTime() {
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');
            var formattedTime = hours + ':' + minutes + ':' + seconds;
            document.getElementById('liveTime').innerText = formattedTime;
        }

        setInterval(updateTime, 1000); // Update every second
        updateTime(); // Initial call to set the time immediately
    </script>
    <!-- Internal Chart.Bundle js-->
    <script src="{{ asset('assets/plugins/chartjs/Chart.bundle.min.js') }}"></script>

    <!-- Moment js -->
    <script src="{{ asset('assets/plugins/raphael/raphael.min.js') }}"></script>

    <!-- INTERNAL Apexchart js -->
    <script src="{{ asset('assets/js/apexcharts.js') }}"></script>

    <!--Internal Sparkline js -->
    <script src="{{ asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

    <!--Internal  index js -->
    <script src="{{ asset('assets/js/index.js') }}"></script>

    <!-- Chart-circle js -->
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script>

    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewButtons = document.querySelectorAll('.view-booking');

            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');

                    // Fetch booking details using AJAX
                    fetch(`/pandit/booking/details/${bookingId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Mask the mobile number and name
                            const maskedMobileNumber = maskMobileNumber(data.user
                                ?.mobile_number);
                            const maskedName = maskName(data.user?.name);

                            // Update modal content
                            document.getElementById('modal-full-name').textContent =
                                maskedName || 'N/A';
                            document.getElementById('modal-pooja-name').textContent = data.pooja
                                ?.pooja_name || 'N/A';
                            document.getElementById('modal-mobile-number').textContent =
                                maskedMobileNumber || 'N/A';
                            document.getElementById('modal-pooja-fee').textContent = data.pooja
                                ?.pooja_fee || 'N/A';
                            document.getElementById('modal-paid-amount').textContent = data
                                .paid || 'N/A';
                            document.getElementById('modal-date-time').textContent = data
                                .booking_time || 'N/A';
                            document.getElementById('modal-address').innerHTML = `
                            Area: ${data.address?.area || 'N/A'}<br>
                            City: ${data.address?.city || 'N/A'}<br>
                            State: ${data.address?.state || 'N/A'}<br>
                            Pincode: ${data.address?.pincode || 'N/A'}<br>
                        ${data.address?.country || 'N/A'}<br>
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
