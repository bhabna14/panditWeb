<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
               
                'address_id' => 'required',
            ]);

            // Assign the authenticated user's ID to the booking
            $validatedData['user_id'] = Auth::guard('sanctum')->user()->userid;
            $validatedData['application_status'] = 'pending';
            $validatedData['payment_status'] = 'pending';
            $validatedData['status'] = 'pending';

            // Create a new booking record
            $booking = Booking::create($validatedData);

            // Log success message
            \Log::info('Booking created successfully.', ['data' => $validatedData]);

            $booking->load(['user', 'pandit', 'pooja', 'address']);
            $booking->pooja->pooja_photo =asset('assets/img/'.$booking->pooja->pooja_photo);
            $booking->pandit->profile_photo = asset($booking->pandit->profile_photo);
            $booking->user->userphoto = asset(Storage::url($booking->user->userphoto));

            // Return a success response with the booking details
            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully!',
                'booking' => $booking
            ], 201);
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
    public function processPayment(Request $request, $booking_id)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'payment_id' => 'required|string',
                // 'application_status' => 'required|string',
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
        
            // Update booking with payment details
            $booking->payment_id = $validatedData['payment_id'];
            // $booking->application_status = $validatedData['application_status'];
            $booking->payment_status = $validatedData['payment_status'];
            $booking->status = $validatedData['status'];
            $booking->paid = $validatedData['paid'];
            $booking->payment_type = $validatedData['payment_type'];
            $booking->payment_method = $validatedData['payment_method'];
        
            // Save booking and check for errors
            if (!$booking->save()) {
                \Log::error('Failed to save booking', ['booking_id' => $booking_id]);
                return response()->json(['error' => 'Failed to save payment details. Please try again.'], 500);
            }
        
            return response()->json(['success' => 'Payment details saved successfully!', 'booking' => $booking], 200);
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
    



    public function cancelBooking(Request $request, $booking_id)
{
    // dd("hi");
    try {
        $booking = Booking::where('booking_id', $booking_id)->firstOrFail();
        $today = Carbon::today();
        $bookingDate = Carbon::parse($booking->booking_date);
        $daysDifference = $bookingDate->diffInDays($today);

        $validator = Validator::make($request->all(), [
            'cancel_reason' => 'required|string|max:255',
            'refund_method' => 'required|string|in:original',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Log the booking details and cancellation request
        Log::info('Booking cancellation requested', [
            'booking_id' => $booking->id,
            'booking_date' => $booking->booking_date,
            'today' => $today,
            'days_difference' => $daysDifference,
            'cancel_reason' => $validatedData['cancel_reason'],
            'refund_method' => $validatedData['refund_method']
        ]);

        if ($daysDifference > 20) {
            $refundAmount = $booking->pooja_fee;
        } elseif ($daysDifference > 1 && $daysDifference <= 20) {
            $refundAmount = $booking->paid * 0.80; // 20% cancellation fee
        } else {
            $refundAmount = 0; // No refund
        }

        $booking->status = 'canceled';
        $booking->payment_status = 'process';
        $booking->application_status = 'canceled';
        $booking->canceled_at = now();
        $booking->cancel_reason = $validatedData['cancel_reason'];
        $booking->refund_method = $validatedData['refund_method'];
        $booking->refund_amount = $refundAmount;
        $booking->save();

        // Log booking cancellation
        Log::info('Booking canceled successfully', [
            'booking_id' => $booking->id,
            'refund_amount' => $refundAmount
        ]);

        return response()->json(['success' => 'Booking canceled successfully!', 'booking' => $booking], 200);
    } catch (\Exception $e) {
        Log::error('Booking cancellation failed: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to cancel booking. Please try again.'], 500);
    }
}
    
    
}
