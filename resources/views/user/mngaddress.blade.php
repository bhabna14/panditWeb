@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

    <div class="dashboard__main">
      <div class="dashboard__content bg-light-2">
        <div class="row y-gap-20 justify-between items-end pb-20 mt-30 lg:pb-40 md:pb-32">
          <div class="col-auto">

            <h1 class="text-30 lh-14 fw-600">Manage Address</h1>

          </div>

          <div class="col-auto">

          </div>
        </div>


        <div class="row y-gap-30">

            <div class="col-xl-4 col-md-6 ">
              <a href="{{ url('add-address')}}">
                <div class="single-address text-center" style="cursor: pointer">
                    <div class=" rounded-4 bg-white shadow-3">
                        <div class="add-address-cont">
                          <i class="fa fa-plus"></i>
                          <h6>Add Address</h6>
                        </div>
                    </div>
                </div>
              </a>
            </div>

         

          <div class="col-xl-4 col-md-6 ">
            <div class="single-address">
                <div class=" rounded-4 bg-white shadow-3">
                    <div class="fw-500 lh-14 address-single-heading">Home</div>
                    <div class="address-details">
                        <p>Bhabna samantara</p>
                        <p>Near bhagabati temple</p>
                        <p>Dasarathipur</p>
                        <p>BANAPUR, ODISHA 752031</p>
                        <p>India</p>
                        <p>Phone number: 9040112795</p>
                    </div>
                    <div class="action-btns">
                        <a href="">Edit</a> | <a href=""> Remove</a>
                    </div>
                </div>
            </div>
          </div>

          <div class="col-xl-4 col-md-6 ">
            <div class="single-address">
                <div class=" rounded-4 bg-white shadow-3">
                    <div class="fw-500 lh-14 address-single-heading">Work</div>
                    <div class="address-details">
                        <p>Bhabna samantara</p>
                        <p>Near bhagabati temple</p>
                        <p>Dasarathipur</p>
                        <p>BANAPUR, ODISHA 752031</p>
                        <p>India</p>
                        <p>Phone number: 9040112795</p>
                    </div>
                    <div class="action-btns">
                        <a href="">Edit</a> | <a href=""> Remove</a>
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