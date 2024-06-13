<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Addressdetail;
use App\Models\Profile;



class AddressController extends Controller
{
    //
 
   
    public function address(){
      
        $addressdata = Addressdetail::first();
        return view('pandit/panditaddress', compact('addressdata'));
    }
    public function saveaddress(Request $request)
    {
        // $userId = Auth::id();
        $profile = Profile::where('status', 'active')->first();
         $profileId = $profile->profile_id;
        
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
            ['pandit_id' => $profileId],
            $request->all()
        );

        return redirect()->back()->with('success', 'Address details saved successfully!');
    }


}
