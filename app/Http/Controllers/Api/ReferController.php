<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReferOffer;
use App\Models\ReferOfferClaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;


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


}public function saveOfferClaim(Request $request)
{
    try {
        // Read raw inputs (no Validator)
        $userId        = $request->input('user_id');
        $offerId       = $request->input('offer_id');
        $claimDatetime = $request->input('claim_datetime'); // expected "Y-m-d\TH:i"
        $pairsRaw      = $request->input('selected_pairs', []); // ["idx|refer|benefit", ...]

        // Parse datetime; fallback to now if parsing fails or missing
        try {
            $dt = $claimDatetime
                ? Carbon::createFromFormat('Y-m-d\TH:i', $claimDatetime, config('app.timezone'))->toDateTimeString()
                : Carbon::now(config('app.timezone'))->toDateTimeString();
        } catch (\Throwable $e) {
            $dt = Carbon::now(config('app.timezone'))->toDateTimeString();
        }

        // Parse pairs -> structured array (skip obviously broken rows)
        $parsedSelections = collect(is_array($pairsRaw) ? $pairsRaw : [])
            ->map(function ($v) {
                [$i, $r, $b] = array_pad(explode('|', (string) $v, 3), 3, null);
                return [
                    'index'   => is_numeric($i) ? (int) $i : null,
                    'refer'   => $r,
                    'benefit' => $b,
                ];
            })
            ->filter(fn ($row) => $row['refer'] !== null && $row['benefit'] !== null)
            ->values();

        // Soft sanity check against offer arrays (NON-blocking; we just drop invalid indices)
        $offer = ReferOffer::find($offerId);
        if ($offer && is_array($offer->no_of_refer) && is_array($offer->benefit)) {
            $maxIndex = min(count($offer->no_of_refer), count($offer->benefit)) - 1;
            if ($maxIndex >= 0) {
                $parsedSelections = $parsedSelections->filter(function ($row) use ($maxIndex) {
                    return $row['index'] !== null && $row['index'] >= 0 && $row['index'] <= $maxIndex;
                })->values();
            }
        }

        // Upsert by (user_id, offer_id)
        $claim = DB::transaction(function () use ($userId, $offerId, $parsedSelections, $dt) {
            return ReferOfferClaim::updateOrCreate(
                ['user_id' => $userId, 'offer_id' => $offerId],
                [
                    'selected_pairs' => $parsedSelections->all(), // cast -> JSON
                    'date_time'      => $dt,
                    'status'         => 'claimed',
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => $claim->wasRecentlyCreated
                ? 'Offer claim created successfully.'
                : 'Offer claim updated successfully.',
            'data'    => ['claim' => $claim],
        ], 200);

    } catch (\Throwable $e) {
        Log::error('API saveOfferClaim failed', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => app()->environment('local')
                ? 'Failed to save offer claim: ' . $e->getMessage()
                : 'Failed to save offer claim.',
        ], 500);
    }
}


}