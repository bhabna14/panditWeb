<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferDetails;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class OfferDetailsApiController extends Controller
{
   public function getOfferDetails(Request $request)
{
    try {
        $offerDetails = OfferDetails::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get([
                'main_header',
                'sub_header',
                'content',
                'discount',
                'menu',
                'image',
                'start_date',
                'end_date',
                'product_id' // fetch product_id to map package names
            ]);

        // Format each offer
        $offerDetails->transform(function ($offer) {
            // Convert image to full URL
            $offer->image = $offer->image ? url($offer->image) : null;

            // Extract and resolve package names
            $packageNames = [];
            if (!empty($offer->product_id)) {
                $productIds = explode(',', $offer->product_id);
                $packageNames = FlowerProduct::whereIn('product_id', $productIds)->pluck('name')->toArray();
            }

            $offer->package_names = $packageNames;

            return $offer;
        });

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
