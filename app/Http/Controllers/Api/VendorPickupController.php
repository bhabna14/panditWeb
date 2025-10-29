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
            // ✅ Auth via vendor-api guard
            $vendor = Auth::guard('vendor-api')->user();

            if (!$vendor) {
                return response()->json([
                    'status'  => 401,
                    'message' => 'Unauthorized. Vendor not logged in.',
                ], 401);
            }

            // Optional filter: ?date=YYYY-MM-DD
            $date = $request->query('date');

            $pickupsQuery = FlowerPickupDetails::with([
                    'flowerPickupItems.flower',
                    'flowerPickupItems.unit',
                    'rider'
                ])
                ->where('vendor_id', $vendor->vendor_id)
                ->where('stuatus',  'pending')
                ->orderBy('created_at', 'desc');
                

            if ($date) {
                // Match either pickup_date or created_at to the given date (adjust to your schema)
                $pickupsQuery->whereDate('pickup_date', $date);
            }

            $pickups = $pickupsQuery->get();

            // Build a consistent vendor block for the payload
            $vendorBlock = [
                'vendor_id'   => $vendor->vendor_id,
                'vendor_name' => $vendor->vendor_name ?? null,
                'phone_no'    => $vendor->phone_no ?? null,
            ];

            if ($pickups->isEmpty()) {
                return response()->json([
                    'status'  => 200,
                    'message' => 'No pickup requests found for this vendor.',
                    'vendor'  => $vendorBlock,
                    'data'    => [],
                ], 200);
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Pickup requests fetched successfully.',
                'vendor'  => $vendorBlock,
                'data'    => $pickups,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong while fetching pickups.',
                'error'   => app()->environment('production') ? 'server_error' : $e->getMessage(),
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

        // Strict mode by query: ?strict=1  (keeps old 422 behavior on mismatch)
        $strict = (bool) $request->boolean('strict', false);

        // --- Helpers for money (store as paise internally) ---
        $toPaise  = fn($x) => (int) round(((float)$x) * 100);
        $toRupees = fn($p) => round(((int)$p) / 100, 2);

        // ✅ Validate
        $validated = $request->validate([
            'total_price' => ['nullable','numeric','min:0'],
            'status'      => ['nullable', Rule::in(['PickupCompleted','Pending','Cancelled','InProgress'])],

            'flower_pickup_items'                     => ['nullable','array','min:1'],
            'flower_pickup_items.*.id'                => ['nullable','integer'],
            'flower_pickup_items.*.flower_id'         => ['nullable','string'],
            'flower_pickup_items.*.price'             => ['nullable','numeric','min:0'],
            'flower_pickup_items.*.quantity'          => ['nullable','numeric','min:0'],    // optional
            'flower_pickup_items.*.item_total_price'  => ['nullable','numeric','min:0'],    // optional; we can compute
        ]);

        try {
            $json = DB::transaction(function () use ($vendor, $pickupId, $validated, $strict, $toPaise, $toRupees) {

                // 1) Lock pickup and ensure vendor owns it
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

                $updatedItems   = [];
                $sumItemPaise   = 0;
                $hadItemsInBody = isset($validated['flower_pickup_items']) && is_array($validated['flower_pickup_items']);

                // 2) If items provided, load and update them
                if ($hadItemsInBody) {
                    $itemIds = collect($validated['flower_pickup_items'])
                        ->pluck('id')->filter()->unique()->values();

                    // Fetch only provided ids (if ids missing, we will error below)
                    $existing = \App\Models\FlowerPickupItems::where('pick_up_id', $pickupId)
                        ->whereIn('id', $itemIds)
                        ->get()->keyBy('id');

                    // Ensure all IDs exist and belong to this pickup
                    $missing = $itemIds->diff($existing->keys());
                    if ($missing->isNotEmpty()) {
                        return response()->json([
                            'status'  => 422,
                            'message' => 'Some items do not belong to this pickup or do not exist.',
                            'errors'  => ['missing_item_ids' => $missing->values()],
                        ], 422);
                    }

                    foreach ($validated['flower_pickup_items'] as $row) {
                        if (!isset($row['id'])) {
                            return response()->json([
                                'status'  => 422,
                                'message' => 'Each item must include an id.',
                            ], 422);
                        }

                        $it = $existing[$row['id']];

                        // flower_id integrity if provided
                        if (isset($row['flower_id']) && (string)$it->flower_id !== (string)$row['flower_id']) {
                            return response()->json([
                                'status'  => 422,
                                'message' => 'flower_id mismatch for item id '.$row['id'],
                            ], 422);
                        }

                        // Price (₹) -> paise
                        $pricePaise = isset($row['price']) ? $toPaise($row['price']) : $toPaise($it->price ?? 0);
                        $qty        = isset($row['quantity']) ? (float)$row['quantity'] : (float)($it->quantity ?? 1);

                        // Determine item_total_price (₹) if not provided: price * qty
                        if (isset($row['item_total_price'])) {
                            $itemTotalPaise = $toPaise($row['item_total_price']);
                        } else {
                            $itemTotalPaise = (int) round($pricePaise * $qty);
                        }

                        // Save back to DB in rupees format
                        $it->price            = $toRupees($pricePaise);
                        // Persist quantity if your schema has it
                        if (property_exists($it, 'quantity')) {
                            $it->quantity = $qty;
                        }
                        $it->item_total_price = $toRupees($itemTotalPaise);
                        $it->save();

                        $sumItemPaise += $itemTotalPaise;

                        $updatedItems[] = [
                            'id'               => $it->id,
                            'flower_id'        => $it->flower_id,
                            'price'            => (float) $it->price,
                            'quantity'         => isset($it->quantity) ? (float)$it->quantity : $qty,
                            'item_total_price' => (float) $it->item_total_price,
                        ];
                    }
                } else {
                    // If no items provided, compute current sum from DB
                    $sumItemPaise = \App\Models\FlowerPickupItems::where('pick_up_id', $pickupId)
                        ->get()
                        ->reduce(function ($carry, $it) use ($toPaise) {
                            return $carry + $toPaise($it->item_total_price ?? 0);
                        }, 0);
                }

                // 3) Decide total
                $providedTotalPaise = isset($validated['total_price']) ? $toPaise($validated['total_price']) : null;

                $totalPriceAdjusted = false;
                $finalTotalPaise    = $sumItemPaise;

                if ($providedTotalPaise !== null) {
                    if ($strict && $providedTotalPaise !== $sumItemPaise) {
                        // Strict mode => keep old behavior (422)
                        return response()->json([
                            'status'  => 422,
                            'message' => 'Provided total_price does not match the sum of item_total_price.',
                            'data'    => [
                                'provided_total_price' => $toRupees($providedTotalPaise),
                                'sum_item_total_price' => $toRupees($sumItemPaise),
                                'difference'           => $toRupees($sumItemPaise - $providedTotalPaise),
                            ],
                        ], 422);
                    }

                    // Non-strict: we auto-snap to computed sum and flag it
                    if ($providedTotalPaise !== $sumItemPaise) {
                        $totalPriceAdjusted = true;
                    }
                }

                // 4) Save pickup total + status + audit
                $pickup->total_price = $toRupees($finalTotalPaise);
                if (isset($validated['status'])) {
                    $pickup->status = $validated['status'];
                } else {
                    // Default to PickupCompleted if caller intended to close
                    $pickup->status = $pickup->status ?? 'PickupCompleted';
                }
                $pickup->updated_by = $vendor->vendor_name;
                $pickup->save();

                return response()->json([
                    'status'  => 200,
                    'message' => 'Prices updated successfully by '.$vendor->vendor_name,
                    'data'    => [
                        'pickup' => [
                            'pick_up_id'           => $pickup->pick_up_id,
                            'total_price'          => (float) $pickup->total_price,
                            'status'               => $pickup->status,
                            'updated_by'           => $pickup->updated_by,
                            'updated_at'           => optional($pickup->updated_at)->toDateTimeString(),
                            'total_price_adjusted' => $totalPriceAdjusted,
                            'provided_total_price' => $providedTotalPaise !== null ? $toRupees($providedTotalPaise) : null,
                            'sum_item_total_price' => $toRupees($sumItemPaise),
                        ],
                        'items_updated' => count($updatedItems),
                        'items'         => $updatedItems,
                    ],
                ], 200);
            });

            return $json;

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'An error occurred while updating prices.',
                'error'   => app()->environment('production') ? 'server_error' : $e->getMessage(),
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
            ->where(function ($q) {
                $q->where('status', '!=', 'pending')
                  ->orWhereNull('status'); // keep this only if you want NULLs included
            })
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