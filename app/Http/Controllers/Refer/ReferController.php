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
        // Basic validation of top-level + array presence
        $validated = $request->validate([
            'offer_name'      => 'required|string|max:255',
            'description'     => 'required|string|max:2000',
            'no_of_refer'     => 'required|array|min:1',
            'no_of_refer.*'   => 'nullable|integer|min:1',
            'benefit'         => 'required|array|min:1',
            'benefit.*'       => 'nullable|string|max:255',
            // If you submit a status field in the form later, validate it here:
            // 'status'       => 'nullable|in:active,inactive',
        ]);

        // Pair up rows; skip empty clones
        $rows = [];
        $count = max(count($validated['no_of_refer']), count($validated['benefit']));

        for ($i = 0; $i < $count; $i++) {
            $refer   = $validated['no_of_refer'][$i] ?? null;
            $benefit = isset($validated['benefit'][$i]) ? trim($validated['benefit'][$i]) : null;

            // Skip if both fields empty (common when cloning rows in the UI)
            if (empty($refer) && (empty($benefit) && $benefit !== '0')) {
                continue;
            }

            // Server-side require both when one is provided
            if (empty($refer) || $benefit === null || $benefit === '') {
                return back()
                    ->withInput()
                    ->withErrors(['benefit' => 'Each Refer & Benefit row must have both values.']);
            }

            $rows[] = [
                'offer_name'   => $validated['offer_name'],
                'description'  => $validated['description'],
                'no_of_refer'  => (int) $refer,
                'benefit'      => $benefit,
                'status'       => $request->input('status', 'active'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        if (empty($rows)) {
            return back()
                ->withInput()
                ->withErrors(['no_of_refer' => 'Please add at least one valid Refer & Benefit row.']);
        }

        DB::transaction(function () use ($rows) {
            ReferOffer::insert($rows); // insert many rows in one go
        });

        return redirect()
            ->route('refer.offerCreate')
            ->with('success', 'Refer offer saved successfully!');
    }
}
