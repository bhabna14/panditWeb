<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class BookingController extends Controller
{
    //
    public function confirmBooking(Request $request)
    {
        // dd("hi");
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'pandit_id' => 'required|exists:pandit_profile,id',
                'pooja_id' => 'required|exists:pandit_poojadetails,id',
                'pooja_fee' => 'required|numeric',
                'advance_fee' => 'required|numeric',
                'booking_date' => 'required|date',
                'booking_time' => 'required|string',
                'address_id' => 'required',
            ]);

            // Assign the authenticated user's ID to the booking
            $validatedData['user_id'] = Auth::guard('sanctum')->user()->userid;
            $validatedData['application_status'] = 'pending';
            $validatedData['status'] = 'pending';

            // Create a new booking record
            $booking = Booking::create($validatedData);

            // Log success message
            \Log::info('Booking created successfully.', ['data' => $validatedData]);

            // Return a JSON response
            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully!',
                'booking_id' => $booking->booking_id
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating booking: ' . $e->getMessage());

            // Return a JSON error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm booking. Please try again.'
            ], 500);
        }
    }
}
