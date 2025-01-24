<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PodcastPrepair;

use Carbon\Carbon;

class PodcastPlanningController extends Controller
{
    public function podcastPlanning()
    {
        // Fetch active podcasts
        $all_podcast = PodcastPrepair::where('status', 'active')->get();

        // Group podcasts by month-year using the podcast_create_date column
        $podcastDetails = $all_podcast->groupBy(function ($podcast) {
            return Carbon::parse($podcast->podcast_create_date)->format('F Y');
        });

        // Sort the grouped podcasts by month-year in ascending order
        $sortedPodcastDetails = $podcastDetails->sortKeysUsing(function ($a, $b) {
            return Carbon::createFromFormat('F Y', $a)->timestamp <=> Carbon::createFromFormat('F Y', $b)->timestamp;
        });

        return view('admin/podcast-planning', compact('sortedPodcastDetails', 'all_podcast'));
    }
}

