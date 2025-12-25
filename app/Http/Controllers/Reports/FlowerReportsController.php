<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\FlowerPickupDetails;
use Yajra\DataTables\DataTables; // IMPORTANT: use the class (service), not the Facade
use App\Models\User;
use App\Models\FlowerVendor;
use App\Models\Order;
use App\Models\Address;
use App\Models\LocalityDetails;
use App\Models\PauseResumeLog;
use App\Models\Rider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class FlowerReportsController extends Controller
{


public function subscriptionReport(Request $request)
{
    // ✅ CSV trigger: allow ?export=csv
    $wantsCsv = $request->get('export') === 'csv';

    if ($request->ajax() || $wantsCsv) {
        // ---------- DATE FILTER (single source of truth) ----------
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        // Tab filter: "new" or "renew" (for separate lists)
        $type = $request->get('type'); // null | 'new' | 'renew'

        // ---------- BASE QUERY (for ALL subs in range; used for KPIs) ----------
        $baseQuery = Subscription::with([
                'order.address.localityDetails',
                'flowerPayments',
                'users.addressDetails',
                'flowerProducts',
                'latestPayment',
                'latestPaidPayment',
            ])
            ->whereBetween('start_date', [$from, $to])
            ->orderBy('id', 'desc');

        // Materialize rows for KPI math (ALL subs in range)
        $subscriptions = (clone $baseQuery)->get();

        // ---------- MAP: user_id => first-ever subscription id ----------
        $firstIds = Subscription::select('user_id', DB::raw('MIN(id) as first_id'))
            ->groupBy('user_id')
            ->pluck('first_id', 'user_id'); // [user_id => first_id]

        $firstIdValues = $firstIds->values(); // collection of subscription IDs that are "NEW"

        // ---------- KPI COMPUTATION (for all rows in range) ----------
        $totalPrice     = 0.0;
        $newUserPrice   = 0.0;
        $renewUserPrice = 0.0;

        foreach ($subscriptions as $sub) {
            $price = (float) ($sub->order->total_price ?? 0);
            $totalPrice += $price;

            $isFirstEver = isset($firstIds[$sub->user_id]) && ((int) $firstIds[$sub->user_id] === (int) $sub->id);
            if ($isFirstEver) {
                $newUserPrice += $price;
            } else {
                $renewUserPrice += $price;
            }
        }

        // ---------- DATA QUERY (filtered by type for tabs / CSV) ----------
        $dataQuery = (clone $baseQuery);

        if ($type === 'new') {
            // Only first-ever subscriptions
            if ($firstIdValues->isNotEmpty()) {
                $dataQuery->whereIn('id', $firstIdValues);
            } else {
                // No new subs at all
                $dataQuery->whereRaw('1=0');
            }
        } elseif ($type === 'renew') {
            // Subscriptions that are NOT first-ever
            if ($firstIdValues->isNotEmpty()) {
                $dataQuery->whereNotIn('id', $firstIdValues);
            }
            // if there are somehow no firstIds, everything is effectively "renew",
            // but practically that won't happen
        }

        // ---------- OPTIONAL: CSV EXPORT (respects "type" filter) ----------
        if ($wantsCsv) {
            $filename = 'subscription-report-' . $from->toDateString() . '_to_' . $to->toDateString();

            if ($type === 'new') {
                $filename .= '-new.csv';
            } elseif ($type === 'renew') {
                $filename .= '-renew.csv';
            } else {
                $filename .= '.csv';
            }

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $exportSubs = (clone $dataQuery)->get();

            $callback = function () use ($exportSubs, $firstIds, $totalPrice, $newUserPrice, $renewUserPrice) {
                $out = fopen('php://output', 'w');

                // Header rows with KPIs (for WHOLE range, not just filtered list)
                fputcsv($out, ['Total Subscription Revenue', number_format($totalPrice, 2, '.', '')]);
                fputcsv($out, ['Renew Customers Revenue', number_format($renewUserPrice, 2, '.', '')]);
                fputcsv($out, ['New Subscriptions Revenue', number_format($newUserPrice, 2, '.', '')]);
                fputcsv($out, []); // blank line

                // Table header
                fputcsv($out, [
                    'Customer Name', 'Mobile', 'Apartment/Flat No', 'Apartment Name', 'Locality',
                    'Purchase Start', 'Purchase End', 'Duration (days, inclusive)',
                    'Payment Method', 'Price', 'Status', 'Type (NEW/RENEW)'
                ]);

                foreach ($exportSubs as $row) {
                    $user   = $row->users;
                    $addr   = $user?->addressDetails;
                    $start  = $row->start_date ? Carbon::parse($row->start_date) : null;
                    $end    = $row->end_date ? Carbon::parse($row->end_date) : null;
                    $days   = ($start && $end) ? $start->diffInDays($end) + 1 : 0;
                    $method = $row->latestPaidPayment->payment_method
                        ?? $row->latestPayment->payment_method
                        ?? null;
                    $price  = (float) ($row->order->total_price ?? 0);
                    $type   = (isset($firstIds[$row->user_id]) && ((int) $firstIds[$row->user_id] === (int) $row->id))
                        ? 'NEW'
                        : 'RENEW';

                    fputcsv($out, [
                        $user->name ?? 'N/A',
                        $user->mobile_number ?? 'N/A',
                        $addr->apartment_flat_plot ?? '',
                        $addr->apartment_name ?? '',
                        $addr->locality ?? '',
                        $start?->format('Y-m-d') ?? '',
                        $end?->format('Y-m-d') ?? '',
                        $days,
                        $method ?: '',
                        number_format($price, 2, '.', ''),
                        ucfirst($row->status ?? ''),
                        $type,
                    ]);
                }

                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        // ---------- DATATABLES (serverSide, separate list per tab) ----------
        $dataTable = DataTables::of($dataQuery)
            ->addColumn('user', function ($row) {
                $user = $row->users;
                return [
                    'userid' => $user->userid ?? null,
                    'name' => $user->name ?? 'N/A',
                    'mobile_number' => $user->mobile_number ?? 'N/A',
                    'address_details' => $user?->addressDetails ? [
                        'apartment_flat_plot' => $user->addressDetails->apartment_flat_plot ?? '',
                        'apartment_name'      => $user->addressDetails->apartment_name ?? '',
                        'locality'            => $user->addressDetails->locality ?? '',
                        'landmark'            => $user->addressDetails->landmark ?? '',
                        'pincode'             => $user->addressDetails->pincode ?? '',
                        'city'                => $user->addressDetails->city ?? '',
                        'state'               => $user->addressDetails->state ?? '',
                    ] : null
                ];
            })
            ->addColumn('purchase_date', fn($row) => [
                'start' => $row->start_date,
                'end'   => $row->end_date
            ])
            ->addColumn('duration', fn($row) =>
                Carbon::parse($row->start_date)->diffInDays(Carbon::parse($row->end_date)) + 1
            )
            ->addColumn('price', fn($row) => (float) ($row->order->total_price ?? 0))
            ->addColumn('payment_method', function ($row) {
                return $row->latestPaidPayment->payment_method
                    ?? $row->latestPayment->payment_method
                    ?? null;
            })
            ->addColumn('status', fn($row) => ucfirst($row->status));

        $json = $dataTable->make(true)->getData(true);

        // Inject KPIs (always same regardless of tab/type)
        $json['total_price']      = round($totalPrice, 2);
        $json['new_user_price']   = round($newUserPrice, 2);
        $json['renew_user_price'] = round($renewUserPrice, 2);

        return response()->json($json);
    }

    // Initial page (Blade)
    return view('admin.reports.flower-subscription-report');
}

    public function reportCustomize(Request $request, DataTables $dataTables)
    {
        if (!$request->ajax()) {
            return view('admin.reports.flower-customize-report');
        }

        $draw = (int) $request->input('draw', 1);

        try {
            // Default: current month
            $from = $request->from_date
                ? Carbon::parse($request->from_date)->startOfDay()
                : Carbon::now()->startOfMonth();

            $to = $request->to_date
                ? Carbon::parse($request->to_date)->endOfDay()
                : Carbon::now()->endOfMonth();

            // Main server-side query
            $baseQuery = FlowerRequest::query()
                ->with([
                    'order',
                    'user.addressDetails',
                    'address.localityDetails',
                    'flowerRequestItems',
                ])
                ->whereBetween('flower_requests.created_at', [$from, $to])
                ->orderByDesc('flower_requests.id');

            // Totals (SQL)
            $totalPrice = (float) FlowerRequest::query()
                ->leftJoin('orders', 'orders.request_id', '=', 'flower_requests.request_id')
                ->whereBetween('flower_requests.created_at', [$from, $to])
                ->selectRaw('COALESCE(SUM(COALESCE(orders.total_price, orders.requested_flower_price, 0)), 0) AS total_sum')
                ->value('total_sum');

            $todayStart = Carbon::today()->startOfDay();
            $todayEnd   = Carbon::today()->endOfDay();

            $todayPrice = (float) FlowerRequest::query()
                ->leftJoin('orders', 'orders.request_id', '=', 'flower_requests.request_id')
                ->whereBetween('flower_requests.created_at', [$todayStart, $todayEnd])
                ->selectRaw('COALESCE(SUM(COALESCE(orders.total_price, orders.requested_flower_price, 0)), 0) AS today_sum')
                ->value('today_sum');

            // IMPORTANT: use injected service object (no static call)
            return $dataTables->eloquent($baseQuery)
                ->with([
                    'total_price_sum' => $totalPrice,
                    'today_price_sum' => $todayPrice,
                ])
                ->addColumn('user', function ($row) {
                    $user = $row->user; // can be null
                    $addr = $user->addressDetails ?? null;

                    if ($addr instanceof \Illuminate\Support\Collection) {
                        $addr = $addr->first();
                    }

                    return [
                        'userid'          => $user->userid ?? null,
                        'name'            => $user->name ?? 'N/A',
                        'mobile_number'   => $user->mobile_number ?? 'N/A',
                        'address_details' => $addr,
                    ];
                })
                ->addColumn('purchase_date', function ($row) {
                    return optional($row->created_at)->format('d M Y') ?? 'N/A';
                })
                ->addColumn('delivery_date', function ($row) {
                    return $row->date
                        ? Carbon::parse($row->date)->format('d M Y') . ($row->time ? ' ' . $row->time : '')
                        : 'N/A';
                })
                ->addColumn('flower_items', function ($row) {
                    if (!$row->relationLoaded('flowerRequestItems') || $row->flowerRequestItems->isEmpty()) {
                        return 'N/A';
                    }

                    return $row->flowerRequestItems->map(function ($item) {
                        $name = $item->flower_name ?? '';
                        $qty  = $item->flower_quantity ?? '';
                        $unit = $item->flower_unit ?? '';
                        return trim($name) . ' (' . trim($qty . ' ' . $unit) . ')';
                    })->implode(', ');
                })
                // Numeric for Amount (prevents NaN in Blade render)
                ->addColumn('price_number', function ($row) {
                    $price = 0;
                    if ($row->order) {
                        $price = $row->order->total_price ?? $row->order->requested_flower_price ?? 0;
                    }
                    return (float) $price;
                })
                ->addColumn('status', function ($row) {
                    return ucfirst($row->status ?? 'N/A');
                })
                ->make(true);

        } catch (\Throwable $e) {
            Log::error('reportCustomize DataTables error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => Str::limit($e->getTraceAsString(), 4000),
            ]);

            // Valid DataTables JSON so the modal can show it
            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'total_price_sum' => 0,
                'today_price_sum' => 0,
                'error'           => 'Server error: ' . $e->getMessage(),
            ], 200);
        }
    }
public function flowerPickUp(Request $request)
{
    $fromDate = $request->input('from_date', \Carbon\Carbon::now()->startOfMonth()->toDateString());
    $toDate   = $request->input('to_date',   \Carbon\Carbon::now()->toDateString());

    $vendors = \App\Models\FlowerVendor::select('vendor_id', 'vendor_name')
        ->orderBy('vendor_name')
        ->get();

    // Build a BASE query that always applies date + category + payment filters
    $base = \App\Models\FlowerPickupDetails::with([
            'flowerPickupItems.flower:product_id,name,category',
            'flowerPickupItems.unit:id,unit_name',
            'vendor:vendor_id,vendor_name',
            'rider:rider_id,rider_name',
        ])
        ->whereDate('pickup_date', '>=', $fromDate)
        ->whereDate('pickup_date', '<=', $toDate)
        ->whereHas('flowerPickupItems.flower', function ($q) {
            $q->where('category', 'Flower');
        });

    if ($request->filled('payment_mode')) {
        $base->where('payment_method', $request->payment_mode);
    }

    // Clone for filtered listing
    $filtered = (clone $base);

    if ($request->filled('vendor_id')) {
        $filtered->where('vendor_id', $request->vendor_id);
    }

    // ORDER: latest pickup_date first, then latest id
    $filtered->orderBy('pickup_date', 'desc')
             ->orderBy('id', 'desc');

    // Main data (DESC)
    $reportData = $filtered->get();

    $totalPrice = (float) $reportData->sum('total_price');
    $todayPrice = (float) $reportData
        ->filter(fn ($row) => \Carbon\Carbon::parse($row->pickup_date)->isToday())
        ->sum('total_price');

    // === Summaries ===
    // All vendors (ignore vendor filter, respect date+payment+category)
    // (No need to order here for grouping; we still sort by total_amount desc)
    $vendorSummariesAll = (clone $base)->get()
        ->groupBy('vendor_id')
        ->map(function ($rows) {
            $first = $rows->first();
            $lastPickup = $rows->max('pickup_date');
            return [
                'vendor_id'     => $first->vendor->vendor_id ?? $first->vendor_id,
                'vendor_name'   => $first->vendor->vendor_name ?? '—',
                'total_amount'  => (float) $rows->sum('total_price'),
                'pickups_count' => (int) $rows->count(),
                'last_pickup'   => $lastPickup ? \Carbon\Carbon::parse($lastPickup)->format('Y-m-d') : null,
            ];
        })
        ->sortByDesc('total_amount')
        ->values();

    // Filtered vendors (kept in response for completeness)
    $vendorSummariesFiltered = $reportData
        ->groupBy('vendor_id')
        ->map(function ($rows) {
            $first = $rows->first();
            $lastPickup = $rows->max('pickup_date');
            return [
                'vendor_id'     => $first->vendor->vendor_id ?? $first->vendor_id,
                'vendor_name'   => $first->vendor->vendor_name ?? '—',
                'total_amount'  => (float) $rows->sum('total_price'),
                'pickups_count' => (int) $rows->count(),
                'last_pickup'   => $lastPickup ? \Carbon\Carbon::parse($lastPickup)->format('Y-m-d') : null,
            ];
        })
        ->sortByDesc('total_amount')
        ->values();

    if ($request->ajax()) {
        return response()->json([
            'data'                      => $reportData,
            'total_price'               => $totalPrice,
            'today_price'               => $todayPrice,
            'from_date'                 => $fromDate,
            'to_date'                   => $toDate,
            // IMPORTANT: always send the "ALL vendors" summaries for the cards
            'vendor_summaries_all'      => $vendorSummariesAll,
            'vendor_summaries_filtered' => $vendorSummariesFiltered,
        ]);
    }

    return view('admin.reports.flower-pick-up-reports', [
        'reportData'         => $reportData,
        'total_price'        => $totalPrice,
        'today_price'        => $todayPrice,
        'fromDate'           => $fromDate,
        'toDate'             => $toDate,
        'vendors'            => $vendors,
        // Render cards from ALL vendors on initial load too
        'vendorSummariesAll' => $vendorSummariesAll,
    ]);
}



}