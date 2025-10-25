<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerVendor;

use Illuminate\Support\Facades\Auth;

class VendorPickupController extends Controller
{
    public function getVendorPickups(Request $request)
    {
        try {
            // ✅ Get the logged-in vendor (via vendor-api guard)
            $vendor = Auth::guard('vendor-api')->user();

            if (!$vendor) {
                return response()->json([
                    'status'  => 401,
                    'message' => 'Unauthorized. Vendor not logged in.',
                ], 401);
            }

            // Optional: filter by date if needed
            $today = now()->toDateString();

            // ✅ Fetch pickups for this vendor
            $pickups = FlowerPickupDetails::with([
                    'flowerPickupItems.flower',
                    'flowerPickupItems.unit',
                    'rider'
                ])
                ->where('vendor_id', $vendor->vendor_id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($pickups->isEmpty()) {
                return response()->json([
                    'status'  => 200,
                    'message' => 'No pickup requests found for this vendor.',
                    'data'    => [],
                ]);
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Pickup requests fetched successfully.',
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

    public function updateFlowerPrices(Request $request, $pickupId)
    {
        try {
            // ✅ Authenticate vendor
            $vendor = Auth::guard('vendor-api')->user();

            if (!$vendor) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized. Vendor not logged in.',
                ], 401);
            }

            // ✅ Validate the incoming request
            $validated = $request->validate([
                'total_price' => 'required|numeric',
                'flower_pickup_items' => 'required|array',
                'flower_pickup_items.*.id' => 'required|integer',
                'flower_pickup_items.*.flower_id' => 'required|string',
                'flower_pickup_items.*.price' => 'required|numeric',
            ]);

            // ✅ Find the pickup record by ID
            $pickup = FlowerPickupDetails::where('pick_up_id', $pickupId)
                ->where('vendor_id', $vendor->vendor_id) // ensure vendor owns it
                ->first();

            if (!$pickup) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pickup not found or not assigned to this vendor.',
                ], 404);
            }

            // ✅ Update the pickup details
            $pickup->total_price = $validated['total_price'];
            $pickup->status = 'PickupCompleted';
            $pickup->updated_by = $vendor->vendor_name; // ← record vendor who updated
            $pickup->save();

            // ✅ Update each flower item price
            foreach ($validated['flower_pickup_items'] as $item) {
                $flowerPickupItem = FlowerPickupItems::where('id', $item['id'])
                    ->where('pick_up_id', $pickupId)
                    ->first();

                if ($flowerPickupItem) {
                    $flowerPickupItem->price = $item['price'];
                    $flowerPickupItem->save();
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Prices updated successfully by ' . $vendor->vendor_name,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllPickups()
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
                    'vendor',
                    'rider'
                ])
                ->where('vendor_id', $vendor->vendor_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status'  => 200,
                'message' => 'All pickup requests fetched successfully.',
                'data'    => $pickups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong while fetching all pickups.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function vendorDetails()
    {
        $vendor = Auth::guard('vendor-api')->user();

        if (!$vendor) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized. Vendor not logged in.',
            ], 401);
        }

        $vendorDetails =  FlowerVendor::where('status', 'Active')
            ->where('vendor_id', $vendor->vendor_id)
            ->first();

        return response()->json([
            'status'  => 200,
            'message' => 'Vendor details fetched successfully.',
            'data'    => $vendorDetails,
        ]);
    }

}