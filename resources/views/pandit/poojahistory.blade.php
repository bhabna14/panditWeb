@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
   
@endsection

@section('content')

    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">POOJA HISTORY</span>
        </div>
        <div class="justify-content-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">history</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12 col-xl-12 p-0">
            <div class="row">
                @foreach ($complete_pooja as $pooja)
                    <div class="col-xl-3 col-lg-6 alert">

                        <div class="card item-card ">
                            <div class="card-body pb-0">
                                <div class="text-center zoom">
                                    <a data-bs-toggle="modal" data-bs-target="#full-screen"
                                        data-booking-id="{{ $pooja->id }}">
                                        <img class="w-100 br-5"
                                            src="{{ asset('assets/img/' . $pooja->poojaList->pooja_photo) }}"
                                            alt="img">
                                    </a>
                                </div>
                                <div class="card-body px-0 pb-3">
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="cardtitle">
                                                <div class="rating-container">
                                                    <div class="rating">
                                                        <input type="radio" id="star5_{{ $pooja->booking_id }}" name="rating_{{ $pooja->booking_id }}" value="5" {{ isset($pooja->rating) && $pooja->rating->rating == 5 ? 'checked' : '' }}>
                                                        <label for="star5_{{ $pooja->booking_id }}"></label>
                                                        <input type="radio" id="star4_{{ $pooja->booking_id }}" name="rating_{{ $pooja->booking_id }}" value="4" {{ isset($pooja->rating) && $pooja->rating->rating == 4 ? 'checked' : '' }}>
                                                        <label for="star4_{{ $pooja->booking_id }}"></label>
                                                        <input type="radio" id="star3_{{ $pooja->booking_id }}" name="rating_{{ $pooja->booking_id }}" value="3" {{ isset($pooja->rating) && $pooja->rating->rating == 3 ? 'checked' : '' }}>
                                                        <label for="star3_{{ $pooja->booking_id }}"></label>
                                                        <input type="radio" id="star2_{{ $pooja->booking_id }}" name="rating_{{ $pooja->booking_id }}" value="2" {{ isset($pooja->rating) && $pooja->rating->rating == 2 ? 'checked' : '' }}>
                                                        <label for="star2_{{ $pooja->booking_id }}"></label>
                                                        <input type="radio" id="star1_{{ $pooja->booking_id }}" name="rating_{{ $pooja->booking_id }}" value="1" {{ isset($pooja->rating) && $pooja->rating->rating == 1 ? 'checked' : '' }}>
                                                        <label for="star1_{{ $pooja->booking_id }}"></label>
                                                    </div>
                                                </div>
                                                <h5 class="shop-title fs-18">{{ $pooja->poojaList->pooja_name }}</h5>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="col-2">
                                            <div class="cardprice-2">
                                                <span class="number-font">{{ $pooja->payment_status }}</span>
                                            </div>
                                        </div>
                                        <div style="text-align: center;width: 100%">
                                            <h4 class="shop-description fs-13 text-muted mt-2 mb-0">
                                                ({{ \Carbon\Carbon::parse($pooja->poojaList->pooja_date)->format('d-m-Y') }})
                                            </h4>
                                            <h6 class="shop-description fs-13 text-muted mt-2 mb-0"
                                                style="font-weight: bold">
                                                <span>{{ $pooja->pooja_status ?? 'N/A' }}</span>
                                            </h6>
                                            <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration -
                                                <span>{{ $pooja->poojaStatus->pooja_duration ?? 'N/A' }}</span></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
                
                @foreach ($all_poojas as $booking)
                    @if (!$booking->status || !$booking->status->end_time)
                        <div class="col-xl-3 col-lg-6 alert">
                            <div class="card item-card">
                                <div class="card-body pb-0">
                                    <div class="text-center zoom">
                                        <a href="#"><img class="w-100 br-5"
                                                src="{{ asset('assets/img/' . $booking->poojaList->pooja_photo) }}"
                                                alt="img"></a>
                                    </div>
                                    <div class="card-body px-0 pb-3">
                                        <div class="row">
                                            <h5 class="text-dark font-weight-semibold mb-2">
                                                {{ $booking->pooja_name }}
                                                ({{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i') }})
                                            </h5>
                                            <div class="d-flex justify-content-center align-items-center"
                                                style="margin-left:70px">
                                                @if ($booking->status)
                                                    @if ($booking->status->start_time && !$booking->status->end_time)
                                                        <!-- If started but not ended, show End button -->
                                                        <form action="{{ route('pooja.end') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="booking_id"
                                                                value="{{ $booking->booking_id }}">
                                                            <input type="hidden" name="pooja_id"
                                                                value="{{ $booking->pooja_id }}">
                                                            <button type="submit" class="btn btn-success mb-2">End</button>
                                                        </form>
                                                    @elseif (!$booking->status->start_time)
                                                        <!-- If not started, show Start button -->
                                                        <form action="{{ route('pooja.start') }}" method="POST"
                                                            class="mr-2">
                                                            @csrf
                                                            <input type="hidden" name="booking_id"
                                                                value="{{ $booking->booking_id }}">
                                                            <input type="hidden" name="pooja_id"
                                                                value="{{ $booking->pooja_id }}">
                                                            <button type="submit"
                                                                class="btn btn-primary mb-2">Start</button>
                                                        </form>
                                                    @else
                                                        <!-- If started and ended, show Completed button -->
                                                        <button class="btn btn-secondary mb-2" disabled>Pooja
                                                            Completed</button>
                                                    @endif
                                                @else
                                                    <!-- If no status record, show both Start and End buttons -->
                                                    <form action="{{ route('pooja.start') }}" method="POST"
                                                        class="mr-2">
                                                        @csrf
                                                        <input type="hidden" name="booking_id"
                                                            value="{{ $booking->booking_id }}">
                                                        <input type="hidden" name="pooja_id"
                                                            value="{{ $booking->pooja_id }}">
                                                        <button type="submit" class="btn btn-primary mb-2">Start</button>
                                                    </form>
                                                    <form action="{{ route('pooja.end') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="booking_id"
                                                            value="{{ $booking->booking_id }}">
                                                        <input type="hidden" name="pooja_id"
                                                            value="{{ $booking->pooja_id }}">
                                                        <button type="submit" class="btn btn-success mb-2"
                                                            style="margin-left: 10px">End</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="modal fade" id="full-screen" tabindex="-1" aria-labelledby="fullScreenModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document" style="width: 1200px">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Complete Pooja</h6>
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
                        <tr>
                            <th>Feedback Message</th>
                            <td id="modal-feedback-message">N/A</td>
                        </tr>
                        <tr>
                            <th>Review Image</th>
                            <td>
                                <img id="modal-image" src="" alt="Feedback Image" style="display: none; width: 60px; height: 70px; margin-top: 10px;" />
                            </td>
                        </tr>
                        <tr>
                            <th>Review Audio</th>
                            <td>
                                <audio id="modal-audio" controls style="display: none; width: 100%;">
                                    <source id="audio-source" src="" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </td>
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

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Internal Select2 js-->


   
    <script>
     document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('[data-bs-target="#full-screen"]');

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');

            // Fetch booking details using AJAX
            fetch(`${baseUrl}/pandit/booking/details/${bookingId}`)  // Using the dynamic base URL
                .then(response => response.json())
                .then(data => {
                    if (data) {
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
                        document.getElementById('modal-feedback-message').textContent = data.review || 'No feedback available';

                        // Handle image display
                        const modalImageElement = document.getElementById('modal-image');
                        if (data.image_path) {
                            modalImageElement.src = `${baseUrl}/storage/${data.image_path}`;  // Using dynamic base URL
                            modalImageElement.style.display = 'block';
                        } else {
                            modalImageElement.style.display = 'none';
                        }

                        // Handle audio display
                        const modalAudioElement = document.getElementById('modal-audio');
                        const audioSourceElement = document.getElementById('audio-source');
                        if (data.audio_path) {
                            audioSourceElement.src = `${baseUrl}/storage/${data.audio_path}`;  // Using dynamic base URL
                            modalAudioElement.style.display = 'block';
                            modalAudioElement.load(); // Reload audio element with new source
                        } else {
                            modalAudioElement.style.display = 'none';
                        }

                        document.getElementById('modal-address').innerHTML = `
                            Country: ${data.address?.country || 'N/A'}<br>
                            State: ${data.address?.state || 'N/A'}<br>
                            City: ${data.address?.city || 'N/A'}<br>
                            Area: ${data.address?.area || 'N/A'}<br>
                            Pincode: ${data.address?.pincode || 'N/A'}<br>
                            Address Type: ${data.address?.address_type || 'N/A'}<br>
                        `;
                    }
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
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>


    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
       <!-- Include your JavaScript files, or scripts can go here -->
       <script>
        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    </script>

@endsection
