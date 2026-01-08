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
        $localities = Locality::where('status', 'active')->get();
        return view('admin.add-rider-details', compact('localities'));
    }

    public function saveRiderDetails(Request $request)
    {
        $validatedData = $request->validate([
            'rider_name'    => 'required|string|max:255',
            'phone_number'  => 'required|digits:10',
            'salary'        => 'required|numeric|min:0',

            'dob'           => 'required|date|before_or_equal:today',

            // Joining date: optional, but if given should be <= today and >= dob
            'date_of_joining' => 'nullable|date|after_or_equal:dob|before_or_equal:today',

            'rider_img'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'documents'     => 'nullable|array|max:5',
            'documents.*'   => 'file|mimes:pdf,jpg,jpeg,png|max:5120',

            'description'   => 'nullable|string|max:2000',
        ]);

        try {
            $phoneNumber = '+91' . $validatedData['phone_number'];

            do {
                $rider_id = 'RIDER' . rand(10000, 99999);
            } while (\App\Models\RiderDetails::where('rider_id', $rider_id)->exists());

            $imagePath = null;
            if ($request->hasFile('rider_img')) {
                $imagePath = $request->file('rider_img')->store("riders/{$rider_id}/photo", 'public');
            }

            $docPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $doc) {
                    $docPaths[] = $doc->store("riders/{$rider_id}/documents", 'public');
                }
            }

            \App\Models\RiderDetails::create([
                'rider_id'        => $rider_id,
                'rider_name'      => $validatedData['rider_name'],
                'phone_number'    => $phoneNumber,
                'salary'          => $validatedData['salary'],
                'dob'             => $validatedData['dob'],
                'date_of_joining' => $validatedData['date_of_joining'] ?? null,
                'rider_img'       => $imagePath,
                'documents'       => !empty($docPaths) ? $docPaths : null,
                'description'     => $validatedData['description'] ?? null,
                'tracking'        => 'stop',
            ]);

            return redirect()->back()->with('success', 'Rider details saved successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to save rider details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save rider details. Please try again.');
        }
    }
        
    public function manageRiderDetails()
    {
        $rider_details = RiderDetails::where('status', 'active')
            ->orderByDesc('id')
            ->get();

        // Collect all locality IDs once (avoid N+1)
        $allLocalityIds = $rider_details->flatMap(function ($rider) {
            $ids = explode(',', (string) ($rider->locality_id ?? ''));
            return array_filter(array_map('trim', $ids));
        })->unique()->values();

        $localityMap = Locality::whereIn('id', $allLocalityIds)
            ->pluck('locality_name', 'id');

        // Attach locality names
        $rider_details = $rider_details->map(function ($rider) use ($localityMap) {
            $ids = explode(',', (string) ($rider->locality_id ?? ''));
            $ids = array_filter(array_map('trim', $ids));

            $rider->locality_names = collect($ids)
                ->map(fn ($id) => $localityMap[$id] ?? null)
                ->filter()
                ->values()
                ->toArray();

            return $rider;
        });

        return view('admin.manage-rider-details', compact('rider_details'));
    }

    public function editRiderDetails($id)
    {
        $rider = RiderDetails::findOrFail($id);
        $localities = Locality::all();

        // documents is cast to array in model, but keep it safe
        $existingDocs = $rider->documents ?? [];
        if (!is_array($existingDocs)) {
            $existingDocs = (array) $existingDocs;
        }

        return view('admin.edit-rider-details', compact('rider', 'localities', 'existingDocs'));
    }

    public function updateRiderDetails(Request $request, $id)
    {
        $validated = $request->validate([
            'rider_name'   => 'required|string|max:255',
            'phone_number' => 'required|digits:10',
            'salary'       => 'required|numeric|min:0',

            'dob'             => 'required|date|before_or_equal:today',
            'date_of_joining' => 'nullable|date|after_or_equal:dob|before_or_equal:today',

            'rider_img'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // New uploads
            'documents'     => 'nullable|array|max:5',
            'documents.*'   => 'file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Removing existing docs
            'remove_documents'   => 'nullable|array',
            'remove_documents.*' => 'string',

            'description'  => 'nullable|string|max:2000',
        ]);

        $rider = RiderDetails::findOrFail($id);

        // ---- Phone storage with +91 (avoid double +91) ----
        $digits10 = preg_replace('/\D/', '', (string) $validated['phone_number']);
        $digits10 = substr($digits10, -10);
        $phoneWithCode = '+91' . $digits10;

        // ---- Existing documents (array) ----
        $existingDocs = $rider->documents ?? [];
        if (!is_array($existingDocs)) $existingDocs = [];

        // ---- Remove selected docs ----
        $removeDocs = $request->input('remove_documents', []);
        if (!is_array($removeDocs)) $removeDocs = [];

        $remainingDocs = array_values(array_filter($existingDocs, function ($path) use ($removeDocs) {
            return !in_array($path, $removeDocs, true);
        }));

        // Delete removed files from disk
        foreach ($removeDocs as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // ---- Upload new documents ----
        $newDocs = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $folder = $rider->rider_id ? "riders/{$rider->rider_id}/documents" : "riders/{$rider->id}/documents";
                $newDocs[] = $doc->store($folder, 'public');
            }
        }

        // ---- Enforce total max 5 docs ----
        $totalDocs = count($remainingDocs) + count($newDocs);
        if ($totalDocs > 5) {
            // Rollback newly uploaded docs if limit exceeded
            foreach ($newDocs as $p) {
                if (Storage::disk('public')->exists($p)) Storage::disk('public')->delete($p);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['documents' => 'Maximum 5 documents allowed (existing + new). Please remove some documents and try again.']);
        }

        // ---- Update basic fields ----
        $rider->rider_name       = $validated['rider_name'];
        $rider->phone_number     = $phoneWithCode;
        $rider->salary           = $validated['salary'];
        $rider->dob              = $validated['dob'];
        $rider->date_of_joining  = $validated['date_of_joining'] ?? null;
        $rider->description      = $validated['description'] ?? null;

        // ---- Update image if provided ----
        if ($request->hasFile('rider_img')) {
            if ($rider->rider_img && Storage::disk('public')->exists($rider->rider_img)) {
                Storage::disk('public')->delete($rider->rider_img);
            }

            $imgFolder = $rider->rider_id ? "riders/{$rider->rider_id}/photo" : "riders/{$rider->id}/photo";
            $rider->rider_img = $request->file('rider_img')->store($imgFolder, 'public');
        }

        // ---- Save documents merged ----
        $finalDocs = array_values(array_merge($remainingDocs, $newDocs));
        $rider->documents = !empty($finalDocs) ? $finalDocs : null;

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
