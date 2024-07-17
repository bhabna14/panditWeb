@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('saveaddress') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          {{-- <label for="exampleInputEmail1">Full name (First and Last name)</label> --}}
                          <input type="text" class="form-control" id="exampleInputEmail1" value="" name="fullname" placeholder="Enter Your Full Name">
                        </div>
                      </div>
                      <div class="col-md-6">
                      <div class="form-group">
                        {{-- <label for="exampleInputEmail1">Mobile number</label> --}}
                        <input type="text" class="form-control" id="exampleInputEmail1" value="" name="number" placeholder="Enter Mobile number">
                      </div>
                      </div>
                    </div>
                    <div class="row mt-10">
                      <div class="col-md-6">
                        <div class="form-group">
                          {{-- <label for="exampleInputEmail1">Country</label> --}}
                          <select name="country" class="form-control" id="">
                            <option value="India">India</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                      <div class="form-group">
                        {{-- <label for="exampleInputEmail1">State</label> --}}
                        <select name="state" class="form-control" id="">
                          <option value="Odisha">Odisha</option>
                        </select>
                      </div>
                      </div>
                    </div>
                    <div class="row mt-10">
                      <div class="col-md-6">
                        <div class="form-group">
                          {{-- <label for="exampleInputEmail1">Town/City   </label> --}}
                          <input type="text" class="form-control" id="exampleInputEmail1" value="" name="city" placeholder="Enter Town/City">
                        </div>
                      </div>
                      <div class="col-md-6">
                      <div class="form-group">
                        {{-- <label for="exampleInputEmail1">Pincode</label> --}}
                        <input type="text" class="form-control" id="exampleInputEmail1" value="" name="pincode" placeholder="Enter Pincode">
                      </div>
                      </div>
                    </div>

                    <div class="row mt-10">
                      
                      <div class="col-md-12">
                      <div class="form-group">
                        {{-- <label for="exampleInputEmail1">Area, Street</label> --}}
                        <textarea name="area" class="form-control" id=""  rows="15" placeholder="Enter Area, Street, Sector, Village"></textarea>
                        {{-- <input type="text" class="form-control" id="exampleInputEmail1" value="" name="area" placeholder="Enter Area, Street, Sector, Village"> --}}
                      </div>
                      </div>
                    </div>
                    

                    <div class="row mt-10">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="exampleInputEmail1">Address Type</label>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <label class="rdiobox"><input name="address_type" type="radio" value="Home"> <span>Home</span></label>
                      </div>
                      <div class="col-lg-2">
                          <label class="rdiobox"><input name="address_type" type="radio" value="Work"> <span>Work</span></label>
                      </div>
                      <div class="col-lg-2">
                        <label class="rdiobox"><input checked name="address_type" type="radio" value="Other"> <span>Other</span></label>
                    </div>
                    </div>
                   
                  
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
</section>
@endsection

@section('scripts')
@endsection
