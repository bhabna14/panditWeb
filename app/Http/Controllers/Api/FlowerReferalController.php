<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FLowerReferal; // your model name as given
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlowerReferalController extends Controller
{
  
    public function claim(Request $request)
    {
        $data = $request->validate([
            'referral_code' => 'required|string|max:32',
        ]);

        $referred = Auth::user(); // current logged-in user
        if (!$referred) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $code = strtoupper(trim($data['referral_code']));

        // Find referrer by code
        $referrer = User::where('referral_code', $code)->first();
        if (!$referrer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid referral code.',
            ], 422);
        }

        // Block self-referral
        if ((int)$referrer->id === (int)$referred->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot use your own referral code.',
            ], 422);
        }

     
        // Idempotent: if this user already claimed, return the existing record
        $existing = FLowerReferal::where('referred_user_id', $referred->id)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Referral already claimed.',
                'data'    => $existing,
            ]);
        }

        try {
            $result = DB::transaction(function () use ($referrer, $referred) {
                // Create the referral record
                $ref = FLowerReferal::create([
                    'referrer_user_id'     => $referrer->userid,
                    'referred_user_id'     => $referred->userid,
                    'subscription_user_id' => null, // will fill if we find/apply a subscription
                ]);

                return [
                    'ref'          => $ref,
                    'applied'      => $applied,
                    'nextRenewal'  => $nextRenewal ? Carbon::parse($nextRenewal)->toDateTimeString() : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => $result['applied']
                    ? 'Referral applied: 1 free month granted to your referrer.'
                    : 'Referral recorded. Free month will be applied when your referrer has an active subscription.',
                'data' => [
                    'referral'          => $result['ref'],
                    'reward_applied'    => $result['applied'],
                    'referrer_next_renewal_at' => $result['nextRenewal'],
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to claim referral.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
