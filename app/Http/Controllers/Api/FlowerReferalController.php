<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FLowerReferal; // your model name as given
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;
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

        // Idempotent: if this user already claimed, return the existing record
        $existing = FLowerReferal::where('referrer_user_id', $referred->userid)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Referral already claimed.',
                'data'    => $existing,
            ], 200);
        }

        try {
            $ref = DB::transaction(function () use ($referrer, $referred) {
                return FLowerReferal::create([
                    'user_id'             => $referred->userid, // use numeric PK
                    'referrer_user_id'     => $referrer->userid,   // use numeric PK
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Referral claimed successfully',
                'data' => [
                    'referrer'    => $referrer->only(['id', 'name', 'email', 'mobile_number']),
                    'referred'    => $referred->only(['id', 'name', 'email', 'mobile_number']),
                    'referral_id' => $ref->id,
                    'referral'    => $ref,
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

public function stats()
    {
       $userid =  Auth::user()->userid; // Assuming userid is the primary key

        // 1) Times THIS user used someone else’s code (+ who referred them)
        $usedRows = FlowerReferral::with(['referrer:id,name,mobile_number'])
            ->where('user_id', $userid)
            ->get();

        $usedReferral = [
            'count' => $usedRows->count(),
            'rows'  => $usedRows->map(function ($r) {
                return [
                    'referrer_id'             => $r->referrer_user_id,
                    'referrer_name'           => optional($r->referrer)->name,
                    'referrer_mobile_number'  => optional($r->referrer)->mobile_number,
                    'status'                  => $r->status,
                ];
            })->values(),
        ];

        // 2) Users who used MY referral code (name + mobile)
        // NOTE: change 'users.id' to 'users.userid' if that's your PK.
        $myReferredUsers = FlowerReferral::select(
                'users.id as user_id',
                'users.name',
                'users.mobile_number'
            )
            ->join('users', 'flower_referrals.user_id', '=', 'users.id')
            ->where('flower_referrals.referrer_user_id', $userid)
            ->get();

        $myReferrals = [
            'count' => $myReferredUsers->count(),
            'users' => $myReferredUsers->values(),
        ];

        // 3) Of those referred users, who completed a subscription? (+ their data)
        // Define what "completed" means. Here we treat status 'completed' OR is_active = 1 as completed.
        $completedReferredUsers = Subscription::select(
                'users.id as user_id',
                'users.name',
                'users.mobile_number'
            )
            ->join('users', 'subscriptions.user_id', '=', 'users.id')
            ->join('flower_referrals', 'flower_referrals.user_id', '=', 'subscriptions.user_id')
            ->where('flower_referrals.referrer_user_id', $userid)
            ->where(function ($q) {
                $q->where('subscriptions.status', 'active');
            })
            ->distinct()
            ->get();

        $completed = [
            'count' => $completedReferredUsers->count(),
            'users' => $completedReferredUsers->values(),
        ];

        return response()->json([
            'used_referral'             => $usedReferral,
            'my_referrals'              => $myReferrals,
            'completed_referred_users'  => $completed,
        ]);
    }
}
