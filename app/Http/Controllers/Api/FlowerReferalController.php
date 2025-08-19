<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FLowerReferal;
use App\Models\User;
use App\Models\Subscription;
use App\Models\ReferOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Prevent claiming your own code
        if ($referrer->userid === $referred->userid) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot claim your own referral code.',
            ], 422);
        }

        // Idempotent check: has THIS user already claimed a referral?
        // (Fix: check by fr.user_id == current user)
        $existing = FLowerReferal::where('user_id', $referred->userid)->first();
        if ($existing) {
            // Ensure code_status is "yes" even if it wasn’t set previously
            if (strtolower((string) $referred->code_status) !== 'yes') {
                User::where('userid', $referred->userid)->update(['code_status' => 'yes']);
                $referred->code_status = 'yes';
            }

            return response()->json([
                'success' => true,
                'message' => 'Referral already claimed.',
                'data' => [
                    'referral'    => $existing,
                    'referrer'    => $referrer->only(['id', 'name', 'email', 'mobile_number']),
                    'referred'    => $referred->only(['id', 'name', 'email', 'mobile_number']) + ['code_status' => $referred->code_status],
                ],
            ], 200);
        }

        try {
            $ref = DB::transaction(function () use ($referrer, $referred) {
                // Create referral row
                $ref = FLowerReferal::create([
                    'user_id'           => $referred->userid,     // the claimer
                    'referrer_user_id'  => $referrer->userid,     // who owns the code
                    'status'            => 'claimed',
                    'code_status'       => 'yes',

                ]);

                // Mark this user as having used a referral code
                User::where('userid', $referred->userid)->update(['code_status' => 'yes']);

                return $ref;
            });

            // Refresh in-memory value for response
            $referred->code_status = 'yes';

            return response()->json([
                'success' => true,
                'message' => 'Referral claimed successfully',
                'data' => [
                    'referrer'    => $referrer->only(['id', 'name', 'email', 'mobile_number', 'code_status']),
                    'referred'    => $referred->only(['id', 'name', 'email', 'mobile_number']) + ['code_status' => $referred->code_status],
                    'referral_id' => $ref->id,
                    'referral'    => $ref,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to claim referral.',
                'error'   => app()->environment('local') ? $e->getMessage() : null,
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
                $q->where('s.status', 'active');
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
                    $s->where('s.status', 'active');
                });
            })
            ->select('u.userid as id', 'u.name', 'u.mobile_number')
            ->distinct()
            ->get();

        // COMPLETED referrers = people who referred me AND have an active subscription
        $myReferrersCompleted = DB::table('flower_referrals as fr')
            ->join('users as r', 'r.userid', '=', 'fr.referrer_user_id')
            ->join('subscriptions as s', 's.user_id', '=', 'r.userid')
            ->where('fr.user_id', $userId)
            ->where(function ($q) {
                $q->where('s.status', 'active')
                ->orWhere('s.is_active', 1);
            })
            ->select('r.userid as id', 'r.name', 'r.mobile_number', 'fr.created_at')
            ->distinct()
            ->get();

        // PENDING referrers = people who referred me WITHOUT an active subscription
        $myReferrersPending = DB::table('flower_referrals as fr')
            ->join('users as r', 'r.userid', '=', 'fr.referrer_user_id')
            ->where('fr.user_id', $userId)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('subscriptions as s')
                ->whereColumn('s.user_id', 'fr.referrer_user_id')
                ->where(function ($s) {
                    $s->where('s.status', 'active')
                    ->orWhere('s.is_active', 1);
                });
            })
            ->select('r.userid as id', 'r.name', 'r.mobile_number', 'fr.status', 'fr.created_at')
            ->orderBy('fr.created_at', 'desc')
            ->distinct()
            ->get();

        // ---------- FINAL DE-DUP SAFETY NET ----------
        $completedIds     = $completedList->pluck('id')->all();
        $usedPendingList  = $usedPendingList->reject(fn ($row) => in_array($row->id, $completedIds))->values();

        $myRefCompletedIds  = $myReferrersCompleted->pluck('id')->all();
        $myReferrersPending = $myReferrersPending->reject(fn ($row) => in_array($row->id, $myRefCompletedIds))->values();

        // ---------- OFFER DETAILS (single object) ----------
        // Optional filter: ?status=active|inactive|all  (default: active)
        $status = $request->query('status', 'active');

        $query = ReferOffer::query();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get the latest offer. Select both id and offer_id to be safe across schemas.
        $offerRecord = $query->orderByDesc('created_at')
            ->first(['id', 'offer_id', 'offer_name', 'description', 'no_of_refer', 'benefit', 'status', 'created_at', 'updated_at']);

        // Shape payload as a single object with key "id" (fallback to offer_id if no numeric id)
        $offerDetails = null;
        if ($offerRecord) {
            $offerDetails = [
                'id'          => $offerRecord->id ?? $offerRecord->offer_id, // always expose as "id"
                'offer_name'  => $offerRecord->offer_name,
                'description' => $offerRecord->description,
                'no_of_refer' => is_array($offerRecord->no_of_refer) ? array_values($offerRecord->no_of_refer) : [],
                'benefit'     => is_array($offerRecord->benefit) ? array_values($offerRecord->benefit) : [],
                'status'      => $offerRecord->status,
                'created_at'  => $offerRecord->created_at, // will serialize to ISO8601
                'updated_at'  => $offerRecord->updated_at,
            ];
        }

        return response()->json([
            'success'     => true,
            'refer_data'  => [
                'referred_by' => [
                    'referrers_count'           => $myReferrersPending->count(),
                    'referrers_list'            => $myReferrersPending,
                    'referrers_completed_count' => $myReferrersCompleted->count(),
                    'referrers_completed_list'  => $myReferrersCompleted,
                ],
            ],
            // ✅ Single object, not an array
            'offer_details' => $offerDetails,
        ], 200);
    }

}
