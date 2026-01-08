<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiderLocationTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RiderLocationTrackingController extends Controller
{
     public function store(Request $request)
    {
        try {
            // Rider auth (your requirement)
            $rider = Auth::guard('rider-api')->user();

            if (!$rider) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized rider.',
                ], 401);
            }

            // Validate input (recommended)
            $validated = $request->validate([
                'latitude'  => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                // optional: allow client to pass date_time, otherwise use server time
                'date_time' => ['nullable', 'date'],
            ]);

            $tracking = RiderLocationTracking::create([
                'rider_id'   => $rider->rider_id, // from RiderDetails
                'latitude'   => $validated['latitude'],
                'longitude'  => $validated['longitude'],
                'date_time'  => !empty($validated['date_time'])
                    ? Carbon::parse($validated['date_time'])
                    : Carbon::now(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Rider location saved successfully.',
                'data'    => [
                    'id'        => $tracking->id ?? null,
                    'rider_id'   => $tracking->rider_id,
                    'latitude'   => $tracking->latitude,
                    'longitude'  => $tracking->longitude,
                    'date_time'  => $tracking->date_time,
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Keep validation as 422 (best practice)
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Rider location tracking store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    public function updateTracking(Request $request)
    {
        try {
            // Validate input (ONLY rider_id + tracking)
            $validated = $request->validate([
                'rider_id'  => ['required'],
                'tracking'  => ['required', 'in:start,stop'],
            ]);

            $riderId = (string) $validated['rider_id'];

            // Create default row if not exists
            $row = RiderLocationTracking::firstOrCreate(
                ['rider_id' => $riderId],
                [
                    'tracking'  => 'stop',
                    'date_time' => Carbon::now(),
                ]
            );

            // Update tracking
            $row->tracking  = $validated['tracking'];  // start/stop
            $row->date_time = Carbon::now();
            $row->save();

            $message = ($row->tracking === 'start')
                ? 'Tracking started successfully.'
                : 'Tracking stopped successfully.';

            return response()->json([
                'status'  => true,
                'message' => $message,
                'data'    => [
                    'rider_id' => $row->rider_id,
                    'tracking' => $row->tracking,
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Rider tracking update error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error. Unable to update tracking.',
            ], 500);
        }
    }
}
