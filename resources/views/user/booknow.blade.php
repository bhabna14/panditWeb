@extends('user.layouts.front')

@section('styles')
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
            <div class="col-md-7">
                <form action="">
                    <div class="row">
                      <div class="col-md-12">
                        @foreach ($addressdata as $index => $addressdata)
                        <div class="your-address">
                          <input type="radio" name="address" id=""><span class="text-blod">{{$addressdata->fullname}}</span>,{{$addressdata->landmark}},{{$addressdata->city}},{{$addressdata->state}},{{$addressdata->country}},{{$addressdata->pincode}},<br>Mbile Number: {{$addressdata->number}}
                        </div>
                        @endforeach
                      </div>
                    </div>
                    <div class="row" style="margin-top:20px">
                      <div class="col-md-4">
                        <a href="#" class="add-address-btn"><i class="fa fa-plus"></i>Add Address</a>
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-input mt-20 col-md-6">
                            <input type="date" required class="form-control">
                            <label class="lh-1 text-16 text-light-1">Date</label>
                        </div>
                        <div class="form-input mt-20 col-md-6">
                            {{-- <input type="date" required class="form-control"> --}}
                            <select id="time" name="time" class="nice-select-dropdown">
                                <option value="00:00">12:00 AM</option>
                                <option value="01:00">1:00 AM</option>
                                <option value="02:00">2:00 AM</option>
                                <option value="03:00">3:00 AM</option>
                                <option value="04:00">4:00 AM</option>
                                <option value="05:00">5:00 AM</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="21:00">9:00 PM</option>
                                <option value="22:00">10:00 PM</option>
                                <option value="23:00">11:00 PM</option>
                            </select>
                            {{-- <label class="lh-1 text-16 text-light-1">Time</label> --}}
                        </div>
                    </div>

                    <button class="button -md -blue-1 bg-dark-3 text-white mt-20">Book Now</button>
                </form>
            </div>
            <div class="col-xl-5 col-lg-5">
                <div class="md:ml-0">
                  <div class="px-30 py-30 border-light rounded-4">
                    <div class="text-20 fw-500 mb-30">Your booking details</div>
    
                    <div class="row x-gap-15 y-gap-20">
                      <div class="col-auto">
                        <img src="{{ asset('front-assets/img/avatars/pandit.jpeg') }}" alt="image" class="size-140 rounded-4 object-cover">
                      </div>
    
                      <div class="col">
                    
    
                        <div class="lh-17 fw-500">P.Bibhu Panda                        </div>
                        <div class="text-14 lh-15 mt-5">Janmasthami Puja</div>
    
                        <div class="row x-gap-10 y-gap-10 items-center pt-10">
                          <div class="col-auto">
                            <div class="d-flex items-center">
                              <div class="size-30 flex-center bg-blue-1 rounded-4">
                                <div class="text-12 fw-600 text-white">4.8</div>
                              </div>
    
                              <div class="text-14 fw-500 ml-10">Exceptional</div>
                            </div>
                          </div>
    
                          <div class="col-auto">
                            <div class="text-14">3,014 reviews</div>
                          </div>
                        </div>
                        <div class="text-16 lh-15 mt-5 fw-600">₹ 300</div>
                      </div>
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
@endsection