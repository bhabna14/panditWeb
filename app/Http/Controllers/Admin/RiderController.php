<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiderDetails;
use App\Models\Apartment;
use App\Models\Locality;
use App\Models\RiderArea;
use Illuminate\Support\Facades\Storage;

class RiderController extends Controller
{
    
    public function addRiderDetails()
    {
        $localities = Locality::where('status','active')->get();

        return view('admin.add-rider-details', compact('localities'));
    }
        
    public function saveRiderDetails(Request $request)
    {
        $validatedData = $request->validate([
            'rider_name'    => 'required|string|max:255',
            'phone_number'  => 'required|digits:10',
            'salary'        => 'required|numeric|min:0',
            'rider_img'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB
            'description'   => 'nullable|string',
        ]);

        try {
            // Prepend +91 to the phone number
            $phoneNumber = '+91' . $validatedData['phone_number'];

            // Upload image if present
            $imagePath = null;
            if ($request->hasFile('rider_img')) {
                $imagePath = $request->file('rider_img')->store('images', 'public');
            }

            // Generate unique Rider ID (avoid collision)
            do {
                $rider_id = 'RIDER' . rand(10000, 99999);
            } while (RiderDetails::where('rider_id', $rider_id)->exists());

            // Save
            RiderDetails::create([
                'rider_id'      => $rider_id,
                'rider_name'    => $validatedData['rider_name'],
                'phone_number'  => $phoneNumber,
                'salary'        => $validatedData['salary'],
                'rider_img'     => $imagePath,
                'description'   => $validatedData['description'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Rider details saved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to save rider details: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to save rider details. Please try again.');
        }
    } 

    public function manageRiderDetails()
    {
        // Fetch rider details along with their locality names
        $rider_details = RiderDetails::where('status', 'active')
            ->get()
            ->map(function ($rider) {
                $localityIds = explode(',', $rider->locality_id); // Convert comma-separated string to an array
                $rider->locality_names = Locality::whereIn('id', $localityIds)
                    ->pluck('locality_name')
                    ->toArray(); // Fetch locality names and convert to an array
                return $rider;
            });

        return view('admin.manage-rider-details', compact('rider_details'));
    }

    public function editRiderDetails($id)
    {
        $rider = RiderDetails::findOrFail($id); // Fetch rider by ID
        $localities = Locality::all(); // Fetch all localities
        return view('admin.edit-rider-details', compact('rider', 'localities'));
    }
        
    public function updateRiderDetails(Request $request, $id)
    {
        $validated = $request->validate([
            'rider_name'   => 'required|string|max:255',
            'phone_number' => 'required|digits:10',
            'salary'       => 'required|numeric|min:0',
            'rider_img'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description'  => 'nullable|string|max:1000',
        ]);

        $rider = RiderDetails::findOrFail($id);

        // Update basic details
        $rider->rider_name   = $validated['rider_name'];
        $rider->phone_number = '+91' . $validated['phone_number']; // store with +91
        $rider->salary       = $validated['salary'];
        $rider->description  = $validated['description'] ?? null;

        // Update image if provided
        if ($request->hasFile('rider_img')) {
            // Delete old image from public disk if exists
            if ($rider->rider_img && Storage::disk('public')->exists($rider->rider_img)) {
                Storage::disk('public')->delete($rider->rider_img);
            }

            // Store new image
            $rider->rider_img = $request->file('rider_img')->store('images', 'public');
        }

        $rider->save();

        return redirect()
            ->route('admin.manageRiderDetails')
            ->with('success', 'Rider details updated successfully.');
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

    // Rider Order Assign controllre 

    public function addOrderAssign()
    {
        $rider_names = RiderDetails::where('status','active')->get();

        $localities = Locality::where('status','active')->get();

        $apartments = Apartment::where('status','active')->get();

        return view('admin.add-order-assign', compact('localities','apartments','rider_names'));
    }

    public function getApartments(Request $request)
    {
        $request->validate([
            'locality_id' => 'required|exists:localities,id',
        ]);

        $apartments = Apartment::where('locality_id', $request->locality_id)->get();

        return response()->json(['apartments' => $apartments]);
    }

    public function saveOrderAssign(Request $request)
    {
        // Validate the request
        $request->validate([
            'rider_name' => 'required',
            'locality_name' => 'required|array',
            'locality_name.*' => 'required',
            'apartment_name' => 'required|array',
            'apartment_name.*' => 'required|array',
        ]);

        // Loop through each locality and corresponding apartments
        foreach ($request->locality_name as $index => $localityId) {
            // Get the apartments for the current locality
            $apartments = $request->apartment_name[$index] ?? [];

            // Create a new row for each locality-apartment group
            RiderArea::create([
                'rider_id' => $request->rider_name,
                'assign_date' => $request->assign_date,
                'locality_id' => $localityId,
                'apartment_id' => implode(',', $apartments), // Save apartments as comma-separated values
            ]);
        }

        // Redirect with success message
        return redirect()->back()->with('success', 'Order assignment saved successfully!');
    }

    public function manageOrderAssign()
    {
        // Fetch rider details along with locality and apartment names
        $rider_details = RiderArea::where('status', 'active')
            ->with(['locality', 'apartment', 'rider'])
            ->get()
            ->groupBy('rider.rider_name') // Group by rider name
            ->map(function ($group) {
                return $group->sortBy('assign_date'); // Sort each group by assign date
            });

        return view('admin.manage-order-assign', compact('rider_details'));
    }

    public function editOrderAssign($id)
    {
        // Fetch rider details by ID for editing
        $rider = RiderArea::with(['locality', 'apartment'])->findOrFail($id);

        // Fetch all riders, localities, and apartments for dropdowns
        $rider_names = RiderDetails::all();
        $localities = Locality::all();
        
        // Get apartments filtered by selected locality(ies)
        $selectedLocalities = explode(',', $rider->locality_id); // Assuming multiple locality IDs are stored as comma-separated values
        $apartments = Apartment::whereIn('locality_id', $selectedLocalities)->get();

        return view('admin.edit-order-assign', compact('rider', 'rider_names', 'localities', 'apartments'));
    }

    public function updateOrderAssign(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'rider_name' => 'required',
            'assign_date' => 'required|date',
            'locality_name' => 'required|array',
            'apartment_name' => 'required|array',
        ]);

        // Fetch the RiderArea model to update
        $rider = RiderArea::findOrFail($id);

        // Update the fields
        $rider->rider_id = $request->rider_name;
        $rider->assign_date = $request->assign_date;
        $rider->locality_id = implode(',', $request->locality_name);
        $rider->apartment_id = implode(',', $request->apartment_name);

        // Save the updated rider
        $rider->save();

        // Redirect back to the manage page with a success message
        return redirect()->route('admin.manageOrderAssign')->with('success', 'Order assignment updated successfully.');
    }

    public function deleteOrderAssign($id)
    {
            // Find the rider area by ID
            $rider = RiderArea::findOrFail($id);

            // Update status to 'deleted' (rather than deleting the record)
            $rider->status = 'deleted';
            $rider->save();

            // Return JSON response for success
            return redirect()->route('admin.manageOrderAssign')->with('success', 'Order assignment updated successfully.');

    }

    public function deactiveOrderAssign($rider_id)
    {
        // Find all riders by rider_id
        $riders = RiderArea::where('rider_id', $rider_id)->get();

        // Check if there are any riders with the given rider_id
        if ($riders->isNotEmpty()) {
            // Update the status to 'deactive' for all matching records
            RiderArea::where('rider_id', $rider_id)->update(['status' => 'deactive']);

            // Redirect with success message
            return redirect()->route('admin.manageOrderAssign')->with('success', 'Order assignments updated successfully to deactive.');
        } else {
            // If no riders exist with the given rider_id, return an error message
            return redirect()->route('admin.manageOrderAssign')->with('error', 'No riders found for the given rider_id.');
        }
    }

}
