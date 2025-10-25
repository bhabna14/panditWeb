<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerPickupDetails;
use App\Models\FlowerPickupItems;
use App\Models\FlowerVendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class VendorPickupController extends Controller
{
public function getVendorPickups(Request $request)
{
    try {
        // ✅ Auth (vendor-api)
        $vendor = Auth::guard('vendor-api')->user();
        if (!$vendor) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized. Vendor not logged in.',
            ], 401);
        }

        // ✅ Fetch pickups (with relations)
        $pickups = FlowerPickupDetails::with([
                'flowerPickupItems.flower',
                'flowerPickupItems.unit',
                'rider',
            ])
            ->where('vendor_id', $vendor->vendor_id)
            ->orderByDesc('created_at')
            ->get();

        if ($pickups->isEmpty()) {
            return response()->json([
                'status'  => 200,
                'message' => 'No pickup requests found for this vendor.',
                'data'    => [],
                'meta'    => ['count' => 0],
            ]);
        }

        // ✅ Inject vendor fields into each pickup object
        $data = $pickups->map(function ($pickup) use ($vendor) {
            $row = $pickup->toArray();           // keep all original fields & relations
            $row['vendor_name'] = $vendor->vendor_name;
            $row['phone_no']    = $vendor->phone_no;
            // vendor_id is already present from DB as $row['vendor_id']
            return $row;
        });

        return response()->json([
            'status'  => 200,
            'message' => 'Pickup requests fetched successfully.',
            'data'    => $data,                  // 👈 vendor_name & phone_no included per row
            'meta'    => ['count' => $data->count()],
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 500,
            'message' => 'Something went wrong while fetching pickups.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


    public function updateFlowerPrices(Request $request, $pickupId)
    {
        $vendor = Auth::guard('vendor-api')->user();
        if (!$vendor) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized. Vendor not logged in.',
            ], 401);
        }

        // ✅ 2) Validate payload (uses item_total_price per item)
        $validated = $request->validate([
            'total_price' => ['required','numeric','min:0'],
            'status'      => ['nullable', Rule::in(['PickupCompleted','Pending','Cancelled','InProgress'])],
            'flower_pickup_items'                 => ['nullable','array','min:1'],
            'flower_pickup_items.*.id'            => ['nullable','integer'],
            'flower_pickup_items.*.flower_id'     => ['nullable','string'],
            'flower_pickup_items.*.price'         => ['nullable','numeric','min:0'],
            'flower_pickup_items.*.item_total_price' => ['nullable','numeric','min:0'], // 👈 new key
        ]);

        $ALLOWED_DIFF = 0.01; // tiny float tolerance

        try {
            $json = DB::transaction(function () use ($vendor, $pickupId, $validated, $ALLOWED_DIFF) {
                // ✅ 3) Lock pickup and ensure vendor owns it
                $pickup = \App\Models\FlowerPickupDetails::where('pick_up_id', $pickupId)
                    ->where('vendor_id', $vendor->vendor_id)
                    ->lockForUpdate()
                    ->first();

                if (!$pickup) {
                    return response()->json([
                        'status'  => 404,
                        'message' => 'Pickup not found or not assigned to this vendor.',
                    ], 404);
                }

                // ✅ 4) Fetch all target items for this pickup
                $itemIds = collect($validated['flower_pickup_items'])->pluck('id')->unique()->values();
                $existing = \App\Models\FlowerPickupItems::where('pick_up_id', $pickupId)
                    ->whereIn('id', $itemIds)
                    ->get()
                    ->keyBy('id');

                $missing = $itemIds->diff($existing->keys());
                if ($missing->isNotEmpty()) {
                    return response()->json([
                        'status'  => 422,
                        'message' => 'Some items do not belong to this pickup or do not exist.',
                        'errors'  => ['missing_item_ids' => $missing->values()],
                    ], 422);
                }

                // ✅ 5) Update items and compute sum(item_total_price)
                $sumItemTotals = 0.0;
                $updatedItems  = [];

                foreach ($validated['flower_pickup_items'] as $row) {
                    $it = $existing[$row['id']];

                    // Ensure flower_id integrity
                    if ((string)$it->flower_id !== (string)$row['flower_id']) {
                        return response()->json([
                            'status'  => 422,
                            'message' => 'flower_id mismatch for item id '.$row['id'],
                        ], 422);
                    }

                    $it->price            = $row['price'];
                    $it->item_total_price = $row['item_total_price']; // 👈 save to DB
                    $it->save();

                    $sumItemTotals += (float) $row['item_total_price'];

                    $updatedItems[] = [
                        'id'               => $it->id,
                        'flower_id'        => $it->flower_id,
                        'price'            => (float) $it->price,
                        'item_total_price' => (float) $it->item_total_price,
                    ];
                }

                // ✅ 6) Verify pickup total equals sum of item totals
                $providedTotal = (float) $validated['total_price'];
                if (abs($sumItemTotals - $providedTotal) > $ALLOWED_DIFF) {
                    return response()->json([
                        'status'  => 422,
                        'message' => 'Provided total_price does not match the sum of item_total_price.',
                        'data'    => [
                            'provided_total_price' => round($providedTotal, 2),
                            'sum_item_total_price' => round($sumItemTotals, 2),
                            'difference'           => round($sumItemTotals - $providedTotal, 2),
                        ],
                    ], 422);
                }

                // ✅ 7) Save pickup total + status + audit
                $pickup->total_price = $providedTotal;
                $pickup->status      = $validated['status'] ?? 'PickupCompleted';
                $pickup->updated_by  = $vendor->vendor_name;
                $pickup->save();

                return response()->json([
                    'status'  => 200,
                    'message' => 'Prices updated successfully by '.$vendor->vendor_name,
                    'data'    => [
                        'pickup' => [
                            'pick_up_id'   => $pickup->pick_up_id,
                            'total_price'  => (float) $pickup->total_price,
                            'status'       => $pickup->status,
                            'updated_by'   => $pickup->updated_by,
                            'updated_at'   => optional($pickup->updated_at)->toDateTimeString(),
                        ],
                        'items_updated'        => count($updatedItems),
                        'items'                => $updatedItems,
                        'sum_item_total_price' => round($sumItemTotals, 2),
                    ],
                ], 200);
            });

            return $json;

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'An error occurred while updating prices.',
                'error'   => $e->getMessage(),
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