<?php

namespace App\Http\Controllers\Refer;

use App\Http\Controllers\Controller;
use App\Models\ReferOffer;
use App\Models\ReferOfferClaim;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferController extends Controller
{
    public function offerCreate()
    {
        return view('refer.offer-create');
    }

    public function saveReferOffer(Request $request)
    {
        $validated = $request->validate([
            'offer_name'    => 'required|string|max:255',
            'description'   => 'required|string|max:2000',
            'no_of_refer'   => 'required|array|min:1',
            'no_of_refer.*' => 'nullable|integer|min:1',
            'benefit'       => 'required|array|min:1',
            'benefit.*'     => 'nullable|string|max:255',
        ]);

        $referArray   = [];
        $benefitArray = [];

        $count = max(count($validated['no_of_refer']), count($validated['benefit']));
        for ($i = 0; $i < $count; $i++) {
            $refer   = $validated['no_of_refer'][$i] ?? null;
            $benefit = isset($validated['benefit'][$i]) ? trim($validated['benefit'][$i]) : null;

            // Skip cloned/empty rows
            if (empty($refer) && ($benefit === null || $benefit === '')) {
                continue;
            }

            // Require both values per row
            if (empty($refer) || $benefit === null || $benefit === '') {
                return back()
                    ->withInput()
                    ->withErrors(['benefit' => 'Each Refer & Benefit row must have both values.']);
            }

            $referArray[]   = (int) $refer;
            $benefitArray[] = $benefit;
        }

        if (empty($referArray)) {
            return back()
                ->withInput()
                ->withErrors(['no_of_refer' => 'Please add at least one valid Refer & Benefit row.']);
        }

        DB::transaction(function () use ($request, $validated, $referArray, $benefitArray) {
            ReferOffer::create([
                'offer_id'    => 'OFFER' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT),
                'offer_name'  => $validated['offer_name'],
                'description' => $validated['description'],
                'no_of_refer' => $referArray,
                'benefit'     => $benefitArray,
            ]);
        });

        return redirect()
            ->route('refer.offerCreate')
            ->with('success', 'Refer offer saved successfully!');
    }

    public function manageReferOffer()
    {
        $offers = ReferOffer::where('status','active')->get();

        return view('refer.manage-offer', compact('offers'));
    }

    public function update(Request $request, ReferOffer $offer)
    {
        $validated = $request->validate([
            'offer_name'    => 'required|string|max:255',
            'description'   => 'required|string|max:2000',
            'status'        => 'required|in:active,inactive',
            'no_of_refer'   => 'required|array|min:1',
            'no_of_refer.*' => 'nullable|integer|min:1',
            'benefit'       => 'required|array|min:1',
            'benefit.*'     => 'nullable|string|max:255',
        ]);

        $referArray = [];
        $benefitArray = [];
        $count = max(count($validated['no_of_refer']), count($validated['benefit']));
        for ($i = 0; $i < $count; $i++) {
            $r = $validated['no_of_refer'][$i] ?? null;
            $b = isset($validated['benefit'][$i]) ? trim($validated['benefit'][$i]) : null;
            if (empty($r) && ($b === null || $b === '')) continue;
            if (empty($r) || $b === null || $b === '') {
                return back()->withInput()->withErrors(['benefit' => 'Each Refer & Benefit row must have both values.']);
            }
            $referArray[] = (int)$r;
            $benefitArray[] = $b;
        }
        if (empty($referArray)) {
            return back()->withInput()->withErrors(['no_of_refer' => 'Please add at least one valid Refer & Benefit row.']);
        }

        $offer->update([
            'offer_name'  => $validated['offer_name'],
            'description' => $validated['description'],
            'status'      => $validated['status'],
            'no_of_refer' => $referArray,
            'benefit'     => $benefitArray,
        ]);

        return redirect()->route('refer.manageReferOffer')->with('success', 'Offer updated successfully.');
    }

    public function destroy(ReferOffer $offer)
    {
        $offer->delete();
        return redirect()->route('refer.manageReferOffer')->with('success', 'Offer deleted successfully.');
    }

    public function offerClaim()
    {
        $users = User::select('userid','name','mobile_number')->orderBy('name')->get();
        $offers = ReferOffer::where('status','active')->get();

        return view('refer.offer-claim', compact('users', 'offers'));
    }

    public function saveOfferClaim(Request $request)
    {
        // Validate first so field-level errors show properly
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,userid',
            'offer_id'         => 'required|exists:flower__refer_offer,offer_id',
            'claim_datetime'   => 'required|date_format:Y-m-d\TH:i',
            'selected_pairs'   => 'nullable|array',
            'selected_pairs.*' => 'string',
        ]);

        try {
            // Parse & sanity-check (unchanged)
            $parsedSelections = collect($request->input('selected_pairs', []))
                ->map(function ($v) {
                    [$i, $r, $b] = array_pad(explode('|', $v, 3), 3, null);
                    return ['index' => is_numeric($i) ? (int)$i : null, 'refer' => $r, 'benefit' => $b];
                })
                ->filter(fn ($row) => $row['refer'] !== null && $row['benefit'] !== null)
                ->values()
                ->all();

            if (!empty($parsedSelections)) {
                $offer = ReferOffer::findOrFail($validated['offer_id']);
                $maxIndex = min(count($offer->no_of_refer ?? []), count($offer->benefit ?? [])) - 1;
                foreach ($parsedSelections as $sel) {
                    if ($sel['index'] === null || $sel['index'] < 0 || $sel['index'] > $maxIndex) {
                        return back()->withInput()->withErrors([
                            'selected_pairs' => 'One or more selected benefit options are invalid.',
                        ]);
                    }
                }
            }

            $dt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['claim_datetime'], config('app.timezone'))
                        ->toDateTimeString();

            $existing = ReferOfferClaim::where('user_id', $validated['user_id'])
                ->where('offer_id', $validated['offer_id'])
                ->first();

            if ($existing) {
                $existing->update([
                    'selected_pairs' => $parsedSelections,
                    'date_time'      => $dt,
                    'status'         => 'claimed',
                ]);
                return redirect()->back()->with('success', 'Offer claim updated successfully!');
            }

            DB::transaction(function () use ($validated, $parsedSelections, $dt) {
                ReferOfferClaim::create([
                    'user_id'        => $validated['user_id'],
                    'offer_id'       => $validated['offer_id'],
                    'selected_pairs' => $parsedSelections,
                    'date_time'      => $dt,
                    'status'         => 'claimed',
                ]);
            });

            return redirect()->back()->with('success', 'Offer claim saved successfully!');
        } catch (\Throwable $e) {

            Log::error('saveOfferClaim failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // IMPORTANT: do NOT add a generic withErrors() here.
            // Put a clear session error and a detailed error only in local.
            return back()
                ->withInput()
                ->with('error', 'Failed to save offer claim.')
                ->with('error_detail', app()->environment('local') ? $e->getMessage() : null);
        }
    }

    public function manageOfferClaim(Request $request)
    {
        // Optional filter: ?status=claimed|approved|rejected|all (default: claimed)
        $status = $request->query('status','claimed');

        $query = ReferOfferClaim::with(['user:id,userid,name,mobile_number', 'offer:id,offer_name'])->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $claimedOffer = $query->get();

        return view('refer.manage-offer-claim', compact('claimedOffer', 'status'));
    }

    public function updateClaimStatus(Request $request, ReferOfferClaim $claim)
    {
        $request->validate([
            'status' => 'required|in:claimed,approved,rejected',
        ]);

        try {
            $claim->update(['status' => $request->status]);

            return redirect()
                ->back()
                ->with('success', 'Claim status updated to ' . ucfirst($request->status) . '.');

        } catch (\Throwable $e) {
            Log::error('updateClaimStatus failed', [
                'id'      => $claim->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update claim status.');
        }
    }

    public function destroyClaim(ReferOfferClaim $claim)
    {
        try {
            $claim->delete();

            return redirect()
                ->back()
                ->with('success', 'Claim deleted successfully.');

        } catch (\Throwable $e) {
            Log::error('destroyClaim failed', [
                'id'      => $claim->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete claim.');
        }
    }


    public function startApprovalCode(ReferOfferClaim $claim)
{
    try {
        if (strtolower((string)$claim->status) === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'This claim is already approved.'
            ], 400);
        }

        // Generate a 6-digit numeric code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store on the claim (optionally you could store a generated_at timestamp too)
        $claim->update(['code' => $code]);

        // Return the code so the admin must re-enter it to confirm
        return response()->json([
            'success' => true,
            'message' => 'Approval code generated. Please enter it to confirm.',
            'code'    => $code,
            'claim_id'=> $claim->id,
        ], 200);

    } catch (\Throwable $e) {
        Log::error('startApprovalCode failed', ['id' => $claim->id, 'msg' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Server error while starting approval.',
        ], 500);
    }
}

public function verifyApprovalCode(Request $request, ReferOfferClaim $claim)
{
    $request->validate([
        'code' => 'required|digits:6',
    ]);

    try {
        // Must have a code generated
        if (empty($claim->code)) {
            return response()->json([
                'success' => false,
                'message' => 'No approval code generated for this claim. Please start again.',
            ], 409);
        }

        // Compare
        if ((string) $claim->code !== (string) $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code.',
            ], 422);
        }

        // Approve and clear the code
        $claim->update([
            'status' => 'approved',
            'code'   => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Claim approved successfully.',
            'data'    => ['claim_id' => $claim->id, 'status' => $claim->status],
        ], 200);

    } catch (\Throwable $e) {
        Log::error('verifyApprovalCode failed', ['id' => $claim->id, 'msg' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Server error while verifying the code.',
        ], 500);
    }
}


}
