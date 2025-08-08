<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionDetails;

class PromotionControllerApi extends Controller
{
    public function managePromotion()
    {
        try {
            $promotion = PromotionDetails::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();
           
            return response()->json([
                'success' => true,
                'message' => 'Promotion managed successfully',
                'data' => $promotion
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error managing promotion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
