<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReferOffer;
use App\Models\ReferOfferClaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

 public function saveOfferClaim(Request $request)
    {
        // Validate (return JSON 422 on errors)
        $validator = Validator::make($request->all(), [
            'user_id'          => 'required|exists:users,userid',
            'offer_id'         => 'required|exists:flower__refer_offer,id',
            'claim_datetime'   => 'required|date_format:Y-m-d\TH:i',
            'selected_pairs'   => 'nullable|array',
            'selected_pairs.*' => 'string', // each: "idx|refer|benefit"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            // Parse checkbox selections into structured array
            $parsedSelections = collect($request->input('selected_pairs', []))
                ->map(function ($v) {
                    [$i, $r, $b] = array_pad(explode('|', $v, 3), 3, null);
                    return [
                        'index'   => is_numeric($i) ? (int) $i : null,
                        'refer'   => $r,
                        'benefit' => $b,
                    ];
                })
                ->filter(fn ($row) => $row['refer'] !== null && $row['benefit'] !== null)
                ->values()
                ->all();

            // Sanity check against the offer’s arrays (only if there are selections)
            if (!empty($parsedSelections)) {
                $offer = ReferOffer::findOrFail($validated['offer_id']);
                $maxIndex = min(count($offer->no_of_refer ?? []), count($offer->benefit ?? [])) - 1;

                foreach ($parsedSelections as $sel) {
                    if ($sel['index'] === null || $sel['index'] < 0 || $sel['index'] > $maxIndex) {
                        return response()->json([
                            'success' => false,
                            'message' => 'One or more selected benefit options are invalid.',
                            'errors'  => ['selected_pairs' => ['Invalid selection indices.']],
                        ], 422);
                    }
                }
            }

            // Convert HTML datetime-local to "Y-m-d H:i:s"
            $dt = Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $validated['claim_datetime'],
                config('app.timezone')
            )->toDateTimeString();

            // Upsert by (user_id, offer_id)
            $claim = DB::transaction(function () use ($validated, $parsedSelections, $dt) {
                return ReferOfferClaim::updateOrCreate(
                    [
                        'user_id'  => $validated['user_id'],
                        'offer_id' => $validated['offer_id'],
                    ],
                    [
                        'selected_pairs' => $parsedSelections, // model cast to JSON
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
                'data'    => [
                    'claim' => $claim,
                ],
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