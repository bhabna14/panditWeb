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
                  @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                  @endif
              
                  @if(session()->has('success'))
                      <div class="alert alert-success" id="Message">
                          {{ session()->get('success') }}
                      </div>
                  @endif
              
                  @if ($errors->has('danger'))
                      <div class="alert alert-danger" id="Message">
                          {{ $errors->first('danger') }}
                      </div>
                  @endif
              
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


     
    </div>
  </div>

@endsection

@section('scripts')
@endsection