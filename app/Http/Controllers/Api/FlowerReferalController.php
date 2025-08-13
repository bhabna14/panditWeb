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

public function stats(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    $userId = $authUser->userid; // e.g. "USER65632"
    $onlyActiveReferralRows = $request->boolean('only_active_referral_rows');

    // ---------- AS REFERRER ----------
    // COMPLETED = referred users who have an active subscription
    $completedList = DB::table('flower_referrals as fr')
        ->join('users as u', 'u.userid', '=', 'fr.user_id')
        ->join('subscriptions as s', 's.user_id', '=', 'u.userid')
        ->where('fr.referrer_user_id', $userId)
        ->when($onlyActiveReferralRows, fn ($q) => $q->where('fr.status', 'active'))
        ->where(function ($q) {
            $q->where('s.status', 'active')
              ->orWhere('s.is_active', 1);
        })
        ->select('u.userid as id', 'u.name', 'u.mobile_number')
        ->distinct()
        ->get();

    // USED (PENDING) = referred users WITHOUT an active subscription
    $usedPendingList = DB::table('flower_referrals as fr')
        ->join('users as u', 'u.userid', '=', 'fr.user_id')
        ->where('fr.referrer_user_id', $userId)
        ->when($onlyActiveReferralRows, fn ($q) => $q->where('fr.status', 'active'))
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('subscriptions as s')
              ->whereColumn('s.user_id', 'fr.user_id')
              ->where(function ($s) {
                  $s->where('s.status', 'active')
                    ->orWhere('s.is_active', 1);
              });
        })
        ->select('u.userid as id', 'u.name', 'u.mobile_number')
        ->distinct()
        ->get();

    // ---------- REFERRED BY (who claims to have referred ME) ----------
    $myReferrers = DB::table('flower_referrals as fr')
        ->join('users as r', 'r.userid', '=', 'fr.referrer_user_id')
        ->where('fr.user_id', $userId)
        ->select(
            'r.userid as id',
            'r.name',
            'r.mobile_number',
            'fr.status',
            'fr.created_at'
        )
        ->orderBy('fr.created_at', 'desc')
        ->get();

    $myReferrersCompleted = DB::table('flower_referrals as fr')
        ->join('users as r', 'r.userid', '=', 'fr.referrer_user_id')
        ->join('subscriptions as s', 's.user_id', '=', 'r.userid')
        ->where('fr.user_id', $userId)
        ->where(function ($q) {
            $q->where('s.status', 'active');
        })
        ->select('r.userid as id', 'r.name', 'r.mobile_number')
        ->distinct()
        ->get();

    return response()->json([
        'success' => true,
        'data' => [
           
            'referred_by' => [
                'referrers_count'           => $myReferrers->count(),
                'referrers_list'            => $myReferrers,
                'referrers_completed_count' => $myReferrersCompleted->count(),
                'referrers_completed_list'  => $myReferrersCompleted,
            ],
        ],
    ], 200);
}


}
