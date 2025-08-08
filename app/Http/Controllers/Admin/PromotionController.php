<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionDetails;

class PromotionController extends Controller
{
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
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('promotions', 'public');
            }

            // Save to DB
            PromotionDetails::create([
                'header'     => $validated['header'],
                'body'       => $validated['body'],
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'],
                'photo'      => $photoPath,
                'status'     => 1 // active by default
            ]);

            // Redirect with success message
            return redirect()
                ->route('admin.promotionList') // Change this route name to your list page
                ->with('success', 'Promotion saved successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to save promotion: ' . $e->getMessage());
        }
    }
}
