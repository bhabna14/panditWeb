<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;

class PodcastMediaController extends Controller
{
    public function podcastMedia(){

        $podcast_details = PodcastPrepair::where('status','active')->get();
        return view('admin.podcast-media',compact('podcast_details'));
        
    }

    public function updatePodcastMedia(Request $request, $podcast_id)
    {
        $request->validate([
            'podcast_image_path' => 'nullable',
            'podcast_video_path' => 'nullable',
            'podcast_audio_path' => 'nullable',
        ]);

            try {
                // Find the podcast record by ID
                $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
                // Update the fields
                $podcast->podcast_image_path = $request->input('podcast_image_path');
                $podcast->podcast_video_path = $request->input('podcast_video_path');
                $podcast->podcast_audio_path = $request->input('podcast_audio_path');
                $podcast->save();

                // Return success response
                return redirect()->route('publishPodcast')->with('success', 'Podcast media updated successfully.');
            } catch (\Exception $e) {
                // Return error response
                return redirect()->back()->with('error', 'Failed to update podcast media. Please try again.');
            }
        }
    
    
}
