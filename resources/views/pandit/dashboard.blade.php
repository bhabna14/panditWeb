@extends('pandit.layouts.app')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert css -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet" />

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <style>
     
     .request-count {
    color: black;
    font-size: 40px;
    font-weight: bold;
    text-align: center;
    margin-top: 5px;
    
}
/* Container for the event */
.fc-h-event {
    width: 30px !important;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    /* background-color: rgb(1, 240, 81); Add background color if needed */
    color: white  !important; /* Text color */
    position: absolute; /* Use absolute positioning */
    top: 50%; /* Center vertically */
    left: 50%; /* Center horizontally */
    transform: translate(-50%, -10%); /* Adjust the element position */
    cursor: pointer;
}
/* .fc-h-event: */


/* Title container styling */
.fc-h-event .fc-event-title-container {
    border-radius: 50%;
    height: 30px;
width: 30px;
    border: none;
    color: white  !important;
    /* background-color: rgb(142, 251, 148); */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 100%; /* Ensure it takes full width */
    cursor: pointer;
}

        /* Calendar Container */
        #calendar {
            width: 90%;
            margin: 0 auto;
            position: relative;
            top: 20px;
        }

        /* Event Count Styling */
        .fc-daygrid-day .event-count {
            background-color: #7e33ff;
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 14px;
            position: absolute;
            top: 5px;
            right: 5px;
            text-align: center;
            font-weight: bold;
            width: 80px;
            box-sizing: border-box;
            z-index: 10;
        }

        /* Event Title Font Size */
  
        .fc-event-title {
        font-size: 14px; /* Adjust font size as needed */
        color: #fff; /* Adjust text color as needed */
        padding: 5px;
    }

        /* Day Number Font Size */
        .fc-daygrid-day-number {
            font-size: 14px;
        }

        /* Day Name Font Size */
        .fc-daygrid-day-top {
            font-size: 14px;
        }
    </style>
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
                <li class="breadcrumb-item active" aria-current="page">Pooja</li>
            </ol>
        </div>
    </div>
 
    <!-- row -->
    <div class="row">

        @if ($completionPercentage < 95)
    <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-9 col-lg-7 col-md-6 col-sm-12">
                        <div class="text-justified align-items-center">
                            <h3 class="text-dark font-weight-semibold mb-2 mt-0">Hi, Welcome Back <span class="text-primary">{{ $panditProfile->name }}!</span></h3>
                            <p class="text-dark tx-14 mb-3 lh-3"> You have used {{ number_format($completionPercentage, 2) }}% of your profile completion. Please complete your profile to access more features.</p>
                            <a class="btn btn-primary shadow" href="{{ url('pandit/manageprofile') }}">Complete Profile</a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-5 col-md-6 col-sm-12 d-flex align-items-center justify-content-center">
                        <div class="chart-circle float-md-end mt-4 mt-md-0" data-value="{{ $completionPercentage / 100 }}" data-thickness="12" data-color="">
                            <canvas width="100" height="100"></canvas>
                            <div class="chart-circle-value circle-style">
                                <div class="tx-18 font-weight-semibold">{{ number_format($completionPercentage, 2) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- today pooja --}}
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
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
        
    
            {{-- pooja calendar --}}

            <div  class="col-xl-7 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="text-center pt-4">
                        <h3 style="font-family: fantasy;letter-spacing: 1px">POOJA CALENDAR</h3>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        <!-- Pooja Request -->
        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
                    <div class="card">
                        <div class="text-center pt-4">
                            <h3 style="font-family: fantasy;letter-spacing: 1px">POOJA REQUEST</h3>
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
                                @if ($request->application_status !== 'approved' && $request->application_status !== 'rejected')
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                        <div class="card text-center p-3">
                                                <h5 class="text-dark font-weight-semibold mb-2">
                                                    {{ $request->pooja->pooja_name ?? 'N/A' }}
                                                </h5>
                            
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
                                                                            <input type="hidden" value="{{ $request->booking_id }}" id="booking_id" name="booking_id">
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
                            
                                                    <a style="color: white; margin-left: -15px" class="btn ripple btn-success view-booking" data-bs-toggle="modal" data-bs-target="#full-screen" data-booking-id="{{ $request->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- pooja summary table --}}
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pooja Summary</h4>
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

    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Pooja Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- The dynamic content will be inserted here by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- /row closed -->
@endsection

@section('scripts')
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

    <!-- SweetAlert js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.all.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            const viewButtons = document.querySelectorAll('.view-booking');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    fetch(`/pandit/booking/details/${bookingId}`)
                        .then(response => response.json())
                        .then(data => {
                            const maskedMobileNumber = maskMobileNumber(data.user?.mobile_number);
                            const maskedName = maskName(data.user?.name);
                            document.getElementById('modal-full-name').textContent = maskedName || 'N/A';
                            document.getElementById('modal-pooja-name').textContent = data.pooja?.pooja_name || 'N/A';
                            document.getElementById('modal-mobile-number').textContent = maskedMobileNumber || 'N/A';
                            document.getElementById('modal-pooja-fee').textContent = data.pooja?.pooja_fee || 'N/A';
                            document.getElementById('modal-paid-amount').textContent = data.paid || 'N/A';
                            document.getElementById('modal-date-time').textContent = data.booking_time || 'N/A';
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
                    if (word.length <= 2) return word;
                    const firstChar = word.charAt(0);
                    const lastChar = word.charAt(word.length - 1);
                    const maskedMiddle = '*'.repeat(word.length - 2);
                    return `${firstChar}${maskedMiddle}${lastChar}`;
                }).join(' ');
            }
        });
    </script>
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
         <script src="{{ asset('assets/plugins/chartjs/Chart.bundle.min.js') }}"></script>

         <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

<script>
 document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Convert the PHP collection to a JavaScript object
    var requestCountMap = @json($requestCounts->toArray()).reduce((map, item) => {
        map[item.date] = { count: item.count, details: item.details };
        return map;
    }, {});

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: function(fetchInfo, successCallback, failureCallback) {
    var events = [];
    for (var date in requestCountMap) {
        var count = requestCountMap[date].count;
        if (count > 0) {
            events.push({
                title:   count,
                start: date,
                allDay: true,
                extendedProps: {
                    details: requestCountMap[date].details
                }
            });
        }
    }
    console.log('Events:', events); // Log the events array
    successCallback(events);
},

eventClick: function(info) {
    var details = info.event.extendedProps.details;

    if (details && details.length > 0) {
        const date = info.event.startStr;
        const url = `/pandit/calender/pooja/${date}`;
        console.log('Fetch URL:', url);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                let modalBodyContent = '';
                let serialNo = 1; // Initialize serial number counter

           data.forEach(booking => {
                 modalBodyContent += `
                        <tr>
                             <th>Sl No</th>
                             <td style="font-weight: bold;font-size: 15px">${serialNo++}</td>
                        </tr>
                        <tr>
                            <th>Pooja Name</th>
                            <td>${booking.pooja?.pooja_name || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Date and Time</th>
                            <td>${booking.booking_time || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Payment Status</th>
                            <td>${booking.payment_status || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>
                                Country: ${booking.address?.country || 'N/A'}<br>
                                State: ${booking.address?.state || 'N/A'}<br>
                                City: ${booking.address?.city || 'N/A'}<br>
                                Area: ${booking.address?.area || 'N/A'}<br>
                                Pincode: ${booking.address?.pincode || 'N/A'}<br>
                                Address Type: ${booking.address?.address_type || 'N/A'}
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                    `;
                });

                document.getElementById('modalBody').innerHTML = `
                    <table class="table table-striped">
                        <tbody>${modalBodyContent}</tbody>
                    </table>`;
            })
            .catch(error => console.error('Error fetching booking details:', error));
    } else {
        console.error('No details available for this event');
    }

    var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    eventModal.show();
}

    });

    calendar.render();
});

</script>

    
@endsection

