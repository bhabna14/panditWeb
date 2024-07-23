<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RatingController extends Controller
{
    public function submitOrUpdateRating(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|between:1,5',
            'feedback_message' => 'nullable|string',
            'audioFile' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'rating_id' => 'nullable|exists:ratings,id', // For updating an existing rating
        ]);

        // Determine if this is a new rating or an update
        $rating = $request->has('rating_id') 
            ? Rating::findOrFail($request->rating_id) 
            : new Rating();

        // Fill rating details
        $rating->user_id = Auth::guard('users')->user()->userid; // Save the authenticated user's ID
        $rating->booking_id = $validatedData['booking_id'];
        $rating->rating = $validatedData['rating'];
        $rating->feedback_message = $validatedData['feedback_message'];

        // Handle audio file upload
        if ($request->hasFile('audioFile')) {
            // Delete old audio file if exists
            if ($rating->audio_file) {
                Storage::disk('public')->delete($rating->audio_file);
            }
            $audioPath = $request->file('audioFile')->store('audio', 'public');
            $rating->audio_file = $audioPath;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($rating->image_path) {
                Storage::disk('public')->delete($rating->image_path);
            }
            $imagePath = $request->file('image')->store('images', 'public');
            $rating->image_path = $imagePath;
        }

        $rating->save();

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully!',
            'rating' => $rating
        ], 200);
    }
}

