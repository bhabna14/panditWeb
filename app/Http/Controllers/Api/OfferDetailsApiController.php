<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferDetails;

class OfferDetailsApiController extends Controller
{
    public function getOfferDetails(Request $request)
    {
        try {
            $offerDetails = OfferDetails::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get(['main_header', 'sub_header', 'content', 'discount', 'menu', 'image', 'start_date', 'end_date']);
                
            return response()->json([
                'status' => 200,
                'data' => $offerDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Failed to fetch offer details'
            ], 500);
        }
    }
}
