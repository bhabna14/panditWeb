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
use App\Models\PanditDevice;
// use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\Messaging;

use DB;

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
            $poojaName = DB::table('pooja_list')
                    ->where('id', $booking->pooja_id)
                    ->value('pooja_name'); // 
            // Retrieve the pooja name using the relationship
            $poojaName = $booking->pooja->pooja_name; 
              // Send FCM notification to the pandit
            $factory = (new Factory)->withServiceAccount(config('services.firebase.pandit.credentials'));
            $messaging = $factory->createMessaging();

            // Retrieve pandit's device token

            $panditProfile = Profile::findOrFail($validatedData['pandit_id']);
            $panditId = $panditProfile->pandit_id;
            $panditDevices = PanditDevice::where('pandit_id', $panditId)->get();
    
            if ($panditDevices->isEmpty()) {
                throw new \Exception('Pandit device tokens not found.');
            }
            // if (!$device) {
            //     throw new \Exception('Pandit device token not found.');
            // }
            // Send notifications to all devices
            foreach ($panditDevices as $device) {
                $deviceToken = $device->device_id;

                // Prepare notification message
                $message = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification(Notification::create(
                        'Booking Confirmed',
                        "A new booking for {$poojaName} has been confirmed with ID: {$booking->booking_id} and {$booking->booking_date}. Please check your dashboard for details."
                    ))
                ->withData([
                    'booking_id' => $booking->booking_id,
                    'user_id' => $booking->user_id,
                    'pooja_id' => $booking->pooja_id,
                    'pooja_name' => $poojaName,
                    'message' => 'A new booking has been confirmed for you.',
                    'url' => route('pandit.dashboard')
                ]);
                // Send the notification
                $messaging->send($message);
                try {
                    $messaging->send($message);
                    Log::info('FCM notification sent successfully to pooja name: ' .  $poojaName);
                } catch (\Exception $e) {
                    Log::error('Error sending FCM notification: ' . $e->getMessage());
                }
            }

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
    
        // Fetch all payments related to this booking and user
        $payments = Payment::where('booking_id', $booking->booking_id)
                           ->where('user_id', $booking->user_id)
                           ->get();
    
        if ($payments->isEmpty()) {
            // Log if no payment is found
            \Log::warning('No payment found for booking_id', ['booking_id' => $booking_id]);
            return redirect()->route('booking.history')->with('error', 'No payments found for this booking.');
        }
    
        // Calculate the total paid amount
        $totalPaid = $payments->sum('paid');
    
        // Determine refund amount based on days difference and payment type
        if ($payments->last()->payment_type == 'advance') {
            $refundAmount = 0; // No refund for advance payment
        } else {
            if ($daysDifference > 20) {
                $refundAmount = $totalPaid;
            } elseif ($daysDifference > 0 && $daysDifference <= 20) {
                $refundAmount = $totalPaid * 0.80; // 20% cancellation fee
            } else {
                $refundAmount = $totalPaid * 0.80; // 20% cancellation fee if the booking date is today or less than a day
            }
        }
    
        // Update payment(s) with refund details
        foreach ($payments as $payment) {
            $payment->payment_status = 'refundprocess';
            $payment->canceled_at = now();
            $payment->cancel_reason = $validatedData['cancel_reason'];
            $payment->refund_method = $validatedData['refund_method'];
            $payment->refund_amount = $refundAmount;
            $payment->save();
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
    
        return redirect()->route('booking.history')->with('success', 'Booking canceled successfully! Refund Amount: ₹' . sprintf('%.2f', $refundAmount));
    }
    


/// not needed this function
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


    public function payRemainingAmount($id)
    {
        
        $booking = Booking::findOrFail($id);
        // dd($booking);
        // Calculate the remaining amount to be paid
        $remainingAmount = $booking->pooja_fee - $booking->payment->paid;

        return view('user.pay-remaining-amount', compact('booking', 'remainingAmount'));
    }

    // public function processRemainingPayment(Request $request, $booking_id)
    // {
    // dd($booking_id);

    //     \Log::info('Starting processRemainingPayment', ['booking_id' => $booking_id]);
    
    //     // Ensure the session remains active
    //     session()->keep(['_token']);
    //     try {
    //         $booking = Booking::findOrFail($booking_id);
    //         \Log::info('Booking found', ['booking' => $booking]);
    
    //         // Initialize Razorpay API
    //         $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    //         \Log::info('Razorpay API initialized');
    
    //         // Fetch payment details
    //         $payment = $api->payment->fetch($request->razorpay_payment_id);
    //         \Log::info('Payment details fetched', ['payment' => (array)$payment]);
    
    //         // Capture the payment if it's not captured automatically
    //         if (!$payment->captured) {
    //             $payment = $payment->capture(['amount' => $payment->amount]);
    //             \Log::info('Payment captured', ['payment' => (array)$payment]);
    //         }
    
    //         // Check if payment is captured
    //         if ($payment->status != 'captured') {
    //             \Log::error('Payment verification failed', ['payment_status' => $payment->status]);
    //             return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
    //         }
    
    //         $paidAmountInRupees = $payment->amount / 100;
    //         \Log::info('Paid amount in rupees', ['paidAmountInRupees' => $paidAmountInRupees]);
    
    //         // Save payment details in the payments table
    //         Payment::create([
    //             'booking_id' => $booking->booking_id,
    //             'user_id' => $booking->user_id,
    //             'payment_id' => $request->razorpay_payment_id,
    //             'payment_status' => 'paid',
    //             'paid' => $paidAmountInRupees,
    //             'payment_type' => 'full',
    //             'payment_method' => 'razorpay',
    //         ]);
    //         \Log::info('Payment details saved in the database');
    
    //         // Optionally, update booking details if necessary
    //         // $booking->paid += $paidAmountInRupees;
    //         // $booking->payment_type = 'full';
    //         // $booking->save();
    
    //         \Log::info('Payment process completed successfully');
    //         return view("success");
    //     } catch (\Exception $e) {
    //         \Log::error('Payment verification failed', ['exception' => $e->getMessage()]);
    //         return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
    //     }
    // }
    
    public function processRemainingPayment(Request $request, $booking_id)
    {
        \Log::info('Starting processRemainingPayment', ['booking_id' => $booking_id]);
    
        // Ensure the session remains active
        session()->keep(['_token']);
    
        try {
            // Retrieve the booking details
            $booking = Booking::findOrFail($booking_id);
            \Log::info('Booking found', ['booking' => $booking]);
    
            // Initialize Razorpay API
            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            \Log::info('Razorpay API initialized');
    
            // Fetch payment details
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            \Log::info('Payment details fetched', ['payment' => (array)$payment]);
    
            // Capture the payment if it's not captured automatically
            if (!$payment->captured) {
                $payment = $payment->capture(['amount' => $payment->amount]);
                \Log::info('Payment captured', ['payment' => (array)$payment]);
            }
    
            // Check if payment is captured
            if ($payment->status != 'captured') {
                \Log::error('Payment verification failed', ['payment_status' => $payment->status]);
                return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
            }
    
            // Calculate the paid amount in rupees
            $paidAmountInRupees = $payment->amount / 100;
            \Log::info('Paid amount in rupees', ['paidAmountInRupees' => $paidAmountInRupees]);
    
            // Save payment details in the payments table
            Payment::create([
                'booking_id' => $booking->booking_id, // Use the booking ID, not the booking ID from the request
                'user_id' => $booking->user_id,
                'payment_id' => $request->razorpay_payment_id,
                'payment_status' => 'paid',
                'paid' => $paidAmountInRupees,
                'payment_type' => 'full', // This is a full payment since it's the remaining amount
                'payment_method' => 'razorpay',
            ]);
            \Log::info('Payment details saved in the database');
    
            // Update the booking details
            // $booking->status = 'completed'; // Mark the booking as completed
            // // $booking->paid_amount += $paidAmountInRupees; // Update the paid amount in the booking
            // $booking->save();
            \Log::info('Booking status and paid amount updated', ['booking' => $booking]);
    
            \Log::info('Payment process completed successfully');
            return redirect()->route('booking.success', ['booking' => $booking_id])->with('success', 'Payment successful and booking confirmed!'); // Redirect to a success page or handle success response
        } catch (\Exception $e) {
            \Log::error('Payment verification failed', ['exception' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Payment verification failed. Please try again.');
        }
    }
    
    
    

    
    

    
    
    
    


}
