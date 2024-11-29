<?php

namespace App\Http\Controllers\RiderApi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowerPickupDetails;

class OrderController extends Controller
{
    public function getAssignOrders()
    {
        // dd('g');
        try {
            $rider = Auth::guard('rider')->user();
            dd($rider->rider_id);
            if (!$rider) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $orders = FlowerPickupDetails::with(['flower', 'unit', 'vendor'])
                ->where('rider_id', $rider->rider_id)
                ->orderBy('pickup_date', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Assigned orders fetched successfully',
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
