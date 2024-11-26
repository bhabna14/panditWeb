<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiderDetails;
use Illuminate\Support\Facades\Storage;

class RiderController extends Controller
{
    /**
     * Display the add rider details form.
     */
    public function addRiderDetails()
    {
        return view('admin/add-rider-details');
    }

    /**
     * Save rider details.
     */
    public function saveRiderDetails(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'rider_name' => 'required|string|max:255',
            'phone_number' => 'nullable|numeric|digits_between:10,15',
            'rider_img' => 'nullable|image|mimes:jpeg,png,jpg|max:3072', // Validate image
            'description' => 'nullable|string',
        ]);

        try {
            // Handle file upload
            $imagePath = null;
            if ($request->hasFile('rider_img')) {
                $imagePath = $request->file('rider_img')->store('images', 'public');
            }

            $rider_id = 'RIDER' . rand(10000, 99999);


            // Save rider details to the database
            RiderDetails::create([
                'rider_id' =>  $rider_id,
                'rider_name' => $validatedData['rider_name'],
                'phone_number' => $validatedData['phone_number'] ?? null,
                'rider_img' => $imagePath,
                'description' => $validatedData['description'],
            ]);

            // Redirect back with success message
            return redirect()->back()->with('success', 'Rider details saved successfully.');
        } catch (\Exception $e) {
            // Handle exceptions and errors
            return redirect()->back()->with('error', 'Failed to save rider details. Please try again.');
        }
    }

    public function manageRiderDetails(){

        $rider_details = RiderDetails::where('status', 'active')->get();

        return view('admin.manage-rider-details', compact('rider_details'));
    }

    public function editRiderDetails($id)
{
    $rider = RiderDetails::findOrFail($id); // Retrieve rider by ID or throw 404
    return view('admin.edit-rider-details', compact('rider'));
}

public function updateRiderDetails(Request $request, $id)
{
    // Validate the input
    $validatedData = $request->validate([
        'rider_name' => 'required|string|max:255',
        'phone_number' => 'nullable|numeric|digits_between:10,15',
        'rider_img' => 'nullable|image|mimes:jpeg,png,jpg|max:3072', // Validate image
        'description' => 'nullable|string',
    ]);

    try {
        $rider = RiderDetails::findOrFail($id);

        // Update image if a new one is uploaded
        if ($request->hasFile('rider_img')) {
            // Delete the old image
            if ($rider->rider_img) {
                Storage::disk('public')->delete($rider->rider_img);
            }

            // Store the new image
            $imagePath = $request->file('rider_img')->store('images', 'public');
            $rider->rider_img = $imagePath;
        }

        // Update the other fields
        $rider->rider_name = $validatedData['rider_name'];
        $rider->phone_number = $validatedData['phone_number'] ?? null;
        $rider->description = $validatedData['description'];

        $rider->save(); // Save changes to the database

        return redirect()->route('admin.manageRiderDetails')->with('success', 'Rider details updated successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update rider details. Please try again.');
    }
}

public function deleteRiderDetails($id)
{
    try {
        // Find the rider by ID
        $rider = RiderDetails::findOrFail($id);

        // Update the status to 'deleted'
        $rider->status = 'deleted';
        $rider->save();

        // Redirect back with success message
        return redirect()->route('admin.manageRiderDetails')->with('success', 'Rider marked as deleted successfully.');
    } catch (\Exception $e) {
        // Handle exceptions
        return redirect()->back()->with('error', 'Failed to delete rider. Please try again.');
    }
}


}
