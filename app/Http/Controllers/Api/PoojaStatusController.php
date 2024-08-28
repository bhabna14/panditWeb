<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Poojastatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PanditCancel;
use Illuminate\Support\Facades\Log;
use App\Models\UserDevice;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\Messaging;

use App\Events\BookingApproved;


class PoojaStatusController extends Controller
{
//     public function approveBooking($id)
// {
//     try {
//         $booking = Booking::findOrFail($id);
//         $booking->application_status = 'approved';
//         $booking->save();

//         // Broadcast the event
//         event(new BookingApproved($booking));

//         return response()->json([
//             'status' => 200,
//             'message' => 'Booking approved successfully!',
//             'booking' => $booking
//         ], 200);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 500,
//             'message' => 'Failed to approve booking.',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }

public function approveBooking($id)
{
    try {
        Log::info('Attempting to approve booking with ID: ' . $id);

        // Find and approve the booking
        $booking = Booking::findOrFail($id);
        Log::info('Booking found: ' . json_encode($booking));

        $booking->application_status = 'approved';
        $booking->save();
        Log::info('Booking status updated to approved for booking ID: ' . $id);

        // Find all user devices using user_id from the booking
        $factory = (new Factory)->withServiceAccount(config('services.firebase.user.credentials'));
        $messaging = $factory->createMessaging();
        Log::info('Firebase Messaging instance created successfully.');

        $userDevices = UserDevice::where('user_id', $booking->user_id)->get();
        Log::info('User devices fetched: ' . json_encode($userDevices));

        if ($userDevices->isNotEmpty()) {
            foreach ($userDevices as $userDevice) {
                $deviceToken = $userDevice->device_id;
                Log::info('Sending notification to device token: ' . $deviceToken);

                // Prepare the notification message
                $message = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification(Notification::create(
                        'Booking Approved',
                        "Your booking with ID: {$booking->booking_id} has been approved. Please check your account for details."
                    ))
                    ->withData([
                        'booking_id' => $booking->booking_id,
                        'user_id' => $booking->user_id,
                        'message' => 'Your booking has been approved.',
                        // 'url' => route('user.bookingDetails', ['id' => $booking->booking_id])
                    ]);
                Log::info('Notification message prepared: ' . json_encode($message));

                try {
                    $messaging->send($message);
                    Log::info('FCM notification sent successfully to device token: ' . $deviceToken);
                } catch (\Exception $e) {
                    Log::error('Error sending FCM notification to device token ' . $deviceToken . ': ' . $e->getMessage());
                }
            }
        } else {
            Log::warning('No device tokens found for user ID: ' . $booking->user_id);
        }

        // Broadcast the event
        event(new BookingApproved($booking));
        Log::info('BookingApproved event broadcasted for booking ID: ' . $id);

        return response()->json([
            'status' => 200,
            'message' => 'Booking approved and user(s) notified successfully!',
            'booking' => $booking
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error in approveBooking method: ' . $e->getMessage());
        return response()->json([
            'status' => 500,
            'message' => 'Failed to approve booking.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function rejectBooking(Request $request, $id)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'pandit_cancel_reason' => 'required|string|max:255',
            ]);

            // Find the booking by ID
            $booking = Booking::findOrFail($id);
            
            // Update the booking status to 'rejected'
            $booking->status = 'rejected';
            $booking->application_status = 'rejected';
            $booking->payment_status = 'rejected';
            $booking->pooja_status = 'rejected';
            $booking->save();
        
            // Get the authenticated pandit
            // $pandit = Auth::guard('pandits')->user();
            $pandit = Auth::guard('sanctum')->user();
        
            // Save to PanditCancel table
            PanditCancel::create([
                'pandit_id' => $pandit->pandit_id,
                'booking_id' => $booking->booking_id,  // Save the booking_id
                'pandit_cancel_reason' => $validatedData['pandit_cancel_reason'],
            ]);

              // Find all user devices using user_id from the booking
        $factory = (new Factory)->withServiceAccount(config('services.firebase.user.credentials'));
        $messaging = $factory->createMessaging();
        Log::info('Firebase Messaging instance created successfully.');

        $userDevices = UserDevice::where('user_id', $booking->user_id)->get();
        Log::info('User devices fetched: ' . json_encode($userDevices));

        if ($userDevices->isNotEmpty()) {
            foreach ($userDevices as $userDevice) {
                $deviceToken = $userDevice->device_id;
                Log::info('Sending notification to device token: ' . $deviceToken);

                // Prepare the notification message
                $message = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification(Notification::create(
                        'Booking Rejected',
                        "Your booking with ID: {$booking->booking_id} has been Rejected. Please check your booking details."
                    ))
                    ->withData([
                        'booking_id' => $booking->booking_id,
                        'user_id' => $booking->user_id,
                        'message' => 'Your booking has been Rejected.',
                        // 'url' => route('user.bookingDetails', ['id' => $booking->booking_id])
                    ]);
                Log::info('Notification message prepared: ' . json_encode($message));

                try {
                    $messaging->send($message);
                    Log::info('FCM notification sent successfully to device token: ' . $deviceToken);
                } catch (\Exception $e) {
                    Log::error('Error sending FCM notification to device token ' . $deviceToken . ': ' . $e->getMessage());
                }
            }
        } else {
            Log::warning('No device tokens found for user ID: ' . $booking->user_id);
        }
        
            // Return a success response
            return response()->json(['message' => 'Booking rejected successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error('An error occurred while rejecting the booking.', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'An error occurred while rejecting the booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function start(Request $request)
{
    try {
        // Validate the request
        $validatedData = $request->validate([
            'booking_id' => 'required',
            'pooja_id' => 'required',
        ]);

        // Retrieve the booking_id and pooja_id from the request
        $booking_id = $validatedData['booking_id'];
        $pooja_id = $validatedData['pooja_id'];

        // Fetch the booking record
        $booking = Booking::where('booking_id', $booking_id)->first();

        // Check if the booking exists
        if (!$booking) {
            \Log::error('Booking not found', ['booking_id' => $booking_id]);
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        // Fetch the pandit_id from the booking record
        $pandit_id = $booking->pandit_id;

        // Check if there is any ongoing Pooja for this Pandit
        $ongoingPooja = DB::table('pooja_status')
            ->whereIn('booking_id', function($query) use ($pandit_id) {
                $query->select('booking_id')
                    ->from('bookings')
                    ->where('pandit_id', $pandit_id);
            })
            ->whereNull('end_time')
            ->exists();

        if ($ongoingPooja) {
            return response()->json(['message' => 'You must complete the ongoing Pooja before starting a new one.'], 400);
        }

        $start_time = Carbon::now();

        // Update or insert the record in the pooja_status table
        $statusUpdated = DB::table('pooja_status')->updateOrInsert(
            ['booking_id' => $booking_id, 'pooja_id' => $pooja_id],
            ['start_time' => $start_time, 'end_time' => null, 'pooja_status' => 'started']
        );

        $bookingUpdated = DB::table('bookings')
            ->where('booking_id', $booking_id)
            ->update([
               'pooja_status' => 'started'
                
            ]);

        // Return success or error message
        if ($statusUpdated && $bookingUpdated) {
            return response()->json(['message' => 'Pooja started successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to start Pooja.'], 500);
        }
    } catch (\Exception $e) {
        \Log::error('An error occurred while starting the Pooja.', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'An error occurred while starting the Pooja.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function end(Request $request)
{
    try {
        // Validate the request
        $validatedData = $request->validate([
            'booking_id' => 'required',
            'pooja_id' => 'required',
        ]);

        // Retrieve the booking_id and pooja_id from the request
        $booking_id = $validatedData['booking_id'];
        $pooja_id = $validatedData['pooja_id'];
        
        $end_time = Carbon::now();
        
        // Fetch the pooja_status record for the given booking_id and pooja_id
        $status = DB::table('pooja_status')
                    ->where('booking_id', $booking_id)
                    ->where('pooja_id', $pooja_id)
                    ->first();
        
        if ($status && $status->start_time) {
            // Calculate the duration in seconds
            $start_time = Carbon::parse($status->start_time);
            $durationInSeconds = $end_time->diffInSeconds($start_time);
            
            // Convert duration to H:i:s format
            $hours = intdiv($durationInSeconds, 3600);
            $minutes = intdiv($durationInSeconds % 3600, 60);
            $seconds = $durationInSeconds % 60;
            
            $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            // Check if full payment is made
            $fullPayment = DB::table('payments')
                ->where('booking_id', $booking_id)
                ->where('payment_type', 'full')
                ->first();

            if (!$fullPayment) {
                return response()->json(['message' => 'User has not made full payment for Pooja.'], 400);
            }

            // Update the record in the pooja_status table
            $updated = DB::table('pooja_status')
                ->where('booking_id', $booking_id)
                ->where('pooja_id', $pooja_id)
                ->update([
                    'end_time' => $end_time,
                    'pooja_duration' => $duration, // Store the duration in H:i:s format
                    'pooja_status' => 'completed'  // Update the status to 'completed'
                ]);

            // Update the status in the bookings table
            $bookingUpdated = DB::table('bookings')
                ->where('booking_id', $booking_id)
                ->update([
                    'pooja_status' => 'completed',
                ]);

            // Return success or error message
            if ($updated && $bookingUpdated) {
                return response()->json(['message' => 'Pooja ended successfully.'], 200);
            } else {
                return response()->json(['message' => 'Failed to end Pooja.'], 500);
            }
        } else {
            // If no start time is found, return an error
            return response()->json(['message' => 'Pooja start time not found.'], 404);
        }
    } catch (\Exception $e) {
        \Log::error('An error occurred while ending the Pooja.', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'An error occurred while ending the Pooja.',
            'error' => $e->getMessage()
        ], 500);
    }
}




}
