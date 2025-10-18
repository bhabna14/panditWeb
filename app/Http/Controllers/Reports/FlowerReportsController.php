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

class FlowerReportsController extends Controller
{
public function subscriptionReport(Request $request)
{
    if ($request->ajax()) {
        // Main Query
        $query = Subscription::with([
            'order.address.localityDetails',
            'flowerPayments',
            'users.addressDetails',
            'flowerProducts',
            'latestPayment',       // fallback
            'latestPaidPayment',   // preferred
        ])->orderBy('id', 'desc');

        $from = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $request->filled('to_date')   ? Carbon::parse($request->to_date)->endOfDay()   : Carbon::now()->endOfMonth();

        $query->whereBetween('start_date', [$from, $to]);

        $subscriptions = $query->get();

        // Total Price
        $totalPrice = $subscriptions->sum(fn($sub) => $sub->order->total_price ?? 0);

        // New User Subscriptions (within date range)
        $newUserPrice = Subscription::whereBetween('created_at', [$from, $to])
            ->whereIn('user_id', function ($subQuery) {
                $subQuery->select('user_id')
                    ->from('subscriptions')
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) = 1');
            })
            ->where('status', '!=', 'expired')
            ->get()
            ->sum(fn($sub) => $sub->order->total_price ?? 0);

        // Renewed Users (within date range)
        $renewPrice = Subscription::whereBetween('created_at', [$from, $to])
            ->whereIn('order_id', function ($q) {
                $q->select('order_id')->from('subscriptions')->groupBy('order_id')->havingRaw('COUNT(order_id) > 1');
            })
            ->get()
            ->sum(fn($sub) => $sub->order->total_price ?? 0);

        // DataTable response
        $dataTable = DataTables::of($query)
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
            // Inclusive duration (+1 day)
            ->addColumn('duration', fn($row) => Carbon::parse($row->start_date)->diffInDays(Carbon::parse($row->end_date)) + 1)
            ->addColumn('price', fn($row) => $row->order->total_price ?? 0)
            // NEW: Payment method (prefer latest paid, else latest)
            ->addColumn('payment_method', function ($row) {
                return $row->latestPaidPayment->payment_method
                       ?? $row->latestPayment->payment_method
                       ?? null;
            })
            ->addColumn('status', fn($row) => ucfirst($row->status))
            ->make(true);

        $json = $dataTable->getData(true);
        $json['total_price']     = $totalPrice;
        $json['new_user_price']  = $newUserPrice;
        $json['renew_user_price']= $renewPrice;

        return response()->json($json);
    }

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
            'flowerPickupItems.flower:id,name',
            'flowerPickupItems.unit:id,unit_name',
            'vendor:vendor_id,vendor_name',
            'rider:rider_id,rider_name',
        ])
        ->whereDate('pickup_date', '>=', $fromDate)
        ->whereDate('pickup_date', '<=', $toDate);

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
            return [
                'vendor_id'     => $first->vendor->vendor_id ?? $first->vendor_id,
                'vendor_name'   => $first->vendor->vendor_name ?? '—',
                'total_amount'  => (float) $rows->sum('total_price'),
                'pickups_count' => (int) $rows->count(),
                'last_pickup'   => optional($rows->max('pickup_date'))->format('Y-m-d'),
            ];
        })
        ->sortByDesc('total_amount')
        ->values();

    if ($request->ajax()) {
        return response()->json([
            'data'            => $reportData,
            'total_price'     => $totalPrice,
            'today_price'     => $todayPrice,
            'from_date'       => $fromDate,
            'to_date'         => $toDate,
            'vendor_summaries'=> $vendorSummaries,
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