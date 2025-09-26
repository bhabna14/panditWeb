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
use Illuminate\Support\Str;

use App\Models\Apartment;

use Carbon\Carbon; // Add this at the top of the controller
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade

use Illuminate\Support\Facades\DB;

class NewUserOrderController extends Controller
{
// Controller snippets
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

        // IMPORTANT: Apartment.locality_id stores Locality.unique_code
        $apartments = Apartment::where('status', 'active')
            ->select('id','apartment_name','locality_id')
            ->orderBy('apartment_name')
            ->get();

        // Group by the key we'll read from the <option> (unique_code)
        $apartmentsByLocality = $apartments->groupBy('locality_id');

        return view('new-user-order', compact('localities','flowers','apartmentsByLocality'));
    }

    public function saveNewUserOrder(Request $request)
    {
        try {
            \DB::beginTransaction();

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
                'end_date'       => 'nullable|date', // we will compute if missing
                'duration'       => 'required|integer|in:1,3,6',

                // payment
                'paid_amount'    => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,upi',

                // status
                'status'         => 'nullable|in:active,pending,expired',
            ]);

            // Generate userid (string). If you actually use users.id as FK, switch below accordingly.
            $userCode = 'USER' . random_int(10000, 99999);

            $user = User::create([
                'userid'        => $userCode,
                'user_type'     => $validated['user_type'] ?? 'normal',
                'name'          => $validated['name'] ?? null,
                'mobile_number' => '+91' . $validated['mobile_number'],
            ]);

            // Create address (uses Locality.unique_code in `locality`)
            $address = UserAddress::create([
                // If your FK is integer users.id, change to: 'user_id' => $user->id,
                'user_id'            => $user->userid,
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

            // Dates
            $start = Carbon::parse($validated['start_date'])->startOfDay();

            // Compute end date if not provided: add months (no overflow) then inclusive -1 day
            if (!empty($validated['end_date'])) {
                $end = Carbon::parse($validated['end_date'])->endOfDay();
            } else {
                $end = (clone $start)->addMonthsNoOverflow((int)$validated['duration'])->subDay()->endOfDay();
            }

            // Order
            $orderId = 'ORD-' . strtoupper(Str::random(12));

            $order = Order::create([
                // If your FK is integer users.id, change to: 'user_id' => $user->id,
                'user_id'     => $user->userid,
                'order_id'    => $orderId,
                'product_id'  => $validated['product_id'],
                'quantity'    => 1,
                'start_date'  => $start,
                'address_id'  => $address->id,
                'total_price' => $validated['paid_amount'],
                // let timestamps default to now
            ]);

            // Subscription
            $subscriptionId = 'SUB-' . strtoupper(Str::random(12));

            Subscription::create([
                'subscription_id' => $subscriptionId,
                // If your FK is integer users.id, change to: 'user_id' => $user->id,
                'user_id'     => $user->userid,
                'order_id'    => $order->order_id,
                'product_id'  => $validated['product_id'],
                'start_date'  => $start,
                'end_date'    => $end,
                'status'      => $validated['status'] ?? 'active',
            ]);

            // Payment
            FlowerPayment::create([
                'order_id'       => $order->order_id,
                'payment_id'     => null, // not the string "NULL"
                // If your FK is integer users.id, change to: 'user_id' => $user->id,
                'user_id'        => $user->userid,
                'payment_method' => $validated['payment_method'],
                'paid_amount'    => $validated['paid_amount'],
                'payment_status' => (float)$validated['paid_amount'] > 0 ? 'paid' : 'pending',
            ]);

            \DB::commit();
            return back()->with('success', 'New user added successfully!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            // \Log::error('saveNewUserOrder failed', ['err' => $e->getMessage()]);
            return back()->with('error', 'Failed to save new user order');
        }
    }
}
