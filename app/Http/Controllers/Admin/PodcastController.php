<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;

class PodcastController extends Controller
{
    //
    public function managepodcast(){
        $podcasts = Podcast::all();
        return view('admin/managepodcast',compact('podcasts'));
    }
    public function addpodcast(){

        $podcasts = Podcast::all();

        return view('admin/addpodcast',compact('podcasts'));
    }
    public function savepodcast(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'music' => 'required|mimes:mp3,wav|max:30000', // Added validation for music file size
            'podcast_id' => 'nullable|string', // Ensure validation is set for podcast_id
        ]);
    
        // Determine whether to use an existing podcast ID or create a new one
        if (trim($request->podcast_id) !== '') {
            // Existing podcast selected
            $podcastId = $request->podcast_id;
        } else {
            // No podcast selected, generate a new podcast ID
            $podcastId = 'PODCAST' . rand(10000, 99999);
        }
    
        // Handle the file upload
        if ($request->hasFile('image') && $request->hasFile('music')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $musicPath = $request->file('music')->store('music', 'public');
        } else {
            return redirect()->back()->with('error', 'File upload failed.');
        }
    
        // Create a new podcast record
        $podcast = new Podcast();
        $podcast->name = $request->name;
        $podcast->podcast_id = $podcastId; // Ensure this is saved correctly
        $podcast->language = $request->language;
        $podcast->description = $request->description;
        $podcast->image = $imagePath;
        $podcast->music = $musicPath;
        $podcast->save();
    
        return redirect()->route('addpodcast')->with('success', 'Podcast created successfully.');
    }
    
    
    
    public function editpodcast(Podcast $podcast)
    {
        return view('admin/editpodcast', compact('podcast'));
    }

    public function updatepodcast(Request $request, Podcast $podcast)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'music' => 'nullable|mimes:mp3,wav|max:100000000',
        'language' => 'required|string|in:odia,english,hindi', // Validate language input
    ]);

    // Handle image file upload if it exists
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        $imagePath = $request->file('image')->store('images', 'public');
        $podcast->image = $imagePath;
    }

    // Handle music file upload if it exists
    if ($request->hasFile('music') && $request->file('music')->isValid()) {
        $musicPath = $request->file('music')->store('music', 'public');
        $podcast->music = $musicPath;
    }

    // Update the language field and other fields
    $podcast->language = $request->language;

    // Update the podcast with the validated data
    $podcast->update($request->only(['name', 'description']));

    // Save the podcast model after updating fields directly
    $podcast->save();

    // Redirect with success message
    return redirect()->route('managepodcast')->with('success', 'Podcast updated successfully');
}

    public function destroy(Podcast $podcast)
    {
        $podcast->delete();
        return redirect()->back()->with('danger', 'Podcast delete successfully.');
    }
}
