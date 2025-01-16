<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;
use Carbon\Carbon;

class PodcastScriptController extends Controller
{

    public function podcastScript()
    {
        $podcastDetails = PodcastPrepair::where('podcast_create_status', 'PODCAST INITIALIZE')
            ->where('podcast_script_status', 'PENDING')
            ->get()
            ->groupBy(function ($item) {
                // Use Y-m for a sortable format, then convert it back to F Y for display
                return Carbon::parse($item->podcast_create_date)->format('Y-m');
            })
            ->sortKeys() // Sort by year and month in ascending order
            ->mapWithKeys(function ($items, $key) {
                // Convert the keys back to F Y for display
                $formattedKey = Carbon::createFromFormat('Y-m', $key)->format('F Y');
                return [$formattedKey => $items];
            });
    
        return view('admin/add-podcast-script', compact('podcastDetails'));
    }
    
    

    public function updatePodcastScript(Request $request, $podcast_id)
{
    // Validate the request
    $request->validate([
        'script_location' => 'required|url',
        'story_source' => 'required|string',
        'script_created_by' => 'required|string',
      
    ]);

    try {
        // Find the podcast by podcast_id
        $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

        if (!$podcast) {
            return redirect()->back()->withErrors('Podcast not found.');
        }

        // Update the podcast script-related data
        $podcast->update([
            'script_location' => $request->script_location,
            'story_source' => $request->story_source,
            'script_created_by' => $request->script_created_by,
            'script_created_date' => $request->script_created_date,
            'podcast_script_status' => 'SCRIPT CREATED',
            'podcast_create_status' => 'COMPLETED',
        ]);

        // Redirect with success message
        return redirect()->route('podcastScriptVerified')->with('success', 'Podcast script updated successfully.');
    } catch (\Exception $e) {
        // Handle exceptions and log the error for debugging
        \Log::error('Error updating podcast script: ' . $e->getMessage());
        return redirect()->back()->withErrors('An error occurred while updating the podcast.');
    }
}

// script verified

public function podcastScriptVerified(){

    $podcast_details = PodcastPrepair::where('podcast_script_status','SCRIPT CREATED')->get();

    return view('admin.podcast-script-verified',compact('podcast_details'));

}

public function approvePodcastScript($podcast_id)
{
// Find the podcast by podcast_id
$podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

if ($podcast) {
    // Update the script status to 'COMPLETED'
    $podcast->podcast_script_status = 'APPROVED';
    $podcast->podcast_create_status = 'COMPLETED';

    $podcast->save();

    // Redirect back with success message
    return redirect()->route('podcastRecording')->with('success', 'Podcast approved successfully.');
} else {
    // Redirect back with error message
    return redirect()->back()->with('error', 'Podcast not found.');
}
}


public function rejectPodcastScript(Request $request, $podcast_id)
{
// Validate the input
$request->validate([
    'script_reject_reason' => 'required|string|max:255',
]);

// Find the podcast by podcast_id
$podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

if ($podcast) {
    // Update the script status and save the rejection reason
    $podcast->script_reject_reason = $request->script_reject_reason;
    $podcast->podcast_script_status = 'PENDING';
    $podcast->podcast_create_status = 'PODCAST INITIALIZE';

    $podcast->save();

    // Redirect back with success message
    return redirect()->route('podcastScript')->with('success', 'Podcast rejected successfully.');
} else {
    // Redirect back with error message
    return redirect()->back()->with('error', 'Podcast not found.');
}
}

// app/Http/Controllers/PodcastController.php
public function updateScriptVerified(Request $request, $podcast_id)
{
// Validate the inputs
$request->validate([
    'script_verified_by' => 'required|string|max:255',
    'script_verified_date' => 'required|date',
]);

// Find the podcast by podcast_id
$podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

if ($podcast) {
    // Update the script_verified_by and script_verified_date fields
    $podcast->script_verified_by = $request->script_verified_by;
    $podcast->script_verified_date = $request->script_verified_date;
    $podcast->save();

    // Redirect back with success message
    return redirect()->back()->with('success', 'Podcast script verification details updated successfully.');
} else {
    // Redirect back with error if the podcast is not found
    return redirect()->back()->with('error', 'Podcast not found.');
}
}

    public function podcastRecording(){

        $podcast_recording = PodcastPrepair::where('podcast_script_status','APPROVED')
        ->get();
    
        return view('admin/podcast-recording', compact('podcast_recording'));

    }

    public function startPodcast($podcast_id)
    {
        try {
                $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
            if ($podcast->podcast_recording_status !== 'PENDING') {

                return redirect()->back()->with('error', 'Podcast is not in the correct status to start.');
            }
            $podcast->update(['podcast_recording_status' => 'STARTED']);
            return redirect()->back()->with('success', 'Podcast started successfully.');
        } catch (\Exception $e) {
            \Log::error('Error starting podcast: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while starting the podcast.');
        }
    }
    
    public function cancelPodcast($podcast_id)
    {
        try {
                $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
            if ($podcast->podcast_recording_status !== 'STARTED') {
                return redirect()->back()->with('error', 'Podcast is not in the correct status to cancel.');
            }
            $podcast->update(['podcast_recording_status' => 'PENDING']);
            return redirect()->back()->with('success', 'Podcast canceled successfully.');
        } catch (\Exception $e) {
            \Log::error('Error canceling podcast: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while canceling the podcast.');
        }
    }
    
    public function completePodcast($podcast_id)
    {
        try {
            $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
            if ($podcast->podcast_recording_status !== 'STARTED') {
                return redirect()->back()->with('error', 'Podcast is not in the correct status to complete.');
            }
            $podcast->update(['podcast_recording_status' => 'RECORDING COMPLETED']);
            return redirect()->back()->with('success', 'Podcast marked as completed.');
        } catch (\Exception $e) {
            \Log::error('Error completing podcast: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while completing the podcast.');
        }
    }
    
    public function saveCompleteUrl(Request $request, $podcast_id)
    {
        try {
            $request->validate([
                'recording_complete_url' => 'required|url',
                'podcast_recording_by' => 'required|string',
                'recording_date' => 'required|date',
            ]);
    
            $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
    
            if (!$podcast) {
                return redirect()->back()->with('error', 'Podcast not found.');
            }
    
            if ($podcast->podcast_recording_status !== 'RECORDING COMPLETED') {
                return redirect()->back()->with('error', 'Podcast must be marked as completed before saving the URL.');
            }
    
            $podcast->update([
                'recording_complete_url' => $request->recording_complete_url,
                'podcast_recording_by' => $request->podcast_recording_by,
                'recording_date' => $request->recording_date,
                'podcast_recording_status' => 'COMPLETED',
                'podcast_script_status' => 'COMPLETED',
                'podcast_create_status' => 'COMPLETED',
            ]);
    
            return redirect()->route('podcastEditing')->with('success', 'URL and details saved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error saving podcast details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving the details.');
        }
    }
    
    public function scriptEditor($podcast_id)
    {
    // Retrieve the first matching podcast or fail if not found
    $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->firstOrFail();

    // Pass the podcast object to the view
    return view('admin.script-editor', compact('podcast'));
    }

    public function saveScriptEditor(Request $request, $podcast_id)
    {
        $validated = $request->validate([
            'script_editor' => 'required|string',
        ]);
    
        $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->firstOrFail();
        $podcast->script_editor = $validated['script_editor'];
        $podcast->save();
    
        return redirect()->route('scriptEditor', $podcast_id)->with('success', 'Script updated successfully!');
    }

}
