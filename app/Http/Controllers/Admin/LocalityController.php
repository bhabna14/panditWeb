<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locality;
use App\Models\Apartment;

class LocalityController extends Controller
{
    //
    public function managelocality()
    {
        $localities = Locality::where('status', 'active')
            ->with('apartment') // Eager load apartments
            ->get();
    
        return view('admin.managelocality', compact('localities'));
    }
    
    public function addlocality(){
        return view('admin.addlocality');
    }

    public function saveLocality(Request $request)
    {
        // Validate input fields
        $request->validate([
            'locality_name' => 'required|string|max:255',
            'pincode' => 'required|digits:6',
            'apartment_name' => 'nullable|array', // Ensure apartment_name is an array
            'apartment_name.*' => 'nullable|string|max:255', // Validate each apartment name
        ]);
    
        // Generate a unique 3-digit code
        $lastLocality = Locality::orderBy('id', 'desc')->first();
        $lastCode = $lastLocality ? intval($lastLocality->unique_code) : 0;
        $newCode = str_pad($lastCode + 1, 3, '0', STR_PAD_LEFT);
    
        // Save the locality data
        $locality = Locality::create([
            'locality_name' => $request->locality_name,
            'pincode' => $request->pincode,
            'unique_code' => $newCode,
            'status' => 'active',
        ]);
    
        // Save the apartment names
        foreach ($request->apartment_name as $name) {
            if (!empty(trim($name))) {
                Apartment::create([
                    'locality_id' => $locality->unique_code, // Link the apartment to the locality
                    'apartment_name' => $name,
                ]);
            }
        }
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Locality and apartments added successfully!');
    }
    

    public function editLocality($id)
    {
        $locality = Locality::with('apartment')->findOrFail($id);
        return view('admin.editlocality', compact('locality'));
    }
    
    public function updateLocality(Request $request, $id)
    {
        $request->validate([
            'locality_name' => 'required|string|max:255',
            'pincode' => 'required|digits:6',
            'apartment_name' => 'array',
            'apartment_name.*' => 'nullable|string|max:255',
        ]);
    
        $locality = Locality::findOrFail($id);
        $locality->update([
            'locality_name' => $request->locality_name,
            'pincode' => $request->pincode,
        ]);
    
        // Update apartments
        $apartmentNames = $request->apartment_name;
        $locality->apartment()->delete(); // Remove old apartments
        foreach ($apartmentNames as $name) {
            if (!empty($name)) {
                $locality->apartment()->create(['apartment_name' => $name]);
            }
        }
    
        return redirect()->route('admin.managelocality')->with('success', 'Locality updated successfully!');
    }
    

public function deleteLocality($id)
{
    $locality = Locality::findOrFail($id);
    $locality->delete();

    return redirect()->route('admin.managelocality')->with('success', 'Locality deleted successfully!');
}
}
