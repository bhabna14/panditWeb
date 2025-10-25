<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;

class VendorPickupController extends Controller
{
    // ✅ Fetch vendor pickups
    public function getVendorPickups(Request $request)
    {
        try {
            $vendor = Auth::guard('vendor-api')->user();

            if (!$vendor) {
                return response()->json([
                    'status'  => 401,
                    'message' => 'Unauthorized. Vendor not logged in.',
                ], 401);
            }

            $pickups = FlowerPickupDetails::with([
                    'flowerPickupItems.flower',
                    'flowerPickupItems.unit',
                    'rider'
                ])
                ->where('vendor_id', $vendor->vendor_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status'  => 200,
                'message' => $pickups->isEmpty() ? 'No pickup requests found.' : 'Pickup requests fetched successfully.',
                'data'    => $pickups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong while fetching pickups.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Update flower prices
    public function updateFlowerPrices(Request $request, $pickupId)
    {
        try {
            $vendor = Auth::guard('vendor-api')->user();

            if (!$vendor) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized. Vendor not logged in.',
                ], 401);
            }

            $validated = $request->validate([
                'total_price' => 'required|numeric',
                'flower_pickup_items' => 'required|array',
                'flower_pickup_items.*.id' => 'required|integer',
                'flower_pickup_items.*.flower_id' => 'required|string',
                'flower_pickup_items.*.price' => 'required|numeric',
            ]);

            $pickup = FlowerPickupDetails::where('pick_up_id', $pickupId)
                ->where('vendor_id', $vendor->vendor_id)
                ->first();

            if (!$pickup) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pickup not found or not assigned to this vendor.',
                ], 404);
            }

            $pickup->update([
                'total_price' => $validated['total_price'],
                'status' => 'PickupCompleted',
                'updated_by' => $vendor->vendor_name,
            ]);

            foreach ($validated['flower_pickup_items'] as $item) {
                FlowerPickupItems::where('id', $item['id'])
                    ->where('pick_up_id', $pickupId)
                    ->update(['price' => $item['price']]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Prices updated successfully by ' . $vendor->vendor_name,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
