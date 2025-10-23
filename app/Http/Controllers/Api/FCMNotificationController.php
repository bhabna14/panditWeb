<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FCMNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FCMNotificationController extends Controller
{
   
    public function getMyNotifications(Request $request)
    {
        try {
            // --- Auth (Sanctum) ---
            $authUser = Auth::guard('sanctum')->user();
            if (!$authUser) {
                return response()->json([
                    'status'  => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $userid   = (string) $authUser->userid; // e.g., "USER30382"

            $platform = strtolower((string)($request->input('platform') ?: $request->header('X-Platform', ''))); // optional

            // --- Base query: include "all" and "users" (containing userid) ---
            $q = FCMNotification::query();

            // driver-aware JSON contains for user_ids
            $driver = DB::connection()->getDriverName();

            $q->where(function ($w) use ($driver, $userid) {
                // audience = all
                $w->where('audience', 'all')
                  // audience = users & JSON contains userid
                  ->orWhere(function ($w2) use ($driver, $userid) {
                      $w2->where('audience', 'users');
                      if ($driver === 'mysql') {
                          // MySQL JSON_CONTAINS(user_ids, JSON_QUOTE(?))
                          $w2->whereRaw('JSON_CONTAINS(user_ids, JSON_QUOTE(?))', [$userid]);
                      } else {
                          // Postgres/SQLite: whereJsonContains
                          $w2->whereJsonContains('user_ids', $userid);
                      }
                  });
            });

            // Optional: include platform-targeted if client provided platform
            if (in_array($platform, ['android','ios','web'], true)) {
                $q->orWhere(function ($w) use ($driver, $platform) {
                    $w->where('audience', 'platform');
                    if ($driver === 'mysql') {
                        $w->whereRaw('JSON_CONTAINS(platforms, JSON_QUOTE(?))', [$platform]);
                    } else {
                        $w->whereJsonContains('platforms', $platform);
                    }
                });
            }

            // Latest first
            $notifications = $q->orderBy('created_at', 'desc')->get();

            // Map clean response & explain why user received it (target_type)
                $data = $notifications->map(function ($n) {
                $image = null;
                if ($n->image) {
                    // absolute URL if already http(s), else use storage public URL
                    $image = preg_match('#^https?://#i', $n->image)
                        ? $n->image
                        : Storage::disk('public')->url($n->image);
                }

                return [
                    'id'          => $n->id,
                    'title'       => $n->title,
                    'description' => $n->description,
                    'image'       => $image,               // full absolute URL or null
                    'audience'    => $n->audience,
                    'user_ids'    => $n->user_ids,
                    'platforms'   => $n->platforms,
                    'status'      => $n->status,
                    'success_count'=> $n->success_count,
                    'failure_count'=> $n->failure_count,
                    'created_at'  => optional($n->created_at)->toDateTimeString(),
                    'updated_at'  => optional($n->updated_at)->toDateTimeString(),
                ];
            });
            
            return response()->json([
                'status'  => 200,
                'message' => 'Notifications retrieved successfully!',
                'data'    => $data,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching notifications for user: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 500,
                'message' => 'Failed to retrieve notifications. Please try again later.',
                'error'   => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
