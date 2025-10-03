<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\Locality;
use App\Models\Apartment;

class NewUserOrderController extends Controller
{

    public function newUserOrder()
    {
        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->orderBy('name')
            ->get(['product_id','name']);

        $localities = Locality::where('status', 'active')
            ->select('id','locality_name','unique_code','pincode')
            ->orderBy('locality_name')
            ->get();

        return view('new-user-order', compact('localities','flowers'));
    }

    public function searchUsers(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $users = User::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('mobile_number', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%");
                });
            })
            ->orderByRaw("CASE WHEN mobile_number LIKE ? THEN 0 ELSE 1 END", ["%{$q}%"])
            ->orderBy('name')
            ->limit(20)
            ->get(['userid', 'name', 'mobile_number']);

        // First option is "New user…"
        $results = [
            ['id' => 'NEW', 'text' => '➕ New user…']
        ];

        foreach ($users as $u) {
            $label = trim(($u->mobile_number ?: '—') . ' — ' . ($u->name ?: 'Unnamed'));
            $results[] = ['id' => (string)$u->userid, 'text' => $label];
        }

        return response()->json(['results' => $results]);
    }

    public function userAddresses($userid)
    {
        $user = User::where('userid', $userid)->first();
        if (!$user) {
            return response()->json(['ok' => false, 'data' => []], 404);
        }

        $addrs = UserAddress::where('user_id', $user->userid)
            ->orderByDesc('default')
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($addrs as $a) {
            $bits = array_filter([
                $a->place_category,
                $a->apartment_name,
                $a->apartment_flat_plot,
                $a->area,
                $a->city,
                $a->state,
                $a->pincode
            ], fn($x) => filled($x));

            $data[] = [
                'id'   => $a->id,
                'label'=> implode(', ', $bits) . ($a->default ? ' (Default)' : '')
            ];
        }

        return response()->json(['ok' => true, 'data' => $data]);
    }

    public function apartmentsByLocality(string $uniqueCode)
    {
        // locality_id column stores the Locality.unique_code
        $apartments = Apartment::query()
            ->where('locality_id', $uniqueCode)
            ->whereNotNull('apartment_name')
            ->whereRaw("TRIM(apartment_name) <> ''")
            ->whereRaw("UPPER(TRIM(apartment_name)) <> 'NULL'")
            ->orderBy('apartment_name')
            ->pluck('apartment_name');

        return response()->json([
            'ok'   => true,
            'data' => $apartments,
        ]);
    }

    public function saveNewUserOrder(Request $request)
    {
        // ---- Validation rules (branch by user/address modes) ----
        $baseRules = [
            // product + subscription
            'product_id'     => ['required'],
            'start_date'     => ['required','date'],
            'end_date'       => ['nullable','date','after_or_equal:start_date'],
            'duration'       => ['required','integer', Rule::in([1,3,6])],

            // payment
            'paid_amount'    => ['required','numeric','min:0'],
            'payment_method' => ['required', Rule::in(['cash','upi','Razorpay'])],

            // status
            'status'         => ['nullable', Rule::in(['active','pending','expired'])],
            'payment_status'    =>  ['nullable', Rule::in(['paid','pending'])],

            // user mode
            'user_select'    => ['required'],
        ];

        // If NEW user: need name + 10-digit mobile
        if ($request->input('user_select') === 'NEW') {
            $userRules = [
                'name'          => ['required','string','max:150'],
                'mobile_number' => ['required','digits:10', Rule::unique('users','mobile_number')],
                'user_type'     => ['nullable', Rule::in(['normal','vip'])],
            ];
        } else {
            // Existing user must be supplied; allow optional updates
            $userRules = [
                'existing_user_id' => ['required','exists:users,userid'],
                'name'             => ['nullable','string','max:150'],
                'mobile_number'    => ['nullable','digits:10', Rule::unique('users','mobile_number')->ignore($request->input('existing_user_id'), 'userid')],
                'user_type'        => ['nullable', Rule::in(['normal','vip'])],
            ];
        }

        // Address rules
        if ($request->input('address_mode') === 'existing') {
            $addressRules = [
                'existing_address_id' => ['required','integer','exists:user_addresses,id'],
            ];
        } else {
            $addressRules = [
                'state'               => ['required','string','max:100'],
                'city'                => ['required','string','max:120'],
                'pincode'             => ['required','string','max:10'],
                'locality'            => ['required','string'],     // unique_code
                'apartment_name'      => ['nullable','string','max:150'],
                'place_category'      => ['required', Rule::in(['Individual','Apartment','Business','Temple'])],
                'apartment_flat_plot' => ['required','string','max:150'],
                'landmark'            => ['nullable','string','max:150'],
                'address_type'        => ['nullable', Rule::in(['Home','Work','Other'])],
            ];
        }

        $validated = $request->validate($baseRules + $userRules + $addressRules);

        DB::beginTransaction();
        try {
            // -------- Resolve/Create user --------
            if ($request->input('user_select') === 'NEW') {
                // Your custom userid and +91 formatting
                $userCode = 'USER' . random_int(10000, 99999);
                $user = User::create([
                    'userid'        => $userCode,
                    'user_type'     => $validated['user_type'] ?? 'normal',
                    'name'          => $validated['name'],
                    'mobile_number' => '+91' . $validated['mobile_number'],
                ]);
            } else {
                // Existing
                $user = User::where('userid', $validated['existing_user_id'])->firstOrFail();

                // Optional updates if provided
                $updates = [];
                if (!empty($validated['name'])) {
                    $updates['name'] = $validated['name'];
                }
                if (!empty($validated['mobile_number'])) {
                    $updates['mobile_number'] = '+91' . $validated['mobile_number'];
                }
                if (!empty($validated['user_type'])) {
                    $updates['user_type'] = $validated['user_type'];
                }
                if (!empty($updates)) {
                    $user->update($updates);
                }
            }

            // -------- Resolve/Create address --------
            if ($request->input('address_mode') === 'existing') {
                $address = UserAddress::where('id', $validated['existing_address_id'])
                    ->where('user_id', $user->userid)
                    ->firstOrFail();
            } else {
                $address = UserAddress::create([
                    'user_id'             => $user->userid, // IMPORTANT: you use 'userid' as FK
                    'state'               => $validated['state'],
                    'city'                => $validated['city'],
                    'pincode'             => $validated['pincode'],
                    'locality'            => $validated['locality'], // unique_code
                    'apartment_name'      => $validated['apartment_name'] ?? null,
                    'place_category'      => $validated['place_category'],
                    'apartment_flat_plot' => $validated['apartment_flat_plot'],
                    'landmark'            => $validated['landmark'] ?? null,
                    'address_type'        => $validated['address_type'] ?? 'Other',
                    'country'             => 'India',
                    'default'             => 1,
                ]);
                // mark as default and unset others
                $address->setAsDefault();
            }

            // -------- Dates --------
            $start = Carbon::parse($validated['start_date'])->startOfDay();
            $end = !empty($validated['end_date'])
                ? Carbon::parse($validated['end_date'])->endOfDay()
                : (clone $start)->addMonthsNoOverflow((int)$validated['duration'])->subDay()->endOfDay();

            // -------- Order --------
            $orderId = 'ORD-' . strtoupper(Str::random(12));
            $order = Order::create([
                'user_id'     => $user->userid,       // IMPORTANT: you use 'userid' as FK
                'order_id'    => $orderId,
                'product_id'  => $validated['product_id'],
                'quantity'    => 1,
                'start_date'  => $start,
                'address_id'  => $address->id,
                'total_price' => $validated['paid_amount'],
            ]);

            // -------- Subscription --------
            $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
            Subscription::create([
                'subscription_id' => $subscriptionId,
                'user_id'         => $user->userid,   // IMPORTANT
                'order_id'        => $order->order_id,
                'product_id'      => $validated['product_id'],
                'start_date'      => $start,
                'end_date'        => $end,
                'status'          => $validated['status'] ?? 'active',
            ]);

            // -------- Payment --------
            FlowerPayment::create([
                'order_id'       => $order->order_id,
                'payment_id'     => null,
                'user_id'        => $user->userid,    // IMPORTANT
                'payment_method' => $validated['payment_method'],
                'paid_amount'    => $validated['paid_amount'],
                'payment_status' => $validated['payment_status'],
            ]);

            DB::commit();
            return back()->with('success', $request->input('user_select') === 'NEW'
                ? 'New user added successfully!'
                : 'Order saved for existing user!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // \Log::error($e);
            return back()->withInput()->with('error', 'Failed to save new user order');
        }
    }

}