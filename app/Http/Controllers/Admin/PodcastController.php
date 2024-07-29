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
        return view('admin/addpodcast');
    }
    public function savepodcast(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            // 'music' => 'required|mimes:mp3,wav|max:10000'
        ]);

        // Handle the file upload
        $imagePath = $request->file('image')->store('images', 'public');
        $musicPath = $request->file('music')->store('music', 'public');

        // Create a new podcast record
        $podcast = new Podcast();
        $podcast->name = $request->name;
        $podcast->description = $request->description;
        $podcast->image = $imagePath;
        $podcast->music = $musicPath;
        $podcast->save();

        return redirect()->route('addpodcast')->with('success', 'Podcast created successfully.');
    }
    // public function editpodcast(Request $request, $id){
    //     // $podcastinfo = Podcast::where('id', $id)->first();
    //     return view('admin/editpodcast',compact('id'));

    // }
    public function editpodcast(Podcast $podcast)
    {
        return view('admin/editpodcast', compact('podcast'));
    }
    public function updatepodcast(Request $request, Podcast $podcast)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'music' => 'nullable|mimes:mp3,WAV|max:100000000'
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('images', 'public');
            $podcast->image = $imagePath;
        }

        if ($request->hasFile('music') && $request->file('music')->isValid()) {
            $musicPath = $request->file('music')->store('music', 'public');
            $podcast->music = $musicPath;
        }

        $podcast->update($request->only(['name', 'description']));

        return redirect()->route('managepodcast')->with('success', 'Podcast updated successfully');
    }
    public function destroy(Podcast $podcast)
    {
        $podcast->delete();
        return redirect()->back()->with('danger', 'Podcast delete successfully.');
    }
}
