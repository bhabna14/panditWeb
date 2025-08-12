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
    /**
     * POST /referrals/claim
     * Body: { "referral_code": "ABC1234" }
     */
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

        // Optional: allow claims only for very new accounts (e.g., 7 days)
        // if ($referred->created_at && $referred->created_at->diffInDays(now()) > 7) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Referral code can only be claimed within 7 days of signup.',
        //     ], 422);
        // }

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
                    'referrer_user_id'     => $referrer->id,
                    'referred_user_id'     => $referred->id,
                    'subscription_user_id' => null, // will fill if we find/apply a subscription
                    'status'               => 'CLAIMED',
                ]);

                // Try to grant 1 free month to referrer by extending their subscription.
                // If you have a Subscription model/table, do it here. If not, we still mark EARNED.
                $applied = false;
                $nextRenewal = null;
                $subscriptionId = null;

                if (class_exists(\App\Models\Subscription::class)) {
                    /** @var \App\Models\Subscription|null $sub */
                    $sub = \App\Models\Subscription::where('user_id', $referrer->id)
                        ->where('status', 'active')
                        ->first();

                    if ($sub) {
                        $current = $sub->next_renewal_at
                            ? Carbon::parse($sub->next_renewal_at)
                            : Carbon::now();

                        // addMonthNoOverflow handles end-of-month nicely
                        $sub->next_renewal_at = $current->copy()->addMonthNoOverflow();
                        $sub->save();

                        $applied = true;
                        $nextRenewal = $sub->next_renewal_at;
                        $subscriptionId = $sub->id;
                    }
                }

                // Update referral record to EARNED and optionally set subscription reference
                $ref->status = 'EARNED';
                if ($subscriptionId) {
                    $ref->subscription_user_id = $subscriptionId;
                }
                $ref->save();

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
