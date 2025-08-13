<?php

namespace App\Http\Controllers\Refer;

use App\Http\Controllers\Controller;
use App\Models\ReferOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferController extends Controller
{
    public function offerCreate()
    {
        return view('refer.offer-create');
    }

public function saveReferOffer(Request $request)
{
    $validated = $request->validate([
        'offer_name'    => 'required|string|max:255',
        'description'   => 'required|string|max:2000',
        'no_of_refer'   => 'required|array|min:1',
        'no_of_refer.*' => 'nullable|integer|min:1',
        'benefit'       => 'required|array|min:1',
        'benefit.*'     => 'nullable|string|max:255',
        // 'status'      => 'nullable|in:active,inactive',
    ]);

    $referArray   = [];
    $benefitArray = [];

    $count = max(count($validated['no_of_refer']), count($validated['benefit']));
    for ($i = 0; $i < $count; $i++) {
        $refer   = $validated['no_of_refer'][$i] ?? null;
        $benefit = isset($validated['benefit'][$i]) ? trim($validated['benefit'][$i]) : null;

        // Skip cloned/empty rows
        if (empty($refer) && ($benefit === null || $benefit === '')) {
            continue;
        }

        // Require both values per row
        if (empty($refer) || $benefit === null || $benefit === '') {
            return back()
                ->withInput()
                ->withErrors(['benefit' => 'Each Refer & Benefit row must have both values.']);
        }

        $referArray[]   = (int) $refer;
        $benefitArray[] = $benefit;
    }

    if (empty($referArray)) {
        return back()
            ->withInput()
            ->withErrors(['no_of_refer' => 'Please add at least one valid Refer & Benefit row.']);
    }

    DB::transaction(function () use ($request, $validated, $referArray, $benefitArray) {
        ReferOffer::create([
            'offer_name'  => $validated['offer_name'],
            'description' => $validated['description'],
            'no_of_refer' => $referArray,           // JSON array
            'benefit'     => $benefitArray,         // JSON array
            'status'      => $request->input('status', 'active'),
        ]);
    });

    return redirect()
        ->route('refer.offerCreate')
        ->with('success', 'Refer offer saved successfully!');
}

  public function manageReferOffer()
    {
        $offers = ReferOffer::where('status','active')->get();

        return view('refer.manage-offer', compact('offers'));
    }

     public function update(Request $request, ReferOffer $offer)
    {
        $validated = $request->validate([
            'offer_name'    => 'required|string|max:255',
            'description'   => 'required|string|max:2000',
            'status'        => 'required|in:active,inactive',
            'no_of_refer'   => 'required|array|min:1',
            'no_of_refer.*' => 'nullable|integer|min:1',
            'benefit'       => 'required|array|min:1',
            'benefit.*'     => 'nullable|string|max:255',
        ]);

        $referArray = [];
        $benefitArray = [];
        $count = max(count($validated['no_of_refer']), count($validated['benefit']));
        for ($i = 0; $i < $count; $i++) {
            $r = $validated['no_of_refer'][$i] ?? null;
            $b = isset($validated['benefit'][$i]) ? trim($validated['benefit'][$i]) : null;
            if (empty($r) && ($b === null || $b === '')) continue;
            if (empty($r) || $b === null || $b === '') {
                return back()->withInput()->withErrors(['benefit' => 'Each Refer & Benefit row must have both values.']);
            }
            $referArray[] = (int)$r;
            $benefitArray[] = $b;
        }
        if (empty($referArray)) {
            return back()->withInput()->withErrors(['no_of_refer' => 'Please add at least one valid Refer & Benefit row.']);
        }

        $offer->update([
            'offer_name'  => $validated['offer_name'],
            'description' => $validated['description'],
            'status'      => $validated['status'],
            'no_of_refer' => $referArray,
            'benefit'     => $benefitArray,
        ]);

        return redirect()->route('refer.manageReferOffer')->with('success', 'Offer updated successfully.');
    }

    public function destroy(ReferOffer $offer)
    {
        $offer->delete();
        return redirect()->route('refer.manageReferOffer')->with('success', 'Offer deleted successfully.');
    }
}
