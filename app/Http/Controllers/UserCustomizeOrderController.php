<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\PoojaUnit;
use App\Models\FlowerRequest;
use App\Models\Locality;
use App\Models\FlowerRequestItem;
use Illuminate\Support\Str;
use App\Models\Apartment;
use Carbon\Carbon; // Add this at the top of the controller
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade
use Illuminate\Support\Facades\DB;

class UserCustomizeOrderController extends Controller
{
    public function createCustomizeOrder()
    {

        $user_details = User::get();

        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->get();


        $singleflowers = FlowerProduct::where('status', 'active')
        ->where('category', 'Flower')
        ->get();

        $Poojaunits = PoojaUnit::where('status', 'active')
        ->get();
    
    
        return view('create-customize-order', compact('flowers','user_details','singleflowers','Poojaunits'));
    }

    public function getUserAddresses($userId)
    {
        $addresses = UserAddress::where('user_id', $userId)
            ->where('status', 'active')
            ->get(['id', 'address_type', 'apartment_flat_plot', 'locality', 'landmark', 'city', 'state', 'country', 'pincode', 'default']);

        foreach ($addresses as $address) {
            $address->locality_name = $address->localityDetails->locality_name ?? 'N/A';
        }

        return response()->json(['addresses' => $addresses]);
    }

    public function saveCustomizeOrder(Request $request)
    {
        try {
            // NOTE: Because we submit with Accept: application/json, Laravel will return 422 JSON on validation errors.
            $request->validate([
                'userid'            => 'required|exists:users,userid',
                'address_id'        => 'required|exists:user_addresses,id',
                'date'              => 'required|date',
                'time'              => 'required',
                'flower_name'       => 'required|array|min:1',
                'flower_unit'       => 'required|array|min:1',
                'flower_quantity'   => 'required|array|min:1',
                'flower_name.*'     => 'required|string',
                'flower_unit.*'     => 'required|string',
                'flower_quantity.*' => 'required|numeric|min:0.01',
            ]);

            // Cross-size check
            $totalFlowers = count($request->flower_name);
            if (
                $totalFlowers !== count($request->flower_unit) ||
                $totalFlowers !== count($request->flower_quantity)
            ) {
                return response()->json([
                    'message' => 'Mismatch in the number of flower details provided.',
                ], 400);
            }

            // Generate unique human-readable request id
            $requestId = 'REQ-' . strtoupper(Str::random(12));

            DB::beginTransaction();

            $flowerRequest = FlowerRequest::create([
                'request_id' => $requestId,
                'product_id' => 'FLOW1977630', // TODO: replace if you want a real product link
                'user_id'    => $request->userid,
                'address_id' => $request->address_id,
                'date'       => $request->date,
                'time'       => $request->time,
                'status'     => 'pending',
            ]);

            // If your FlowerRequestItem foreign key references the numeric primary key, use:
            // $parentKey = $flowerRequest->id;
            // and set 'flower_request_id' => $parentKey below.
            // In your current schema it seems to reference the string request_id, so we keep that:
            $parentKey = $requestId;

            foreach ($request->flower_name as $index => $flowerName) {
                FlowerRequestItem::create([
                    'flower_request_id' => $parentKey,
                    'flower_name'       => $flowerName,
                    'flower_unit'       => $request->flower_unit[$index],
                    'flower_quantity'   => $request->flower_quantity[$index],
                ]);
            }

            DB::commit();

            return response()->json([
                'message'     => 'Customize Order added successfully.',
                'request_id'  => $requestId,
                'created_at'  => now()->toDateTimeString(),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in saveCustomizeOrder: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'An error occurred while saving the order.',
                'error'   => app()->hasDebugModeEnabled() ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

      public function reorderCustomizeOrder($id)
    {
        // Load original request (+ items). $id is assumed to be numeric primary key.
        $original = FlowerRequest::with([
            'user',
            'address.localityDetails',
            'flowerRequestItems',
        ])->findOrFail($id);

        // Lookups used by the form
        $user_details = User::get();
        $singleflowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Flower')
            ->orderBy('name')
            ->get();

        $Poojaunits = PoojaUnit::where('status', 'active')
            ->orderBy('unit_name')
            ->get();

        return view('reorder-customize-order', compact(
            'original',
            'user_details',
            'singleflowers',
            'Poojaunits'
        ));
    }

    /**
     * Handle submission of the Re-order form: creates a NEW FlowerRequest with items.
     */
    public function storeReorderCustomizeOrder(Request $request, $id)
    {
        // Find original to lock user (and to optionally validate address belongs to that user).
        $original = FlowerRequest::with('user')->findOrFail($id);

        $request->validate([
            // user is fixed to original (we’ll ignore any spoofed value)
            'address_id'        => 'required|exists:user_addresses,id',
            'date'              => 'required|date',
            'time'              => 'required',
            'flower_name'       => 'required|array|min:1',
            'flower_unit'       => 'required|array|min:1',
            'flower_quantity'   => 'required|array|min:1',
            'flower_name.*'     => 'required|string',
            'flower_unit.*'     => 'required|string',
            'flower_quantity.*' => 'required|numeric|min:0.01',
        ]);

        // Basic array length sanity
        $n = count($request->flower_name);
        if ($n !== count($request->flower_unit) || $n !== count($request->flower_quantity)) {
            return response()->json([
                'message' => 'Mismatch in the number of flower details provided.',
            ], 400);
        }

        // Optional: ensure the chosen address belongs to the SAME user as original
        $addressBelongs = UserAddress::where('id', $request->address_id)
            ->where('user_id', $original->user_id)
            ->exists();

        if (!$addressBelongs) {
            return response()->json([
                'message' => 'The selected address does not belong to the original user.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $newRequestId = 'REQ-' . strtoupper(Str::random(12));

            $new = FlowerRequest::create([
                'request_id' => $newRequestId,
                'product_id' => $original->product_id ?? 'FLOW1977630', // keep or adjust
                'user_id'    => $original->user_id, // locked to original user
                'address_id' => $request->address_id,
                'date'       => $request->date,
                'time'       => $request->time,
                'status'     => 'pending',
            ]);

            // If your FK targets numeric id, use $new->id; otherwise keep request_id
            $parentKey = $newRequestId; // or $new->id

            foreach ($request->flower_name as $i => $flowerName) {
                FlowerRequestItem::create([
                    'flower_request_id' => $parentKey,
                    'flower_name'       => $flowerName,
                    'flower_unit'       => $request->flower_unit[$i],
                    'flower_quantity'   => $request->flower_quantity[$i],
                ]);
            }

            DB::commit();

            return response()->json([
                'message'     => 'Re-order created successfully.',
                'request_id'  => $newRequestId,
                'created_at'  => now()->toDateTimeString(),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in storeReorderCustomizeOrder: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'An error occurred while creating the re-order.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

}
