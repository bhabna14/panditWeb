<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Addressdetail;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function saveaddress(Request $request)
    {
        // Get the authenticated Pandit ID
        $panditId = Auth::guard('sanctum')->user()->pandit_id;
    
        // Validate the request data
        $validatedData = $request->validate([
            'preaddress' => 'required|string|max:255',
            'prepost' => 'required|string|max:255',
            'predistrict' => 'required',
            'prestate' => 'required',
            'precountry' => 'required',
            'prepincode' => 'required',
            'prelandmark' => 'required',
    
            'peraddress' => 'required|string|max:255',
            'perpost' => 'required|string|max:255',
            'perdistri' => 'required',
            'perstate' => 'required',
            'percountry' => 'required',
            'perpincode' => 'required',
            'perlandmark' => 'required',
        ]);
    
        // Update or create address details for the Pandit
        $addressdata = Addressdetail::updateOrCreate(
            ['pandit_id' => $panditId],
            $validatedData
        );
    
        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Address details saved successfully!',
            'data' => $addressdata
        ], 200);
    }
}
