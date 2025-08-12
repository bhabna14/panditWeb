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
    $referrer = Auth::user();

    dd($referrer->userid);
    if (!$referrer) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    $includeUsers = $request->boolean('include_users', false);
    $limit        = (int) $request->query('limit', 50);

    $refKeyNum = (string) $referrer->id;
    $refKeyStr = (string) ($referrer->userid ?? '');

    // Find referrals where THIS user is the referrer (match both formats)
    $referrals = FLowerReferal::where(function ($q) use ($refKeyNum, $refKeyStr) {
            $q->where('referrer_user_id', $refKeyNum);
            if ($refKeyStr !== '') {
                $q->orWhere('referrer_user_id', $refKeyStr);
            }
        })
        ->get();

    // Unique raw referred IDs from the referral table (could be numeric or "USERxxxxx")
    $rawReferredIds = $referrals->pluck('user_id')->unique()->values();

    // Split into numeric vs string "USERxxxxx"
    $numericLike = $rawReferredIds->filter(fn ($v) => ctype_digit((string) $v))
                                  ->map(fn ($v) => (int) $v)
                                  ->values();

    $stringLike  = $rawReferredIds->filter(fn ($v) => !ctype_digit((string) $v))
                                  ->values();

    // Map string "USERxxxxx" -> numeric users.id
    $numericFromStrings = $stringLike->isNotEmpty()
        ? User::whereIn('userid', $stringLike)->pluck('id')
        : collect();

    // Final set of numeric user IDs for referred users
    $referredNumericIds = $numericLike->merge($numericFromStrings)->unique()->values();

    // Count used users
    $usedCount = $referredNumericIds->count();

    // Completed users = have an active subscription (tweak logic as you need)
    $completedUserIds = $referredNumericIds->isNotEmpty()
        ? Subscription::whereIn('user_id', $referredNumericIds)
            ->where(function ($q) {
                $q->orWhere(function ($q2) {
                $q2->whereNotNull('end_date')
                ->whereDate('end_date', '>=', now()->toDateString());
                });
            })
            ->distinct()
            ->pluck('user_id')
        : collect();

    $completedCount = $completedUserIds->count();

    $response = [
        'success' => true,
        'data' => [
            'used_users'      => $usedCount,
            'completed_users' => $completedCount,
        ],
    ];

    if ($includeUsers) {
        $usedUsers = $referredNumericIds->isNotEmpty()
            ? User::whereIn('id', $referredNumericIds)
                ->select('id', 'userid', 'name', 'email', 'mobile_number')
                ->limit($limit)
                ->get()
            : collect();

        $completedUsers = $completedUserIds->isNotEmpty()
            ? User::whereIn('id', $completedUserIds)
                ->select('id', 'userid', 'name', 'email', 'mobile_number')
                ->limit($limit)
                ->get()
            : collect();

        $response['data']['used_users_list']      = $usedUsers;
        $response['data']['completed_users_list'] = $completedUsers;
    }

    return response()->json($response, 200);
}


}
