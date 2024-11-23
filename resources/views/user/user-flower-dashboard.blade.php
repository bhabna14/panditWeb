@extends('user.layouts.front-flower-dashboard')

@section('styles')
@endsection

@section('content')

    <div class="dashboard__main">
      <div class="dashboard__content">
        <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-12">
          <div class="col-auto">

            <h1 class="text-30 lh-14 fw-600 main-content-title">Dashboard</h1>

          </div>

          <div class="col-auto">

          </div>
        </div>


        <div class="row dashboard-row">
          <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="row">
              <div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-xl-9 col-lg-7 col-md-6 col-sm-12">
                        <div class="text-justified align-items-center">
                          <h3 class="text-dark font-weight-semibold mb-2 mt-0">
                            Hi, Welcome Back 
                            <span class="text-primary">
                                {{ Auth::guard('users')->user()->name ?: Auth::guard('users')->user()->mobile_number }}!
                            </span>
                          </h3>
                          {{-- <h3 class="text-dark font-weight-semibold mb-2 mt-0">Hi, Welcome Back <span class="text-primary">{{ Auth::guard('users')->user()->name}}!</span></h3> --}}
                          {{-- <p class="text-dark tx-14 mb-3 lh-3"> You have used the 85% of free plan storage. Please upgrade your plan to get unlimited storage.</p>
                          <button class="btn btn-primary shadow">Upgrade Now</button> --}}
                        </div>
                      </div>
                      {{-- <div class="col-xl-3 col-lg-5 col-md-6 col-sm-12 d-flex align-items-center justify-content-center">
                        <div class="chart-circle float-md-end mt-4 mt-md-0" data-value="0.85" data-thickness="8" data-color=""><canvas width="100" height="100"></canvas>
                          <div class="chart-circle-value circle-style"><div class="tx-18 font-weight-semibold">85%</div></div>
                        </div>
                      </div> --}}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-2 col-lg-12 col-md-12 col-xs-12">
                <div class="card sales-card">
                  <div class="row">
                    <div class="col-8">
                      <div class="ps-4 pt-4 pe-3 pb-4">
                        <div class="">
                          <h6 class="mb-2 tx-12 ">Total Bookings</h6>
                        </div>
                        <div class="pb-0 mt-0">
                          <div class="d-flex">
                            {{-- <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalbookings }}</h4> --}}
                          </div>
                          {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
                            <span class="text-success font-weight-semibold"> +427</span>
                          </p> --}}
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                        <i class="fa fa-user tx-16 text-primary"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-2 col-lg-12 col-md-12 col-xs-12">
                <div class="card sales-card">
                  <div class="row">
                    <div class="col-8">
                      <div class="ps-4 pt-4 pe-3 pb-4">
                        <div class="">
                          <h6 class="mb-2 tx-12 ">Total Completed</h6>
                        </div>
                        <div class="pb-0 mt-0">
                          <div class="d-flex">
                            {{-- <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalCompleted }}</h4> --}}
                          </div>
                          {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
                            <span class="text-success font-weight-semibold"> +427</span>
                          </p> --}}
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                        <i class="fa fa-user tx-16 text-primary"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-2 col-lg-12 col-md-12 col-xs-12">
                <div class="card sales-card">
                  <div class="row">
                    <div class="col-8">
                      <div class="ps-4 pt-4 pe-3 pb-4">
                        <div class="">
                          <h6 class="mb-2 tx-12 ">Total Canceled</h6>
                        </div>
                        <div class="pb-0 mt-0">
                          <div class="d-flex">
                            {{-- <h4 class="tx-20 font-weight-semibold mb-2">{{ $totalCanceled }}</h4> --}}
                          </div>
                          {{-- <p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>
                            <span class="text-success font-weight-semibold"> +427</span>
                          </p> --}}
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
                        <i class="fa fa-user tx-16 text-primary"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            
             
            </div>
          </div>
          <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div>
                    <div>
                        <h2 class="text-18 lh-1 fw-500 ps-4 pt-4">
                            Recent Bookings
                        </h2>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table-2 col-12">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking Id</th>
                                <th>Pooja Name</th>
                                <th>Pandit Name</th>
                                <th>Total Fee</th>
                                <th>Total Paid</th>
                              
                                <th>Refund Amount</th>
                                <th>Pooja Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                       
                    </table>
                </div>
            </div>
          </div>
          <!-- </div> -->
        </div>


      
      </div>
    </div>


@endsection

@section('scripts')
@endsection