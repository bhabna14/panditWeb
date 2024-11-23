@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
      <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-32">
        <div class="col-auto">

          <h1 class="text-30 lh-14 fw-600">Edit Address</h1>
          
        </div>

        <div class="col-auto">

        </div>
      </div>


      <div class="py-20 px-30 rounded-4 bg-white shadow-3">
        <div class="tabs -underline-2 js-tabs">
          

          <div class="tabs__content pt-10 js-tabs-content">
            <div class="tabs__pane -tab-item-1 is-tab-el-active">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
              
                <form action="{{ route('updateuseraddress') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $address->id }}">
                
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="place_category">Type</label>
                            </div>
                        </div>
                        @php
                            $placeCategory = $address->place_category;
                        @endphp
                        <div class="col-md-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="individual" name="place_category" value="Indivisual" {{ $placeCategory == 'Indivisual' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="individual"><span class="custom-radio"></span> Individual</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="apartment" name="place_category" value="Apartment" {{ $placeCategory == 'Apartment' ? 'checked' : '' }}>
                                <label class="form-check-label" for="apartment"><span class="custom-radio"></span> Apartment</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="business" name="place_category" value="Business" {{ $placeCategory == 'Business' ? 'checked' : '' }}>
                                <label class="form-check-label" for="business"><span class="custom-radio"></span> Business</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="temple" name="place_category" value="Temple" {{ $placeCategory == 'Temple' ? 'checked' : '' }}>
                                <label class="form-check-label" for="temple"><span class="custom-radio"></span> Temple</label>
                            </div>
                        </div>
                    </div>
                
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="apartment_flat_plot" class="form-label">Apartment/Flat/Plot</label>
                            <input type="text" class="form-control" id="apartment_flat_plot" name="apartment_flat_plot" placeholder="Enter details" value="{{ $address->apartment_flat_plot }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="landmark" class="form-label">Landmark</label>
                            <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Enter landmark" value="{{ $address->landmark }}" required>
                        </div>
                    </div>
                
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="locality" class="form-label">Locality</label>
                            <select class="form-control" id="locality" name="locality" required>
                                <option value="">Select Locality</option>
                                @foreach($localities as $locality)
                                    <option value="{{ $locality->unique_code }}" data-pincode="{{ $locality->pincode }}" {{ $address->locality == $locality->unique_code ? 'selected' : '' }}>
                                        {{ $locality->locality_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter pincode" value="{{ $address->pincode }}" required pattern="\d{6}" readonly>
                        </div>
                    </div>
                
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">Town/City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter Town/City *" value="{{ $address->city }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state">State</label>
                                <select name="state" class="form-control">
                                    <option value="Odisha" {{ $address->state == 'Odisha' ? 'selected' : '' }}>Odisha</option>
                                </select>
                            </div>
                        </div>
                    </div>
                
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address_type">Address Type</label>
                            </div>
                        </div>
                        @php
                            $addressType = $address->address_type;
                        @endphp
                        <div class="col-md-2">
                            <label class="rdiobox"><input name="address_type" type="radio" value="Home" {{ $addressType == 'Home' ? 'checked' : '' }}> <span>Home</span></label>
                        </div>
                        <div class="col-md-2">
                            <label class="rdiobox"><input name="address_type" type="radio" value="Work" {{ $addressType == 'Work' ? 'checked' : '' }}> <span>Work</span></label>
                        </div>
                        <div class="col-md-2">
                            <label class="rdiobox"><input name="address_type" type="radio" value="Other" {{ $addressType == 'Other' ? 'checked' : '' }}> <span>Other</span></label>
                        </div>
                    </div>
                
                    <div class="d-inline-block pt-30">
                        <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                            Update Address<div class="icon-arrow-top-right ml-15"></div>
                        </button>
                    </div>
                </form>
                
          </div>
        </div>
      </div>


     
    </div>
  </div>

@endsection

@section('scripts')
@endsection