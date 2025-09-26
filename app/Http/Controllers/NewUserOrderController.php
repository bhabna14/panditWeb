<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\Locality;
use App\Models\Apartment;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // View no longer needs apartments preloaded (AJAX will fetch), so no grouping required.
        return view('new-user-order', compact('localities','flowers'));
    }

    /**
     * AJAX: return apartments for a locality unique_code.
     */
    public function apartmentsByLocality(string $uniqueCode)
    {
        // If your table does NOT have a 'status' column, remove the where('status', 'active') line.
        $apartments = Apartment::query()
            ->where('locality_id', $uniqueCode) // locality_id stores Locality.unique_code
            ->when(schemaHasColumn('flower__apartment', 'status'), fn($q) => $q->where('status','active'))
            ->orderBy('apartment_name')
            ->pluck('apartment_name');

        return response()->json([
            'ok' => true,
            'data' => $apartments,
        ]);
    }

    public function saveNewUserOrder(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                // user
                'user_type'      => 'nullable|in:normal,vip',
                'name'           => 'nullable|string|max:150',
                'mobile_number'  => 'required|digits:10',

                // address
                'state'               => 'required|string|max:120',
                'city'                => 'required|string|max:120',
                'pincode'             => 'required|digits:6',
                'locality'            => 'required|string', // Locality.unique_code
                'apartment_name'      => 'nullable|string|max:180',
                'place_category'      => 'required|in:Individual,Apartment,Business,Temple',
                'apartment_flat_plot' => 'required|string|max:180',
                'landmark'            => 'nullable|string|max:180',
                'address_type'        => 'nullable|in:Home,Work,Other',

                // product + subscription
                'product_id'     => 'required',
                'start_date'     => 'required|date',
                'end_date'       => 'nullable|date', // will compute if missing
                'duration'       => 'required|integer|in:1,3,6',

                // payment
                'paid_amount'    => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,upi',

                // status
                'status'         => 'nullable|in:active,pending,expired',
            ]);

            // Generate external-style user code. (If your FKs use users.id, switch to $user->id below.)
            $userCode = 'USER' . random_int(10000, 99999);

            $user = User::create([
                'userid'        => $userCode,
                'user_type'     => $validated['user_type'] ?? 'normal',
                'name'          => $validated['name'] ?? null,
                'mobile_number' => '+91' . $validated['mobile_number'],
            ]);

            $address = UserAddress::create([
                'user_id'            => $user->userid, // change to $user->id if FK is integer
                'state'              => $validated['state'],
                'city'               => $validated['city'],
                'pincode'            => $validated['pincode'],
                'locality'           => $validated['locality'], // unique_code
                'apartment_name'     => $validated['apartment_name'] ?? null,
                'place_category'     => $validated['place_category'],
                'apartment_flat_plot'=> $validated['apartment_flat_plot'],
                'landmark'           => $validated['landmark'] ?? null,
                'address_type'       => $validated['address_type'] ?? 'Other',
                'country'            => 'India',
                'status'             => 'active',
            ]);

            $start = Carbon::parse($validated['start_date'])->startOfDay();

            $end = !empty($validated['end_date'])
                ? Carbon::parse($validated['end_date'])->endOfDay()
                : (clone $start)->addMonthsNoOverflow((int)$validated['duration'])->subDay()->endOfDay();

            $orderId = 'ORD-' . strtoupper(Str::random(12));

            $order = Order::create([
                'user_id'     => $user->userid, // change to $user->id if FK is integer
                'order_id'    => $orderId,
                'product_id'  => $validated['product_id'],
                'quantity'    => 1,
                'start_date'  => $start,
                'address_id'  => $address->id,
                'total_price' => $validated['paid_amount'],
            ]);

            $subscriptionId = 'SUB-' . strtoupper(Str::random(12));

            Subscription::create([
                'subscription_id' => $subscriptionId,
                'user_id'     => $user->userid, // change to $user->id if FK is integer
                'order_id'    => $order->order_id,
                'product_id'  => $validated['product_id'],
                'start_date'  => $start,
                'end_date'    => $end,
                'status'      => $validated['status'] ?? 'active',
            ]);

            FlowerPayment::create([
                'order_id'       => $order->order_id,
                'payment_id'     => null,
                'user_id'        => $user->userid, // change to $user->id if FK is integer
                'payment_method' => $validated['payment_method'],
                'paid_amount'    => $validated['paid_amount'],
                'payment_status' => (float)$validated['paid_amount'] > 0 ? 'paid' : 'pending',
            ]);

            DB::commit();
            return back()->with('success', 'New user added successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // \Log::error('saveNewUserOrder failed', ['err' => $e->getMessage()]);
            return back()->with('error', 'Failed to save new user order');
        }
    }
}

/**
 * Tiny helper to safely check if a column exists (used above).
 * You can move this to a dedicated helper if you prefer.
 */
if (! function_exists('schemaHasColumn')) {
    function schemaHasColumn(string $table, string $column): bool {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
