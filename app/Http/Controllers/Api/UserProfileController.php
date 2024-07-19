<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\UserAddress;

use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    //
     public function getUserDetails()
    {
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            // Generate the full URL for userphoto
            if ($user->userphoto) {
                $user->userphoto = asset(Storage::url($user->userphoto));
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }

    public function updateProfile(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phonenumber' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'dob' => 'nullable|date',
            'about' => 'nullable|string',
            'gender' => 'nullable|string',
            'userphoto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the authenticated user
        $user = Auth::guard('users')->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Update user profile
        $user->name = $request->input('name');
        $user->mobile_number = $request->input('phonenumber');
        $user->email = $request->input('email');
        $user->dob = $request->input('dob');
        $user->about = $request->input('about');
        $user->gender = $request->input('gender');

        // Handle user photo upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($user->userphoto && Storage::exists($user->userphoto)) {
                Storage::delete($user->userphoto);
            }

            // Store the new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->userphoto = $avatarPath;
        }

        // Save the updated user profile
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ], 200);
    }

    public function orderHistory(Request $request)
    {
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();
    
        // Fetch recent bookings for the user
        $bookings = Booking::with('pooja.poojalist', 'pandit', 'address') // Load relationships to get pooja details
                            ->where('user_id', $user->userid)
                            ->orderByDesc('created_at')
                            // ->take(10) // Limit to 10 recent bookings (adjust as needed)
                            ->get();
    
        // Append URLs for pooja_video, pooja_photo, and profile_photo
        $bookings->each(function ($booking) {
            // Append URLs for pooja_video
            if ($booking->pooja && $booking->pooja->pooja_video) {
                $booking->pooja->pooja_video_url = asset($booking->pooja->pooja_video);
            }
    
            // Append URLs for pooja_photo
            if ($booking->pooja->poojalist->pooja_photo) {
                $booking->pooja->poojalist->pooja_photo_url =asset('assets/img/'.$booking->pooja->poojalist->pooja_photo);
            }
            // $booking->pandit->pooja_photo_url = asset('assets/img/'.$booking->pooja->poojalist->pooja_photo); // Adjust accordingly if profile_photo is stored elsewhere
    
            // Append URL for profile_photo (assuming it's stored in the User model)
            $booking->pandit->profile_photo_url = asset($booking->pandit->profile_photo); // Adjust accordingly if profile_photo is stored elsewhere
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Order history fetched successfully.',
            'bookings' => $bookings,
        ], 200);
    }
    
    public function manageAddress(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Fetch managed addresses for the user
        $addressData = UserAddress::where('user_id', $user->userid)->get();

        return response()->json([
            'success' => 200,
            'message' => 'Address fetched successfully.',
            'addressData' => $addressData
        ], 200);
    }
    public function saveAddress(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Validate request data (optional but recommended)
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'area' => 'required|string|max:255',
            'address_type' => 'required|string|max:50',
        ]);

        // Create new UserAddress instance and populate data
        $addressData = new UserAddress();
        $addressData->user_id = $user->userid;
        $addressData->fullname = $validatedData['fullname'];
        $addressData->number = $validatedData['number'];
        $addressData->country = $validatedData['country'];
        $addressData->state = $validatedData['state'];
        $addressData->city = $validatedData['city'];
        $addressData->pincode = $validatedData['pincode'];
        $addressData->area = $validatedData['area'];
        $addressData->address_type = $validatedData['address_type'];

        // Save the address
        $addressData->save();

        return response()->json([
            'success' => 200,
            'message' => 'Address created successfully'
            ]
            , 201);
    }

    public function removeAddress($id)
    {
        // Find the address by ID
        $address = UserAddress::find($id);

        if ($address) {
            // Delete the address
            $address->delete();
            return response()->json([
                'success' => true,
                'message' => 'Address removed successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.'
            ], 404);
        }
    }
    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:user_addresses,id',
            'fullname' => 'required|string|max:255',
            'number' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'area' => 'required|string|max:255',
            'address_type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $address = UserAddress::find($request->id);

        if ($address) {
            $address->fullname = $request->fullname;
            $address->number = $request->number;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->pincode = $request->pincode;
            $address->area = $request->area;
            $address->address_type = $request->address_type;
            $address->save();

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
                'address' => $address
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.'
            ], 404);
        }
    }

}
