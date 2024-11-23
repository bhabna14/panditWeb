<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;


class PodcastSocialMediaController extends Controller
{
    public function PodcastSocialMedia(){

        $podcast_details = PodcastPrepair::where('status','active')->get();

        return view('admin/podcast-social-media',compact('podcast_details'));
    }

    public function updatePodcastSocialMedia(Request $request, $podcast_id)
{

    try {
        // Validate the incoming request
        $request->validate([
            'final_podcast_url' => 'nullable|url',
            'final_podcast_type' => 'nullable|string|in:full,short',
            'youtube_post_date' => 'nullable|date',
            'youtube_post_link' => 'nullable|url',
            'instagram_post_date' => 'nullable|date',
            'instagram_post_link' => 'nullable|url',
            'facebook_post_date' => 'nullable|date',
            'facebook_post_link' => 'nullable|url',
        ]);

        // Find the podcast record by ID
        $podcast = PodcastPrepair::where('podcast_id',$podcast_id)->first();

        // Update the fields
        $podcast->update([
            'final_podcast_url' => $request->input('final_podcast_url'),
            'final_podcast_type' => $request->input('final_podcast_type'),
            'youtube_post_date' => $request->input('youtube_post_date'),
            'youtube_post_link' => $request->input('youtube_post_link'),
            'instagram_post_date' => $request->input('instagram_post_date'),
            'instagram_post_link' => $request->input('instagram_post_link'),
            'facebook_post_date' => $request->input('facebook_post_date'),
            'facebook_post_link' => $request->input('facebook_post_link'),
            'podcast_status' => 'COMPLETED',
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Podcast social media details updated successfully!');
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Handle the case where the podcast ID does not exist
        return redirect()->back()->withErrors(['error' => 'Podcast not found.']);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return redirect()->back()->withErrors($e->errors());
    } catch (\Exception $e) {
        // Handle any other exceptions
        return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}

}
