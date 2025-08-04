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
                    'product_id'
                ]);

            $offerDetails->transform(function ($offer) {
                $offer->image = $offer->image ? url($offer->image) : null;

                // Resolve product ID and name pair
                $packages = [];
                if (!empty($offer->product_id)) {
                    $productIds = array_filter(explode(',', $offer->product_id));
                    $packages = FlowerProduct::whereIn('product_id', $productIds)
                        ->get(['product_id', 'name'])
                        ->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'name' => $product->name,
                            ];
                        })->values()->toArray();
                }

                return [
                    'main_header'   => $offer->main_header,
                    'sub_header'    => $offer->sub_header,
                    'content'       => $offer->content,
                    'discount'      => $offer->discount,
                    'menu'          => $offer->menu,
                    'image'         => $offer->image,
                    'start_date'    => $offer->start_date,
                    'end_date'      => $offer->end_date,
                    'packages'      => $packages,
                ];
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