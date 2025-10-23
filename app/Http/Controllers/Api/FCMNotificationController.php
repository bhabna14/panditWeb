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
     * Backwards-compat method name to avoid BadMethodCallException.
     * Simply proxies to getNotifications().
     */
    public function getMyNotifications(Request $request)
    {
        return $this->getNotifications($request);
    }

    /**
     * Public endpoint:
     * - If NOT authenticated: returns only broadcasts (audience="all" OR user_ids contains "ALL")
     * - If authenticated: broadcasts + user-targeted (+ optional platform-targeted)
     *
     * Optional platform via ?platform=android|ios|web or header "X-Platform".
     * Images returned as absolute URLs.
     */
    public function getNotifications(Request $request)
    {
        try {
            $authUser = Auth::guard('sanctum')->user(); // optional
            $userid   = $authUser ? (string) $authUser->userid : null;
            $platform = strtolower((string)($request->input('platform') ?: $request->header('X-Platform', '')));

            $driver = DB::connection()->getDriverName();

            // Helper: JSON contains with graceful fallback
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

            // Always include broadcasts
            $q->where(function ($w) use ($whereJsonContains) {
                $w->where('audience', 'all')
                  ->orWhere(function ($wALL) use ($whereJsonContains) {
                      $wALL->where('audience', 'users')
                           ->where(function ($sub) use ($whereJsonContains) {
                               $whereJsonContains($sub, 'user_ids', 'ALL');
                           });
                  });
            });

            // If authenticated, include targeted
            if ($userid) {
                // User-targeted
                $q->orWhere(function ($wUser) use ($whereJsonContains, $userid) {
                    $wUser->where('audience', 'users')
                          ->where(function ($sub) use ($whereJsonContains, $userid) {
                              $whereJsonContains($sub, 'user_ids', $userid);
                          });
                });

                // Platform-targeted (only when client declares platform)
                if (in_array($platform, ['android','ios','web'], true)) {
                    $q->orWhere(function ($wPlat) use ($whereJsonContains, $platform) {
                        $wPlat->where('audience', 'platform')
                              ->where(function ($sub) use ($whereJsonContains, $platform) {
                                  $whereJsonContains($sub, 'platforms', $platform);
                              });
                    });
                }
            }

            $notifications = $q->orderBy('created_at', 'desc')->get();

            $data = $notifications->map(function ($n) {
                // Build absolute image URL (supports already-absolute values)
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
                    'image'         => $image,
                    'audience'      => $n->audience,     // 'all' | 'users' | 'platform'
                    'user_ids'      => $n->user_ids,     // ["ALL"] | ["USER..."] | null
                    'platforms'     => $n->platforms,    // ['android','ios','web'] | null
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
