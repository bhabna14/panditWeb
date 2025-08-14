<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferController extends Controller
{
   public function index()
    {
        try {
            
            $offers = ReferOffer::orderByDesc('created_at')
                ->get(['id','offer_name','description','no_of_refer','benefit','status','created_at','updated_at']);

            return response()->json([
                'success' => true,
                'data'    => [
                    'offers' => $offers,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to list refer offers', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve refer offers.',
            ], 500);
        }
    }

}