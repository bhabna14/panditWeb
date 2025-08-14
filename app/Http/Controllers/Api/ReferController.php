<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferController extends Controller
{
  
public function manageReferOffer(Request $request)
{
    try {
        // Optional filter: ?status=active|inactive|all  (default: active)
        $status = $request->query('status', 'active');

        $query = ReferOffer::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $offers = $query
            ->orderByDesc('created_at')
            ->get(['id','offer_name','description','no_of_refer','benefit','status','created_at','updated_at']);

        return response()->json([
            'success' => true,
            'data'    => [
                'offers' => $offers,
            ],
        ], 200);

    } catch (\Throwable $e) {
        Log::error('manageReferOffer failed', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch refer offers.',
        ], 500);
    }
}


}