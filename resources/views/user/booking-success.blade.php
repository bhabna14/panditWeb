@extends('user.layouts.front')

@section('content')
<section class="mt-30 mb-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-column items-center mt-60 lg:md-40 sm:mt-24">
                    <div class="size-80 flex-center rounded-full bg-dark-3">
                      <i class="icon-check text-30 text-white"></i>
                    </div>

                    <div class="text-30 lh-1 fw-600 mt-20"> {{ session()->get('success') }}</div>
                </div>
               
            
              
                 
                <div class="border-type-1 rounded-8 px-50 py-35 mt-40">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="text-15 lh-12">Booking Number</div>
                            <div class="text-15 lh-12 fw-500 text-blue-1">{{ $booking->booking_id }}</div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="text-15 lh-12">Date</div>
                            <div class="text-15 lh-12 fw-500 text-blue-1">{{ $booking->booking_date }}</div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="text-15 lh-12">Paid Amount</div>
                            <div class="text-15 lh-12 fw-500 text-blue-1">{{ $booking->payment->paid ?? 0 }} </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="text-15 lh-12">Total Amount</div>
                            <div class="text-15 lh-12 fw-500 text-blue-1">{{ $booking->pooja_fee }}</div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="text-15 lh-12">Status</div>
                            <div class="text-15 lh-12 fw-500 text-blue-1">{{ ucfirst($booking->status) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border-light rounded-8 px-50 py-40 mt-40">
                    <h4 class="text-20 fw-500 mb-30">Your Information</h4>

                    <div class="row y-gap-10">
                        <div class="col-12">
                            <div class="d-flex justify-between">
                                <div class="text-15 lh-16">Full Name</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $booking->user->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        

                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16">Email</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $booking->user->email ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16">Phone</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ Auth::guard('users')->user()->mobile_number ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16">Address </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16 fw-500 text-blue-1">
                                    {{ $booking->address->area ?? 'N/A' }},{{ $booking->address->city ?? 'N/A' }},{{ $booking->address->state ?? 'N/A' }}
                                    {{ $booking->address->country ?? 'N/A' }}<br>
                                    Pincode : {{ $booking->address->pincode ?? 'N/A' }}<br>
                                   
                                </div>
                            </div>
                        </div>

                       
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border-light rounded-8 px-50 py-40 mt-40">
                    <h4 class="text-20 fw-500 mb-30">Pooja Information</h4>

                    <div class="row y-gap-10">
                        <div class="col-12">
                            <div class="d-flex justify-between">
                                <div class="text-15 lh-16">Pooja Name</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $booking->pooja->pooja_name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16">Pandit Name</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $panditdetails->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16">Total Fee</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $booking->pooja_fee ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
