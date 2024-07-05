<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Razorpay\Api\Api;


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

        // Update booking with payment details
        $booking->status = 'paid';
        $booking->paid =  $paidAmountInRupees;
        $booking->payment_id = $request->razorpay_payment_id;
        $booking->save();

        return redirect()->route('booking.success', ['booking' => $booking_id])->with('success', 'Payment successful and booking confirmed!');
    } catch (\Exception $e) {
        \Log::error('Payment verification failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
    }
}


}
