<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use Yajra\DataTables\DataTables;
use App\Models\User;
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
        $query = Subscription::with([
            'order.address.localityDetails',
            'flowerPayments',
            'users.addressDetails',
            'flowerProducts',
        ])
        ->where('status', '!=', 'expired')
        ->orderBy('id', 'desc');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('start_date', [$request->from_date, $request->to_date]);
        } else {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $query->whereBetween('start_date', [$startOfMonth, $endOfMonth]);
        }

        // Total
        $subscriptions = $query->get();

        $totalPrice = $subscriptions->sum(function ($subscription) {
            return $subscription->order->total_price ?? 0;
        });

        // New User Subscription Total Price (1 subscription only)
        $newUserPrice = Subscription::whereDate('created_at', Carbon::today())
            ->where('status', 'pending')
            ->whereIn('user_id', function ($subQuery) {
                $subQuery->select('user_id')
                    ->from('subscriptions')
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) = 1');
            })
            ->get()
            ->sum(function ($sub) {
                return $sub->order->total_price ?? 0;
            });

        // Renew Customer Total Price (order id occurs more than once)
        $renewPrice = Subscription::whereDate('created_at', Carbon::today())
            ->whereIn('order_id', function ($query) {
                $query->select('order_id')
                    ->from('subscriptions')
                    ->groupBy('order_id')
                    ->havingRaw('COUNT(order_id) > 1');
            })
            ->get()
            ->sum(function ($sub) {
                return $sub->order->total_price ?? 0;
            });

        // DataTable response
        $dataTable = DataTables::of($query)
            ->addColumn('user', function ($row) {
                $user = $row->users;
                return [
                    'userid' => $user->userid ?? null,
                    'name' => $user->name ?? 'N/A',
                    'mobile_number' => $user->mobile_number ?? 'N/A',
                    'address_details' => $user->addressDetails ? [
                        'apartment_flat_plot' => $user->addressDetails->apartment_flat_plot ?? '',
                        'apartment_name' => $user->addressDetails->apartment_name ?? '',
                        'locality' => $user->addressDetails->locality ?? '',
                        'landmark' => $user->addressDetails->landmark ?? '',
                        'pincode' => $user->addressDetails->pincode ?? '',
                        'city' => $user->addressDetails->city ?? '',
                        'state' => $user->addressDetails->state ?? '',
                    ] : null
                ];
            })
            ->addColumn('purchase_date', fn($row) => [
                'start' => $row->start_date,
                'end' => $row->end_date
            ])
            ->addColumn('duration', fn($row) => Carbon::parse($row->start_date)->diffInDays($row->end_date))
            ->addColumn('price', fn($row) => $row->order->total_price ?? 0)
            ->addColumn('status', fn($row) => ucfirst($row->status))
            ->make(true);

        $json = $dataTable->getData(true);
        $json['total_price'] = $totalPrice;
        $json['new_user_price'] = $newUserPrice;
        $json['renew_user_price'] = $renewPrice;

        return response()->json($json);
    }

    return view('admin.reports.flower-subscription-report');
}


public function showRequests(Request $request)
{

    $query = FlowerRequest::with([
        'order' => function ($query) {
            $query->with('flowerPayments', 'delivery');
        },
        'flowerProduct',
        'user',
        'address.localityDetails',
        'flowerRequestItems'
    ])->orderBy('id', 'desc');

   return view('admin.reports.flower-customize-report', compact(
        'query',  
            ));
        }
}
