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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $query = Subscription::with([
            'order.address.localityDetails',
            'flowerPayments',
            'users.addressDetails',
            'flowerProducts',
        ])
        ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
        ->orderBy('id', 'desc');

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('start_date', [$request->from_date, $request->to_date]);
        }

        return DataTables::of($query)
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
            ->addColumn('purchase_date', function ($row) {
                return [
                    'start' => $row->start_date,
                    'end' => $row->end_date
                ];
            })
            ->addColumn('duration', function ($row) {
                return Carbon::parse($row->start_date)->diffInDays($row->end_date);
            })
            ->addColumn('price', function ($row) {
                return $row->order->total_price ?? 'N/A';
            })
            ->addColumn('status', function ($row) {
                return ucfirst($row->status);
            })
            ->make(true);
    }

    return view('admin.reports.flower-subscription-report');
}


}
