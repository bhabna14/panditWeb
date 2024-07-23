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
    public function manageArea()
    {
        $panditId = Auth::guard('pandits')->user()->pandit_id;
    
        $poojaAreas = Poojaarea::where('status', 'active')->where('pandit_id', $panditId)->get();
    
        $states = Panditstate::all()->keyBy('stateCode');
        $districts = Panditdistrict::all()->keyBy('districtCode');
        $subdistricts = Panditsubdistrict::all()->keyBy('subdistrictCode');
        $villages = Panditvillage::all()->keyBy('villageCode');

    
        foreach ($poojaAreas as $poojaArea) {
            $poojaArea->stateName = $states->get($poojaArea->state_code)->stateName ?? '';
            $poojaArea->districtName = $districts->get($poojaArea->district_code)->districtName ?? '';
            $poojaArea->subdistrictName = $subdistricts->get($poojaArea->subdistrict_code)->subdistrictName ?? '';
    
            $villageCodes = explode(',', $poojaArea->village_code);
            $villageNames = [];
            foreach ($villageCodes as $villageCode) {
                if ($village = $villages->get($villageCode)) {
                    $villageNames[] = $village->villageName;
                }
            }
            $poojaArea->villageNames = implode(', ', $villageNames);
        }
    
        return view("pandit/managearea", compact('poojaAreas'));
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
 
    public function getVillage($subdistrictCode)
    {
        $villages = Panditvillage::distinct()->where('subdistrictCode', $subdistrictCode)->get(['villageCode', 'villageName']);
        return response()->json($villages);
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
    public function deletePoojaArea($id)
    {
        $poojaArea = Poojaarea::find($id);

        if ($poojaArea) {
            $poojaArea->status = 'deleted';
            $poojaArea->save();
            return redirect()->back()->with('success', 'Pooja area deleted successfully.');
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to delete pooja area.']);
        }
    }

    public function editPoojaArea($id)
    {
        $poojaArea = Poojaarea::find($id);

        if ($poojaArea) {
            $states = Panditstate::all();
            $districts = Panditdistrict::where('stateCode', $poojaArea->state_code)
            ->distinct('districtCode')
            ->get(['districtCode', 'districtName']);
            $subdistricts = Panditsubdistrict::where('districtCode', $poojaArea->district_code)
            ->distinct('subdistrictCode')
            ->get(['subdistrictCode', 'subdistrictName']);
            $villages = Panditvillage::where('subdistrictCode', $poojaArea->subdistrict_code)
            ->distinct('villageCode')
            ->get(['villageCode', 'villageName']);

            return view('pandit/edit-poojaarea', compact('poojaArea', 'states', 'districts', 'subdistricts', 'villages'));
        } else {
            return redirect()->back()->withErrors(['error' => 'Pooja area not found.']);
        }
    }

    public function updatePoojaArea(Request $request, $id)
    {
        $poojaArea = Poojaarea::find($id);

        if ($poojaArea) {
            $poojaArea->state_code = $request->state;
            $poojaArea->district_code = $request->district;
            $poojaArea->subdistrict_code = $request->city;
            $village = $request->input('village');
            $villageString = implode(',', $village);
            $poojaArea->village_code = $villageString;
            $poojaArea->save();

            return redirect()->route('managearea')->with('success', 'Pooja area updated successfully.');
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to update pooja area.']);
        }
    }

}
