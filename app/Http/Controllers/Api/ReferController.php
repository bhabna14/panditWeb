<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReferOffer;
use App\Models\ReferOfferClaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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


    }

public function saveOfferClaim(Request $request)
{
    try {
        // 1) Auth user — resolve robustly
        $authUser = Auth::guard('sanctum')->user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }
        $userId = (string) ($authUser->userid ?? $authUser->id ?? $authUser->getKey());
        if ($userId === '') {
            return response()->json(['success' => false, 'message' => 'Unauthorized: User not found.'], 401);
        }

        // 2) offer_id (string ids allowed, e.g. "OFFER5598")
        $offerId = trim((string) $request->input('offer_id', ''));
        if ($offerId === '') {
            return response()->json(['success' => false, 'message' => 'offer_id is required.'], 422);
        }

        // 3) Extract exactly one pair (refer/benefit) from any of the accepted shapes
        [$refer, $benefit] = (function () use ($request) {
            $refer   = $request->input('refer');
            $benefit = $request->input('benefit');

            $parsePipe = function (?string $s): array {
                if ($s === null) return [null, null];
                $parts = explode('|', $s, 2);
                $r = isset($parts[0]) ? trim((string)$parts[0]) : null;
                $b = isset($parts[1]) ? trim((string)$parts[1]) : null;
                return [$r ?: null, $b ?: null];
            };

            if ((!$refer || !$benefit) && $request->filled('selected_pair')) {
                $sp = $request->input('selected_pair');
                if (is_string($sp)) {
                    [$r, $b] = $parsePipe($sp);
                    $refer   = $refer   ?: $r;
                    $benefit = $benefit ?: $b;
                } elseif (is_array($sp)) {
                    $refer   = $refer   ?: ($sp['refer']   ?? null);
                    $benefit = $benefit ?: ($sp['benefit'] ?? null);
                }
            }

            if ((!$refer || !$benefit) && $request->has('selected_pairs')) {
                $sps = $request->input('selected_pairs');
                if (is_string($sps)) {
                    [$r, $b] = $parsePipe($sps);
                    $refer   = $refer   ?: $r;
                    $benefit = $benefit ?: $b;
                } elseif (is_array($sps)) {
                    if (isset($sps['refer']) || isset($sps['benefit'])) {
                        $refer   = $refer   ?: ($sps['refer']   ?? null);
                        $benefit = $benefit ?: ($sps['benefit'] ?? null);
                    } elseif (!empty($sps)) {
                        $first = $sps[0];
                        if (is_string($first)) {
                            [$r, $b] = $parsePipe($first);
                            $refer   = $refer   ?: $r;
                            $benefit = $benefit ?: $b;
                        } elseif (is_array($first)) {
                            $refer   = $refer   ?: ($first['refer']   ?? null);
                            $benefit = $benefit ?: ($first['benefit'] ?? null);
                        }
                    }
                }
            }

            return [$refer ? (string)$refer : null, $benefit ? (string)$benefit : null];
        })();

        if (!$refer || !$benefit) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a single pair via (refer & benefit) or selected_pair / selected_pairs.',
            ], 422);
        }

        // 4) Current datetime (Carbon) – best with your casts
        $dt = Carbon::now(config('app.timezone'));

        // 5) Single pair payload
        $pairPayload = [[ 'refer' => (string)$refer, 'benefit' => (string)$benefit ]];

        // 6) Upsert and FORCE the values; use saveOrFail to catch DB issues
        $claim = DB::transaction(function () use ($userId, $offerId, $pairPayload, $dt) {
            /** @var \App\Models\ReferOfferClaim $row */
            $row = ReferOfferClaim::firstOrNew(['user_id' => $userId, 'offer_id' => $offerId]);

            // force values (so user_id / offer_id cannot end up null)
            $row->user_id        = $userId;
            $row->offer_id       = $offerId;
            $row->selected_pairs = $pairPayload;
            $row->date_time      = $dt;
            $row->status         = 'claimed';

            $row->saveOrFail();   // <-- if DB rejects, we see the exception
            return $row->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => $claim->wasRecentlyCreated
                ? 'Offer claim created successfully.'
                : 'Offer claim updated successfully.',
            'data' => ['claim' => $claim],
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