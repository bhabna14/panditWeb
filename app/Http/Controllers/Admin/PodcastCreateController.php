<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;
use App\Models\PodcastPrepair;

use App\Models\PodcastCategory;


class PodcastCreateController extends Controller
{
    public function podcastCreate()
    {
        $pooja_list = Poojalist::where('status', 'active')->get(['pooja_name', 'pooja_date']);
        $categories = PodcastCategory::where('status', 'active')->get();
    
        return view('admin/podcast-create', compact('categories', 'pooja_list'));
    }
    

    public function savePodcastCreate(Request $request)
    {
        // Wrap everything in a try-catch block for error handling
        try {
            // Validate the incoming request
            $request->validate([
                'language' => 'required|string|in:odia,english,hindi',
                'podcast_name' => 'required|string|max:255',
                'date' => 'required|date',
            ]);

            // Generate a random podcast ID
            $podcastId = 'PODCAST' . rand(10000, 99999);

            // Save the podcast data to the database
            $podcast = PodcastPrepair::create([
                'podcast_id' => $podcastId,
                'language' => $request->language,
                'podcast_name' => $request->podcast_name,
                'deity_category' => $request->deity_category,
                'festival_name' => $request->festival_name,
                'podcast_create_date' => $request->date,
                'podcast_create_status' => 'PODCAST INITIALIZE', // Assuming 'active' is a default status
            ]);

            // Redirect back with a success message
            return redirect()->route('podcastScript')->with('success', 'Podcast created successfully with ID: ' . $podcastId);

        } catch (Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error saving podcast: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'An error occurred while saving the podcast. Please try again.');
        }
    }
}
