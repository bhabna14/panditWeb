<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Poojadetails;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\PanditDevice;
use App\Models\Profile;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\Messaging;
use DB;


class BookingController extends Controller
{
    // old method withour booking_date validation
    // public function confirmBooking(Request $request)
    // {
    //     // dd("hi");
    //     try {
    //         // Validate incoming request data
    //         $validatedData = $request->validate([
    //             'pandit_id' => 'required|exists:pandit_profile,id',
    //             'pooja_id' => 'required',
    //             'pooja_fee' => 'required|numeric',
    //             'advance_fee' => 'required|numeric',
    //             'booking_date' => 'required|date',
               
    //             'address_id' => 'required',
    //         ]);

    //         // Assign the authenticated user's ID to the booking
    //         $validatedData['user_id'] = Auth::guard('sanctum')->user()->userid;
    //         $validatedData['application_status'] = 'pending';
    //         $validatedData['payment_status'] = 'pending';
    //         $validatedData['status'] = 'pending';
    //         $validatedData['pooja_status'] = 'pending';

    //         // Create a new booking record
    //         $booking = Booking::create($validatedData);

    //         // Log success message
    //         \Log::info('Booking created successfully.', ['data' => $validatedData]);

    //         $booking->load(['user', 'pandit', 'poojalist', 'address']);
    //         $booking->poojalist->pooja_photo =asset('assets/img/'.$booking->poojalist->pooja_photo);
    //         $booking->pandit->profile_photo = asset($booking->pandit->profile_photo);
    //         $booking->user->userphoto = asset(Storage::url($booking->user->userphoto));

    //         // Return a success response with the booking details
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Booking confirmed successfully!',
    //             'booking' => $booking
    //         ], 201);
    //     } catch (\Exception $e) {
    //         // Log the error
    //         \Log::error('Error creating booking: ' . $e->getMessage());

    //         // Return a JSON error response
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to confirm booking. Please try again.'
    //         ], 500);
    //     }
    // }


    public function confirmBooking(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'pandit_id' => 'required|exists:pandit_profile,id',
                'pooja_id' => 'required|exists:pandit_poojadetails,pooja_id',
                'pooja_fee' => 'required|numeric',
                'advance_fee' => 'required|numeric',
                'booking_date' => 'required',
                'address_id' => 'required',
            ]);
    
            // Get the pooja duration from the Poojadetails model
            $pooja = Poojadetails::where('pooja_id', $validatedData['pooja_id'])->firstOrFail();
            $poojaDurationString = $pooja->pooja_duration;
    
            // Convert the duration string to total minutes
            $poojaDurationMinutes = $this->convertDurationToMinutes($poojaDurationString);
    
            // Calculate the end time of the new pooja
            $newPoojaStartTime = Carbon::parse($validatedData['booking_date']);
            $newPoojaEndTime = $newPoojaStartTime->copy()->addMinutes($poojaDurationMinutes);
    
                   // Check if the Pandit is already booked for the requested time slot
            $conflictingBooking = Booking::where('pandit_id', $validatedData['pandit_id'])
            ->where(function($query) use ($newPoojaStartTime, $newPoojaEndTime) {
                $query->whereBetween('booking_date', [$newPoojaStartTime, $newPoojaEndTime])
                    ->orWhere(function($query) use ($newPoojaStartTime, $newPoojaEndTime) {
                        $query->where('booking_date', '<=', $newPoojaStartTime)
                                ->where('booking_end_time', '>=', $newPoojaStartTime);
                    });
            })
            ->where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'pending')
                        ->where('payment_status', 'pending')
                        ->where('application_status', 'approved')
                        ->where('pooja_status', 'pending');
                })->orWhere(function($query) {
                    $query->where('status', 'paid')
                        ->where('payment_status', 'paid')
                        ->where('application_status', 'approved')
                        ->where('pooja_status', 'pending');
                });
            })
            ->first();

            // if ($conflictingBooking) {
            //     // Get the booking_end_time from the conflicting booking
            //     $nextAvailableTime = Carbon::parse($conflictingBooking->booking_end_time)->format('Y-m-d h:i A'); 

            //     return back()->with('error', "The Pandit is already booked for the selected date and time. Please choose a different time or date after {$nextAvailableTime}.");
            // }
    
            if ($conflictingBooking) {
                $nextAvailableTime = Carbon::parse($conflictingBooking->booking_end_time)->format('Y-m-d h:i A');
                return response()->json([
                    'success' => false,
                    'message' => "The Pandit is already booked for the selected date and time. Please choose a different time or date after {$nextAvailableTime}."
                ], 409); // 409 Conflict
            }
    
            // Assign the authenticated user's ID to the booking
            $validatedData['user_id'] = Auth::guard('sanctum')->user()->userid;
            $validatedData['application_status'] = 'pending';
            $validatedData['payment_status'] = 'pending';
            $validatedData['status'] = 'pending';
            $validatedData['pooja_status'] = 'pending';
            $validatedData['booking_end_time'] = $newPoojaEndTime;
    
            // Create a new booking record
            $booking = Booking::create($validatedData);
            // Send FCM notification to the pandit
            $factory = (new Factory)->withServiceAccount(config('services.firebase.pandit.credentials'));
            $messaging = $factory->createMessaging();

            // Retrieve all pandit's device tokens
            
            $panditProfile = Profile::findOrFail($validatedData['pandit_id']);
            $panditId = $panditProfile->pandit_id;
            $panditDevices = PanditDevice::where('pandit_id', $panditId)->get();

            if ($panditDevices->isEmpty()) {
                throw new \Exception('Pandit device tokens not found.');
            }

            // Send notifications to all devices
            foreach ($panditDevices as $device) {
                $deviceToken = $device->device_id;

                // Prepare notification message
                $message = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification(Notification::create(
                        'New Booking Request',
                        "A new booking request with ID: {$booking->booking_id}. Please check your dashboard for details."
                    ))
                    ->withData([
                        'booking_id' => $booking->booking_id,
                        'user_id' => Auth::guard('sanctum')->user()->userid,
                        'pooja_id' => $validatedData['pooja_id'],
                        'message' => 'A new booking request for you.',
                     
                    ]);

                try {
                    $messaging->send($message);
                    Log::info('FCM notification sent successfully to device token: ' . $deviceToken);
                } catch (\Exception $e) {
                    Log::error('Error sending FCM notification to device token ' . $deviceToken . ': ' . $e->getMessage());
                }
            }


    
            // Load related data
            $booking->load(['user', 'pandit', 'poojalist', 'address']);
            $booking->poojalist->pooja_photo = asset('assets/img/' . $booking->poojalist->pooja_photo);
            $booking->pandit->profile_photo = asset($booking->pandit->profile_photo);
            $booking->user->userphoto = asset(Storage::url($booking->user->userphoto));
    
            // Return a success response with the booking details
            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully!',
                'booking' => $booking
            ], 200); // 200 OK
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating booking: ' . $e->getMessage());
    
            // Return a JSON error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm booking. Please try again.'
            ], 500); // 500 Internal Server Error
        }
    }
    
    
    
    private function convertDurationToMinutes($durationString)
    {
        $totalMinutes = 0;
    
        // Split by commas to handle multiple parts (e.g., "2 Hour, 45 Minute")
        $parts = explode(',', $durationString);
    
        foreach ($parts as $part) {
            $part = trim($part);
            if (strpos($part, 'Hour') !== false) {
                $hours = (int) filter_var($part, FILTER_SANITIZE_NUMBER_INT);
                $totalMinutes += $hours * 60;
            } elseif (strpos($part, 'Minute') !== false) {
                $minutes = (int) filter_var($part, FILTER_SANITIZE_NUMBER_INT);
                $totalMinutes += $minutes;
            } elseif (strpos($part, 'Day') !== false) {
                $days = (int) filter_var($part, FILTER_SANITIZE_NUMBER_INT);
                $totalMinutes += $days * 24 * 60;
            }
        }
    
        return $totalMinutes;
    }
    
    


    // public function processPayment(Request $request, $booking_id)
    // {
    //     try {
    //         // Validate incoming request data
    //         $validatedData = $request->validate([
    //             'payment_id' => 'required|string',
    //             // 'application_status' => 'required|string',
    //             'payment_status' => 'required|string',
    //             'status' => 'required|string',
    //             'paid' => 'required|numeric',
    //             'payment_type' => 'required|string',
    //             'payment_method' => 'required|string',
    //         ]);
        
    //         // Find the booking by booking_id
    //         $booking = Booking::where('booking_id', $booking_id)->first();
        
    //         // Check if booking exists
    //         if (!$booking) {
    //             \Log::error('Booking not found', ['booking_id' => $booking_id]);
    //             return response()->json(['error' => 'Booking not found.'], 404);
    //         }
        
    //         // Update booking with payment details
    //         $booking->payment_id = $validatedData['payment_id'];
    //         // $booking->application_status = $validatedData['application_status'];
    //         $booking->payment_status = $validatedData['payment_status'];
    //         $booking->status = $validatedData['status'];
    //         $booking->paid = $validatedData['paid'];
    //         $booking->payment_type = $validatedData['payment_type'];
    //         $booking->payment_method = $validatedData['payment_method'];
        
    //         // Save booking and check for errors
    //         if (!$booking->save()) {
    //             \Log::error('Failed to save booking', ['booking_id' => $booking_id]);
    //             return response()->json(['error' => 'Failed to save payment details. Please try again.'], 500);
    //         }
        
    //         return response()->json(['success' => 'Payment details saved successfully!', 'booking' => $booking], 200);
    //     } catch (\Exception $e) {
    //         // Log detailed error information
    //         \Log::error('Exception occurred while saving payment details', [
    //             'exception' => $e->getMessage(),
    //             'booking_id' => $booking_id,
    //             'request_data' => $request->all()
    //         ]);
    //         return response()->json(['error' => 'Failed to save payment details. Please try again.'], 500);
    //     }
    // }


    public function processPayment(Request $request, $booking_id)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'payment_id' => 'required|string',
                'payment_status' => 'required|string',
                'status' => 'required|string',
                'paid' => 'required|numeric',
                'payment_type' => 'required|string',
                'payment_method' => 'required|string',
            ]);

            // Find the booking by booking_id
            $booking = Booking::where('booking_id', $booking_id)->first();

            // Check if booking exists
            if (!$booking) {
                \Log::error('Booking not found', ['booking_id' => $booking_id]);
                return response()->json(['error' => 'Booking not found.'], 404);
            }

            // Update booking with payment statuses
            $booking->payment_status = $validatedData['payment_status'];
            $booking->status = $validatedData['status'];
            $booking->save();

            // Save payment details in the payments table
            Payment::updateOrCreate(
                ['booking_id' => $booking_id],
                [
                    'user_id' => $booking->user_id,
                    'payment_id' => $validatedData['payment_id'],
                    'payment_status' => $validatedData['payment_status'],
                    'paid' => $validatedData['paid'],
                    'payment_type' => $validatedData['payment_type'],
                    'payment_method' => $validatedData['payment_method'],
                ]
            );

            // Fetch the updated payment details
            $payment = Payment::where('booking_id', $booking_id)->first();

            $poojaName = DB::table('pooja_list')
            ->where('id', $booking->pooja_id)
            ->value('pooja_name'); // 
                // Retrieve the pooja name using the relationship
                $poojaName = $booking->pooja->pooja_name; 

            $factory = (new Factory)->withServiceAccount(config('services.firebase.pandit.credentials'));
            $messaging = $factory->createMessaging();
            // Retrieve pandit's device token

            $panditProfile = Profile::findOrFail($validatedData['pandit_id']);
            $panditId = $panditProfile->pandit_id;
            $panditDevices = PanditDevice::where('pandit_id', $panditId)->get();

            if ($panditDevices->isEmpty()) {
                throw new \Exception('Pandit device tokens not found.');
            }
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
                    // 'url' => route('pandit.dashboard')
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

            return response()->json([
                'success' => 'Payment details saved successfully!',
                'booking' => $booking,
                'payment' => $payment
            ], 200);
        } catch (\Exception $e) {
            // Log detailed error information
            \Log::error('Exception occurred while saving payment details', [
                'exception' => $e->getMessage(),
                'booking_id' => $booking_id,
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Failed to save payment details. Please try again.'], 500);
        }
    }


    // process for remaining payment
    public function processRemainingPayment(Request $request, $booking_id)
    {
        \Log::info('Starting processRemainingPayment', ['booking_id' => $booking_id]);
    
        // Ensure the session remains active
        session()->keep(['_token']);
    
        try {
            // Find the booking
            $booking = Booking::where('booking_id', $booking_id)->first();
            if (!$booking) {
                \Log::error('Booking not found', ['booking_id' => $booking_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.'
                ], 404);
            }
            
            \Log::info('Booking found', ['booking' => $booking]);
    
            // Validate request data
            $validatedData = $request->validate([
                'payment_id' => 'required|string',
                'paid' => 'required|numeric',
            ]);
            
            // Save payment details in the payments table
            $payment = Payment::create([
                'booking_id' => $booking->booking_id,
                'user_id' => $booking->user_id,
                'payment_id' => $validatedData['payment_id'],
                'payment_status' => 'paid',
                'paid' => $validatedData['paid'],
                'payment_type' => 'full',
                'payment_method' => 'razorpay', // or any method you want to specify
            ]);
            \Log::info('Payment details saved in the database', ['payment' => $payment]);
    
            \Log::info('Payment process completed successfully');
            return response()->json([
                'success' => true,
                'message' => 'Payment details saved successfully.',
                'data' => $payment
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Payment process failed', ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Payment process failed. Please try again.'
            ], 500);
        }
    }
    
    

    



    public function cancelBooking(Request $request, $booking_id)
    {
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();
    
        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    
        try {
            // Attempt to find the booking by its ID and user ID
            $booking = Booking::where('booking_id', $booking_id)
                              ->where('user_id', $user->userid)
                              ->first();
    
            if (!$booking) {
                // If the booking is not found, return an error response
                Log::warning('Booking not found', ['booking_id' => $booking_id, 'user_id' => $user->userid]);
                return response()->json(['error' => 'Booking not found.'], 404);
            }
    
            // Proceed with the rest of the cancellation logic
            $today = Carbon::today();
            $bookingDate = Carbon::parse($booking->booking_date);
            $daysDifference = $bookingDate->diffInDays($today);
    
            // Validate request data
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
                               ->where('user_id', $user->userid)
                               ->get();
    
            if ($payments->isEmpty()) {
                Log::warning('No payment found for booking_id', ['booking_id' => $booking_id]);
                return response()->json(['error' => 'No payments found for this booking.'], 404);
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
            Log::info('Booking canceled successfully', [
                'booking_id' => $booking->booking_id,
                'refund_amount' => $refundAmount
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Booking canceled successfully!',
                'refund_amount' => '₹' . sprintf('%.2f', $refundAmount)
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Booking cancellation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to cancel booking. Please try again.'], 500);
        }
    }
    
    


    
    
}
