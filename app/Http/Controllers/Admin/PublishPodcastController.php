<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;
use App\Models\PublishPodcast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PublishPodcastController extends Controller
{
    public function publishPodcast()
    {
        // Fetch all podcasts with 'APPROVED' editing status
        $podcast_details = PodcastPrepair::where('podcast_editing_status', 'APPROVED')->get();
    
        // Fetch published podcast IDs
        $publishedPodcasts = PublishPodcast::pluck('podcast_id'); // Only get the 'podcast_id' column
    
        // Pass both datasets to the view
        return view('admin.publish-podcast', compact('podcast_details', 'publishedPodcasts'));
    }
    
 
    
    public function savePublishPodcast(Request $request)
    {
        // Validate the input fields
        $request->validate([
            'podcast_id' => 'required|exists:podcast_prepair,podcast_id', // Ensure podcast_id exists in the related table
            'podcast_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max file size 2MB
            'podcast_music' => 'required|mimes:mp3,wav,ogg|max:30720', // Max file size 30MB
            'publish_date' => 'required|date',
            'podcast_video_url' => 'nullable|string', // Optional field
            'description' => 'required|string|max:1000',
        ]);
    
        try {
            Log::info('Starting podcast publishing process', ['request_data' => $request->all()]);
    
            // Handle file uploads
            $imagePath = null;
            $musicPath = null;
    
            if ($request->hasFile('podcast_image')) {
                Log::info('Uploading podcast image...');
                $imagePath = $request->file('podcast_image')->store('images', 'public');
                Log::info('Podcast image uploaded', ['path' => $imagePath]);
            }
    
            if ($request->hasFile('podcast_music')) {
                Log::info('Uploading podcast music...');
                $musicPath = $request->file('podcast_music')->store('music', 'public');
                Log::info('Podcast music uploaded', ['path' => $musicPath]);
            }
    
            // Save podcast details to the database
            $publishPodcast = PublishPodcast::create([
                'podcast_id' => $request->podcast_id,
                'podcast_image' => $imagePath,
                'podcast_music' => $musicPath,
                'podcast_video_url' => $request->podcast_video_url, // Corrected this line
                'publish_date' => $request->publish_date,
                'description' => $request->description,
            ]);
            Log::info('Podcast details saved successfully', ['publishPodcast' => $publishPodcast]);
    
            // Update the podcast_status and podcast_editing_status in PodcastPrepair model
            $updateStatus = PodcastPrepair::where('podcast_id', $request->podcast_id)
                ->update([
                    'podcast_status' => 'PUBLISHED',
                    'podcast_editing_status' => 'completed', // Add this line to update the podcast_editing_status
                ]);
            Log::info('PodcastPrepair status updated', ['podcast_id' => $request->podcast_id, 'updateStatus' => $updateStatus]);
    
            return redirect()->route('PodcastSocialMedia')->with('success', 'Podcast published successfully!');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error occurred while publishing podcast', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return redirect()->back()->with('error', 'An error occurred while publishing the podcast: ' . $e->getMessage());
        }
    }
    
    
}
