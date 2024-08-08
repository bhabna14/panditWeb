@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection
@section('content')

<div class="row row-sm">
 
<div class="col-lg-12 col-xl-12 p-0">
    <div class="row">
        @foreach($complete_pooja as $pooja)
        <div class="col-xl-3 col-lg-6 alert">

            <div class="card item-card ">
                <div class="card-body pb-0">
                    <div class="text-center zoom">
                        <a data-bs-toggle="modal" data-bs-target="#full-screen" data-booking-id="{{ $pooja->id }}">
                            <img class="w-100 br-5" src="{{ asset('assets/img/' . $pooja->poojaList->pooja_photo) }}" alt="img">
                        </a>
                    </div>
                    <div class="card-body px-0 pb-3">
                        <div class="row">
                            <div class="col-10">
                                <div class="cardtitle">
                                    <div>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star-half text-warning fs-16"></i></a>
                                        <a href="javascript:void(0);"><i class="fa fa-star-o text-warning fs-16"></i></a>
                                    </div>
                                    <a class="shop-title fs-18">{{ $pooja->poojaList->pooja_name }}</a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-2">
                                <div class="cardprice-2">
                                    <span class="number-font">{{ $pooja->payment_status}}</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">({{ \Carbon\Carbon::parse($pooja->poojaList->pooja_date)->format('d-m-Y') }})</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0"><span>{{ $pooja->pooja_status ?? 'N/A' }}</span></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>
        @endforeach
        <div class="modal fade" id="full-screen" tabindex="-1" aria-labelledby="fullScreenModalLabel" aria-hidden="true">
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


    <script>
        function addressFunction() {
            if (document.getElementById("same").checked) {
                document.getElementById("peraddress").value = document.getElementById("preaddress").value;
                document.getElementById("perpost").value = document.getElementById("prepost").value;
                document.getElementById("perdistri").value = document.getElementById("predistrict").value;
                document.getElementById("perstate").value = document.getElementById("prestate").value;
                document.getElementById("percountry").value = document.getElementById("precountry").value;
                document.getElementById("perpincode").value = document.getElementById("prepincode").value;
                document.getElementById("perlandmark").value = document.getElementById("prelandmark").value;

            } else {
                document.getElementById("peraddress").value = "";
                document.getElementById("perpost").value = "";
                document.getElementById("perdistri").value = "";
                document.getElementById("perstate").value = "";
                document.getElementById("percountry").value = "";
                document.getElementById("perpincode").value = "";
                document.getElementById("perlandmark").value = "";
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('[data-bs-target="#full-screen"]');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-booking-id');
            
            // Fetch booking details using AJAX
            fetch(`/pandit/booking/details/${bookingId}`)
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

                        document.getElementById('modal-address').innerHTML = `
                            ${data.address?.country || 'N/A'}<br>
                            Pincode: ${data.address?.pincode || 'N/A'}<br>
                            Landmark: ${data.address?.landmark || 'N/A'}
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
@endsection
