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
             
              
                    <form action="">
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
                      <div class="row mt-10">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Country</label>
                            <select name="" class="form-control" id="">
                              <option value="">India</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="exampleInputEmail1">State</label>
                          <select name="" class="form-control" id="">
                            <option value="">Odisha</option>
                          </select>
                        </div>
                        </div>
                      </div>
                      <div class="row mt-10">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Town/City   </label>
                            <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Town/City">
                          </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="exampleInputEmail1">Pincode</label>
                          <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Pincode">
                        </div>
                        </div>
                      </div>

                      <div class="row mt-10">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Flat, House no., Building</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Flat, House no., Building">
                          </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="exampleInputEmail1">Area, Street, Sector, Village</label>
                          <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="Enter Area, Street, Sector, Village">
                        </div>
                        </div>
                      </div>
                      <div class="row mt-10">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Landmark</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" value="" name="name" placeholder="E.g. near apollo hospital">
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
                            <label class="rdiobox"><input name="marital" type="radio" value="Married"> <span>Home</span></label>
                        </div>
                        <div class="col-lg-2">
                            <label class="rdiobox"><input checked name="marital" type="radio" value="Unmarried"> <span>Work</span></label>
                        </div>
                    </div>
                    </form>
                  
                </div>
              </div>

              <div class="d-inline-block pt-30">

                <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                  Save Address<div class="icon-arrow-top-right ml-15"></div>
                </a>

              </div>
            
          
          </div>
        </div>
      </div>


     
    </div>
  </div>

@endsection

@section('scripts')
@endsection