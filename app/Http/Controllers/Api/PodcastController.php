<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;
use App\Models\PodcastPrepair;
use App\Models\PublishPodcast;
use App\Models\PodcastCategory;

use Carbon\Carbon;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function podcasts()
    {
        // Fetch data by joining PublishPodcast with PodcastPrepair
        $podcasts = PublishPodcast::where('status', 'active')
            ->orderBy('id', 'desc')
            ->with(['podcastPrepair' => function ($query) {
                $query->select('podcast_id', 'language', 'podcast_name', 'deity_category', 'festival_name');
            }])
            ->get();
    
        // Format the data (if needed)
        foreach ($podcasts as $podcast) {
            $podcast->podcast_image = asset('storage/' . $podcast->podcast_image);
            $podcast->podcast_music = asset('storage/' . $podcast->podcast_music);
        }
    
        // Check if no data is found
        if ($podcasts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);
        }
    
        // Return the combined data
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $podcasts
        ], 200);
    }
    
   
    public function podcasthomepage()
    {
        // Step 1: Get active categories with formatted image URLs
        $categories = PodcastCategory::where('status', 'active')
            ->get()
            ->map(function ($category) {
                $category->image_url = asset('storage/' . $category->category_img); // Format category image URL
                return $category;
            });
    
        // Step 2: Get the most recent podcast with related `podcastPrepair` details
        $recentPodcast = PublishPodcast::where('status', 'active')
            ->latest('publish_date') // Ensure ordering by publish date
            ->with(['podcastPrepair' => function ($query) {
                $query->select('podcast_id', 'language', 'podcast_name', 'deity_category', 'festival_name');
            }])
            ->first();
    
        // Step 3: Get podcasts published in the last 7 days with formatted image and music URLs
        $lastWeekPodcasts = PublishPodcast::where('status', 'active')
            ->where('publish_date', '>=', Carbon::now()->subDays(7)) // Only last 7 days
            ->get()
            ->map(function ($podcast) {
                $podcast->podcast_image = asset('storage/' . $podcast->image); // Format podcast image URL
                $podcast->podcast_music = asset('storage/' . $podcast->music); // Format podcast music URL
                return $podcast;
            });
    
        // Step 4: Format URLs for the most recent podcast (if it exists)
        if ($recentPodcast) {
            $recentPodcast->podcast_image = asset('storage/' . $recentPodcast->image);
            $recentPodcast->podcast_music = asset('storage/' . $recentPodcast->music);
        }
    
        // Step 5: Prepare the response data
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => [
                'categories' => $categories, // List of categories
                'recent_podcast' => $recentPodcast, // Most recent podcast with details
                'last_week_podcasts' => $lastWeekPodcasts // Podcasts published in the last 7 days
            ]
        ], 200);
    }
    
public function podcastCategory()
{
    try {
        $categories = PodcastCategory::where('status', 'active')->get()->map(function ($category) {
            $category->image_url = $category->category_img 
                ? asset('storage/' . $category->category_img)
                : null; // Ensure there's a fallback if no image is available
            return $category;
        });

        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $categories
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while retrieving data',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
