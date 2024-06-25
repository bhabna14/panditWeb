<?php

namespace App\Http\Controllers\pandit;

use App\Models\Poojaarea;
use App\Models\Panditstate;
use Illuminate\Http\Request;
use App\Models\Panditvillage;
use App\Models\Panditdistrict;
use App\Models\Panditsubdistrict;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    public function poojaArea()
    {
        $states = Panditstate::distinct()->limit(200)->get(['stateCode', 'stateName']);
        $districts = Panditdistrict::distinct()->limit(200)->get(['districtCode', 'districtName']);
        $citys = Panditsubdistrict::distinct()->get(['subdistrictCode', 'subdistrictName']);
        $villages = Panditvillage::limit(200)->get(['villageCode', 'villageName']);

        return view("pandit/poojaarea", compact('states', 'districts', 'citys', 'villages'));
    }

    public function getDistrict($stateCode)
    {
        $districts = Panditdistrict::distinct()->where('stateCode', $stateCode)->get(['districtCode', 'districtName']);
        return response()->json($districts);
    }

    public function getSubdistrict($districtCode)
    {
        $subdistricts = Panditsubdistrict::distinct()->where('districtCode', $districtCode)->get(['subdistrictCode', 'subdistrictName']);
        return response()->json($subdistricts);
    }

    public function saveForm(Request $request)
    {
      
        $Area = new Poojaarea();

        $Area->pandit_id = Auth::guard('pandits')->user()->pandit_id;

        $Area->state_code = $request->state;
        $Area->district_code = $request->district;
        $Area->subdistrict_code = $request->city;
       
        $village = $request->input('village');
 
            $villageString = implode(',', $village);
            $Area->village_code = $villageString;

            if ($Area->save()) {
                return redirect()->back()->with('success', 'Data saved successfully.');
            } else {
                return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
            }

    }
}
