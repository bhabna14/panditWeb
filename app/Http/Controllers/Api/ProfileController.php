<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:profiles,email',
            'whatsappno' => 'nullable|string|max:20',
            'bloodgroup' => 'nullable|string|max:10',
            'maritalstatus' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // Ensure it's an image file
        ]);

        $profile = new Profile();

        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->maritalstatus;
        $profile->language = $request->language;

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filename;
        }

        $profile->save();

        return response()->json(['message' => 'Profile created successfully', 'user' => $profile], 201);
    }
}
