<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\FlowerPickupDetails;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Models\FlowerVendor;
use App\Models\Order;
use App\Models\Address;
use App\Models\LocalityDetails;
use App\Models\PauseResumeLog;
use App\Models\Rider;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB; // ✅ needed for DB::raw

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

            // ---------- BASE QUERY FOR TABLE (filter by purchase period = start_date) ----------
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

            // Materialize rows for KPI math (clone so DataTables can still use original builder)
            $subscriptions = (clone $baseQuery)->get();

            // ---------- MAP: user_id => first-ever subscription id ----------
            $firstIds = Subscription::select('user_id', DB::raw('MIN(id) as first_id'))
                ->groupBy('user_id')
                ->pluck('first_id', 'user_id'); // [user_id => first_id]

            // ---------- KPI COMPUTATION (guaranteed add up) ----------
            $totalPrice = 0.0;
            $newUserPrice = 0.0;    // first-ever subs falling in the filtered range
            $renewUserPrice = 0.0;  // all other subs falling in the filtered range

            foreach ($subscriptions as $sub) {
                $price = (float) ($sub->order->total_price ?? 0);
                $totalPrice += $price;

                $isFirstEver = isset($firstIds[$sub->user_id]) && ((int)$firstIds[$sub->user_id] === (int)$sub->id);
                if ($isFirstEver) {
                    $newUserPrice += $price;
                } else {
                    $renewUserPrice += $price;
                }
            }

            // ---------- OPTIONAL: CSV EXPORT FOR THE SAME FILTER ----------
            if ($wantsCsv) {
                $filename = 'subscription-report-' . $from->toDateString() . '_to_' . $to->toDateString() . '.csv';

                $headers = [
                    'Content-Type'        => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"$filename\"",
                ];

                $callback = function () use ($subscriptions, $firstIds, $totalPrice, $newUserPrice, $renewUserPrice) {
                    $out = fopen('php://output', 'w');

                    // Header rows with KPIs
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

                    foreach ($subscriptions as $row) {
                        $user   = $row->users;
                        $addr   = $user?->addressDetails;
                        $start  = $row->start_date ? Carbon::parse($row->start_date) : null;
                        $end    = $row->end_date ? Carbon::parse($row->end_date) : null;
                        $days   = ($start && $end) ? $start->diffInDays($end) + 1 : 0;
                        $method = $row->latestPaidPayment->payment_method
                                  ?? $row->latestPayment->payment_method
                                  ?? null;
                        $price  = (float) ($row->order->total_price ?? 0);
                        $type   = (isset($firstIds[$row->user_id]) && ((int)$firstIds[$row->user_id] === (int)$row->id)) ? 'NEW' : 'RENEW';

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

            // ---------- DATATABLES (serverSide) ----------
            $dataTable = DataTables::of($baseQuery)
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
                ->addColumn('price', fn($row) => (float)($row->order->total_price ?? 0))
                ->addColumn('payment_method', function ($row) {
                    return $row->latestPaidPayment->payment_method
                        ?? $row->latestPayment->payment_method
                        ?? null;
                })
                ->addColumn('status', fn($row) => ucfirst($row->status));

            // NOTE: Do not call ->make(true) yet; we need to inject KPIs first via getData(true)
            $json = $dataTable->make(true)->getData(true);

            // Inject KPIs (these always add up: total = new + renew)
            $json['total_price']       = round($totalPrice, 2);
            $json['new_user_price']    = round($newUserPrice, 2);
            $json['renew_user_price']  = round($renewUserPrice, 2);

            return response()->json($json);
        }

        // Initial page (Blade)
        return view('admin.reports.flower-subscription-report');
    }
public function reportCustomize(Request $request)
{
    if ($request->ajax()) {
        $query = FlowerRequest::with([
            'order',
            'user.addressDetails',
            'address.localityDetails',
            'flowerRequestItems'
        ])->orderBy('id', 'desc');

        // Default: current month
        $from = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $to = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query->whereBetween('created_at', [$from, $to]);

        // Calculate total and today's price
        $allData = $query->get();
        $totalPrice = $allData->sum(function ($item) {
            if ($item->order && $item->order->total_price) {
                return $item->order->total_price;
            } elseif ($item->order && $item->order->requested_flower_price) {
                return $item->order->requested_flower_price;
            }
            return 0;
        });

        $today = Carbon::today();
        $todayPrice = $allData->whereBetween('created_at', [$today->startOfDay(), $today->endOfDay()])->sum(function ($item) {
            if ($item->order && $item->order->total_price) {
                return $item->order->total_price;
            } elseif ($item->order && $item->order->requested_flower_price) {
                return $item->order->requested_flower_price;
            }
            return 0;
        });

        return DataTables::of($allData)
            ->with([
                'total_price_sum' => $totalPrice,
                'today_price_sum' => $todayPrice
            ])
            ->addColumn('user', function ($row) {
                return [
                    'userid' => $row->user->userid ?? null,
                    'name' => $row->user->name ?? 'N/A',
                    'mobile_number' => $row->user->mobile_number ?? 'N/A',
                    'address_details' => $row->user->addressDetails ?? null
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
                if ($row->flowerRequestItems->isEmpty()) return 'N/A';

                return $row->flowerRequestItems->map(function ($item) {
                    return $item->flower_name . ' (' . $item->flower_quantity . ' ' . $item->flower_unit . ')';
                })->implode(', ');
            })
            ->addColumn('price', function ($row) {
                if ($row->order) {
                    if ($row->order->total_price) {
                        return '₹' . number_format($row->order->total_price, 2);
                    } elseif ($row->order->requested_flower_price) {
                        return '₹' . number_format($row->order->requested_flower_price, 2);
                    }
                }
                return '₹0';
            })
            ->addColumn('status', function ($row) {
                return ucfirst($row->status ?? 'N/A');
            })
            ->make(true);
    }

    return view('admin.reports.flower-customize-report');
}
public function flowerPickUp(Request $request)
{
    $fromDate = $request->input('from_date', \Carbon\Carbon::now()->startOfMonth()->toDateString());
    $toDate   = $request->input('to_date',   \Carbon\Carbon::now()->toDateString());

    $vendors = \App\Models\FlowerVendor::select('vendor_id', 'vendor_name')
        ->orderBy('vendor_name')
        ->get();

    $query = \App\Models\FlowerPickupDetails::with([
            // IMPORTANT: use product_id (owner key) — not id
            'flowerPickupItems.flower:product_id,name,category',
            'flowerPickupItems.unit:id,unit_name',
            'vendor:vendor_id,vendor_name',
            'rider:rider_id,rider_name',
        ])
        ->whereDate('pickup_date', '>=', $fromDate)
        ->whereDate('pickup_date', '<=', $toDate);

    // Only include items where the product category is "Flower" (case-sensitive).
    $query->whereHas('flowerPickupItems.flower', function ($q) {
        $q->where('category', 'Flower');
    });

    if ($request->filled('vendor_id')) {
        $query->where('vendor_id', $request->vendor_id);
    }
    if ($request->filled('payment_mode')) {
        // UI says "Mode of Payment", DB column is payment_method
        $query->where('payment_method', $request->payment_mode);
    }

    $reportData = $query->get();

    $totalPrice = (float) $reportData->sum('total_price');
    $todayPrice = (float) $reportData
        ->filter(fn ($row) => \Carbon\Carbon::parse($row->pickup_date)->isToday())
        ->sum('total_price');

    // Build vendor card summaries from the (already filtered) dataset
    $vendorSummaries = $reportData
        ->groupBy('vendor_id')
        ->map(function ($rows) {
            $first = $rows->first();
            $lastPickup = $rows->max('pickup_date'); // Carbon (pickup_date is cast)
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
            'data'             => $reportData,
            'total_price'      => $totalPrice,
            'today_price'      => $todayPrice,
            'from_date'        => $fromDate,
            'to_date'          => $toDate,
            'vendor_summaries' => $vendorSummaries,
        ]);
    }

    return view('admin.reports.flower-pick-up-reports', [
        'reportData'       => $reportData,
        'total_price'      => $totalPrice,
        'today_price'      => $todayPrice,
        'fromDate'         => $fromDate,
        'toDate'           => $toDate,
        'vendors'          => $vendors,
        'vendorSummaries'  => $vendorSummaries,
    ]);
}


}