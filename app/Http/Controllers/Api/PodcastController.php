<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;
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
        //
        $podcasts = Podcast::where('status', 'active')->orderBy('id', 'desc')->get();
        foreach ($podcasts as $podcast) {
            $podcast->image_url = asset('storage/' . $podcast->image);
            $podcast->music_url = asset('storage/' . $podcast->music);
        }
        if ($podcasts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);
        }
        // return response()->json($podcasts);
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $podcasts
        ], 200);
    }

    public function podcasthomepage()
{
    // Step 1: Get active categories
    // $categories = PodcastCategory::where('status', 'active')->get();
    $categories = PodcastCategory::where('status', 'active')->get()->map(function ($category) {
        $category->image_url = asset('storage/' . $category->category_img); // Assuming category_img is the field for the image path
        return $category;
    });

    // Step 2: Get the most recent podcast
    $recentPodcast = Podcast::where('status', 'active')->latest()->first(); // Retrieves the latest uploaded podcast

    // Step 3: Get podcasts published in the last 7 days
    $lastWeekPodcasts = Podcast::where('publish_date', '>=', Carbon::now()->subDays(7))->get();

    // Step 4: Format URLs for image and music for the recent podcast
    if ($recentPodcast) {
        $recentPodcast->image_url = asset('storage/' . $recentPodcast->image);
        $recentPodcast->music_url = asset('storage/' . $recentPodcast->music);
    }

    // Step 5: Format URLs for image and music for the last week podcasts
    foreach ($lastWeekPodcasts as $podcast) {
        $podcast->image_url = asset('storage/' . $podcast->image);
        $podcast->music_url = asset('storage/' . $podcast->music);
    }

    // Step 6: Prepare the response data
    return response()->json([
        'status' => 200,
        'message' => 'Data retrieved successfully',
        'data' => [
            'categories' => $categories,
            'recent_podcast' => $recentPodcast, // Single podcast object
            'last_week_podcasts' => $lastWeekPodcasts
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
