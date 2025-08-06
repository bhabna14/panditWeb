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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $query = Subscription::with([
            'order.address.localityDetails',
            'flowerPayments',
            'users',
            'flowerProducts',
        ])
        ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
        ->orderBy('id', 'desc');

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('start_date', [$request->from_date, $request->to_date]);
        }

        return DataTables::of($query)
            ->addColumn('customer_details', function ($row) {
                $user = $row->users;
                $order = $row->order;
                $userId = $user->userid ?? null;
                $orderId = $order->order_id ?? null;
                $address = $order->address ?? null;
                $locality = $address?->localityDetails?->locality ?? '';

                $tooltip = "<strong>Ord:</strong> {$orderId}<br>" .
                           "<strong>Name:</strong> {$user->name}<br>" .
                           "<strong>No:</strong> {$user->mobile_number}";

                $modalId = "addressModal{$orderId}";
                $viewBtn = $userId
                    ? "<a href='/admin/show-customer/{$userId}/details' class='btn btn-outline-info btn-sm'><i class='fas fa-eye'></i></a>"
                    : '';

                $addressHtml = "
                    <div class='modal fade' id='{$modalId}' tabindex='-1' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-primary text-white'>
                                    <h5 class='modal-title'><i class='fas fa-home'></i> Address Details</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                    <p><strong>Address:</strong> {$address->apartment_flat_plot}, {$address->apartment_name}, {$locality}</p>
                                    <p><strong>Landmark:</strong> {$address->landmark}</p>
                                    <p><strong>Pin Code:</strong> {$address->pincode}</p>
                                    <p><strong>City:</strong> {$address->city}</p>
                                    <p><strong>State:</strong> {$address->state}</p>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                ";

                return "
                    <div class='order-details' data-bs-toggle='tooltip' data-bs-html='true' title='{$tooltip}'>
                        <strong>Ord:</strong> {$orderId}<br>
                        <strong>Name:</strong> {$user->name}<br>
                        <strong>No:</strong> {$user->mobile_number}<br>
                        {$viewBtn}
                        <br><button class='btn btn-sm btn-warning mt-1' data-bs-toggle='modal' data-bs-target='#{$modalId}'>View Address</button>
                    </div>
                    {$addressHtml}
                ";
            })

            ->addColumn('purchase_date', function ($row) {
                return Carbon::parse($row->start_date)->format('d M Y') . ' - ' . Carbon::parse($row->end_date)->format('d M Y');
            })

            ->addColumn('duration', function ($row) {
                return Carbon::parse($row->start_date)->diffInDays($row->end_date) . ' days';
            })

            ->addColumn('price', function ($row) {
                return $row->order->total_price ?? 'N/A';
            })

            ->addColumn('status', function ($row) {
                return ucfirst($row->status);
            })

            ->rawColumns(['customer_details', 'purchase_date', 'duration', 'price', 'status'])
            ->make(true);
    }

    return view('admin.reports.flower-subscription-report');
}
}
