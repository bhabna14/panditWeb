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
    /**
     * Public endpoint:
     * - If NOT authenticated: returns only broadcasts (audience="all" or user_ids contains "ALL")
     * - If authenticated (Sanctum): returns broadcasts + user-targeted (+ optional platform-targeted)
     *
     * Optional platform can be provided via query ?platform=android|ios|web or header "X-Platform".
     */
    public function getNotifications(Request $request)
    {
        try {
            $authUser = Auth::guard('sanctum')->user(); // optional
            $userid   = $authUser ? (string) $authUser->userid : null; // e.g., "USER30382"
            $platform = strtolower((string)($request->input('platform') ?: $request->header('X-Platform', ''))); // optional

            $driver = DB::connection()->getDriverName();

            // Helpers: JSON contains with fallback
            $whereJsonContains = function ($q, string $column, string $value) use ($driver) {
                try {
                    $q->whereJsonContains($column, $value);
                } catch (\Throwable $e) {
                    if ($driver === 'mysql') {
                        try {
                            $q->orWhereRaw('JSON_CONTAINS('.$column.', JSON_QUOTE(?))', [$value]);
                        } catch (\Throwable $e2) {
                            $q->orWhere($column, 'like', '%"'.$value.'"%');
                        }
                    } else {
                        $q->orWhere($column, 'like', '%"'.$value.'"%');
                    }
                }
            };

            $q = FCMNotification::query();

            // Always include BROADCASTS:
            // - audience = 'all'
            // - OR user_ids contains "ALL" (historical compatibility)
            $q->where(function ($w) use ($whereJsonContains) {
                $w->where('audience', 'all')
                  ->orWhere(function ($wALL) use ($whereJsonContains) {
                      $wALL->where('audience', 'users')
                           ->where(function ($sub) use ($whereJsonContains) {
                               $whereJsonContains($sub, 'user_ids', 'ALL');
                           });
                  });
            });

            // If authenticated, also include:
            //   - audience = 'users' & user_ids contains $userid
            //   - audience = 'platform' & platforms contains $platform (if given)
            if ($userid) {
                $q->orWhere(function ($wUser) use ($whereJsonContains, $userid) {
                    $wUser->where('audience', 'users')
                          ->where(function ($sub) use ($whereJsonContains, $userid) {
                              $whereJsonContains($sub, 'user_ids', $userid);
                          });
                });

                if (in_array($platform, ['android','ios','web'], true)) {
                    $q->orWhere(function ($wPlat) use ($whereJsonContains, $platform) {
                        $wPlat->where('audience', 'platform')
                              ->where(function ($sub) use ($whereJsonContains, $platform) {
                                  $whereJsonContains($sub, 'platforms', $platform);
                              });
                    });
                }
            }

            // Latest first
            $notifications = $q->orderBy('created_at', 'desc')->get();

            // Map payload with absolute image URL
            $data = $notifications->map(function ($n) {
                $image = null;
                if ($n->image) {
                    $image = preg_match('#^https?://#i', $n->image)
                        ? $n->image
                        : Storage::disk('public')->url($n->image);
                }

                return [
                    'id'            => $n->id,
                    'title'         => $n->title,
                    'description'   => $n->description,
                    'image'         => $image,               // full absolute URL or null
                    'audience'      => $n->audience,         // 'all' | 'users' | 'platform'
                    'user_ids'      => $n->user_ids,         // ["ALL"] or ["USER..."] or null
                    'platforms'     => $n->platforms,        // ['android', ...] or null
                    'status'        => $n->status,
                    'success_count' => $n->success_count,
                    'failure_count' => $n->failure_count,
                    'created_at'    => optional($n->created_at)->toDateTimeString(),
                    'updated_at'    => optional($n->updated_at)->toDateTimeString(),
                ];
            });

            return response()->json([
                'status'  => 200,
                'message' => 'Notifications retrieved successfully!',
                'data'    => $data,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching notifications: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 500,
                'message' => 'Failed to retrieve notifications. Please try again later.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
