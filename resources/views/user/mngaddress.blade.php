@extends('user.layouts.front-dashboard')

@section('styles')
<style>
  .text-success-2{
    background-color: #def2d7;
    /* color: #fff; */
    color: #5b7052;
    padding: 17px;
    font-size: 18px;
    margin: 16px 0px;
    border-radius: 5px;
}
.text-error-2{
    background-color: #def2d7;
    /* color: #fff; */
    color: red;
    padding: 17px;
    font-size: 18px;
    margin: 16px 0px;
    border-radius: 5px;
}
.flex-div {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #dfdfdf;
}
</style>
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
          @if(session()->has('success'))
          <div class="text-success-2 lh-1 fw-500" id="Message">
              {{ session()->get('success') }}
          </div>
          @endif

          @if(session()->has('error'))
          <div class="text-error-2 lh-1 fw-500" id="Message">
              {{ session()->get('error') }}
          </div>
          @endif
      
          @if ($errors->has('danger'))
              <div class="alert alert-danger" id="Message">
                  {{ $errors->first('danger') }}
              </div>
          @endif
          @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
         @endif
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
           
            @foreach ($addressdata as $index => $address)
            <div class="col-xl-4 col-md-6">
                <div class="single-address">
                    <div class="rounded-4 bg-white shadow-3 position-relative">
                       <div class="flex-div">
                            
                            <div class="fw-500 lh-14 address-single-heading">{{$address->address_type}}</div>
                            @if($address->default == 1)
                                <div class="fw-500 lh-14 address-single-heading">
                                    Default
                                </div>
                            @endif
                       </div>
                        <div class="address-details">
                            <p>{{$address->area}}</p>
                            <p>{{$address->city}}</p>
                            <p>{{$address->state}} {{$address->pincode}}</p>
                            <p>{{$address->country}}</p>
                        </div>
                        <div class="action-btns">
                            <a href="{{route('editAddress', $address->id)}}">Edit</a> | 
                            @if($address->default == 1)
                                <!-- Disable Remove link for default address -->
                                <span>Remove (Default address cannot be removed)</span>
                            @else
                                <a href="{{route('removeaddress', $address->id)}}" onclick="return confirm('Are you sure you want to remove this address?')">Remove</a>
                            @endif
                            |
                            @if(!$address->default)
                                <a href="{{route('setDefaultAddress', $address->id)}}">Set as Default</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        
            

          {{-- <div class="col-xl-4 col-md-6 ">
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
          </div> --}}

        </div>
        

      
      </div>
    </div>


@endsection

@section('scripts')
@endsection