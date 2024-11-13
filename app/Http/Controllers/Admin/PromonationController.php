<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promonation;

class PromonationController extends Controller
{
    //
    public function managepromonation()
    {
        $promonations = Promonation::where('status', 'active')->get();
        return view('admin.managepromonations', compact('promonations'));
    }

    public function addpromonation()
    {
        return view('admin.addpromonation');
    }

    public function savepromonation(Request $request)
    {
        $request->validate([
            'promonation_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date' => 'required|date',
            'description' => 'required|string',
            'promo_heading' => 'required|string',
            'button_title' => 'required|string',

        ]);

        $imageName = time() . '.' . $request->promonation_image->extension();
        $request->promonation_image->move(public_path('images/promonations'), $imageName);

        Promonation::create([
            'promonation_image' => $imageName,
            'date' => $request->date,
            'description' => $request->description,
            'promo_heading' => $request->promo_heading,
            'button_title' => $request->button_title,
            'status' => 'active'
        ]);

        return redirect()->route('admin.managepromonation')->with('success', 'Promonation added successfully!');
    }

    public function editpromonation($id)
    {
        $promonation = Promonation::findOrFail($id);
        return view('admin.editpromonation', compact('promonation'));
    }

    public function updatepromonation(Request $request, $id)
    {
        $request->validate([
            'promonation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date' => 'required|date',
            'description' => 'required|string',
            'promo_heading' => 'required|string',
            'button_title' => 'required|string',
        ]);

        $promonation = Promonation::findOrFail($id);
        if ($request->hasFile('promonation_image')) {
            $imageName = time() . '.' . $request->promonation_image->extension();
            $request->promonation_image->move(public_path('images/promonations'), $imageName);
            $promonation->promonation_image = $imageName;
        }

        $promonation->date = $request->date;
        $promonation->description = $request->description;
        $promonation->promo_heading = $request->promo_heading;
         $promonation->button_title = $request->button_title;
        $promonation->save();

        return redirect()->route('admin.managepromonation')->with('success', 'Promonation updated successfully!');
    }

    public function deletepromonation($id)
    {
        $promonation = Promonation::findOrFail($id);
        $promonation->status = 'deleted';
        $promonation->save();

        return redirect()->route('admin.managepromonation')->with('success', 'Promonation deleted successfully!');
    }
}
