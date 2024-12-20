<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;
use App\Models\PublishPodcast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\UserDevice;
use App\Services\NotificationService;
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
        'podcast_id' => 'required|exists:podcast_prepair,podcast_id',
        'podcast_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'podcast_music' => 'required|mimes:mp3,wav,ogg|max:30720',
        'publish_date' => 'required|date',
        'podcast_video_url' => 'nullable|string',
        'description' => 'required|string|max:1000',
    ]);

    \Log::info('Starting podcast publishing process', ['request_data' => $request->all()]);

    try {
        $imagePath = null;
        $musicPath = null;

        // Handle podcast image upload
        if ($request->hasFile('podcast_image') && $request->file('podcast_image')->isValid()) {
            \Log::info('Uploading podcast image...');
            $imagePath = $request->file('podcast_image')->store('images', 'public');
            \Log::info('Podcast image uploaded', ['path' => $imagePath]);
        } else {
            \Log::error('Podcast image upload failed');
            return redirect()->back()->with('error', 'Invalid podcast image file');
        }

        // Handle podcast music upload
        if ($request->hasFile('podcast_music') && $request->file('podcast_music')->isValid()) {
            \Log::info('Uploading podcast music...');
            $musicPath = $request->file('podcast_music')->store('music', 'public');
            \Log::info('Podcast music uploaded', ['path' => $musicPath]);
        } else {
            \Log::error('Podcast music upload failed');
            return redirect()->back()->with('error', 'Invalid podcast music file');
        }

        // Save podcast details to the database
        $publishPodcast = PublishPodcast::create([
            'podcast_id' => $request->podcast_id,
            'podcast_image' => $imagePath,
            'podcast_music' => $musicPath,
            'podcast_video_url' => $request->podcast_video_url,
            'publish_date' => $request->publish_date,
            'description' => $request->description,
        ]);

        \Log::info('Podcast details saved successfully', ['publishPodcast' => $publishPodcast]);

        PodcastPrepair::where('podcast_id', $request->podcast_id)
            ->update([
                'podcast_status' => 'PUBLISHED',
                'podcast_editing_status' => 'COMPLETED',
            ]);

        // Send notifications to users
        \Log::info('Sending notifications to users...');
        $deviceTokens = UserDevice::pluck('device_id')->toArray();

        if (!empty($deviceTokens)) {
            $notificationService = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $imageUrl = $imagePath ? asset('storage/' . $imagePath) : null; // Generate full URL of the image

            $response = $notificationService->sendBulkNotifications(
                $deviceTokens,
                "New Podcast Published!",
                "Check out our latest podcast!",
                [
                    'podcast_id' => $request->podcast_id,
                    'image' => $imageUrl, // Add image URL to payload
                ]
            );

            \Log::info('Notifications sent successfully', ['response' => $response]);
        } else {
            \Log::info('No device tokens found. Skipping notification.');
        }

        return redirect()->route('PodcastSocialMedia')->with('success', 'Podcast published successfully!');
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('An error occurred while publishing the podcast', [
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()->with('error', 'An error occurred while publishing the podcast. Please try again.');
    }
}

    
    
    
    
}
