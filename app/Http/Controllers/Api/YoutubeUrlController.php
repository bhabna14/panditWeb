<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Youtube;

class YoutubeUrlController extends Controller
{
    public function manageYoutube()
{
    $youtubes = Youtube::where('status', 'active')->get();

    return response()->json([
        'status' => 200,
        'message' => 'Data fetched successfully.',
        'data' => $youtubes
    ], 200);
}

}
