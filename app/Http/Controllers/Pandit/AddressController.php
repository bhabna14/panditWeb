<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Addressdetail;
use App\Models\Profile;



class AddressController extends Controller
{
    //
 
   
    public function address()
{
    // Get the authenticated Pandit's pandit_id
    $panditId = Auth::guard('pandits')->user()->pandit_id;

    // Fetch the address details associated with the authenticated Pandit
    $addressdata = Addressdetail::where('pandit_id', $panditId)->first();

    // Return the view with the address data
    return view('pandit.panditaddress', compact('addressdata'));
}

    public function saveaddress(Request $request)
    {
        // $userId = Auth::id();
        $panditId = Auth::guard('pandits')->user()->pandit_id;

        $request->validate([
            'preaddress' => '|required|string|max:255',
            'prepost' => '|required|string|max:255|',
            'predistrict' => 'required',
            'prestate' => '|required',
            'precountry' => 'required',
            'prepincode' => 'required',
            'prelandmark' => 'required',

            'peraddress' => '|required|string|max:255',
            'perpost' => '|required|string|max:255|',
            'perdistri' => 'required',
            'perstate' => '|required',
            'percountry' => 'required',
            'perpincode' => 'required',
            'perlandmark' => 'required',
        ]);

        $addressdata = Addressdetail::updateOrCreate(
            ['pandit_id' => $panditId],
            $request->all()
        );

        return redirect()->back()->with('success', 'Address details saved successfully!');
    }


}
