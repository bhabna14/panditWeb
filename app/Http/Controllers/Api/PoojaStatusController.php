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

use App\Events\BookingApproved;


class PoojaStatusController extends Controller
{
    public function approveBooking($id)
{
    try {
        $booking = Booking::findOrFail($id);
        $booking->application_status = 'approved';
        $booking->save();

        // Broadcast the event
        event(new BookingApproved($booking));

        return response()->json([
            'status' => 200,
            'message' => 'Booking approved successfully!',
            'booking' => $booking
        ], 200);
    } catch (\Exception $e) {
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
        $booking = Booking::findOrFail($id);
        $booking->application_status = 'rejected';
        $booking->status = 'rejected';
        $booking->cancel_reason = $request->cancel_reason;
        $booking->save();

        // Broadcast the event or perform any other necessary actions here

        return response()->json([
            'status' => 200,
            'message' => 'Booking rejected successfully!',
            'booking' => $booking
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Failed to reject booking.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function start(Request $request)
{
    try {
        // Validate the request
        $validatedData = $request->validate([
            'booking_id' => 'required|string',
            'pooja_id' => 'required|string',
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
                'application_status' => 'started',
                'status' => 'started',
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
        // Retrieve the booking_id and pooja_id from the request
        $booking_id = $request->input('booking_id');
        $pooja_id = $request->input('pooja_id');
        
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
            
            // Update the record in the pooja_status table
            $updated = DB::table('pooja_status')
                ->where('booking_id', $booking_id)
                ->where('pooja_id', $pooja_id)
                ->update([
                    'end_time' => $end_time,
                    'pooja_duration' => $duration, // Store the duration in H:i:s format
                    'pooja_status' => 'completed'  // Update the status to 'completed'
                ]);

            $bookingUpdated = DB::table('bookings')
                ->where('booking_id', $booking_id)
                ->update([
                    'application_status' => 'completed',
                    'status' => 'completed',
                ]);
    
            // Return success or error message
            if ($updated) {
                return response()->json(['message' => 'Pooja ended successfully.'], 200);
            } else {
                return response()->json(['message' => 'Failed to end pooja.'], 500);
            }
        } else {
            // If no start time is found, return an error
            return response()->json(['message' => 'Pooja start time not found.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while ending the Pooja.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
