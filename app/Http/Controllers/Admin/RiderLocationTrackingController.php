<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiderDetails;
use App\Models\RiderLocationTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiderLocationTrackingController extends Controller
{
    private function normalizeTracking($value): string
    {
        return strtolower(trim((string)($value ?? '')));
    }

    private function isTrackingOn($value): bool
    {
        $v = $this->normalizeTracking($value);

        // ON values
        if (in_array($v, ['1', 'true', 'start', 'on', 'yes', 'active'], true)) {
            return true;
        }

        // OFF values
        if ($v === '' || in_array($v, ['0', 'false', 'stop', 'off', 'no', 'inactive'], true)) {
            return false;
        }

        // fallback: if it's numeric, treat >0 as ON else OFF
        if (is_numeric($v)) {
            return ((int)$v) > 0;
        }

        // fallback: unknown string => OFF (safer)
        return false;
    }

    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Kolkata');

        $riderId = trim((string) $request->query('rider_id', ''));
        $from    = trim((string) $request->query('from_date', ''));
        $to      = trim((string) $request->query('to_date', ''));

        $fromCarbon = $from !== '' ? Carbon::parse($from, $tz)->startOfDay() : null;
        $toCarbon   = $to   !== '' ? Carbon::parse($to,   $tz)->endOfDay()   : null;

        // Dropdown riders
        $riders = RiderDetails::query()
            ->orderBy('rider_name')
            ->get(['rider_id', 'rider_name', 'phone_number', 'rider_img', 'tracking']);

        // Base filter for stats
        $base = RiderLocationTracking::query();

        if ($riderId !== '') {
            $base->where('rider_id', $riderId);
        }
        if ($fromCarbon) {
            $base->where('date_time', '>=', $fromCarbon);
        }
        if ($toCarbon) {
            $base->where('date_time', '<=', $toCarbon);
        }

        $totalPings   = (clone $base)->count();
        $uniqueRiders = (clone $base)->distinct('rider_id')->count('rider_id');
        $latestPing   = (clone $base)->max('date_time');

        // Tracking table (history)
        $trackings = RiderLocationTracking::query()
            ->leftJoin('flower__rider_details as rd', function ($join) {
                $join->on(
                    DB::raw('rd.rider_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('rider__location_tracking.rider_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->select([
                'rider__location_tracking.*',
                'rd.rider_name',
                'rd.phone_number',
                'rd.rider_img',
            ])
            ->when($riderId !== '', function ($q) use ($riderId) {
                return $q->where('rider__location_tracking.rider_id', $riderId);
            })
            ->when($fromCarbon, function ($q) use ($fromCarbon) {
                return $q->where('rider__location_tracking.date_time', '>=', $fromCarbon);
            })
            ->when($toCarbon, function ($q) use ($toCarbon) {
                return $q->where('rider__location_tracking.date_time', '<=', $toCarbon);
            })
            ->orderByDesc('rider__location_tracking.date_time')
            ->paginate(50)
            ->withQueryString();

        // Latest location per rider (for map + rider cards)
        $latestSub = RiderLocationTracking::query()
            ->select('rider_id', DB::raw('MAX(date_time) as max_date_time'))
            ->when($riderId !== '', function ($q) use ($riderId) {
                return $q->where('rider_id', $riderId);
            })
            ->when($fromCarbon, function ($q) use ($fromCarbon) {
                return $q->where('date_time', '>=', $fromCarbon);
            })
            ->when($toCarbon, function ($q) use ($toCarbon) {
                return $q->where('date_time', '<=', $toCarbon);
            })
            ->groupBy('rider_id');

        $latestPerRider = RiderLocationTracking::query()
            ->joinSub($latestSub, 't', function ($join) {
                $join->on('rider__location_tracking.rider_id', '=', 't.rider_id')
                     ->on('rider__location_tracking.date_time', '=', 't.max_date_time');
            })
            ->leftJoin('flower__rider_details as rd', function ($join) {
                $join->on(
                    DB::raw('rd.rider_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('rider__location_tracking.rider_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->select([
                'rider__location_tracking.rider_id',
                'rider__location_tracking.latitude',
                'rider__location_tracking.longitude',
                'rider__location_tracking.date_time',
                'rd.rider_name',
                'rd.phone_number',
                'rd.rider_img',
            ])
            ->orderBy('rd.rider_name')
            ->get();

        // Markers for map
        $latestMarkers = $latestPerRider->map(function ($x) {
            return [
                'rider_id' => $x->rider_id,
                'name'     => $x->rider_name ?: ('Rider #' . $x->rider_id),
                'phone'    => $x->phone_number ?: '',
                'lat'      => $x->latitude !== null ? (float) $x->latitude : null,
                'lng'      => $x->longitude !== null ? (float) $x->longitude : null,
                'time'     => $x->date_time ? Carbon::parse($x->date_time)->format('d M Y, h:i A') : '',
            ];
        })->values()->all();

        // Rider cards (all riders + last ping + tracking status)
        $latestByRider = $latestPerRider->keyBy('rider_id');

        $riderCards = $riders->map(function ($r) use ($latestByRider) {
            $last = $latestByRider->get($r->rider_id);

            $img = null;
            if (!empty($r->rider_img)) {
                try {
                    $img = \Storage::url($r->rider_img);
                } catch (\Throwable $e) {
                    $img = null;
                }
            }

            $trackingOn = $this->isTrackingOn($r->tracking);

            return [
                'rider_id'      => (string) $r->rider_id,
                'name'          => $r->rider_name ?: ('Rider #' . $r->rider_id),
                'phone'         => $r->phone_number ?: '',
                'img'           => $img,

                // IMPORTANT: send boolean for UI
                'tracking_on'   => $trackingOn,
                'tracking_value'=> $r->tracking, // optional debug / display if you want

                'lat'           => $last && $last->latitude !== null ? (float) $last->latitude : null,
                'lng'           => $last && $last->longitude !== null ? (float) $last->longitude : null,
                'last_time'     => $last && $last->date_time ? Carbon::parse($last->date_time)->format('d M Y, h:i A') : '',
            ];
        });

        return view('admin.riders.location-tracking', compact(
            'riders',
            'riderCards',
            'trackings',
            'totalPings',
            'uniqueRiders',
            'latestPing',
            'latestPerRider',
            'latestMarkers',
            'riderId',
            'from',
            'to'
        ));
    }

    public function toggleTracking(Request $request)
    {
        $validated = $request->validate([
            'rider_id' => 'required',
            'action'   => 'required|in:start,stop',
        ]);

        $riderId = (string) $validated['rider_id'];
        $action  = $validated['action'];

        $rider = RiderDetails::query()
            ->where('rider_id', $riderId)
            ->first();

        if (!$rider) {
            return response()->json([
                'success' => false,
                'message' => 'Rider not found.',
            ], 404);
        }

        // Prefer saving "start/stop" (your requirement).
        // If column is numeric (tinyint), fallback to 1/0 automatically.
        try {
            $rider->tracking = ($action === 'start') ? 'start' : 'stop';
            $rider->save();
        } catch (\Throwable $e) {
            $rider->tracking = ($action === 'start') ? 1 : 0;
            $rider->save();
        }

        $trackingOn = $this->isTrackingOn($rider->tracking);

        return response()->json([
            'success'        => true,
            'message'        => 'Tracking updated successfully.',
            'rider_id'       => $riderId,

            // IMPORTANT: JS must use this boolean
            'tracking_on'    => $trackingOn,

            // Optional: raw value saved in DB
            'tracking_value' => $rider->tracking,

            // Optional: numeric 1/0 mirror (safe for JS too)
            'tracking'       => $trackingOn ? 1 : 0,
        ]);
    }
}
