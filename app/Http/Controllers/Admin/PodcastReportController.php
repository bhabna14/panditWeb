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
        // Fetch all active podcasts
        $podcast_details = PodcastPrepair::where('status', 'active')->get();
    
        // Fetch publish dates for all podcasts (if any)
        $publish_data = PublishPodcast::whereIn('podcast_id', $podcast_details->pluck('podcast_id'))
                                      ->pluck('publish_date', 'podcast_id');
    
        // Separate unpublished podcasts and assign a formatted `podcast_create_date`
        $podcastDetailsWithPublishDate = $podcast_details->map(function ($podcast) use ($publish_data) {
            $publish_date = $publish_data->get($podcast->podcast_id, null);
            return [
                'podcast' => $podcast,
                'publish_date' => $publish_date ? Carbon::parse($publish_date)->format('F Y') : 'Unpublished',
                'create_date' => $publish_date ? null : Carbon::parse($podcast->podcast_create_date)->format('F Y'),
            ];
        });
    
        // Group podcasts by publish date or creation date for unpublished
        $groupedPodcasts = $podcastDetailsWithPublishDate->groupBy(function ($item) {
            return $item['publish_date'] !== 'Unpublished' ? $item['publish_date'] : $item['create_date'];
        });
    
        // Sort groups by month-year in ascending order
        $sortedGroupedPodcasts = $groupedPodcasts->sortKeysUsing(function ($a, $b) {
            return Carbon::createFromFormat('F Y', $a)->timestamp <=> Carbon::createFromFormat('F Y', $b)->timestamp;
        });
    
        return view('admin/podcast-report', compact('sortedGroupedPodcasts'));
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
