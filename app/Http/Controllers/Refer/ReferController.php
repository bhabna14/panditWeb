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
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,userid',
            'offer_id'        => 'required|exists:flower__refer_offer,id',
            'claim_datetime'  => 'required|date_format:Y-m-d\TH:i', // HTML datetime-local
            'selected_pairs'  => 'nullable|array',
            'selected_pairs.*'=> 'string', // "idx|refer|benefit"
        ]);

        try {
            // Parse selected pairs into structured array
            $parsedSelections = collect($request->input('selected_pairs', []))
                ->map(function ($v) {
                    [$i, $r, $b] = array_pad(explode('|', $v, 3), 3, null);
                    return [
                        'index'   => is_numeric($i) ? (int)$i : null,
                        'refer'   => $r,
                        'benefit' => $b,
                    ];
                })
                ->filter(fn ($row) => $row['refer'] !== null && $row['benefit'] !== null)
                ->values()
                ->all();

            // Optional sanity check against offer arrays
            $offer = ReferOffer::findOrFail($validated['offer_id']);
            $maxIndex = min(count($offer->no_of_refer ?? []), count($offer->benefit ?? [])) - 1;
            foreach ($parsedSelections as $sel) {
                if ($sel['index'] === null || $sel['index'] < 0 || $sel['index'] > $maxIndex) {
                    // Put a specific message into the error bag so SweetAlert shows it
                    return back()->withInput()->withErrors([
                        'selected_pairs' => 'One or more selected benefit options are invalid.',
                    ]);
                }
            }

            // Parse datetime-local to app timezone (store as "Y-m-d H:i:s")
            $dt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['claim_datetime'], config('app.timezone'))
                        ->format('Y-m-d H:i:s');

            // Upsert per (user_id, offer_id)
            $existing = ReferOfferClaim::where('user_id', $validated['user_id'])
                ->where('offer_id', $validated['offer_id'])
                ->first();

            if ($existing) {
                $existing->update([
                    'selected_pairs' => $parsedSelections, // cast -> JSON
                    'date_time'      => $dt,
                    'status'         => 'claimed',
                ]);

                return redirect()
                    ->back()
                    ->with('success', 'Offer claim updated successfully!');
            }

            DB::transaction(function () use ($validated, $parsedSelections, $dt) {
                ReferOfferClaim::create([
                    'user_id'        => $validated['user_id'],
                    'offer_id'       => $validated['offer_id'],
                    'selected_pairs' => $parsedSelections, // cast -> JSON
                    'date_time'      => $dt,
                    'status'         => 'claimed',
                ]);
            });

            return redirect()
                ->back()
                ->with('success', 'Offer claim saved successfully!');

        } catch (\Throwable $e) {
            Log::error('saveOfferClaim failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Put a specific error message in session and also in the error bag
            return back()
                ->withInput()
                ->with('error', 'Unexpected server error while saving the claim.')
                ->withErrors(['error' => 'Failed to save offer claim.']);
        }
    }

}
