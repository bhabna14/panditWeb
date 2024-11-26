<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;

class PodcastEditingController extends Controller
{
    public function podcastEditing(){

        $podcast_editing = PodcastPrepair::where('podcast_recording_status', 'COMPLETED')
        ->whereIn('podcast_editing_status', ['PENDING', 'STARTED', 'EDITING COMPLETED'])
        ->get();
    
        return view('admin/podcast-editing',compact('podcast_editing'));
    }

    public function startPodcastEdit($podcast_id)
    {
        try {
            $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
    
            if (!$podcast) {
                return redirect()->back()->with('error', 'Podcast not found.');
            }
    
            if ($podcast->podcast_editing_status !== 'PENDING') {
                return redirect()->back()->with('error', 'Podcast is not in the correct status to start.');
            }
    
            $podcast->update(['podcast_editing_status' => 'STARTED']);
    
            return redirect()->back()->with('success', 'Podcast started successfully.');
        } catch (\Exception $e) {
            \Log::error('Error starting podcast: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while starting the podcast.');
        }
    }
    
    public function cancelPodcastEdit($podcast_id)
    {
        try {
            $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
    
            if (!$podcast) {
                return redirect()->back()->with('error', 'Podcast not found.');
            }
    
            if ($podcast->podcast_editing_status !== 'STARTED') {
                return redirect()->back()->with('error', 'Podcast is not in the correct status to cancel.');
            }
    
            $podcast->update(['podcast_editing_status' => 'PENDING']);
    
            return redirect()->route('podcastEditing')->with('success', 'Podcast canceled successfully.');

        } catch (\Exception $e) {
            \Log::error('Error canceling podcast: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while canceling the podcast.');
        }
    }
    
    public function completePodcastEdit($podcast_id)
    {
        try {
            $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
    
            if (!$podcast) {
                return redirect()->back()->with('error', 'Podcast not found.');
            }
    
            if ($podcast->podcast_editing_status !== 'STARTED') {
                return redirect()->back()->with('error', 'Podcast is not in the correct status to complete.');
            }
    
            $podcast->update(['podcast_editing_status' => 'EDITING COMPLETED']);
    
            return redirect()->back()->with('success', 'Podcast marked as completed.');
        } catch (\Exception $e) {
            \Log::error('Error completing podcast: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while completing the podcast.');
        }
    }
    

    public function saveEditing(Request $request, $podcast_id)
    {
        try {
            // Fetch the specific podcast record
            $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();
    
            // Check if the podcast exists
            if (!$podcast) {
                return redirect()->back()->with('error', 'Podcast not found.');
            }
    
            // Validate the request
            $request->validate([
                'audio_edited_by' => 'required|string|max:255',
                'music_source' => 'nullable|string',
                'editing_complete_url' => 'nullable|url',
            ]);
    
            // Process music_source input (optional: trim each URL and remove extra spaces)
            $musicSources = $request->music_source
                ? implode(',', array_map('trim', explode(',', $request->music_source)))
                : null;
    
            // Update the podcast record
            $podcast->update([
                'audio_edited_by' => $request->audio_edited_by,
                'editing_date' => $request->editing_date,
                'editing_complete_url' => $request->editing_complete_url,
                'music_source' => $musicSources,
                'podcast_editing_status' => 'COMPLETED',
            ]);
    
            return redirect()->route('podcastEditingVerified')->with('success', 'Podcast editing details saved successfully!');
        } catch (\Illuminate\Database\QueryException $qe) {
            \Log::error('Database Query Error: ' . $qe->getMessage());
            return redirect()->back()->with('error', 'Database error occurred while saving the details.');
        } catch (\Exception $e) {
            \Log::error('Error saving podcast editing details: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while saving the details.');
        }
    }
    
    

public function podcastEditingVerified()
{
    // Fetch podcasts with 'COMPLETED' editing status
    $podcast_details = PodcastPrepair::where('podcast_editing_status', 'COMPLETED')->get();

    return view('admin.podcast-editing-verified', compact('podcast_details'));
}

public function approvePodcastEditing($podcast_id)
{
    // Find the podcast by podcast_id
    $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

    if ($podcast) {
        // Update the editing status to 'APPROVED'
        $podcast->podcast_editing_status = 'APPROVED';
        $podcast->save();

        // Redirect back with a success message
        return redirect()->route('podcastMedia')->with('success', 'Podcast approved successfully.');
    } else {
        // Redirect back with an error message
        return redirect()->back()->with('error', 'Podcast not found.');
    }
}

public function rejectPodcastEditing(Request $request, $podcast_id)
{
    // Validate the input
    $request->validate([
        'editing_reject_reason' => 'required|string|max:255',
    ]);

    // Find the podcast by podcast_id
    $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

    if ($podcast) {
        // Update the rejection reason and set the status to 'PENDING'
        $podcast->editing_reject_reason = $request->editing_reject_reason;
        $podcast->podcast_editing_status = 'PENDING';
        $podcast->save();

        // Redirect back with a success message
        return redirect()->route('podcastEditing')->with('success', 'Podcast rejected successfully.');
    } else {
        // Redirect back with an error message
        return redirect()->back()->with('error', 'Podcast not found.');
    }
}

public function updateEditingVerified(Request $request, $podcast_id)
{
    // Validate the inputs
    $request->validate([
        'editing_verified_by' => 'required|string|max:255',
        'editing_verified_date' => 'required|date',
    ]);

    // Find the podcast by podcast_id
    $podcast = PodcastPrepair::where('podcast_id', $podcast_id)->first();

    if ($podcast) {
        // Update the editing verified details
        $podcast->editing_verified_by = $request->editing_verified_by;
        $podcast->editing_verified_date = $request->editing_verified_date;
        $podcast->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Podcast editing verification details updated successfully.');
    } else {
        // Redirect back with an error if the podcast is not found
        return redirect()->back()->with('error', 'Podcast not found.');
    }
}
}
