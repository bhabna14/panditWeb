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
    public function demoCustomizeOrder()
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
    
    
        return view('demo-customize-order', compact('flowers','user_details','singleflowers','Poojaunits'));
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
        // Validate the incoming request data
        $request->validate([
            'userid' => 'required|exists:users,userid',
            'address_id' => 'required|exists:user_addresses,id',
            'date' => 'required|date',
            'time' => 'required',
            'flower_name' => 'required|array|min:1',
            'flower_unit' => 'required|array|min:1',
            'flower_quantity' => 'required|array|min:1',
            'flower_name.*' => 'required|string',
            'flower_unit.*' => 'required|string',
            'flower_quantity.*' => 'required|numeric',
        ]);

        // Check if the input arrays have the same number of items
        $totalFlowers = count($request->flower_name);
        if (
            $totalFlowers !== count($request->flower_unit) ||
            $totalFlowers !== count($request->flower_quantity)
        ) {
            return response()->json([
                'message' => 'Mismatch in the number of flower details provided.',
            ], 400);
        }

        // Generate a unique request ID
        $requestId = 'REQ-' . strtoupper(Str::random(12));

        // Create the main flower request record
        $flowerRequest = FlowerRequest::create([
            'request_id' => $requestId,
            'product_id' => 'FLOW1977630', // Placeholder product ID, change as needed
            'user_id' => $request->userid,
            'address_id' => $request->address_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        // Iterate through the flower details and create associated records
        foreach ($request->flower_name as $index => $flowerName) {
            FlowerRequestItem::create([
                'flower_request_id' => $flowerRequest->id, // Use the ID from the created FlowerRequest
                'flower_name' => $flowerName,
                'flower_unit' => $request->flower_unit[$index],
                'flower_quantity' => $request->flower_quantity[$index],
            ]);
        }

        // Return success response
        return response()->json([
            'message' => 'Customize Order added successfully.',
        ], 200);
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error('Error in saveCustomizeOrder: ' . $e->getMessage());

        // Return error response
        return response()->json([
            'message' => 'An error occurred while saving the order.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
