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
            ->get(['rider_id', 'rider_name', 'phone_number']);

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

        /**
         * NOTE: Collation workaround in JOIN (prevents Illegal mix of collations)
         * Replace utf8mb4_unicode_ci with your chosen common collation if needed.
         */
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

        // Latest location per rider (for map)
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

        // Build marker array in controller (so Blade stays simple and error-free)
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

        return view('admin.riders.location-tracking', compact(
            'riders',
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
}
