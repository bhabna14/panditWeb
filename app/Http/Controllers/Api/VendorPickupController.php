<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerVendor;
use Carbon\Carbon;

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

   public function vendorDetails(Request $request)
    {
        $vendor = Auth::guard('vendor-api')->user();

        if (!$vendor) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized. Vendor not logged in.',
            ], 401);
        }

        // Eager-load monthly prices + product & unit
        $vendorDetails = FlowerVendor::active()
            ->where('vendor_id', $vendor->vendor_id)
            ->with([
                // All monthly prices (latest first)
                'monthPrices' => fn ($q) => $q->orderByDesc('start_date')->orderByDesc('id'),
                'monthPrices.product:product_id,name',
                'monthPrices.unit:id,unit_name',
            ])
            ->first();

        if (!$vendorDetails) {
            return response()->json([
                'status'  => 404,
                'message' => 'Vendor not found or inactive.',
                'data'    => null,
            ], 404);
        }

        // Derive "current" prices = valid today (start <= today <= end OR end null)
        $today = Carbon::today();
        $currentPrices = $vendorDetails->monthPrices
            ->filter(function ($p) use ($today) {
                $starts = $p->start_date ? $p->start_date->lte($today) : true;
                $ends   = !$p->end_date || $p->end_date->gte($today);
                return $starts && $ends;
            })
            ->values()
            ->map(function ($p) {
                return [
                    'price_id'       => $p->id,
                    'product_id'     => $p->product_id,
                    'product_name'   => optional($p->product)->name,
                    'unit_id'        => $p->unit_id,
                    'unit_name'      => optional($p->unit)->unit_name,
                    'quantity'       => (int) $p->quantity,
                    'price_per_unit' => (float) $p->price_per_unit,
                    'start_date'     => optional($p->start_date)->toDateString(),
                    'end_date'       => optional($p->end_date)->toDateString(),
                ];
            });

        // Map all monthly prices (for full history in UI if needed)
        $allPrices = $vendorDetails->monthPrices
            ->map(function ($p) {
                return [
                    'price_id'       => $p->id,
                    'product_id'     => $p->product_id,
                    'product_name'   => optional($p->product)->name,
                    'unit_id'        => $p->unit_id,
                    'unit_name'      => optional($p->unit)->unit_name,
                    'quantity'       => (int) $p->quantity,
                    'price_per_unit' => (float) $p->price_per_unit,
                    'start_date'     => optional($p->start_date)->toDateString(),
                    'end_date'       => optional($p->end_date)->toDateString(),
                ];
            });

        // Shape the profile payload (hide password/otp automatically via model $hidden)
        $profile = [
            'vendor_id'       => $vendorDetails->vendor_id,
            'vendor_name'     => $vendorDetails->vendor_name,
            'phone_no'        => $vendorDetails->phone_no,
            'email_id'        => $vendorDetails->email_id,
            'vendor_category' => $vendorDetails->vendor_category,
            'payment_type'    => $vendorDetails->payment_type,
            'vendor_gst'      => $vendorDetails->vendor_gst,
            'vendor_address'  => $vendorDetails->vendor_address,
            'flower_ids'      => $vendorDetails->flower_ids,
            'date_of_joining' => $vendorDetails->date_of_joining,
            'vendor_document' => $vendorDetails->vendor_document,
            'status'          => $vendorDetails->status,

            // New bits:
            'current_monthly_prices' => $currentPrices, // valid today
            'all_monthly_prices'     => $allPrices,     // full list
        ];

        return response()->json([
            'status'  => 200,
            'message' => 'Vendor details fetched successfully.',
            'data'    => $profile,
        ]);
    }

}