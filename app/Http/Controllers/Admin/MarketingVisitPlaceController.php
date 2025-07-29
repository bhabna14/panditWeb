<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingVisitPlace;
use Illuminate\Support\Carbon;

class MarketingVisitPlaceController extends Controller
{
    public function getVisitPlace()
    {
        return view('admin.flower-request.flower-marketing-visit-place');
    }

 public function storeVisitPlace(Request $request)
{
    $request->validate([
        'visitor_name' => 'required|string|max:255',
        'locationType' => 'required|string',
        'datetime' => 'required|date',
        'contactName' => 'required|string',
        'contactNumber' => 'required|array|min:1',
        'contactNumber.*' => 'nullable|string',
        'noOfApartments' => 'nullable|integer',
        'delivered' => 'nullable|string',
        'apartmentName' => 'nullable|string',
        'apartmentNumber' => 'nullable|string',
        'locality' => 'nullable|string',
        'landmark' => 'nullable|string',
    ]);

    $contactNumbers = implode(',', array_filter($request->contactNumber));

    MarketingVisitPlace::create([
        'visitor_name'          => $request->visitor_name,
        'location_type'         => $request->locationType,
        'date_time'             => $request->datetime,
        'contact_person_name'   => $request->contactName,
        'contact_person_number' => $contactNumbers,
        'no_of_apartment'       => $request->noOfApartments,
        'already_delivery'      => $request->delivered,
        'apartment_name'        => $request->apartmentName,
        'apartment_number'      => $request->apartmentNumber,
        'locality_name'         => $request->locality,
        'landmark'              => $request->landmark,
    ]);

    return redirect()->back()->with('success', 'Marketing visit data saved successfully.');
}

public function manageVisitPlace(Request $request)
{
    $filter = $request->query('filter');

    if ($filter === 'todayVisitPlace') {
        $visitPlaces = MarketingVisitPlace::whereDate('created_at', Carbon::today())->get();
    } else {
        $visitPlaces = MarketingVisitPlace::all();
    }

    return view('admin.flower-request.manage-marketing-visit-place', compact('visitPlaces'));
}

public function editVisitPlace($id)
{
    $visitPlace = MarketingVisitPlace::findOrFail($id);
    return response()->json($visitPlace);
}

public function updateVisitPlace(Request $request, $id)
{
    $request->validate([
        'visitor_name' => 'required|string|max:255',
        'location_type' => 'required|string',
        'date_time' => 'required|date',
        'contact_person_name' => 'required|string',
        'contact_person_number' => 'required|string', // comma-separated string
        'no_of_apartment' => 'nullable|integer',
        'already_delivery' => 'nullable|string',
        'apartment_name' => 'nullable|string',
        'apartment_number' => 'nullable|string',
        'locality_name' => 'nullable|string',
        'landmark' => 'nullable|string',
    ]);

    $visitPlace = MarketingVisitPlace::findOrFail($id);
    $visitPlace->update($request->all());

    return response()->json(['success' => true, 'message' => 'Visit place updated successfully.']);
}
}
