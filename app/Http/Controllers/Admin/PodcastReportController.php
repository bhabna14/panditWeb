<?php

namespace App\Http\Controllers\Admin;

use App\Models\PodcastPrepair;
use App\Models\PublishPodcast;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PodcastReportController extends Controller
{

    public function podcastReport()
    {
        $podcast_details = PodcastPrepair::where('status', 'active')->get();
    
        // Fetch publish date for each podcast
        $publish_data = PublishPodcast::whereIn('podcast_id', $podcast_details->pluck('podcast_id'))
                                      ->pluck('publish_date', 'podcast_id');
        
        // Group podcast data by month-year of the publish_date
        $podcastsByMonth = $publish_data->map(function ($publishDate, $podcastId) use ($podcast_details) {
            return [
                'podcast' => $podcast_details->firstWhere('podcast_id', $podcastId),
                'publish_date' => Carbon::parse($publishDate)->format('F Y') // Group by month-year
            ];
        });
    
        // Group by formatted month and year
        $groupedPodcasts = $podcastsByMonth->groupBy('publish_date');
    
        return view('admin/podcast-report', compact('groupedPodcasts'));
    }
    
    
    

    public function getScriptDetails(Request $request)
{
    $podcast = PodcastPrepair::where('podcast_id', $request->podcast_id)->first();

    if ($podcast) {
        return response()->json([
            'script_location' => $podcast->script_location,
            'story_source' => $podcast->story_source,
            'script_verified_by' => $podcast->script_verified_by,
            'script_created_by' => $podcast->script_created_by,
            'script_created_date' => $podcast->script_created_date,
            'script_verified_date' => $podcast->script_verified_date,
            'script_reject_reason' => $podcast->script_reject_reason,
            'script_editor' => $podcast->script_editor,
            'podcast_script_status' => $podcast->podcast_script_status,
        ]);
    }

    return response()->json(['message' => 'Podcast not found'], 404);
}

public function getRecordingDetails(Request $request)
{

    $podcast = PodcastPrepair::where('podcast_id', $request->podcast_id)->first();

    if ($podcast) {
        return response()->json([
            'podcast_recording_by' => $podcast->podcast_recording_by,
            'podcast_image_path' => $podcast->podcast_image_path,
            'podcast_video_path' => $podcast->podcast_video_path,
            'podcast_audio_path' => $podcast->podcast_audio_path,
            'recording_date' => $podcast->recording_date,
            'recording_complete_url' => $podcast->recording_complete_url,
            'podcast_recording_status' => $podcast->podcast_recording_status,
        ]);
    }

    return response()->json(['error' => 'Podcast not found'], 404);
}


public function getEditingDetails(Request $request)
{
    $podcast = PodcastPrepair::where('podcast_id', $request->podcast_id)->first();

    if ($podcast) {
        return response()->json([
            'editing_date' => $podcast->editing_date,
            'music_source' => $podcast->music_source,
            'audio_edited_by' => $podcast->audio_edited_by,
            'editing_verified_by' => $podcast->editing_verified_by,
            'editing_verified_date' => $podcast->editing_verified_date,
            'editing_complete_url' => $podcast->editing_complete_url,
            'podcast_editing_status' => $podcast->podcast_editing_status,
        ]);
    }
   
    return response()->json(['error' => 'Podcast not found'], 404);

}

public function getPublishDetails(Request $request)
{
    $podcastId = $request->podcast_id;

    // Fetch details from PublishPodcast and PodcastPrepair tables
    $publishDetails = PublishPodcast::where('podcast_id', $podcastId)->first();
    $podcastPrepair = PodcastPrepair::where('podcast_id', $podcastId)->first();

    if ($publishDetails && $podcastPrepair) {
        return response()->json([
            'podcast_image' => $publishDetails->podcast_image,
            'podcast_music' => $publishDetails->podcast_music,
            'podcast_video_url' => $publishDetails->podcast_video_url,
            'publish_date' => $publishDetails->publish_date,
            'description' => $publishDetails->description,
            'youtube_post_date' => $podcastPrepair->youtube_post_date,
            'facebook_post_date' => $podcastPrepair->facebook_post_date,
            'instagram_post_date' => $podcastPrepair->instagram_post_date,
            'youtube_post_link' => $podcastPrepair->youtube_post_link,
            'facebook_post_link' => $podcastPrepair->facebook_post_link,
            'instagram_post_link' => $podcastPrepair->instagram_post_link,
            'final_podcast_type' => $podcastPrepair->final_podcast_type,
            'final_podcast_url' => $podcastPrepair->final_podcast_url,
            'podcast_status' => $podcastPrepair->podcast_status,
        ]);
    }

    return response()->json(['error' => 'Podcast details not found'], 404);
}


}    
