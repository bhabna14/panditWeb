<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MarketingFollowUp;
use Carbon\Carbon;

// Notifications
use App\Models\UserDevice;
use App\Models\FCMNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FollowUpController extends Controller
{
    public function followUpSubscriptions()
    {
        $orders = Order::whereNull('request_id')
            ->with([
                'subscription' => function ($query) {
                    $query->where('status', 'active')
                        ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(5)]);
                },
                'user',
                'address.localityDetails',
                'flowerProduct',
                'marketingFollowUps'
            ])
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active')
                    ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(5)]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.marketing.follow-up-subscriptions', compact('orders'));
    }

    public function saveFollowUp(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'note'     => 'required|string',
        ]);

        MarketingFollowUp::create([
            'order_id'        => $request->order_id,
            'subscription_id' => $request->subscription_id,
            'user_id'         => $request->user_id,
            'followup_date'   => now()->toDateString(),
            'note'            => $request->note,
            'created_at'      => now(),
        ]);

        return back()->with('success', 'Follow-up information saved successfully.');
    }

    /**
     * Send a push notification (FCM) to a single user.
     * Accepts either 'user_id' or 'userid' and normalizes to $uid.
     */
    public function sendUserNotification(Request $request)
    {
        // Accept user_id OR userid (safety net), but require at least one.
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required_without:userid|string|nullable',
            'userid'            => 'required_without:user_id|string|nullable',
            'title'             => 'required|string|max:255',
            'description'       => 'required|string|max:1000',
            'image'             => 'nullable|image|max:2048',
            'context_user_name' => 'nullable|string',
            'context_order_id'  => 'nullable|string',
            'context_end_date'  => 'nullable|string',
        ], [
            'user_id.required_without' => 'The user id field is required.',
            'userid.required_without'  => 'The user id field is required.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'open_send_modal' => true,
                    'open_user_id'    => $request->input('user_id', $request->input('userid', '')),
                    'open_user_name'  => $request->input('context_user_name', 'User'),
                    'open_order_id'   => $request->input('context_order_id', '-'),
                    'open_end'        => $request->input('context_end_date', '-'),
                ]);
        }

        $validated = $validator->validated();
        $uid = $validated['user_id'] ?? $validated['userid'] ?? '';

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('notifications', 'public')
            : null;

        $notification = FCMNotification::create([
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'image'         => $imagePath,
            'status'        => 'queued',
            'success_count' => 0,
            'failure_count' => 0,
        ]);

        $tokens = UserDevice::query()
            ->where('user_id', $uid) // stores users.userid string
            ->whereNotNull('device_id')
            ->pluck('device_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return back()
                ->withInput()
                ->with([
                    'error' => 'No valid device tokens found for this user.',
                    'open_send_modal' => true,
                    'open_user_id'    => $uid,
                    'open_user_name'  => $request->input('context_user_name', 'User'),
                    'open_order_id'   => $request->input('context_order_id', '-'),
                    'open_end'        => $request->input('context_end_date', '-'),
                ]);
        }

        try {
            $service = new NotificationService(env('FIREBASE_USER_CREDENTIALS_PATH'));
            $resp = $service->sendBulkNotifications(
                $tokens,
                $notification->title,
                $notification->description,
                ['image' => $imagePath ? asset('storage/'.$imagePath) : '']
            );

            $success = method_exists($resp, 'successes') ? count($resp->successes()->getItems()) : null;
            $failure = method_exists($resp, 'failures') ? count($resp->failures()->getItems()) : null;

            $notification->update([
                'status'        => ($failure === 0) ? 'sent' : (($success > 0) ? 'partial' : 'failed'),
                'success_count' => $success,
                'failure_count' => $failure,
            ]);

            return back()->with('success', 'Notification sent successfully to the selected user!');
        } catch (\Throwable $e) {
            Log::error('Single user FCM send error: '.$e->getMessage(), ['user_id' => $uid]);
            $notification->update(['status' => 'failed']);

            return back()
                ->withInput()
                ->with([
                    'error' => 'Failed to send notification. '.$e->getMessage(),
                    'open_send_modal' => true,
                    'open_user_id'    => $uid,
                    'open_user_name'  => $request->input('context_user_name', 'User'),
                    'open_order_id'   => $request->input('context_order_id', '-'),
                    'open_end'        => $request->input('context_end_date', '-'),
                ]);
        }
    }
}
