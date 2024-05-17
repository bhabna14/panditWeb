@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
      <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
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
             
                <div class="row">
                    <form action="">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Full name (First and Last name)</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Name">
                        </div>
                    </form>
                  
                </div>
              </div>

              <div class="d-inline-block pt-30">

                <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                  Save Changes <div class="icon-arrow-top-right ml-15"></div>
                </a>

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