@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
      <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-32">
        <div class="col-auto">

          <h1 class="text-30 lh-14 fw-600">Profile</h1>
         
        </div>

        <div class="col-auto">

        </div>
      </div>


      <div class="py-30 px-30 rounded-4 bg-white shadow-3">
        <div class="tabs -underline-2 js-tabs">
          <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">

            <div class="col-auto">
              <button class="tabs__button text-18 lg:text-16 text-light-1 fw-500 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">Personal Information</button>
            </div>

           

          </div>

          <div class="tabs__content pt-30 js-tabs-content">
            <div class="tabs__pane -tab-item-1 is-tab-el-active">
              <div class="row y-gap-30 items-center">
                <div class="col-auto">
                  <div class="d-flex ratio ratio-1:1 w-200">
                    <img src="{{ asset('front-assets/img/misc/avatar-1.png')}}" alt="image" class="img-ratio rounded-4">

                    <div class="d-flex justify-end px-10 py-10 h-100 w-1/1 absolute">
                      <div class="size-30 bg-white rounded-4 text-center">
                        <i class="icon-trash text-16"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-auto">
                  <h4 class="text-16 fw-500">Your avatar</h4>
                  <div class="text-14 mt-5">PNG or JPG no bigger than 800px wide and tall.</div>

                  <div class="d-inline-block mt-15">
                    <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                      <i class="icon-upload-file text-20 mr-10"></i>
                      Browse
                    </button>
                  </div>
                </div>
              </div>

              <div class="border-top-light mt-30 mb-30"></div>

              <div class="col-xl-12">
                <div class="row x-gap-20 y-gap-20">
                  

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Full name (First and Last name)</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Name">
                      </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Mobile number</label>
                      <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Mobile number">
                    </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Email">
                      </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Date of Birth</label>
                      <input type="date" class="form-control" id="exampleInputEmail1" value="" name="name" >
                    </div>
                    </div>
                  </div>

                  
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="exampleInputEmail1">About Yourself</label>
                        <textarea name="" class="form-control" id=""  rows="10"></textarea>
                      </div>
                    </div>
                  </div>

                  
                </div>
              </div>

              <div class="d-inline-block pt-30">

                <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                  Save Changes <div class="icon-arrow-top-right ml-15"></div>
                </a>

              </div>
            </div>

            <div class="tabs__pane -tab-item-2">
              <div class="col-xl-9">
                <div class="row x-gap-20 y-gap-20">
                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Address Line 1</label>
                    </div>

                  </div>

                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Address Line 2</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">City</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">State</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Select Country</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">ZIP Code</label>
                    </div>

                  </div>

                  <div class="col-12">
                    <div class="d-inline-block">

                      <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                        Save Changes <div class="icon-arrow-top-right ml-15"></div>
                      </a>

                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tabs__pane -tab-item-3">
              <div class="col-xl-9">
                <div class="row x-gap-20 y-gap-20">
                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Current Password</label>
                    </div>

                  </div>

                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">New Password</label>
                    </div>

                  </div>

                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">New Password Again</label>
                    </div>

                  </div>

                  <div class="col-12">
                    <div class="row x-gap-10 y-gap-10">
                      <div class="col-auto">

                        <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                          Save Changes <div class="icon-arrow-top-right ml-15"></div>
                        </a>

                      </div>

                      <div class="col-auto">
                        <button class="button h-50 px-24 -blue-1 bg-blue-1-05 text-blue-1">Cancel</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


     
    </div>
  </div>

@endsection

@section('scripts')
@endsection