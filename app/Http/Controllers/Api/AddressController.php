<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Addressdetail;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function saveAddress(Request $request)
    {
        // Get the authenticated pandit_id
        $panditId = Auth::guard('pandits')->user()->pandit_id;

        // Validate the request data
        $request->validate([
            'preaddress' => 'required|string|max:255',
            'prepost' => 'required|string|max:255',
            'predistrict' => 'required|string|max:255',
            'prestate' => 'required|string|max:255',
            'precountry' => 'required|string|max:255',
            'prepincode' => 'required|string|max:10',
            'prelandmark' => 'nullable|string|max:255', // Changed to nullable

            'peraddress' => 'required|string|max:255',
            'perpost' => 'required|string|max:255',
            'perdistrict' => 'required|string|max:255',
            'perstate' => 'required|string|max:255',
            'percountry' => 'required|string|max:255',
            'perpincode' => 'required|string|max:10',
            'perlandmark' => 'nullable|string|max:255', // Changed to nullable
        ]);

        // Prepare data for update or create
        $addressData = [
            'preaddress' => $request->preaddress,
            'prepost' => $request->prepost,
            'predistrict' => $request->predistrict,
            'prestate' => $request->prestate,
            'precountry' => $request->precountry,
            'prepincode' => $request->prepincode,
            'prelandmark' => $request->prelandmark,

            'peraddress' => $request->peraddress,
            'perpost' => $request->perpost,
            'perdistrict' => $request->perdistrict,
            'perstate' => $request->perstate,
            'percountry' => $request->percountry,
            'perpincode' => $request->perpincode,
            'perlandmark' => $request->perlandmark,
        ];

        // Update or create the address details
        $addressDetail = Addressdetail::updateOrCreate(
            ['pandit_id' => $panditId],
            $addressData
        );

        // Return a JSON response
        return response()->json([
            'success' => 'Address details saved successfully!',
            'data' => $addressDetail
        ], 200);
    }
}
