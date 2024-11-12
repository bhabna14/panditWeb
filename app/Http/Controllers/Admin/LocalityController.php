<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locality;
class LocalityController extends Controller
{
    //
    public function managelocality()
    {
        $localities = Locality::where('status','active')->get();
        return view('admin.managelocality', compact('localities'));
    }
    public function addlocality(){
        return view('admin.addlocality');
    }
    public function saveLocality(Request $request)
    {
        $request->validate([
            'locality_name' => 'required|string|max:255',
            'pincode' => 'required|digits:6'
        ]);

        // Generate a unique code
        // $uniqueCode = uniqid('loc_');
         // Generate a unique 3-digit code
         $lastLocality = Locality::orderBy('id', 'desc')->first();
         $lastCode = $lastLocality ? intval($lastLocality->unique_code) : 0;
         $newCode = str_pad($lastCode + 1, 3, '0', STR_PAD_LEFT);

        // Save the data
        Locality::create([
            'locality_name' => $request->locality_name,
            'pincode' => $request->pincode,
            'unique_code' => $newCode,
            'status' => 'active',
        ]);

        return redirect()->route('admin.managelocality')->with('success', 'Locality added successfully!');
    }

    public function editLocality($id)
{
    $locality = Locality::findOrFail($id);
    return view('admin.editlocality', compact('locality'));
}

public function updateLocality(Request $request, $id)
{
    $request->validate([
        'locality_name' => 'required|string|max:255',
        'pincode' => 'required|digits:6',
        
    ]);

    $locality = Locality::findOrFail($id);
    $locality->update([
        'locality_name' => $request->locality_name,
        'pincode' => $request->pincode,
       
    ]);

    return redirect()->route('admin.managelocality')->with('success', 'Locality updated successfully!');
}

public function deleteLocality($id)
{
    $locality = Locality::findOrFail($id);
    $locality->delete();

    return redirect()->route('admin.managelocality')->with('success', 'Locality deleted successfully!');
}
}
