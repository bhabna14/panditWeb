<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Payment;

use App\Models\Profile;
use App\Models\Poojalist;

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

    public function updateUserProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'phonenumber' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            // 'dob' => 'nullable|date',
            'about' => 'nullable|string',
            'gender' => 'nullable|string',
            'userphoto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
    
        // Using Sanctum guard to get authenticated user
        $user = Auth::guard('sanctum')->user();
    
        // Check if user is authenticated
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
    
        $user->name = $request->input('name');
        // $user->mobile_number = $request->input('phonenumber');
        $user->email = $request->input('email');
        // $user->dob = $request->input('dob');
        $user->about = $request->input('about');
        $user->gender = $request->input('gender');
    
        if ($request->hasFile('userphoto')) {
            // Delete the old avatar if it exists
            if ($user->userphoto && Storage::exists($user->userphoto)) {
                Storage::delete($user->userphoto);
            }
    
            $avatarPath = $request->file('userphoto')->store('avatars', 'public');
            $user->userphoto = $avatarPath;
        }
    
        $user->save();
    
        return response()->json(['success' => true, 'message' => 'Profile updated successfully.', 'user' => $user], 200);
    }

    public function updateUserPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userphoto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        if ($request->hasFile('userphoto')) {
            if ($user->userphoto && Storage::exists($user->userphoto)) {
                Storage::delete($user->userphoto);
            }

            $avatarPath = $request->file('userphoto')->store('avatars', 'public');
            $user->userphoto = $avatarPath;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User photo updated successfully.',
                'user' => $user,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'User photo not uploaded.',
        ], 400);
    }

// public function orderHistory(Request $request)
// {
//     // Get the authenticated user
//     $user = Auth::guard('sanctum')->user();

//     // Fetch recent bookings for the user
//     $bookings = Booking::with(['poojalist', 'pandit', 'address', 'ratings']) // Load relationships to get pooja details and ratings
//                         ->where('user_id', $user->userid)
//                         ->orderByDesc('created_at')
//                         ->get();

//     // Append URLs for pooja_video, pooja_photo, profile_photo, and rating media files
//     $bookings->each(function ($booking) {
//         // Check if poojalist exists before accessing its properties
//         if ($booking->poojalist) {
//             // Append URLs for pooja_photo in poojalist
//             if ($booking->poojalist->pooja_photo) {
//                 $booking->poojalist->pooja_photo_url = asset('assets/img/' . $booking->poojalist->pooja_photo);
//             }
//         }

//         // Append URL for profile_photo
//         if ($booking->pandit && $booking->pandit->profile_photo) {
//             $booking->pandit->profile_photo_url = asset($booking->pandit->profile_photo);
//         }

//         // Include ratings and their media file URLs as an object
//         if ($booking->ratings) {
//             $rating = $booking->ratings->first(); // Assuming only one rating per booking

//             if ($rating) {
//                 $rating->rating_date = $rating->created_at->format('Y-m-d');
//                 $rating->image_url = $rating->image_path ? asset(Storage::url($rating->image_path)) : null;
//                 $rating->audio_url = $rating->audio_file ? asset(Storage::url($rating->audio_file)) : null;

//                 // Append rating details as an object in the booking
//                 $booking->rating_details = $rating->toArray();
//             } else {
//                 $booking->rating_details = null; // No ratings available
//             }
//         } else {
//             $booking->rating_details = null; // No ratings relationship
//         }

//         // Remove the ratings relationship to avoid redundancy
//         unset($booking->ratings);
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Order history fetched successfully.',
//         'bookings' => $bookings,
//     ], 200);
// } 

public function orderHistory(Request $request)
{
    // Get the authenticated user
    $user = Auth::guard('sanctum')->user();

    // Fetch recent bookings for the user
    $bookings = Booking::with(['pooja.poojalist', 'pandit', 'address', 'ratings'])
                        ->where('user_id', $user->userid)
                        ->orderByDesc('created_at')
                        ->get();

    $bookings->each(function ($booking) {
        // Append URLs for pooja_photo in poojalist
        if ($booking->pooja && $booking->pooja->poojalist) {
            if ($booking->pooja->poojalist->pooja_photo) {
                $booking->pooja->poojalist->pooja_photo_url = asset('assets/img/' . $booking->pooja->poojalist->pooja_photo);
            }
        }

        // Append URL for profile_photo
        if ($booking->pandit->profile_photo) {
            $booking->pandit->profile_photo_url = asset($booking->pandit->profile_photo);
        }

        // Include ratings and their media file URLs as an object
        if ($booking->ratings) {
            $rating = $booking->ratings->first(); // Assuming only one rating per booking

            if ($rating) {
                $rating->rating_date = $rating->created_at->format('Y-m-d');
                $rating->image_url = $rating->image_path ? asset(Storage::url($rating->image_path)) : null;
                $rating->audio_url = $rating->audio_file ? asset(Storage::url($rating->audio_file)) : null;

                // Append rating details as an object in the booking
                $booking->rating_details = $rating->toArray();
            } else {
                $booking->rating_details = null; // No ratings available
            }
        } else {
            $booking->rating_details = null; // No ratings relationship
        }

        // Remove the ratings relationship to avoid redundancy
        unset($booking->ratings);

        // Fetch the latest payment directly
        $latestPayment = Payment::where('booking_id', $booking->booking_id)
                                ->orderByDesc('created_at')
                                ->first(); // Get the most recent payment

        if ($latestPayment) {
            $latestPayment->payment_date = $latestPayment->created_at->format('Y-m-d');
            $latestPayment->payment_method_url = $latestPayment->payment_method_image ? asset('assets/img/' . $latestPayment->payment_method_image) : null;

            // Assign the latest payment to the payment attribute
            $booking->payment = $latestPayment;
        } else {
            $booking->payment = null; // No payment details available
        }
    });

    return response()->json([
        'success' => true,
        'message' => 'Order history fetched successfully.',
        'bookings' => $bookings,
    ], 200);
}








// public function orderHistory(Request $request)
// {
//     // Get the authenticated user
//     $user = Auth::guard('sanctum')->user();

//     // Fetch recent bookings for the user
//     $bookings = Booking::with(['pooja.poojalist', 'pandit', 'address', 'ratings']) // Load relationships to get pooja details and ratings
//                         ->where('user_id', $user->userid)
//                         ->orderByDesc('created_at')
//                         ->get();

//     // Append URLs for pooja_video, pooja_photo, profile_photo, and rating media files
//     $bookings->each(function ($booking) {
//         // Append URLs for pooja_video
//         if ($booking->pooja && $booking->pooja->pooja_video) {
//             $booking->pooja->pooja_video_url = asset($booking->pooja->pooja_video);
//         }

//         // Append URLs for pooja_photo
//         if ($booking->pooja->poojalist->pooja_photo) {
//             $booking->pooja->poojalist->pooja_photo_url = asset('assets/img/' . $booking->pooja->poojalist->pooja_photo);
//         }

//         // Append URL for profile_photo
//         if ($booking->pandit->profile_photo) {
//             $booking->pandit->profile_photo_url = asset($booking->pandit->profile_photo);
//         }

//         // Include ratings and their media file URLs as an object
//         if ($booking->ratings) {
//             $rating = $booking->ratings->first(); // Assuming only one rating per booking

//             if ($rating) {
//                 $rating->rating_date = $rating->created_at->format('Y-m-d');
//                 $rating->image_url = $rating->image_path ? asset(Storage::url($rating->image_path)) : null;
//                 $rating->audio_url = $rating->audio_file ? asset(Storage::url($rating->audio_file)) : null;

//                 // Append rating details as an object in the booking
//                 $booking->rating_details = $rating->toArray();
//             } else {
//                 $booking->rating_details = null; // No ratings available
//             }
//         } else {
//             $booking->rating_details = null; // No ratings relationship
//         }

//         // Remove the ratings relationship to avoid redundancy
//         unset($booking->ratings);
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Order history fetched successfully.',
//         'bookings' => $bookings,
//     ], 200);
// }


    
    public function manageAddress(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Fetch managed addresses for the user
        $addressData = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();

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
            // 'fullname' => 'required|string|max:255',
            // 'number' => 'required|string|max:20',
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
        // $addressData->fullname = $validatedData['fullname'];
        // $addressData->number = $validatedData['number'];
        $addressData->country = $validatedData['country'];
        $addressData->state = $validatedData['state'];
        $addressData->city = $validatedData['city'];
        $addressData->pincode = $validatedData['pincode'];
        $addressData->area = $validatedData['area'];
        $addressData->address_type = $validatedData['address_type'];
        $addressData->status = 'active';
        // $addressdata->default = '0';

        // Save the address
        $addressData->save();

        return response()->json([
            'success' => 200,
            'message' => 'Address created successfully'
            ]
            , 201);
    }

    // public function removeAddress($id)
    // {
    //     // Find the address by ID
    //     $address = UserAddress::find($id);

    //     if ($address) {
    //         // Delete the address
    //         $address->delete();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Address removed successfully.'
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Address not found.'
    //         ], 404);
    //     }
    // }

    public function removeAddress($id)
    {
        // Find the address by ID
        $address = UserAddress::find($id);

        if ($address) {
            // Set the status to 'inactive' instead of deleting
            $address->status = 'inactive';
            $address->save();
            
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
            // 'fullname' => 'required|string|max:255',
            // 'number' => 'required|string|max:15',
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
            // $address->fullname = $request->fullname;
            // $address->number = $request->number;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->pincode = $request->pincode;
            $address->area = $request->area;
            $address->address_type = $request->address_type;
            $addressData->status = 'active';
            // $addressdata->default = '0';
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
    public function combinedSearch(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
    
        // Search for pandits
        $pandits = Profile::where('name', 'LIKE', '%' . $searchTerm . '%')
                            ->where('pandit_status','accepted')
                            ->get();
    
        // Search for poojas
        $poojas = Poojalist::where('pooja_name', 'LIKE', '%' . $searchTerm . '%')
        ->where('status','active')->get();
    
        // if ($pandits->isEmpty() && $poojas->isEmpty()) {
        //     return response()->json([
        //         'message' => 'No data found'
        //     ], 404);
        // }

        $data = [
            'pandits' => $pandits->isEmpty() ? [] : $pandits->map(function ($pandit) {
                // Generate the URL for the profile photo
                $pandit->profile_photo = $pandit->profile_photo ? asset($pandit->profile_photo) : null;
                return $pandit;
            }),
            'poojas'  => $poojas->isEmpty() ? [] : $poojas->map(function ($pooja) {
                // Generate the URL for the pooja image
                $pooja->pooja_img_url = $pooja->pooja_photo ?  asset('assets/img/' . $pooja->pooja_photo) : null;
                return $pooja;
            }),
        ];
    
        return response()->json([
           'success' => true,
            'message' => 'Search Result Fetched Successfully.',
            'date' => $data
        ]);
    }
    public function setDefault($id)
    {
        $address = UserAddress::findOrFail($id);

        // Ensure the address belongs to the authenticated user
        if ($address->user_id != Auth::guard('sanctum')->user()->userid) {
            return response()->json(['error' => 'You do not have permission to set this address as default.'], 403);
        }

        // Set the address as default
        $address->setAsDefault();

        return response()->json(['success' => 'Address set as default successfully.'], 200);
    }
    
    
}
