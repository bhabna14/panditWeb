<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionDetails;
use Illuminate\Support\Facades\Storage; // ⬅️ add this at the top

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

public function manageFlowerPromotion()
{
    $promotions = PromotionDetails::where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->get();
    return view('admin.manage-flower-promotion', compact('promotions'));

}
 public function updateFlowerPromotion(Request $request, $id)
    {
        $promotion = PromotionDetails::findOrFail($id);

        $validated = $request->validate([
            'header'     => 'required|string|max:255',
            'body'       => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            // New photo? Replace and (try to) delete old local file if it came from /storage
            if ($request->hasFile('photo')) {
                // delete old if local
                if (!empty($promotion->photo)) {
                    $oldRelative = $this->relativeStoragePathFromUrl($promotion->photo);
                    if ($oldRelative && Storage::disk('public')->exists($oldRelative)) {
                        Storage::disk('public')->delete($oldRelative);
                    }
                }
                $relativePath = $request->file('photo')->store('promotions', 'public');
                $promotion->photo = url(Storage::disk('public')->url($relativePath)); // full URL
            }

            $promotion->header = $validated['header'];
            $promotion->body = $validated['body'];
            $promotion->start_date = $validated['start_date'];
            $promotion->end_date = $validated['end_date'];
            $promotion->save();

            return redirect()
                ->route('admin.promotionList')
                ->with('success', 'Promotion updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update promotion: ' . $e->getMessage());
        }
    }

    // Delete
    public function deleteFlowerPromotion($id)
    {
        $promotion = PromotionDetails::findOrFail($id);

        try {
            // Attempt to delete local file if applicable
            if (!empty($promotion->photo)) {
                $oldRelative = $this->relativeStoragePathFromUrl($promotion->photo);
                if ($oldRelative && Storage::disk('public')->exists($oldRelative)) {
                    Storage::disk('public')->delete($oldRelative);
                }
            }

            $promotion->delete();

            return redirect()
                ->route('admin.promotionList')
                ->with('success', 'Promotion deleted successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.promotionList')
                ->with('error', 'Failed to delete promotion: ' . $e->getMessage());
        }
    }

    private function relativeStoragePathFromUrl(string $url): ?string
    {
        $prefix = url('/storage') . '/'; // e.g. https://example.com/storage/
        if (strpos($url, $prefix) === 0) {
            return substr($url, strlen($prefix)); // promotions/file.jpg
        }
        return null; // likely remote/third-party URL
    }
}
