<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Locality;
use App\Models\Apartment;

use App\Models\Promonation;

use App\Models\Profile;
use App\Models\Rating;

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
            'name' => 'string|max:255',
            // 'phonenumber' => 'required|string|max:15',
            'email' => 'email|max:255',
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

    // Fetch recent bookings for the user without loading ratings
    $bookings = Booking::with(['pooja.poojalist', 'pandit', 'address.localityDetails'])
                        ->where('user_id', $user->userid)
                        ->orderByDesc('created_at')
                        ->get();

    // Fetch and attach ratings to each booking
    $bookings->each(function ($booking) {
        // Append URLs for pooja_photo in poojalist
        if ($booking->pooja && $booking->pooja->poojalist) {
            if ($booking->pooja->poojalist->pooja_photo) {
                $booking->pooja->poojalist->pooja_photo_url = asset('assets/img/' . $booking->pooja->poojalist->pooja_photo);
            }
        }

        // Append URL for profile_photo
        if ($booking->pandit && $booking->pandit->profile_photo) {
            $booking->pandit->profile_photo_url = asset($booking->pandit->profile_photo);
        }

        // Fetch the rating for the current booking
        $rating = Rating::where('booking_id', $booking->booking_id)->first(); // Get the rating for the booking

        if ($rating) {
            $rating->rating_date = $rating->created_at->format('Y-m-d');
            $rating->image_url = $rating->image_path ? asset(Storage::url($rating->image_path)) : null;
            $rating->audio_url = $rating->audio_file ? asset(Storage::url($rating->audio_file)) : null;

            // Append rating details as an object in the booking
            $booking->rating_details = $rating->toArray();
        } else {
            $booking->rating_details = null; // No ratings available
        }

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

    public function deletePhoto()
    {
        // Authenticate the user
        $user = Auth::guard('sanctum')->user();
        
        // Log the user ID attempting to delete the photo
        \Log::info('User ID ' . $user->userid . ' is attempting to delete their photo.');

        // Check if the user has a photo
        if ($user->userphoto) {
            try {
                // Delete the photo from storage
                Storage::delete('public/' . $user->userphoto);

                // Update the user's photo column in the database
                $user->update(['userphoto' => null]);

                // Log success message
                \Log::info('Photo deleted successfully for User ID ' . $user->userid);

                return response()->json(['message' => 'Photo deleted successfully'], 200);
            } catch (\Exception $e) {
                // Log error if deletion fails
                \Log::error('Failed to delete photo for User ID ' . $user->userid . ': ' . $e->getMessage());

                return response()->json(['message' => 'Failed to delete photo'], 500);
            }
        }

        // Log if no photo found for deletion
        \Log::info('No photo found for deletion for User ID ' . $user->userid);
        return response()->json([
            'success' => 200,
            'message' => 'No photo found for deletion'], 404);
    }

    // public function getActiveLocalities()
    // {
    //     $localities = Locality::where('status', 'active')->get();

    //     return response()->json([
    //         'success' => 200,
    //         'data' => $localities,
    //     ], 200);
    // }
    
    public function getActiveLocalities()
    {
        // Fetch localities with their apartments
        $localities = Locality::where('status', 'active')
            ->with(['apartment' => function ($query) {
                $query->select('id', 'locality_id', 'apartment_name'); // Fetch only necessary fields
            }])
            ->get();
    
        // Structure response
        $response = $localities->map(function ($locality) {
            return [
                'locality_id' => $locality->id,
                'locality_name' => $locality->locality_name,
                'pincode' => $locality->pincode,
                'unique_code' => $locality->unique_code,

                'apartment' => $locality->apartment->map(function ($apartment) {
                    return [
                        'apartment_id' => $apartment->id,
                        'locality_id' => $apartment->locality_id,
                        'apartment_name' => $apartment->apartment_name,
                    ];
                }),
            ];
        });
    
        // Return JSON response
        return response()->json([
            'success' => 200,
            'data' => $response,
        ], 200);
    }
    
    public function managepromonation()
    {
        $promonations = Promonation::where('status', 'active')->get();

        // Map the image field to include the full URL
        $promonations->transform(function($promonation) {
            // Assuming 'promonation_image' is the field that contains the image filename
            $promonation->promonation_image = url('images/promonations/' . $promonation->promonation_image);
            return $promonation;
        });

        return response()->json([
            'status' => 200,
            'data' => $promonations
        ], 200);
    }

    public function manageAddress(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
    
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        // Fetch managed addresses for the user with locality details
        $addressData = UserAddress::where('user_id', $user->userid)
            ->where('status', 'active')
            ->with('localityDetails') // Eager load the localityDetails relationship
            ->orderBy('id', 'desc')
            ->get();
    
        return response()->json([
            'success' => 200,
            'message' => 'Address fetched successfully.',
            'addressData' => $addressData
        ], 200);
    }
    
    // public function saveAddress(Request $request)
    // {
    //     try {
    //         $user = Auth::guard('api')->user();
    
    //         if (!$user) {
    //             return response()->json(['error' => 'Unauthorized'], 401);
    //         }
    
    //         $userid = $user->userid;
    
    //         // Check if the user already has addresses
    //         $hasAddresses = UserAddress::where('user_id', $userid)
    //                                     ->where('status', 'active')
    //                                     ->exists();
    
    //         // Create the new address
    //         $addressdata = new UserAddress();
    //         $addressdata->user_id = $userid;
    //         $addressdata->country = 'India';
    //         $addressdata->state = $request->state;
    //         $addressdata->city = $request->city;
    //         $addressdata->pincode = $request->pincode;
    //         $addressdata->area = $request->area;
    //         $addressdata->address_type = $request->address_type;
    //         $addressdata->locality = $request->locality;
    //         $addressdata->apartment_name = $request->apartment_name;
    //         $addressdata->place_category = $request->place_category;
    //         $addressdata->apartment_flat_plot = $request->apartment_flat_plot;
    //         $addressdata->landmark = $request->landmark;
    //         $addressdata->status = 'active';
    
    //         // Set as default if it's the first address
    //         if (!$hasAddresses) {
    //             $addressdata->default = 1;
    //         }
    
    //         $addressdata->save();

    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Address created successfully.'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 500, 'message' => $e->getMessage()], 500);
    //     }
    // }
    
    public function saveAddress(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userid = $user->userid;

            // Check if the user already has addresses
            $hasAddresses = UserAddress::where('user_id', $userid)
                                        ->where('status', 'active')
                                        ->exists();

            // Check if the apartment name exists in flower__apartment table
            $apartment = Apartment::where('apartment_name', $request->apartment_name)->first();

            if (!$apartment) {
                // Save the new apartment if it doesn't exist
                $apartment = Apartment::create([
                    'locality_id' => $request->locality, // Assuming locality is passed as an ID
                    'apartment_name' => $request->apartment_name,
                ]);
            }

            // Create the new address
            $addressdata = new UserAddress();
            $addressdata->user_id = $userid;
            $addressdata->country = 'India';
            $addressdata->state = $request->state;
            $addressdata->city = $request->city;
            $addressdata->pincode = $request->pincode;
            $addressdata->area = $request->area;
            $addressdata->address_type = $request->address_type;
            $addressdata->locality = $request->locality;
            $addressdata->apartment_name = $request->apartment_name;
            $addressdata->place_category = $request->place_category;
            $addressdata->apartment_flat_plot = $request->apartment_flat_plot;
            $addressdata->landmark = $request->landmark;
            $addressdata->status = 'active';

            // Set as default if it's the first address
            if (!$hasAddresses) {
                $addressdata->default = 1;
            }

            $addressdata->save();

            return response()->json([
                'success' => 200,
                'message' => 'Address created successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 500, 'message' => $e->getMessage()], 500);
        }
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
        $user = Auth::guard('api')->user();
    
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        try {
            $address = UserAddress::find($request->id);
    
            if ($address) {
                // Update address fields
                $address->country = $request->country;
                $address->state = $request->state;
                $address->city = $request->city;
                $address->pincode = $request->pincode;
                $address->area = $request->area;
                $address->address_type = $request->address_type;
                $address->locality = $request->locality;
                $address->apartment_name = $request->apartment_name;
                $address->place_category = $request->place_category;
                $address->apartment_flat_plot = $request->apartment_flat_plot;
                $address->landmark = $request->landmark;
                $address->status = 'active';
    
                // Save the updated address
                $address->save();
    
                return response()->json([
                    'success' => 200,
                    'message' => 'Address updated successfully.',
                    'address' => $address
                ], 200);
            } else {
                return response()->json([
                    'success' => 404,
                    'message' => 'Address not found.'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to update the address. Error: ' . $e->getMessage()
            ], 500);
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
