@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
      <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-32">
        <div class="col-auto">

          <h1 class="text-30 lh-14 fw-600">Add Address</h1>
          
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
              
                  <form action="{{ route('updateaddress') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $address->id }}">
                  
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="country" class="form-control">
                                    <option value="India" {{ $address->country == 'India' ? 'selected' : '' }}>India</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="state" class="form-control">
                                    <option value="Odisha" {{ $address->state == 'Odisha' ? 'selected' : '' }}>Odisha</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="city" value="{{ $address->city }}" placeholder="Enter Town/City">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="pincode" value="{{ $address->pincode }}" placeholder="Enter Pincode">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea name="area" class="form-control" rows="15" placeholder="Enter Area, Street, Sector, Village">{{ $address->area }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address_type">Address Type</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="rdiobox"><input name="address_type" type="radio" value="Home" {{ $address->address_type == 'Home' ? 'checked' : '' }}> <span>Home</span></label>
                        </div>
                        <div class="col-lg-2">
                            <label class="rdiobox"><input name="address_type" type="radio" value="Work" {{ $address->address_type == 'Work' ? 'checked' : '' }}> <span>Work</span></label>
                        </div>
                        <div class="col-lg-2">
                            <label class="rdiobox"><input name="address_type" type="radio" value="Other" {{ $address->address_type == 'Other' ? 'checked' : '' }}> <span>Other</span></label>
                        </div>
                    </div>
                    <div class="d-inline-block pt-30">
                        <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                            Save Address<div class="icon-arrow-top-right ml-15"></div>
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