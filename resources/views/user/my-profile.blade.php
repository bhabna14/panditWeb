@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

    <div class="dashboard__main">
      <div class="dashboard__content bg-light-2">
        <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
          <div class="col-auto">

            <h1 class="text-30 lh-14 fw-600">Dashboard</h1>
            <div class="text-15 text-light-1">Lorem ipsum dolor sit amet, consectetur.</div>

          </div>

          <div class="col-auto">

          </div>
        </div>


        <div class="row y-gap-30">

          <div class="col-xl-4 col-md-6">
            <div class="py-30 px-30 rounded-4 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Pending Booking</div>
                  <div class="text-26 lh-16 fw-600 mt-5">1</div>
                  <div class="text-15 lh-14 text-light-1 mt-5">Total pending</div>
                </div>

                {{-- <div class="col-auto">
                  <img src="{{ asset('front-assets/img/dashboard/icons/1.svg')}}" alt="icon">
                </div> --}}
              </div>
            </div>
          </div>

          {{-- <div class="col-xl-3 col-md-6">
            <div class="py-30 px-30 rounded-4 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Earnings</div>
                  <div class="text-26 lh-16 fw-600 mt-5">$14,200</div>
                  <div class="text-15 lh-14 text-light-1 mt-5">Total earnings</div>
                </div>

                <div class="col-auto">
                  <img src="img/dashboard/icons/2.svg" alt="icon">
                </div>
              </div>
            </div>
          </div> --}}

          <div class="col-xl-4 col-md-6">
            <div class="py-30 px-30 rounded-4 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Bookings</div>
                  <div class="text-26 lh-16 fw-600 mt-5">10</div>
                  <div class="text-15 lh-14 text-light-1 mt-5">Total bookings</div>
                </div>

                {{-- <div class="col-auto">
                  <img src="img/dashboard/icons/3.svg" alt="icon">
                </div> --}}
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-md-6">
            <div class="py-30 px-30 rounded-4 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Services</div>
                  <div class="text-26 lh-16 fw-600 mt-5">22</div>
                  <div class="text-15 lh-14 text-light-1 mt-5">Total services</div>
                </div>

                {{-- <div class="col-auto">
                  <img src="img/dashboard/icons/4.svg" alt="icon">
                </div> --}}
              </div>
            </div>
          </div>

        </div>

        <div class="row y-gap-30 pt-20">
         
          <div class="col-xl-12 col-md-6">
            <div class="py-30 px-30 rounded-4 bg-white shadow-3">
              <div class="d-flex justify-between items-center">
                <h2 class="text-18 lh-1 fw-500">
                  Recent Bookings
                </h2>

                <div class="">
                  <a href="#" class="text-14 text-blue-1 fw-500 underline">View All</a>
                </div>
              </div>

              <div class="overflow-scroll scroll-bar-1 pt-30">
                <table class="table-2 col-12">
                  <thead class="">
                    <tr>
                      <th>#</th>
                      <th>Item</th>
                      <th>Total</th>
                      <th>Paid</th>
                      <th>Status</th>
                      <th>Created At</th>
                    </tr>
                  </thead>
                  <tbody>

                    <tr>
                      <td>#1</td>
                      <td>P.Bibhu Panda<br> Baladevjew Pooja                      </td>
                      <td class="fw-500">1300</td>
                      <td>300</td>
                      <td>
                        <div class="rounded-100 py-4 text-center col-12 text-14 fw-500 bg-yellow-4 text-yellow-3">Pending</div>
                      </td>
                      <td>04/04/2022<br>08:16</td>
                    </tr>


                    <tr>
                      <td>#5</td>
                      <td>P.Bibhu Panda<br> Baladevjew Pooja                      </td>
                      <td class="fw-500">1300</td>
                      <td>1300</td>
                      <td>
                        <div class="rounded-100 py-4 text-center col-12 text-14 fw-500 bg-blue-1-05 text-blue-1">Confirmed</div>
                      </td>
                      <td>04/04/2022<br>08:16</td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>


      
      </div>
    </div>


@endsection

@section('scripts')
@endsection