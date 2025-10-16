<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowerDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlowerDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $rows = FlowerDetails::query()
            ->when($q, function ($builder) use ($q) {
                $builder->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('unit', 'like', "%{$q}%")
                      ->orWhere('flower_id', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.manage-flower-details', compact('rows', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = ['bunch', 'piece', 'kg', 'g', 'bundle', 'bouquet', 'garland', 'packet'];
        return view('admin.add-flower-details', [
            'row'   => new FlowerDetails(),
            'units' => $units,
            'mode'  => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'flower_id' => ['nullable', 'string', 'max:50'],
            'name'      => ['required', 'string', 'max:120'],
            'image'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'quantity'  => ['required', 'numeric', 'min:0'],
            'unit'      => ['required', 'string', 'max:20'],
            'price'     => ['required', 'numeric', 'min:0'],
        ]);

        // Ensure uppercase + autogenerate if empty
        $data['flower_id'] = $this->ensureFlowerId($data['flower_id'] ?? null, $data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('flower_details', 'public');
        }

        FlowerDetails::create($data);

        return redirect()
            ->route('admin.flower-details.index')
            ->with('success', 'Flower details added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FlowerDetails $flower_detail)
    {
        $units = ['bunch', 'piece', 'kg', 'g', 'bundle', 'bouquet', 'garland', 'packet'];

        return view('admin.add-flower-details', [
            'row'   => $flower_detail,
            'units' => $units,
            'mode'  => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FlowerDetails $flower_detail)
    {
        $data = $request->validate([
            'flower_id' => ['nullable', 'string', 'max:50'],
            'name'      => ['required', 'string', 'max:120'],
            'image'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'quantity'  => ['required', 'numeric', 'min:0'],
            'unit'      => ['required', 'string', 'max:20'],
            'price'     => ['required', 'numeric', 'min:0'],
        ]);

        // If provided, uppercase it; if empty, regenerate from name (keeps existing if already set)
        if (!empty($data['flower_id'])) {
            $data['flower_id'] = strtoupper($data['flower_id']);
        } else {
            // Keep existing if present; otherwise generate
            $data['flower_id'] = $flower_detail->flower_id ?: $this->generateFlowerIdFromName($data['name']);
            // Try to avoid accidental duplicates if changed
            $data['flower_id'] = $this->makeUniqueIfNeeded($data['flower_id']);
        }

        if ($request->hasFile('image')) {
            if ($flower_detail->image && Storage::disk('public')->exists($flower_detail->image)) {
                Storage::disk('public')->delete($flower_detail->image);
            }
            $data['image'] = $request->file('image')->store('flower_details', 'public');
        }

        $flower_detail->update($data);

        return redirect()
            ->route('admin.flower-details.index')
            ->with('success', 'Flower details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FlowerDetails $flower_detail)
    {
        if ($flower_detail->image && Storage::disk('public')->exists($flower_detail->image)) {
            Storage::disk('public')->delete($flower_detail->image);
        }

        $flower_detail->delete();

        return redirect()
            ->route('admin.flower-details.index')
            ->with('success', 'Flower details deleted.');
    }

    /**
     * Ensure an uppercase Flower ID; generate from name if empty.
     */
    private function ensureFlowerId(?string $flowerId, string $name): string
    {
        $candidate = $flowerId ? strtoupper($flowerId) : $this->generateFlowerIdFromName($name);
        return $this->makeUniqueIfNeeded($candidate);
    }

    /**
     * Generate ID: first 4 letters of name (A–Z only) + 4 random digits.
     * Example: "Marigold" -> "MARI4821"
     */
    private function generateFlowerIdFromName(string $name): string
    {
        // Keep only letters, then take first 4; if fewer, right-pad with X
        $lettersOnly = preg_replace('/[^a-zA-Z]/', '', $name) ?: 'FLOW';
        $prefix = strtoupper(Str::substr($lettersOnly, 0, 4));
        $prefix = str_pad($prefix, 4, 'X'); // make sure it's 4 chars

        $digits = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $digits;
    }

    /**
     * Try a few times to avoid duplicates.
     */
    private function makeUniqueIfNeeded(string $candidate): string
    {
        if (!FlowerDetails::where('flower_id', $candidate)->exists()) {
            return $candidate;
        }
        // Try a handful of variations
        for ($i = 0; $i < 5; $i++) {
            $base = substr($candidate, 0, 4); // first 4 are the letters
            $candidate = $base . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            if (!FlowerDetails::where('flower_id', $candidate)->exists()) {
                return $candidate;
            }
        }
        // Fall back to a timestamp suffix (very unlikely)
        return substr($candidate, 0, 4) . substr((string)time(), -4);
    }
}
