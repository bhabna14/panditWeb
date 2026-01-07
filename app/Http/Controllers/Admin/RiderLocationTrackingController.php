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

        // Base filter query for stats
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

        // Table list (with rider details)
        $trackings = RiderLocationTracking::query()
            ->leftJoin('flower__rider_details as rd', 'rd.rider_id', '=', 'rider__location_tracking.rider_id')
            ->select([
                'rider__location_tracking.*',
                'rd.rider_name',
                'rd.phone_number',
                'rd.rider_img',
            ])
            ->when($riderId !== '', fn($q) => $q->where('rider__location_tracking.rider_id', $riderId))
            ->when($fromCarbon, fn($q) => $q->where('rider__location_tracking.date_time', '>=', $fromCarbon))
            ->when($toCarbon, fn($q) => $q->where('rider__location_tracking.date_time', '<=', $toCarbon))
            ->orderByDesc('rider__location_tracking.date_time')
            ->paginate(50)
            ->withQueryString();

        /**
         * Latest location per rider (for map)
         * We find MAX(date_time) per rider, then join back to get lat/lng row.
         */
        $latestSub = RiderLocationTracking::query()
            ->select('rider_id', DB::raw('MAX(date_time) as max_date_time'))
            ->when($riderId !== '', fn($q) => $q->where('rider_id', $riderId))
            ->when($fromCarbon, fn($q) => $q->where('date_time', '>=', $fromCarbon))
            ->when($toCarbon, fn($q) => $q->where('date_time', '<=', $toCarbon))
            ->groupBy('rider_id');

        $latestPerRider = RiderLocationTracking::query()
            ->joinSub($latestSub, 't', function ($join) {
                $join->on('rider__location_tracking.rider_id', '=', 't.rider_id')
                     ->on('rider__location_tracking.date_time', '=', 't.max_date_time');
            })
            ->leftJoin('flower__rider_details as rd', 'rd.rider_id', '=', 'rider__location_tracking.rider_id')
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

        return view('admin.riders.location-tracking', compact(
            'riders',
            'trackings',
            'totalPings',
            'uniqueRiders',
            'latestPing',
            'latestPerRider',
            'riderId',
            'from',
            'to'
        ));
    }
}
