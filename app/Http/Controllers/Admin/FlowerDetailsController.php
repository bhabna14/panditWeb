<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowerDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $units = ['bunch', 'piece', 'kg', 'g', 'bundle'];
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

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('flower_details', 'public'); // storage/app/public/flower_details/...
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
        $units = ['bunch', 'piece', 'kg', 'g', 'bundle'];

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

        if ($request->hasFile('image')) {
            // delete old image if any
            if ($flower_detail->image && Storage::disk('public')->exists($flower_detail->image)) {
                Storage::disk('public')->delete($flower_detail->image);
            }
            $data['image'] = $request->file('image')
                ->store('flower_details', 'public');
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
}
