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


class FlowerReportsController extends Controller
{
    public function subscriptionReport(Request $request)
    {
        if ($request->ajax()) {
            $query = Subscription::with([
                'order.address.localityDetails',
                'flowerPayments',
                'users',
                'flowerProducts',
            ])->orderBy('id', 'desc');

            if ($request->from_date && $request->to_date) {
                $query->whereBetween('start_date', [$request->from_date, $request->to_date]);
            }

            return DataTables::of($query)
                ->addColumn('customer_details', function ($row) {
                    return $row->users->name ?? 'N/A';
                })
                ->addColumn('purchase_date', function ($row) {
                    return $row->start_date;
                })
                ->addColumn('duration', function ($row) {
                    return \Carbon\Carbon::parse($row->start_date)->diffInDays($row->end_date) . ' days';
                })
                ->addColumn('price', function ($row) {
                    return optional($row->flowerPayments->first())->amount ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return ucfirst($row->status);
                })
                ->rawColumns(['customer_details', 'purchase_date', 'duration', 'price', 'status'])
                ->make(true);
        }

        return view('report.subscription-report');
    }
}
