<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Razorpay\Api\Api;
use App\Models\UserBankDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Profile;

class PaymentController extends Controller
{
    //
    public function showPaymentPage($booking_id)
{
    $booking = Booking::with('pooja', 'pandit')->findOrFail($booking_id);

    // Check if the booking is approved
    if ($booking->application_status != 'approved') {
        return redirect()->back()->with('error', 'Booking is not approved yet.');
    }

    return view('user/paymentpage', compact('booking'));
}

// public function processPayment(Request $request, $booking_id)
// {
//     $booking = Booking::findOrFail($booking_id);

//     try {
//         // Initialize Razorpay API
//         $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));

//         // Fetch payment details
//         $payment = $api->payment->fetch($request->razorpay_payment_id);

//         \Log::info('Payment details:', (array)$payment);

//         // Capture the payment if it's not captured automatically
//         if (!$payment->captured) {
//             $payment = $payment->capture(['amount' => $payment->amount]);
//         }

//         // Check if payment is captured
//         if ($payment->status != 'captured') {
//             return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
//         }
//         $paidAmountInRupees = $payment->amount / 100;

//         // Update booking with payment details
//         // $booking->application_status = 'paid';
//         $booking->payment_status = 'paid';
//         $booking->status = 'paid';
//         $booking->pooja_status = 'pending';
//         $booking->paid =  $paidAmountInRupees;
//         $booking->payment_id = $request->razorpay_payment_id;
//         $booking->payment_type = $request->payment_type;
//         $booking->payment_method = 'razorpay';
//         $booking->save();

//         return redirect()->route('booking.success', ['booking' => $booking_id])->with('success', 'Payment successful and booking confirmed!');
//     } catch (\Exception $e) {
//         \Log::error('Payment verification failed: ' . $e->getMessage());
//         return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
//     }
// }

    public function processPayment(Request $request, $booking_id)
    {
        $booking = Booking::findOrFail($booking_id);

        try {
            // Initialize Razorpay API
            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Fetch payment details
            $payment = $api->payment->fetch($request->razorpay_payment_id);

            \Log::info('Payment details:', (array)$payment);

            // Capture the payment if it's not captured automatically
            if (!$payment->captured) {
                $payment = $payment->capture(['amount' => $payment->amount]);
            }

            // Check if payment is captured
            if ($payment->status != 'captured') {
                return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
            }
            $paidAmountInRupees = $payment->amount / 100;

            // Update booking with payment statuses
            $booking->payment_status = 'paid';
            $booking->status = 'paid';
            $booking->pooja_status = 'pending';
            $booking->save();

            // Save payment details in the payments table
            Payment::create([
                'booking_id' => $booking->booking_id,
                'user_id' => $booking->user_id,  // Assuming you have a user_id in the bookings table
                'payment_id' => $request->razorpay_payment_id,
                'payment_status' => 'paid',
                'paid' => $paidAmountInRupees,
                'payment_type' => $request->payment_type,
                'payment_method' => 'razorpay',
            ]);

            return redirect()->route('booking.success', ['booking' => $booking_id])->with('success', 'Payment successful and booking confirmed!');
        } catch (\Exception $e) {
            \Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
        }
    }
    public function bookingSuccess($id)
    {
        $booking = Booking::with(['user', 'pandit', 'pooja', 'address', 'payment'])->findOrFail($id);
        $pandit_id = $booking->pandit_id;
        $panditdetails = Profile::where('id', $pandit_id)->first();
    
        return view('user.booking-success', compact('booking', 'panditdetails'));
    }
    


    public function showCancelForm($id)
    {
        $booking = Booking::findOrFail($id);
        $userBankDetails = UserBankDetail::where('user_id', Auth::id())->first();
        return view('user/cancel-form', compact('booking', 'userBankDetails'));
    }

    // public function cancelBooking(Request $request, $id)
    // {
    //     $booking = Booking::findOrFail($id);
    //     $today = Carbon::today();
    //     $bookingDate = Carbon::parse($booking->booking_date);
    //     $daysDifference = $bookingDate->diffInDays($today);
    
    //     $validatedData = $request->validate([
    //         'cancel_reason' => 'required|string|max:255',
    //         'refund_method' => 'required|string|in:original',
    //     ]);
    
    //     // Log the booking details and cancellation request
    //     Log::info('Booking cancellation requested', [
    //         'booking_id' => $booking->id,
    //         'booking_date' => $booking->booking_date,
    //         'today' => $today,
    //         'days_difference' => $daysDifference,
    //         'cancel_reason' => $validatedData['cancel_reason'],
    //         'refund_method' => $validatedData['refund_method']
    //     ]);
    
    //     if ($booking->payment_type == 'advance') {
    //         $refundAmount = 0; // No refund for advance payment
    //     } else {
    //         if ($daysDifference > 20) {
    //             $refundAmount = $booking->paid;
    //         } elseif ($daysDifference > 0 && $daysDifference <= 20) {
    //             $refundAmount = $booking->paid * 0.80; // 20% cancellation fee
    //         } else {
    //             $refundAmount = $booking->paid * 0.80; // 20% cancellation fee if the booking date is today or less than a day
    //         }
    //     }
    
    //     $booking->status = 'canceled';
    //     $booking->payment_status = 'refundprocess';
    //     $booking->pooja_status = 'canceled';
    //     $booking->canceled_at = now();
    //     $booking->cancel_reason = $validatedData['cancel_reason'];
    //     $booking->refund_method = $validatedData['refund_method'];
    //     $booking->refund_amount = $refundAmount;
    //     $booking->save();
    
    //     // Log booking cancellation
    //     Log::info('Booking canceled successfully', [
    //         'booking_id' => $booking->id,
    //         'refund_amount' => $refundAmount
    //     ]);
    
    //     return redirect()->route('booking.history')->with('success', 'Booking canceled successfully! Refund Amount: ₹' . $refundAmount);
    // }
    public function cancelBooking(Request $request, $booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        $today = Carbon::today();
        $bookingDate = Carbon::parse($booking->booking_date);
        $daysDifference = $bookingDate->diffInDays($today);
    
        $validatedData = $request->validate([
            'cancel_reason' => 'required|string|max:255',
            'refund_method' => 'required|string|in:original',
        ]);
    
        // Log the booking details and cancellation request
        Log::info('Booking cancellation requested', [
            'booking_id' => $booking->id,
            'booking_date' => $booking->booking_date,
            'today' => $today,
            'days_difference' => $daysDifference,
            'cancel_reason' => $validatedData['cancel_reason'],
            'refund_method' => $validatedData['refund_method']
        ]);
    
        // Update payment with refund details
        $payment = Payment::where('booking_id',  $booking->booking_id)->first();
        if ($payment) {

            if ($payment->payment_type == 'advance') {
                $refundAmount = 0; // No refund for advance payment
            } else {
                if ($daysDifference > 20) {
                    $refundAmount = $payment->paid;
                } elseif ($daysDifference > 0 && $daysDifference <= 20) {
                    $refundAmount = $payment->paid * 0.80; // 20% cancellation fee
                } else {
                    $refundAmount = $payment->paid * 0.80; // 20% cancellation fee if the booking date is today or less than a day
                }
            }
            
            $payment->payment_status = 'refundprocess';
            $payment->canceled_at = now();
            $payment->cancel_reason = $validatedData['cancel_reason'];
            $payment->refund_method = $validatedData['refund_method'];
            $payment->refund_amount = $refundAmount;
            $payment->save();
        } else {
            // Optionally log if no payment is found
            \Log::warning('No payment found for booking_id', ['booking_id' => $booking_id]);
        }


        // Update booking with cancellation details
        $booking->status = 'canceled';
        $booking->payment_status = 'refundprocess';
        $booking->pooja_status = 'canceled';
      
      
        $booking->save();
    
        // Log booking cancellation
        \Log::info('Booking canceled successfully', [
            'booking_id' => $booking->booking_id,
            'refund_amount' => $refundAmount
        ]);
    
        return redirect()->route('booking.history')->with('success', 'Booking canceled successfully! Refund Amount: ₹' . $refundAmount);
    }
    



    protected function processRazorpayRefund($booking, $amount)
    {
        // Assuming you have the Razorpay API setup
        $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));


        try {
            $refund = $api->refund->create([
                'payment_id' => $booking->payment_id, // Assuming you have this saved in the booking
                'amount' => $amount * 100, // Amount in paise
                'notes' => ['Reason' => 'Booking Cancellation'],
            ]);

            \Log::info('Razorpay refund processed', [
                'payment_id' => $booking->payment_id,
                'refund_id' => $refund->id,
                'amount' => $amount
            ]);
        } catch (\Exception $e) {
            \Log::error('Razorpay refund error', [
                'error' => $e->getMessage()
            ]);
        }
    }

    
    
    
    


}
