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
                    <div class="text-30 lh-1 fw-600 mt-20">{{ session('success') }}</div>
                </div>

                <div class="border-type-1 rounded-8 px-50 py-35 mt-40">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="text-15 lh-12">Product ID</div>
                            <div class="text-15 lh-12 fw-500 text-blue-1">{{ $booking->product_id }}</div>
                        </div>
                        <!-- Other booking details -->
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="col-md-6">
                <div class="border-light rounded-8 px-50 py-40 mt-40">
                    <h4 class="text-20 fw-500 mb-30">User Information</h4>
                    <div class="row y-gap-10">
                        <div class="col-12">
                            <div class="d-flex justify-between">
                                <div class="text-15 lh-16">Full Name</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $booking->user->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <!-- Other user details -->
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="col-md-6">
                <div class="border-light rounded-8 px-50 py-40 mt-40">
                    <h4 class="text-20 fw-500 mb-30">Payment Details</h4>
                    <div class="row y-gap-10">
                        <div class="col-12">
                            <div class="d-flex justify-between">
                                <div class="text-15 lh-16">Total Paid</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ $booking->paid_amount ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-between border-top-light pt-10">
                                <div class="text-15 lh-16">Payment Status</div>
                                <div class="text-15 lh-16 fw-500 text-blue-1">{{ ucfirst($booking->status) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
