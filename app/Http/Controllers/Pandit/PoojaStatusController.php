<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojastatus;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PoojaStatusController extends Controller
{
    public function start(Request $request)
    {
        // Retrieve the booking_id and pooja_id from the request
        $booking_id = $request->input('booking_id');
        $pooja_id = $request->input('pooja_id');
    
        // Fetch the booking record
        $booking = Booking::find($booking_id);
    
        // Check if the booking exists
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }
    
        // Fetch the pandit_id from the booking record
        $pandit_id = $booking->pandit_id;
    
        // Check if there is any ongoing Pooja for this Pandit
        $ongoingPooja = DB::table('pooja_status')
                          ->whereIn('booking_id', function($query) use ($pandit_id) {
                              $query->select('id')
                                    ->from('bookings')
                                    ->where('pandit_id', $pandit_id);
                          })
                          ->whereNull('end_time')
                          ->exists();
    
        if ($ongoingPooja) {
            return redirect()->back()->with('error', 'You must complete the ongoing Pooja before starting a new one.');
        }
    
        $start_time = Carbon::now();
    
        // Update or insert the record in the pooja_status table
        $statusUpdated = DB::table('pooja_status')->updateOrInsert(
            ['booking_id' => $booking_id, 'pooja_id' => $pooja_id],
            ['start_time' => $start_time, 'end_time' => null, 'pooja_status' => 'started']
        );
    
        // Redirect with success or error message
        if ($statusUpdated) {
            return redirect()->back()->with('success', 'Pooja started successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to start Pooja.');
        }
    }
    
public function end(Request $request)
{
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
            ->update(['application_status' => 'completed',
            'status' => 'completed',
        ]);

        // Redirect with success or error message
        if ($updated) {
            return redirect()->back()->with('success', 'Pooja ended successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to end pooja.');
        }
    } else {
        // If no start time is found, return an error
        return redirect()->back()->with('error', 'Pooja start time not found.');
    }
}


    
}