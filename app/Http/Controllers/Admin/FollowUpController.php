<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MarketingFollowUp;
use Carbon\Carbon;


class FollowUpController extends Controller
{
    //
    public function followUpSubscriptions()
{
    // Get orders related to subscriptions ending in the next 3 days
    $orders = Order::whereNull('request_id')
        ->with([
            'subscription' => function ($query) {
                $query->where('status', 'active')
                      ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(3)]);
            },
            'user',
            'address.localityDetails'
        ])
        ->whereHas('subscription', function ($query) {
            $query->where('status', 'active')
                  ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(3)]);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.marketing.follow-up-subscriptions', compact('orders'));
}

public function saveFollowUp(Request $request)
{
    $request->validate([
        'order_id' => 'required',
        'note' => 'required|string',
    ]);

    MarketingFollowUp::create([
        'order_id' => $request->order_id,
        'subscription_id' => $request->subscription_id,
        'user_id' => $request->user_id,
        'followup_date' => now()->toDateString(), // Automatically set today's date
        'note' => $request->note,
        'created_at' => now(),
    ]);

    return back()->with('success', 'Follow-up information saved successfully.');
}


}
