<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionDetails;
use Illuminate\Support\Facades\Storage; // ⬅️ add this at the top

class PromotionController extends Controller
{
   use Illuminate\Support\Facades\Storage; // ⬅️ add this at the top

public function saveFlowerPromotion(Request $request)
{
    // Validation
    $validated = $request->validate([
        'header'     => 'required|string|max:255',
        'body'       => 'required|string',
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'photo'      => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    try {
        // Handle photo upload
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $relativePath = $request->file('photo')->store('promotions', 'public');
            $photoUrl = url(Storage::disk('public')->url($relativePath));
        }

        // Save to DB (photo column now holds the absolute URL)
        PromotionDetails::create([
            'header'     => $validated['header'],
            'body'       => $validated['body'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'photo'      => $photoUrl,
            // 'status'   => 1, // uncomment if you want to default to active
        ]);

        return redirect()
            ->route('admin.promotionList')
            ->with('success', 'Promotion saved successfully.');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Failed to save promotion: ' . $e->getMessage());
    }
}

}
