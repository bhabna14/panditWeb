<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingVisitPlace;

class MarketingVisitPlaceController extends Controller
{
    public function getVisitPlace()
    {
        return view('admin.flower-request.flower-marketing-visit-place');
    }

    public function storeVisitPlace(Request $request)
    {
        $request->validate([
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
            'visitor_name'          => auth()->user()->name ?? 'Admin', // optional
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
}
