<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function submitRating(Request $request)
    {
        // dd("hi");
        // dd($request->all());
        // Validate incoming request data
        $validatedData = $request->validate([
            'booking_id' => 'required',
            'rating' => 'required|integer|between:1,5',
            'feedback_message' => 'nullable|string',
            'audioFile' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
        // dd($validatedData);


        // Create a new rating
        $rating = new Rating();
        $rating->user_id = Auth::guard('api')->user()->userid; // Save the authenticated user's ID
        $rating->booking_id = $validatedData['booking_id'];
        $rating->rating = $validatedData['rating'];
        $rating->feedback_message = $validatedData['feedback_message'];
        // dd($rating);
        // Handle audio file upload
        if ($request->hasFile('audioFile')) {
            $audioPath = $request->file('audioFile')->store('audio', 'public');
            $rating->audio_file = $audioPath;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $rating->image_path = $imagePath;
        }

        $rating->save();

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully!',
            'rating' => $rating
        ], 201);
    }

    public function updateRating(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'rating_id' => 'required|exists:ratings,id',
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|between:1,5',
            'feedback_message' => 'nullable|string',
            'audioFile' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Find the existing rating
        $rating = Rating::findOrFail($validatedData['rating_id']);
        $rating->user_id = Auth::guard('sanctum')->user()->userid; // Save the authenticated user's ID
        $rating->booking_id = $validatedData['booking_id'];
        $rating->rating = $validatedData['rating'];
        $rating->feedback_message = $validatedData['feedback_message'];

       // Handle audio file upload if present
    if ($request->hasFile('audioFile')) {
        $audioPath = $request->file('audioFile')->store('audio', 'public');
        $rating->audio_file = $audioPath;
    }

    // Handle image upload if present
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $rating->image_path = $imagePath;
    }

        $rating->save();

        return response()->json([
            'success' => true,
            'message' => 'Rating updated successfully!',
            'rating' => $rating
        ], 200);
    }

    public function showRating($id)
    {
        // Find the rating by ID
        $rating = Rating::findOrFail($id);

        // Append the full URL for the image and audio file
        $rating->image_url = $rating->image_path ? asset(Storage::url($rating->image_path)) : null;
        $rating->audio_url = $rating->audio_file ? asset(Storage::url($rating->audio_file)) : null;

        return response()->json([
            'success' => true,
            'rating' => $rating
        ], 200);
    }
}

