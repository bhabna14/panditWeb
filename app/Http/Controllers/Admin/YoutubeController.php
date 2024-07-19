<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Youtube;

class YoutubeController extends Controller
{
    public function youTube(){
        return view('admin/youtube');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_name' => 'required|string|max:255',
            'youtube_url' => 'required',
            'description' => 'nullable|string',
        ]);

        $youtube = new Youtube();
        $youtube->title = $request->title_name;
        $youtube->youtube_url = $request->youtube_url;
        $youtube->description = $request->description;
        $youtube->save();

        return redirect()->back()->with('success', 'YouTube URL saved successfully!');
    }

    public function manageYoutube(){

        $youtubes = Youtube::where('status', 'active')->get();

        return view('admin/manageyoutube',compact('youtubes'));

    }
    public function destroy($id)
    {
        $youtube = Youtube::findOrFail($id);
        $youtube->status = 'deleted';
        $youtube->save();

        return redirect()->back()->with('success', 'YouTube URL marked as deleted successfully!');
    }

    public function edit($id)
    {
        $youtube = Youtube::findOrFail($id);
        return view('admin/edit-youtube', compact('youtube'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title_name' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        $youtube = Youtube::findOrFail($id);
        $youtube->title = $request->title_name;
        $youtube->youtube_url = $request->youtube_url;
        $youtube->description = $request->description;
        $youtube->save();

        return redirect()->back()->with('success', 'YouTube URL updated successfully!');
    }


}
