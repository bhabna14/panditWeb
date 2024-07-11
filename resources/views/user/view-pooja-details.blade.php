@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">

        <h1 class="text-30 lh-14 fw-600">Booking Details</h1>
       

      </div>

      <div class="col-auto">

      </div>
    </div>
    <div class="row">
       <div class="col-md-12">
         <div class="order-inner-details">
            <div class="row">
               <div class="col-md-4">
                 <h4>Address</h4>
                 {{ $booking->address->fullname ?? 'N/A' }}<br>
                 {{ $booking->address->area ?? 'N/A' }},{{ $booking->address->city ?? 'N/A' }},{{ $booking->address->state ?? 'N/A' }}
                  {{ $booking->address->country ?? 'N/A' }}<br>
                  Pincode : {{ $booking->address->pincode ?? 'N/A' }}<br>
                  Landmark : {{ $booking->address->landmark ?? 'N/A' }}</p>
               </div>
               <div class="col-md-4">
                 <h4>Payment Method</h4>
                 {{ $booking->refund_method ?? 'N/A' }}<br>
               </div>
               <div class="col-md-4">
                <h4>Booking Summary</h4>
                Total Fee: ₹ {{ $booking->pooja_fee ?? 'N/A' }}<br>
                Advance Fee: ₹ {{ $booking->advance_fee ?? 'N/A' }}<br>
                Paid Amount: ₹ {{ $booking->paid ?? 'N/A' }}<br>
                Refund Details: ₹{{ $booking->refund_amount ?? 'N/A' }}<br>
               
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