<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FlowerCalendor;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;

class FlowerCalendarApiController extends Controller
{

  use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\FlowerCalendor;
use App\Models\FlowerProduct;

public function getFestivalCalendar(): JsonResponse
{
    try {
        $festivals = FlowerCalendor::where('status', 'active')->get();

        $formattedFestivals = $festivals->map(function ($festival) {
            // Build package info: each with product_id and name
            $packageInfo = [];
            if (!empty($festival->product_id)) {
                $productIds = explode(',', $festival->product_id);

                $products = FlowerProduct::whereIn('product_id', $productIds)
                    ->get(['product_id', 'name']);

                foreach ($products as $product) {
                    $packageInfo[] = [
                        'product_id' => $product->product_id,
                        'name'       => $product->name
                    ];
                }
            }

            return [
                'id'              => $festival->id,
                'festival_name'   => $festival->festival_name,
                'festival_date'   => $festival->festival_date,
                'package_price'   => $festival->package_price,
                'description'     => $festival->description,
                'related_flower'  => $festival->related_flower,
                'packages'        => $packageInfo, // now an array of objects
                'festival_image'  => $festival->festival_image
                    ? URL::to($festival->festival_image)
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formattedFestivals,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Failed to fetch festivals: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Server Error. Unable to fetch festivals.',
        ], 500);
    }
}

}
