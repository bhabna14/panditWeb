<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Addressdetail;

class AddressController extends Controller
{
    public function saveAddress(Request $request)
    {
        // Get the active profile
        $profile = Profile::where('status', 'active')->first();
        if (!$profile) {
            return response()->json(['error' => 'No active profile found.'], 404);
        }
        $profileId = $profile->profile_id;

        // Validate the request data
        $request->validate([
            'preaddress' => 'required|string|max:255',
            'prepost' => 'required|string|max:255',
            'predistrict' => 'required|string|max:255',
            'prestate' => 'required|string|max:255',
            'precountry' => 'required|string|max:255',
            'prepincode' => 'required|string|max:10',
            'prelandmark' => 'required|string|max:255',

            'peraddress' => 'required|string|max:255',
            'perpost' => 'required|string|max:255',
            'perdistri' => 'required|string|max:255',
            'perstate' => 'required|string|max:255',
            'percountry' => 'required|string|max:255',
            'perpincode' => 'required|string|max:10',
            'perlandmark' => 'required|string|max:255',
        ]);

        // Update or create the address details
        $addressdata = Addressdetail::updateOrCreate(
            ['pandit_id' => $profileId],
            $request->all()
        );

        // Return a JSON response
        return response()->json(['success' => 'Address details saved successfully!', 'data' => $addressdata], 200);
    }
}
