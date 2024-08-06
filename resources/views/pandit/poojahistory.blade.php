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
                        <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/' . $pooja->poojaList->pooja_photo) }}" alt="img"></a>
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
                                    <span class="number-font">{{ $pooja->paid ?? '00.00'  }}</span>
                                </div>
                            </div>
                            <div style="text-align: center;width: 100%">
                                <h4 class="shop-description fs-13 text-muted mt-2 mb-0">({{ \Carbon\Carbon::parse($pooja->poojaList->pooja_date)->format('d-m-Y') }})</h4>
                                <h6 class="shop-description fs-13 text-muted mt-2 mb-0">Duration - <span>{{ $pooja->poojaStatus->pooja_duration ?? 'N/A' }}</span></h6>
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
                            <a href="#"><img class="w-100 br-5" src="{{ asset('assets/img/' . $booking->poojaList->pooja_photo) }}" alt="img"></a>
                        </div>
                        <div class="card-body px-0 pb-3">
                            <div class="row">
                                <h5 class="text-dark font-weight-semibold mb-2">
                                    {{ $booking->pooja_name }}
                                    ({{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i') }})
                                </h5>
                                <div class="d-flex justify-content-center align-items-center" style="margin-left:70px">
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
                    </div>
                </div>
            </div>
        @endif
    @endforeach
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
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-profile.js') }}"></script>


    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
