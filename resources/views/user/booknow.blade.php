@extends('user.layouts.front')

@section('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>
    .ui-datepicker {
        font-size: 16px;
    }
    .form-control {
        cursor: pointer;
    }
    .ui-datepicker td a {
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <div class="sc-gJqsIT bdDCMj logo" height="6rem" width="30rem">
                    <div class="low-res-container">
                    </div>
                </div>
                <h1 class="sc-7kepeu-0 kYnyFA description">BOOK NOW</h1>
            </div>
        </div>
</section>
<section class="booking-form mt-30 mb-30">
  <div class="container">
      <div class="row">
          <h4 class="mb-20">Book Now</h4>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
            @endforeach
          <div class="col-md-7">
              <form action="{{ route('booking.confirm') }}" method="POST">
                  @csrf
                  <input type="hidden" name="pandit_id" value="{{ $pandit->id }}">
                  <input type="hidden" name="pooja_id" value="{{ $pooja->id }}">
                  <input type="hidden" name="pooja_fee" value="{{ $poojaFee }}">
                  <input type="hidden" class="form-control" name="advance_fee" value="{{ $poojaFee * 20 / 100 }}">
                  <div class="row">
                      <div class="col-md-12">
                          @foreach ($addresses as $address)
                              <div class="your-address">
                                  <input type="radio" name="address_id" id="address{{ $address->id }}" value="{{ $address->id }}" required>
                                  <label for="address{{ $address->id }}">
                                      {{ $address->fullname }}, {{ $address->landmark }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country }}, {{ $address->pincode }}<br>
                                      Mobile Number: {{ $address->number }}
                                  </label>
                              </div>
                          @endforeach
                      </div>
                  </div>
                  <div class="row" style="margin-top:20px">
                      <div class="col-md-4">
                          <a href="{{route('addfrontaddress')}}" class="add-address-btn"><i class="fa fa-plus"></i> Add Address</a>
                      </div>
                  </div>
                  <div class="row">
                        <div class="form-input mt-20 col-md-12">
                            <input type="text" name="booking_date" required class="form-control" id="booking_date"  placeholder="Select a date and time">
                            {{-- <label class="lh-1 text-16 text-light-1">Date</label> --}}
                        </div>
                      
                  </div>
                  <button type="submit" class="button -md -blue-1 bg-dark-3 text-white mt-20">Confirm Booking</button>
              </form>
          </div>
          <div class="col-xl-5 col-lg-5">
              <div class="md:ml-0">
                  <div class="px-30 py-30 border-light rounded-4">
                      <div class="text-20 fw-500 mb-30">Your booking details</div>
                      <div class="row x-gap-15 y-gap-20">
                          <div class="col-auto">
                              <!-- Display pandit's photo (example) -->
                              <img src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image" class="size-140 rounded-4 object-cover">
                          </div>
                          <div class="col">
                              <div class="lh-17 fw-500">{{ $pandit->name }}</div>
                              <input type="hidden" class="form-control" name="pandit_id" value="{{ $pandit->pandit_id }}">
                              <div class="text-14 lh-15 mt-5">{{ $pooja->poojalist->pooja_name }}</div>
                              {{-- <div class="text-16 lh-15 mt-5 fw-600">₹ {{ $pooja->poojalist->pooja_fee }}</div> --}}
                              <div class="text-16 lh-15 mt-5 fw-600">Total Fee: ₹{{ $poojaFee }}</div>
                              <div class="text-16 lh-15 mt-5 fw-600">Advance Fee: ₹{{ $poojaFee * 20/100 }}</div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>



@endsection

@section('scripts')
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script>
    $(function() {
        var today = new Date();
        var maxDate = new Date();
        maxDate.setMonth(today.getMonth() + 2);

        // Initialize the date-time picker with the desired format and restrictions
        $("#booking_date").datetimepicker({
            format: "Y-m-d H:i", // Format the date and time as YYYY-MM-DD HH:MM
            step: 30, // Step time in minutes
            minDate: today, // Disable past dates
            maxDate: maxDate // Disable dates more than two months from today
        });
    });
</script>


@endsection
