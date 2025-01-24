<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;

class PodcastPlanningController extends Controller
{
    public function podcastPlanning()
    {
        // Fetch active podcasts and group by month
        $all_podcast = PodcastPrepair::where('status', 'active')->get();
    
        // Group podcasts by month using the podcast_create_date column
        $podcastDetails = $all_podcast->groupBy(function ($podcast) {
            return \Carbon\Carbon::parse($podcast->podcast_create_date)->format('F Y');
        });
    
        return view('admin/podcast-planning', compact('podcastDetails', 'all_podcast'));
    }
    
}
