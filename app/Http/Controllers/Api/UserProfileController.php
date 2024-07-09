<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;



class UserProfileController extends Controller
{
    //
    public function orderHistory(Request $request)
    {
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();

        // Fetch recent bookings for the user
        $bookings = Booking::with('pooja.poojalist', 'pandit', 'address') // Load relationships to get pooja details
                            ->where('user_id', $user->userid)
                            ->orderByDesc('created_at')
                            ->take(10) // Limit to 10 recent bookings (adjust as needed)
                            ->get();

        return response()->json([
            'success' => 200,
            'message' => 'Order history fetched successfully.',
            'bookings' => $bookings,
        ], 200);
    }
}
