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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

        $Poojaunits = PoojaUnit::where('status', 'active')->get();

        return view('create-customize-order', compact('flowers','user_details','singleflowers','Poojaunits'));
    }

    public function reorderCustomizeOrder($id)
    {
        $user_details = User::get();

        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->get();

        $singleflowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Flower')
            ->get();

        $Poojaunits = PoojaUnit::where('status', 'active')->get();

        $prefill = FlowerRequest::with(['flowerRequestItems', 'user', 'address.localityDetails'])
            ->findOrFail($id);

        $mode = 'reorder';

        return view('create-customize-order', compact('flowers','user_details','singleflowers','Poojaunits','prefill','mode'));
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

            // Your schema uses string request_id as the FK in items:
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
            Log::error('Error in saveCustomizeOrder: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'An error occurred while saving the order.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }
}
